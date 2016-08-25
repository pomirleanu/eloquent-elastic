<?php
/**
 * Created by PhpStorm.
 * User: pomir
 * Date: 8/25/2016
 * Time: 12:44 PM
 */

namespace EloquentElastic;

use Illuminate\Database\Eloquent\Collection as BaseCollection;

class Collection extends BaseCollection
{

    /**
     * Add all documents in this collection to to the Elasticsearch document index.
     *
     * @param  array $result
     *
     * @return $this
     */
    public function addToIndex(&$result = null)
    {
        if ($this->isEmpty()) {
            return $this;
        }
        $result = $this->first()->getIndexRepository()->add($this);

        return $this;
    }


    /**
     * Delete from index.
     *
     * @param  array $result
     *
     * @return $this
     */
    public function deleteFromIndex(&$result = null)
    {
        if ($this->isEmpty()) {
            return $this;
        }
        $result = $this->first()->getIndexRepository()->remove($this);

        return $this;
    }


    /**
     * Delete the items and then re-index them.
     *
     * @param  array $result
     *
     * @return $this
     */
    public function updateIndex(&$result = null)
    {
        if ($this->isEmpty()) {
            return $this;
        }
        $result = $this->first()->getIndexRepository()->update($this);

        return $this;
    }


    /**
     * Add or replace the items in the index.
     *
     * @param  array $result
     *
     * @return $this
     */
    public function saveToIndex(&$result = null)
    {
        if ($this->isEmpty()) {
            return $this;
        }
        $result = $this->first()->getIndexRepository()->save($this);

        return $this;
    }
}