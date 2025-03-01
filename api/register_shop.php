<?php

include_once '../config/connect_db_api.php';
include_once '../model/shop.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$database = new Database();
$db = $database->getConnection();

$shop = new Shop($db);

$data = json_decode(file_get_contents("php://input"));

if (
    !empty($data->name) &&
    !empty($data->phone_number) &&
    !empty($data->address) &&
    !empty($data->contact_person)
) {
    $shop->name = $data->name;
    $shop->phone_number = $data->phone_number;
    $shop->address = $data->address;
    $shop->contact_person = $data->contact_person;

    if ($shop->create()) {
        http_response_code(201);
        echo json_encode(array("message" => "Shop was registered."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to register shop."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to register shop. Data is incomplete."));
}
