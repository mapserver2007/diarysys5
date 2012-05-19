<?php
/**
 * ログインコントローラ
 * @author Ryuichi TANAKA.
 * @since 2012/03/20
 */
class LoginController extends AppController {
    /**
     * before filter
     */
    public function before() {
        $this->isReferer();
        parent::before();
    }
    
    /**
     * 認証を実行する
     */
    public function auth() {
        // すでに認証済みの場合、直接ログイン画面へ遷移
        $user_id = $this->session->get("user_id");
        if (isset($user_hash)) {
            Logger::info("Already authenticated: " . $user_id);
            $this->redirect("/diarysys5/login");
        }
        // 未認証の場合、認証処理を実行する
        else {
            Logger::info("Execute authenticate.");
            $auth_url = $this->Login->getAuthURL();
            $this->redirect($auth_url);
        }
    }
    
    /**
     * はてな認証からのコールバックを受信し、ログイン画面または
     * ログインエラー画面へ遷移する
     */
    public function authBack() {
        // はてなユーザIDを取得
        $user_id = $this->Login->getUserID($this->request->get("cert"));
        // ユーザが登録されている場合、ログイン画面へ遷移
        if (isset($user_id)) {
            // セッションに保存
            $this->session->set("user_id", $user_id);
            // ログイン画面へ遷移
            $this->redirect("/diarysys5/login");
        }
        // ユーザが登録されていない場合、エラー画面へ遷移
        else {
            $this->redirect("/diarysys5/login_failure");
        }
    }
    
    /**
     * ログイン処理を実行する
     */
    public function login() {
        $user_id = $this->request->post("user_id");
        $password = $this->request->post("password");
        // 認証処理
        if (isset($user_id) && isset($password)) {
            // ログイン成功
            if ($this->Login->execLogin($user_id, $password)) {
                // 再度セッションにユーザIDを保存
                $this->session->set("user_id", $user_id);
                Logger::info("Login success: " . $user_id);
                $this->render_loggedin();
            }
            // ログイン失敗
            else {
                // ログイン失敗時にそのままテンプレートを描画しないでリダイレクトする
                // そのまま描画するとログイン失敗にもかかわらずURLが「/login」となりURLが不自然なため
                $this->redirect("/diarysys5/login_error");
            }
        }
        // ログイン画面描画
        else {
            $this->render_login();
            // 一旦ユーザIDをセッションから削除する。
            // ログイン画面から戻るボタンが押されたときにセッションに残るため。
            $this->session->delete("user_id");
        }
    }
    
    /**
     * ログアウト処理を実行する
     */
    public function logout() {
        $user_id = $this->session->get("user_id");
        Logger::info("Logout success: " . $user_id);
        $this->session->delete("user_id");
        $this->render_logout();
    }
    
    /**
     * ログインエラー画面
     */
    public function error() {
        Logger::error("Login failure.");
        $this->session->delete("user_id");
        $this->layout("base.login", array(
            "template" => "failure",
            "title" => $this->getTitle("ログインエラー"),
            "content" => array()
        ));
    }
    
    /**
     * ログイン画面を描画する
     */
    private function render_login() {
        $this->layout("base.login", array(
            "template" => "login",
            "title" => $this->getTitle("ログイン"),
            "content" => array(
                "user_id" => $this->session->get("user_id")
            )
        ));
    }
    
    /**
     * ログイン成功画面を描画する
     */
    private function render_loggedin() {
        $this->layout("base.login", array(
            "template" => "success",
            "title" => $this->getTitle("ログイン"),
            "content" => array()
        ));
    }
    
    /**
     * ログアウト成功画面を描画する
     */
    private function render_logout() {
        $this->layout("base.login", array(
            "template" => "logout",
            "title" => $this->getTitle("ログアウト"),
            "content" => array()
        ));
    }
}
