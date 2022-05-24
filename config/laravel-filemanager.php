<?php

return [
    /**
     * Settings for the api-based endpoints.
     */
    'api' => [
        'routes' => true,
        'prefix' => '/api/v1/file-manager',
        'middleware' => [
            'api'
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
            //'web'
        ],
    ],
    'debug' => [
        'disable_cleanup' => false,
        'no_redirects' => false,
    ]
];