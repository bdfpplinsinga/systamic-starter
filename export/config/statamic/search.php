<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default search index
    |--------------------------------------------------------------------------
    |
    | This option controls the search index that gets queried when performing
    | search functions without explicitly selecting another index.
    |
    */

    'default' => env('STATAMIC_DEFAULT_SEARCH_INDEX', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Search Indexes
    |--------------------------------------------------------------------------
    |
    | Here you can define all of the available search indexes.
    |
    */

    'indexes' => [

        // Doorzoekt alle collecties; de page builder wordt platte tekst via PageBuilderText.
        'default' => [
            'driver' => 'local',
            'searchables' => ['collection:*'],
            'filter' => \App\Search\HasUrl::class,
            'fields' => ['title', 'excerpt', 'meta_description', 'page_builder'],
            'transformers' => [
                'page_builder' => \App\Search\PageBuilderText::class,
            ],
        ],

        // 'blog' => [
        //     'driver' => 'local',
        //     'searchables' => 'collection:blog',
        // ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Driver Defaults
    |--------------------------------------------------------------------------
    |
    | Here you can specify default configuration to be applied to all indexes
    | that use the corresponding driver. For instance, if you have two
    | indexes that use the "local" driver, both of them can have the
    | same base configuration. You may override for each index.
    |
    */

    'drivers' => [

        'local' => [
            'path' => storage_path('statamic/search'),
        ],

        'algolia' => [
            'credentials' => [
                'id' => env('ALGOLIA_APP_ID', ''),
                'secret' => env('ALGOLIA_SECRET', ''),
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Search Defaults
    |--------------------------------------------------------------------------
    |
    | Here you can specify default configuration to be applied to all indexes
    | regardless of the driver. You can override these per driver or per index.
    |
    */

    'defaults' => [
        'fields' => ['title'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Indexing Queue
    |--------------------------------------------------------------------------
    |
    | Here you may configure the queue name and connection used when indexing
    | documents, along with a timeout (in seconds) for each indexing job.
    |
    */

    'queue' => env('STATAMIC_SEARCH_QUEUE'),

    'queue_connection' => env('STATAMIC_SEARCH_QUEUE_CONNECTION'),

    'queue_timeout' => env('STATAMIC_SEARCH_QUEUE_TIMEOUT'),

    /*
    |--------------------------------------------------------------------------
    | Chunk Size
    |--------------------------------------------------------------------------
    |
    | Here you can configure the chunk size used when indexing documents.
    | The higher you make it, the more memory it will use, but the quicker
    | the indexing process will be.
    |
    */

    'chunk_size' => 100,

];
