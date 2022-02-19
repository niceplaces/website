<?php

require_once 'DaoEvents.php';

class DaoPlaces
{

    private $connection;
    private $version;
    private $mode;
    private $table_areas;
    private $table_places;

    function __construct($conn, $version, $mode)
    {
        $this->connection = $conn;
        $this->version = $version;
        $this->mode = $mode;
        $this->table_areas = "areas_" . $version . "_" . $mode;
        $this->table_places = "places_" . $version . "_" . $mode;
    }

    function insert($id_area, $name)
    {
        $sql = "INSERT INTO " . $this->table_places . " (id, id_area, name, name_en, description, description_en, desc_sources, latitude, 
                longitude, image, img_credits, wiki_url, wiki_url_en, facebook, instagram, insert_time, last_update) 
                VALUES (NULL, '" . $id_area . "', '" . $name . "', '', '', '', '', 0, 0, '', '', '', '', '', '', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);";
        $result = $this->connection->query($sql);
        return $result;
    }

    function update($id, $data)
    {
        $sql = "UPDATE " . $this->table_places . " AS places SET 
                name_en = '" . $data["name_en"] . "', description = '" . $data["description"] . "', description_en = '" . $data["description_en"] . "', desc_sources = '" . $data["desc_sources"] . "', 
                latitude = '" . $data["latitude"] . "', longitude= '" . $data["longitude"] . "', wiki_url = '" . $data["wiki_url"] . "', 
                facebook = '" . $data["facebook"] . "', instagram = '" . $data["instagram"] . "', image = '" . $data["image"] . "',
                img_credits = '" . $data["img_credits"] . "', last_update = CURRENT_TIMESTAMP
                WHERE places.id = " . $id;
        $result = $this->connection->query($sql);
        return $result;
    }

    function getList()
    {
        $sql = "SELECT places.id AS id, places.name AS place_name, places.description AS description, places.description_en AS description_en, 
                places.image AS image, areas.name AS area_name, facebook, instagram
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
                'name' => $row["place_name"],
                'area' => $row["area_name"],
                'image' => $row["image"],
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
                'has_image' => $hasImage
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
        $sql = "SELECT * FROM " . $this->table_places . " AS places 
                WHERE latitude > " . $min_lat . " AND latitude < " . $max_lat . "
                AND longitude > " . $min_lon . " AND longitude < " . $max_lon;
        $result = $this->connection->query($sql);
        $array = array();
        while ($row = $result->fetch_assoc()) {
            $distance = $this->distance($lat, $lon, $row["latitude"], $row["longitude"], "K");
            if ($distance <= $km_radius) {
                $hasDescription = (strcmp($row["description"], "") != 0);
                $hasDescriptionEn = (strcmp($row["description_en"], "") != 0);
                $object = array(
                    'id' => $row["id"],
                    'name' => $row["name"],
                    'name_en' => $row["name_en"],
                    'latitude' => $row["latitude"],
                    'longitude' => $row["longitude"],
                    'image' => $row["image"],
                    'wiki_url' => $row["wiki_url"],
                    'wiki_url_en' => $row["wiki_url_en"],
                    'has_description' => $hasDescription,
                    'has_description_en' => $hasDescriptionEn,
                    'distance_km' => round($distance, 2)
                );
                array_push($array, $object);
            }
        }
        usort($array, function ($a, $b){
            return $a["distance_km"] > $b["distance_km"];
        });
        return $array;
    }

    function getOne($id)
    {
        $sql = "SELECT *, places.id as id, places.name as place_name, places.name_en as place_name_en, 
                areas.name as area_name, areas.name_en as area_name_en, places.image as image
                FROM " . $this->table_places . " AS places 
                INNER JOIN " . $this->table_areas . " AS areas ON places.id_area = areas.id 
                WHERE places.id = " . $id;
        $result = $this->connection->query($sql);
        $place = null;
        $daoEvents = new DaoEvents($this->connection, $this->version, $this->mode);
        if ($row = $result->fetch_assoc()) {
            $place = array(
                'id' => $row["id"],
                'name' => $row["place_name"],
                'name_en' => $row["place_name_en"],
                'area' => $row["area_name"],
                'area_en' => $row["area_name_en"],
                'description' => $row["description"],
                'description_en' => $row["description_en"],
                'desc_sources' => $row["desc_sources"],
                'latitude' => $row["latitude"],
                'longitude' => $row["longitude"],
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

    function getPlaceOfTheDay($language)
    {
        $today = strtotime(date("Y-m-d", time()));
        $epoch = strtotime("2018-01-01");
        $datediff = $today - $epoch;
        $desc_field = "description_en";
        if ($language == "IT"){
            $desc_field = "description";
        }
        $count = $this->getCountWithWhere($desc_field." <> ''");
        $index = floor($datediff / (60 * 60 * 24)) % $count;
        $sql = "SELECT id
                FROM " . $this->table_places . "
                WHERE ".$desc_field." <> ''
                ORDER BY latitude DESC, longitude
                LIMIT " . ($index % $count) . ", 1";
        $result = $this->connection->query($sql);
        $object = null;
        if ($row = $result->fetch_assoc()) {
            $object = $this->getOne($row["id"]);
        }
        return $object;
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
            " WHERE " . $where_clause;
        $result = $this->connection->query($sql);
        $count = null;
        if ($row = $result->fetch_assoc()) {
            $count = $row["count"];
        }
        return $count;
    }

}