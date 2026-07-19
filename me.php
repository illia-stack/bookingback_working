<?php
    require_once __DIR__ . '/../includes/bootstrap.php';

    echo json_encode([
        "user" => $_SESSION['user'] ?? null
    ]);
?>