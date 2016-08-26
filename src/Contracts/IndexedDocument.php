<?php

namespace EloquentElastic\Contracts;

interface IndexedDocument
{
    /**
     * Get the document data of this model instance used for the index.
     *
     * @return array
     */
    public function toIndexDocument();
}
