<?php
// ตั้งค่าหมายเลขการตอบกลับ (response code)
$response = [
    'status' => false,
    'message' => 'File upload failed!',
    'url' => ''
];

// ตรวจสอบว่ามีการส่งไฟล์มาในฟอร์มหรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    // ระบุที่เก็บไฟล์ที่อัปโหลด
    $uploadDir = '../uploads/'; // โฟลเดอร์ที่ต้องการเก็บไฟล์
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // สร้างโฟลเดอร์หากยังไม่มี
    }

    // ตั้งชื่อไฟล์ใหม่ (สามารถตั้งชื่อให้เหมาะสมได้)
    $fileName = uniqid() . '-' . basename($_FILES['image']['name']);
    $filePath = $uploadDir . $fileName;

    // ตรวจสอบชนิดไฟล์
    $fileType = mime_content_type($_FILES['image']['tmp_name']);
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif']; // ประเภทไฟล์ที่อนุญาต

    if (in_array($fileType, $allowedTypes)) {
        // ย้ายไฟล์จาก temp folder ไปที่ที่เก็บที่กำหนด
        if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
            // เมื่ออัปโหลดสำเร็จ, ส่ง URL ของไฟล์กลับไป
            $response['status'] = true;
            $response['message'] = 'File uploaded successfully!';
            $response['url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $filePath;
        }
    } else {
        $response['message'] = 'Invalid file type.';
    }
} else {
    $response['message'] = 'No file uploaded.';
}

// ส่งข้อมูลตอบกลับในรูปแบบ JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
