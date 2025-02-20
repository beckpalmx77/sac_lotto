<?php
require_once '../config/connect_lotto_db.php';
require '../vendor/autoload.php'; // Load PhpSpreadsheet
include '../util/record_util.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

try {
    $str = rand();
    $seq_record = md5($str);

    if (isset($_FILES['excelFile']['name']) && $_FILES['excelFile']['error'] == UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['excelFile']['tmp_name'];
        $file_Upload = $_FILES['excelFile']['name']; // เก็บชื่อไฟล์ที่อัปโหลด

        $spreadsheet = IOFactory::load($fileTmp);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        $importedRows = 0;
        $updatedRows = 0;
        $duplicateRows = 0;
        $totalRows = count($rows) - 1; // ลบ 1 เพื่อไม่นับ header
        $table_name = "ims_customer_master"; // กำหนดชื่อตารางที่ใช้บันทึก log

        foreach ($rows as $index => $row) {
            if ($index == 0) continue; // ข้ามแถวหัวตาราง

            $customer_id = ($row[0] === "" || $row[0] === null) ? "-" : trim($row[0]);
            $customer_name = ($row[1] === "" || $row[1] === null) ? "-" : trim($row[1]);
            $province = ($row[2] === "" || $row[2] === null) ? "-" : $row[2];
            $sale_name = ($row[3] === "" || $row[3] === null) ? "-" : $row[3];

            // ตรวจสอบข้อมูลซ้ำ
            $statement = $conn->prepare("SELECT COUNT(*) FROM ims_customer_master WHERE customer_id = ? AND customer_name = ?");
            $statement->execute([$customer_id, $customer_name]);
            $count = $statement->fetchColumn();

            if ($count == 0) {
                // Insert new customer
                $stmt_insert_customer = $conn->prepare("INSERT INTO ims_customer_master (customer_id, customer_name, sale_name, province, seq_record) 
                VALUES (?, ?, ?, ?, ?)");
                $stmt_insert_customer->execute([$customer_id, $customer_name, $sale_name, $province, $seq_record]);
                $importedRows++;
            } else {
                // Update existing customer
                $stmt_update_customer = $conn->prepare("UPDATE ims_customer_master SET sale_name = ?, province = ?, seq_record = ? 
                WHERE customer_id = ? AND customer_name = ?");
                $stmt_update_customer->execute([$sale_name, $province, $seq_record, $customer_id, $customer_name]);
                $updatedRows++;
            }
        }

        // คำนวณข้อมูลซ้ำ (ข้อมูลที่มีอยู่แล้วแต่ไม่ได้อัปเดต)
        $duplicateRows = $totalRows - ($importedRows + $updatedRows);

        // บันทึก Log การนำเข้า
        $import_success = "จำนวนที่ Upload จาก Excel : $totalRows รายการ\nนำเข้าใหม่สำเร็จ: $importedRows รายการ\nอัปเดตข้อมูล: $updatedRows รายการ\nมีข้อมูลซ้ำที่ไม่ได้เปลี่ยนแปลง: $duplicateRows รายการ";

        $sql_insert_log = "INSERT INTO log_import_data (screen_name, total_record, import_record, detail1, detail2, seq_record) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert_log = $conn->prepare($sql_insert_log);
        $stmt_insert_log->execute([$table_name, $totalRows, $importedRows, $import_success, $file_Upload, $seq_record]);

        echo "Imported: $importedRows, Updated: $updatedRows, Duplicates Skipped: $duplicateRows";
    } else {
        echo "No file uploaded or error in file upload.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
