<?php
/*
 * Created on Jun 8, 2007
 * Author: Yoni Rosenbaum
 *
 */

require_once("BreadCrumbs.php");
require_once("classes/utils/StringUtils.php");
require_once("classes/utils/FileUtils.php");
require_once("classes/ui/Menu.php");

abstract class Page {
    public $mode;
    private $crumbs;
    private $noMenu = false;
    public $ctx;
    public $onload = array();
    //public $menu;
    public $title;

    public abstract function getPageTemplate();

    function begin($menuItem="") { 
        $this->mode = "begin";
        //$this->menu = new Menu();
        //$this->menu->setMenuSelection($menuItem);
        include($this->getPageTemplate());
    }

    function end() {
        $this->mode = "end";
        include($this->getPageTemplate());
    }

    public function get_crumbs() {
        if (!$this->crumbs) {
            $this->crumbs = new Bread_Crumbs();
        }
        return $this->crumbs;
    }

    public function isNoMenu() {
        return $this->noMenu;
    }

    public function setNoMenu($noMenu) {
        $this->noMenu = $noMenu;
    }

    public function addOnLoad($event) {
        $this->onload[] = $event;
    }

    public function getOnloadValue() {
        $text = StringUtils::arrayToString($this->onload, ";", true);
        return $text;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getTitle() {
        return $this->title;
    }
}

?>
