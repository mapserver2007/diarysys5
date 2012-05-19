<?php
/**
 * ログインモデル
 * @author Ryuichi TANAKA.
 * @since 2012/03/21
 */
class LoginModel extends CoreModel {
    /**
     * 指定したユーザが存在するかどうか
     * @param String ユーザID
     * @return Boolean 存在するかどうか
     */
    public function isExistUser($user_id) {
        $sql = "SELECT id FROM authentication WHERE user_id = :user_id";
        $bind = array("user_id" => $user_id);
        $result = $this->db->select($sql, $bind);
        return count($result) === 1;
    }
    
    /**
     * 指定したユーザは正しいかどうか
     * @param String ユーザID
     * @param String 認証トークン
     * @return Boolean 正しいかどうか
     */
    public function isValidUser($user_id, $token) {
        $sql = "SELECT id FROM authentication WHERE user_id = :user_id ";
        $sql.= "AND token = :token";
        $bind = array(
            "user_id" => $user_id,
            "token" => $token
        );
        $result = $this->db->select($sql, $bind);
        return count($result) === 1;
    }
}
