<?php
namespace WebStream;
/**
 * DataMapperクラス
 * @author Ryuichi TANAKA.
 * @since 2012/09/04
 */
class DataMapper {
    /** キャッシュデータ */
    private $data = array();
    /** DBアクセサ */
    private $db;
    /** カラム情報 */
    private $columns;
    /** テーブル名のリスト */
    private $tables;
    
    /**
     * コンストラクタ
     * @param Object DBアクセサインスタンス
     * @param Array カラム情報
     * @param Array テーブル名リスト
     */
    private function __construct($db, $columns, $tables) {
        $this->db = $db;
        $this->columns = $columns;
        $this->tables = $tables;
    }
    
    /**
     * マッピングメソッドが呼ばれたときの処理
     * @param String メソッド名
     * @param Array 引数のリスト
     * @return mixed 実行結果
     */
    public function __call($method, $arguments) {
        $result = array();
        foreach ($this->tables as $table) {
            if (!array_key_exists($table, $this->data)) {
                $this->data[$table] = $this->getStoredData($method, $arguments, $table);
            }
            $result = array_merge($result, $this->data[$table]);
        }
        return $result;
    }
    
    /**
     * メソッド名とカラムをマッピングして結果を返却する
     * @param String メソッド名
     * @param Array 引数のリスト
     * @param String テーブル名
     * @return Array selectの実行結果
     */
    private function getStoredData($method, $arguments, $table) {
        $snakeMethod = Utility::camel2snake($method);
        $columnName = null;
        $columns = $this->columns[$table];
        if ($columns != null) {
            foreach ($columns as $column) {
                if ($column["name"] === $snakeMethod) {
                    $columnName = $snakeMethod;
                    break;
                }
            }
        }
        
        if ($columnName === null) {
            $className = get_class($this);
            $errorMsg = "Column '$snakeMethod' is not found in Table '$table.' " .
                        "$className#$method mapping failed.";
            throw new MethodNotFoundException($errorMsg);
        }
        
        $sql = sprintf("SELECT %s FROM %s", $columnName, $table);
        $bind = array();
        $limitSql = "LIMIT :limit OFFSET :offset";
        if (count($arguments) == 1 && is_int($arguments[0])) {
            $bind["limit"] = $arguments[0];
            $bind["offset"] = 0;
            $sql .= " " . $limitSql;
        }
        else if (count($arguments) == 2 && is_int($arguments[0]) && is_int($arguments[1])) {
            $bind["limit"] = $arguments[1];
            $bind["offset"] = $arguments[0];
            $sql .= " " . $limitSql;
        }
        
        return $this->db->select($sql, $bind);
    }
    
    /**
     * DataMapperインスタンスを取得する
     * @param Object DBアクセサインスタンス
     * @param Array カラム情報
     * @param Array テーブル名リスト
     * @return Object DataMapperインスタンス
     */
    public static function get($dbAccessor, $columns, $tables) {
        self::validate($dbAccessor, $columns, $tables);
        return new DataMapper($dbAccessor, $columns, $tables);
    }

    /**
     * バリデーション
     * @param Object DBアクセサインスタンス
     * @param Array カラム情報
     * @param Array テーブル名リスト
     */
    private static function validate($dbAccessor, $columns, $tables) {
        if (!$dbAccessor instanceof Database) {
            throw new DatabaseException("Invalid database accessor object.");
        }
        foreach ($tables as $table) {
            if (!array_key_exists($table, $columns)) {
                throw new DatabaseException("Undefined table: ${table}");
            }
        }
    }
}
