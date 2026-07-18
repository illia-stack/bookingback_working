<?php

if (!isset($_SESSION['user'])) {

    http_response_code(401);

    echo json_encode([

        "success"=>false,

        "message"=>"Unauthorized"

    ]);

    exit;

}


$userId = $_SESSION['user']['id'];