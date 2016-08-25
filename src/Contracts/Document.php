<?php
/**
 * Created by PhpStorm.
 * User: pomir
 * Date: 8/25/2016
 * Time: 12:23 PM
 */

namespace EloquentElastic\Contracts;

interface Document
{
    /**
     * Get the document data of this model instance used for the index.
     *
     * @return array
     */
    public function toIndexDocument();
}