<?php
session_start();
error_reporting(0);

include('../config/connect_lotto_db.php');
include('../config/lang.php');
include('../util/record_util.php');


if ($_POST["action"] === 'GET_DATA') {

    $id = $_POST["id"];

    $return_arr = array();

    $sql_get = "SELECT * FROM ims_lotto WHERE id = " . $id;
    $statement = $conn->query($sql_get);
    $results = $statement->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $result) {
        $return_arr[] = array("id" => $result['id'],
            "lotto_name" => $result['lotto_name'],
            "lotto_phone" => $result['lotto_phone'],
            "status" => $result['status']);
    }

    echo json_encode($return_arr);

}

if ($_POST["action"] === 'UPDATE') {

    if ($_POST["lotto_phone"] != '') {

        $id = $_POST["id"];
        $lotto_name = $_POST["lotto_name"];
        $lotto_phone = $_POST["lotto_phone"];
        $status = $_POST["status"];
        $sql_find = "SELECT * FROM ims_lotto WHERE lotto_name = '" . $lotto_name . "'";
        $nRows = $conn->query($sql_find)->fetchColumn();
        if ($nRows > 0) {
            $sql_update = "UPDATE ims_lotto SET lotto_name=:lotto_name,lotto_phone=:lotto_phone,status=:status            
            WHERE id = :id";
            $query = $conn->prepare($sql_update);
            $query->bindParam(':lotto_name', $lotto_name, PDO::PARAM_STR);
            $query->bindParam(':lotto_phone', $lotto_phone, PDO::PARAM_STR);
            $query->bindParam(':status', $status, PDO::PARAM_STR);
            $query->bindParam(':id', $id, PDO::PARAM_STR);
            $query->execute();
            echo $save_success;
        }

    }
}



if ($_POST["action"] === 'GET_LOTTO_SHOW') {

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
        lotto_phone LIKE :lotto_phone or sale_name LIKE :sale_name) ";
        $searchArray = array(
            'lotto_name' => "%$searchValue%",
            'lotto_phone' => "%$searchValue%",
            'sale_name' => "%$searchValue%",
        );
    }

## Total number of records without filtering
    $stmt = $conn->prepare("SELECT COUNT(*) AS allcount FROM ims_lotto ");
    $stmt->execute();
    $records = $stmt->fetch();
    $totalRecords = $records['allcount'];

## Total number of records with filtering
    $stmt = $conn->prepare("SELECT COUNT(*) AS allcount FROM ims_lotto WHERE 1 " . $searchQuery);
    $stmt->execute($searchArray);
    $records = $stmt->fetch();
    $totalRecordwithFilter = $records['allcount'];

## Fetch records
    $stmt = $conn->prepare("SELECT * FROM ims_lotto WHERE 1 " . $searchQuery
        . " ORDER BY id DESC LIMIT :limit,:offset");

// Bind values
    foreach ($searchArray as $key => $search) {
        $stmt->bindValue(':' . $key, $search, PDO::PARAM_STR);
    }

    $stmt->bindValue(':limit', (int)$row, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$rowperpage, PDO::PARAM_INT);
    $stmt->execute();
    $empRecords = $stmt->fetchAll();
    $data = array();

    $start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
    $order_no = $start; // ใช้ start เป็นค่าตั้งต้นแทนการเริ่มจาก 0

    foreach ($empRecords as $row) {

        if ($_POST['sub_action'] === "GET_MASTER") {
            $order_no++;
            $data[] = array(
                "id" => $row['id'],
                "order_no" => $order_no,
                "lotto_name" => $row['lotto_name'],
                "lotto_phone" => $row['lotto_phone'],
                "lotto_province" => $row['lotto_province'],
                "lotto_number" => $row['lotto_number'],
                "sale_name" => $row['sale_name'],
                "lotto_file" => $row['lotto_file'],
                "lotto_file1" => $row['lotto_file1'],
                "lotto_file2" => $row['lotto_file2'],
                "lotto_file3" => $row['lotto_file3'],
                "lotto_file4" => $row['lotto_file4'],
                "lotto_file5" => $row['lotto_file5'],
                "approve_status" => $row['approve_status'],
                "create_date" => $row['create_date'],
                "update_date" => $row['update_date'],
                "update" => "<button type='button' name='update' id='" . $row['id'] . "' class='btn btn-info btn-xs update' data-toggle='tooltip' title='Update'>Update</button>",
                "delete" => "<button type='button' name='delete' id='" . $row['id'] . "' class='btn btn-danger btn-xs delete' data-toggle='tooltip' title='Delete'>Delete</button>",
                "status" => $row['status'] === 'Active' ? "<div class='text-success'>" . $row['status'] . "</div>" : "<div class='text-muted'> " . $row['status'] . "</div>"
            );
        } else {
            $data[] = array(
                "id" => $row['id'],
                "lotto_name" => $row['lotto_name'],
                "lotto_phone" => $row['lotto_phone'],
                "select" => "<button type='button' name='select' id='" . $row['lotto_name'] . "@" . $row['lotto_phone'] . "' class='btn btn-outline-success btn-xs select' data-toggle='tooltip' title='select'>select <i class='fa fa-check' aria-hidden='true'></i>
</button>",
            );
        }

    }

## Response Return Value
    $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordwithFilter,
        "aaData" => $data
    );

    echo json_encode($response);

}
