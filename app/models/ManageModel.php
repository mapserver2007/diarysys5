<?php
namespace WebStream;
/**
 * 管理モデル
 * @Inject
 * @Database("diarysys")
 * @Table("DIARY5, TAG")
 * @Properties("sql/manage.properties")
 * @author Ryuichi TANAKA.
 * @since 2012/05/03
 */
class ManageModel extends CoreModel {
    /**
     * ユーザIDを取得する
     * @Inject
     * @SQL("manage.user_id")
     * @param String ユーザ名
     * @return Integer ユーザID
     */
    public function getUserId($name) {
        return $this->select(array("name" => $name));
    }
    
    /**
     * 直近で登録したエントリIDを取得する
     * @Inject
     * @SQL("manage.recent_entry_id")
     * @return Integer エントリID
     */
    public function getRecentEntryId() {
        return $this->select();
    }
    
    /**
     * エントリ一覧を取得する
     * @Inject
     * @SQL("manage.entries")
     * @param Integer 検索開始位置
     * @param Integer 取得件数
     * @return Hash エントリデータ
     */
    public function entryList($offset, $limit) {
        return $this->select(array("offset" => $offset, "limit" => $limit));
    }
    
    /**
     * エントリのタグ情報を取得する
     * @Inject
     * @SQL("manage.tags")
     * @return Hash タグ情報
     */
    public function tags() {
        return $this->select();
    }
    
    /**
     * エントリを登録する
     * @Inject
     * @SQL("manage.register_entry")
     * @param String タイトル
     * @param String 本文
     * @param String 日付
     * @param Integer 天気ID
     * @param Integer ユーザID
     * @return Boolean 実行結果
     */
    public function registerEntry($title, $description, $date, $weather, $user_id) {
        return $this->insert(array(
            "title" => $title,
            "description" => $description,
            "date" => $date,
            "weather" => $weather,
            "user_id" => $user_id
        ));
    }
    
    /**
     * エントリを削除する
     * @param Integer エントリID
     */
    public function deleteEntry($id) {
        
    }
    
    
    /**
     * タグを登録する
     * @Inject
     * @SQL("manage.register_tag")
     * @param Integer エントリID
     * @param Integer タグID
     * @return Boolean 実行結果
     */
    public function registerTag($entry_id, $tag_id) {
        return $this->insert(array("entry_id" => $entry_id, "tag_id" => $tag_id));
    }
    
}
