<?php

require_once __DIR__ . '/includes/bootstrap.php';

if (!isset($_SESSION['user'])) {
    echo json_encode([
        "success" => true,
        "user" => null
    ]);
    exit;
}

echo json_encode([
    "success" => true,
    "user" => $_SESSION['user']
]);