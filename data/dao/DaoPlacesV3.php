<?php

require_once 'DaoEvents.php';

class DaoPlacesV3
{

    private $connection;
    private $version;
    private $mode;
    private $table_regions;
    private $table_areas;
    private $table_places;
    private $table_lists;

    function __construct($conn, $version, $mode)
    {
        $this->connection = $conn;
        $this->version = $version;
        $this->mode = $mode;
        $this->table_regions = "regions_" . $version . "_" . $mode;
        $this->table_areas = "areas_" . $version . "_" . $mode;
        $this->table_places = "places_" . $version . "_" . $mode;
        $this->table_lists = "lists_" . $version . "_" . $mode;
    }

    function insert($id_area, $name)
    {
        $sql = "INSERT INTO " . $this->table_places . " (id, id_string, id_string_en, id_area, name, name_en, description, 
                description_en, desc_sources, latitude, longitude, image, img_credits, wiki_url, wiki_url_en, facebook, 
                instagram, insert_time, last_update) 
                VALUES (NULL, '', '', '" . $id_area . "', '" . $name . "', '', '', '', '', 0, 0, '', '', '', '', '', '', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);";
        $result = $this->connection->query($sql);
        return $result;
    }

    function update($id, $data)
    {
        $sql = "UPDATE " . $this->table_places . " AS places SET 
                name_en = '" . $data["name_en"] . "', description = '" . $data["description"] . "', description_en = '" . $data["description_en"] . "', desc_sources = '" . $data["desc_sources"] . "', 
                latitude = '" . $data["latitude"] . "', longitude= '" . $data["longitude"] . "', wiki_url = '" . $data["wiki_url"] . "', wiki_url_en = '" . $data["wiki_url_en"] . "', 
                facebook = '" . $data["facebook"] . "', instagram = '" . $data["instagram"] . "', image = '" . $data["image"] . "',
                img_credits = '" . $data["img_credits"] . "', last_update = CURRENT_TIMESTAMP
                WHERE places.id = " . $id;
        $result = $this->connection->query($sql);
        return $result;
    }

    function getList()
    {
        $sql = "SELECT places.id AS id, places.id_string AS id_string_place, areas.id_string AS id_string_area,
                places.name AS place_name, places.description AS description, places.description_en AS description_en, 
                places.image AS image, areas.name AS area_name, wiki_url, wiki_url_en, facebook, instagram
                FROM " . $this->table_places . " AS places 
                INNER JOIN " . $this->table_areas . " AS areas ON places.id_area = areas.id 
                WHERE places.latitude <> 0 AND places.longitude <> 0
                ORDER BY area_name, place_name";
        $result = $this->connection->query($sql);
        $array = array();
        while ($row = $result->fetch_assoc()) {
            $hasDescription = (strcmp($row["description"], "") != 0);
            $hasDescriptionEn = (strcmp($row["description_en"], "") != 0);
            $object = array(
                'id' => $row["id"],
                'id_string_place' => $row["id_string_place"],
                'id_string_area' => $row["id_string_area"],
                'name' => $row["place_name"],
                'area' => $row["area_name"],
                'image' => $row["image"],
                'wiki_url' => $row["wiki_url"],
                'wiki_url_en' => $row["wiki_url_en"],
                'has_description' => $hasDescription,
                'has_description_en' => $hasDescriptionEn,
                'facebook' => $row["facebook"],
                'instagram' => $row["instagram"],
            );
            array_push($array, $object);
        }
        return $array;
    }

    function getByArea($id_area)
    {
        $sql = "SELECT * FROM " . $this->table_places . " AS places WHERE places.id_area = " . $id_area . " ORDER BY name";
        $result = $this->connection->query($sql);
        $array = array();
        while ($row = $result->fetch_assoc()) {
            $hasDescription = (strcmp($row["description"], "") != 0);
            $hasDescriptionEn = (strcmp($row["description_en"], "") != 0);
            $hasImage = (strcmp($row["image"], "") != 0);
            $object = array(
                'id' => $row["id"],
                'name' => $row["name"],
                'name_en' => $row["name_en"],
                'image' => $row["image"],
                'has_description' => $hasDescription,
                'has_description_en' => $hasDescriptionEn,
                'author' => $row["id_author"],
                'has_image' => $hasImage,
                'wiki_url' => $row["wiki_url"],
                'wiki_url_en' => $row["wiki_url_en"]
            );
            array_push($array, $object);
        }
        return $array;
    }

    function distance($lat1, $lon1, $lat2, $lon2, $unit)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);
        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    function getNearest($lat, $lon, $km_radius)
    {
        $km_per_lat_degree = 111.32;
        $km_per_lon_degree = 40075 * cos($lat) / 360;
        $max_lat = $lat + $km_radius / $km_per_lat_degree;
        $min_lat = $lat - $km_radius / $km_per_lat_degree;
        $max_lon = $lon + $km_radius / $km_per_lon_degree;
        $min_lon = $lon - $km_radius / $km_per_lon_degree;
        if ($max_lon < $min_lon){
            $tmp = $max_lon;
            $max_lon = $min_lon;
            $min_lon = $tmp;
        }
        $sql = "SELECT *, places.id AS id, places.id_string AS id_string_place, areas.id_string AS id_string_area,
                places.name AS name, places.name_en AS name_en, places.latitude AS latitude, places.longitude AS longitude, 
                places.image AS image, description, description_en,
                wiki_url, wiki_url_en
                FROM " . $this->table_places . " AS places
                INNER JOIN " . $this->table_areas . " AS areas ON places.id_area = areas.id 
                WHERE places.latitude > " . $min_lat . " AND places.latitude < " . $max_lat . "
                AND places.longitude > " . $min_lon . " AND places.longitude < " . $max_lon;
        $result = $this->connection->query($sql);
        $array = array();
        while ($row = $result->fetch_assoc()) {
            $distance = $this->distance($lat, $lon, $row["latitude"], $row["longitude"], "K");
            if ($distance <= $km_radius) {
                $hasDescription = (strcmp($row["description"], "") != 0);
                $hasDescriptionEn = (strcmp($row["description_en"], "") != 0);
                $object = array(
                    'id' => $row["id"],
                    'id_string_place' => $row["id_string_place"],
                    'id_string_area' => $row["id_string_area"],
                    'name' => $row["name"],
                    'name_en' => $row["name_en"],
                    'latitude' => $row["latitude"],
                    'longitude' => $row["longitude"],
                    'image' => $row["image"],
                    'wiki_url' => $row["wiki_url"],
                    'wiki_url_en' => $row["wiki_url_en"],
                    'has_description' => $hasDescription,
                    'has_description_en' => $hasDescriptionEn,
                    'author' => $row["id_author"],
                    'distance_km' => round($distance, 2)
                );
                array_push($array, $object);
            }
        }
        if ($this->mode == "debug"){
            //$array = array_merge($array, $this->getNearestFromDBPedia($lat, $lon, $km_radius));
        }
        usort($array, function ($a, $b){
            return $a["distance_km"] > $b["distance_km"];
        });
        return $array;
    }

    function getNearestFromDBPedia($lat, $lon, $km_radius)
    {
        $km_per_lat_degree = 111.32;
        $km_per_lon_degree = 40075 * cos($lat) / 360;
        $max_lat = $lat + $km_radius / $km_per_lat_degree;
        $min_lat = $lat - $km_radius / $km_per_lat_degree;
        $max_lon = $lon + $km_radius / $km_per_lon_degree;
        $min_lon = $lon - $km_radius / $km_per_lon_degree;
        $csv= file_get_contents("http://localhost/niceplaces/data/dao/sparql_2022-06-19_10-36-38Z.csv");
        $result = array_map("str_getcsv", explode("\n", $csv));
        $array = array();
        for ($i = 1; $i < count($result); $i++) {
            $row = $result[$i];
            $distance = $this->distance($lat, $lon, $row[5], $row[6], "K");
            if ($distance <= $km_radius) {
                //$hasDescription = (strcmp($row["description"], "") != 0);
                //$hasDescriptionEn = (strcmp($row["description_en"], "") != 0);
                $wiki_id = substr($row[0], strrpos($row[0], "/")+1, strlen($row[0]));
                $object = array(
                    'id' => null,
                    'id_string_place' => null,
                    'id_string_area' => null,
                    'name' => $row[1],
                    'name_en' => $row[1],
                    'latitude' => $row[5],
                    'longitude' => $row[6],
                    'image' => $row[7],
                    'wiki_url' => "https://it.wikipedia.org/wiki/" . $wiki_id,
                    'wiki_url_en' => "https://en.wikipedia.org/wiki/" . $wiki_id,
                    'has_description' => null,
                    'has_description_en' => null,
                    'author' => null,
                    'distance_km' => round($distance, 2)
                );
                array_push($array, $object);
            }
        }
        return $array;
    }

    function getFirstNearest($lat, $lon, $limit)
    {
        $sql = "SELECT *, places.id AS id, places.id_string AS id_string, places.id_string_en AS id_string_en, 
                areas.id_string AS id_area, areas.id_string_en AS id_area_en, 
                regions.id_string AS id_region, regions.id_string_en AS id_region_en, 
                places.name AS name, places.name_en AS name_en, areas.name as area_name, areas.name_en as area_name_en,
                regions.name as region_name, regions.name_en as region_name_en,
                places.latitude, places.longitude, places.image AS image, description, description_en,
                wiki_url, wiki_url_en
                FROM " . $this->table_places . " AS places
                INNER JOIN " . $this->table_areas . " AS areas ON places.id_area = areas.id
                INNER JOIN " . $this->table_regions . " AS regions ON areas.id_region = regions.id";
        $result = $this->connection->query($sql);
        $array = array();
        $i = 0;
        while ($row = $result->fetch_assoc()) {
            $distance = $this->distance($lat, $lon, $row["latitude"], $row["longitude"], "K");
            if ($distance > 0) {
                $hasDescription = (strcmp($row["description"], "") != 0);
                $hasDescriptionEn = (strcmp($row["description_en"], "") != 0);
                $object = array(
                    'id' => $row["id"],
                    'id_string' => $row["id_string"],
                    'id_string_en' => $row["id_string_en"],
                    'id_area' => $row["id_area"],
                    'id_area_en' => $row["id_area_en"],
                    'id_region' => $row["id_region"],
                    'id_region_en' => $row["id_region_en"],
                    'name' => $row["name"],
                    'name_en' => $row["name_en"],
                    'area' => $row["area_name"],
                    'area_en' => $row["area_name_en"],
                    'region' => $row["region_name"],
                    'region_en' => $row["region_name_en"],
                    'latitude' => $row["latitude"],
                    'longitude' => $row["longitude"],
                    'image' => $row["image"],
                    'wiki_url' => $row["wiki_url"],
                    'wiki_url_en' => $row["wiki_url_en"],
                    'has_description' => $hasDescription,
                    'has_description_en' => $hasDescriptionEn,
                    'author' => $row["id_author"],
                    'distance_km' => round($distance, 2)
                );
                array_push($array, $object);
            }
        }
        usort($array, function ($a, $b){
            return $a["distance_km"] > $b["distance_km"];
        });
        return array_slice($array, 0, $limit);
    }

    function getOne($id)
    {
        $sql = "SELECT *, places.id as id, places.id_string AS id_string, places.id_string_en AS id_string_en, 
                areas.id_string AS id_area, areas.id_string_en AS id_area_en, 
                regions.id_string AS id_region, regions.id_string_en AS id_region_en, 
                places.name as place_name, places.name_en as place_name_en, 
                places.latitude as place_lat, places.longitude as place_lon,
                areas.name as area_name, areas.name_en as area_name_en, places.img_credits as img_credits,
                regions.name as region_name, regions.name_en as region_name_en, places.image as image
                FROM " . $this->table_places . " AS places 
                INNER JOIN " . $this->table_areas . " AS areas ON places.id_area = areas.id 
                INNER JOIN " . $this->table_regions . " AS regions ON areas.id_region = regions.id 
                WHERE places.id = " . $id;
        $result = $this->connection->query($sql);
        $place = null;
        $daoEvents = new DaoEvents($this->connection, $this->version, $this->mode);
        if ($row = $result->fetch_assoc()) {
            $place = array(
                'id' => $row["id"],
                'id_string' => $row["id_string"],
                'id_string_en' => $row["id_string_en"],
                'id_area' => $row["id_area"],
                'id_area_en' => $row["id_area_en"],
                'id_region' => $row["id_region"],
                'id_region_en' => $row["id_region_en"],
                'name' => $row["place_name"],
                'name_en' => $row["place_name_en"],
                'area' => $row["area_name"],
                'area_en' => $row["area_name_en"],
                'region' => $row["region_name"],
                'region_en' => $row["region_name_en"],
                'description' => $row["description"],
                'description_en' => $row["description_en"],
                'desc_sources' => $row["desc_sources"],
                'author' => $row["id_author"],
                'latitude' => $row["place_lat"],
                'longitude' => $row["place_lon"],
                'image' => $row["image"],
                'img_credits' => $row["img_credits"],
                'wiki_url' => $row["wiki_url"],
                'wiki_url_en' => $row["wiki_url_en"],
                'facebook' => $row["facebook"],
                'instagram' => $row["instagram"],
                'events' => $daoEvents->getByPlace($id)
            );
        }
        return $place;
    }

    function getOneSummary($id)
    {
        $sql = "SELECT *, places.id as id, places.id_string AS id_string, places.id_string_en AS id_string_en,
                areas.id_string as id_area, areas.id_string_en as id_area_en, places.name as place_name, places.name_en as place_name_en, 
                areas.name as area_name, areas.name_en as area_name_en, places.img_credits as img_credits,
                regions.name as region_name, regions.name_en as region_name_en, places.image as image,
                wiki_url, wiki_url_en
                FROM " . $this->table_places . " AS places 
                INNER JOIN " . $this->table_areas . " AS areas ON places.id_area = areas.id 
                INNER JOIN " . $this->table_regions . " AS regions ON areas.id_region = regions.id 
                WHERE places.id = " . $id;
        $result = $this->connection->query($sql);
        $place = null;
        if ($row = $result->fetch_assoc()) {
            $hasDescription = (strcmp($row["description"], "") != 0);
            $hasDescriptionEn = (strcmp($row["description_en"], "") != 0);
            $place = array(
                'id' => $row["id"],
                'id_string' => $row["id_string"],
                'id_string_en' => $row["id_string_en"],
                'id_area' => $row["id_area"],
                'id_area_en' => $row["id_area_en"],
                'name' => $row["place_name"],
                'name_en' => $row["place_name_en"],
                'area' => $row["area_name"],
                'area_en' => $row["area_name_en"],
                'region' => $row["region_name"],
                'region_en' => $row["region_name_en"],
                'has_description' => $hasDescription,
                'has_description_en' => $hasDescriptionEn,
                'author' => $row["id_author"],
                'image' => $row["image"],
                'wiki_url' => $row["wiki_url"],
                'wiki_url_en' => $row["wiki_url_en"]
            );
        }
        return $place;
    }

    function getOneByIdString($id_string_area, $id_string_place)
    {
        $sql = "SELECT *, places.id as id,  places.id_string AS id_string, places.id_string_en AS id_string_en, 
                places.name as place_name, places.name_en as place_name_en, 
                areas.id_string as id_area, areas.id_string_en as id_area_en, 
                regions.id_string as id_region, regions.id_string_en as id_region_en, 
                areas.name as area_name, areas.name_en as area_name_en,
                regions.name as region_name, regions.name_en as region_name_en, places.image as image,
                places.img_credits as img_credits
                FROM " . $this->table_places . " AS places 
                INNER JOIN " . $this->table_areas . " AS areas ON places.id_area = areas.id 
                INNER JOIN " . $this->table_regions . " AS regions ON areas.id_region = regions.id 
                WHERE (areas.id_string = '" . $id_string_area . "' AND places.id_string = '" . $id_string_place . "')
                OR (areas.id_string_en = '" . $id_string_area . "' AND places.id_string_en = '" . $id_string_place . "')";
        $result = $this->connection->query($sql);
        $place = null;
        $daoEvents = new DaoEvents($this->connection, $this->version, $this->mode);
        if ($row = $result->fetch_assoc()) {
            $place = array(
                'id' => $row["id"],
                'id_string' => $row["id_string"],
                'id_string_en' => $row["id_string_en"],
                'id_area' => $row["id_area"],
                'id_area_en' => $row["id_area_en"],
                'id_region' => $row["id_region"],
                'id_region_en' => $row["id_region_en"],
                'name' => $row["place_name"],
                'name_en' => $row["place_name_en"],
                'area' => $row["area_name"],
                'area_en' => $row["area_name_en"],
                'region' => $row["region_name"],
                'region_en' => $row["region_name_en"],
                'description' => $row["description"],
                'description_en' => $row["description_en"],
                'desc_sources' => $row["desc_sources"],
                'author' => $row["id_author"],
                'latitude' => $row["latitude"],
                'longitude' => $row["longitude"],
                'image' => $row["image"],
                'img_credits' => $row["img_credits"],
                'wiki_url' => $row["wiki_url"],
                'wiki_url_en' => $row["wiki_url_en"],
                'facebook' => $row["facebook"],
                'instagram' => $row["instagram"],
                'events' => $daoEvents->getByPlace($row["id"])
            );
        }
        return $place;
    }

    function getByAreaIdString($id_string_area)
    {
        $sql = "SELECT *, places.id as id, places.id_string AS id_string, places.id_string_en AS id_string_en, places.name as place_name, 
                places.name_en as place_name_en, 
                areas.id_string as id_area, areas.id_string_en as id_area_en, areas.name as area_name, areas.name_en as area_name_en,
                regions.name as region_name, regions.name_en as region_name_en, places.image as image
                FROM " . $this->table_places . " AS places 
                INNER JOIN " . $this->table_areas . " AS areas ON places.id_area = areas.id 
                INNER JOIN " . $this->table_regions . " AS regions ON areas.id_region = regions.id 
                WHERE areas.id_string = '" . $id_string_area . "'
                ORDER BY places.name";
        $result = $this->connection->query($sql);
        $place = null;
        $array = array();
        while ($row = $result->fetch_assoc()) {
            $hasDescription = (strcmp($row["description"], "") != 0);
            $hasDescriptionEn = (strcmp($row["description_en"], "") != 0);
            $place = array(
                'id' => $row["id"],
                'id_string' => $row["id_string"],
                'id_string_en' => $row["id_string_en"],
                'id_area' => $row["id_area"],
                'id_area_en' => $row["id_area_en"],
                'name' => $row["place_name"],
                'name_en' => $row["place_name_en"],
                'area' => $row["area_name"],
                'area_en' => $row["area_name_en"],
                'region' => $row["region_name"],
                'region_en' => $row["region_name_en"],
                'image' => $row["image"],
                'has_description' => $hasDescription,
                'has_description_en' => $hasDescriptionEn,
                'author' => $row["id_author"]
            );
            array_push($array, $place);
        }
        return $array;
    }


    function getStats()
    {
        $sql = "SELECT COUNT(*) AS count FROM " . $this->table_places;
        $result = $this->connection->query($sql);
        $array = array();
        if ($row = $result->fetch_assoc()) {
            $array['places'] = $row["count"];
        }
        $sql = "SELECT COUNT(*) AS count FROM " . $this->table_places . " WHERE image <> ''";
        $result = $this->connection->query($sql);
        if ($row = $result->fetch_assoc()) {
            $array['places_with_photo'] = $row["count"];
        }
        $sql = "SELECT COUNT(*) AS count FROM " . $this->table_places . " WHERE description <> ''";
        $result = $this->connection->query($sql);
        if ($row = $result->fetch_assoc()) {
            $array['places_with_description'] = $row["count"];
        }
        $sql = "SELECT COUNT(*) AS count FROM " . $this->table_places . " WHERE description_en <> ''";
        $result = $this->connection->query($sql);
        if ($row = $result->fetch_assoc()) {
            $array['places_with_description_en'] = $row["count"];
        }
        return $array;
    }

    function getPlaceOfTheDay($language, $time = null)
    {
        $today = strtotime(date("Y-m-d", ($time == null) ? time() : $time));
        $epoch = strtotime("2020-01-06");
        $datediff = $today - $epoch;
        $desc_field = "description_en";
        if ($language == "IT"){
            $desc_field = "description";
        }
        $where_clause = $desc_field." <> '' AND places.image <> '' AND CHAR_LENGTH(places.".$desc_field.") > 500 AND places.place_of_the_day = 1";
        $count = $this->getCountWithWhere($where_clause);
        $one_day = (60 * 60 * 24);
        $index = floor($datediff / $one_day) % $count;
        $sql = "SELECT places.id
                FROM " . $this->table_places . " AS places
                INNER JOIN " . $this->table_areas . " AS areas ON places.id_area = areas.id 
                WHERE ".$where_clause."
                ORDER BY areas.latitude DESC, places.latitude DESC, places.longitude DESC
                LIMIT " . ($index % $count) . ", 1";
        $result = $this->connection->query($sql);
        $object = null;
        if ($row = $result->fetch_assoc()) {
            $object = $this->getOne($row["id"]);
        }
        return $object;
    }

    function getLastInserted()
    {
        $sql = "SELECT places.name AS place, areas.name AS area, insert_time
                FROM " . $this->table_places . " AS places
                INNER JOIN " . $this->table_areas . " AS areas ON places.id_area = areas.id 
                ORDER BY insert_time DESC LIMIT 50";
        $result = $this->connection->query($sql);
        $array = array();
        while ($row = $result->fetch_assoc()) {
            $object = array(
                'place' => $row["place"],
                'area' => $row["area"],
                'time' => $row["insert_time"]
            );
            array_push($array, $object);
        }
        return $array;
    }

    function getLastUpdated()
    {
        $sql = "SELECT places.name AS place, areas.name AS area, last_update
                FROM " . $this->table_places . " AS places
                INNER JOIN " . $this->table_areas . " AS areas ON places.id_area = areas.id 
                ORDER BY last_update DESC LIMIT 50";
        $result = $this->connection->query($sql);
        $array = array();
        while ($row = $result->fetch_assoc()) {
            $object = array(
                'place' => $row["place"],
                'area' => $row["area"],
                'time' => $row["last_update"]
            );
            array_push($array, $object);
        }
        return $array;
    }

    function getLatestInserted()
    {
        $sql = "SELECT *, places.id AS id, places.name as place_name, places.name_en as place_name_en, 
                areas.name as area_name, areas.name_en as area_name_en, regions.name as region_name, 
                regions.name_en as region_name_en, places.description AS description, places.description_en AS description_en, 
                places.image AS image, places.wiki_url AS wiki_url, places.wiki_url_en AS wiki_url_en,
                facebook, instagram
                FROM " . $this->table_places . " AS places 
                INNER JOIN " . $this->table_areas . " AS areas ON places.id_area = areas.id 
                INNER JOIN " . $this->table_regions . " AS regions ON areas.id_region = regions.id 
                ORDER BY insert_time DESC";
        $result = $this->connection->query($sql);
        $array = array();
        while ($row = $result->fetch_assoc()) {
            $hasDescription = (strcmp($row["description"], "") != 0);
            $hasDescriptionEn = (strcmp($row["description_en"], "") != 0);
            $object = array(
                'id' => $row["id"],
                'name' => $row["place_name"],
                'name_en' => $row["place_name_en"],
                'area' => $row["area_name"],
                'area_en' => $row["area_name_en"],
                'region' => $row["region_name"],
                'region_en' => $row["region_name_en"],
                'image' => $row["image"],
                'author' => $row["id_author"],
                'has_description' => $hasDescription,
                'has_description_en' => $hasDescriptionEn,
                'wiki_url' => $row["wiki_url"],
                'wiki_url_en' => $row["wiki_url_en"],
                'facebook' => $row["facebook"],
                'instagram' => $row["instagram"],
            );
            array_push($array, $object);
        }
        return $array;
    }

    function getLatestUpdated()
    {
        $sql = "SELECT *, places.id AS id, places.name as place_name, places.name_en as place_name_en, 
                areas.name as area_name, areas.name_en as area_name_en, regions.name as region_name, 
                regions.name_en as region_name_en, places.description AS description, places.description_en AS description_en, 
                places.image AS image, places.wiki_url AS wiki_url, places.wiki_url_en AS wiki_url_en,
                facebook, instagram
                FROM " . $this->table_places . " AS places 
                INNER JOIN " . $this->table_areas . " AS areas ON places.id_area = areas.id 
                INNER JOIN " . $this->table_regions . " AS regions ON areas.id_region = regions.id 
                ORDER BY last_update DESC";
        $result = $this->connection->query($sql);
        $array = array();
        while ($row = $result->fetch_assoc()) {
            $hasDescription = (strcmp($row["description"], "") != 0);
            $hasDescriptionEn = (strcmp($row["description_en"], "") != 0);
            $object = array(
                'id' => $row["id"],
                'name' => $row["place_name"],
                'name_en' => $row["place_name_en"],
                'area' => $row["area_name"],
                'area_en' => $row["area_name_en"],
                'region' => $row["region_name"],
                'region_en' => $row["region_name_en"],
                'image' => $row["image"],
                'author' => $row["id_author"],
                'has_description' => $hasDescription,
                'has_description_en' => $hasDescriptionEn,
                'wiki_url' => $row["wiki_url"],
                'wiki_url_en' => $row["wiki_url_en"],
                'facebook' => $row["facebook"],
                'instagram' => $row["instagram"],
            );
            array_push($array, $object);
        }
        return $array;
    }

    function getSearchResults($keyword_string){
        $sql = "SELECT *, places.id AS id, places.name as place_name, places.name_en as place_name_en, 
                areas.name as area_name, areas.name_en as area_name_en, regions.name as region_name, 
                regions.name_en as region_name_en, places.description AS description, places.description_en AS description_en, 
                places.image AS image
                FROM " . $this->table_places . " AS places 
                INNER JOIN " . $this->table_areas . " AS areas ON places.id_area = areas.id 
                INNER JOIN " . $this->table_regions . " AS regions ON areas.id_region = regions.id";
        $result = $this->connection->query($sql);
        $array = array();
        $array2 = array();
        $array3 = array();
        while ($row = $result->fetch_assoc()) {
            if (strpos(strtolower($row["place_name"]), strtolower($keyword_string)) !== false) {
                $hasDescription = (strcmp($row["description"], "") != 0);
                $hasDescriptionEn = (strcmp($row["description_en"], "") != 0);
                $object = array(
                    'id' => $row["id"],
                    'name' => $row["place_name"],
                    'name_en' => $row["place_name_en"],
                    'area' => $row["area_name"],
                    'area_en' => $row["area_name_en"],
                    'region' => $row["region_name"],
                    'region_en' => $row["region_name_en"],
                    'image' => $row["image"],
                    'author' => $row["id_author"],
                    'wiki_url' => $row["wiki_url"],
                    'wiki_url_en' => $row["wiki_url_en"],
                    'has_description' => $hasDescription,
                    'has_description_en' => $hasDescriptionEn,
                );
                array_push($array, $object);
            } else if (strpos(strtolower($row["description"]), strtolower($keyword_string)) !== false ||
                strpos(strtolower($row["description_en"]), strtolower($keyword_string)) !== false) {
                $hasDescription = (strcmp($row["description"], "") != 0);
                $hasDescriptionEn = (strcmp($row["description_en"], "") != 0);
                $object = array(
                    'id' => $row["id"],
                    'name' => $row["place_name"],
                    'name_en' => $row["place_name_en"],
                    'area' => $row["area_name"],
                    'area_en' => $row["area_name_en"],
                    'region' => $row["region_name"],
                    'region_en' => $row["region_name_en"],
                    'image' => $row["image"],
                    'author' => $row["id_author"],
                    'wiki_url' => $row["wiki_url"],
                    'wiki_url_en' => $row["wiki_url_en"],
                    'has_description' => $hasDescription,
                    'has_description_en' => $hasDescriptionEn,
                );
                array_push($array2, $object);
            } else if (strpos(strtolower($row["area_name"]), strtolower($keyword_string)) !== false ||
                strpos(strtolower($row["area_name_en"]), strtolower($keyword_string)) !== false ||
                strpos(strtolower($row["region_name"]), strtolower($keyword_string)) !== false ||
                strpos(strtolower($row["region_name_en"]), strtolower($keyword_string)) !== false) {
                $hasDescription = (strcmp($row["description"], "") != 0);
                $hasDescriptionEn = (strcmp($row["description_en"], "") != 0);
                $object = array(
                    'id' => $row["id"],
                    'name' => $row["place_name"],
                    'name_en' => $row["place_name_en"],
                    'area' => $row["area_name"],
                    'area_en' => $row["area_name_en"],
                    'region' => $row["region_name"],
                    'region_en' => $row["region_name_en"],
                    'image' => $row["image"],
                    'author' => $row["id_author"],
                    'wiki_url' => $row["wiki_url"],
                    'wiki_url_en' => $row["wiki_url_en"],
                    'has_description' => $hasDescription,
                    'has_description_en' => $hasDescriptionEn,
                );
                array_push($array3, $object);
            }
        }
        usort($array, function ($a, $b) use ($keyword_string) {
            return (levenshtein($keyword_string, $a["name"]) < levenshtein($keyword_string, $b["name"])) ? -1 : 1;
        });
        return array_merge($array, $array2, $array3);
    }

    function getCount()
    {
        $sql = "SELECT COUNT(*) AS count
                FROM " . $this->table_places;
        $result = $this->connection->query($sql);
        $count = null;
        if ($row = $result->fetch_assoc()) {
            $count = $row["count"];
        }
        return $count;
    }

    function getCountWithWhere($where_clause)
    {
        $sql = "SELECT COUNT(*) AS count FROM " . $this->table_places .
            " AS places WHERE " . $where_clause;
        $result = $this->connection->query($sql);
        $count = null;
        if ($row = $result->fetch_assoc()) {
            $count = $row["count"];
        }
        return $count;
    }

    function getAllUrls(){
        $sql = "SELECT areas.id_string AS id_area, places.id_string AS id_place,
                areas.id_string_en AS id_area_en, places.id_string_en AS id_place_en
                FROM " . $this->table_places . " AS places
                INNER JOIN " . $this->table_areas . " AS areas ON places.id_area = areas.id 
                ORDER BY id_area, id_place";
        $result = $this->connection->query($sql);
        $count = null;
        $array = array();
        while ($row = $result->fetch_assoc()) {
            array_push($array, array(0 => "it", 1 => $row["id_area"], 2 => $row["id_place"]));
            array_push($array, array(0 => "en", 1 => $row["id_area_en"], 2 => $row["id_place_en"]));
        }
        return $array;
    }

    function getRandom($n){
        $sql = "SELECT id
                FROM " . $this->table_places . "
                ORDER BY RAND()
                LIMIT " . $n;
        $result = $this->connection->query($sql);
        $count = null;
        $array = array();
        while ($row = $result->fetch_assoc()) {
            array_push($array, $this->getOne($row["id"]));
        }
        return $array;
    }

    function getRandomNearest($lat, $lon, $limit){
        $array = $this->getFirstNearest($lat, $lon, $limit*3);
        shuffle($array);
        return array_slice($array, 0, $limit);
    }

    function printChangelog(){
        $domain = "https://www.niceplaces.it/";
        // Nuovi luoghi IT
        $sql = "SELECT p.name AS place, p.id_string AS p_id, a.name AS area, r.name AS region, a.id_string AS a_id
        FROM places_v3_debug AS p
        INNER JOIN areas_v3_debug AS a ON p.id_area = a.id
        INNER JOIN regions_v3_debug AS r ON a.id_region = r.id
        WHERE p.id NOT IN ( SELECT id FROM places_v3_release )
        ORDER BY place";
        $result = $this->connection->query($sql);
        echo "<h3>Nuovi luoghi (".$result->num_rows.")</h3><ol>";
        while ($row = $result->fetch_assoc()) {
            echo '<li><a href="'.$domain.$row["a_id"].'/'.$row["p_id"].'">'.$row["place"].'</a> (<a href="'.
                $domain.$row["a_id"].'/">'.$row["area"].', '.$row["region"].'</a>)</li>';
        }
        echo "</ol>";
        // Nuove descrizioni IT
        $sql = "SELECT p.name AS place, p.id_string AS p_id, a.name AS area, r.name AS region, a.id_string AS a_id
        FROM places_v3_debug AS p
        INNER JOIN areas_v3_debug AS a ON p.id_area = a.id
        INNER JOIN regions_v3_debug AS r ON a.id_region = r.id
        INNER JOIN places_v3_release AS pr ON p.id = pr.id
        WHERE p.description <> '' AND pr.description = ''
        ORDER BY place";
        $result = $this->connection->query($sql);
        echo "<h3>Nuove descrizioni (".$result->num_rows.")</h3><ol>";
        while ($row = $result->fetch_assoc()) {
            echo '<li><a href="'.$domain.$row["a_id"].'/'.$row["p_id"].'">'.$row["place"].'</a> (<a href="'.
                $domain.$row["a_id"].'/">'.$row["area"].', '.$row["region"].'</a>)</li>';
        }
        echo "</ol>";
        // Descrizioni aggiornate IT
        $sql = "SELECT p.name AS place, p.id_string AS p_id, a.name AS area, r.name AS region, a.id_string AS a_id
        FROM places_v3_debug AS p
        INNER JOIN areas_v3_debug AS a ON p.id_area = a.id
        INNER JOIN regions_v3_debug AS r ON a.id_region = r.id
        INNER JOIN places_v3_release AS pr ON p.id = pr.id
        WHERE p.description <> pr.description AND pr.description <> ''
        ORDER BY place";
        $result = $this->connection->query($sql);
        echo "<h3>Descrizioni aggiornate (".$result->num_rows.")</h3><ol>";
        while ($row = $result->fetch_assoc()) {
            echo '<li><a href="'.$domain.$row["a_id"].'/'.$row["p_id"].'">'.$row["place"].'</a> (<a href="'.
                $domain.$row["a_id"].'/">'.$row["area"].', '.$row["region"].'</a>)</li>';
        }
        echo "</ol>";
        // Nuovi luoghi EN
        $sql = "SELECT IF(LENGTH(p.name_en)>0, p.name_en, p.name) AS place, p.id_string_en AS p_id, 
        IF(LENGTH(a.name_en)>0, a.name_en, a.name) AS area, 
        IF(LENGTH(r.name_en)>0, r.name_en, r.name) AS region, a.id_string_en AS a_id
        FROM places_v3_debug AS p
        INNER JOIN areas_v3_debug AS a ON p.id_area = a.id
        INNER JOIN regions_v3_debug AS r ON a.id_region = r.id
        WHERE p.id NOT IN ( SELECT id FROM places_v3_release )
        ORDER BY place";
        $result = $this->connection->query($sql);
        echo "<h3>New places (".$result->num_rows.")</h3><ol>";
        while ($row = $result->fetch_assoc()) {
            echo '<li><a href="'.$domain.$row["a_id"].'/'.$row["p_id"].'">'.$row["place"].'</a> (<a href="'.
                $domain.$row["a_id"].'/">'.$row["area"].', '.$row["region"].'</a>)</li>';
        }
        echo "</ol>";
        // Nuove descrizioni EN
        $sql = "SELECT IF(LENGTH(p.name_en)>0, p.name_en, p.name) AS place, p.id_string_en AS p_id, 
        IF(LENGTH(a.name_en)>0, a.name_en, a.name) AS area, 
        IF(LENGTH(r.name_en)>0, r.name_en, r.name) AS region, a.id_string_en AS a_id
        FROM places_v3_debug AS p
        INNER JOIN areas_v3_debug AS a ON p.id_area = a.id
        INNER JOIN regions_v3_debug AS r ON a.id_region = r.id
        INNER JOIN places_v3_release AS pr ON p.id = pr.id
        WHERE p.description_en <> '' AND pr.description_en = ''
        ORDER BY place";
        $result = $this->connection->query($sql);
        echo "<h3>New descriptions (".$result->num_rows.")</h3><ol>";
        while ($row = $result->fetch_assoc()) {
            echo '<li><a href="'.$domain.$row["a_id"].'/'.$row["p_id"].'">'.$row["place"].'</a> (<a href="'.
                $domain.$row["a_id"].'/">'.$row["area"].', '.$row["region"].'</a>)</li>';
        }
        echo "</ol>";
        // Descrizioni aggiornate EN
        $sql = "SELECT IF(LENGTH(p.name_en)>0, p.name_en, p.name) AS place, p.id_string_en AS p_id, 
        IF(LENGTH(a.name_en)>0, a.name_en, a.name) AS area, 
        IF(LENGTH(r.name_en)>0, r.name_en, r.name) AS region, a.id_string_en AS a_id
        FROM places_v3_debug AS p
        INNER JOIN areas_v3_debug AS a ON p.id_area = a.id
        INNER JOIN regions_v3_debug AS r ON a.id_region = r.id
        INNER JOIN places_v3_release AS pr ON p.id = pr.id
        WHERE p.description_en <> pr.description_en AND pr.description_en <> ''
        ORDER BY place";
        $result = $this->connection->query($sql);
        echo "<h3>Updated descriptions (".$result->num_rows.")</h3><ol>";
        while ($row = $result->fetch_assoc()) {
            echo '<li><a href="'.$domain.$row["a_id"].'/'.$row["p_id"].'">'.$row["place"].'</a> (<a href="'.
                $domain.$row["a_id"].'/">'.$row["area"].', '.$row["region"].'</a>)</li>';
        }
        echo "</ol>";
    }

}