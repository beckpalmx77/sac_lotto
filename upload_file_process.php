<?php

$upload_url = "http://171.100.56.194:8888/sac_lotto/upload.php"; // URL ปลายทาง
//$upload_url = "http://localhost:8888/sac_lotto/upload.php"; // URL ปลายทาง
$local_folder = __DIR__ . "\\uploads\\"; // โฟลเดอร์ไฟล์ที่ต้องการอัปโหลด

/*
$my_file = fopen("local_folder.txt", "w") or die("Unable to open file!");
fwrite($my_file, " local_folder = " . $local_folder);
fclose($my_file);
*/

// ตรวจสอบว่ามีไฟล์ในโฟลเดอร์หรือไม่
$files = glob($local_folder . "*.*"); // ดึงไฟล์ทั้งหมดในโฟลเดอร์
if (!$files) {
    echo "ไม่มีไฟล์ที่ต้องอัปโหลด\n";
    exit;
}

foreach ($files as $file_path) {
    $cfile = new CURLFile($file_path, mime_content_type($file_path), basename($file_path));

    $data = array("fileToUpload" => $cfile);
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $upload_url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // ปิด SSL Verify หากเซิร์ฟเวอร์ไม่มี SSL ที่ถูกต้อง

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    // แสดงผลลัพธ์
    if ($http_code == 200) {
        echo "✅ อัปโหลดสำเร็จ: " . basename($file_path) . "\n";
        //unlink($file_path); // ลบไฟล์ต้นฉบับหลังอัปโหลดสำเร็จ
    } else {
        echo "❌ อัปโหลดล้มเหลว: " . basename($file_path) . " → " . $response . "\n";
    }

}