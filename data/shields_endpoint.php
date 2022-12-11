<?php

header("Content-type:application/json; charset=UTF-8");

require_once 'requires.php';

error_reporting(0);

$conn = mySqlConnect();

$param = $_GET['param'];

$daoPlaces = new DaoPlacesV3($conn, 'v3', 'release');

$result = json_decode('{
    "schemaVersion": 1,
    "color": "success"
}', true);

$stats = $daoPlaces->getStats();

switch ($param){
    case 'places_count':
        $result["label"] = "Places";
        $result["message"] = $stats['places'];
        $result["color"] = "orange";
        break;
    case 'descriptions_count':
        $result["label"] = "Descriptions";
        $result["message"] = max($stats['places_with_description'], $stats['places_with_description_en']);
        $result["color"] = "yellow";
        break;
    case 'downloads':
        $result["label"] = "Downloads";
        $result["message"] = "1.2K";
        break;
    case 'contributors':
        $result["label"] = "Contributors";
        $result["message"] = "10";
        $result["color"] = "blue";
        break;
    default:
        break;
}

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);