<?php
namespace WebStream;
/**
 * Paginateライブラリ
 * @author Ryuichi TANAKA.
 * @since 2011/11/06
 */
import("core/Utility");

class Paginate {
    /** Paginateを表示するパス(URI) */
    private $path;
    /** 1ページあたりのエントリ表示数 */
    private $display;
    /** Paginateブロックの表示数 */
    private $page_block;
    /** 現在のページ番号 */
    private $current_page;
    /** ページ数 */
    private $page_num;
    /** 出力するHTML */
    private $html;
    
    /**
     * コンストラクタ
     * @param Integer 現在のページ番号
     * @param Integer エントリ数
     * @param Integer 1ページあたりの表示数
     * @param Integer 表示ブロック数
     */
    public function __construct($current_page,
                                $entry_num,
                                $display,
                                $page_block) {
        $this->display = $display;
        $this->page_block = $page_block;
        $this->path = "/" . Utility::getProjectName() . STREAM_ROUTING_PATH;
        $this->current_page = intval($current_page);
        $this->page_num = ceil(intval($entry_num) / $this->display);
        
        $this->html = "";
    }
    
    /**
     * PaginateのHTMLを返却する
     * @reutrn String HTML
     */
    public function html() {
        if ($this->current_page > $this->page_num) {
            throw new ResourceNotFoundException("Out of page range");
        }
        $this->previousPage();
        $this->page();
        $this->nextPage();
        
        return $this->html;
    }
    
    /**
     * 前のページのHTMLをセットする
     */
    private function previousPage() {
        if ($this->current_page == 1) {
            $this->html .= "<span class='disabled'>«Previous</span>";
        }
        else {
            $page = $this->current_page - 1;
            $this->html .= "<a href='{$this->path}?page={$page}' rel='prev'>«Previous</a>";
        }
    }
    
    /**
     * ページ番号のHTMLをセットする
     */
    private function page() {
        $page_block = array();
        $last_block_num = floor($this->page_num / $this->page_block);
        $first_block_num = $this->page_block - $last_block_num;
        
        if ($first_block_num > 0 && $this->page_num > $this->page_block) {
            if (($first_block_num < $this->current_page) &&
                     ($this->page_num - $last_block_num >= $this->current_page)) {

                for ($i = 1; $i < $first_block_num; $i++) {
                    $this->html .= "<a href='{$this->path}{$i}'>{$i}</a>";
                }
                $this->html .= "<span class='gap'>...</span>";
                $this->html .= "<span class='current'>{$this->current_page}</span>";
                $this->html .= "<span class='gap'>...</span>";
                
                $page = $this->page_num - $last_block_num;
                if ($this->current_page == $page) {
                    $page += 2;
                }
                else {
                    $page += 1;
                }
                
                for ($j = $page; $j <= $this->page_num; $j++) {
                    $this->html .= "<a href='{$this->path}?page={$j}'>{$j}</a>";
                }
            }
            else {
                for ($i = 1; $i <= $first_block_num; $i++) {
                    if ($i == $this->current_page) {
                        $this->html .= "<span class='current'>{$i}</span>";
                    }
                    else {
                        $this->html .= "<a href='{$this->path}?page={$i}'>{$i}</a>";
                    }
                }
                $this->html .= "<span class='gap'>...</span>";
                $page = $this->page_num - $last_block_num + 1;
                for ($j = $page; $j <= $this->page_num; $j++) {
                    if ($j == $this->current_page) {
                        $this->html .= "<span class='current'>{$j}</span>";
                    }
                    else {
                        $this->html .= "<a href='{$this->path}?page={$j}'>{$j}</a>";
                    }
                }
            }
        }
        // ブロックそのまま
        else {
            for ($i = 1; $i <= $this->page_num; $i++) {
                if ($i == $this->current_page) {
                    $this->html .= "<span class='current'>{$i}</span>";
                }
                else {
                    $this->html .= "<a href='{$this->path}?page={$i}'>{$i}</a>";
                }
            }
        }
    }
    
    /**
     * 次のページのHTMLをセットする
     */
    private function nextPage() {
        if ($this->current_page == $this->page_num) {
            $this->html .= "<span class='disabled'>Next»</span>";
        }
        else {
            $page = $this->current_page + 1;
            $this->html .= "<a href='{$this->path}?page={$page}' rel='next'>Next»</a>";
        }
    }
}
