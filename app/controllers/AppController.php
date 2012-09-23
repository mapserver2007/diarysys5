<?php
namespace WebStream;
/**
 * 共通コントローラ
 * @author Ryuichi TANAKA.
 * @since 2011/11/02
 */
class AppController extends CoreController {
    /** ロケール設定ファイル */
    protected $_ja;
    /** ページ */
    protected $_page;
    /** メッセージ */
    protected $_message;
    /** UserAgent */
    protected $isPC = false;
    protected $isMobile = false;
    
    /**
     * @Inject
     * @Filter("Before")
     */
    public function before() {
        define('PROJECT_ROOT', STREAM_ROOT);
        $this->_ja = Utility::parseConfig("config/locales/ja.ini");
        $this->_page = $this->getPage();
        $this->userAgent();
    }
    
    /**
     * ログイン済みでない場合は403を返す
     */
    protected function isLoggedIn() {
        if (!$this->session->get("user_id")) {
            $this->forbidden();
        }
    }
    
    /**
     * 妥当なリファラかどうか
     */
    protected function isReferer() {
        if (!$this->request->referer()) {
            //$this->forbidden();
        }
    }
    
    /**
     * メッセージを取得する
     * @param String メッセージキー
     * @return String メッセージ
     */
    protected function message($key) {
        return $this->_ja[$key];
    }
    
    /**
     * <title>に表示する内容を返却する
     * @param String ページ固有のtitle
     * @return String title
     */
    protected function getTitle($page_title) {
        return $this->message("common_title") . " - " . 
            $page_title . " - " . $this->message("domain");
    }
    
    /**
     * ヘッダのテンプレートを取得する
     * @return String ヘッダテンプレート名
     */
    protected function getHeaderTemplate() {
        return $this->session->get("user_id") !== null ?
            "header.loggedin" : "header";
    }
    
    /**
     * テンプレートに書き出すパラメータに共通のパラメータをマージして返却する
     * @param Hash ページ固有パラメータ
     * @return Hash マージ済みパラメータ
     */
    protected function getContent($params) {
        if (!array_key_exists("content", $params)) {
            $params["content"] = array();
        }
        return array_merge($params, array(
            "header" => array(
                "site_title" => $this->message("site_title"),
                "site_summary" => $this->message("site_summary"),
                "user_id" => $this->session->get("user_id")
            )
        ));
    }
    
    /**
     * GETパラメータによるページ番号を取得する
     * @return int ページ番号
     */
    protected function getPage() {
        $request = new Request();
        $page = $request->get("page");
        if (isset($page)) {
            if (!preg_match('/^[1-9]\d{0,}/', $page)) {
                throw new ResoureceNotFoundException("Invalid page: " . $page);
            }
            $page = intval($page);
        }
        else {
            $page = 1;
        }
        return $page;
    }
    
    /**
     * UserAgentを判定する
     */
    protected function userAgent() {
        // mobile
        if (preg_match('/iP(?:hone|ad)/', $this->request->userAgent())) {
            $this->isMobile = true;
        }
        // PC
        else {
            $this->isPC = true;
        }
    }
    
    /**
     * 条件に関係なくエントリを描画する
     * @param Hash エントリデータ
     * @param Hash カレンダーデータ
     * @param String PaginateのHTML
     */
    protected function render_entry($entries, $calendar, $paginate) {
        $this->layout("base", $this->getContent(array(
            "template" => "index",
            "header_template" => $this->getHeaderTemplate(),
            "title" => $this->getTitle($entries[0]["TITLE"]),
            "content" => array(
                "entries" => $entries,
                "calendar" => $calendar,
                "paginate" => $paginate
            )
        )));
    }
    
    protected function render_mobile_entry() {
        $this->layout("base.mobile", $this->getContent(array(
            "template" => "index.mobile",
            "title" => "test title mobile",
            "content" => array(
                "test" =>"kita-"
            )
        )));
    }
    
}
