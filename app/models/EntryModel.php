<?php
namespace WebStream;
/**
 * エントリモデル
 * @Inject
 * @Database("diarysys")
 * @Table("DIARY5, TAG")
 * @Properties("sql/entry.properties")
 * @author Ryuichi TANAKA.
 * @since 2011/11/02
 */
class EntryModel extends CoreModel {
    /**
     * エントリを取得する
     * @Inject
     * @SQL("entry.entry")
     * @param Integer 検索開始位置
     * @param Integer 取得件数
     * @return Hash エントリデータ
     */
    public function entry($offset, $limit) {
        $bind = array("offset" => $offset, "limit" => $limit);
        return $this->select($bind);
    }
    
    /**
     * タグによるエントリを取得する
     * @Inject
     * @SQL("entry.entry_by_tag")
     * @param String タグ名
     * @param Integer 検索開始位置
     * @param Integer 取得件数
     * @return Hash エントリデータ
     */
    public function entryByTag($name, $offset, $limit) {
        return $this->select(array("name" => $name, "offset" => $offset, "limit" => $limit));
    }
    
    /**
     * タグによるエントリを取得する
     * @Inject
     * @SQL("entry.entry_by_id")
     * @param Integer エントリID
     * @param Integer 検索開始位置
     * @param Integer 取得件数
     * @return Hash エントリデータ
     */
    public function entryById($id, $offset, $limit) {
        return $this->select(array("id" => $id, "offset" => $offset, "limit" => $limit));
    }
    
    /**
     * 年単位によるエントリを取得する
     * @Inject
     * @SQL("entry.entry_by_year")
     * @param String 年
     * @param Integer 検索開始位置
     * @param Integer 取得件数
     * @return Hash エントリデータ
     */
    public function entryByYear($year, $offset, $limit) {
        return $this->select(array("year" => $year, "offset" => $offset, "limit" => $limit));
    }
    
    /**
     * 月単位によるエントリを取得する
     * @Inject
     * @SQL("entry.entry_by_month")
     * @param String 年月
     * @param Integer 検索開始位置
     * @param Integer 取得件数
     * @return Hash エントリデータ
     */
    public function entryByMonth($month, $offset, $limit) {
        return $this->select(array("month" => $month, "offset" => $offset, "limit" => $limit));
    }
    
    /**
     * 日単位によるエントリを取得する
     * @Inject
     * @SQL("entry.entry_by_day")
     * @param String 年月日
     * @param Integer 検索開始位置
     * @param Integer 取得件数
     * @return Hash エントリデータ
     */
    public function entryByDay($day, $offset, $limit) {
        return $this->select(array("day" => $day, "offset" => $offset, "limit" => $limit));
    }
    
    /**
     * エントリ数を取得する
     * @Inject
     * @SQL("entry.entry_num")
     * @return Integer エントリ数
     */
    public function entryNum() {
        $result = $this->select();
        return $result[0]["ENTRY_NUM"];
    }

    /**
     * タグによるエントリ数を取得する
     * @Inject
     * @SQL("entry.entry_num_by_tag")
     * @return Integer エントリ数
     */
    public function entryNumByTag($name) {
        $result = $this->select(array("name" => $name));
        return $result[0]["ENTRY_NUM"];
    }
    
    /**
     * 年単位によるエントリ数を取得する
     * @Inject
     * @SQL("entry.entry_num_by_year")
     * @return Integer エントリ数
     */
    public function entryNumByYear($year) {
        $result = $this->select(array("year" => $year));
        return $result[0]["ENTRY_NUM"];
    }
    
    /**
     * 月単位によるエントリ数を取得する
     * @Inject
     * @SQL("entry.entry_num_by_month")
     * @return Integer エントリ数
     */
    public function entryNumByMonth($month) {
        $result = $this->select(array("month" => $month));
        return $result[0]["ENTRY_NUM"];
    }
    
    /**
     * 日単位によるエントリ数を取得する
     * @Inject
     * @SQL("entry.entry_num_by_day")
     * @return Integer エントリ数
     */
    public function entryNumByDay($day) {
        $result = $this->select(array("day" => $day));
        return $result[0]["ENTRY_NUM"];
    }
    
    /**
     * エントリのタグ情報を取得する
     * @Inject
     * @SQL("entry.tag")
     * @return Hash タグ情報
     */
    public function tag() {
        return $this->select();
    }
    
    /**
     * カレンダー(アーカイブデータ)を取得する
     * @Inject
     * @SQL("entry.calendar")
     * @return Hash アーカイブデータ
     */
    public function calendar() {
        return $this->select();
    }
}
