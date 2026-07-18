<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


switch ($path) {

    case "/auth/login":
        require __DIR__ . "/../api/auth/login.php";
        exit;


    case "/auth/register":
        require __DIR__ . "/../api/auth/register.php";
        exit;


    case "/auth/me":
        require __DIR__ . "/../api/auth/me.php";
        exit;


    case "/auth/logout":
        require __DIR__ . "/../api/auth/logout.php";
        exit;


    case "/auth/csrf":
        require __DIR__ . "/../api/auth/csrf.php";
        exit;


    default:

        http_response_code(404);

        echo json_encode([
            "success"=>false,
            "message"=>"Auth endpoint not found"
        ]);

        exit;
}