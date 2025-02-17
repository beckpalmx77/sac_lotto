<?php
include('includes/Header.php');
require_once 'config/connect_lotto_db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- datatable -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" charset="utf8"
            src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
    <title>SAC LOTTO LIST</title>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12"><br>
            <div><img src="img/logo/logo text-01.png" width="200" height="79"/></div>
            <h6 class="text-primary"><b>SAC LOTTO LIST</b></h6>
            <button type="button" id="backBtn" class="btn btn-danger mb-3">กลับหน้าแรก</button>

            <table id="DataTable" class="table table-striped table-hover table-bordered">
                <thead>
                <tr>
                    <th>ลำดับ</th>
                    <th>ชื่อร้าน</th>
                    <th>โทรศัพท์</th>
                    <th>จังหวัด</th>
                    <th>หมายเลข</th>
                    <th>Sale</th>
                    <th>อนุมัติ</th>
                    <th>วันที่บันทึก</th>
                    <th>รูปป้ายไวนิล</th>
                    <th>รูปเลขหลังป้าย</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $stmt = $conn->prepare("SELECT * FROM ims_lotto ORDER BY id DESC");
                $stmt->execute();
                $result = $stmt->fetchAll();
                $line_no = 0;
                foreach ($result as $rows) {
                    $line_no++;
                    ?>
                    <tr>
                        <td><?= $line_no; ?></td>
                        <td><?= htmlspecialchars($rows['lotto_name']); ?></td>
                        <td><?= htmlspecialchars($rows['lotto_phone']); ?></td>
                        <td><?= htmlspecialchars($rows['lotto_province']); ?></td>
                        <td><?= htmlspecialchars($rows['lotto_number']); ?></td>
                        <td><?= htmlspecialchars($rows['sale_name']); ?></td>
                        <td class="text-center text-<?= $rows['approve_status'] == 'Y' ? 'success' : 'secondary'; ?>">
                            <?= $rows['approve_status'] == 'Y' ? 'อนุมัติ' : 'ไม่อนุมัติ'; ?>
                        </td>
                        <td><?= htmlspecialchars($rows['create_date']); ?></td>
                        <td>
                            <?php
                            if (!empty($rows['lotto_file'])) {
                                foreach (explode(",", $rows['lotto_file']) as $index => $file) {
                                    echo "<a href='uploads/" . htmlspecialchars($file) . "' target='_blank'>รูปที่ " . ($index + 1) . "</a><br>";
                                }
                            } else {
                                echo "ไม่มีรูป";
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if (!empty($rows['lotto_file2'])) {
                                foreach (explode(",", $rows['lotto_file2']) as $index => $file) {
                                    echo "<a href='uploads/" . htmlspecialchars($file) . "' target='_blank'>รูปที่ " . ($index + 1) . "</a><br>";
                                }
                            } else {
                                echo "ไม่มีรูป";
                            }
                            ?>
                        </td>
                        <td>
                            <button class="btn btn-outline-info" onclick="openUpdateModal(<?= $rows['id']; ?>)">Update
                            </button>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="updateModal" name="updateModal" tabindex="-1" aria-labelledby="updateModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateModalLabel">Update Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateForm" enctype="multipart/form-data">
                    <input type="hidden" id="id" name="id">
                    <div class="mb-3">
                        <label>ชื่อร้าน</label>
                        <input type="text" class="form-control" id="lotto_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label>โทรศัพท์</label>
                        <input type="text" class="form-control" id="lotto_phone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label>จังหวัด</label>
                        <input type="text" class="form-control" id="lotto_province" name="province" required>
                    </div>
                    <div class="mb-3">
                        <label>หมายเลข</label>
                        <input type="text" class="form-control" id="lotto_number" name="number" required>
                    </div>
                    <div class="mb-3">
                        <label>ชื่อ Sale</label>
                        <input type="text" class="form-control" id="sale_name" name="sale" required>
                    </div>
                    <div class="mb-3">
                        <label>สถานะอนุมัติ</label>
                        <select class="form-select" id="approve_status" name="status">
                            <option value="N">ไม่อนุมัติ</option>
                            <option value="Y">อนุมัติ</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>อัพโหลดรูปป้ายไวนิล</label>
                        <input type="file" name="lotto_file[]" id="lotto_file_input" multiple>
                    </div>
                    <div id="lotto_file_images"></div> <!-- แสดงรูปจาก lotto_file -->
                    <div class="mb-3">
                        <label>อัพโหลดรูปเลขหลังป้าย</label>
                        <input type="file" name="lotto_file2[]" id="lotto_file2_input" multiple>
                    </div>
                    <div id="lotto_file2_images"></div> <!-- แสดงรูปจาก lotto_file2 -->
                    <input type="hidden" id="action" name="action" value="">
                    <div class="md-3">
                        <div class="form-group">
                            <button type="button" name="saveBtn" id="saveBtn" tabindex="4"
                                    class="form-control btn btn-primary">
                                            <span>
                                                <i class="fa fa-save" aria-hidden="true"></i>
                                                บันทึก
                                            </span>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#DataTable').DataTable();
        $('#backBtn').click(function () {
            window.location.href = "sac_lotto";
        });
    });
</script>

<script>
    $(document).ready(function () {
        // เมื่อเลือกไฟล์จาก input lotto_file
        $('#lotto_file_input').on('change', function (e) {
            let files = e.target.files;
            let previewContainer = $('#lotto_file_images');
            previewContainer.html(""); // ลบพรีวิวเก่าก่อน

            // ลูปผ่านไฟล์ที่เลือกและแสดงพรีวิว
            for (let i = 0; i < files.length; i++) {
                let file = files[i];
                let reader = new FileReader();

                reader.onload = function (event) {
                    let imageUrl = event.target.result;
                    let imageName = file.name;
                    let previewHtml = `
                    <div class="file-preview">
                        <img src="${imageUrl}" alt="${imageName}" width="150" height="150">
                    </div>
                `;
                    previewContainer.append(previewHtml);
                };

                reader.readAsDataURL(file);
            }
        });

        // เมื่อเลือกไฟล์จาก input lotto_file2
        $('#lotto_file2_input').on('change', function (e) {
            let files = e.target.files;
            let previewContainer = $('#lotto_file2_images');
            previewContainer.html(""); // ลบพรีวิวเก่าก่อน

            // ลูปผ่านไฟล์ที่เลือกและแสดงพรีวิว
            for (let i = 0; i < files.length; i++) {
                let file = files[i];
                let reader = new FileReader();

                reader.onload = function (event) {
                    let imageUrl = event.target.result;
                    let imageName = file.name;
                    let previewHtml = `
                    <div class="file-preview">
                        <img src="${imageUrl}" alt="${imageName}" width="150" height="150">
                    </div>
                `;
                    previewContainer.append(previewHtml);
                };

                reader.readAsDataURL(file);
            }
        });
    });
</script>

<script>
    function openUpdateModal(id) {
        $.ajax({
            type: "POST",
            url: 'model/lotto_process.php',
            data: {action: "GET_DATA", id: id, table_name: "ims_lotto"},
            dataType: "json",
            success: function (data) {
                if (data.error) {
                    alert(data.error);
                    return;
                }

                // กรอกข้อมูลในฟอร์ม
                $('#id').val(data.id);
                $('#lotto_name').val(data.lotto_name);
                $('#lotto_phone').val(data.lotto_phone);
                $('#lotto_province').val(data.lotto_province);
                $('#lotto_number').val(data.lotto_number);
                $('#sale_name').val(data.sale_name);

                // แสดงรูปภาพ lotto_file
                displayImages('#lotto_file_images', data.lotto_file);
                displayImages('#lotto_file2_images', data.lotto_file2);

                $('#updateModal').modal('show');
            },
            error: function (xhr, status, error) {
                alert(`เกิดข้อผิดพลาด: ${status} - ${error}`);
            }
        });
    }

    // ฟังก์ชันแสดงรูปภาพ
    function displayImages(container, files) {
        $(container).empty();
        if (files) {
            const fileArray = files.split(',');
            fileArray.forEach(file => {
                const fileName = file.trim();
                if (fileName) {
                    $(container).append(`
                    <div class="file-preview">
                        <img src="uploads/${fileName}" class="img-thumbnail mb-2" style="max-width:100px;">
                        <button type="button" class="btn btn-danger btn-sm remove-file" data-file="${fileName}">ลบ</button>
                    </div>
                `);
                }
            });
        }
    }

</script>

<script>
    $('#saveBtn').click(function () {
        let action = "UPDATE";
        let table_name = "ims_lotto";
        let id = $('#id').val();
        let lotto_name = $('#lotto_name').val();
        let lotto_phone = $('#lotto_phone').val();
        let lotto_province = $('#lotto_province').val();
        let lotto_number = $('#lotto_number').val();
        let sale_name = $('#sale_name').val();
        let files = $('#lotto_file_input')[0].files; // ดึงไฟล์จาก input ที่รองรับหลายไฟล์
        let files2 = $('#lotto_file2_input')[0].files; // ดึงไฟล์จาก input ที่รองรับหลายไฟล์

        // ตรวจสอบการกรอกข้อมูลทั้งหมด
        if (!lotto_name || !lotto_phone || !lotto_province || !sale_name || !lotto_number) {
            alertify.error("กรุณากรอกข้อมูลให้ครบถ้วน");
            return;
        }

        // ตรวจสอบไฟล์ที่เลือกในช่อง lotto_file และ lotto_file2
        if (files.length < 2) {
            alertify.error("กรุณาอัพโหลดรูปภาพ ป้ายไวนิล อย่างน้อย 2 รูป");
            return;
        }

        if (files2.length < 1) {
            alertify.error("กรุณาอัพโหลดรูปภาพ เลขหลังป้ายไวนิล อย่างน้อย 1 รูป");
            return;
        }

        // ตรวจสอบว่าไฟล์ที่เลือกเป็นไฟล์รูปภาพ
        for (let i = 0; i < files.length; i++) {
            if (!files[i].type.startsWith('image/')) {
                alertify.error("ไฟล์ที่อัพโหลดสำหรับ 'lotto_file' ต้องเป็นไฟล์ภาพ");
                return;
            }
        }

        for (let i = 0; i < files2.length; i++) {
            if (!files2[i].type.startsWith('image/')) {
                alertify.error("ไฟล์ที่อัพโหลดสำหรับ 'lotto_file2' ต้องเป็นไฟล์ภาพ");
                return;
            }
        }

        // เตรียมข้อมูลที่ต้องการส่ง
        let formData = new FormData();
        formData.append("action", action);
        formData.append("id", id);
        formData.append("table_name", table_name);
        formData.append("lotto_name", lotto_name);
        formData.append("lotto_phone", lotto_phone);
        formData.append("lotto_province", lotto_province);
        formData.append("lotto_number", lotto_number);
        formData.append("sale_name", sale_name);

        // แนบไฟล์ใหม่
        for (let i = 0; i < files.length; i++) {
            formData.append("lotto_file[]", files[i]);
        }
        for (let i = 0; i < files2.length; i++) {
            formData.append("lotto_file2[]", files2[i]);
        }

        // ส่งข้อมูลไปยัง server
        $.ajax({
            type: "POST",
            url: 'model/lotto_process.php',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response === "0") {
                    alertify.error("ไม่สามารถบันทึกข้อมูลได้ กรุณาตรวจสอบข้อมูล");
                } else {
                    alertify.success("บันทึกสำเร็จ");
                    $('#updateModal').modal('hide');
                    //$('#DataTable').DataTable().ajax.reload(); // รีเฟรชตารางเมื่อบันทึกสำเร็จ
                }
            },
            error: function (xhr, status, error) {
                let errorMessage = "เกิดข้อผิดพลาด : " + error;
                if (xhr.responseText) {
                    try {
                        let response = JSON.parse(xhr.responseText);
                        errorMessage += "\n" + JSON.stringify(response, null, 2);
                    } catch (e) {
                        errorMessage += "\n" + xhr.responseText;
                    }
                }
                alertify.error(errorMessage);
            }
        });
    });
</script>


</body>
</html>
