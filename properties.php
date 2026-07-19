<?php

require_once __DIR__ . "/includes/bootstrap.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// GET /properties/{id}


$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


if (preg_match('#^/properties/(\d+)$#', $path, $matches)) {

    $id = (int)$matches[1];

    $stmt = $pdo->prepare(
        "SELECT *
         FROM properties
         WHERE id = :id"
    );

    $stmt->execute([
        ':id' => $id
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


    echo json_encode([
        "success" => true,
        "data" => $property,
        "message" => null
    ]);

    exit;
}



// GET /properties

$stmt = $pdo->query(
    "SELECT *
     FROM properties
     ORDER BY created_at DESC"
);

$properties = $stmt->fetchAll();


echo json_encode([
    "success" => true,
    "data" => $properties,
    "message" => null
]);