<?php

return [

    'db' => [

        'host' => getenv('DB_HOST'),

        'port' => getenv('DB_PORT'),

        'database' => getenv('DB_DATABASE'),

        'username' => getenv('DB_USERNAME'),

        'password' => getenv('DB_PASSWORD'),

        'sslmode' => getenv('DB_SSLMODE') ?: 'require',

    ],

];