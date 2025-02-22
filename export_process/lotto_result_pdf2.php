<?php
require_once '../config/connect_lotto_db.php';
require_once '../vendor/autoload.php'; // mPDF
require_once '../util/month_util.php';

class CustomPDF {
    public function createPDF($conn, $period_no, $period_month, $month_name, $period_year) {
        $defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'fontDir' => array_merge($fontDirs, [__DIR__ . '/../vendor/mpdf/mpdf/ttfontdata']),
            'fontdata' => array_merge($fontData, [
                'prompt' => [ // ✅ ใช้ฟอนต์ Prompt
                    'R' => 'Prompt-Regular.ttf',
                    'B' => 'Prompt-Bold.ttf',
                    'I' => 'Prompt-Italic.ttf',
                    'BI' => 'Prompt-BoldItalic.ttf'
                ]
            ]),
            'default_font' => 'prompt'
        ]);

        $mpdf->AddPage();
/*
        $my_file = fopen("period.txt", "w") or die("Unable to open file!");
        fwrite($my_file, " period = " . $period_no . " | " .$period_month . " | " . $period_year);
        fclose($my_file);
*/

        //$mpdf->Image('../img/logo/Logo-01.png', 10, 10, 30, 0, 'PNG');
        $mpdf->Image('../img/logo/Logo-01.png', 10, 10, 20, 0, 'PNG');

        $mpdf->SetFont('prompt', 'B', 20);
        $mpdf->WriteHTML('<div style="text-align:center;">ผลการออกรางวัล งวดวันที่ ' . $period_no . ' เดือน ' . $month_name . ' ปี ' . $period_year . '</div>');
        $mpdf->Ln(5);

        $sql = "SELECT p.*, pr.prize_id, pr.detail, pr.prize FROM ims_lotto_period p
                LEFT JOIN ims_lotto_prize pr ON pr.prize_id = p.lotto_type
                WHERE period_no = :period_no AND period_month = :period_month AND period_year = :period_year 
                ORDER BY lotto_type DESC";

        $stmt = $conn->prepare($sql);
        $stmt->execute(['period_no' => $period_no, 'period_month' => $period_month, 'period_year' => $period_year]);

        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $lotto_type_desc = $row['lotto_type'] == 2 ? "เลขท้าย 3 ตัว" : "เลขท้าย 2 ตัว";
                $mpdf->SetFont('prompt', 'B', 16);
                $mpdf->WriteHTML('<div>' . "$lotto_type_desc รางวัล: {$row['prize']} บาท เลข: {$row['lotto_number_result']}" . '</div>');
                $mpdf->Ln(5);

                $mpdf->SetFont('prompt', '', 14);
                $mpdf->WriteHTML($this->generateTable($conn, $row));
                $mpdf->Ln(10);
            }
        } else {
            $mpdf->WriteHTML('<div>ไม่พบข้อมูล</div>');
        }

        $mpdf->SetFooter('หน้า {PAGENO} / {nb}');
        $uniqueName = 'lotto_results_' . time() . '.pdf';
        $mpdf->Output($uniqueName, 'D');
    }

    private function generateTable($conn, $row) {
        $where = $row['lotto_type'] == 2 ? "WHERE lotto_number = :num" : "WHERE lotto_number LIKE :pattern";
        $stmtLotto = $conn->prepare("SELECT * FROM ims_lotto $where");

        if ($row['lotto_type'] == 2) {
            $stmtLotto->execute(['num' => $row['lotto_number_result']]);
        } else {
            $last2 = substr($row['lotto_number_result'], -2);
            $stmtLotto->execute(['pattern' => "%$last2"]);
        }

        if ($stmtLotto->rowCount() > 0) {
            $html = '<table border="1" style="border-collapse:collapse; width:100%;">';
            $html .= '<tr style="background-color: #c8dcff;">
                        <th style="text-align:center; padding:5px;">ลำดับ</th>
                        <th style="text-align:center; padding:5px;">ชื่อ</th>
                        <th style="text-align:center; padding:5px;">โทรศัพท์</th>
                        <th style="text-align:center; padding:5px;">จังหวัด</th>
                        <th style="text-align:center; padding:5px;">หมายเลข</th>
                        <th style="text-align:center; padding:5px;">ชื่อ sale</th>
                      </tr>';

            $rank = 1;
            while ($lotto = $stmtLotto->fetch(PDO::FETCH_ASSOC)) {
                $html .= '<tr>
                            <td style="text-align:center;">' . $rank++ . '</td>
                            <td>' . $lotto['lotto_name'] . '</td>
                            <td>' . $lotto['lotto_phone'] . '</td>
                            <td>' . $lotto['lotto_province'] . '</td>
                            <td style="text-align:center;">' . $lotto['lotto_number'] . '</td>
                            <td>' . $lotto['sale_name'] . '</td>
                          </tr>';
            }
            $html .= '</table>';
            return $html;
        }
        return '<div>ไม่มีผู้ถูกรางวัล</div>';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $period_no = $_POST['period_no'];
    $period_month = $_POST['period_month'];
    $period_year = $_POST['period_year'];
    $month_name = isset($month_arr[$period_month]) ? $month_arr[$period_month] : 'ไม่พบเดือน';
    $pdf = new CustomPDF();
    $pdf->createPDF($conn, $period_no, $period_month, $month_name, $period_year);
}
