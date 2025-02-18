<?php

require_once '../vendor/PhpXlsxGenerator-master/PhpXlsxGenerator.php';

$fileName = "simple_test_" . date('Y-m-d_H-i-s') . ".xlsx";

$data = [
    ['ID', 'Name', 'Phone'],
    [1, 'Alice', '0812345678'],
    [2, 'Bob', '0899999999'],
    [3, 'Charlie', '0888888888'],
    [],
    ['IDX', 'NameX', 'PhoneX'],
    [1, 'Alice', '0812345678'],
    [2, 'Bob', '0899999999'],
    [3, 'Charlie', '0888888888'],
];

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Cache-Control: max-age=0');

$xlsx = CodexWorld\PhpXlsxGenerator::fromArray($data);
$xlsx->downloadAs($fileName);
exit;
