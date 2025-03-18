<?php

$secret = 'secret_qOxpxIdpXHDH1sLt'; // ใส่ API Key ของ ConvertAPI

$url = 'https://v2.convertapi.com/convert/heic/to/jpg?Secret=' . $secret;

// เปิดไฟล์ HEIC
$filePath = '../uploads/file_67d8cf0cac46f7.55294372.heic';
$file = new CURLFile($filePath, 'image/heic');

// ส่งคำขอแปลงไฟล์
$postData = [
    'File' => $file
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

/*
curl -X POST https://v2.convertapi.com/convert/heic/to/jpg \
 -H "Authorization: Bearer secret_qOxpxIdpXHDH1sLt" \
-F "File=@/path/to/my_file.heic" \
-F "StoreFile=true"
*/

$response = curl_exec($ch);
curl_close($ch);

$responseData = json_decode($response, true);
if (isset($responseData['Files'][0]['Url'])) {
    echo "<img src='" . $responseData['Files'][0]['Url'] . "' alt='Converted Image'>";
} else {
    echo "❌ เกิดข้อผิดพลาดในการแปลงไฟล์ HEIC";
}

