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
    private $onclick;
    private $class;
    private $content;
    private $subcontent;
    private $add;
    private $display;
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
        $this->onclick = $xmlElement->onclick;
        $this->class = $xmlElement->class;
        $this->content = $xmlElement->content;
        $this->subcontent = $xmlElement->subcontent;
        $this->add = $xmlElement->add;
        $this->display = $xmlElement->disp;
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
    
    public function getOnClick() {
        if ($this->onclick) {
            return (string)$this->onclick;
        }
        return null;
    }
    
    public function getClass() {
        if ($this->class) {
            return (string)$this->class;
        }
        return null;
    }
    
    public function getContent() {
        if ($this->content) {
            return (string)$this->content;
        }
        return null;
    }
    
    public function getSubContent() {
        if ($this->subcontent) {
            return (string)$this->subcontent;
        }
        return null;
    }
    
    public function getAdd() {
        if ($this->add) {
            return (string)$this->add;
        }
        return null;
    }
    
    public function getDisplay() {
        if ($this->display) {
            return (string)$this->display;
        }
        return null;
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