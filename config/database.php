<?php

$db = $config['db'];

try {

    $dsn = sprintf(
        "pgsql:host=%s;port=%s;dbname=%s;sslmode=%s",
        $db['host'],
        $db['port'],
        $db['database'],
        $db['sslmode']
    );

    $pdo = new PDO(
        $dsn,
        $db['username'],
        $db['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    $conn = $pdo;


} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Database connection failed."
    ]);

    exit;

}