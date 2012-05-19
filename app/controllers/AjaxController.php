<?php
/**
 * Ajax系処理
 * @author Ryuichi TANAKA.
 * @since 2011/11/01
 */
class AjaxController extends AppController {
    /**
     * ひとりごと(Tweet)の内容を返却する
     */
    public function hitorigoto() {
        $this->render("hitorigoto", array(
            "tweet" => $this->Ajax->tweet()
        ));
    }
    
    /**
     * タグ一覧を取得する
     */
    public function tagList() {
        $this->render("tag_list", array(
            "tags" => $this->Ajax->tagList()
        ));
    }
    
    /**
     * タグを登録する
     * @param Hash リクエストパラメータ
     */
    public function tagRegister($params) {
        $this->isLoggedIn();
        $this->isReferer();
        $this->render_json($this->Ajax->tagRegister($params["name"]));
    }
    
    /**
     * 使用されていないタグを全て削除する
     */
    public function tagDelete() {
        $this->isLoggedIn();
        $this->isReferer();
        $this->render_json($this->Ajax->tagDelete());
    }
    
    /**
     * 登録済み画像一覧を取得する
     * @params Hash リクエストパラメータ
     */
    public function imageList($params) {
        $this->isLoggedIn();
        $this->isReferer();
        $this->render_json($this->Ajax->imageList($params["page"]));
    }
    
    /**
     * Amazon商品検索結果を描画する
     * @params Hash リクエストパラメータ
     */
    public function amazonItem($params) {
        $this->render("amazon_item", array(
            "items" => $this->Ajax->amazonItem($params["keyword"])
        ));
    }
    
    /**
     * エントリ内容を一時保存する
     */
    public function temporarySave() {
        return $this->render_json(array(
            "result" => $this->Ajax->temporarySave(array(
                "title" => $this->request->post("title"),
                "description" => $this->request->post("description"),
                "tags" => $this->request->post("tags")
            ))
        ));
    }
    
    /**
     * エントリ内容の一時保存取得する
     */
    public function temporaryLoad() {
        return $this->render_json(
            $this->Ajax->temporaryLoad()
        );
    }
}
