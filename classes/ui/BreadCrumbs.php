<?php
/*
 * Created on Jun 22, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class Bread_Crumbs {
    private $items = array();

    /**
     * Add an item to the bread crumbs.
     * 
     * @param $item Object
     * @return Bread_Crumbs
     */
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

    /**
     * Get all the bread crumb items.
     * 
     * @return Array of Objects
     */
    public function getAll() {
        return $this->items;
    }

    /**
     * Add the given items to this bread crumb's items.
     * 
     * @param $items Array of Objects
     * @return Bread_Crumbs
     */
    public function addAll($items) {
        foreach ($items as $item) {
            $this->items[] = $item;
        }
        return $this;
    }
}
