<?php

class DebugModel extends CoreModel {
    
    
    public function run($title, $description) {
        $sql = "INSERT INTO DIARY5 (TITLE, DESCRIPTION, DATE, WEATHER, USER_ID) ";
        $sql.= "VALUES (:title, :description, :date, :weather, :user_id)";
        $bind = array(
            "title" => $title,
            "description" => $description,
            "date" => date('c'),
            "weather" => 1,
            "user_id" => 1
        );
        var_dump($bind);
        
        return $this->db->insert($sql, $bind);
    }
    
    public function entry() {
        $sql = "SELECT ID FROM DIARY5 ORDER BY ID DESC LIMIT 0, 1";
        $result = $this->db->select($sql);
        var_dump($result);
        return $result[0]["ID"];
    }
    
    public function runTag() {
        $entry_id = $this->entry();
        $sql = "INSERT INTO TAG (ENTRY_ID, TAG_ID) ";
        $sql.= "VALUES (:entry_id, :tag_id)";
        $bind = array(
            "entry_id" => $entry_id,
            "tag_id" => 2
        );
        return $this->db->insert($sql, $bind);
    }
    
}
