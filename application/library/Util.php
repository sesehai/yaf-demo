<?php
/**
 * 扩展功能函数类
 */
class Util{

    /**
     * 分页函数
     * @param $reload url
     * @param $page 当前页
     * @param $tpages 每页分页数量
     * @param $adjacents 每页分页数量
     */
    public static function paginate($reload, $page, $tpages, $adjacents = 4) {
        $firstlabel = "&laquo;&nbsp;";
        $prevlabel  = "&lsaquo;&nbsp;";
        $nextlabel  = "&nbsp;&rsaquo;";
        $lastlabel  = "&nbsp;&raquo;";
        
        $out = "<div class=\"pagin\">\n";
        
        // first
        if($page>($adjacents+1)) {
            $out.= "<a href=\"" . $reload . "\">" . $firstlabel . "</a>\n";
        }
        else {
            $out.= "<span>" . $firstlabel . "</span>\n";
        }
        
        // previous
        if($page==1) {
            $out.= "<span>" . $prevlabel . "</span>\n";
        }
        elseif($page==2) {
            $out.= "<a href=\"" . $reload . "\">" . $prevlabel . "</a>\n";
        }
        else {
            $out.= "<a href=\"" . $reload . "&amp;page=" . ($page-1) . "\">" . $prevlabel . "</a>\n";
        }
        
        // 1 2 3 4 etc
        $pmin = ($page>$adjacents) ? ($page-$adjacents) : 1;
        $pmax = ($page<($tpages-$adjacents)) ? ($page+$adjacents) : $tpages;
        for($i=$pmin; $i<=$pmax; $i++) {
            if($i==$page) {
                $out.= "<span class=\"current\">" . $i . "</span>\n";
            }
            elseif($i==1) {
                $out.= "<a href=\"" . $reload . "\">" . $i . "</a>\n";
            }
            else {
                $out.= "<a href=\"" . $reload . "&amp;page=" . $i . "\">" . $i . "</a>\n";
            }
        }
        
        // next
        if($page<$tpages) {
            $out.= "<a href=\"" . $reload . "&amp;page=" .($page+1) . "\">" . $nextlabel . "</a>\n";
        }
        else {
            $out.= "<span>" . $nextlabel . "</span>\n";
        }
        
        // last
        if($page<($tpages-$adjacents)) {
            $out.= "<a href=\"" . $reload . "&amp;page=" . $tpages . "\">" . $lastlabel . "</a>\n";
        }
        else {
            $out.= "<span>" . $lastlabel . "</span>\n";
        }
        
        $out.= "</div>";
        
        return $out;
    }
}
