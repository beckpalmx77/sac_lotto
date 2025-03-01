<?php

// รับค่าจากฟอร์ม
$name = isset($_POST['name']) ? $_POST['name'] : '';
$phone_number = isset($_POST['phone_number']) ? $_POST['phone_number'] : '';
$address = isset($_POST['address']) ? $_POST['address'] : '';
$contact_person = isset($_POST['contact_person']) ? $_POST['contact_person'] : '';

// ข้อมูลที่ต้องการส่งไปยัง API
$data = array(
    'name' => $name,
    'phone_number' => $phone_number,
    'address' => $address,
    'contact_person' => $contact_person
);

// เปลี่ยนข้อมูลเป็น JSON
$data_json = json_encode($data);

// ตั้งค่า URL ของ API
$api_url = 'https://syycp.com/sac_lotto/api/register_shop.php';

// ตั้งค่าหัวข้อ HTTP
$options = array(
    'http' => array(
        'method' => 'POST',
        'header' => "Content-Type: application/json; charset=UTF-8\r\n" .
            "Accept: application/json\r\n",
        'content' => $data_json
    )
);

// สร้าง context
$context = stream_context_create($options);

// ส่งคำขอไปยัง API และรับผลลัพธ์
$response = file_get_contents($api_url, false, $context);

// ตรวจสอบผลลัพธ์
if ($response === FALSE) {
    echo json_encode(array('message' => 'ไม่สามารถติดต่อ API ได้'));
} else {
    // ส่งข้อมูลที่ได้รับจาก API กลับไปที่ฟอร์ม
    echo $response;
}


