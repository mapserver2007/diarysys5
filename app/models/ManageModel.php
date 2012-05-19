<?php
/**
 * 管理モデル
 * @author Ryuichi TANAKA.
 * @since 2012/05/03
 */
class ManageModel extends CoreModel {
    /**
     * ユーザIDを取得する
     * @param String ユーザ名
     * @return Integer ユーザID
     */
    public function getUserId($name) {
        $sql = "SELECT ID FROM AUTHENTICATION WHERE USER_ID = :name";
        $bind = array("name" => $name);
        return $this->db->select($sql, $bind);
    }
    
    /**
     * 直近で登録したエントリIDを取得する
     * @return Integer エントリID
     */
    public function getRecentEntryId() {
        $sql = "SELECT ID FROM DIARY5 ORDER BY ID DESC LIMIT 0, 1";
        return $this->db->select($sql);
    }
    
    /**
     * エントリ一覧を取得する
     * @param Integer 検索開始位置
     * @param Integer 取得件数
     * @return Hash エントリデータ
     */
    public function entryList($offset, $limit) {
        $sql = "SELECT ID, TITLE, DATE FROM DIARY5 ";
        $sql.= "ORDER BY ID DESC ";
        $sql.= "LIMIT :offset, :limit";
        $bind = array("offset" => $offset, "limit" => $limit);
        return $this->db->select($sql, $bind);
    }
    
    /**
     * エントリのタグ情報を取得する
     * @return Hash タグ情報
     */
    public function tags() {
        $sql = "SELECT T.ENTRY_ID AS ID, TM.NAME FROM TAG_MASTER AS TM ";
        $sql.= "LEFT OUTER JOIN TAG AS T ON TM.ID = T.TAG_ID ORDER BY ID";
        return $this->db->select($sql);
    }
    
    /**
     * エントリを登録する
     * @param String タイトル
     * @param String 本文
     * @param String 日付
     * @param Integer 天気ID
     * @paran Integer ユーザID
     * @return Boolean 実行結果
     */
    public function registerEntry($title, $description, $date, $weather, $user_id) {
        $sql = "INSERT INTO DIARY5 (TITLE, DESCRIPTION, DATE, WEATHER, USER_ID) ";
        $sql.= "VALUES (:title, :description, :date, :weather, :user_id)";
        $bind = array(
            "title" => $title,
            "description" => $description,
            "date" => $date,
            "weather" => $weather,
            "user_id" => $user_id
        );
        return $this->db->insert($sql, $bind);
    }
    
    /**
     * タグを登録する
     * @param Integer エントリID
     * @param Integer タグID
     * @return Boolean 実行結果
     */
    public function registerTag($entry_id, $tag_id) {
        $sql = "INSERT INTO TAG (ENTRY_ID, TAG_ID) VALUES (:entry_id, :tag_id)";
        $bind = array("entry_id" => $entry_id, "tag_id" => $tag_id);
        return $this->db->insert($sql, $bind);
    }
    
}
