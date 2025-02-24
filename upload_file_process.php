<?php

$upload_url = "http://171.100.56.194:8888/file_uploads/sac_lotto/upload.php"; // URL ปลายทาง
//$upload_url = "http://localhost:8888/sac_lotto/upload.php"; // URL ปลายทาง

// ตรวจสอบระบบปฏิบัติการ
$os = strtolower(PHP_OS);
$local_folder = __DIR__ . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR;

if (strpos($os, 'win') !== false) {
    $local_folder = __DIR__ . "\\uploads\\";
} elseif (strpos($os, 'linux') !== false) {
    if (file_exists('/etc/os-release')) {
        $os_info = file_get_contents('/etc/os-release');
        if (strpos($os_info, 'Ubuntu') !== false) {
            $local_folder = __DIR__ . "/uploads/";
        }
    }
}

// แสดงผลระบบปฏิบัติการและโฟลเดอร์ที่ใช้
echo "ระบบที่ใช้งาน: " . PHP_OS . "\n";
echo "โฟลเดอร์ที่ใช้: " . $local_folder . "\n";

// ตรวจสอบว่าโฟลเดอร์มีอยู่จริง ถ้าไม่มีให้สร้างขึ้นมา
if (!is_dir($local_folder)) {
    mkdir($local_folder, 0777, true);
}

// กำหนดวันที่ปัจจุบัน (ใช้เปรียบเทียบ)
$current_date = date("Y-m-d");

// ตรวจสอบไฟล์ในโฟลเดอร์
$files = glob($local_folder . "*.*");
if (!$files) {
    echo "ไม่มีไฟล์ที่ต้องอัปโหลด\n";
    exit;
}

$uploaded_count = 0;
foreach ($files as $file_path) {
    if (!file_exists($file_path)) {
        echo "❌ ไฟล์ไม่พบ: " . basename($file_path) . "\n";
        continue;
    }

    // ตรวจสอบว่าไฟล์ถูกสร้างหรือแก้ไขล่าสุดในวันปัจจุบันหรือไม่
    $file_date = date("Y-m-d", filemtime($file_path));
    if ($file_date !== $current_date) {
        echo "⏭ ข้ามไฟล์: " . basename($file_path) . " (อัปเดตล่าสุด: $file_date)\n";
        continue;
    }

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
        $uploaded_count++;
        //unlink($file_path); // ลบไฟล์ต้นฉบับหลังอัปโหลดสำเร็จ
    } else {
        echo "❌ อัปโหลดล้มเหลว: " . basename($file_path) . " → " . $response . "\n";
    }
}

// สรุปผล
if ($uploaded_count === 0) {
    echo "📌 ไม่มีไฟล์ที่อัปโหลดในวันนี้ ($current_date)\n";
} else {
    echo "🎉 อัปโหลดไฟล์สำเร็จทั้งหมด: $uploaded_count ไฟล์\n";
}