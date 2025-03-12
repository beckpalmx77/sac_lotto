<?php

require '../config/connect_lotto_db.php';
// ใช้ autoload ของ Composer
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

// ดึงข้อมูลจากตาราง ims_lotto
$sql = "SELECT * FROM ims_lotto";
$stmt = $conn->query($sql);

// สร้างเอกสาร Excel ใหม่
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// ตั้งชื่อแถวหัวสำหรับ Excel
$sheet->setCellValue('A1', 'ID')
    ->setCellValue('B1', 'Lotto Name')
    ->setCellValue('C1', 'Lotto Phone')
    ->setCellValue('D1', 'Lotto Province')
    ->setCellValue('E1', 'Lotto Number')
    ->setCellValue('F1', 'Sale Name')
    ->setCellValue('G1', 'Lotto File')
    ->setCellValue('H1', 'Lotto File1')
    ->setCellValue('I1', 'Lotto File2')
    ->setCellValue('J1', 'Lotto File3')
    ->setCellValue('K1', 'Lotto File4')
    ->setCellValue('L1', 'Lotto File5')
    ->setCellValue('M1', 'Client IP Address')
    ->setCellValue('N1', 'Approve Status')
    ->setCellValue('O1', 'Create Date')
    ->setCellValue('P1', 'Update Date');

// ตรวจสอบว่ามีข้อมูลหรือไม่
$rowNum = 2; // เริ่มจากแถวที่ 2 สำหรับข้อมูล

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // แทรกรูปภาพในแต่ละฟิลด์
    $sheet->setCellValue('A' . $rowNum, $row['id']);
    $sheet->setCellValue('B' . $rowNum, $row['lotto_name']);
    $sheet->setCellValue('C' . $rowNum, $row['lotto_phone']);
    $sheet->setCellValue('D' . $rowNum, $row['lotto_province']);
    $sheet->setCellValue('E' . $rowNum, $row['lotto_number']);
    $sheet->setCellValue('F' . $rowNum, $row['sale_name']);
    $sheet->setCellValue('M' . $rowNum, $row['client_ip_address']);
    $sheet->setCellValue('N' . $rowNum, $row['approve_status']);
    $sheet->setCellValue('O' . $rowNum, $row['create_date']);
    $sheet->setCellValue('P' . $rowNum, $row['update_date']);

    // เพิ่มรูปภาพจาก URL หรือ path ของรูปภาพ
    $imageFields = ['lotto_file', 'lotto_file1', 'lotto_file2', 'lotto_file3', 'lotto_file4', 'lotto_file5'];
    $col = 'G'; // เริ่มต้นที่คอลัมน์ G สำหรับ lotto_file

    foreach ($imageFields as $field) {
        $imagePath = '../uploads/' . $row[$field]; // เพิ่มเส้นทางรูปภาพจากโฟลเดอร์ ../uploads
        if (!empty($row[$field]) && file_exists($imagePath)) { // ตรวจสอบว่ามีไฟล์และไฟล์นั้นมีอยู่จริง
            $drawing = new Drawing();
            $drawing->setName($field);
            $drawing->setDescription($field);
            $drawing->setPath($imagePath);  // กำหนดเส้นทางไฟล์รูปภาพ
            $drawing->setHeight(100);  // กำหนดขนาดความสูงของรูปภาพ
            $drawing->setCoordinates($col . $rowNum);  // กำหนดตำแหน่งใน Excel (เช่น G2, H2, ...)
            $drawing->setWorksheet($sheet);  // แทรกรูปภาพลงใน Worksheet
        }
        $col++; // เพิ่มคอลัมน์สำหรับรูปภาพถัดไป
    }

    $rowNum++; // เพิ่มแถวถัดไป
}

// ตั้งค่าหัวข้อ HTTP สำหรับการดาวน์โหลดไฟล์
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="ims_lotto_data_' . date('Ymd_His') . '.xlsx"'); // ตั้งชื่อไฟล์พร้อม timestamp
header('Cache-Control: max-age=0');

// ทำให้ไฟล์ Excel ถูกส่งออก
$writer = new Xlsx($spreadsheet);

// สำรองข้อมูลและบันทึก
ob_end_clean(); // ป้องกันข้อผิดพลาดเกี่ยวกับการออกข้อมูลก่อนหน้านี้
$writer->save('php://output');

// ปิดการเชื่อมต่อฐานข้อมูล
$conn = null;
exit;