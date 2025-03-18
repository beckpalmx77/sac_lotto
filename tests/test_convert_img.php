<?php

$input = "../uploads/file_67d8cf0cac46f7.55294372.heic";
$output = "imagefile_67.jpg";

// ตรวจสอบว่าไฟล์ HEIC มีอยู่จริง
if (file_exists($input)) {
    exec("magick convert $input $output");
    echo "<img src='$output' alt='Converted Image'>";
} else {
    echo "HEIC file not found!";
}

