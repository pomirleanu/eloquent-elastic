<?php
/**
 * Created by PhpStorm.
 * User: pomir
 * Date: 8/25/2016
 * Time: 12:40 PM
 */

namespace EloquentElastic\Contracts;

interface Manager
{
    /**
     * Get the default index manager.
     *
     * @return \EloquentElastic\Manager
     */
    public static function getDefaultIndexManager();
    /**
     * Convenient method to access the index manager of the current class and
     * open the index.
     *
     * @param  string $indexName
     * @return array
     */
    public static function openIndex($indexName = null);
    /**
     * Convenient method to access the index manager of the current class and
     * close the index.
     *
     * @param  string $indexName
     * @return array
     */
    public static function closeIndex($indexName = null);
    /**
     * Convenient method to access the index manager of the current class and
     * put index settings.
     *
     * @param  array $settings
     * @param  string|null $indexName
     * @return array
     */
    public static function putIndexSettings(array $settings, $indexName = null);
    /**
     * Convenient method to access the index manager of the current class and
     * put the property mappings of a type into the index.
     *
     * @param  string|null $indexName
     * @return array
     */
    public static function putIndexMappings($indexName = null);
}