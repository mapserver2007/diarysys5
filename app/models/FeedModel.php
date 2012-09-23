<?php
namespace WebStream;
/**
 * フィードモデル
 * @Inject
 * @Database("diarysys")
 * @Table("DIARY5, TAG")
 * @Properties("sql/feed.properties")
 * @author Ryuichi TANAKA.
 * @since 2012/02/07
 */
class FeedModel extends CoreModel {
    /**
     * エントリを取得する
     * @Inject
     * @SQL("feed.entry")
     * @param Integer 検索開始位置
     * @param Integer 取得件数
     * @return Hash エントリデータ
     */
    public function entry($offset, $limit) {
        return $this->select(array("offset" => $offset, "limit" => $limit));
    }
    
    /**
     * エントリのタグ情報を取得する
     * @Inject
     * @SQL("feed.tag")
     * @return Hash タグ情報
     */
    public function tag() {
        return $this->select();
    }
}
