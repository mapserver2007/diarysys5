<?php
namespace WebStream;
/**
 * 管理コントローラ
 * @author Ryuichi TANAKA.
 * @since 2012/03/27
 */
class ManageController extends AppController {
    /**
     * before filter
     */
    public function before() {
        $this->isLoggedIn();
        parent::before();
    }
    
    /**
     * エントリ一覧を表示する
     */
    public function entryList() {
        $this->layout("base", $this->getContent(array(
            "template" => "entry_list",
            "header_template" => "header.manage",
            "title" => $this->getTitle("エントリ一覧"),
            "content" => array(
                "entry" => $this->Manage->entryList($this->_page)
            )
        )));
    }
    
    /**
     * エントリ登録画面を描画する
     */
    public function entryRegister() {
        $this->layout("base", $this->getContent(array(
            "template" => "entry_register",
            "header_template" => "header.manage",
            "title" => $this->getTitle("エントリ登録"),
            "content" => array(
                "entry" => $this->Manage->remandLoad()
            )
        )));
    }
    
    /**
     * エントリ登録プレビュー画面を描画する
     */
    public function entryRegisterPreview() {
        $this->render("entry_register_preview", array(
            "title" => $this->getTitle("エントリ登録プレビュー"),
            "date" => $this->Manage->date(),
            "weather" => $this->Manage->weather()
        ));
    }
    
    /**
     * タグ登録画面を描画する
     */
    public function tagRegister() {
        $this->layout("base", $this->getContent(array(
            "template" => "tag_register",
            "header_template" => "header.manage",
            "title" => $this->getTitle("タグ登録")
        )));
    }
    
    /**
     * 登録画像一覧画面を描画する
     */
    public function imageList() {
        $this->layout("base", $this->getContent(array(
            "template" => "image_list",
            "header_template" => "header.manage",
            "title" => $this->getTitle("登録画像一覧"),
            "content" => array(
                "paginate" => $this->Manage->imageListPaginate($this->_page),
                "page" => $this->_page
            )
        )));
    }
    
    /**
     * 登録画像選択画面を描画する
     */
    public function uploadImageRegister() {
        $this->render("upload_image_register", array(
            "title" => $this->getTitle("アップロード済み画像選択"),
            "paginate" => $this->Manage->imageListPaginate($this->_page),
            "page" => $this->_page
        ));
    }
    
    /**
     * Amazon商品検索画面を描画する
     */
    public function amazonSearch() {
        $this->render("amazon_search", array(
            "title" => $this->getTitle("Amazon商品検索"),
        ));
    }
    
    /**
     * エントリ登録確認画面を表示する
     */
    public function confirm() {
        $entry = array(
            "title" => $this->request->post("title"),
            "description" => $this->request->post("description"),
            "tags" => $this->request->post("tags"),
            "tagNames" => $this->request->post("tag_names")
        );
        // エントリ登録に必要なデータをチェック
        $this->Manage->checkEntryData($entry);
        // 本文をHTMLに変換する
        $entry["description_html"] = $this->Manage->bb2html($entry["description"]);
                
        $this->layout("base", $this->getContent(array(
            "template" => "entry_register_confirm",
            "header_template" => "header.manage",
            "title" => $this->getTitle("エントリ登録確認"),
            "content" => $entry
        )));
    }
    
    /**
     * エントリ内容を差し戻す
     */
    public function remand() {
        // エントリ内容を一時保存する
        $this->Manage->remandSave(array(
            "title" => $this->request->post("title"),
            "description" => $this->request->post("description"),
            "tags" => $this->request->post("tags")
        ));
        // エントリ登録画面へリダイレクト
        $this->redirect("/diarysys5/entry_register");
    }
    
    /**
     * エントリを登録する
     */
    public function register() {
        // エントリ内容を保存する
        $this->Manage->register($this->request->post("title"),
                                $this->request->post("description"),
                                $this->request->post("tags"),
                                $this->request->post("date"),
                                $this->request->post("weather"),
                                $this->session->get("user_id"));
        
        $this->layout("base", $this->getContent(array(
            "template" => "entry_register_result",
            "header_template" => "header.manage",
            "title" => $this->getTitle("エントリ登録結果"),
            "content" => array()
        )));
    }
    
    /**
     * エントリ削除確認画面を描画する
     */
    public function deleteConfirm($id) {
        // エントリ内容を取得する
    }

    /**
     * エントリを削除する
     */
    public function delete() {
        $id = $this->request->post("entry_id");
        
        
        
        
    }
    
    /**
     * エントリを編集する
     */
    public function edit() {
        $id = $this->request->post("entry_id");
        var_dump($id);
    }


}
