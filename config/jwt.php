<?php

return [

    'secret_key' => strval(env('JWT_SECRET')),

    'public_key' => strval(env('JWT_PUBLIC')),

    'expires_in' => intval(env('JWT_EXPIRES_IN', 36000)),

    'app_url' => strval(env('APP_URL')),

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
