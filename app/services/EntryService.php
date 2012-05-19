<?php
/**
 * エントリサービス
 * @author Ryuichi TANAKA.
 * @since 2011/11/02
 */
class EntryService extends CoreService {
    /**
     * エントリデータを取得する
     * @param String 処理メソッド名
     * @param Integer ページ番号
     * @param Array 処理メソッドに渡す引数
     */    
    private function getEntry($callback, $page, $arguments = array()) {
        $paginate = Utility::parseConfig("config/paginate.ini");
        $display = $paginate["display"];
        $start = ($page - 1) * $display;
        array_push($arguments, $start, $display);
        
        $entries = call_user_func_array(array($this->Entry, $callback), $arguments);
        
        if (count($entries) === 0) {
            throw new ResourceNotFoundException("Entry data not found");
        }
        
        $tags = $this->tags();
        foreach ($entries as &$entry) {
            $entry["TAG"] = $tags[$entry["ID"]];
            $entry["WEATHER"] = "http://image.weather.livedoor.com/img/icon/{$entry['WEATHER']}.gif";
            $entry["DESCRIPTION"] = BBCode::convHTML($entry["DESCRIPTION"]);
        }
        
        return $entries;
    }
    
    /**
     * エントリデータを加工して返却する
     * @param Integer ページ番号
     * @return Hash 加工済みエントリデータ
     */
    public function entry($page) {
        return $this->getEntry("entry", $page);
    }
    
    /**
     * タグによるエントリデータを加工して返却する
     * @param String タグ名
     * @param Integer ページ番号
     * @return Hash 加工済みエントリデータ
     */
    public function entryByTag($name, $page) {
        return $this->getEntry("entryByTag", $page, array($name));
    }
    
    /**
     * IDによるエントリデータを加工して返却する
     * @param Integer エントリID
     * @param Integer ページ番号
     * @return Hash 加工済みエントリデータ
     */
    public function entryById($id, $page) {
        return $this->getEntry("entryById", $page, array($id));
    }
    
    /**
     * 年単位によるエントリデータを加工して返却する
     * @param Integer 年
     * @param Integer ページ番号
     */
    public function entryByArchive($ymd, $page) {
        // 年単位、月単位、日単位に分ける
        if (preg_match('/^(\d{4}){1}(\d{2}){0,1}(\d{2}){0,1}$/', $ymd, $matches)) {
            $year = isset($matches[1]) ? $matches[1] : null;
            $month = isset($matches[2]) ? $year . $matches[2] : null;
            $day = isset($matches[3]) ? $month . $matches[3] : null;
            // 年単位
            if ($year !== null && $month === null && $day === null) {
                return $this->getEntry("entryByYear", $page, array($year));
            }
            // 月単位
            else if ($year !== null && $month !== null && $day === null) {
                return $this->getEntry("entryByMonth", $page, array($month));
            }
            // 日単位
            else if ($year !== null && $month !== null && $day !== null) {
                return $this->getEntry("entryByDay", $page, array($day));
            }
        }
        else {
            throw new ResourceNotFoundException("Invalid archive URL");
        }
    }
    
    /**
     * PaginateのHTMLを返却する
     * @param Integer 現在のページ番号
     * @return String PaginateのHTML
     */
    public function paginate($current_page = 1) {
        $paginate = new Paginate($current_page,
                                 $this->Entry->entryNum(),
                                 $this->getPageNum(),
                                 $this->getPageBlock());
        return $paginate->html();
    }
    
    /**
     * タグによるPaginateのHTMLを返却する
     * @param String タグ名
     * @param Integer 現在のページ番号
     * @return String PaginateのHTML
     */
    public function paginateByTag($name, $current_page = 1) {
        $paginate = new Paginate($current_page,
                                 $this->Entry->entryNumByTag($name),
                                 $this->getPageNum(),
                                 $this->getPageBlock());
        return $paginate->html();
    }
    
    /**
     * IDによるPaginateのHTMLを返却する
     * @param Integer 現在のページ番号
     * @return String PaginateのHTML
     */
    public function paginateById($current_page = 1) {
        $paginate = new Paginate($current_page,
                                 1,
                                 $this->getPageNum(),
                                 $this->getPageBlock());
        return $paginate->html();
    }
    
    /**
     * 日付によるPaginateのHTMLを返却する
     * @param Integer 現在のページ番号
     * @return String PaginateのHTML
     */
    public function paginateByArchive($ymd, $current_page = 1) {
        $entry_num = 0;
        // 年単位、月単位、日単位に分ける
        if (preg_match('/^(\d{4}){1}(\d{2}){0,1}(\d{2}){0,1}$/', $ymd, $matches)) {
            $year = isset($matches[1]) ? $matches[1] : null;
            $month = isset($matches[2]) ? $year . $matches[2] : null;
            $day = isset($matches[3]) ? $month . $matches[3] : null;
            // 年単位
            if ($year !== null && $month === null && $day === null) {
                $entry_num = $this->Entry->entryNumByYear($year);
            }
            // 月単位
            else if ($year !== null && $month !== null && $day === null) {
                $entry_num = $this->Entry->entryNumByMonth($month);
            }
            // 日単位
            else if ($year !== null && $month !== null && $day !== null) {
                $entry_num = $this->Entry->entryNumByDay($day);
            }
        }
        $paginate = new Paginate($current_page, $entry_num);
        return $paginate->html();
    }
    
    /**
     * カレンダー(アーカイブデータ)を返却する
     * 
     */
    public function calendar() {
        return Calendar::convHTML($this->Entry->calendar());
    }
    
    /**
     * 1ページに表示するエントリ数
     * @return Integer 表示するエントリ数
     */
    private function getPageNum() {
        $paginate = Utility::parseConfig("config/paginate.ini");
        return $paginate["display"];
    }
    
    /**
     * 表示する最大ブロック数
     * @return Integer ブロック数
     */
    private function getPageBlock() {
        $paginate = Utility::parseConfig("config/paginate.ini");
        return $paginate["page_block"];
    }
    
    /**
     * エントリにひもづくタグを返却する
     * @return Hash タグ情報
     */
    private function tags() {
        $tags = $this->Entry->tag();
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
}
