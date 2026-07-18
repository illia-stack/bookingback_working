<?php

require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


switch (true) {

    // GET /api/properties
    case $method === 'GET' && $path === '/properties':
        require __DIR__ . '/../routes/properties.php';
        break;

    // GET /api/properties/123
    case $method === 'GET' && preg_match('#^/properties/\d+$#', $path):
        require __DIR__ . '/../routes/properties.php';
        break;

    // POST /api/bookings
    case $method === 'POST' && $path === '/bookings':
        require __DIR__ . '/../routes/bookings.php';
        break;

    // GET /api/my-bookings
    case $method === 'GET' && $path === '/my-bookings':
        require __DIR__ . '/../routes/bookings.php';
        break;

    // POST /api/contact
    case $method === 'POST' && $path === '/contact':
        require __DIR__ . '/../routes/contact.php';
        break;

    // Auth routes
    case str_starts_with($path, '/auth/'):
    require __DIR__ . '/../routes/auth.php';
    break;

    // Stripe
    case str_starts_with($path, '/stripe'):
        require __DIR__ . '/../routes/stripe.php';
        break;

    default:
        http_response_code(404);

        echo json_encode([
            'success' => false,
            'data' => null,
            'message' => 'Endpoint not found.'
        ]);
}