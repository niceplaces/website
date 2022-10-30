<?php

class DaoRegions {

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
        $sql = "INSERT INTO ".$this->table_regions." (id, name, name_en) 
                VALUES (NULL, '".$name."', '');";
        $result = $this->connection->query($sql);
        return $result;
    }

    function update($id, $data){
        $sql = "UPDATE ".$this->table_regions." SET 
            id_string = '" . $data["id_string"] . "', 
            id_string_en = '" . $data["id_string_en"] . "', 
            name_en = '" . $data["name_en"] . "'
            WHERE id = " . $id;
        $result = $this->connection->query($sql);
        return $result;
    }

    function getOne($id_string){
        $sql = "SELECT *
                FROM ".$this->table_regions."
                WHERE id_string = '".$id_string."' OR id_string_en = '".$id_string."'";
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
        $sql1 = "SELECT regions.id AS id, regions.name AS name, regions.name_en AS name_en, 
                regions.id_string AS id_string, regions.id_string_en AS id_string_en, COUNT(places.id) AS count 
                FROM ".$this->table_regions." AS regions
                INNER JOIN ".$this->table_areas." AS areas ON regions.id = areas.id_region
                LEFT JOIN ".$this->table_places." AS places ON areas.id = places.id_area 
                WHERE regions.id <= 20
                GROUP BY regions.id ORDER BY count DESC";
        $sql2 = "SELECT regions.id AS id, regions.name AS name, regions.name_en AS name_en, 
                regions.id_string AS id_string, regions.id_string_en AS id_string_en, COUNT(places.id) AS count 
                FROM ".$this->table_regions." AS regions
                INNER JOIN ".$this->table_areas." AS areas ON regions.id = areas.id_region
                LEFT JOIN ".$this->table_places." AS places ON areas.id = places.id_area 
                WHERE regions.id > 20
                GROUP BY regions.id ORDER BY count DESC";
        $result1 = $this->connection->query($sql1);
        $result2 = $this->connection->query($sql2);
        $array = array();
        while ($row = $result1->fetch_assoc()) {
            $object = array(
                'id' => $row["id"],
                'id_string' => $row["id_string"],
                'id_string_en' => $row["id_string_en"],
                'name' => $row["name"],
                'name_en' => $row["name_en"],
                'count' => $row["count"]
            );
            array_push($array, $object);
        }
        while ($row = $result2->fetch_assoc()) {
            $object = array(
                'id' => $row["id"],
                'id_string' => $row["id_string"],
                'id_string_en' => $row["id_string_en"],
                'name' => $row["name"],
                'name_en' => $row["name_en"],
                'count' => $row["count"]
            );
            array_push($array, $object);
        }
        return $array;
    }

    function getAllUrls(){
        $sql = "SELECT regions.id_string AS id_region, regions.id_string_en AS id_region_en
                FROM " . $this->table_regions . " AS regions
                ORDER BY id_region";
        $result = $this->connection->query($sql);
        $count = null;
        $array = array();
        while ($row = $result->fetch_assoc()) {
            array_push($array, array(0 => "it", 1 => $row["id_region"]));
            array_push($array, array(0 => "en", 1 => $row["id_region_en"]));
        }
        return $array;
    }

}