<?php

require_once('../vendor/convertapi/convertapi-php/lib/ConvertApi/autoload.php');

use \ConvertApi\ConvertApi;

# set your api secret or token
ConvertApi::setApiCredentials(getenv('secret_qOxpxIdpXHDH1sLt'));

# กำหนดตำแหน่งไฟล์ HEIC
$dir = sys_get_temp_dir();
$heicFile = '../uploads/file_67d8cf0cac46f7.55294372.heic'; // เปลี่ยน path ตามที่เก็บไฟล์ HEIC ของคุณ

# ใช้ wrapper สำหรับการอัปโหลดไฟล์ไปยัง API
$upload = new \ConvertApi\FileUpload($heicFile);

# แปลง HEIC เป็น JPG
$result = ConvertApi::convert('jpg', ['File' => $upload]);

# บันทึกไฟล์ที่แปลงแล้วในไดเรกทอรี
$savedFiles = $result->saveFiles($dir);

echo "The JPG saved to:\n";
print_r($savedFiles);




