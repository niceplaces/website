<?php

class DaoPhotos {

    private $connection;
    private $mode;

    function __construct($conn, $mode){
        $this->connection = $conn;
        $this->mode = $mode;
    }

    /*function insert($id_area, $name){
        $sql = "INSERT INTO places (id, id_area, name, description, latitude, longitude, image, img_credits, wiki_url) 
                VALUES (NULL, '".$id_area."', '".$name."', '', 0, 0, '', '', '');";
        $result = $this->connection->query($sql);
        return $result;
    }

    function update($id, $data){
        $sql = "UPDATE places SET description = '".$data["description"]."', latitude = '".$data["latitude"]."',
                longitude= '".$data["longitude"]."', wiki_url = '".$data["wiki_url"]."' WHERE places.id = ".$id;
        $result = $this->connection->query($sql);
        return $result;
    }*/

    function getAll(){
        return array_slice(scandir("./photos/".$this->mode), 2);
    }

    /*function getOne($id){
        $sql = "SELECT * FROM places WHERE places.id = ".$id;
        $result = $this->connection->query($sql);
        $place = null;
        $daoEvents = new DaoEvents($this->connection);
        if ($row = $result->fetch_assoc()) {
            $place = array(
                'id' => $row["id"],
                'name' => $row["name"],
                'description' => $row["description"],
                'latitude' => $row["latitude"],
                'longitude' => $row["longitude"],
                'image' => $row["image"],
                'img_credits' => $row["img_credits"],
                'wiki_url' => $row["wiki_url"],
                'events' => $daoEvents->getByPlace($id)
            );
        }
        return $place;
    }*/

}