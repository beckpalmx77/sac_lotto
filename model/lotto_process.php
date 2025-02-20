<?php

include('../config/connect_lotto_db.php');

$table_name = $_POST["table_name"];

if ($_POST["action"] === 'GET_DATA') {
    $id = $_POST["id"];
    $sql_get = "SELECT * FROM ims_lotto WHERE id = :id";
    $statement = $conn->prepare($sql_get);
    $statement->bindParam(':id', $id, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $return_arr = array(
            "id" => $result['id'],
            "lotto_name" => $result['lotto_name'],
            "lotto_phone" => $result['lotto_phone'],
            "lotto_province" => $result['lotto_province'],
            "lotto_number" => $result['lotto_number'],
            "sale_name" => $result['sale_name'],
            "lotto_file" => $result['lotto_file'],
            "lotto_file2" => $result['lotto_file2'],
            "approve_status" => $result['approve_status']
        );
        echo json_encode($return_arr);
    } else {
        echo json_encode(["error" => "ไม่พบข้อมูล"]);
    }
}


if ($_POST["action"] === 'CHECK_NUMBER_DATA') {
    $cond = $_POST["cond"];
    $return_arr = array();
    $sql_get = "SELECT count(*) as record_counts  FROM " . $table_name . " " . $cond;

    $statement = $conn->query($sql_get);
    $results = $statement->fetchAll(PDO::FETCH_ASSOC);
    foreach ($results as $result) {
        $record = $result['record_counts'];
    }

    /*
        $my_file = fopen("sql_getdata.txt", "w") or die("Unable to open file!");
        fwrite($my_file, " sql_get = " . $sql_get . " Count = " . $record);
        fclose($my_file);
    */

    echo $record;

}

if ($_POST["action"] === 'SAVE_DATA') {
    $ins = 0; // กำหนดค่าเริ่มต้นเป็น 0
    $sql = "";
    $lotto_name = $_POST["lotto_name"];
    $lotto_phone = str_replace("-", "", $_POST["lotto_phone"]);
    $lotto_province = $_POST["lotto_province"];
    $sale_name = $_POST["sale_name"];

    // ตรวจสอบว่ามีไฟล์ถูกอัปโหลดหรือไม่
    if (!empty($_FILES['lotto_file']['name'][0])) {
        $upload_dir = "../uploads/";
        $lotto_files = [];

        for ($i = 0; $i < count($_FILES['lotto_file']['name']); $i++) {
            $file_extension = pathinfo($_FILES["lotto_file"]["name"][$i], PATHINFO_EXTENSION);
            $unique_id = uniqid("file_", true);
            $file_name = $unique_id . "." . $file_extension;
            $file_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES["lotto_file"]["tmp_name"][$i], $file_path)) {
                $lotto_files[] = $file_name;
            } else {
                echo 0; // ถ้าอัปโหลดไม่สำเร็จให้ return 0
                exit();
            }
        }

        $lotto_files_str = implode(",", $lotto_files);
    } else {
        $lotto_files_str = NULL;
    }

    if (!empty($_FILES['lotto_file2']['name'][0])) {
        $lotto_files2 = [];

        for ($i = 0; $i < count($_FILES['lotto_file2']['name']); $i++) {
            $file_extension = pathinfo($_FILES["lotto_file2"]["name"][$i], PATHINFO_EXTENSION);
            $unique_id = uniqid("file_", true);
            $file_name = $unique_id . "." . $file_extension;
            $file_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES["lotto_file2"]["tmp_name"][$i], $file_path)) {
                $lotto_files2[] = $file_name;
            } else {
                echo 0;
                exit();
            }
        }

        $lotto_files2_str = implode(",", $lotto_files2);
    } else {
        $lotto_files2_str = NULL;
    }

    // รับ IP ของผู้ใช้
    $client_ip_address = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];

    // รับ lotto_number และปรับรูปแบบ
    $lotto_number = sprintf("%03d", $_POST["lotto_number"]);

    // ตรวจสอบข้อมูลซ้ำ
    $cond = " WHERE lotto_name = :lotto_name OR lotto_phone = :lotto_phone OR lotto_number = :lotto_number";
    $sql_get = "SELECT COUNT(*) as record_counts FROM " . $table_name . $cond;

    $statement = $conn->prepare($sql_get);
    $statement->bindParam(':lotto_name', $lotto_name, PDO::PARAM_STR);
    $statement->bindParam(':lotto_phone', $lotto_phone, PDO::PARAM_STR);
    $statement->bindParam(':lotto_number', $lotto_number, PDO::PARAM_STR);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $record = $result['record_counts'];

    if ($record <= 0) {
        // Insert ข้อมูลใหม่
        try {
            $sql = "INSERT INTO ims_lotto(lotto_name, lotto_phone, lotto_province, lotto_number, sale_name, client_ip_address, lotto_file, lotto_file2)
                    VALUES (:lotto_name, :lotto_phone, :lotto_province, :lotto_number, :sale_name, :client_ip_address, :lotto_file, :lotto_file2)";
            $query = $conn->prepare($sql);
            $query->bindParam(':lotto_name', $lotto_name, PDO::PARAM_STR);
            $query->bindParam(':lotto_phone', $lotto_phone, PDO::PARAM_STR);
            $query->bindParam(':lotto_province', $lotto_province, PDO::PARAM_STR);
            $query->bindParam(':lotto_number', $lotto_number, PDO::PARAM_STR);
            $query->bindParam(':sale_name', $sale_name, PDO::PARAM_STR);
            $query->bindParam(':client_ip_address', $client_ip_address, PDO::PARAM_STR);
            $query->bindParam(':lotto_file', $lotto_files_str, PDO::PARAM_STR);
            $query->bindParam(':lotto_file2', $lotto_files2_str, PDO::PARAM_STR);

            if ($query->execute()) {
                $lastInsertId = $conn->lastInsertId();
                $ins = 1; // INSERT สำเร็จ
            }
        } catch (Exception $e) {
            echo 0;
            exit();
        }
    }

    if ($record <= 0 && $ins == 1) {
        echo $lastInsertId; // สำเร็จ
    } else {
        echo 0; // ล้มเหลว
    }
}


if ($_POST["action"] === 'UPDATE') {

    $id = $_POST['id'];
    $lotto_name = $_POST["lotto_name"];
    $lotto_phone = str_replace("-", "", $_POST["lotto_phone"]);
    $lotto_province = $_POST["lotto_province"];
    $sale_name = $_POST["sale_name"];
    $lotto_number = sprintf("%03d", $_POST["lotto_number"]);
    $approve_status = $_POST["approve_status"];

/*
    $txt = $id . " | " .  $lotto_province . " | " .  $approve_status . " | " . $lotto_name;
    ;
    $my_file = fopen("lotto_data.txt", "w") or die("Unable to open file!");
    fwrite($my_file, " txt = " . $txt);
    fclose($my_file);
*/

    $upload_dir = "../uploads/";
    $lotto_files_str = null;
    $lotto_files2_str = null;

    // อัปโหลดไฟล์ lotto_file ถ้ามี
    if (!empty($_FILES['lotto_file']['name'][0])) {
        $lotto_files = [];
        foreach ($_FILES['lotto_file']['name'] as $index => $name) {
            $file_extension = pathinfo($name, PATHINFO_EXTENSION);
            $file_name = uniqid("file_") . "." . $file_extension;
            $file_path = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['lotto_file']['tmp_name'][$index], $file_path)) {
                $lotto_files[] = $file_name;
            } else {
                echo "UPLOAD_FAILED";
                exit();
            }
        }
        $lotto_files_str = implode(",", $lotto_files);
    }

    // อัปโหลดไฟล์ lotto_file2 ถ้ามี
    if (!empty($_FILES['lotto_file2']['name'][0])) {
        $lotto_files2 = [];
        foreach ($_FILES['lotto_file2']['name'] as $index => $name) {
            $file_extension = pathinfo($name, PATHINFO_EXTENSION);
            $file_name = uniqid("file_") . "." . $file_extension;
            $file_path = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['lotto_file2']['tmp_name'][$index], $file_path)) {
                $lotto_files2[] = $file_name;
            } else {
                echo "UPLOAD_FAILED";
                exit();
            }
        }
        $lotto_files2_str = implode(",", $lotto_files2);
    }

    // สร้างคำสั่ง SQL แบบไดนามิก
    $sql = "UPDATE ims_lotto SET 
            lotto_name = :lotto_name, 
            lotto_phone = :lotto_phone, 
            lotto_province = :lotto_province,
            lotto_number = :lotto_number,
            sale_name = :sale_name,  
            approve_status = :approve_status";

    // เพิ่มการอัปเดตไฟล์ถ้ามี
    if ($lotto_files_str !== null) {
        $sql .= ", lotto_file = :lotto_file";
    }
    if ($lotto_files2_str !== null) {
        $sql .= ", lotto_file2 = :lotto_file2";
    }

    $sql .= " WHERE id = :id";

    $query = $conn->prepare($sql);
    $query->bindParam(':lotto_name', $lotto_name, PDO::PARAM_STR);
    $query->bindParam(':lotto_phone', $lotto_phone, PDO::PARAM_STR);
    $query->bindParam(':lotto_province', $lotto_province, PDO::PARAM_STR);
    $query->bindParam(':lotto_number', $lotto_number, PDO::PARAM_STR);
    $query->bindParam(':sale_name', $sale_name, PDO::PARAM_STR);
    $query->bindParam(':approve_status', $approve_status, PDO::PARAM_STR);

    if ($lotto_files_str !== null) {
        $query->bindParam(':lotto_file', $lotto_files_str, PDO::PARAM_STR);
    }
    if ($lotto_files2_str !== null) {
        $query->bindParam(':lotto_file2', $lotto_files2_str, PDO::PARAM_STR);
    }
    $query->bindParam(':id', $id, PDO::PARAM_INT);

    if ($query->execute()) {
        echo $id;
    } else {
        echo 0;
    }
}



if ($_POST["action"] === 'DELETE') {

    if ($_POST["lotto_number"] !== '') {
        $lotto_number = $_POST["lotto_number"];
        $sql_find = "SELECT * FROM ims_lotto WHERE lotto_number = '" . $lotto_number . "'";

        /*
                $my_file = fopen("sql_find.txt", "w") or die("Unable to open file!");
                fwrite($my_file, " sql_find = " . $sql_find);
                fclose($my_file);
        */

        $nRows = $conn->query($sql_find)->fetchColumn();
        if ($nRows > 0) {

            $sql_del = "DELETE FROM ims_lotto WHERE lotto_number = " . $lotto_number;
            $query = $conn->prepare($sql_del);
            $query->execute();

            $sql_up = "UPDATE ims_number_reserve SET reserve_status = 'N' WHERE lotto_number = " . $lotto_number;
            $query = $conn->prepare($sql_up);
            $query->execute();
            /*
                        $my_file = fopen("sql_del.txt", "w") or die("Unable to open file!");
                        fwrite($my_file, " sql_del = " . $sql_del . " | " . $sql_up);
                        fclose($my_file);
            */

            $del = 1;
        } else {
            $del = 3;
        }

    }

    /*
        $my_file = fopen("sql_search.txt", "w") or die("Unable to open file!");
        fwrite($my_file, " sql_search = " . $del);
        fclose($my_file);
    */

}

if ($_POST["action"] === 'GET_SHOW_LOTTO') {

    ## Read value
    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $searchValue = $_POST['search']['value']; // Search value

    $searchArray = array();

## Search
    $searchQuery = " ";

    if ($searchValue != '') {
        $searchQuery = " AND (lotto_name LIKE :lotto_name or
        lotto_phone LIKE :lotto_phone) ";
        $searchArray = array(
            'lotto_name' => "%$searchValue%",
            'lotto_phone' => "%$searchValue%",
        );
    }

## Total number of records without filtering
    $stmt = $conn->prepare("SELECT COUNT(*) AS allcount FROM ims_lotto where 1 = 1 ");
    $stmt->execute();
    $records = $stmt->fetch();
    $totalRecords = $records['allcount'];

## Total number of records with filtering
    $stmt = $conn->prepare("SELECT COUNT(*) AS allcount FROM ims_lotto WHERE 1 = 1 " . $searchQuery);
    $stmt->execute($searchArray);
    $records = $stmt->fetch();
    $totalRecordwithFilter = $records['allcount'];

## Fetch records

    $columnName = " id,lotto_number,lotto_name ";
    /*
        $sql_getdata = "SELECT * FROM ims_lotto WHERE 1 = 1 " . $searchQuery
            . " ORDER BY " . $columnName . " " . $columnSortOrder . " LIMIT :limit,:offset";
    */

    $sql_getdata = "SELECT * FROM ims_lotto WHERE 1 = 1 " . $searchQuery
        . " ORDER BY " . $columnName . " " . $columnSortOrder . " LIMIT " . $row . "," . $rowperpage;


    /*
    $my_file = fopen("sql_getdata.txt", "w") or die("Unable to open file!");
    fwrite($my_file, " sql_getdata = " . $sql_getdata);
    fclose($my_file);
*/

    $stmt = $conn->prepare($sql_getdata);

// Bind values
    foreach ($searchArray as $key => $search) {
        $stmt->bindValue(':' . $key, $search, PDO::PARAM_STR);
    }

    /*
        $stmt->bindValue(':limit', (int)$row, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$rowperpage, PDO::PARAM_INT);
    */

    $stmt->execute();
    $empRecords = $stmt->fetchAll();
    $data = array();

    foreach ($empRecords as $rows) {

        if ($_POST['sub_action'] === "GET_MASTER") {
            $data[] = array(
                "id" => $rows['id'],
                "lotto_name" => $rows['lotto_name'],
                "lotto_phone" => $rows['lotto_phone'],
                "lotto_province" => $rows['lotto_province'],
                "lotto_number" => $rows['lotto_number'],
                "approve_status" => $rows['approve_status']

            );
        }

    }

    //file_put_contents("data_get.txt", print_r($data, true), FILE_APPEND);

    //$my_file = fopen("getproduct_data.txt", "w") or die("Unable to open file!");
    //fwrite($my_file, " getproductdata = " . $draw . " | " . $totalRecords . " | " . $totalRecordwithFilter . " | " . $data);
    //fclose($my_file);

## Response Return Value
    $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordwithFilter,
        "aaData" => $data
    );

    //file_put_contents("data_res.txt", print_r($response, true), FILE_APPEND);

    echo json_encode($response);


}

