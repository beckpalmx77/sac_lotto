<?php

require_once '../config/connect_lotto_db.php';

$screen_name = "ims_customer_master";
$table_name = "ims_customer_master";
$latestImp = "";
$sql_get_seq = "SELECT seq_record FROM log_import_data WHERE screen_name = '" . $screen_name . "' ORDER BY id DESC limit 1";
$statement = $conn->query($sql_get_seq);
$results = $statement->fetchAll(PDO::FETCH_ASSOC);

foreach ($results as $result) {
    $latestImp = ($result['seq_record']);
}

if ($latestImp !== '') {
    $cond = " AND seq_record = '" . $latestImp . "'";
}

$sql_get = "SELECT * FROM " . $table_name . " WHERE 1 " . $cond . " ORDER BY id DESC";
$stmt = $conn->prepare($sql_get);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(["data" => $rows]);

