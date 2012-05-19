<?php
/**
 * Amazonライブラリ
 * [pear install Services_Amazon-0.9.0]
 * @author Ryuichi TANAKA.
 * @since 2012/04/15
 */
class Amazon extends HttpAgent {
    /** アクセスキー */
    private $access_key;
    /** シークレットキー */
    private $secret_access_key;
    /** アソシエイトID */
    private $associate_id;
    
    /**
     * コンストラクタ
     * @param String アクセスキー
     * @param String シークレットアクセスキー
     * @param String アソシエイトID
     */
    public function __construct($access_key, $secret_access_key, $associate_id) {
        parent::__construct(array(
            "titmeout" => 10
        ));
        $this->access_key = $access_key;
        $this->secret_access_key = $secret_access_key;
        $this->associate_id = $associate_id;
    }
    
    /**
     * Amazonから商品を検索する
     * @param String キーワード
     * @return Hash 商品データ
     */
    public function search($keyword) {
        $cache = new Cache();
        $items = $cache->get(Constants::AMAZON_CACHE_ID);
        if ($items === null) {
            $url = $this->getRequestURL($keyword);
            $xml = simplexml_load_string($this->get($url));
            if (parent::getStatusCode() === 200) {
                $items = Utility::xml2array($xml);
                $cache->save(Constants::AMAZON_CACHE_ID,
                             $items,
                             Constants::AMAZON_CACHE_TIME);
            }
        }
        return $items;
    }
    
    /**
     * Product Advertising APIのURLを取得する
     * @param String キーワード
     * @return String URL
     */
    private function getRequestURL($keyword) {
        $params = array(
            'Service' => 'AWSECommerceService',
            'Operation' => 'ItemSearch',
            'AWSAccessKeyId' => $this->access_key,
            'AssociateTag' => $this->associate_id,
            'ResponseGroup' => 'ItemAttributes,Images',
            'SearchIndex' => 'Books',
            'Keywords' => rawurlencode($keyword),
            'secret_key' => $this->secret_access_key
        );
        $q = array();
        foreach ($params as $key => $value) {
            $q[] = "${key}=${value}";
        }
        $url = Constants::PRODUCT_ADVERTISING_API . "?" . implode("&", $q);
        $url_array = parse_url($url);
        parse_str($url_array['query'], $param_array);
        $param_array['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
        ksort($param_array);
        $str = sprintf("GET\n%s\n%s\n", $url_array['host'], $url_array['path']);
        $str_param = '';
        while(list($key, $value) = each($param_array)) {
            $str_param .= sprintf('%s=%s&', strtr($key, '_', '.'), rawurlencode($value));
        }
        $str .= substr($str_param, 0, strlen($str_param) - 1);
        $signature = base64_encode(hash_hmac('sha256', $str, $params['secret_key'], true));
        $url_sig = sprintf('%s://%s?%sSignature=%s',
                           $url_array['scheme'],
                           $url_array['host'] . $url_array['path'],
                           $str_param,
                           rawurlencode($signature));
        
        return $url_sig;
    }
}
