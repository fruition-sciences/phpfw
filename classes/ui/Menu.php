<?php
/*
 * Created on Oct 5, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class Menu {
    /**
     * @var SimpleXMLElement
     */
    private $xml;
    private $mainMenuId;
    private $subMenuId;
    private $xmlFile = "application/menu/menu.xml";

    /**
     * Construct a new template.
     *
     * @param String $xmlFile path to XML file. Relative to include path. 
     */
    public function __construct($xmlFile=null) {
        if ($xmlFile) {
            $this->xmlFile = $xmlFile;
        }
        $this->load();
    }

    private function load() {
        $xmlStr = FileUtils::getFileContent($this->xmlFile);
        $this->xml = new SimpleXMLElement($xmlStr);
    }

    /**
     * Set the menu selection string. This is a string of the following format:
     * mainMenuId->subMenuId
     * The sub menu id is optional.
     */
    public function setMenuSelection($menuSelectionStr) {
        $parts = explode("->", $menuSelectionStr);
        if (count($parts) > 0) {
            $this->mainMenuId = $parts[0];
        }
        if (count($parts) > 1) {
            $this->subMenuId = $parts[1];
        }
    }

    public function getItems() {
        $user = Transaction::getInstance()->getUser();
        $items = array();
        foreach ($this->xml->item as $element) {
            $item = new MenuItem($this, $element);
            if ($item->getId() == $this->mainMenuId) {
                $item->setSelected(true);
            }
            if (!$item->isAdminOnly() || $user->isAdmin()) {
                $items[] = $item;
            }
        }
        return $items;
    }

    public function setMainMenuId($mainMenuId) {
        $this->mainMenuId = $mainMenuId;
    }

    public function getMainMenuId() {
        return $this->mainMenuId;
    }

    public function setSubMenuId($subMenuId) {
        $this->subMenuId = $subMenuId;
    }

    public function getSubMenuId() {
        return $this->subMenuId;
    }
}

class MenuItem {
    private $id;
    private $name;
    private $href;
    private $adminOnly; // Can be seen only by admin users
    private $items = array();
    private $selected = false;
    /**
     * @var Menu
     */
    private $menu;
    /**
     * @var SimpleXMLElement
     */
    private $xmlElement;

    public function __construct($menu, $xmlElement) {        
        $this->menu = $menu;
        $this->id = $xmlElement['id'];
        $this->adminOnly = $xmlElement['adminOnly'];
        $this->name = $xmlElement->name;
        $this->href = $xmlElement->href;
        $this->xmlElement = $xmlElement;
        foreach ($xmlElement->item as $childItem) {
            $item = new MenuItem($this->menu, $childItem);
            if ($item->getId() == $menu->getSubMenuId()) {
                $item->setSelected(true);
            }
            $this->items[] = $item;
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getHref() {
        return (string)$this->href;
    }

    public function getItems() {
        return $this->items;
    }

    public function isSelected() {
        return $this->selected;
    }

    public function setSelected($selected) {
        $this->selected = $selected;
    }

    public function isAdminOnly() {
    	return $this->adminOnly;
    }
    
    public function getXmlElement() {
        return $this->xmlElement;
    }
}