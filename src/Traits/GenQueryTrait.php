<?php
/**
 * Created by PhpStorm.
 * User: pomir
 * Date: 8/25/2016
 * Time: 1:43 PM
 */

namespace EloquentElastic\Traits;

use ONGR\ElasticsearchDSL\Aggregation\CardinalityAggregation;
use ONGR\ElasticsearchDSL\Query\BoolQuery;
use ONGR\ElasticsearchDSL\Query\MatchQuery;
use ONGR\ElasticsearchDSL\Query\NestedQuery;
use ONGR\ElasticsearchDSL\Query\RangeQuery;
use ONGR\ElasticsearchDSL\Query\TermQuery;

trait GenQueryTrait
{
    /**
     * Generates a boolean query with a given match.
     *
     * @param null $match
     *
     * @return BoolQuery
     */
    private function genBoolQuery($match = null)
    {
        $boolQuery = new BoolQuery();
        if (isset( $match ) && $match !== null) {
            $boolQuery->addParameter("minimum_should_match", $match);
        }

        return $boolQuery;
    }


    /**
     * Generates a term query for a given field.
     *
     * @param $field
     * @param $value
     *
     * @return TermQuery
     */
    private function genTermQuery($field, $value)
    {
        return new TermQuery($field, $value);
    }


    /**
     * Generates a text query for a given field and precision.
     *
     * @param $field
     * @param $searchFor
     * @param $precision
     *
     * @return MatchQuery
     */
    private function genSearchTextQuery($field, $searchFor, $precision)
    {
        return new MatchQuery($field, $searchFor, [
            'minimum_should_match'     => $precision
        ]);
    }


    /**
     * Generates a range query for a field with a given type.
     *
     * @param $field
     * @param $type
     * @param $value
     *
     * @return RangeQuery
     */
    private function genRangeQuery($field, $type, $value)
    {
        return new RangeQuery($field, [ $type => $value ]);
    }


    /**
     * Generates a nested query for a given query.
     *
     * @param $field
     * @param $query
     *
     * @return NestedQuery
     */
    private function genNestedQuery($field, $query)
    {
        return new NestedQuery($field, $query);
    }


    /**
     * Generates a cardinality aggregation for a specific field.
     *
     * @param $name
     * @param $field
     *
     * @return CardinalityAggregation
     */
    private function genCardinality($name, $field)
    {
        $q = new CardinalityAggregation($name);
        $q->setField($field);

        return $q;
    }
}