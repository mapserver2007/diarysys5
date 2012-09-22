<?php
namespace WebStream;
/**
 * ログインサービス
 * @author Ryuichi TANAKA.
 * @since 2012/03/21
 */
class LoginService extends CoreService {
    /**
     * ログインする
     * @param String ユーザID
     * @param String パスワード
     * @return Boolean 認証結果
     */
    public function execLogin($user_id, $password) {
        $config = Utility::parseConfig("config/auth.ini");
        $salt = $config["salt"];
        $token = sha1("${user_id}--${password}--${salt}");
        return $this->Login->isValidUser($user_id, $token);
    }
    
    /**
     * 認証URLを取得する
     * @return String 認証URL
     */
    public function getAuthURL() {
        // 認証を実行
        $auth = $this->createAuth();
        $login_url = $auth->getAuthURL();
        Logger::info("Login URL: " . $login_url);
        return $login_url;
    }
    
    /**
     * 認証後に取得できるユーザIDを返却する
     * @param String 認証トークン
     * @return String ユーザID
     */
    public function getUserID($cert) {
        // 認証APIキーを取得
        $auth = $this->createAuth();
        $user_id = $auth->getUserID($cert);
        if ($user_id == "") {
            if (empty($cert)) $cert = "(empty)";
            throw new Exception("Invalid cert token value: " . $cert);
        }
        Logger::info("Hatena ID: " . $user_id);
        
        // はてなIDと同じユーザがDBに登録されているかどうか
        if ($this->Login->isExistUser($user_id)) {
            Logger::info("${user_id} is registered.");
            return $user_id;
        }
        else {
            Logger::error("${user_id} is not registered.");
            return null;
        }
    }
    
    /**
     * 認証を実行するためのインスタンスを返却する
     * @return Object 認証インスタンス
     */
    private function createAuth() {
        // 認証APIキーを取得
        $config = Utility::parseConfig("config/auth.ini");
        $api_key = $config["api_key"];
        $secret_key = $config["secret_key"];
        return new Auth($api_key, $secret_key);
    }
}
