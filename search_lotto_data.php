<?php
include('includes/Header.php');
require_once 'config/connect_lotto_db.php';

// ตรวจสอบว่ามีการส่งข้อมูลเงื่อนไขมาหรือไม่
$condition = '';
$params = [];
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['lotto_name']) && empty($_POST['lotto_phone'])) {
        $message = 'กรุณาป้อนเงื่อนไข (ชื่อร้าน หรือ หมายเลขโทรศัพท์) ก่อน';
    } else {
        if (!empty($_POST['lotto_name'])) {
            $condition .= " AND lotto_name LIKE :lotto_name";
            $params[':lotto_name'] = "%" . $_POST['lotto_name'] . "%";
        }
        if (!empty($_POST['lotto_phone'])) {
            $condition .= " AND lotto_phone LIKE :lotto_phone";
            $params[':lotto_phone'] = "%" . $_POST['lotto_phone'] . "%";
        }
    }
}

// สร้างคำสั่ง SQL โดยเพิ่มเงื่อนไขกรองข้อมูล
$sql = "SELECT * FROM ims_lotto WHERE 1=1 $condition ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$result = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
    <title>SAC LOTTO LIST</title>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12"><br>
            <div><img src="img/logo/logo text-01.png" width="200" height="79"/></div>
            <h6 style="color: blue"><b>ค้นหาข้อมูลการลงทะเบียน SAC LOTTO</b></h6>

            <!-- ฟอร์มสำหรับกรองข้อมูล -->
            <form method="POST" action="">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <input type="text" name="lotto_name" class="form-control" placeholder="ชื่อร้าน">
                    </div>&nbsp;
                    <div class="col-md-3">
                        <input type="text" name="lotto_phone" class="form-control" placeholder="หมายเลขโทรศัพท์">
                    </div>&nbsp;
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">ค้นหา</button>
                        <button type="button" id="resetBtn" class="btn btn-secondary">ล้างค่า</button>
                        <button type="button" id="closeBtn" class="btn btn-danger">ปิด</button>
                    </div>
                </div>
            </form>

            <!-- แสดงข้อความแจ้งเตือนหากไม่มีการกรอกข้อมูล -->
            <?php if ($message): ?>
                <div class="alert alert-danger" role="alert" style="color: white; background-color: red;">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($_POST) && $message === ''): ?> <!-- แสดงข้อมูลเมื่อมีการกดปุ่มค้นหาและเงื่อนไขไม่ว่าง -->
                <table id="DataTable"
                       class="display table table-striped table-hover table-responsive table-bordered">
                    <thead>
                    <tr>
                        <th width="5%">ลำดับ</th>
                        <th width="25%">ชื่อร้าน</th>
                        <th width="10%">หมายเลขโทรศัพท์</th>
                        <th width="15%">จังหวัด</th>
                        <th width="15%">หมายเลขที่เลือก</th>
                        <th width="15%">ชื่อ Sale</th>
                        <th width="15%">การอนุมัติ</th>
                        <th width="15%">วันที่บันทึก</th>
                        <th width="15%">รูปภาพป้ายไวนิล</th>
                        <th width="15%">รูปภาพเลขหลังป้ายไวนิล</th>
                        <th width="15%">ดูข้อมูล</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $line_no = 0;
                    foreach ($result as $rows) {
                        $line_no++;
                        ?>
                        <tr>
                            <td><?= $line_no; ?></td>
                            <td><?= $rows['lotto_name']; ?></td>
                            <td><?= $rows['lotto_phone']; ?></td>
                            <td><?= $rows['lotto_province']; ?></td>
                            <td><?= $rows['lotto_number']; ?></td>
                            <td><?= $rows['sale_name']; ?></td>
                            <td style="color: <?= $rows['approve_status'] == 'Y' ? 'green' : ($rows['approve_status'] == 'N' ? 'gray' : 'black'); ?>; text-align: center;">
                                <?= $rows['approve_status'] == 'Y' ? 'อนุมัติ' : ($rows['approve_status'] == 'N' ? 'ยังไม่อนุมัติ' : ''); ?>
                            </td>
                            <td><?= $rows['create_date']; ?></td>
                            <td>
                                <?php
                                if (!empty($rows['lotto_file'])) {
                                    $files = explode(",", $rows['lotto_file']);
                                    $index = 1;
                                    foreach ($files as $file) {
                                        echo '<a href="uploads/' . $file . '" target="_blank">รูปที่ ' . $index . '</a><br>';
                                        $index++;
                                    }
                                } else {
                                    echo "ไม่มีรูปภาพ";
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if (!empty($rows['lotto_file2'])) {
                                    $files2 = explode(",", $rows['lotto_file2']);
                                    $index2 = 1;
                                    foreach ($files2 as $file2) {
                                        echo '<a href="uploads/' . $file2 . '" target="_blank">รูปที่ ' . $index2 . '</a><br>';
                                        $index2++;
                                    }
                                } else {
                                    echo "ไม่มีรูปภาพ";
                                }
                                ?>
                            </td>
                            <td style="text-align: center;">
                                <a href="javascript:void(0);" class="btn btn-primary btn-sm"
                                   onclick="openLottoData('<?= $rows['id']; ?>')">
                                    ดูข้อมูล
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#DataTable').DataTable();

        $("#resetBtn").click(function () {
            window.location.href = window.location.pathname;
        });
    });

    function openPopup(id) {
        $.ajax({
            url: 'model/get_approve_status.php',
            type: 'GET',
            data: {id: id},
            success: function (data) {
                $('#approve_status').val(data);
                $('#lotto_id').val(id);
                $('#approveModal').modal('show');
            }
        });
    }
</script>

<script>
    function openLottoData(id) {
        window.open(`show_data_lotto?id=${id}`);
    }
</script>

<script>
    $(document).ready(function () {
        $('#closeBtn').click(function (event) {
            event.preventDefault(); // ป้องกันการโหลดหน้าใหม่โดยตรงจาก `<a>`
            window.location.href = "sac_lotto";
        });
    });
</script>

</body>
</html>
