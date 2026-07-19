<?php

require_once __DIR__ . '/middleware/auth.php';

header("Content-Type: application/json");


$method = $_SERVER['REQUEST_METHOD'];



// ======================
// GET COMMENTS
// ======================

if ($method === "GET") {


    $propertyId = $_GET['property_id'] ?? null;


    if (!$propertyId) {

        http_response_code(400);

        echo json_encode([
            "success"=>false,
            "message"=>"Missing property id"
        ]);

        exit;
    }



    $stmt = $pdo->prepare(

        "SELECT

            pc.id,
            pc.comment,
            pc.created_at,

            u.name

        FROM property_comments pc

        JOIN users u

        ON u.id = pc.user_id


        WHERE pc.property_id = :property_id


        ORDER BY pc.created_at DESC"

    );


    $stmt->execute([
        ":property_id"=>$propertyId
    ]);



    echo json_encode([

        "success"=>true,

        "data"=>$stmt->fetchAll()

    ]);


    exit;

}





// ======================
// CREATE COMMENT
// ======================


if ($method === "POST") {


    $input=json_decode(
        file_get_contents("php://input"),
        true
    );


    if (
        empty($input['property_id']) ||
        empty($input['comment'])
    ) {


        http_response_code(400);


        echo json_encode([

            "success"=>false,

            "message"=>"Missing data"

        ]);

        exit;
    }



    $comment = trim($input['comment']);



    if(strlen($comment)>1000){

        http_response_code(400);

        echo json_encode([

            "success"=>false,

            "message"=>"Comment too long"

        ]);

        exit;

    }



    $stmt=$pdo->prepare(

        "INSERT INTO property_comments

        (
            property_id,
            user_id,
            comment
        )

        VALUES

        (
            :property,
            :user,
            :comment
        )"

    );



    $stmt->execute([

        ":property"=>$input['property_id'],

        ":user"=>$_SESSION['user']['id'],

        ":comment"=>$comment

    ]);



    echo json_encode([

        "success"=>true

    ]);


    exit;

}