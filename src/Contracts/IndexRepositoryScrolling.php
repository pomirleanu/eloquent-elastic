<?php

namespace EloquentElastic\Contracts;

use EloquentElastic\Search;

interface IndexRepositoryScrolling
{
    /**
     * Search using a scroll request for a large number of results.
     *
     * @param  \EloquentElastic\Search $search
     * @param  callable $callback
     * @return void
     */
    public function scroll(Search $search, callable $callback);
}
