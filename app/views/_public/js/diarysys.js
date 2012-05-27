/** ひとりごと */
function hitorigoto() {
    D.Hitorigoto.xhr();
}

/** エントリ一覧 */
function entryList() {
	D.EntryList.init();
}

/** エントリ表示画面 */
function entry() {
    D.PageLayout.plugin();
}

/** エントリ登録画面 */
function entryRegister() {
    return D.EntryRegister.mix(D.PageLayout, D.Tag).init();
}

/** エントリ登録確認画面 */
function entryConfirm() {
    return D.EntryConfirm.mix(D.PageLayout).init();
}

/** エントリ登録画面(差し戻し) */
function entryRemand(entry) {
    entryRegister().remandLoad(entry);
}

/** タグ登録画面 */
function tagRegister() {
    return D.Tag.mix(D.PageLayout).init();
}

/** 登録画像一覧 */
function imageList(page) {
    return D.ImageList.mix(D.PageLayout).init(page);
}

/** 登録済み画像検索 */
function onLoadUploadImage() {
    D.QuickTagsExtension.onLoadUploadImage();
}

/** Amazon商品検索 */
function onLoadAmazonSearch() {
    D.QuickTagsExtension.onLoadAmazonSearch();
}

/** エントリ登録画面 - 登録済み画像一覧 */
function registeredImageList(page) {
    return D.RegisteredImageList.init(page);
}

/** エントリ登録画面 - Amazon商品検索 */
function amazonItemList() {
    D.Amazon.init();
}
