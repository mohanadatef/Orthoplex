<?php
return [
    'driver' => env('SCOUT_DRIVER','meilisearch'),
    'meilisearch' => [
        'host' => env('MEILISEARCH_HOST','http://meilisearch:7700'),
        'key' => env('MEILISEARCH_KEY', null),
    ],
];
