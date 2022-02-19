<?php

require_once '../data/requires.php';

$conn = mySqlConnect();

session_start();
if (isset($_POST["action"]) && strcmp($_POST["action"], "logout") == 0){
    $_SESSION = array();
    session_destroy();
}
if (isset($_SESSION["login"])){
    include 'home.php';
} else if (!isset($_POST['username']) && !isset($_POST['password'])){
    include 'login.php';
} else {
    $daoUsers = new DaoUsers($conn, 'v2', 'debug');
    $res = $daoUsers->login($_POST['username'], md5($_POST['password']));
    if ($res != false){
        $_SESSION["id_user"] = $res[0];
        $_SESSION["login"] = $res[1];
        include 'home.php';
    } else {
        header("Location: /editor?op=wrong_credentials");
    }
}