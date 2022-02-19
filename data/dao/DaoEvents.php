<?php

class DaoEvents {

    private $connection;
    private $table;

    function __construct($conn, $version, $mode){
        $this->connection = $conn;
        $this->table = "events_".$version."_".$mode;
    }

    function processRequest($id_place, $data){
        $result = true;
        for ($i = 0; $i < count($data["insert"]); $i++){
            $result = $result && $this->insert($id_place, $data["insert"][$i]);
        }
        for ($i = 0; $i < count($data["update"]); $i++){
            $result = $result && $this->update($data["update"][$i]);
        }
        for ($i = 0; $i < count($data["delete"]); $i++){
            $result = $result && $this->delete($data["delete"][$i]["id"]);
        }
        return $result;
    }

    function insert($id_place, $event){
        $sql = "INSERT INTO ".$this->table." (id, id_place, date, description) 
                VALUES (NULL, '".$id_place."', '".$event["date"]."', '".$event["description"]."');";
        $result = $this->connection->query($sql);
        return $result;
    }

    function update($event){
        $sql = "UPDATE ".$this->table." AS events SET date = '".$event["date"]."', description = '".$event["description"]."' WHERE events.id = ".$event["id"];
        $result = $this->connection->query($sql);
        return $result;
    }

    function delete($id){
        $sql = "DELETE FROM ".$this->table." AS events WHERE events.id = ".$id;
        $result = $this->connection->query($sql);
        return $result;
    }

    function getByPlace($id_place){
        $sql = "SELECT * FROM ".$this->table." AS events WHERE events.id_place = ".$id_place;
        $result = $this->connection->query($sql);
        $array = array();
        while ($row = $result->fetch_assoc()) {
            $object = array(
                'id' => $row["id"],
                'date' => $row["date"],
                'description' => $row["description"]
            );
            array_push($array, $object);
        }
        return $array;
    }

    function getOne($id){
        $sql = "SELECT * FROM ".$this->table." AS events WHERE events.id = ".$id;
        $result = $this->connection->query($sql);
        if ($row = $result->fetch_assoc()) {
            $object = array(
                'id' => $row["id"],
                'date' => $row["date"],
                'description' => $row["description"]
            );
        }
        return $object;
    }

}