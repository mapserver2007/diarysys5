<?php
namespace WebStream;
/**
 * Ajaxモデル
 * @Inject
 * @Database("diarysys")
 * @Table("TAG", "TAG_MASTER")
 * @Properties("sql/ajax.properties")
 * @author Ryuichi TANAKA.
 * @since 2012/03/30
 */
class AjaxModel extends CoreModel {
    /**
     * 登録済みタグ一覧を取得する
     * @Inject
     * @SQL("ajax.tag_list")
     * @return Hash タグ一覧
     */
    public function tagList() {
        return $this->select();
    }
    
    /**
     * タグを登録する
     * @Inject
     * @SQL("ajax.tag_register")
     * @param String タグ名
     * @return Boolean 結果
     */
    public function tagRegister($name) {
        return $this->insert(array("name" => $name));
    }
    
    /**
     * タグを削除する
     * @Inject
     * @SQL("ajax.tag_delete")
     * @return Boolean 結果
     */
    public function tagDelete() {
        return $this->delete();
    }
}
