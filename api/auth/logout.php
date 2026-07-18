<?php

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/security.php";

header("Content-Type: application/json");


// clear session data

$_SESSION = [];



// remove cookie

if (ini_get("session.use_cookies")) {

    $params = session_get_cookie_params();


    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );

}



// destroy old session

session_destroy();



// create new session

session_start();

session_regenerate_id(true);



// new csrf token

$_SESSION['csrf_token'] =
    bin2hex(random_bytes(32));



echo json_encode([

    "success" => true,

    "csrfToken" => $_SESSION['csrf_token']

]);