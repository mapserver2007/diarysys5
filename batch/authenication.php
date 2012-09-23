<?php
/**
 * 認証用テーブルマイグレーションスクリプト
 * はてな認証＋diarysys5ログインユーザを登録する
 * 
 * @example
 *     $> php authentication.php -m init // DBを初期化
 *     $> php authentication.php -m add -u user_id -p user_password // ユーザ情報を登録
 *     $> php authentication.php -m edit -u user_id -p new_user_password // ユーザ情報を変更
 * 
 * @author Ryuichi TANAKA.
 * @since 2012/03/12
 */
namespace WebStream;
 
require_once '../core/AutoImport.php';
require_once '../core/Functions.php';
importAll("core");

// ログ出力ディレクトリ、ログレベルをテスト用に変更
Logger::init();
 
function init() {
    $create_sql = <<< CREATE_SQL
CREATE TABLE authentication (
    id int AUTO_INCREMENT PRIMARY KEY,
    user_id varchar(32) NOT NULL UNIQUE,
    token VARCHAR(64) NOT NULL UNIQUE
);
CREATE_SQL;

    $drop_sql = <<< DROP_SQL
DROP TABLE authentication;
DROP_SQL;
    
    $db = Database::manager();
    $db->drop($drop_sql);
    echo "[INFO]\tDROP TABLE\n";
    $db->create($create_sql);
    echo "[INFO]\tCREATE TABLE\n";
}

function register($user_id, $password) {
    $config = Utility::parseConfig("config/auth.ini");
    $salt = $config["salt"];
    $token = sha1("${user_id}--${password}--${salt}");
    $sql = "INSERT INTO AUTHENTICATION (user_id, token) VALUES (:user_id, :token)";
    $bind = array(
        "user_id" => $user_id,
        "token" => $token
    );
    
    $db = Database::manager();
    return $db->insert($sql, $bind);
}

function update($user_id, $password) {
    $db = Database::manager();
    $sql = "SELECT id FROM AUTHENTICATION WHERE user_id = :user_id";
    $bind = array("user_id" => $user_id);
    $result = $db->select($sql, $bind);
    $id = $result[0]["id"];
    
    if (empty($id)) return false;
    
    $config = Utility::parseConfig("config/auth.ini");
    $salt = $config["salt"];
    $token = sha1("${user_id}--${password}--${salt}");
    
    $sql = "UPDATE AUTHENTICATION SET token = :token WHERE id = :id";
    $bind = array(
        "token" => $token,
        "id" => $id
    );
    
    return $db->update($sql, $bind);
}

$command = $argv[1];
$mode = $argv[2];
if ($command === "-m" && $mode === "init") {
    init();
}
else if ($command === "-m" && $mode === "add") {
    $command1 = $argv[3];
    $command2 = $argv[5];
    $user_id = $argv[4];
    $password = $argv[6];
    if ($command1 === "-u" && $command2 === "-p") {
        if (register($user_id, $password)) {
            echo "[INFO]\tREGISTER SUCCESS\n";
        }
        else {
            echo "[INFO]\tREGISTER FAILURE\n";
        }
    }
    else {
        echo "[ERROR]\tInvalid command\n";
    }
}
else if ($command === "-m" && $mode === "edit") {
    $command1 = $argv[3];
    $command2 = $argv[5];
    $user_id = $argv[4];
    $password = $argv[6];
    if ($command1 === "-u" && $command2 === "-p") {
        if (update($user_id, $password)) {
            echo "[INFO]\tUPDATE SUCCESS\n";
        }
        else {
            echo "[INFO]\tUPDATE FAILURE\n";
        }
    }
    else {
        echo "[ERROR]\tInvalid command\n";
    }
}
else {
    echo "[ERROR]\tInvalid command\n";
}
