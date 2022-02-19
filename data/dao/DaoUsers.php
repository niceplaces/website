<?php

class DaoUsers {

    public $connection;
    private $table_areas;
    private $table_places;
    private $table_users;
    private $table_tokens;

    function __construct($conn, $version, $mode){
        $this->connection = $conn;
        $this->table_areas = "areas_".$version."_".$mode;
        $this->table_places = "places_".$version."_".$mode;
        $this->table_users = "app_users_".$mode;
        $this->table_tokens = "access_tokens_".$mode;
    }

    function login($usename, $md5_password){
        $sql = "SELECT * FROM users WHERE username = '".$usename."'";
        $result = $this->connection->query($sql);
        if ($row = $result->fetch_assoc()) {
            if (strcmp($row["md5_password"], $md5_password) == 0){
                return array($row["id"], $row["name"]);
            } else {
                return false;
            }
        }
        return false;
    }

    function appRegister($username, $sha256_password, $name){
        if (strlen($username) == 0 || strlen($sha256_password) != 64 || strlen($name) == 0)
            return false;
        $sql = "INSERT INTO ".$this->table_users." (username, sha256_password, name) 
                VALUES ('".$username."', '".$sha256_password."', '".$name."');";
        $result = $this->connection->query($sql);
        if ($result){
            return $this->appLogin($username, $sha256_password);
        } else {
            return false;
        }
    }

    function appLogin($username, $sha256_password){
        if (strlen($username) == 0 || strlen($sha256_password) != 64)
            return false;
        $sql = "SELECT * FROM ".$this->table_users." WHERE username = '".$username."'";
        $result = $this->connection->query($sql);
        if ($row = $result->fetch_assoc()) {
            if (strcmp($row["sha256_password"], $sha256_password) == 0){
                $new_token = $this->generateAccessToken();
                $sql = "INSERT INTO ".$this->table_tokens." (token) VALUES ('".$new_token."');";
                $result = $this->connection->query($sql);
                if ($result){
                    return array('name' => $row["name"], 'token' => $new_token);
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        return false;
    }

    function appLogout($token){
        if (strlen($token) != 32)
            return false;
        $sql = "DELETE * FROM ".$this->table_tokens." WHERE token = '".$token."'";
        $result = $this->connection->query($sql);
        return $result;
    }

    function generateAccessToken() {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 32; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function getLastChanges(){
        $sql = "SELECT users.name AS user, places.name AS place, areas.name AS area, time
                FROM last_changes INNER JOIN users ON last_changes.id_user = users.id 
                INNER JOIN ".$this->table_places." AS places ON last_changes.id_place = places.id 
                INNER JOIN ".$this->table_areas." AS areas ON places.id_area = areas.id 
                ORDER BY time DESC LIMIT 10";
        $result = $this->connection->query($sql);
        $array = array();
        while ($row = $result->fetch_assoc()) {
            $object = array(
                'user' => $row["user"],
                'place' => $row["place"],
                'area' => $row["area"],
                'time' => $row["time"]
            );
            array_push($array, $object);
        }
        return $array;
    }

    function insertChange($id_user, $id_place){
        $sql = "INSERT INTO `last_changes` (id_user, id_place, time) 
                VALUES ('".$id_user."', '".$id_place."', CURRENT_TIMESTAMP);";
        $result = $this->connection->query($sql);
        return $result;
    }

}