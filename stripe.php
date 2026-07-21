<?php

header("Access-Control-Allow-Origin: https://bookingfront-b9j1.onrender.com");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-CSRF-Token");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

//Load Stripe library
require_once __DIR__ . '/vendor/autoload.php';

// This loads Stripe + API key)
require_once __DIR__ . '/config.php';

require_once __DIR__ . '/middleware/auth.php';



if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    http_response_code(405);
    exit;
}



$input = json_decode(
    file_get_contents("php://input"),
    true
);



if (
    empty($input["booking_id"])
) {

    http_response_code(400);

    echo json_encode([
        "success"=>false,
        "message"=>"Missing booking id"
    ]);

    exit;
}



//Get booking

$stmt = $pdo->prepare(
"SELECT 
    b.*,
    p.title

 FROM bookings b

 JOIN properties p

 ON p.id=b.property_id

 WHERE b.id=:id
 AND b.user_id=:user_id"
);


$stmt->execute([

":id"=>$input["booking_id"],

":user_id"=>$_SESSION['user']['id']

]);



$booking=$stmt->fetch();

if ($booking && $booking["status"] === "paid") {

    http_response_code(409);

    echo json_encode([
        "success" => false,
        "message" => "Booking already paid"
    ]);

    exit;
}

if(!$booking){

    http_response_code(404);

    echo json_encode([

        "success"=>false,

        "message"=>"Booking not found"

    ]);

    exit;
}



$frontend = getenv("FRONTEND_URL");

if (!$frontend) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Frontend URL not configured"
    ]);
    exit;
}



$session = \Stripe\Checkout\Session::create([


    "payment_method_types"=>[

        "card"

    ],

    "client_reference_id" => (string)$booking["id"],

    "metadata" => ["booking_id" => $booking["id"]],


    "line_items"=>[[

        "price_data"=>[

            "currency"=>"eur",

            "product_data"=>[

                "name"=>$booking["title"]

            ],

            "unit_amount"=>
                intval($booking["total_price"] * 100)

        ],

        "quantity"=>1

    ]],


    "mode"=>"payment",

    
    "success_url"=>

        $frontend .
        "/success?booking_id=" .
        $booking["id"],


    "cancel_url"=>

        $frontend .
        "/cancel?booking_id=" .
        $booking["id"]

]);





// Save stripe session id

$stmt=$pdo->prepare(

    "UPDATE bookings

     SET stripe_session_id=:session,

     updated_at=NOW()

     WHERE id=:id"

);


$stmt->execute([

    ":session"=>$session->id,

    ":id"=>$booking["id"]

]);




echo json_encode([

    "success"=>true,
    "data"=>[
        "checkout_url"=>$session->url
    ]

]);