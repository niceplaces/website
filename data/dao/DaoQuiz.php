<?php

class DaoQuiz {

    private $connection;
    private $table_regions;
    private $table_provinces;
    private $table_municipalities;
    private $table_places;
    private $table_quiz;

    function __construct($conn, $version, $mode){
        $this->connection = $conn;
        $this->table_regions = "regions_".$version."_".$mode;
        $this->table_provinces = "provinces_".$version."_".$mode;
        $this->table_municipalities = "municipalities_".$version."_".$mode;
        $this->table_places = "places_".$version."_".$mode;
        $this->table_quiz = "quiz_".$version."_".$mode;
    }

    function getAll(){
        $sql = "SELECT places.name AS places_name, places.name_en AS places_name_en, mun.name AS mun_name, prov.code AS prov_code,
                question, correct_answer, wrong_answer_1, wrong_answer_2 
                FROM ".$this->table_quiz." AS quiz INNER JOIN ".$this->table_places." AS places ON quiz.place_id = places.id
                INNER JOIN ".$this->table_municipalities." AS mun ON places.id_municipality = mun.id
                INNER JOIN ".$this->table_provinces." AS prov ON mun.id_province = prov.id";
        $result = $this->connection->query($sql);
        $array = array();
        while ($row = $result->fetch_assoc()) {
            $object = array(
                'name' => $row["places_name"],
                'name_en' => $row["places_name_en"],
                'area' => $row["mun_name"]." (".$row["prov_code"].")",
                'question' => $row["question"],
                'correct_answer' => $row["correct_answer"],
                'wrong_answer_1' => $row["wrong_answer_1"],
                'wrong_answer_2' => $row["wrong_answer_2"]
            );
            array_push($array, $object);
        }
        return $array;
    }

}