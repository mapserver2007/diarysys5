<?php
namespace WebStream;
/**
 * フィードコントローラ
 * @author Ryuichi TANAKA.
 * @since 2012/02/07
 */
class FeedController extends AppController {
    /**
     * RSS2.0を表示する
     */
    public function rss() {
        $this->render_rss("rss", array(
            "title"         => $this->message("rss_title"),
            "link"          => $this->message("rss_link"),
            "description"   => $this->message("rss_description"),
            "language"      => $this->message("rss_language"),
            "copyright"     => $this->message("rss_copyright"),
            "lastBuildDate" => date("r"),
            "generator"     => $this->message("rss_generator"),
            "docs"          => $this->message("rss_docs"),
            "contents"      => $this->Feed->contents()
        ));
    }
}
    