<?php

namespace EloquentElastic\Contracts;

use EloquentElastic\Search;

interface IndexRepository
{
    /**
     * Add the model's document representation to the index.
     *
     * @param  \EloquentElastic\Contracts\IndexedModel|\Illuminate\Support\Collection $model
     * @return array
     */
    public function add($model);

    /**
     * Update the indexed document for the model entity.
     *
     * @param  \EloquentElastic\Contracts\IndexedModel|\Illuminate\Support\Collection $model
     * @return array
     */
    public function update($model);

    /**
     * Remove the indexed document for the model entity.
     *
     * @param  \EloquentElastic\Contracts\IndexedModel|\Illuminate\Support\Collection $model
     * @return array
     */
    public function remove($model);

    /**
     * Add or replace the model's document representation in the index.
     *
     * @param  \EloquentElastic\Contracts\IndexedModel|\Illuminate\Support\Collection $model
     * @return array
     */
    public function save($model);

    /**
     * Get all indexed model documents.
     *
     * @param  array|null $with
     * @param  int|null $limit
     * @return \EloquentElastic\SearchResult
     */
    public function all($limit = null);

    /**
     * Perform a search on the index repository.
     *
     * @param  \EloquentElastic\Search $search
     * @return \EloquentElastic\SearchResult
     */
    public function search(Search $search);

    /**
     * Count the number of documents for a search.
     *
     * @param  \EloquentElastic\Search $search
     * @return int
     */
    public function count(Search $search);
}
