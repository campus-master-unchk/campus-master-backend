<?php


return [

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        env('APP_URL_FRONT'),
    ],

    'allowed_headers' => [
        'Content-Type',
        'Authorization',
        'X-Requested-With',
        'Accept',
    ],

    'exposed_headers' => [
        'Authorization',
    ],

    'max_age' => 0,

    'supports_credentials' => false, 
];
