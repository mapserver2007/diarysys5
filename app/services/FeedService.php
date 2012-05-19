<?php
/**
 * フィードサービス
 * @author Ryuichi TANAKA.
 * @since 2012/02/18
 */
class FeedService extends CoreService {
    /**
     * フィードに表示するエントリ内容を取得する
     * @return Hash エントリデータ
     */
    public function contents() {
        $paginate = Utility::parseConfig("config/paginate.ini");
        $message = Utility::parseConfig("config/locales/ja.ini");
        $entries = $this->Feed->entry(0, $paginate["display"]);
        $tags = $this->tags();
        $items = array();
        foreach ($entries as $entry) {
            $entry["TAG"] = $tags[$entry["ID"]];
            $entry["DESCRIPTION"] = "<![CDATA[" . BBCode::convHTML($entry["DESCRIPTION"]) . "]]>";
            $items[] = array(
                "title" => $entry["TITLE"],
                "link" => $message['rss_link'] . "enntry/${entry['ID']}",
                "description" => $entry["DESCRIPTION"],
                "dc:subject" => implode(", ", $entry["TAG"]),
                "dc:creator" => $message["author"],
                "dc:date" => $entry["DATE"]
            );
        }
        
        return $items;
    }
    
    /**
     * タグを取得する
     * @return Hash タグ一覧
     */
    private function tags() {
        $tags = $this->Feed->tag();
        $entry_tags = array();
        // タグIDごとにまとめる
        foreach ($tags as $tag) {
            if (!array_key_exists($tag["ID"], $entry_tags)) {
                $entry_tags[$tag["ID"]] = array();
            }
            $entry_tags[$tag["ID"]][] .= $tag["NAME"];
        }
        return $entry_tags;
    }
}
