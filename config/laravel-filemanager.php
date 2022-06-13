<?php

return [
    /**
     * Settings for the api-based endpoints.
     */
    'api' => [
        'routes' => true,
        'prefix' => '/api/v1',
        'middleware' => [
            'api',
        ],
        'middleware_tus' => [
            'api',
        ],
        'view' => true,
    ],
    /**
     * Settings for the web-based endpoints.
     */
    'head' => [
        'routes' => true,
        'prefix' => '/file-manager',
        'middleware' => [
            'web',
        ],
        'middleware_tus' => [
            'web',
        ],
    ],
    'debug' => [
        
    ]
];