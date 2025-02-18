<?php
require_once '../config/connect_lotto_db.php';
require_once '../vendor/PhpXlsxGenerator-master/PhpXlsxGenerator.php';
require_once '../util/month_util.php';

try {
    // รับค่าจาก URL
    $period_no = $_GET['period_no'] ;
    $period_month = $_GET['period_month'] ;
    $period_year = $_GET['period_year'] ;

    // ตรวจสอบค่าที่รับมา
    if (empty($period_no) || empty($period_month) || empty($period_year)) {
        throw new Exception("กรุณากรอกข้อมูลให้ครบถ้วน");
    }

    // สร้างชื่อไฟล์
    $fileName = "sac_lotto_result_" . date('Y-m-d_H-i-s') . ".xlsx";

    // Query ข้อมูลรางวัล
    $sql = "SELECT ims_lotto_period.*, prize.prize_id, prize.detail, prize.prize 
            FROM ims_lotto_period
            LEFT JOIN ims_lotto_prize prize ON prize.prize_id = ims_lotto_period.lotto_type
            WHERE period_no = :period_no AND period_month = :period_month AND period_year = :period_year
            ORDER BY lotto_type DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':period_no', $period_no, PDO::PARAM_STR);
    $stmt->bindParam(':period_month', $period_month, PDO::PARAM_STR);
    $stmt->bindParam(':period_year', $period_year, PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_OBJ);

    // ตรวจสอบผลลัพธ์
    if (!$results) {
        throw new Exception("ไม่พบข้อมูลสำหรับงวดที่ระบุ");
    }
    $month_name = isset($month_arr[$period_month]) ? $month_arr[$period_month] : 'ไม่พบเดือน';
    $excelData[] = ["ผลการออกรางวัลงวดวันที่ : " . $period_no . " เดือน : " . $month_name . " ปี : " . $period_year];
    $excelData[] = [""];  // เพิ่มบรรทัดว่างก่อนเริ่มข้อมูลรางวัล
    foreach ($results as $row) {
        $excelData[] = ["เลขที่ถูกรางวัล : {$row->detail}" . " คือ {$row->lotto_number_result}"];
        $excelData[] = ["รางวัล: {$row->prize}"];
        $excelData[] = ['ลำดับที่', 'ชื่อร้าน', 'หมายเลขโทรศัพท์', 'จังหวัด', 'หมายเลขที่เลือก', 'ชื่อ Sale'];

        // เตรียมเงื่อนไข SQL ตามประเภทของรางวัล
        if ($row->lotto_type == 2) {
            $sql_lotto = "SELECT * FROM ims_lotto WHERE lotto_number = :lotto_number";
            $stmt_lotto = $conn->prepare($sql_lotto);
            $stmt_lotto->bindParam(':lotto_number', $row->lotto_number_result, PDO::PARAM_STR);
        } else {
            $last2 = substr($row->lotto_number_result, -2);
            $sql_lotto = "SELECT * FROM ims_lotto WHERE lotto_number LIKE :pattern";
            $pattern = "%$last2";
            $stmt_lotto = $conn->prepare($sql_lotto);
            $stmt_lotto->bindParam(':pattern', $pattern, PDO::PARAM_STR);
        }

        // ดึงข้อมูลและเพิ่มลงใน Excel
        $stmt_lotto->execute();
        $rank = 1;
        while ($lotto = $stmt_lotto->fetch(PDO::FETCH_ASSOC)) {
            $excelData[] = [
                str_pad($rank++, 5, ' ', STR_PAD_RIGHT), // จัดชิดซ้าย
                $lotto['lotto_name'],
                $lotto['lotto_phone'],
                $lotto['lotto_province'],
                $lotto['lotto_number'],
                $lotto['sale_name']
            ];
        }

        // เพิ่มบรรทัดว่างเพื่อแบ่งกลุ่ม
        $excelData[] = [""];
    }

    // สร้างไฟล์ Excel
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');

    $xlsx = CodexWorld\PhpXlsxGenerator::fromArray($excelData);
    $xlsx->downloadAs($fileName);
    exit;

} catch (Exception $e) {
    die("❌ Error: " . $e->getMessage());
}

