<?php

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/vendor/autoload.php';

$stripeSecretKey = getenv('STRIPE_SECRET_KEY') ?: ($_ENV['STRIPE_SECRET_KEY'] ?? null);

// ✅ DEBUG OUTPUT (JSON-safe)
if (!$stripeSecretKey) {
    echo json_encode([
        "success" => false,
        "debug" => "NO STRIPE KEY",
        "env" => getenv('STRIPE_SECRET_KEY'),
        "_ENV" => $_ENV['STRIPE_SECRET_KEY'] ?? null
    ]);
    exit;
}

// ✅ OPTIONAL: confirm it exists
echo json_encode([
    "success" => true,
    "debug" => "STRIPE KEY FOUND",
    "length" => strlen($stripeSecretKey)
]);
exit;

// 🚨 comment this during debug
// \Stripe\Stripe::setApiKey($stripeSecretKey);