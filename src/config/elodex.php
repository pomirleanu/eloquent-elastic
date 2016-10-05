<?php

return [
    /*
      |--------------------------------------------------------------------------
      | Elasticsearch Client Configuration
      |--------------------------------------------------------------------------
      |
      | These options will be passed to the Elasticsearch client instance.
      |
      | For more info about configuration options visit:
      | http://www.elasticsearch.org/guide/en/elasticsearch/client/php-api/current/_configuration.html
      |
    */
    'config'        => [
        'hosts'    => ['localhost:9200'],
        'logPath'  => storage_path('logs/elasticsearch.log'),
        'logLevel' => \Monolog\Logger::INFO,
        'retries'  => 1,
    ],

    /*
      |--------------------------------------------------------------------------
      | Default Index Name
      |--------------------------------------------------------------------------
      |
      | The default index name used by all indexed models.
      |
    */
    'default_index' => 'pixteller_v3',

    /*
      |--------------------------------------------------------------------------
      | Index Analyzers
      |--------------------------------------------------------------------------
      |
      | Analyzers added to the ElasticSearch index during index creation with
      | the 'es:create-index' command.
      |
     */
    'analyzer'      => [],

    /*
     |--------------------------------------------------------------------------
     | Cache eloquent query and elastic documents/mappings
     |--------------------------------------------------------------------------
     | Cache time can be set to forever or in minutes
     |
    */
    'cache' => [
        'enable'           => env('ELODEX_CACHE_DOCUMENTS_ENABLE', false),
        'documents-enable' => env('ELODEX_CACHE_DOCUMENTS_ENABLE', false),
        'documents-time'   => env('ELODEX_CACHE_DOCUMENTS_TIME', 'forever'),
        'query-enable'     => env('ELODEX_CACHE_DOCUMENTS_ENABLE', true),
        'query-time'       => env('ELODEX_CACHE_DOCUMENTS_TIME', 'forever'),
    ]

];
