<?php
/**
 * BBコードをHTMLに置換するクラス
 * @author Ryuichi TANAKA.
 * @since 2011/11/06
 */
class BBCode {
    /** 変換パターン */
    private $bbcode = array();
    
    /**
     * HTMLに変換する
     * @param String BBコード
     * @return String HTML
     */
    public static function convHTML($str) {
        $bb = new BBCode();
        $bbcode = $bb->getBBCode();
        foreach($bbcode as $codes){
            $str = preg_replace($codes["pattern"], $codes["replacement"], $str);
        }
        return $str;
    }
    
    /**
     * コンストラクタ
     */
    public function __construct() {
        $this->regexp();
    }
    
    /**
     * BBコード変換パターンを返却する
     * @return Hash 変換パターン
     */
    public function getBBCode() {
        return $this->bbcode;
    }
    
    /**
     * 変換パターンセットする
     */
    private function regexp() {
        //[color]
        $this->bbcode["color"] = array(
            "pattern" => '/\[color\=\#([0-9a-fA-F]{3})\](.+)\[\/color\]/',
            "replacement" => '<span style="color: #$1;">$2</span>'
        );
        //[image]
        $this->bbcode["image1"] = array(
            "pattern" => '/\[image\=(https?:\/\/[\-\_\.\!\~\*\'\(\)a-zA-Z0-9\;\/\?\:\@\&\=\+\$,\%\#]+\.(gif|jpe?g|png|bmp))\](.+?)\[\/image\]/',
            "replacement" => '<a href="$1" class="fancybox"><img src="$1" alt="$3" title="$3" class="pic2" /></a>'
        );
        //[image]
        $this->bbcode["image2"] = array(
            "pattern" => '/\[image\](https?:\/\/[\-\_\.\!\~\*\'\(\)a-zA-Z0-9\;\/\?\:\@\&\=\+\$,\%\#]+\.(gif|jpe?g|png|bmp))\[\/image\]/',
            "replacement" => '<a href="$1" class="fancybox"><img src="$1" alt="$1" title="$1" class="pic2" /></a>'
        );
        //[upload]
        $this->bbcode["upload"] = array(
            "pattern" => '/\[upload\]((https?:\/\/[\-\_\.\!\~\*\'\(\)a-zA-Z0-9\;\/\?\:\@\&\=\+\$,\%\#]+)(\/small)(.+))\[\/upload\]/',
            "replacement" => '<a href="$2$4" class="fancybox"><img src="$1" alt="$1" title="$1" class="pic2" /></a>'
        );
        //[url]
        $this->bbcode["url1"] = array(
            "pattern" => '/\[url\=(https?:\/\/[\-\_\.\!\~\*\'\(\)a-zA-Z0-9\;\/\?\:\@\&\=\+\$,\%\#]+)\](.+?)\[\/url\]/',
            "replacement" => '<a href="$1">$2</a>'
        );
        //[url]
        $this->bbcode["url2"] = array(
            "pattern" => '/\[url\](https?:\/\/[\-\_\.\!\~\*\'\(\)a-zA-Z0-9\;\/\?\:\@\&\=\+\$,\%\#]+)\[\/url\]/',
            "replacement" => '<a href="$1">$1</a>'
        );
        //[code]
        $this->bbcode["code"] = array(
            "pattern" => '/\[code\](.+?)\[\/code\]/s',
            "replacement" => '<pre class="prettyprint"><core>$1</code></pre>'
        );
        //[left]
        $this->bbcode["left"] = array(
            "pattern" => '/\[left\](.+?)\[\/left\]/',
            "replacement" => '<p style="text-align: left;">$1</p>'
        );
        //[center]
        $this->bbcode["center"] = array(
            "pattern" => '/\[center\](.+?)\[\/center\]/',
            "replacement" => '<p style="text-align: center;">$1</p>'
        );
        //[right]
        $this->bbcode["right"] = array(
            "pattern" => '/\[right\](.+?)\[\/right\]/',
            "replacement" => '<p style="text-align: right;">$1</p>'
        );
        //[amazon]
        $this->bbcode["amazon"] = array(
            "pattern" => "/\[amazon\=(https?:\/\/.*?)\](.+?)\|(.+?)\|(.+?)\|(.+?)\|(.+?)\|(.+?)\|(.+?)\[\/amazon\]/",
            "replacement" => '<a href="$1"><div class="qt_amazon"><div class="amazon_l"><img src="$2"/></div><div class="amazon_r">$3<br/>$4</br/>$5</br/>$6</br/>$7</br/>$8</br/></div><div class="clearfix"></div></div></a>'            
        );
        //Others
        $this->bbcode["others"] = array(
            "pattern" => '/\[(.+?)\](.+?)\[\/(.+?)\]/',
            "replacement" => '<$1>$2</$3>'
        );
    }
}
