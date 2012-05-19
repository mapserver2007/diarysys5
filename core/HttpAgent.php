<?php
/**
 * HTTPエージェントクラス
 * @author Ryuichi TANAKA.
 * @since 2010/10/01
 */
class HttpAgent {
    /** ステータスコード */
    private $status_code;
    /** コンテントタイプ */
    private $content_type;
    /** タイムアウト時間 */
    private $timeout;
    /** PROXYホスト名 */
    private $proxy_host;
    /** PROXYポート番号 */
    private $proxy_port;
    /** Basic認証ID */
    private $basic_auth_id;
    /** Basic認証パスワード */
    private $basic_auth_password;
    /** デフォルトタイムアウト時間 */
    const DEFAULT_TIMEOUT = 3;
    
    /**
     * コンストラクタ
     * @param Hash オプションパラメータ
     */
    public function __construct($options = array()) {
        $this->timeout             = $this->getOptionParameter($options, "timeout", self::DEFAULT_TIMEOUT);
        $this->proxy_host          = $this->getOptionParameter($options, "proxy_host");
        $this->proxy_port          = $this->getOptionParameter($options, "proxy_port");
        $this->basic_auth_id       = $this->getOptionParameter($options, "basic_auth_id");
        $this->basic_auth_password = $this->getOptionParameter($options, "basic_auth_password");
    }
    
    /**
     * ステータスコードを返却する
     * @return Integer ステータスコード
     */
    public function getStatusCode() {
        return $this->status_code;
    }
    
    /**
     * コンテントタイプを返却する
     * @return String コンテントタイプ
     */
    public function getContentType() {
        return $this->content_type;
    }
    
    /**
     * GETリクエストを発行する
     * @param String URL
     * @param Hash リクエストパラメータ
     * @param Hash リクエストヘッダ
     * @return String レスポンス
     */
    public function get($url, $params = array(), $headers = array()) {
        // GETの場合、$paramsは任意
        if (!empty($params)) {
            $params = http_build_query($params);
        }
        
        $method = strtoupper(__FUNCTION__);
        $request = array(
            "method"  => $method,
            "content" => $params,
            "timeout" => $this->timeout
        );
        
        return $this->http($url, $headers, $request, $method);
    }
    
    /**
     * POSTリクエストを発行する
     * @param String URL
     * @param Hash リクエストパラメータ
     * @param Hash リクエストヘッダ
     * @return String レスポンス
     */
    public function post($url, $params, $headers = array()) {
        // POSTの場合、$paramsは必須
        $params = http_build_query($params);
        
        if (empty($headers)) {
            $headers = array(
                "Content-Type: application/x-www-form-urlencoded",
                "Content-Length: " . strlen($params)
            );
        }
        
        $method = strtoupper(__FUNCTION__);
        $request = array(
            "method"  => $method,
            "content" => $params,
            "timeout" => $this->timeout
        );

        return $this->http($url, $headers, $request, $method);
    }
    
    /**
     * PROXY設定をする
     * @param Hash リクエストコンテント
     */
    private function proxy(&$request) {
        // 「http://」は「tcp://」に、「https://」は「ssl://」に置換する
        $host = $this->proxy_host;
        $host = preg_replace('/^http(:\/\/.+)$/', 'tcp${1}', $host);
        $host = preg_replace('/^https(:\/\/.+)$/', 'ssl${1}', $host);
        $request["proxy"] = $host . ":" . strval($this->proxy_port);
        $request["request_fulluri"] = true;
    }
    
    /**
     * Basic認証を設定する
     * @param Hash リクエストヘッダ
     */
    private function basicAuth(&$headers) {
        $headers[] = "Authorization: Basic " .
            base64_encode($this->basic_auth_id . ":" . $this->basic_auth_password);
    }
    
    /**
     * HTTP通信を実行する
     * @param String URL
     * @param Hash リクエストパラメータ
     * @param Hash リクエストヘッダ
     * @param String 実行するRESTメソッド
     * @return String レスポンス
     */
    private function http($url, $headers, $request, $method) {
        // Proxy設定
        if ($this->proxy_host && $this->proxy_port) {
            $this->proxy($request);
        }
        
        // Basic認証
        if ($this->basic_auth_id && $this->basic_auth_password) {
            $this->basicAuth($headers);
        }
        
        if (!empty($headers)) {
            $request["header"] = implode("\r\n", $headers);
        }
        
        // レスポンス
        $response = @file_get_contents($url, false, stream_context_create(array("http" => $request)));

        if (!isset($http_response_header)) {
            $hasHeader = @get_headers($url);
            // ヘッダを持たない場合、存在しないURL
            if ($hasHeader === false) {
                $this->status_code = 404;
                Logger::error("URL not found: " . $url);
            }
            // ヘッダを持つ場合はタイムアウトが発生
            else {
                $this->status_code = 408;
                Logger::error("Request timeout: " . $url);
            }
            return null;
        }
        
        // ヘッダ情報を取得
        foreach ($http_response_header as $value) {
            // Content-Type
            if (preg_match('/^Content-Type:\s.+/', $value)) {
                $this->content_type = $value;
            }
            // ステータスコード
            if (preg_match('/^HTTP\/.+\s([0-9]{3})/', $http_response_header[0], $matches)) {
                $this->status_code = intval($matches[1]);
            }
        }
        
        // HTTP通信の結果が200
        if ($this->status_code === 200) {
            Logger::info("HTTP {$method} success({$this->status_code}): {$url}");
            return $response;
        }
        // HTTP通信の結果が200以外
        else {
            if ($this->status_code === null) {
                $this->status_code = 500;
            }
            Logger::error("HTTP {$method} failure({$this->status_code}): {$url}");
            return null;
        }
    }

    /**
     * 配列から安全に値を取得する
     * @param Hash オプション配列
     * @param String 配列キー
     * @param String or Integer デフォルト値
     * @return String or Integer オプション配列の値
     */
    private function getOptionParameter($options, $key, $default_value = null) {
        return array_key_exists($key, $options) ? $options[$key] : $default_value;
    }
}
