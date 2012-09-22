<?php
namespace WebStream;
/**
 * Calendarライブラリ
 * @author Ryuichi TANAKA.
 * @since 2012/01/21
 */
class Calendar {
    private function __construct() {}
    
    public static function convHTML($data) {
        $dirname = Utility::getProjectName();
        $html = "<div class='calendar'>";
        $year = null;
        for ($i = 0; $i < count($data); $i++) {
            $date = explode("-", $data[$i]["DATETIME"]);
            $count = $data[$i]["COUNT"];
            if ($year !== $date[0]) {
                if ($year !== null) {
                    $html .= "<br/>";
                }
                $year = $date[0];
                $html .= "<span class='year'>${year}</span>";
            }
            $month = $date[1];
            $html .= "<span class='month'><a href='/${dirname}/archive/${year}${month}'>${month}</a>";
            $html .= "<sup class='entry_count'>${count}</sup></span>";
        }
        $html.= "</div>";
        
        return $html;
    }
}
