<?php
/**
 * 認証クラス
 * <pre>
 * はてな認証API(http://auth.hatena.ne.jp/)を使用。
 * 利用に際しては認証APIにアプリケーション登録が必要になる。
 * </pre>
 * @author Ryuichi TANAKA.
 * @since 2010/08/21
 */
class Auth {
    const URL = "http://auth.hatena.ne.jp";
    const AUTH_PATH = "/auth";
    const JSON_PATH = "/api/auth.json";

    private $api_key;
    private $secret;

    /**
     * コンストラクタ
     * @param String APIキー
     * @param String 秘密鍵
     */
    function __construct($api_key, $secret) {
        $this->api_key = $api_key;
        $this->secret = $secret;
    }

    /**
     * はてな認証用URLを返却する
     * @return String 認証URL
     */
    public function getAuthURL() {
        return self::URL . self::AUTH_PATH . $this->getQueryString(
            array("api_key" => $this->api_key)
        );
    }

    /**
     * 認証後に返ってくるJSONからユーザIDを抽出し返却する
     * @param String CERTキー
     * @return String ユーザID
     */
    public function getUserID($cert) {
        $url = self::URL . self::JSON_PATH;
        $url.= $this->getQueryString(
            array("api_key" => $this->api_key, "cert" => $cert)
        );
        $json = file_get_contents($url);
        $hash = json_decode($json, true);
        return $hash["user"]["name"];
    }

    /**
     * クエリストリングを返却する
     * @param Array リクエストハッシュ
     * @return String クエリストリング
     */
    private function getQueryString($request) {
        $query = array_merge(
            $request,
            array(
                "api_sig" => $this->getSigneture($request)
            )
        );
        return $this->createQueryString($query);
    }

    /**
     * シグネチャを返却する
     * @params Array シグネチャ生成に使用するハッシュ
     * @return String シグネチャ
     */
    private function getSigneture($args) {
        $sig = $this->secret;
        $keys = array_keys($args);
        sort($keys);
        foreach ($keys as $key) {
            $sig .= $key . $args[$key];
        }
        return md5($sig);
    }

    /**
     * クエリストリングを生成する
     * @param Array クエリハッシュ
     * @return String クエリストリング
     */
    private function createQueryString($query) {
        $q = array();
        foreach ($query as $key => $value) {
            $q[] = "${key}=${value}";
        }
        return "?" . implode("&", $q);
    }
}
