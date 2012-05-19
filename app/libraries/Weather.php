<?php
/**
 * 天気取得クラス
 * @author Ryuichi TANAKA.
 * @since 2012/03/27
 */
class Weather extends HttpAgent {
    /** 画像取得先URL */
    const URL = "http://weather.livedoor.com/forecast/webservice/rest/v1?city=63&day=today";
    /** キャッシュID */
    const CACHE_ID = "weather_cache";
    /** 画像のキャッシュ時間(秒) */
    const CACHE_TIME = 3600;
    
    /**
     * 天気画像を取得する
     * @return String 画像URL
     */
    public function getWeather() {
        $cache = new Cache();
        $data = $cache->get(self::CACHE_ID);
        if ($data === null) {
            $xml = simplexml_load_string($this->get(self::URL));
            if (parent::getStatusCode() === 200) {
                $list = preg_split("/\/|\./", $xml->image->url);
                $data = sprintf("/diarysys5/img/weather/%s.gif", $list[8]);
                $cache->save(self::CACHE_ID, $data, self::CACHE_TIME);
            }
        }
        return $data;
    }
}