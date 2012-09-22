<?php
namespace WebStream;
/**
 * 管理ヘルパー
 * @author Ryuichi TANAKA.
 * @since 2012/05/02
 */
class ManageHelper {
    /**
     * タグ名を展開する
     * @return String 展開したタグ名の文字列
     */
    public function showTags($tags) {
        return implode(" ", $tags);
    }
    
    /**
     * タグIDをinputタグにセットする
     * @param Array タグIDリスト
     * @return String HTML
     */
    public function setTags($tags) {
        $html = "";
        foreach ($tags as $tagId) {
            $html .= "<input type=\"hidden\" name=\"tags[]\" value=\"${tagId}\" />";
        }
        return $html;
    }

    /**
     * 天気IDを返却する
     * @param String 天気画像パス
     * @return Integer 天気ID
     */
    public function weatherId($src) {
        if (preg_match("/.*\/(\d+)/", $src, $matches)) {
            return $matches[1];
        }
    }
    
    public function entryList($entries) {
        $html = "";
        foreach ($entries as $entry) {
            $tag = implode(", ", $entry["TAG"]);
            $html .= <<< ENTRY_LIST2
        <tr>
            <td>${entry["DATE"]}</td>
            <td>${entry["TITLE"]}</td>
            <td>${tag}</td>
            <td>
                <a href="/diarysys5/delete" class="delete_button">
                    <img src="/diarysys5/img/delete.png"/>
                    <div class="entry_id">${entry["ID"]}</div>
                </a>
            </td>
            <td>
                <a href="/diarysys5/edit" class="edit_button">
                    <img src="/diarysys5/img/edit.png"/>
                    <div class="entry_id">${entry["ID"]}</div>
                </a>
            </td>
        </tr>
ENTRY_LIST2;
        }
        
        return $html;
    }
    
    /**
     * エントリ登録画面のクライアント処理を実行
     * @param Object 差し戻しエントリデータ
     */
    public function entryRegister($entry) {
        $json = json_encode($entry);
        return <<< SCRIPT
<script type="text/javascript">
    var obj = entryRegister();
    obj.subHeader(1);
    obj.onRealtimePreview();
    obj.caution();
    obj.tag(function() {
        if ($json) {
            obj.remandLoad($json);
        }
    });
</script>
SCRIPT;
    }
}
