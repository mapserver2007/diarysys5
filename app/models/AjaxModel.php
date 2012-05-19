<?php
/**
 * Ajaxモデル
 * @author Ryuichi TANAKA.
 * @since 2012/03/30
 */
class AjaxModel extends CoreModel {
    /**
     * 登録済みタグ一覧を取得する
     * @return Hash タグ一覧
     */
    public function tagList() {
        $sql = "SELECT TM.ID AS ID, TM.NAME AS NAME, COUNT(T.ID) AS COUNT ";
        $sql.= "FROM TAG_MASTER TM LEFT OUTER JOIN TAG T ON TM.ID = T.TAG_ID ";
        $sql.= "GROUP BY TM.ID";
        return $this->db->select($sql);
    }
    
    /**
     * タグを登録する
     * @param String タグ名
     * @return Boolean 結果
     */
    public function tagRegister($name) {
        $sql = "INSERT INTO TAG_MASTER (name) VALUES (:name)";
        $bind = array("name" => $name);
        return $this->db->insert($sql, $bind);
    }
    
    public function tagDelete() {
        $sql = "DELETE FROM TAG_MASTER WHERE ID IN (";
        $sql.= "SELECT ID FROM (";
        $sql.= "SELECT TM.ID AS ID, COUNT(T.ID) AS COUNT FROM TAG_MASTER AS TM LEFT OUTER JOIN TAG T ";
        $sql.= "ON TM.ID = T.TAG_ID GROUP BY TM.ID) ";
        $sql.= "AS TMP WHERE COUNT = 0)";
        return $this->db->delete($sql);
    }
}
