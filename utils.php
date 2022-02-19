<?php

const DEBUG = true;

$BASE_URL = "https://www.niceplaces.it/";
if (DEBUG){
    $BASE_URL = "http://localhost/niceplaces/";
}


function get_lang(){
    if (strpos(get_url(), "/en/")) {
        return "en";
    } else {
        return "it";
    }
}

function get_url(){
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
        "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
}

function t($slug){
    $jsonFile = file_get_contents($GLOBALS['BASE_URL'] . "translations.json");
    $t = json_decode($jsonFile, true);
    return $t[$slug][get_lang()];
}