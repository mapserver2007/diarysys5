<?php
namespace WebStream;
/**
 * Ajaxヘルパー
 * @author Ryuichi TANAKA.
 * @since 2012/05/03
 */
class AjaxHelper {
    /**
     * Amazon商品検索結果を表示
     * @param Array 商品データ
     */
    public function amazonItem($items) {
        $html = "";
        if (!empty($items)) {
            foreach ($items as $item) {
                $html .= <<< AMAZON_ITEM
<li>
    <div class="amazon_item">
        <img src="${item['image']}" />
        <div class="item_info">
            <div class="image">${item["image"]}</div>
            <div class="author">${item["author"]}</div>
            <div class="price">${item["price"]}</div>
            <div class="isbn">${item["ISBN"]}</div>
            <div class="publisher">${item["publisher"]}</div>
            <div class="title">${item["title"]}</div>
            <div class="date">${item["date"]}</div>
            <div class="url">${item["url"]}</div>
        </div>
    </div>
</li>
AMAZON_ITEM;
            }
            $html = "<ul>${html}</ul>";
        }
        else {
            $html = "<div class=\"item_not_found\"><h3>Item not found.</h3></div>";
        }
        return $html;
    }
}
