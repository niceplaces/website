<?php

class DaoAreas {

    private $connection;
    private $table_regions;
    private $table_areas;
    private $table_places;

    function __construct($conn, $version, $mode){
        $this->connection = $conn;
        $this->table_regions = "regions_".$version."_".$mode;
        $this->table_areas = "areas_".$version."_".$mode;
        $this->table_places = "places_".$version."_".$mode;
    }

    function insert($name){
        $sql = "INSERT INTO ".$this->table_areas." (id, name, name_en, id_region, image) 
                VALUES (NULL, '".$name."', '', 0, '');";
        $result = $this->connection->query($sql);
        return $result;
    }

    function getOne($id_string)
    {
        $sql = "SELECT *
                FROM " . $this->table_areas . "
                WHERE id_string = '" . $id_string . "' OR id_string_en = '" . $id_string . "'";
        $result = $this->connection->query($sql);
        $object = null;
        if ($row = $result->fetch_assoc()) {
            $object = array(
                'id' => $row["id"],
                'id_string' => $row["id_string"],
                'id_string_en' => $row["id_string_en"],
                'name' => $row["name"],
                'name_en' => $row["name_en"],
            );
        }
        return $object;
    }

    function getAll(){
        $sql = "SELECT areas.id AS id, areas.id_string AS id_string, areas.name AS name, areas.name_en AS name_en, areas.image as image, COUNT(places.id) AS count 
                FROM ".$this->table_areas." AS areas LEFT JOIN ".$this->table_places." AS places ON areas.id = places.id_area 
                GROUP BY areas.id ORDER BY areas.name";
        $result = $this->connection->query($sql);
        $array = array();
        while ($row = $result->fetch_assoc()) {
            $object = array(
                'id' => $row["id"],
                'id_string' => $row["id_string"],
                'name' => $row["name"],
                'name_en' => $row["name_en"],
                'image' => $row["image"],
                'count' => $row["count"]
            );
            array_push($array, $object);
        }
        return $array;
    }

    function getByRegion($id_region)
    {
        $sql = "SELECT areas.id AS id, areas.name AS name, areas.name_en AS name_en, areas.image as image, COUNT(places.id) AS count 
                FROM ".$this->table_areas." AS areas LEFT JOIN ".$this->table_places." AS places ON areas.id = places.id_area
                WHERE areas.id_region = " . $id_region . "
                GROUP BY areas.id ORDER BY count DESC";
        $result = $this->connection->query($sql);
        $array = array();
        while ($row = $result->fetch_assoc()) {
            $object = array(
                'id' => $row["id"],
                'name' => $row["name"],
                'name_en' => $row["name_en"],
                'image' => $row["image"],
                'count' => $row["count"]
            );
            array_push($array, $object);
        }
        return $array;
    }

    function getByRegionIdString($id_region)
    {
        $sql = "SELECT areas.id AS id, areas.id_string AS id_string, areas.id_string_en AS id_string_en, areas.name AS name, 
                areas.name_en AS name_en, 
                areas.image as image, COUNT(places.id) AS count 
                FROM ".$this->table_regions." AS regions 
                LEFT JOIN ".$this->table_areas." AS areas ON regions.id = areas.id_region
                LEFT JOIN ".$this->table_places." AS places ON areas.id = places.id_area
                WHERE regions.id_string = '" . $id_region . "'
                GROUP BY areas.id ORDER BY count DESC";
        $result = $this->connection->query($sql);
        $array = array();
        while ($row = $result->fetch_assoc()) {
            $object = array(
                'id' => $row["id"],
                'id_string' => $row["id_string"],
                'id_string_en' => $row["id_string_en"],
                'name' => $row["name"],
                'name_en' => $row["name_en"],
                'image' => $row["image"],
                'count' => $row["count"]
            );
            array_push($array, $object);
        }
        return $array;
    }

    function getAllUrls(){
        $sql = "SELECT areas.id_string AS id_area, areas.id_string_en AS id_area_en
                FROM " . $this->table_areas . " AS areas
                ORDER BY id_area";
        $result = $this->connection->query($sql);
        $count = null;
        $array = array();
        while ($row = $result->fetch_assoc()) {
            array_push($array, array(0 => "it", 1 => $row["id_area"]));
            array_push($array, array(0 => "en", 1 => $row["id_area_en"]));
        }
        return $array;
    }

}