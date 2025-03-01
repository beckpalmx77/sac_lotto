<?php
require 'config.php';

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    echo json_encode(["error" => "No data received"]);
    exit();
}

$sql = "INSERT INTO shops (name, phone_number, address, contact_person) VALUES (:name, :phone, :address, :contact)";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":name", $data["name"]);
$stmt->bindParam(":phone", $data["phone_number"]);
$stmt->bindParam(":address", $data["address"]);
$stmt->bindParam(":contact", $data["contact_person"]);

if ($stmt->execute()) {
    echo json_encode(["message" => "Shop added successfully"]);
} else {
    echo json_encode(["error" => "Failed to add shop"]);
}
?>
