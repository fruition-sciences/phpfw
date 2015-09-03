<?php
/*
 * Created on Apr 30 2013
 * Author: bsoufflet
 * 
 */

class MenuItem {
    private $id;
    private $name;
    private $href;
    private $adminOnly; // Can be seen only by admin users
    private $items = array();
    private $selected = false;
    private $attributes;

    /**
     * @var Menu
     */
    private $menu;
    /**
     * @var SimpleXMLElement
     */
    private $xmlElement;

    public function __construct($menu, $xmlElement) {
        $this->attributes = self::getXmlElementAttributes($xmlElement);        
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

    private static function getXmlElementAttributes($xmlElement) {
        $attributes = array();
        foreach ($xmlElement->attributes() as $k => $v) {
            $attributes[$k] = (string)$v;
        }
        return $attributes;
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

    public function getAttributes() {
        return $this->attributes;
    }

    public function getAttribute($key) {
        if (!isset($this->attributes[$key])) {
            return null;
        }
        return $this->attributes[$key];
    }
}