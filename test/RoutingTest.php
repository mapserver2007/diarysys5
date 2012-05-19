<?php
/**
 * diarysys5のルーティングに関するテスト
 * @author Ryuichi TANAKA.
 * @since 2012/01/16
 */
require_once 'DataProvider.php';

class RoutingTest extends DataProvider {
    
    public function setUp() {
        parent::setUp();
    }
    
    /**
     * 正常系
     * 指定したURLに対して指定したステータスコードを取得できること
     * @dataProvider urlListProvider
     */
    public function testOkRouting($path, $statusCode) {
        $url = $this->root_url . $path;
        $http = new HttpAgent();
        $html = $http->get($url);
        $this->assertTrue(!empty($html));
        $this->assertEquals($http->getStatusCode(), $statusCode);
    }
    
}
