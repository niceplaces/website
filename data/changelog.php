<?php
require_once 'requires.php';
$conn = mySqlConnect();
$daoPlaces = new DaoPlacesV3($conn, "v3", "debug");
$daoPlaces->printChangelog();