<?php

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/security.php";

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

echo json_encode([
    "session_id" => session_id(),
    "csrf" => $_SESSION['csrf_token']
]);