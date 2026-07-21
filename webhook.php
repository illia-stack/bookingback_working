<?php

require_once __DIR__ . '/includes/bootstrap.php';

require_once __DIR__ . '/vendor/autoload.php';


\Stripe\Stripe::setApiKey(
    getenv("STRIPE_SECRET_KEY")
);

$payload = @file_get_contents("php://input");

$sigHeader = $_SERVER["HTTP_STRIPE_SIGNATURE"] ?? "";


try {

    $event = \Stripe\Webhook::constructEvent(

        $payload,

        $sigHeader,

        getenv("STRIPE_WEBHOOK_SECRET")

    );

 } catch (\UnexpectedValueException $e) {

    http_response_code(400);
    exit;

} catch (\Stripe\Exception\SignatureVerificationException $e) {

    http_response_code(400);
    exit;

}

if ($event->type !== "checkout.session.completed") {

    http_response_code(200);

    echo json_encode([
        "received" => true
    ]);

    exit;
}



$session = $event->data->object;


$stmt = $pdo->prepare(

    "SELECT id
     FROM bookings
     WHERE stripe_session_id = :session"

);

$stmt->execute([

    ":session" => $session->id

]);



$booking = $stmt->fetch();

if (!$booking) {
    http_response_code(404);
    exit;
}



$stmt = $pdo->prepare(

"
UPDATE bookings

SET

status='paid',

stripe_payment_intent=:payment,

paid_at=NOW(),

updated_at=NOW()

WHERE id=:id
AND status <> 'paid'
"
);


$stmt->execute([

    ":payment" => $session->payment_intent,

    ":id" => $booking["id"]

]);

 
http_response_code(200);

echo json_encode([
    "received" => true
]);
