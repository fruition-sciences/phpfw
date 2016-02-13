<?php
/*
 * Created on Jun 22, 2007
 * Author: Yoni Rosenbaum
 *
 */

class Link extends HtmlElement {
    /**
     * @var Href
     */
    private $href;

    /**
     * If set to false, link will show up as text.
     *
     * @var boolean
     */
    private $active = true;

    public function __construct($href, $title='') {
        parent::__construct("a");
        $this->href = $href;
        $this->setTitle($title);
    }

    public function setTitle($title) {
        $this->setBody($title);
        return $this;
    }

    public function setParam($name, $value) {
        $this->href->set($name, $value);
        return $this;
    }

    public function setAction($action) {
        $this->href->set("_ac", $action);
        return $this;
    }

    /**
     * An 'inactive' link simply shows the text, but does not render the 'a' tag.
     *
     * @param boolean $active
     * @return Link
     */
    public function setActive($active) {
        $this->active = $active;
        return $this;
    }

    /**
     * Set the fragment identifier (i.e: the value following a '#' at the end of the URL).
     *
     * @param String $anchor
     */
    public function setAnchor($anchor) {
        $this->href->setAnchor($anchor);
        return $this;
    }

    public function __toString() {
        $this->set("href", $this->href);
        return $this->active ? parent::__toString() : $this->getBody();
    }

    public function getHref() {
        return $this->href;
    }
}