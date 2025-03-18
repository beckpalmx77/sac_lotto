<?php
include('config/connect_lotto_db.php');

// กรองค่า id ให้เป็นตัวเลข
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

$stmt = $conn->prepare("SELECT * FROM ims_lotto WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);

// ฟังก์ชันจัดการรูปภาพ
function getImages($data, $key)
{
    return !empty($data[$key]) ? array_filter(explode(',', $data[$key])) : [];
}

// โหลดรูปภาพ
$images = [
    'lotto_file' => getImages($data, 'lotto_file'),
    'lotto_file1' => getImages($data, 'lotto_file1'),
    'lotto_file3' => getImages($data, 'lotto_file3'),
    'lotto_file4' => getImages($data, 'lotto_file4'),
    'lotto_file5' => getImages($data, 'lotto_file5'),
    'lotto_file2' => getImages($data, 'lotto_file2') // เลขหลังป้าย
];

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สงวนออโต้คาร์ | ผลการลงทะเบียน</title>
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
<div class="container py-4">
    <div class="card shadow-lg">
        <div class="card-body">
            <h3 class="card-title text-center mb-4">🎯 ผลการลงทะเบียน</h3>
            <img src="img/logo/<?= ($data['approve_status'] == 'Y') ? 'approve.png' : 'none-approve.png' ?>"
                 alt="Approval Status">

            <div class="mb-3"><strong>🏪 ชื่อร้านค้า:</strong> <?= htmlspecialchars($data['lotto_name']) ?></div>
            <div class="mb-3"><strong>📞 โทรศัพท์:</strong> <?= htmlspecialchars($data['lotto_phone']) ?></div>
            <div class="mb-3"><strong>🗺️ จังหวัด:</strong> <?= htmlspecialchars($data['lotto_province']) ?></div>
            <div class="mb-3"><strong>🧑‍💼 ชื่อ Sale:</strong> <?= htmlspecialchars($data['sale_name']) ?></div>
            <div class="mb-4"><strong>🎟️ หมายเลข:</strong> <?= htmlspecialchars($data['lotto_number']) ?></div>
            <div class="mb-4"><strong>📝 หมายเหตุ:</strong> <?= htmlspecialchars($data['remark']) ?></div>

            <?php
            // ฟังก์ชันแสดงรูปภาพ
            function renderImages($title, $images)
            {
                if (!empty($images)) {
                    echo "<h5 class='text-left mb-3'>$title</h5><div class='row text-left'>";
                    foreach ($images as $image) {
                        echo "<div class='col-md-4 mb-3'>
                                <div class='card'>
                                    <a href='uploads/" . htmlspecialchars(trim($image)) . "' target='_blank'>
                                        <img src='uploads/" . htmlspecialchars(trim($image)) . "' class='card-img-top img-fluid' alt='รูปภาพ'>
                                    </a>
                                </div>
                              </div>";
                    }
                    echo "</div>";
                }
            }

            // แสดงรูปภาพที่บันทึก
            renderImages('🖼️ รูปภาพป้ายไวนิลที่บันทึก (Click ที่รูปเพื่อขยาย)', $images['lotto_file']);
            renderImages('🖼️ รูปภาพป้ายไวนิลที่บันทึก', $images['lotto_file1']);
            renderImages('🖼️ รูปภาพป้ายไวนิลที่บันทึก', $images['lotto_file3']);
            renderImages('🖼️ รูปภาพป้ายไวนิลที่บันทึก', $images['lotto_file4']);
            renderImages('🖼️ รูปภาพป้ายไวนิลที่บันทึก', $images['lotto_file5']);
            renderImages('🖼️ รูปภาพเลขหลังป้ายไวนิลที่บันทึก (Click ที่รูปเพื่อขยาย)', $images['lotto_file2']);
            ?>

            <div class="col-md-12">
                <button type="button" id="closeBtn" class="form-control btn btn-danger">
                    <i class="fa fa-reply" aria-hidden="true"></i> ปิด
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#closeBtn").click(function () {
            window.close();
        });
    });
</script>

</body>
</html>
