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