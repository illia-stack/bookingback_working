<?php

    require_once __DIR__ . "/includes/bootstrap.php";


    //  Delete all Session-Data
    $_SESSION = [];


    //  Delete the Session-Cookie 
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();

        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => 'None'
        ]);
    }


    //  End Session 
    session_destroy();


    //  Start a new Session
    session_start();
    session_regenerate_id(true);


    //  New CSRF Token
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    echo json_encode([
        "success" => true,
        "csrfToken" => $_SESSION['csrf_token']
    ]);

?>