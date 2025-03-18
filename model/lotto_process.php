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
            "lotto_file1" => $result['lotto_file1'],
            "lotto_file2" => $result['lotto_file2'],
            "lotto_file3" => $result['lotto_file3'],
            "lotto_file4" => $result['lotto_file4'],
            "lotto_file5" => $result['lotto_file5'],
            "lotto_file6" => $result['lotto_file6'],
            "lotto_file7" => $result['lotto_file7'],
            "lotto_file8" => $result['lotto_file8'],
            "remark" => $result['remark'],
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

    echo $record;
}

if ($_POST["action"] === 'SAVE_DATA') {
    $ins = 0;
    $upload_dir = "../uploads/";
    $lotto_name = $_POST["lotto_name"];
    $lotto_phone = str_replace("-", "", $_POST["lotto_phone"]);
    $lotto_province = $_POST["lotto_province"];
    $sale_name = $_POST["sale_name"];
    $remark = empty($_POST["remark"]) ? "-" : $_POST["remark"];
    $client_ip_address = "-";
    $lotto_number = sprintf("%03d", $_POST["lotto_number"]);

    function uploadFiles($input_name, $upload_dir) {
        if (!empty($_FILES[$input_name]['name'][0])) {
            $uploaded_files = [];
            foreach ($_FILES[$input_name]['name'] as $key => $name) {
                $file_extension = pathinfo($name, PATHINFO_EXTENSION);
                $file_name = uniqid("file_", true) . "." . $file_extension;
                $file_path = $upload_dir . $file_name;

                if (move_uploaded_file($_FILES[$input_name]["tmp_name"][$key], $file_path)) {
                    $uploaded_files[] = $file_name;
                } else {
                    return null;
                }
            }
            return implode(",", $uploaded_files);
        }
        return null;
    }

    $lotto_files_str = uploadFiles('lotto_file', $upload_dir);
    $lotto_files1_str = uploadFiles('lotto_file1', $upload_dir);
    $lotto_files2_str = uploadFiles('lotto_file2', $upload_dir);
    $lotto_files3_str = uploadFiles('lotto_file3', $upload_dir);
    $lotto_files4_str = uploadFiles('lotto_file4', $upload_dir);
    $lotto_files5_str = uploadFiles('lotto_file5', $upload_dir);
    $lotto_files6_str = uploadFiles('lotto_file6', $upload_dir);
    $lotto_files7_str = uploadFiles('lotto_file7', $upload_dir);
    $lotto_files8_str = uploadFiles('lotto_file8', $upload_dir);

    if (is_null($lotto_files_str) || is_null($lotto_files1_str) || is_null($lotto_files2_str)) {
        echo 0;
        exit();
    }

    // ตรวจสอบข้อมูลซ้ำ
    $sql_get = "SELECT COUNT(*) as record_counts FROM " . $table_name . " WHERE lotto_name = :lotto_name OR lotto_phone = :lotto_phone";
    $statement = $conn->prepare($sql_get);
    $statement->bindParam(':lotto_name', $lotto_name, PDO::PARAM_STR);
    $statement->bindParam(':lotto_phone', $lotto_phone, PDO::PARAM_STR);
    $statement->execute();
    $record = $statement->fetch(PDO::FETCH_ASSOC)['record_counts'];

    if ($record <= 0) {
        try {
            $sql = "INSERT INTO ims_lotto (lotto_name, lotto_phone, lotto_province, lotto_number, sale_name, client_ip_address, 
                    lotto_file, lotto_file1, lotto_file2, lotto_file3, lotto_file4, lotto_file5, lotto_file6, lotto_file7, lotto_file8, remark)
                    VALUES (:lotto_name, :lotto_phone, :lotto_province, :lotto_number, :sale_name, :client_ip_address, 
                    :lotto_file, :lotto_file1, :lotto_file2, :lotto_file3, :lotto_file4, :lotto_file5, :lotto_file6, :lotto_file7, :lotto_file8, :remark)";

            $query = $conn->prepare($sql);
            $query->bindParam(':lotto_name', $lotto_name, PDO::PARAM_STR);
            $query->bindParam(':lotto_phone', $lotto_phone, PDO::PARAM_STR);
            $query->bindParam(':lotto_province', $lotto_province, PDO::PARAM_STR);
            $query->bindParam(':lotto_number', $lotto_number, PDO::PARAM_STR);
            $query->bindParam(':sale_name', $sale_name, PDO::PARAM_STR);
            $query->bindParam(':client_ip_address', $client_ip_address, PDO::PARAM_STR);
            $query->bindParam(':lotto_file', $lotto_files_str, PDO::PARAM_STR);
            $query->bindParam(':lotto_file1', $lotto_files1_str, PDO::PARAM_STR);
            $query->bindParam(':lotto_file2', $lotto_files2_str, PDO::PARAM_STR);
            $query->bindParam(':lotto_file3', $lotto_files3_str, PDO::PARAM_STR);
            $query->bindParam(':lotto_file4', $lotto_files4_str, PDO::PARAM_STR);
            $query->bindParam(':lotto_file5', $lotto_files5_str, PDO::PARAM_STR);
            $query->bindParam(':lotto_file6', $lotto_files6_str, PDO::PARAM_STR);
            $query->bindParam(':lotto_file7', $lotto_files7_str, PDO::PARAM_STR);
            $query->bindParam(':lotto_file8', $lotto_files8_str, PDO::PARAM_STR);
            $query->bindParam(':remark', $remark, PDO::PARAM_STR);

            if ($query->execute()) {
                echo $conn->lastInsertId();
                exit();
            }
        } catch (Exception $e) {
            echo 0;
            exit();
        }
    }

    echo 0;
}


if ($_POST["action"] === 'UPDATE') {
    $id = $_POST['id'];
    $lotto_name = $_POST["lotto_name"];
    $lotto_phone = str_replace("-", "", $_POST["lotto_phone"]);
    $lotto_province = $_POST["lotto_province"];
    $sale_name = $_POST["sale_name"];
    $lotto_number = sprintf("%03d", $_POST["lotto_number"]);
    $approve_status = $_POST["approve_status"];
    $remark = empty($_POST["remark"]) ? "-" : $_POST["remark"];

    $upload_dir = "../uploads/";
    $file_fields = ["lotto_file", "lotto_file1", "lotto_file2", "lotto_file3", "lotto_file4", "lotto_file5", "lotto_file6", "lotto_file7", "lotto_file8"];
    $uploaded_files = [];

    function uploadFiles($field_name, $upload_dir) {
        if (!empty($_FILES[$field_name]['name'][0])) {
            $files = [];
            foreach ($_FILES[$field_name]['name'] as $index => $name) {
                $file_extension = pathinfo($name, PATHINFO_EXTENSION);
                $file_name = uniqid("file_") . "." . $file_extension;
                $file_path = $upload_dir . $file_name;
                if (move_uploaded_file($_FILES[$field_name]['tmp_name'][$index], $file_path)) {
                    $files[] = $file_name;
                } else {
                    echo "UPLOAD_FAILED";
                    exit();
                }
            }
            return implode(",", $files);
        }
        return null;
    }

    foreach ($file_fields as $field) {
        $uploaded_files[$field] = uploadFiles($field, $upload_dir);
    }

    $sql = "UPDATE ims_lotto SET 
            lotto_name = :lotto_name, 
            lotto_phone = :lotto_phone, 
            lotto_province = :lotto_province,
            lotto_number = :lotto_number,
            sale_name = :sale_name,  
            approve_status = :approve_status,
            remark = :remark";

    foreach ($uploaded_files as $key => $value) {
        if ($value !== null) {
            $sql .= ", $key = :$key";
        }
    }

    $sql .= " WHERE id = :id";
    $query = $conn->prepare($sql);
    $query->bindParam(':lotto_name', $lotto_name, PDO::PARAM_STR);
    $query->bindParam(':lotto_phone', $lotto_phone, PDO::PARAM_STR);
    $query->bindParam(':lotto_province', $lotto_province, PDO::PARAM_STR);
    $query->bindParam(':lotto_number', $lotto_number, PDO::PARAM_STR);
    $query->bindParam(':sale_name', $sale_name, PDO::PARAM_STR);
    $query->bindParam(':approve_status', $approve_status, PDO::PARAM_STR);
    $query->bindParam(':remark', $remark, PDO::PARAM_STR);

    foreach ($uploaded_files as $key => $value) {
        if ($value !== null) {
            $query->bindParam(":" . $key, $uploaded_files[$key], PDO::PARAM_STR);
        }
    }

    $query->bindParam(':id', $id, PDO::PARAM_INT);

    echo $query->execute() ? $id : 0;
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
                "remark" => $rows['remark'],
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

