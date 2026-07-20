<?php

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/vendor/autoload.php';

$stripeSecretKey = getenv('STRIPE_SECRET_KEY') ?: ($_ENV['STRIPE_SECRET_KEY'] ?? null);

if (!$stripeSecretKey) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Stripe key not configured"
    ]);
    exit;
}

// ✅ SET STRIPE KEY (THIS IS THE REAL GOAL)
\Stripe\Stripe::setApiKey($stripeSecretKey);