<?php
/**
 * Paginateライブラリ
 * @author Ryuichi TANAKA.
 * @since 2012/04/14
 */
class Constants {
    /** ファイルアップロード画像(オリジナル)ディレクトリパス */
    const UPLOAD_DIR = "/app/views/_public/img/upload";
    /** ファイルアップロード画像(縮小サイズ)ディレクトリパス */
    const SMALL_DIR = "/app/views/_public/img/upload/small";
    /** ファイルアップロード画像(サムネイル)ディレクトリパス */
    const THUMBNAIL_DIR = "/app/views/_public/img/upload/thumbnail";
    
    /** Product Advertising API URL */
    const PRODUCT_ADVERTISING_API = "http://ecs.amazonaws.jp/onca/xml";
    /** Amazon: キャッシュID */
    const AMAZON_CACHE_ID = "amazon_cache";
    /** Amazon: キャッシュ時間(秒) */
    const AMAZON_CACHE_TIME = 30;
    
    /** エントリ内容一時保存キャッシュID */
    const ENTRY_TEMPORARY_CACHE_ID = "entry_temporary_cache";
    /** エントリ内容: キャッシュ時間(秒) */
    const ENTRY_TEMPORARY_CACHE_TIME = 86400;
    /** 差し戻しエントリ内容一時保存キャッシュID */
    const REMAND_ENTRY_TEMPORARY_CACHE_ID = "remand_entry_temporary_cache";
}
