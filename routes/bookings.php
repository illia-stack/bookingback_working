<?php

require_once __DIR__ . '/../middleware/auth.php';

$method = $_SERVER['REQUEST_METHOD'];


// CREATE BOOKING
if ($method === "POST") {


    $input = json_decode(
        file_get_contents("php://input"),
        true
    );


    if (
        empty($input['property_id']) ||
        empty($input['check_in']) ||
        empty($input['check_out'])
    ) {

        http_response_code(400);

        echo json_encode([
            "success" => false,
            "data" => null,
            "message" => "Missing booking data"
        ]);

        exit;
    }



    $userId = $_SESSION['user']['id'];


    $stmt = $pdo->prepare(
        "SELECT *
         FROM properties
         WHERE id = :id"
    );


    $stmt->execute([
        ':id' => $input['property_id']
    ]);


    $property = $stmt->fetch();


    if (!$property) {

        http_response_code(404);

        echo json_encode([
            "success" => false,
            "data" => null,
            "message" => "Property not found"
        ]);

        exit;
    }

    $checkIn = strtotime($input['check_in']);
    $checkOut = strtotime($input['check_out']);

    if ($checkOut <= $checkIn) {

        http_response_code(400);

        echo json_encode([
            "success"=>false,
            "message"=>"Invalid dates"
        ]);

        exit;
    }

    $days =
        (strtotime($input['check_out']) -
        strtotime($input['check_in']))
        / 86400;


    $total =
        $days * $property['price_per_night'];



    $stmt = $pdo->prepare(

        "INSERT INTO bookings
        (
            user_id,
            property_id,
            check_in,
            check_out,
            total_price,
            status,
            created_at,
            updated_at
        )
        VALUES
        (
            :user_id,
            :property_id,
            :check_in,
            :check_out,
            :total_price,
            'pending',
            NOW(),
            NOW()
        )"

    );


    $stmt->execute([

        ':user_id' => $userId,

        ':property_id' => $input['property_id'],

        ':check_in' => $input['check_in'],

        ':check_out' => $input['check_out'],

        ':total_price' => $total

    ]);


    $bookingId = $pdo->lastInsertId();



    echo json_encode([

        "success" => true,

        "data" => [

            "booking_id" => $bookingId,

            // temporary placeholder
            "checkout_url" =>
                "https://bookingback-working.onrender.com/success?booking_id=".$bookingId

        ],

        "message" => null

    ]);

    exit;

}




// GET MY BOOKINGS

if ($method === "GET") {


    $userId = $_SESSION['user']['id'];


    $stmt = $pdo->prepare(

        "SELECT

            b.*,

            p.title,
            p.city,
            p.image_url

        FROM bookings b

        JOIN properties p

        ON p.id = b.property_id

        WHERE b.user_id = :user_id

        ORDER BY b.created_at DESC"

    );


    $stmt->execute([
        ':user_id'=>$userId
    ]);


    $bookings = $stmt->fetchAll();


    echo json_encode([

        "success"=>true,

        "data"=>$bookings,

        "message"=>null

    ]);

    exit;

}