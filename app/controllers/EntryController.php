<?php
namespace WebStream;
/**
 * エントリコントローラ
 * @author Ryuichi TANAKA.
 * @since 2011/11/02
 */
class EntryController extends AppController {
    /**
     * 「/」からアクセスされるアクション
     */
    public function entry() {
        if ($this->isPC) {
            $entries = $this->Entry->entry($this->_page);
            $paginate = $this->Entry->paginate($this->_page);
            $calendar = $this->Entry->calendar();
            $this->render_entry($entries, $calendar, $paginate);
        }
        else if ($this->isMobile) {
            $this->render_mobile_entry();
        }
    }
    
    /**
     * 「/tag/:name」からアクセスされるアクション
     */
    public function tag($params) {
        $entry = $this->Entry->entryByTag($params["name"], $this->_page);
        $paginate = $this->Entry->paginateByTag($params['name'], $this->_page);
        $this->render_entry($entry, null, $paginate);
    }
    
    /**
     * 「/entry/:id」からアクセスされるアクション
     */
    public function id($params) {
        $entry = $this->Entry->entryById($params["id"], $this->_page);
        $paginate = $this->Entry->paginateById($this->_page);
        $calendar = $this->Entry->calendar();
        $this->render_entry($entry, $calendar, $paginate);
    }
    
    /**
     * 「/archive/:ymd」からアクセスされるアクション
     */
    public function archive($params) {
        $entry = $this->Entry->entryByArchive($params["ymd"], $this->_page);
        $paginate = $this->Entry->paginateByArchive($params["ymd"], $this->_page);
        $calendar = $this->Entry->calendar();
        $this->render_entry($entry, $calendar, $paginate);
    }
    
    
    
    public function entry_mobile($params) {
        echo "kita-";
    }
    
    
}
