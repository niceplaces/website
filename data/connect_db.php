<?php

require_once "../protected/config.php";

ini_set('max_execution_time', 300);

define("VERSION", "v3");
define("MODE", "debug");

function mySqlConnect(){
    $servername = MYSQL_SERVERNAME;
    $username = MYSQL_USERNAME;
    $password = MYSQL_PASSWORD;
    $database = MYSQL_DATABASE;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);
    mysqli_set_charset($conn, 'utf8');
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}