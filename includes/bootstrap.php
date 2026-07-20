<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    header("Access-Control-Allow-Origin: https://bookingfront-b9j1.onrender.com");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-CSRF-Token");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

// 🚨 HANDLE PREFLIGHT (THIS IS THE FIX)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "PHP ERROR",
        "message" => $errstr,
        "file" => $errfile,
        "line" => $errline
    ]);
    exit;
});

set_exception_handler(function ($e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "EXCEPTION",
        "message" => $e->getMessage(),
        "file" => $e->getFile(),
        "line" => $e->getLine()
    ]);
    exit;
});



    file_put_contents(
    '/tmp/debug.txt',
    "bootstrap reached\n",
    FILE_APPEND
);



    //Cookie Settings
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => 'bookingback-working.onrender.com',
        'httponly' => true,
        'secure' => true,
        'samesite' => 'None'
    ]);

    
    session_start();

    require_once __DIR__ . '/security.php';

    require_once __DIR__ . '/db.php';


    function getProductById($id) {
        global $pdo;

        $stmt = $pdo->prepare("SELECT name, price FROM products WHERE id = ?");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

?>
