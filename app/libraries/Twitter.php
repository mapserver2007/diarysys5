<?php
namespace WebStream;
/**
 * Twitterライブラリ
 * @author Ryuichi TANAKA.
 * @since 2012/04/05
 * 以下の準備が必要
 * HTTP_OAuth       0.2.3   alpha
 * HTTP_Request2    2.0.0   stable
 * Net_URL2         2.0.0   stable
 */
require_once 'HTTP/OAuth/Consumer.php';

class Twitter {
    const API_URL = "http://api.twitter.com/1/statuses/user_timeline.xml";
    const STATUS_URL = "http://twitter.com/%s/status/%s";
    const ICON_URL = "https://api.twitter.com/1/users/profile_image?screen_name=%s&size=bigger";
    
    /** キャッシュID */
    const CACHE_ID = "twitter_cache";
    /** 画像のキャッシュ時間(秒) */
    const CACHE_TIME = 300;
    
    /** Twitter OAuthキー */
    private $consumer_key;
    private $consumer_secret;
    private $access_token;
    private $access_token_secret;
    
    /**
     * コンストラクタ
     * @param String コンシューマキー
     * @param String コンシューマシークレットキー
     * @param String アクセストークン
     * @param String アクセストークンシークレットキー
     */
    public function __construct($consumer_key,
                                $consumer_secret,
                                $access_token,
                                $access_token_secret) {
        $this->consumer_key = $consumer_key;
        $this->consumer_secret = $consumer_secret;
        $this->access_token = $access_token;
        $this->access_token_secret = $access_token_secret;
    }
               
    /**
     * OAuth認証しインスタンスを返却する
     * @return Object OAuthオブジェクト
     */                 
    private function auth() {
        $consumer = new \HTTP_OAuth_Consumer(
            $this->consumer_key,
            $this->consumer_secret
        );
        $consumer->setToken($this->access_token);
        $consumer->setTokenSecret($this->access_token_secret);
        return $consumer;
    }
    
    /**
     * 最新のTweetを取得する
     * @return String 最新のTweet
     */
    public function getTweet() {
        $cache = new Cache();
        $data = $cache->get(self::CACHE_ID);
        if ($data === null) {
            $obj = $this->auth();
            $response = simplexml_load_string($obj->sendRequest(
                self::API_URL,
                array("count" => 1),
                "GET"
            )->getBody());
            
            $status = $response->status[0];
            $data = array(
                "text" => (string) $status->text,
                "created_at" =>  date("Y-m-d H:i:s", strtotime((string) $status->created_at)),
                "link" => sprintf(self::STATUS_URL, (string) $status->user->screen_name, $status->id),
                "icon" => sprintf(self::ICON_URL, (string) $status->user->screen_name)
            );
            
            $text = (string) $response->status[0]->text;
            Logger::info("Get tweet: ${data['text']} - ${data['created_at']}");
            $cache->save(self::CACHE_ID, $data, self::CACHE_TIME);
        }

        return $data;
    }
}
