<?php

require_once '../config/connect_lotto_db.php';
require_once '../vendor/PhpXlsxGenerator-master/PhpXlsxGenerator.php';

$fileName = "sac_lotto_result_" . date('Y-m-d_H-i-s') . ".xlsx";

// Get input parameters
$period_no = $_POST['period_no'];
$period_month = $_POST['period_month'];
$period_year = $_POST['period_year'];

try {
    // Query to get prize details
    $String_Sql = "SELECT ims_lotto_period.*, prize.prize_id, prize.detail, prize.prize FROM ims_lotto_period
                    LEFT JOIN ims_lotto_prize prize ON prize.prize_id = ims_lotto_period.lotto_type
                    WHERE period_no = :period_no AND period_month = :period_month AND period_year = :period_year
                    ORDER BY lotto_type DESC";

    $query = $conn->prepare($String_Sql);
    $query->execute(['period_no' => $period_no, 'period_month' => $period_month, 'period_year' => $period_year]);
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    $excelData = [];

    if (empty($results)) {
        throw new Exception("No data found for the specified period.");
    }

    foreach ($results as $row) {
        // Add header for each prize type
        $lotto_type_desc = $row->prize;
        $detail = $row->detail;
        $excelData[] = ["เลขที่ถูกรางวัล : $detail"];
        $excelData[] = ["รางวัล: $lotto_type_desc"];
        $excelData[] = ['ลำดับ', 'ชื่อร้าน', 'หมายเลขโทรศัพท์', 'จังหวัด', 'หมายเลขที่เลือก', 'ชื่อ Sale'];

        // Prepare SQL based on lotto type
        if ($row->lotto_type == 2) {
            $where = "WHERE lotto_number = '" . $row->lotto_number_result . "'";
        } else {
            $last2 = substr($row->lotto_number_result, -2);
            $where = "WHERE lotto_number LIKE '%$last2'";
        }

        // Fetch lotto data
        $sql_str = "SELECT * FROM ims_lotto $where";
        $stmt_lotto = $conn->prepare($sql_str);
        $stmt_lotto->execute();

        $rank = 1;
        while ($lotto = $stmt_lotto->fetch(PDO::FETCH_ASSOC)) {
            $excelData[] = [
                $rank++,
                $lotto['lotto_name'],
                $lotto['lotto_phone'],
                $lotto['lotto_province'],
                $lotto['lotto_number'],
                $lotto['sale_name']
            ];
        }
        // Add empty row as separator
        $excelData[] = [""];
    }

    // Generate and download the Excel file
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');

    $xlsx = CodexWorld\PhpXlsxGenerator::fromArray($excelData);
    $xlsx->downloadAs($fileName);
    exit;

} catch (Exception $e) {
    // Handle errors
    die("Error: " . $e->getMessage());
}