<?php


class DataProvider extends PHPUnit_Framework_TestCase {
    /** テスト用URL */
    protected $root_url = "http://localhost/";
    protected $project_name = "diarysys5";
    
    public function setUp() {
        $this->loadModule();
    }
    
    private function loadModule() {
        require_once $this->getRoot() . "/core/AutoImport.php";
        importAll("core");
        $this->root_url = $this->root_url . $this->project_name;
    }
    
    private function getRoot() {
        $current = dirname(__FILE__);
        $path_hierarchy_list = explode(DIRECTORY_SEPARATOR, $current);
        array_pop($path_hierarchy_list);
        $project_root = implode("/", $path_hierarchy_list);
        return is_dir($project_root) ? $project_root : null;
    }
    
    public function urlListProvider() {
        return array(
            array('/', 200),
            array('tag')
        );
    }
}
