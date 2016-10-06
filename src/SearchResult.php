<?php

namespace EloquentElastic;

use Countable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Facades\Cache;
use IteratorAggregate;

class SearchResult implements IteratorAggregate, Countable, Arrayable
{

    /**
     * The partial raw search result data.
     *
     * @var array
     */
    protected $data;

    /**
     * The model class for with the documents were created.
     *
     * @var string
     */
    protected $entityClass;

    /**
     * Dictionary of metadata for the documents.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $metadata;

    /**
     * The documents returned by the search result.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $documents;

    /**
     * Collection of model entities belonging to the search result documents.
     * Will be lazily fetched upon access.
     *
     * @var \EloquentElastic\Collection|null
     */
    protected $items;

    /**
     * The suggest result instance if available.
     *
     * @var \EloquentElastic\SuggestResult|null
     */
    protected $suggestResult;

    /**
     * The elodex config.
     *
     * @var array $config
     */
    protected $config;


    /**
     * Create a new SearchResult instance.
     *
     * @param array  $results
     * @param string $entityClass
     */
    public function __construct($results, $entityClass)
    {
        $this->data = $results;

        $this->entityClass = $entityClass;

        $this->config = config('elodex');

        // Process the hits of the query result data.
        $this->metadata  = $this->metadataForHits($this->data[ 'hits' ]);
        $this->documents = $this->documentCollectionForHits($this->data[ 'hits' ]);

        unset($this->data[ 'hits' ][ 'hits' ]);
        $this->items = null;

        // Process the suggestions of the query result data.
        if (isset($this->data[ 'suggest' ])) {
            $this->suggestResult = new SuggestResult($this->data[ 'suggest' ]);
        }
    }


    /**
     * Return the metadata from the hits as a dictionary keyes by the hit IDs.
     *
     * @param  array $hits
     *
     * @return \Illuminate\Support\Collection
     */
    protected function metadataForHits(array $hits)
    {
        $metadata   = [];
        $exclusions = ['_id', '_index', '_type', '_source'];

        foreach ($hits[ 'hits' ] as $hit) {
            $metadata[ $hit[ '_id' ] ] = array_except($hit, $exclusions);
        }

        return new BaseCollection($metadata);
    }


    /**
     * Builds a documents collection out of the search hits results keyed by
     * their ID.
     *
     * @param  array $hits
     *
     * @return \Illuminate\Support\Collection
     */
    protected function documentCollectionForHits(array $hits)
    {
        $items = [];

        foreach ($hits[ 'hits' ] as $hit) {
            $items[ $hit[ '_id' ] ] = $hit[ '_source' ];
        }

        return new BaseCollection($items);
    }


    /**
     * Total number of hits.
     *
     * @return int
     */
    public function totalHits()
    {
        return Arr::get($this->data, 'hits.total');
    }


    /**
     * Get the max score of the search result.
     *
     * @return float
     */
    public function maxScore()
    {
        return Arr::get($this->data, 'hits.max_score');
    }


    /**
     * Get shard info for the search result containing the number of failed shards.
     *
     * @return array
     */
    public function getShards()
    {
        return $this->data[ '_shards' ];
    }


    /**
     * The execution time the query for the result took in ms.
     *
     * @return int
     */
    public function took()
    {
        return $this->data[ 'took' ];
    }


    /**
     * Returns true if the query for the results timed out.
     *
     * @return bool
     */
    public function timedOut()
    {
        return (bool) $this->data[ 'timed_out' ];
    }


    /**
     * Get the scroll ID of a scroll based search.
     *
     * @return string|null
     */
    public function getScrollId()
    {
        return Arr::get($this->data, '_scroll_id');
    }


    /**
     * Get the aggregations of the search result.
     *
     * @return array
     */
    public function getAggregations()
    {
        return isset($this->data[ 'aggregations' ]) ? $this->data[ 'aggregations' ] : [];
    }


    /**
     * Get the documents returned by the search filled with highlighted fields.
     *
     * @return array
     */
    public function getHighlightedDocuments()
    {
        $highlightedDocuments = [];

        // Merge the highlighted fields into the source documents.
        foreach ($this->getDocuments() as $key => $document) {
            $highlighted = $this->getHighlight($key) ?: [];
            if (! empty($highlighted)) {
                // Merge the highlight fragments.
                foreach ($highlighted as $field => $highlights) {
                    $highlighted[ $field ] = implode('', $highlights);
                }
            }

            $highlightedDocuments[ $key ] = array_merge($document, $highlighted);
        }

        return $highlightedDocuments;
    }


    /**
     * Get the documents returned by the search.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDocuments()
    {
        return $this->documents;
    }


    /**
     * Get the highlighted fields of a document in the search result.
     *
     * @param  string $id
     *
     * @return array|null
     */
    public function getHighlight($id)
    {
        return Arr::get($this->getMetadata(), "{$id}.highlight");
    }


    /**
     * Returns the metadata dictionary for the documents.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getMetadata()
    {
        return $this->metadata;
    }


    /**
     * Gets the collection of models for which the documents were returned by the search.
     *
     * @param  array|null $with
     *
     * @return \Illuminate\Database\Eloquent\Collection|array
     * @throws \RuntimeException
     */
    public function getModels(array $with = null)
    {
        // Loading models is only supported for eloquent models.
        if (! is_a($this->entityClass, \Illuminate\Database\Eloquent\Model::class, true)) {
            throw new \RuntimeException("Entity class {$this->entityClass} is not an Eloquent model.");
        }

        // Lazy loading of the models.
        if (is_null($this->items)) {
            $this->items = $this->loadItems($with);
        }

        return $this->items;
    }


    /**
     * Loads the Eloquent models for the indexed documents found by the search.
     * Uses eager loading for the specified relations array.
     *
     * @param  array|null $with
     *
     * @return \EloquentElastic\Collection
     */
    protected function loadItems(array $with = null)
    {
        $class = $this->entityClass;

        // Eager load the index relations by default.
        if (is_null($with)) {
            $with = (new $class)->getIndexRelations();
        }
        $ids = array_keys($this->documents->all());
        if ($this->config[ 'cache' ][ 'enable' ]) {
            $collection = $this->cacheTheEloquentQuery($with, $ids);
        } else {
            // Load the Eloquent models from the DB.
            $collection = $class::with($with)->find($ids);
        }

        // The database query returns the items in a random order which means
        // a previously specified sort order in the document search will be lost.
        // So we need to manually bring the result in the desired order.
        $dictionary = $collection->getDictionary();
        $sorted     = [];
        foreach ($ids as $id) {
            if (isset($dictionary[ $id ])) {
                $model = $dictionary[ $id ];

                // Fill the model with index metadata.
                if (isset($this->metadata[ $id ][ '_score' ])) {
                    $model->setIndexScore($this->metadata[ $id ][ '_score' ]);
                }

                if (isset($this->metadata[ $id ][ '_version' ])) {
                    $model->setIndexVersion($this->metadata[ $id ][ '_version' ]);
                }

                $sorted[ $id ] = $model;
            }

        }

        return new Collection($sorted);
    }


    /**
     * Cache the eloquent query
     *
     * @param $with
     * @param $cachedDocuments
     *
     * @return mixed
     */
    private function cacheTheEloquentQuery($with, $cachedDocuments)
    {
        $class          = $this->entityClass;
        $cacheKeyPrefix = (new $class)->getElasticCachePrefix();

        $cachedDocuments = array_map(function ($id) use (&$cacheKeyPrefix, $cachedDocuments) {
            $itemCacheKey = $cacheKeyPrefix.$id;
            $design       = Cache::get($itemCacheKey);

            return (is_null($design)) ? $id : $design;
        }, $cachedDocuments);

        $collection = $class::with($with)->find(array_filter($cachedDocuments, 'is_int'));
        $collection->each(function ($item) use ($cacheKeyPrefix) {
            $itemCacheKey = $cacheKeyPrefix.$item->id;
            if ($this->config[ 'cache' ][ 'time' ] === 'forever') {
                Cache::forever($itemCacheKey, $item);
            } else {
                Cache::put($itemCacheKey, $item, $this->config[ 'cache' ][ 'time' ]);
            }

        });

        return $collection->merge(collect(array_filter($cachedDocuments, 'is_object')));
    }


    /**
     * Return the suggestions of the query result if available.
     *
     * @return \EloquentElastic\SuggestResult|null
     */
    public function getSuggestions()
    {
        return $this->suggestResult;
    }


    /**
     * Get an iterator for the items.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return $this->documents->getIterator();
    }


    /**
     * Count the number of items in the search result.
     *
     * @return int
     */
    public function count()
    {
        return $this->documents->count();
    }


    /**
     * Get the array representation of the search result.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }
}
