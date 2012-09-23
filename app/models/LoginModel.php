<?php
namespace WebStream;
/**
 * ログインモデル
 * @Inject
 * @Database("diarysys")
 * @Table("AUTHENTICATION")
 * @Properties("sql/login.properties")
 * @author Ryuichi TANAKA.
 * @since 2012/03/21
 */
class LoginModel extends CoreModel {
    /**
     * 指定したユーザが存在するかどうか
     * @Inject
     * @SQL("login.exist_user")
     * @param String ユーザID
     * @return Boolean 存在するかどうか
     */
    public function isExistUser($user_id) {
        $result = $this->select(array("user_id" => $user_id));
        return count($result) === 1;
    }
    
    /**
     * 指定したユーザは正しいかどうか
     * @Inject
     * @SQL("login.valid_user")
     * @param String ユーザID
     * @param String 認証トークン
     * @return Boolean 正しいかどうか
     */
    public function isValidUser($user_id, $token) {
        $result = $this->select(array("user_id" => $user_id, "token" => $token));
        return count($result) === 1;
    }
}
