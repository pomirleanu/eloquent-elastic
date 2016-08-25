<?php
/**
 * Created by PhpStorm.
 * User: pomir
 * Date: 8/25/2016
 * Time: 12:10 PM
 */

namespace EloquentElastic\Pagination;


use Illuminate\Contracts\Pagination\LengthAwarePaginator as Paginator;

class LengthAwarePaginator extends Paginator
{
    /**
     * The search result instance for this paginator.
     *
     * @var \EloquentElastic\SearchResult
     */
    protected $searchResult;
    /**
     * Create a new paginator instance.
     *
     * @param  \EloquentElastic\SearchResult $searchResult
     * @param  int $perPage
     * @param  int|null $currentPage
     * @param  array $options (path, query, fragment, pageName)
     */
    public function __construct($searchResult, $perPage, $currentPage = null, array $options = [])
    {
        $this->searchResult = $searchResult;
        $total = $this->searchResult->totalHits();
        $items = $this->searchResult->getDocuments();
        parent::__construct($items, $total, $perPage, $currentPage, $options);
    }
    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $data = parent::toArray();
        $data['search_result'] = $this->searchResult->toArray();
        return $data;
    }
    /**
     * Get the collection of models for the search result.
     *
     * @param  array|null $with
     * @return \Illuminate\Database\Eloquent\Collection|array
     */
    public function getModels(array $with = null)
    {
        return $this->searchResults->getModels($with);
    }
    /**
     * Get the search result instance for this paginator.
     *
     * @return \EloquentElastic\SearchResult
     */
    public function getSearchResult()
    {
        return $this->searchResult;
    }
}