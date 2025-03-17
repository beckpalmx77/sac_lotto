<?php

// ฟังก์ชันสำหรับการเข้ารหัส
function encrypt_data($data, $key)
{
    $cipher = "aes-256-cbc";
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
    $encrypted_data = openssl_encrypt($data, $cipher, $key, 0, $iv);
    return base64_encode($encrypted_data . '::' . $iv);
}

// ฟังก์ชันสำหรับการถอดรหัส
function decrypt_data($encrypted_data, $key)
{
    $cipher = "aes-256-cbc";
    list($encrypted_data, $iv) = explode('::', base64_decode($encrypted_data), 2);
    return openssl_decrypt($encrypted_data, $cipher, $key, 0, $iv);
}

// รับค่าจาก AJAX (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST['data'];
    $action = $_POST['action'];
    $key = $_POST['key'];  // คีย์สำหรับการเข้ารหัส/ถอดรหัส

    // ตรวจสอบการทำงานที่เลือกในฟอร์ม
    if ($action == 'encrypt' && !empty($data)) {
        // เข้ารหัสข้อมูล
        echo "Encrypted Data: \n\r" . encrypt_data($data, $key);
    } elseif ($action == 'decrypt' && !empty($data)) {
        // ถอดรหัสข้อมูล
        echo "Decrypted Data: \n\r" . decrypt_data($data, $key);
    } else {
        echo "Invalid action or missing data.";
    }
}

