<?php

require_once __DIR__ . '/includes/bootstrap.php';

// ✅ Only POST
if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed"
    ]);
    exit;
}

// ✅ RATE LIMIT (protect public endpoint)
rate_limit("contact", 5, 60);

// -------------------------
// VALIDATE CONTENT TYPE
// -------------------------
if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') === false) {
    http_response_code(415);
    echo json_encode([
        "success" => false,
        "message" => "Invalid content type"
    ]);
    exit;
}

// -------------------------
// PARSE INPUT
// -------------------------
$input = json_decode(file_get_contents("php://input"), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Invalid JSON"
    ]);
    exit;
}

// Honeypot (anti-spam)
if (!empty($input['website'])) {
    http_response_code(400);
    exit;
}

// -------------------------
// VALIDATION
// -------------------------
$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$subject = trim($input['subject'] ?? 'No subject');
$message = trim($input['message'] ?? '');

if (!$name || !$email || !$message) {
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

// -------------------------
// SANITIZE
// -------------------------
$nameSafe = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$emailSafe = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$subjectSafe = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
$messageSafe = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));

// -------------------------
// SEND EMAIL (RESEND)
// -------------------------
$apiKey = getenv('RESEND_API_KEY');

if (!$apiKey) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Email service not configured"
    ]);
    exit;
}

$payload = [
    "from" => "onboarding@resend.dev",
    "to" => ["illiashapshalov38@gmail.com"],
    "subject" => "[Contact] " . $subjectSafe,
    "html" => "
        <h3>New message</h3>
        <p><b>Name:</b> $nameSafe</p>
        <p><b>Email:</b> $emailSafe</p>
        <p><b>Message:</b><br>$messageSafe</p>
    "
];

$ch = curl_init("https://api.resend.com/emails");

curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $apiKey",
        "Content-Type: application/json"
    ],
    CURLOPT_POSTFIELDS => json_encode($payload)
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

// -------------------------
// RESPONSE
// -------------------------
if ($error) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Email service error"
    ]);
    exit;
}

if ($httpCode >= 200 && $httpCode < 300) {
    echo json_encode([
        "success" => true
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Email service error"
    ]);
}