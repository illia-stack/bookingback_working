<?php

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/security.php";

header("Content-Type: application/json");


echo json_encode([

    "success"=>true,

    "data"=>[
        "user"=>$_SESSION['user'] ?? null
    ],

    "message"=>null

]);