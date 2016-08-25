<?php
/**
 * Created by PhpStorm.
 * User: pomir
 * Date: 8/25/2016
 * Time: 12:13 PM
 */

namespace EloquentElastic\Traits;
use EloquentElastic\Search;
use Illuminate\Container\Container;

trait AccessTrait
{
    /**
     * Add the model's document to the index.
     *
     * @return array
     */
    public function addToIndex()
    {
        return $this->getIndexRepository()->add($this);
    }
    /**
     * Update the index for the model's document.
     *
     * @return array
     */
    public function updateIndex()
    {
        return $this->getIndexRepository()->update($this);
    }
    /**
     * Remove the model's document from the index.
     *
     * @return array
     */
    public function removeFromIndex()
    {
        return $this->getIndexRepository()->remove($this);
    }
    /**
     * Add or replace the model's document to the index.
     *
     * @return array
     */
    public function saveToIndex()
    {
        return $this->getIndexRepository()->save($this);
    }
    /**
     * Return an index repository used for this model instance.
     *
     * @param  string|null $index
     * @return \EloquentElastic\Repository\IndexRepository
     */
    public function getIndexRepository($index = null)
    {
        return static::getClassIndexRepository($index);
    }
    /**
     * Return an index repository used for this model class.
     *
     * @param  string|null $index
     * @return \EloquentElastic\Repository\IndexRepository
     */
    public static function getClassIndexRepository($index = null)
    {
        $app = Container::getInstance();
        return $app->make('elodex.repository')->repository(get_called_class(), $index);
    }
    /**
     * Create a new index search query.
     *
     * @return \EloquentElastic\Search
     */
    public function newIndexSearch()
    {
        $search = new Search();
        $search->setModel($this);
        return $search;
    }
    /**
     * Create a new index based search query.
     *
     * @return \EloquentElastic\Search
     */
    public static function indexSearch()
    {
        return (new static)->newIndexSearch();
    }
}