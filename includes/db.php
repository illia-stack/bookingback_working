<?php
    $host = getenv('DB_HOST');
    $dbname = getenv('DB_NAME');
    $user = getenv('DB_USER');
    $password = getenv('DB_PASSWORD');
    $port = getenv('DB_PORT') ?: "6543"; 

    try {
        $pdo = new PDO(
            "pgsql:host=$host;port=$port;dbname=$dbname",
            $user,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 5
            ]
        );

    } catch (PDOException $e) {

        error_log($e->getMessage());

        http_response_code(500);

        echo json_encode([
            "success" => false,
            "message" => "Database connection failed"
        ]);

        exit;
    }
?>