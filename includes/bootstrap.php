<?php

    header("Access-Control-Allow-Origin: https://bookingfront-b9j1.onrender.com");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, X-CSRF-Token");
    header("Access-Control-Allow-Credentials: true");
    header("Content-Type: application/json");


    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }


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
