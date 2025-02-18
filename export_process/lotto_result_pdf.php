<?php
require_once '../config/connect_lotto_db.php';
require_once '../vendor/autoload.php'; // TCPDF
require_once '../util/month_util.php';

// แปลงฟอนต์ หากยังไม่เคยทำมาก่อน
if (!file_exists('../vendor/tcpdf/fonts/thsarabunnew.php')) {
    TCPDF_FONTS::addTTFfont('../vendor/tcpdf/fonts/THSarabunNew.ttf', 'TrueTypeUnicode', '');
    TCPDF_FONTS::addTTFfont('../vendor/tcpdf/fonts/THSarabunNew-Bold.ttf', 'TrueTypeUnicode', '');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $period_no = $_POST['period_no'];
    $period_month = $_POST['period_month'];
    $period_year = $_POST['period_year'];

    $sql = "SELECT p.*, pr.prize_id, pr.detail, pr.prize 
            FROM ims_lotto_period p
            LEFT JOIN ims_lotto_prize pr ON pr.prize_id = p.lotto_type
            WHERE period_no = :period_no AND period_month = :period_month AND period_year = :period_year 
            ORDER BY lotto_type DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute(['period_no' => $period_no, 'period_month' => $period_month, 'period_year' => $period_year]);

    // เริ่มสร้าง PDF
    $pdf = new TCPDF();
    $pdf->SetFont('thsarabunnew', '', 14);
    $pdf->AddPage();

    $month_name = isset($month_arr[$period_month]) ? $month_arr[$period_month] : 'ไม่พบเดือน';
    // แสดงข้อมูลใน Header

    $pdf->Cell(0, 10, 'ผลการออกรางวัลงวดวันที่ ' . $period_no . ' เดือน ' . $month_name . ' ปี ' . $period_year, 0, 1, 'C');
    $pdf->Ln(5); // เว้นบรรทัดหลังจาก Header

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lotto_type_desc = $row['lotto_type'] == 2 ? "เลขท้าย 3 ตัว" : "เลขท้าย 2 ตัว";
            $where = $row['lotto_type'] == 2
                ? "WHERE lotto_number = :num"
                : "WHERE lotto_number LIKE :pattern";

            $stmtLotto = $conn->prepare("SELECT * FROM ims_lotto $where");
            if ($row['lotto_type'] == 2) {
                $stmtLotto->execute(['num' => $row['lotto_number_result']]);
            } else {
                $last2 = substr($row['lotto_number_result'], -2);
                $stmtLotto->execute(['pattern' => "%$last2"]);
            }

            // แสดงหัวข้อรางวัล
            $pdf->Cell(0, 10, "$lotto_type_desc รางวัล: {$row['prize']} บาท เลข: {$row['lotto_number_result']}", 0, 1, 'L');
            $pdf->Ln(5);

            // แสดงตารางผู้ถูกรางวัล
            if ($stmtLotto->rowCount() > 0) {
                $pdf->SetFillColor(200, 220, 255);
                $pdf->Cell(10, 7, 'ลำดับ', 1, 0, 'C', true);
                $pdf->Cell(40, 7, 'ชื่อ', 1, 0, 'C', true);
                $pdf->Cell(30, 7, 'หมายเลข', 1, 0, 'C', true);
                $pdf->Cell(30, 7, 'โทรศัพท์', 1, 0, 'C', true);
                $pdf->Cell(30, 7, 'จังหวัด', 1, 0, 'C', true);
                $pdf->Ln();

                $rank = 1;
                while ($lotto = $stmtLotto->fetch(PDO::FETCH_ASSOC)) {
                    $pdf->Cell(10, 7, $rank++, 1, 0, 'L');
                    $pdf->Cell(40, 7, $lotto['lotto_name'], 1, 0, 'L');
                    $pdf->Cell(30, 7, $lotto['lotto_number'], 1, 0, 'L');
                    $pdf->Cell(30, 7, $lotto['lotto_phone'], 1, 0, 'L');
                    $pdf->Cell(30, 7, $lotto['lotto_province'], 1, 0, 'L');
                    $pdf->Ln();
                }
            } else {
                $pdf->Cell(0, 7, "ไม่มีผู้ถูกรางวัล", 0, 1, 'L');
            }
            $pdf->Ln(10);
        }
    } else {
        $pdf->Cell(0, 10, "ไม่พบข้อมูล", 0, 1, 'L');
    }

    $uniqueName = 'lotto_results_' . time() . '.pdf';  // Using timestamp for uniqueness
    $pdf->Output($uniqueName, 'D');
}

