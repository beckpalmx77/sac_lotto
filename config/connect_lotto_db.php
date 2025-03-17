<?php
date_default_timezone_set("Asia/Bangkok");
include('db_lotto_value.inc');
include('util/encode_data.php');

$DB_HOST = decrypt_data(DB_HOST,'Sac168168');
$DB_PORT = decrypt_data(DB_PORT,'Sac168168');
$DB_NAME = decrypt_data(DB_NAME,'Sac168168');
$DB_USER = decrypt_data(DB_USER,'Sac168168');
$DB_PASS = decrypt_data(DB_PASS,'Sac168168');

try
{
    $conn = new PDO("mysql:host=".$DB_HOST.";dbname=".$DB_NAME.";port=" .$DB_PORT
        ,$DB_USER, $DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
}
catch (PDOException $e)
{
    echo "Error: " . $e->getMessage();
    exit("Error: " . $e->getMessage());
}