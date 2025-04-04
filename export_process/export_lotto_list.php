<?php


require_once '../config/connect_lotto_db.php';

// Include XLSX generator library
require_once '../vendor/PhpXlsxGenerator-master/PhpXlsxGenerator.php';

// Excel file name for download
$fileName = "sac_lotto_list" . "-" . date('m/d/Y H:i:s', time()) . ".xlsx";

@header('Content-type: text/csv; charset=UTF-8');
@header('Content-Encoding: UTF-8');
@header("Content-Disposition: attachment; filename=" . $filename);

// Define column names
$excelData[] = array('ลำดับ', 'ชื่อร้าน', 'หมายเลขโทรศัพท์', 'จังหวัด', 'หมายเลขที่เลือก', 'ชื่อ Sale', 'วันที่บันทึก', 'สถานะการอนุมัติ','หมายเหตุ');

// Fetch records from database and store in an array

$select_query_daily = "  SELECT * FROM ims_lotto ORDER BY id ";

$String_Sql = $select_query_daily ;

$query = $conn->prepare($String_Sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

if ($query->rowCount() >= 1) {
    $line_no = 0;
    foreach ($results as $result) {
        $line_no++;
        $create_date = $result->create_date;

        // ตรวจสอบว่า $create_date เป็น DateTime object หรือไม่
        if (!$create_date instanceof DateTime) {
            $create_date = new DateTime($create_date); // แปลงสตริงเป็น DateTime object
        }

        $approve_status_detail = $result->approve_status === "Y" ? "อนุมัติ" : "ยังไม่อนุมัติ";
        $lineData = array(
            $line_no,
            $result->lotto_name,
            strval($result->lotto_phone),
            $result->lotto_province,
            strval($result->lotto_number),
            $result->sale_name,
            $create_date->format('d-m-Y H:i:s'), // แปลงวันที่ให้เป็นรูปแบบที่ต้องการ
            $approve_status_detail,
            $result->remark
        );
        $excelData[] = $lineData;
    }
}

// Export data to excel and download as xlsx file
$xlsx = CodexWorld\PhpXlsxGenerator::fromArray($excelData);
$xlsx->downloadAs($fileName);

exit;

