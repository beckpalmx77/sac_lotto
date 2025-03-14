<?php
// ฟังก์ชันสำหรับการถอดรหัส
function decrypt_data($encrypted_data, $key) {
    $cipher = "aes-256-cbc";
    list($encrypted_data, $iv) = explode('::', base64_decode($encrypted_data), 2);  // แยกข้อมูลและ IV
    return openssl_decrypt($encrypted_data, $cipher, $key, 0, $iv);  // ถอดรหัสข้อมูลและคืนค่า
}
