<?php
namespace WebStream;
/**
 * Ajaxサービス
 * @author Ryuichi TANAKA.
 * @since 2012/03/30
 */
class AjaxService extends CoreService {
    /**
     * 最新のTweetを取得
     * @return String 最新のTweet
     */
    public function tweet() {
        $twitter_key = Utility::parseConfig("config/tweet.ini");
        $twitter = new Twitter(
            $twitter_key["consumer_key"],
            $twitter_key["consumer_secret"],
            $twitter_key["access_token"],
            $twitter_key["access_token_secret"]
        );
        return $twitter->getTweet();
    }
    
    /**
     * タグを登録し結果を受け取る
     * @param String タグ
     * @rerutn Hash 登録結果
     */
    public function tagRegister($name) {
        $result = array("status" => "failure");
        try {
            if ($this->Ajax->tagRegister($name)) {
                $result["status"] = "success";
                $result["message"] = "Success!";
            }
        }
        catch (Exception $e) {
            $message = "Already registered tag: ${name}";
            Logger::error($message);
            $result["message"] = $message;
        }
        return $result;
    }
    
    /**
     * 使用されていないタグを全て削除する
     * @return Hash 削除結果
     */
    public function tagDelete() {
        $result = array("status" => "failure");
        if ($this->Ajax->tagDelete()) {
            $result["status"] = "success";
            $result["message"] = "Success!";
        }
        return $result;
    }
    
    /**
     * 画像一覧を返却する
     * @params Integer ページ番号
     * @return Array 画像一覧
     */
    public function imageList($page) {
        $dir = PROJECT_ROOT . Constants::THUMBNAIL_DIR;
        $list = array();
        $hash = array();
        $limit = $this->getPageNum();
        $offset = $limit * ($page - 1);
        if ($handle = opendir($dir)) {
            while (false !== ($entry = readdir($handle))) {
                if (!in_array($entry, array(".", "..", ".svn", ".htaccess"))) {
                    $filepath = $dir . "/" . $entry;
                    $hash[filemtime($filepath)] = $entry;
                }
            }
            closedir($handle);
        }
        // タイムスタンプの降順にソート
        arsort($hash);
        // ファイル名のリストを作成
        $i = 0;
        $j = 0;
        foreach ($hash as $filename) {
            if ($offset > $i++) continue;
            if ($limit > $j++) {
                $list[] = $filename;
            }
            else {
                break;
            }
        }
        
        return $list;
    }
    
    /**
     * Amazon検索を実行する
     * @param String キーワード
     * @return Hash 検索結果
     */
    public function amazonItem($keyword) {
        $amazon_key = Utility::parseConfig("config/amazon.ini");
        $amazon = new Amazon($amazon_key["access_key"],
                             $amazon_key["secret_access_key"],
                             $amazon_key["associate_id"]);
        $result = $amazon->search($keyword);
        $items = array();
        
        if (array_key_exists("Item", $result["Items"])) {
            foreach ($result["Items"]["Item"] as $item) {
                $attribute = $item["ItemAttributes"];
                $author = null;
                if (array_key_exists("Author", $attribute)) {
                    $author = $attribute["Author"];
                }
                else if (array_key_exists("Creator", $attribute)) {
                    $author = $attribute["Creator"];
                }
                else {
                    $author = "-";
                }
                
                if (is_array($author)) {
                    $author = implode(",", $author);
                }
                $items[] = array(
                    "url" => $item["DetailPageURL"],
                    "image" => $item["MediumImage"]["URL"],
                    "author" => $author,
                    "price" => $attribute["ListPrice"]["FormattedPrice"],
                    "publisher" => $attribute["Publisher"],
                    "title" => $attribute["Title"],
                    "date" => $attribute["PublicationDate"],
                    "ISBN" => $attribute["ISBN"]
                );
            }
        }
        return $items;
    }
    
    /**
     * エントリ内容をキャッシュに保存する
     * @param Hash エントリデータ
     * @return Boolean 保存結果
     */
    public function temporarySave($data) {
        $cache = new Cache();
        return $cache->save(
            Constants::ENTRY_TEMPORARY_CACHE_ID,
            $data,
            Constants::ENTRY_TEMPORARY_CACHE_TIME,
            true
        );
    }
    
    /**
     * エントリ内容のキャッシュデータを取得する
     * @return Hash エントリデータ
     */
    public function temporaryLoad() {
        $cache = new Cache();
        return $cache->get(Constants::ENTRY_TEMPORARY_CACHE_ID);
    }
    
    /**
     * 1ページに表示するエントリ数
     * @return Integer 表示するエントリ数
     */
    private function getPageNum() {
        $paginate = Utility::parseConfig("config/paginate.ini");
        return $paginate["img_display"];
    }
}
