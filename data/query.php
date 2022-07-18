<?php

//header('Access-Control-Allow-Origin: *');
//header("Access-Control-Allow-Headers: Content-Type");
header("Content-type:application/json; charset=UTF-8");

require_once 'requires.php';

error_reporting(0);

$conn = mySqlConnect();

$POST = json_decode(file_get_contents('php://input'), true);

$version = $_GET['version'];
$mode = $_GET['mode'];
$getp1 = $_GET['p1'];
if (isset($POST)){
    $post = $POST;
}
if (isset($_GET['p2'])){
    $getp2 = $_GET['p2'];
}
if (isset($_GET['p3'])){
    $getp3 = $_GET['p3'];
}
if (isset($_GET['p4'])){
    $getp4 = $_GET['p4'];
}

$daoPlaces = new DaoPlaces($conn, $version, $mode);
if (strcmp($version, "v3") == 0){
    $daoPlaces = new DaoPlacesV3($conn, $version, $mode);
}
$daoRegions = new DaoRegions($conn, $version, $mode);
$daoAreas = new DaoAreas($conn, $version, $mode);
$daoEvents = new DaoEvents($conn, $version, $mode);
$daoUsers = new DaoUsers($conn, $version, $mode);
$daoPhotos = new DaoPhotos($conn, $mode);
$daoLists = new DaoLists($conn, $version, $mode);
$daoQuiz = new DaoQuiz($conn, $version, $mode);

switch ($getp1){
    case 'getplacesbyarea':
        $result = $daoPlaces->getByArea($getp2);
        break;
    case 'getareasbyregion':
        $result = $daoAreas->getByRegion($getp2);
        break;
    case 'getplacesbyid':
        $result = $daoPlaces->getOne($getp2);
        break;
    case 'getplacesbyidstring':
        $result = $daoPlaces->getOneByIdString($getp2, $getp3);
        break;
    case 'getplacesbyareaidstring':
        $result = $daoPlaces->getByAreaIdString($getp2);
        break;
    case 'updateplace':
        $result = $daoPlaces->update($getp2, $post);
        break;
    case 'updateevents':
        $result = $daoEvents->processRequest($getp2, $post);
        break;
    case 'getregions':
        $result = $daoRegions->getAll();
        break;
    case 'getareas':
        $result = $daoAreas->getAll();
        break;
    case 'getlastchanges':
        $result = $daoUsers->getLastChanges();
        break;
    case 'getlastinserted':
        $result = $daoPlaces->getLastInserted();
        break;
    case 'getlastupdated':
        $result = $daoPlaces->getLastUpdated();
        break;
    case 'insertarea':
        $result = $daoAreas->insert($getp2);
        break;
    case 'insertplace':
        $result = $daoPlaces->insert($getp2, $getp3);
        break;
    case 'insertchange':
        $result = $daoUsers->insertChange($getp2, $getp3);
        break;
    case 'getnearestplaces':
        $result = $daoPlaces->getNearest($getp2, $getp3, $getp4);
        break;
    case 'getrandomnearest':
        $result = $daoPlaces->getRandomNearest($getp2, $getp3, $getp4);
        break;
    case 'getphotos':
        $result = $daoPhotos->getAll();
        break;
    case 'getstats':
        $result = $daoPlaces->getStats();
        break;
    case 'getlist':
        $result = $daoPlaces->getList();
        break;
    case 'getplaceoftheday':
        $result = $daoPlaces->getPlaceOfTheDay("IT");
        break;
    case 'getplaceofthedayen':
        $result = $daoPlaces->getPlaceOfTheDay("EN");
        break;
    case 'getlatestinserted':
        $result = $daoPlaces->getLatestInserted();
        break;
    case 'getlatestupdated':
        $result = $daoPlaces->getLatestUpdated();
        break;
    case 'getsearchresults':
        $result = $daoPlaces->getSearchResults($getp2);
        break;
    case 'getlists':
        $result = $daoLists->getLists();
        break;
    case 'getplacesbylist':
        $result = $daoLists->getPlacesByListId($getp2);
        break;
    case 'getrandom':
        $result = $daoPlaces->getRandom($getp2);
        break;
    case 'getquiz':
        $result = $daoQuiz->getAll();
        break;
    case 'register':
        $result = $daoUsers->appRegister($post['username'], $post['sha256_password'], $post['name']);
        break;
    case 'login':
        $result = $daoUsers->appLogin($post['username'], $post['sha256_password']);
        break;
    case 'logout':
        $result = $daoUsers->appLogout($post['token']);
        break;
}

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);