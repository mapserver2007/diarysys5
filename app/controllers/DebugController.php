<?php
namespace WebStream;
class DebugController extends AppController {
    
    
    public function run($params) {
        $description = <<< EOF
[image]http://static.php.net/www.php.net/images/php.gif[/image]
EOF;
        
        $title = $params["title"];
        $this->Debug->run($title, $description);
        $this->Debug->runTag();
        
        echo "end";
    }
    
    public function amazon($params) {
        $this->Debug->amazon($params["keyword"]);
    }
}
