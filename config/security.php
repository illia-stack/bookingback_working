<?php

if (session_status() === PHP_SESSION_NONE) {

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'None'
    ]);

    session_start();
}

header('Content-Type: application/json; charset=UTF-8');

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');


$allowedOrigins = [
    "http://localhost:5173",
    "https://bookingfront-b9j1.onrender.com"
];


$origin = $_SERVER['HTTP_ORIGIN'] ?? "";


if (in_array($origin, $allowedOrigins, true)) {

    header(
        "Access-Control-Allow-Origin: ".$origin
    );

    header(
        "Access-Control-Allow-Credentials: true"
    );
}


header(
    "Access-Control-Allow-Headers: Content-Type, Authorization, X-CSRF-Token"
);


header(
    "Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS"
);



if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {

    http_response_code(204);
    exit;

}



if (!headers_sent() && extension_loaded('zlib')) {

    ob_start('ob_gzhandler');

}




function validate_csrf()
{
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

    if (
        empty($_SESSION['csrf_token']) ||
        empty($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    ) {

        http_response_code(419);

        echo json_encode([
            "session_id" => session_id(),
            "session_token" => $_SESSION['csrf_token'] ?? null,
            "header_token" => $token,
            "cookies" => $_COOKIE,
        ]);

        exit;
    }
}




function rate_limit(
    $key,
    $maxAttempts = 5,
    $window = 60
){

    if (!isset($_SESSION['rate_limit'])) {

        $_SESSION['rate_limit'] = [];

    }


    if (!isset($_SESSION['rate_limit'][$key])) {

        $_SESSION['rate_limit'][$key] = [];

    }


    $now = time();



    $_SESSION['rate_limit'][$key] =
        array_filter(
            $_SESSION['rate_limit'][$key],
            function($time) use ($now, $window){

                return ($now - $time) < $window;

            }
        );



    if (
        count($_SESSION['rate_limit'][$key])
        >=
        $maxAttempts
    ){

        http_response_code(429);

        echo json_encode([
            "success"=>false,
            "message"=>"Too many attempts"
        ]);

        exit;

    }



    $_SESSION['rate_limit'][$key][] = $now;

}