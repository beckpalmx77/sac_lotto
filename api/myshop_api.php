<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$host = "192.168.88.7";
$dbname = "sac_lotto";
$username = "myadmin";  // เปลี่ยนตามเซิร์ฟเวอร์
$password = "myadmin";      // เปลี่ยนตามเซิร์ฟเวอร์
$port = 3307;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["error" => "Database connection failed: " . $e->getMessage()]);
    exit();
}


