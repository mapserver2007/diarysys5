<?php
namespace WebStream;
/**
 * エントリモデル
 * @author Ryuichi TANAKA.
 * @since 2011/11/02
 */
class EntryModel extends CoreModel {
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
     * タグによるエントリを取得する
     * @param String タグ名
     * @param Integer 検索開始位置
     * @param Integer 取得件数
     * @return Hash エントリデータ
     */
    public function entryByTag($name, $offset, $limit) {
        $sql = "SELECT D.ID, D.TITLE, D.DESCRIPTION, D.DATE, D.WEATHER FROM DIARY5 AS D ";
        $sql.= "LEFT OUTER JOIN TAG AS T ON D.ID = T.ENTRY_ID ";
        $sql.= "LEFT OUTER JOIN TAG_MASTER AS TM ON TM.ID = T.TAG_ID ";
        $sql.= "WHERE TM.NAME = :name ";
        $sql.= "ORDER BY D.ID LIMIT :offset, :limit";
        $bind = array("name" => $name, "offset" => $offset, "limit" => $limit);
        return $this->db->select($sql, $bind);
    }
    
    /**
     * タグによるエントリを取得する
     * @param Integer エントリID
     * @param Integer 検索開始位置
     * @param Integer 取得件数
     * @return Hash エントリデータ
     */
    public function entryById($id, $offset, $limit) {
        $sql = "SELECT ID, TITLE, DESCRIPTION, DATE, WEATHER FROM DIARY5 ";
        $sql.= "WHERE ID = :id ";
        $sql.= "ORDER BY ID DESC ";
        $sql.= "LIMIT :offset, :limit";
        $bind = array("id" => $id, "offset" => $offset, "limit" => $limit);
        return $this->db->select($sql, $bind);
    }
    
    /**
     * 年単位によるエントリを取得する
     * @param String 年
     * @param Integer 検索開始位置
     * @param Integer 取得件数
     * @return Hash エントリデータ
     */
    public function entryByYear($year, $offset, $limit) {
        $sql = "SELECT ID, TITLE, DESCRIPTION, DATE, WEATHER FROM DIARY5 ";
        $sql.= "WHERE DATE_FORMAT(DATE, '%Y') = :year ";
        $sql.= "ORDER BY ID LIMIT :offset, :limit";
        $bind = array("year" => $year, "offset" => $offset, "limit" => $limit);
        return $this->db->select($sql, $bind);
    }
    
    /**
     * 月単位によるエントリを取得する
     * @param String 年月
     * @param Integer 検索開始位置
     * @param Integer 取得件数
     * @return Hash エントリデータ
     */
    public function entryByMonth($month, $offset, $limit) {
        $sql = "SELECT ID, TITLE, DESCRIPTION, DATE, WEATHER FROM DIARY5 ";
        $sql.= "WHERE DATE_FORMAT(DATE, '%Y%m') = :month ";
        $sql.= "ORDER BY ID LIMIT :offset, :limit";
        $bind = array("month" => $month, "offset" => $offset, "limit" => $limit);
        return $this->db->select($sql, $bind);
    }
    
    /**
     * 日単位によるエントリを取得する
     * @param String 年月日
     * @param Integer 検索開始位置
     * @param Integer 取得件数
     * @return Hash エントリデータ
     */
    public function entryByDay($day, $offset, $limit) {
        $sql = "SELECT ID, TITLE, DESCRIPTION, DATE, WEATHER FROM DIARY5 ";
        $sql.= "WHERE DATE_FORMAT(DATE, '%Y%m%d') = :day ";
        $sql.= "ORDER BY ID LIMIT :offset, :limit";
        $bind = array("day" => $day, "offset" => $offset, "limit" => $limit);
        return $this->db->select($sql, $bind);
    }
    
    /**
     * エントリ数を取得する
     * @return Integer エントリ数
     */
    public function entryNum() {
        $sql = "SELECT COUNT(ID) AS ENTRY_NUM FROM DIARY5";
        $result = $this->db->select($sql);
        return $result[0]["ENTRY_NUM"];
    }

    /**
     * タグによるエントリ数を取得する
     * @return Integer エントリ数
     */
    public function entryNumByTag($name) {
        $sql = "SELECT COUNT(D.ID) AS ENTRY_NUM FROM DIARY5 AS D LEFT OUTER JOIN TAG AS T ON D.ID = T.ENTRY_ID ";
        $sql.= "LEFT OUTER JOIN TAG_MASTER AS TM ON TM.ID = T.TAG_ID ";
        $sql.= "WHERE TM.NAME = :name";
        $bind = array("name" => $name);
        $result = $this->db->select($sql, $bind);
        return $result[0]["ENTRY_NUM"];
    }
    
    /**
     * 年単位によるエントリ数を取得する
     * @return Integer エントリ数
     */
    public function entryNumByYear($year) {
        $sql = "SELECT COUNT(ID) AS ENTRY_NUM FROM DIARY5 ";
        $sql.= "WHERE DATE_FORMAT(DATE, '%Y') = :year ";
        $bind = array("year" => $year);
        $result = $this->db->select($sql, $bind);
        return $result[0]["ENTRY_NUM"];
    }
    
    /**
     * 月単位によるエントリ数を取得する
     * @return Integer エントリ数
     */
    public function entryNumByMonth($month) {
        $sql = "SELECT COUNT(ID) AS ENTRY_NUM FROM DIARY5 ";
        $sql.= "WHERE DATE_FORMAT(DATE, '%Y%m') = :month ";
        $bind = array("month" => $month);
        $result = $this->db->select($sql, $bind);
        return $result[0]["ENTRY_NUM"];
    }
    
    /**
     * 日単位によるエントリ数を取得する
     * @return Integer エントリ数
     */
    public function entryNumByDay($day) {
        $sql = "SELECT COUNT(ID) AS ENTRY_NUM FROM DIARY5 ";
        $sql.= "WHERE DATE_FORMAT(DATE, '%Y%m%d') = :day ";
        $bind = array("day" => $day);
        $result = $this->db->select($sql, $bind);
        return $result[0]["ENTRY_NUM"];
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
    
    /**
     * カレンダー(アーカイブデータ)を取得する
     * @return Hash アーカイブデータ
     */
    public function calendar() {
        $sql = "SELECT DATE_FORMAT(DATE, '%Y-%m') AS DATETIME, COUNT(DATE) AS COUNT FROM DIARY5 ";
        $sql.= "GROUP BY DATETIME";
        return $this->db->select($sql);
    }
}
