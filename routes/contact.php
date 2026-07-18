<?php

header("Content-Type: application/json");


// -------------------------
// RATE LIMIT
// -------------------------

$ip = $_SERVER['REMOTE_ADDR'];

$limit = 5;

$window = 60;

$rateFile = sys_get_temp_dir() . "/contact_" . md5($ip);


if (file_exists($rateFile)) {

    $rateData = json_decode(
        file_get_contents($rateFile),
        true
    );


    if (
        $rateData &&
        $rateData['time'] > time() - $window
    ) {

        if ($rateData['count'] >= $limit) {

            http_response_code(429);

            echo json_encode([
                "success" => false,
                "message" => "Too many requests"
            ]);

            exit;
        }


        $rateData['count']++;

    } else {

        $rateData = [
            "count" => 1,
            "time" => time()
        ];

    }

} else {

    $rateData = [
        "count" => 1,
        "time" => time()
    ];

}


file_put_contents(
    $rateFile,
    json_encode($rateData)
);




// -------------------------
// CHECK CONTENT TYPE
// -------------------------

if (
    strpos(
        $_SERVER['CONTENT_TYPE'] ?? '',
        'application/json'
    ) === false
) {

    http_response_code(415);

    echo json_encode([
        "success" => false,
        "message" => "Invalid content type"
    ]);

    exit;
}



// -------------------------
// READ JSON
// -------------------------

$data = json_decode(
    file_get_contents("php://input"),
    true
);


if (!is_array($data)) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid JSON"
    ]);

    exit;
}



// -------------------------
// VALIDATION
// -------------------------

$name = trim($data['name'] ?? '');

$email = trim($data['email'] ?? '');

$subject = trim($data['subject'] ?? 'No subject');

$message = trim($data['message'] ?? '');



if (
    !$name ||
    !$email ||
    !$message
) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Missing fields"
    ]);

    exit;
}



if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid email"
    ]);

    exit;
}



if (
    strlen($name) > 100 ||
    strlen($email) > 150 ||
    strlen($subject) > 150 ||
    strlen($message) > 5000
) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Input too long"
    ]);

    exit;
}



// -------------------------
// RESEND EMAIL
// -------------------------

$apiKey = getenv("RESEND_API_KEY");


if (!$apiKey) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Missing email configuration"
    ]);

    exit;
}



$payload = [

    "from" => "onboarding@resend.dev",

    "to" => [
        "illiashapshalov38@gmail.com"
    ],

    "subject" => "[Contact] ".$subject,

    "html" => "

        <h3>New contact message</h3>

        <p>
        <b>Name:</b> {$name}
        </p>

        <p>
        <b>Email:</b> {$email}
        </p>

        <p>
        <b>Message:</b><br>
        {$message}
        </p>

    "

];




// -------------------------
// CURL REQUEST
// -------------------------

$ch = curl_init(
    "https://api.resend.com/emails"
);


curl_setopt_array($ch,[

    CURLOPT_POST => true,

    CURLOPT_RETURNTRANSFER => true,

    CURLOPT_HTTPHEADER => [

        "Authorization: Bearer ".$apiKey,

        "Content-Type: application/json"

    ],

    CURLOPT_POSTFIELDS =>
        json_encode($payload)

]);


$response = curl_exec($ch);


$status = curl_getinfo(
    $ch,
    CURLINFO_HTTP_CODE
);


$error = curl_error($ch);


curl_close($ch);



if ($error) {

    http_response_code(500);

    echo json_encode([
        "success"=>false,
        "message"=>"Email error"
    ]);

    exit;
}



if ($status >= 200 && $status < 300) {


    echo json_encode([

        "success"=>true

    ]);


} else {


    http_response_code(500);

    echo json_encode([

        "success"=>false,

        "message"=>"Email service error"

    ]);

}