<?php

return [

    'secret_key' => env('JWT_SECRET'),

    'public_key' => env('JWT_PUBLIC'),

    'expires_in' => env('JWT_EXPIRES_IN', 36000),

    'app_url' => env('APP_URL')

];
