<?php

require_once __DIR__ . "/includes/bootstrap.php";
    ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    rate_limit('login', 10, 60);

    $data = json_decode(file_get_contents("php://input"));

    if(!$data || json_last_error() !== JSON_ERROR_NONE)  {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid JSON"]);
        exit();
    }

    $email = strtolower(trim($data->email ?? ''));

    $password = $data->password ?? '';

    if (!$email || !$password) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "All fields required"]);
        exit();
    }


    try {
       
        validate_csrf();
            
        $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = :email");

        $stmt->execute([':email' => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        error_log("EMAIL: " . $email);
error_log("USER: " . print_r($user, true));

        $valid = $user && password_verify($password, $user['password']);

        if (!$valid) {
            http_response_code(401);
            echo json_encode(["success" => false, "message" => "Invalid credentials"]);
            exit();
        }

        
        session_regenerate_id(true);

        $_SESSION['user'] = [
            "id" => $user["id"],
            "name" => $user["name"],
            "email" => $user["email"],
            "role" => $user["role"]
        ];  

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    

        echo json_encode([  
            "success" => true,
            "user" => $_SESSION['user']
        ]);
        

    } catch (Throwable $e) {
        error_log($e->getMessage());
        http_response_code(500);

        echo json_encode([
            "success" => false,
            "message" => "Server error"
        ]);
    }

?>