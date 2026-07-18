<?php

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/security.php";

header("Content-Type: application/json");


try {


    rate_limit("login", 10, 60);


    validate_csrf();



    $data = json_decode(
        file_get_contents("php://input"),
        true
    );



    $email = strtolower(
        trim($data['email'] ?? '')
    );

    $password = $data['password'] ?? '';



    $stmt = $conn->prepare(
        "
        SELECT
            id,
            name,
            email,
            password,
            role

        FROM users

        WHERE email = :email
        "
    );


    $stmt->execute([
        ":email" => $email
    ]);



    $user = $stmt->fetch(PDO::FETCH_ASSOC);



    if (
        !$user ||
        !password_verify(
            $password,
            $user['password']
        )
    ) {


        http_response_code(401);


        echo json_encode([

            "success" => false,

            "message" => "Invalid credentials"

        ]);


        exit;
    }




    session_regenerate_id(true);



    $_SESSION['user'] = [

        "id" => $user['id'],

        "name" => $user['name'],

        "email" => $user['email'],

        "role" => $user['role']

    ];



    $_SESSION['csrf_token'] =
        bin2hex(random_bytes(32));




    echo json_encode([

        "success" => true,

        "user" => $_SESSION['user']

    ]);



} catch(Throwable $e) {


    http_response_code(500);


    echo json_encode([

        "success" => false,

        "message" => "Server error"

    ]);

}