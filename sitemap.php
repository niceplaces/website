<?php

header("Content-type:text/xml; charset=UTF-8");

error_reporting(E_ALL);
require_once 'data/connect_db.php';
require_once "data/dao/DaoRegions.php";
require_once "data/dao/DaoAreas.php";
require_once "data/dao/DaoPlacesV3.php";

$conn = mySqlConnect();

$dao = new DaoRegions($conn, "v3", "release");
$regions = $dao->getAllUrls();
$dao = new DaoAreas($conn, "v3", "release");
$areas = $dao->getAllUrls();
$dao = new DaoPlacesV3($conn, "v3", "release");
$places = $dao->getAllUrls();

$xml_string = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
      <loc>https://www.niceplaces.it/</loc>
      <changefreq>monthly</changefreq>
    </url>
    <url>
      <loc>https://www.niceplaces.it/en/</loc>
      <changefreq>monthly</changefreq>
    </url>';

for ($i = 0; $i < count($regions); $i++){
    $lang = ($regions[$i][0] == "it") ? "" : "en/";
    $xml_string = $xml_string . '<url>
      <loc>https://www.niceplaces.it/' . $lang . $regions[$i][1] . '/</loc>
      <changefreq>monthly</changefreq>
   </url>';
}
for ($i = 0; $i < count($areas); $i++){
    $lang = ($areas[$i][0] == "it") ? "" : "en/";
    $xml_string = $xml_string . '<url>
      <loc>https://www.niceplaces.it/' . $lang . $areas[$i][1] . '/</loc>
      <changefreq>monthly</changefreq>
   </url>';
}
for ($i = 0; $i < count($places); $i++){
    $lang = ($places[$i][0] == "it") ? "" : "en/";
    $xml_string = $xml_string . '<url>
      <loc>https://www.niceplaces.it/' . $lang . $places[$i][1] . '/' . $places[$i][2] . '</loc>
      <changefreq>monthly</changefreq>
   </url>';
}

$xml_string = $xml_string . '</urlset>';

$xml = new SimpleXMLElement($xml_string);
$xml->asXML("sitemap.xml");