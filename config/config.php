<?php

if (session_status() === PHP_SESSION_NONE) {

    ini_set('session.cookie_samesite', 'None');
    ini_set('session.cookie_secure', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_path', '/');

    session_start();
}

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