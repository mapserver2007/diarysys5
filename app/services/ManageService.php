<?php
/**
 * 管理サービス
 * @author Ryuichi TANAKA.
 * @since 2012/03/27
 */
class ManageService extends CoreService {
    /**
     * 天気画像パスを返却する
     * @return String 天気画像パス
     */
    public function weather() {
        $weather = new Weather();
        return $weather->getWeather();
    }
    
    /**
     * 現在時刻を返却する
     * @return String 現在時刻
     */
    public function date() {
        return date('Y-m-d H:i:s');
    }
    
    /**
     * 差し戻しエントリ内容を一時保存する
     */
    public function remandSave($data) {
        $cache = new Cache();
        return $cache->save(
            Constants::REMAND_ENTRY_TEMPORARY_CACHE_ID,
            $data
        );
    }
    
    /**
     * エントリデータ一覧を取得する
     */
    public function entryList($page) {
        $paginate = Utility::parseConfig("config/paginate.ini");
        $display = $paginate["display"];
        $start = ($page - 1) * $display;
        $entries = $this->Manage->entryList($start, $display);
        if (count($entries) === 0) {
            throw new ResourceNotFoundException("Entry data not found");
        }
        $tags = $this->tags();
        foreach ($entries as &$entry) {
            $entry["TAG"] = $tags[$entry["ID"]];
        }
        
        return $entries;
    }
    
    /**
     * エントリにひもづくタグを返却する
     * @return Hash タグ情報
     */
    private function tags() {
        $tags = $this->Manage->tags();
        $entry_tags = array();
        // タグIDごとにまとめる
        foreach ($tags as $tag) {
            if (!array_key_exists($tag["ID"], $entry_tags)) {
                $entry_tags[$tag["ID"]] = array();
            }
            $entry_tags[$tag["ID"]][] = $tag["NAME"];
        }
        return $entry_tags;
    }
    
    /**
     * エントリを登録する
     * @param String タイトル
     * @param String 本文
     * @param Array タグ
     * @param String 日付
     * @param Integer 天気ID
     * @param String ユーザ名
     */
    public function register($title, $description, $tags, $date, $weather, $user_name) {
        // ユーザIDを取得
        $user = $this->Manage->getUserId($user_name);
        $user_id = $user[0]["ID"];
        // エントリを登録
        $this->Manage->registerEntry($title, $description, $date, $weather, $user_id);
        // 今登録したエントリIDを取得
        $entry = $this->Manage->getRecentEntryId();
        $entry_id = $entry[0]["ID"];
        // タグを登録
        foreach ($tags as $tag) {
            $this->Manage->registerTag($entry_id, $tag);
        }
    }
    
    /**
     * 差し戻しエントリ内容を取得する
     * @return Hash 差し戻しエントリ内容
     */
    public function remandLoad() {
        $cache = new Cache();
        $data = $cache->get(Constants::REMAND_ENTRY_TEMPORARY_CACHE_ID);
        if ($data !== null) {
            $cache->delete(Constants::REMAND_ENTRY_TEMPORARY_CACHE_ID);
        }
        return $data;
    }
    
    /**
     * PaginateのHTMLを返却する
     * @param Integer ページ番号
     * @return String PaginateHTML
     */
    public function imageListPaginate($current_page = 1) {
        $paginate = new Paginate($current_page,
                                 $this->getSaveImageNum(),
                                 $this->getPageNum(),
                                 $this->getPageBlock());
        return $paginate->html();
    }
    
    /**
     * エントリデータが存在するかどうか
     * @param Hash エントリデータ
     */
    public function checkEntryData(&$entry) {
        foreach ($entry as $value) {
            if ($value === null) {
                throw new ResourceNotFoundException("Entry data not found");
            }
        }
        // 天気情報を取得
        $entry["weather"] = $this->weather();
        // 現在時刻を取得
        $entry["date"] = $this->date();
    }
    
    /**
     * BBコードからHTMLに変換する
     * @param String BBコード
     * @param String HTML
     */
    public function bb2html($text) {
        return BBCode::convHTML($text);
    }
    
    /**
     * 1ページに表示するエントリ数
     * @return Integer 表示するエントリ数
     */
    private function getPageNum() {
        $paginate = Utility::parseConfig("config/paginate.ini");
        return $paginate["img_display"];
    }
    
    /**
     * 表示する最大ブロック数
     * @return Integer ブロック数
     */
    private function getPageBlock() {
        $paginate = Utility::parseConfig("config/paginate.ini");
        return $paginate["img_page_block"];
    }
    
    /**
     * 保存済み画像数を取得
     * @return Integer 画像数
     */
    private function getSaveImageNum() {
        $dir = PROJECT_ROOT . Constants::THUMBNAIL_DIR;
        $num = 0;
        if ($handle = opendir($dir)) {
            while (false !== ($entry = readdir($handle))) {
                if (!in_array($entry, array(".", "..", ".svn"))) {
                    $num++;
                }
            }
            closedir($handle);
        }
        return $num;
    }
}
