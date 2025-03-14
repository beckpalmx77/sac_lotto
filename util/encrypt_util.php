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

// ฟังก์ชันสำหรับการเขียนข้อมูลที่เข้ารหัสลงในไฟล์
function write_encrypted_data_to_file($file, $data, $key)
{
    $encrypted_data = encrypt_data($data, $key);
    file_put_contents($file, $encrypted_data . PHP_EOL, FILE_APPEND);
}

// ฟังก์ชันสำหรับการอ่านข้อมูลจากไฟล์และถอดรหัส
function read_encrypted_data_from_file($file, $key)
{
    $lines = file($file, FILE_IGNORE_NEW_LINES);  // อ่านไฟล์เป็นอาเรย์
    $decrypted_data = [];

    foreach ($lines as $line) {
        $decrypted_data[] = decrypt_data($line, $key);  // ถอดรหัสทุกบรรทัดในไฟล์
    }

    return $decrypted_data;
}

// ตรวจสอบว่าใช้งานฟังก์ชันไหน
if ($argc == 1) {
    echo "Usage: php encrypt_util.php <action> <data>\n";
    echo "<action> can be 'encrypt' or 'decrypt'\n";
    echo "<data> is the data to encrypt or decrypt\n";
    exit();
}

// ดึงข้อมูลจาก command line
$action = $argv[1];
$key = 'your-secret-key';  // คีย์สำหรับการเข้ารหัส/ถอดรหัส
$data = isset($argv[2]) ? $argv[2] : null;

if ($action == 'encrypt' && $data) {
    // เข้ารหัสข้อมูล
    echo "Encrypted Data: " . encrypt_data($data, $key) . "\n";
} elseif ($action == 'decrypt' && $data) {
    // ถอดรหัสข้อมูล
    echo "Decrypted Data: " . decrypt_data($data, $key) . "\n";
} else {
    echo "Invalid action or missing data.\n";
}
