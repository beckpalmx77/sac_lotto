<?php
include('config/connect_lotto_db.php');
include('includes/Header.php');
?>

<!DOCTYPE html>
<html lang="th">
<body class="bg-gradient-login" id="page-top">

<form method="post" id="lotto_form" name="lotto_form" enctype="multipart/form-data">
    <div class="container-login">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-6 col-md-9">
                <div class="card shadow-sm my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="product-form">
                                    <div class="text-center">
                                        <div><img src="img/logo/logo text-01.png" width="200" height="79"/></div>
                                        <h4 style="color: blue"><b>ลงทะเบียนร้านค้าและเลือกตัวเลข 3 หลัก</b></h4>
                                        <input type="hidden" class="form-control" id="action" name="action"
                                               value="SAVE_DATA">
                                        <input type="hidden" class="form-control" id="table_name" name="table_name"
                                               value="ims_lotto">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="customer_select"><b style="color: blue;">เลือกรายชื่อลูกค้า</b> <b
                                                    style="color: orangered;">ถ้าค้นหาไม่พบให้พิมพ์ชื่อร้านค้าในช่อง</b><b
                                                    style="color: green;"> ชื่อร้านค้า</b></label>
                                        <select id="customer_select" class="form-control" style="width: 100%;">
                                            <option value="">-- ค้นหารายชื่อลูกค้า --</option>
                                            <?php
                                            $sql = "SELECT * FROM ims_customer_master";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->execute();
                                            $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($customers as $customer) {
                                                echo "<option value='{$customer['id']}' data-name='{$customer['customer_name']}' data-phone='{$customer['phone']}' data-province='{$customer['province']}' data-sale='{$customer['sale_name']}'>{$customer['customer_name']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="lotto_name" class="control-label"><b style="color: green;">
                                                        ชื่อร้านค้า</b></label>
                                                <input type="text" class="form-control" id="lotto_name"
                                                       name="lotto_name"
                                                       required="true"
                                                       value=""
                                                       placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="lotto_phone" class="control-label">หมายเลขโทรศัพท์</label>
                                                <input type="number" class="form-control" id="lotto_phone"
                                                       name="lotto_phone"
                                                       required="true"
                                                       value=""
                                                       placeholder="">
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label" for="lotto_province">จังหวัด</label>
                                                <select id="lotto_province" name="lotto_province" required="true"
                                                        class="form-control" data-live-search="true"
                                                        title="Please select">
                                                    <option value="" selected></option>
                                                    <?php
                                                    $sql1 = "SELECT * FROM ims_provinces WHERE 1 =1";
                                                    $query1 = $conn->prepare($sql1);
                                                    $query1->execute();
                                                    $results1 = $query1->fetchAll(PDO::FETCH_OBJ);
                                                    if ($query1->rowCount() > 0) {
                                                        foreach ($results1 as $result1) { ?>
                                                            <option value="<?php echo htmlentities($result1->province_name); ?>"><?php echo htmlentities($result1->province_name); ?></option>
                                                        <?php }
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label" for="sale_name">ชื่อ Sale</label>
                                                <select id="sale_name" name="sale_name" required="true"
                                                        class="form-control" data-live-search="true"
                                                        title="Please select">
                                                    <option value="" selected></option>
                                                    <?php
                                                    $sql2 = "SELECT * FROM ims_sale_team WHERE 1 =1 ORDER BY id ";
                                                    $query2 = $conn->prepare($sql2);
                                                    $query2->execute();
                                                    $results2 = $query2->fetchAll(PDO::FETCH_OBJ);
                                                    if ($query2->rowCount() > 0) {
                                                        foreach ($results2 as $result2) { ?>
                                                            <option value="<?php echo htmlentities($result2->sale_name); ?>"><?php echo htmlentities($result2->sale_name); ?></option>
                                                        <?php }
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="form-group">
                                            <label for="lotto_number" class="control-label">หมายเลขที่เลือก
                                                (000-999)</label>
                                            <input type="number" class="form-control" id="lotto_number"
                                                   name="lotto_number"
                                                   min="0" max="999" required="true"
                                                   value=""
                                                   placeholder="">
                                        </div>
                                    </div>

                                    <!-- อัปโหลดรูปภาพ -->
                                    <div class="form-group">
                                        <label for="lotto_file" class="control-label">อัปโหลดรูปภาพ
                                            (รูปป้ายไวนิล 2 รูปภาพ)</label>
                                        <input type="file" class="form-control" id="lotto_file" name="lotto_file[]"
                                               accept="image/*" multiple>
                                        <div class="preview mt-2" id="previewContainer">
                                            <!-- Preview รูปภาพจะแสดงที่นี่ -->
                                        </div>
                                    </div>

                                    <!-- อัปโหลดรูปภาพ -->
                                    <div class="form-group">
                                        <label for="lotto_file2" class="control-label">อัปโหลดรูปภาพ
                                            (รูปเลขหลังป้ายไวนิล 1 รูปภาพ)</label>
                                        <input type="file" class="form-control" id="lotto_file2" name="lotto_file2[]"
                                               accept="image/*" multiple>
                                        <div class="preview mt-2" id="previewContainer2">
                                            <!-- Preview รูปภาพจะแสดงที่นี่ -->
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="button" name="saveBtn" id="saveBtn" tabindex="4"
                                                class="form-control btn btn-primary">
                                            <span>
                                                <i class="fa fa-save" aria-hidden="true"></i>
                                                บันทึก
                                            </span>
                                    </div>
                                </div>
                                <!--div class="col-md-12">
                                    <div class="form-group">
                                        <button type="button" name="SearchBtn" id="SearchBtn" tabindex="4"
                                                class="form-control btn btn-info">
                                            <span>
                                                <i class="fa fa-search" aria-hidden="true"></i>
                                                ค้นหาข้อมูลการลงทะเบียน
                                            </span>
                                    </div>
                                </div-->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="button" name="backBtn" id="backBtn" tabindex="4"
                                                class="form-control btn btn-danger">
                                            <span>
                                                <i class="fa fa-reply" aria-hidden="true"></i>
                                                กลับหน้าแรก
                                            </span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</form>

<!-- Scroll to top -->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>


<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/myadmin.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
        crossorigin="anonymous"></script>

<script src="vendor/datatables/v11/bootbox.min.js"></script>
<script src="vendor/datatables/v11/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="vendor/datatables/v11/jquery.dataTables.min.css"/>
<link rel="stylesheet" href="vendor/datatables/v11/buttons.dataTables.min.css"/>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>

    .icon-input-btn {
        display: inline-block;
        position: relative;
    }

    .icon-input-btn input[type="submit"] {
        padding-left: 2em;
    }

    .icon-input-btn .fa {
        display: inline-block;
        position: absolute;
        left: 0.65em;
        top: 30%;
    }
</style>

<script>
    $(document).ready(function () {
        $(".icon-input-btn").each(function () {
            let btnFont = $(this).find(".btn").css("font-size");
            let btnColor = $(this).find(".btn").css("color");
            $(this).find(".fa").css({'font-size': btnFont, 'color': btnColor});
        });
    });
</script>

<script>
    $(document).ready(function () {
        $("#backBtn").click(function () {
            window.location.href = "sac_lotto";
        });
    });
</script>

<script>
    function pad(n, width, fill) {
        n = n + '';
        return n.length >= width ? n : new Array(width - n.length + 1).join(fill) + n;
    }
</script>


<script>
    $(document).ready(function () {

        $("#lotto_number").on("change blur", function () {

            let width = 3;
            let fil = 0;
            $('#lotto_number').val(pad($('#lotto_number').val(), width, fil));
            if ($('#lotto_number').val() >= 0 && $('#lotto_number').val() <= 999) {
                let action = "CHECK_NUMBER_DATA";
                let table_name = "ims_lotto";
                let cond = " WHERE lotto_number = " + $('#lotto_number').val();
                let formData = {action: action, table_name: table_name, cond: cond};
                $.ajax({
                    type: "POST",
                    url: 'model/lotto_process.php',
                    data: formData,
                    success: function (response) {
                        if (response > 0) {
                            //alertify.error("มีการจองหมายเลขนี้ในระบบแล้ว");
                            //$('#lotto_number').val("");
                        }
                    },
                    error: function (response) {
                        alertify.error("error : " + response);
                    }
                });

            } else {
                alertify.error("ป้อนเลข 000 - 999 เท่านั้น");
                $('#lotto_number').val('');
            }

        });

    });

</script>

<script>

    $('#lotto_phone').blur(function () {

        let action = "CHECK_NUMBER_DATA";
        let table_name = "ims_lotto";
        let cond = " WHERE lotto_phone = '" + $('#lotto_phone').val() + "'";
        let formData = {action: action, table_name: table_name, cond: cond};
        $.ajax({
            type: "POST",
            url: 'model/lotto_process.php',
            data: formData,
            success: function (response) {
                if (response > 0) {
                    alertify.error("มีการจองโดยหมายเลขโทรศัพท์นี้ในระบบแล้ว");
                    $('#lotto_phone').val("");
                }
            },
            error: function (response) {
                alertify.error("error : " + response);
            }
        });
    });

</script>

<script>

    $('#lotto_name').blur(function () {

        let action = "CHECK_NUMBER_DATA";
        let table_name = "ims_lotto";
        let cond = " WHERE lotto_name = '" + $('#lotto_name').val() + "'";
        let formData = {action: action, table_name: table_name, cond: cond};
        $.ajax({
            type: "POST",
            url: 'model/lotto_process.php',
            data: formData,
            success: function (response) {
                if (response > 0) {
                    alertify.error("มีการจองโดยชื่อร้านค้านี้ในระบบแล้ว");
                    $('#lotto_name').val("");
                }
            },
            error: function (response) {
                alertify.error("error : " + response);
            }
        });
    });

</script>

<script>

    $('#saveBtn').click(function () {
        let action = "SAVE_DATA";
        let table_name = "ims_lotto";
        let lotto_name = $('#lotto_name').val();
        let lotto_phone = $('#lotto_phone').val();
        let lotto_province = $('#lotto_province').val();
        let lotto_number = $('#lotto_number').val();
        let sale_name = $('#sale_name').val();
        let files = $('#lotto_file')[0].files;
        let files2 = $('#lotto_file2')[0].files;

        if (lotto_name === "" || lotto_phone === "" || lotto_province === "" || sale_name === "" || lotto_number === "") {
            alertify.error("กรุณากรอกข้อมูลให้ครบทุกช่อง");
            return;
        }

        if (files.length < 2) {
            alertify.error("กรุณาอัพโหลดรูปภาพ ป้ายไวนิล อย่างน้อย 2 รูป");
            return;
        }

        if (files2.length < 1) {
            alertify.error("กรุณาอัพโหลดรูปภาพ เลขหลังป้ายไวนิล อย่างน้อย 1 รูป");
            return;
        }

        let formData = new FormData();
        formData.append("action", action);
        formData.append("table_name", table_name);
        formData.append("lotto_name", lotto_name);
        formData.append("lotto_phone", lotto_phone);
        formData.append("lotto_province", lotto_province);
        formData.append("lotto_number", lotto_number);
        formData.append("sale_name", sale_name);

        for (let i = 0; i < files.length; i++) {
            formData.append("lotto_file[]", files[i]);
        }

        for (let i = 0; i < files2.length; i++) {
            formData.append("lotto_file2[]", files2[i]);
        }

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
                    $('#lotto_form')[0].reset(); // รีเซ็ตฟอร์ม
                    $('#previewContainer').empty();  // เคลียร์รูปที่แสดงใน div
                    $('#previewContainer2').empty(); // เคลียร์รูปที่แสดงใน div
                    $('#customer_select').val('').trigger('change');
                    // เปิดหน้าผลลัพธ์
                    //window.open(`show_data_register_result?id=${response}`, '_blank');
                    // ใช้ setTimeout เพื่อให้ reset ทำงานก่อน แล้วค่อยเปิดหน้าต่างใหม่
                    //setTimeout(function () {
                        //window.open(`show_data_register_result?id=${response}`, '_blank');
                    //}, 500);
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

<script>
    $(document).ready(function () {
        $('#lotto_file').change(function (event) {
            // Clear existing previews
            $('#previewContainer').empty();

            // Loop through selected files and create previews
            let files = event.target.files;
            for (let i = 0; i < files.length; i++) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    let imgElement = $('<img>')
                        .attr('src', e.target.result)
                        .css('max-width', '30%')
                        .css('margin-right', '10px')
                        .css('margin-bottom', '10px')
                        .show();
                    $('#previewContainer').append(imgElement);
                }
                reader.readAsDataURL(files[i]);
            }
        });
    });
</script>

<script>
    $(document).ready(function () {
        $('#lotto_file2').change(function (event) {
            // Clear existing previews
            $('#previewContainer2').empty();

            // Loop through selected files and create previews
            let files2 = event.target.files;
            for (let i = 0; i < files2.length; i++) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    let imgElement = $('<img>')
                        .attr('src', e.target.result)
                        .css('max-width', '30%')
                        .css('margin-right', '10px')
                        .css('margin-bottom', '10px')
                        .show();
                    $('#previewContainer2').append(imgElement);
                }
                reader.readAsDataURL(files2[i]);
            }
        });
    });
</script>

<script>
    $(document).ready(function () {
        $('#SearchBtn').click(function () {
            window.open('search_lotto_data', '_blank');
        });
    });
</script>

<script>
    $(document).ready(function () {
        // กำหนด Select2
        $('#customer_select').select2({
            placeholder: "พิมพ์เพื่อค้นหารายชื่อลูกค้า",
            allowClear: true
        });

        // เมื่อมีการเลือกจาก Select2
        $('#customer_select').on('change', function () {
            let selected = $(this).find(':selected');
            let name = selected.data('name');
            let phone = selected.data('phone');
            let province = selected.data('province');
            let sale = selected.data('sale');

            $('#lotto_name').val(name);
            $('#lotto_phone').val(phone);

            //alert(name + " | " + phone + " | " + " | " + province + " | " + sale);

            // ค้นหาและกำหนดค่า Province
            $('#lotto_province option').each(function () {
                let optionText = $(this).text();
                if (optionText.includes(province.replace('จ.', '').trim())) {
                    $('#lotto_province').val($(this).val()).change();
                }
            });

            // ค้นหาและกำหนดค่า Sale
            $('#sale_name option').each(function () {
                let optionText = $(this).text();
                if (optionText.includes(sale)) {
                    $('#sale_name').val($(this).val()).change();
                }
            });
        });
    });
</script>

</body>
</html>

