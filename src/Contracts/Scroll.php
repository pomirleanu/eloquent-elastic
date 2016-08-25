<?php
/**
 * Created by PhpStorm.
 * User: pomir
 * Date: 8/25/2016
 * Time: 1:05 PM
 */

namespace EloquentElastic\Contracts;

use EloquentElastic\Search;

interface Scroll
{

    /**
     * Search using a scroll request for a large number of results.
     *
     * @param  \EloquentElastic\Search $search
     * @param  callable                $callback
     *
     * @return void
     */
    public function scroll(Search $search, callable $callback);
}