<?php
/*
 * Created on Jun 22, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class Bread_Crumbs {
    private $items = array();

    public function add($item) {
        $this->items[] = $item;
        return $this;
    }

    public function __toString()
    {
        $html = "<div class='crumbs'>";
        $cumbsTxt = StringUtils::arrayToString($this->items, " &rarr; ");
        $html .= $cumbsTxt;
        $html .= "</div>";
        return $html;
    }
}
