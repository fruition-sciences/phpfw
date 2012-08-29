<?php
/*
 * Created on Jun 8, 2007
 * Author: Yoni Rosenbaum
 *
 */

require_once("BreadCrumbs.php");
require_once("classes/utils/StringUtils.php");
require_once("classes/utils/DateUtils.php");
require_once("classes/utils/FileUtils.php");
require_once("classes/utils/BeanUtils.php");
require_once("classes/ui/Menu.php");

abstract class Page {
    public $mode;
    private $crumbs;
    private $noMenu = false;
    /**
     * @var Context
     */
    public $ctx;
    public $onload = array();
    public $onunload = array();
    //public $menu;
    public $title;
    public $pageTemplateFile;
    private $menuItemName;
    private $attributes = array();
    /**
     * @var ITranslator
     */
    private $translator;

    public function __construct($pageTemplateFile, ITranslator $translator=null) {
        $this->pageTemplateFile = $pageTemplateFile;
        $this->translator = $translator;
    }

    /**
     * Return the path to the page template file.
     */
    public function getPageTemplate() {
        return $this->pageTemplateFile;
    }

    /**
     * Allows overwriting the path to the page template file.
     *
     * @param String $pageTemplate the new path the page template file.
     */
    public function setPageTemplate($pageTemplateFile) {
        $this->pageTemplateFile = $pageTemplateFile;
    }

    function begin($menuItem="") {
        $this->mode = "begin";
        //$this->menu = new Menu();
        //$this->menu->setMenuSelection($menuItem);
        $this->menuItemName = $menuItem;
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

    public function addOnUnLoad($event) {
        $this->onunload[] = $event;
    }

    public function getOnloadValue() {
        $text = StringUtils::arrayToString($this->onload, ";", true);
        return $text;
    }

    public function getOnUnloadValue() {
        $text = StringUtils::arrayToString($this->onunload, ";", true);
        return $text;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getTitle() {
        return $this->title;
    }

    public function set($key, $value) {
        $this->attributes[$key] = $value;
    }

    public function get($key) {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }
        return null;
    }
    
    /**
     * Return the translated passed string
     * @param string $sentence
     * @return string|string
     */
    public function _($sentence) {
        if ($this->translator != null && $this->translator instanceof ITranslator) {
            return $this->translator->_($sentence);
        }
        return $sentence;
    }
    
    /**
     * @return ITranslator
     */
    public function getTranslator() {
        return $this->translator;
    }
}

?>
