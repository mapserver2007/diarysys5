<?php
namespace WebStream\Test;
use WebStream\Database;
/**
 * Databaseクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/10
 */
require_once 'UnitTestBase.php';

class DatabaseTest extends UnitTestBase {
    private $db;
    
    public function setUp() {
        parent::setUp();
        // ログ出力ディレクトリ、ログレベルをテスト用に変更
        $class = new \ReflectionClass("WebStream\Database");
        $property = $class->getProperty("config_path");
        $property->setAccessible(true);
        $property->setValue($class, $this->config_path_mysql);
        $method = $class->getMethod("manager");
        $this->db = $method->invoke(null);
        $this->db->create($this->create_sql);
    }
    
    public function tearDown() {
        $this->db->drop($this->drop_sql);
    }
    
    /**
     * 正常系
     * INSERTのテストを実行する
     */
    public function testOkInsert() {
        $result = $this->db->insert("INSERT INTO stream_test (name) values (:name)", array(
            "name" => "insert test"
        ));
        $this->assertTrue($result);
    }
    
    /**
     * 正常系
     * SELECTのテストを実行する
     */
    public function testOkSelect() {
        // INSERT
        $this->db->insert("INSERT INTO stream_test (name) values (:name)", array(
            "name" => "select test"
        ));
        // SELECT
        $result = $this->db->select("SELECT name FROM stream_test");
        $name = $result[0]["name"];
        $this->assertEquals("select test", $name);
    }
    
    /**
     * 正常系
     * UPDATEのテストを実行する
     */
    public function testOkUpdate() {
        // INSERT
        $this->db->insert("INSERT INTO stream_test (name) values (:name)", array(
            "name" => "insert test"
        ));
        // UPDATE
        $this->db->update("UPDATE stream_test SET name = :name", array(
            "name" => "update test"
        ));
        // SELECT
        $result = $this->db->select("SELECT name FROM stream_test");
        $name = $result[0]["name"];
        $this->assertEquals("update test", $name);
    }
    
    /**
     * 正常系
     * DELETEのテストを実行する
     */
    public function testOnDelete() {
        // INSERT
        $this->db->insert("INSERT INTO stream_test (name) values (:name)", array(
            "name" => "delete test"
        ));
        // DELETE
        $result = $this->db->delete("DELETE FROM stream_test");
        $this->assertTrue($result);
    }
}
