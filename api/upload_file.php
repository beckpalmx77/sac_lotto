<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$target_dir = "../uploads/";
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

if ($_FILES["file"]["error"] == UPLOAD_ERR_OK) {
    $target_file = $target_dir . basename($_FILES["file"]["name"]);

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        echo json_encode(["message" => "อัปโหลดไฟล์สำเร็จ", "path" => $target_file]);
    } else {
        echo json_encode(["message" => "เกิดข้อผิดพลาดในการอัปโหลด"]);
    }
} else {
    echo json_encode(["message" => "ไม่มีไฟล์ถูกส่งมา"]);
}

