<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/security.php";

header("Content-Type: application/json");


try {

    rate_limit("register", 5, 60);


    validate_csrf();


    $data = json_decode(
        file_get_contents("php://input"),
        true
    );


    if (!$data) {

        http_response_code(400);

        echo json_encode([
            "success" => false,
            "errors" => [
                "general" => ["Invalid JSON"]
            ]
        ]);

        exit;
    }



    $name = trim($data['name'] ?? '');
    $email = strtolower(trim($data['email'] ?? ''));
    $password = $data['password'] ?? '';

    $errors = [];



    if ($name === '') {
        $errors['name'][] = "Name is required";
    }


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'][] = "Invalid email";
    }



    if (strlen($password) < 8) {

        $errors['password'][] =
            "Password must contain at least 8 characters";

    }



    if (!empty($errors)) {

        http_response_code(422);

        echo json_encode([
            "success" => false,
            "errors" => $errors
        ]);

        exit;
    }




    // check existing email

    $stmt = $conn->prepare(
        "SELECT id FROM users WHERE email = :email"
    );


    $stmt->execute([
        ":email" => $email
    ]);


    if ($stmt->fetch()) {

        http_response_code(422);

        echo json_encode([
            "success" => false,
            "errors" => [
                "email" => [
                    "Email already registered"
                ]
            ]
        ]);

        exit;
    }





    $hash = password_hash(
        $password,
        PASSWORD_BCRYPT
    );



    $stmt = $conn->prepare(
        "
        INSERT INTO users
        (
            name,
            email,
            password,
            role,
            created_at,
            updated_at
        )

        VALUES
        (
            :name,
            :email,
            :password,
            'user',
            NOW(),
            NOW()
        )
        "
    );



    $stmt->execute([

        ":name" => $name,

        ":email" => $email,

        ":password" => $hash

    ]);




    echo json_encode([

        "success" => true

    ]);



} catch(Throwable $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage(),
        "file" => $e->getFile(),
        "line" => $e->getLine()
    ]);
}