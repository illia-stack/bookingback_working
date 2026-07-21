<?php

require_once __DIR__ . "/includes/bootstrap.php";


$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (isset($_GET['id'])) {

    $id = (int)$_GET['id'];

    $stmt = $pdo->prepare(
        "SELECT *
         FROM properties
         WHERE id = :id"
    );

    $stmt->execute([
        ':id' => $id
    ]);

    
    $property = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$property) {

        http_response_code(404);

        echo json_encode([
            "success" => false,
            "data" => null,
            "message" => "Property not found"
        ]);

        exit;
    }


    echo json_encode([
        "success" => true,
        "data" => $property,
        "message" => null
    ]);

    exit;
}



// Get all properties

$stmt = $pdo->query(
    "SELECT *
     FROM properties
     ORDER BY created_at DESC"
);

$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);


echo json_encode([
    "success" => true,
    "data" => $properties,
    "message" => null
]);