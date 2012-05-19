<?php


class ErrorController extends AppController {
    /**
     * 未使用のためあとで削除
     */
    public function error($params) {
        $this->render("error", array(
            "title" => $this->getTitle("エラー")
        ));
    }
}
