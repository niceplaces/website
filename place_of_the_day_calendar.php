<?php

require_once "data/requires.php";
require_once 'utils.php';
error_reporting(E_ALL && ~E_NOTICE);
$conn = mySqlConnect();
$dao = new DaoPlacesV3($conn, VERSION, MODE);
$one_day = 24*60*60;
$start =  strtotime(date("Y-m-d", time() - $one_day * 7));
$end = strtotime(date("Y-m-d", time() + $one_day * 365));
for ($i = $start; $i < $end; $i += $one_day){
    $placeOfTheDay = $dao->getPlaceOfTheDay(strtoupper(get_lang()), $i);
    echo date("Y-m-d", $i) . " - <a href=\"social/index.php?day=".$i."\">". $placeOfTheDay["name"] . " (" . $placeOfTheDay["area"]. ")</a><br/>";
}