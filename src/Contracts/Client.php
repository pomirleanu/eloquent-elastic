<?php
/**
 * Created by PhpStorm.
 * User: pomir
 * Date: 8/25/2016
 * Time: 12:39 PM
 */

namespace EloquentElastic\Contracts;

interface Client
{

    /**
     * Return the Elasticsearch client instance.
     *
     * @return \Elasticsearch\Client
     */
    public function getClient();
}