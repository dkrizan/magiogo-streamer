<?php

return [
    'settings' => [
        'displayErrorDetails' => true,
        'determineRouteBeforeAppMiddleware' => false,

        'doctrine' => [
            // if true, metadata caching is forcefully disabled
            'dev_mode' => true,
            'cache_dir' => __DIR__ . '/../temp/doctrine',

            // you should add any other path containing annotated entity classes
            'metadata_dirs' => [__DIR__ . '/../src/Entity']
        ]
    ]
];