<?php
/**
 * フィードモデル
 * @author Ryuichi TANAKA.
 * @since 2012/02/07
 */
class FeedModel extends CoreModel {
    /**
     * エントリを取得する
     * @param Integer 検索開始位置
     * @param Integer 取得件数
     * @return Hash エントリデータ
     */
    public function entry($offset, $limit) {
        $sql = "SELECT ID, TITLE, DESCRIPTION, DATE, WEATHER FROM DIARY5 ";
        $sql.= "ORDER BY ID DESC ";
        $sql.= "LIMIT :offset, :limit";
        $bind = array("offset" => $offset, "limit" => $limit);
        return $this->db->select($sql, $bind);
    }
    
    /**
     * エントリのタグ情報を取得する
     * @return Hash タグ情報
     */
    public function tag() {
        $sql = "SELECT T.ENTRY_ID AS ID, TM.NAME FROM TAG_MASTER AS TM ";
        $sql.= "LEFT OUTER JOIN TAG AS T ON TM.ID = T.TAG_ID ORDER BY ID";
        return $this->db->select($sql);
    }
}
