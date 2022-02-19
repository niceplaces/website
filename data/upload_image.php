<?php

ini_set("file_uploads", "On");
header("Content-type:application/json; charset=UTF-8");

$uploaddir = './photos/debug/';
$uploadfile = $uploaddir . basename($_FILES['file']['name']);

if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
    echo 1;
} else {
    echo 0;
}