<?php

require_once '../config/connect_lotto_db.php';
require '../vendor/autoload.php'; // Include PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$fileName = "sac_lotto_result_" . date('Y-m-d_H-i-s') . ".xlsx";

// Get input parameters
if (!isset($_POST['period_no'], $_POST['period_month'], $_POST['period_year'])) {
    die("Error: Missing parameters.");
}

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

    if (empty($results)) {
        throw new Exception("No data found for the specified period.");
    }

    // Create new spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Starting row for Excel data
    $rowCount = 1;

    foreach ($results as $row) {
        // Add header for each prize type
        $lotto_type_desc = $row->prize;
        $detail = $row->detail;

        // Write prize information to Excel
        $sheet->setCellValue("A$rowCount", "เลขที่ถูกรางวัล : $detail " . " คือ " . $row->lotto_number_result);
        $sheet->setCellValue("A" . ($rowCount + 1), "รางวัล: $lotto_type_desc");
        $sheet->setCellValue("A" . ($rowCount + 2), 'ลำดับ | ชื่อร้าน | หมายเลขโทรศัพท์ | จังหวัด | หมายเลขที่เลือก | ชื่อ Sale');

        // Increment row count
        $rowCount += 3;

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
            // Write lotto data to Excel
            $sheet->setCellValue("A$rowCount", "{$rank} | {$lotto['lotto_name']} | {$lotto['lotto_phone']} | {$lotto['lotto_province']} | {$lotto['lotto_number']} | {$lotto['sale_name']}");
            $rowCount++;
            $rank++;
        }

        // Add empty line as separator
        $rowCount++;
    }

    // Set headers to force browser to download the file
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1'); // To support IE 11

    // Write the file to output (browser)
    $writer = new Xlsx($spreadsheet);
    //$writer->save('php://output');
    $writer->save($fileName);

    exit; // Ensure no additional output is sent

} catch (Exception $e) {
    // Handle errors
    die("Error: " . $e->getMessage());
}

