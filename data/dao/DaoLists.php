<?php

require_once 'DaoPlacesV3.php';

class DaoLists
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

    function getLists(){
        $sql = "SELECT *
                FROM ".$this->table_lists."
                ORDER BY id";
        $result = $this->connection->query($sql);
        $array = array();
        while ($row = $result->fetch_assoc()) {
            $object = array(
                'id' => $row["id"],
                'id_string' => $row["id_string"],
                'id_string_en' => $row["id_string_en"],
                'name' => $row["name"],
                'name_en' => $row["name_en"],
                'description' => $row["description"],
                'description_en' => $row["description_en"],
                'places' => $row["places"]
            );
            if ($row["id_string"] != "top-100") {
                $count = count(json_decode($object["places"]));
            } else {
                $count = 100;
            }
            $object["count"] = $count;
            array_push($array, $object);
        }
        return $array;
    }

    function getOne($id_string){
        $sql = "SELECT *
                FROM ".$this->table_lists."
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
                'description' => $row["description"],
                'description_en' => $row["description_en"],
                'places' => $row["places"]
            );
            if ($row["id_string"] != "top-100") {
                $count = count(json_decode($object["places"]));
            } else {
                $count = 100;
            }
            $object["count"] = $count;
        }
        return $object;
    }

    function top_places()
    {
        $array = $fields = array();
        $i = 0;
        if (is_null($GLOBALS['BASE_URL'])){
            $GLOBALS['BASE_URL'] = "https://www.niceplaces.it/";
        }
        $handle = fopen($GLOBALS['BASE_URL']."data/dao/analytics_data_210628.csv", "r");
        if ($handle) {
            while (($row = fgetcsv($handle, 4096)) !== false) {
                if (empty($fields)) {
                    $fields = $row;
                    continue;
                }
                foreach ($row as $k => $value) {
                    $array[$i][$fields[$k]] = $value;
                }
                $i++;
            }
            if (!feof($handle)) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($handle);
        }

        $dao = new DaoPlacesV3($this->connection, $this->version, $this->mode);
        $list = $dao->getList();
        $list = array_map(function ($e) {
            $e["pageviews"] = 0;
            $e["pages"] = array();
            return $e;
        }, $list);

        function remove_stop_words($v)
        {
            return !in_array($v, ["", "/", "a", "al", "degli", "della", "delle", "dello", "di", "in", "la", "le"]);
        }

        $regex = '/\/\w*(-\w+)*\/\w+(-\w+)*$/';

        foreach ($array as $row) {
            if (preg_match($regex, $row["Pagina"])) {
                for ($i = 0; $i < count($list); $i++) {
                    $input = '/' . $list[$i]["id_string_area"] . '/' . $list[$i]["id_string_place"];
                    if ($input == $row["Pagina"]) {
                        $list[$i]["pageviews"] += $row["Visualizzazioni di pagina"];
                        array_push($list[$i]["pages"], $input);
                    }
                }
            } else {
                $string = $row["Pagina"];
                $string_chunks = preg_split('/(\/|-)/', $string);
                $string_chunks = array_filter($string_chunks, "remove_stop_words");
                $best_overlap = 0;
                $choice_index = null;
                for ($i = 0; $i < count($list); $i++) {
                    $input = '/' . $list[$i]["id_string_area"] . '/' . $list[$i]["id_string_place"];
                    $chunks = preg_split('/(\/|-)/', $input);
                    $chunks = array_filter($chunks, "remove_stop_words");
                    $overlap = count(array_intersect($string_chunks, $chunks));
                    if ($overlap > $best_overlap) {
                        $choice_index = $i;
                        $best_overlap = $overlap;
                    }
                }
                if ($choice_index && $best_overlap / count($chunks) >= 0.5) {
                    $list[$choice_index]["pageviews"] += $row["Visualizzazioni di pagina"];
                    array_push($list[$choice_index]["pages"], $string);
                }
            }
        }

        usort($list, function ($a, $b) {
            if ($a["pageviews"] == $b["pageviews"]) {
                return 0;
            }
            return ($a["pageviews"] > $b["pageviews"]) ? -1 : 1;
        });

        $list = array_filter($list, function($x){
            return $x["has_description"];
        });

        /*$i = 0;
        foreach ($list as $row) {
            $i++;
            echo $i . ". /" . $row["id_string_area"] . '/' . $row["id_string_place"] . '   ' . $row["pageviews"] . '<br/>';
            foreach ($row["pages"] as $page) {
                echo " - " . $page . "<br/>";
            }
        }*/

        return array_slice(array_map(function ($e) {
            return intval($e["id"]);
        }, $list), 0, 100);

    }

    function getPlacesByListId($id){
        $array = array();
        $object = null;
        $places = array();
        $daoPlaces = new DaoPlacesV3($this->connection, $this->version, $this->mode);
        if ($id != "1"){
            $sql = "SELECT *
                FROM ".$this->table_lists."
                WHERE id = '".$id."'";
            $result = $this->connection->query($sql);
            if ($row = $result->fetch_assoc()) {
                $places = json_decode($row["places"]);
            }
        } else {
            $places = $this->top_places();
        }
        for ($i = 0; $i < count($places); $i++){
            $object = $daoPlaces->getOneSummary($places[$i]);
            array_push($array, $object);
        }
        if ($id != "1"){
            usort($array, function ($a, $b){
                return strcmp($a["name"], $b["name"]);
            });
        }
        return $array;
    }

    function getPlacesByListIdString($id_string){
        $array = array();
        $object = null;
        $places = array();
        $daoPlaces = new DaoPlacesV3($this->connection, $this->version, $this->mode);
        if ($id_string == "top-100") {
            $places = $this->top_places();
        } else {
            $sql = "SELECT *
                FROM ".$this->table_lists."
                WHERE id_string = '".$id_string."'";
            $result = $this->connection->query($sql);
            if ($row = $result->fetch_assoc()) {
                $places = json_decode($row["places"]);
            }
        }
        for ($i = 0; $i < count($places); $i++){
            $object = $daoPlaces->getOneSummary($places[$i]);
            array_push($array, $object);
        }
        if ($id_string != "top-100"){
            usort($array, function ($a, $b){
                return strcmp($a["name"], $b["name"]);
            });
        }
        return $array;
    }

}