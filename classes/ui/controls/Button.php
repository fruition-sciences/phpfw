<?php
/*
 * Created on Jun 22, 2007
 * Author: Yoni Rosenbaum
 *
 */

class Button extends Control {
    private $href;
    private $title;
    private $target; // Optional
    private $submit = true;
    private $onclick = array();

    /**
     * Tracks whether the 'onclick' attribute is needed or not. This is in order
     * to avoid including it if it's unnecessary.
     *
     * Current implementation: We assume 'onclick' is needed only if 'action' or
     * 'url' have been set. Note: url is always set. We actualy check if there
     * was an attempt to change it.
     *
     * @var boolean
     */
    private $onclickNeeded = false;

    public function __construct($title) {
        parent::__construct("button");
        $this->title = $title;
        $this->href = Href::current();
    }

    public function setUrl($url) {
        if (is_object($url) && get_class($url) == 'Href') {
            $this->href = $url;
        }
        else {
            $this->href = new Href($url);
        }
        $this->onclickNeeded = true;
        return $this;
    }

    public function setAction($action) {
        $this->href->set("_ac", $action);
        $this->onclickNeeded = true;
        return $this;
    }

    /**
     * Use this if you want the button to generate a link to a URL.
     * Otherwise, if the button has an action, it will perform a POST (submit).
     *
     * Do NOT use it if you're planning to attach your own click event handler
     * to this button.
     *
     * @return Button
     */
    public function noSubmit() {
        $this->submit = false;
        // To be safe (for backwards compatibility), mark that onclick is needed
        $this->onclickNeeded = true;
        return $this;
    }

    /**
     * Override to deal with known events, such as 'onclick', so that they are
     * concatenated and not overwritten.
     *
     * @param String $key
     * @param String $val
     */
    public function set($key, $val=null) {
        if ($key == "onclick" && $val) {
            $this->onclick[] = $val;
            $this->onclickNeeded = true;
        }
        else {
            parent::set($key, $val);
        }
        return $this;
    }

    public function setParam($name, $value) {
        $this->href->set($name, $value);
        $this->onclickNeeded = true;
        return $this;
    }

    public function unsetParam($name) {
        $this->href->un_set($name);
        $this->onclickNeeded = true;
        return $this;
    }

    public function __toString()
    {
        $this->setOnClick();
        $this->setBody($this->title);
        if ($this->getType() != 'button') {
            $this->set('button', '1');
        }
        return parent::__toString();
    }

    private function setOnClick() {
        // Avoid setting the 'onclick' attribute if it's not needed
        if (!$this->onclickNeeded) {
            return;
        }

        $params = array($this->href);
        if ($this->target) {
            $params[] = $this->target;
        }
        // Surround params with single quotes
        $params = array_map(function($str) {
            return "'" . $str . "'";
        }, $params);

        $paramListString = implode(', ', $params);
        $jsFunctionName = $this->submit ? 'button_submit' : 'button_click';
        $onclick = "$jsFunctionName($paramListString); return false";
        $this->onclick[] = $onclick;
        $onclickStr = StringUtils::arrayToString($this->onclick, "; ", true);
        parent::set("onclick", $onclickStr);
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getTitle() {
        return $this->title;
    }

    /**
     * Allows opening the URL in a new window.
     *
     * @param String $target the name of the new window.
     */
    public function setTarget($target) {
        $this->target = $target;
        return $this;
    }

    /**
     * @return Href
     */
    public function getHref() {
        return $this->href;
    }

    /**
     * @return mixed
     */
    public function getTarget() {
        return $this->target;
    }

    /**
     * @return boolean
     */
    public function isSubmit() {
        return $this->submit;
    }

    /**
     * @return array
     */
    public function getOnclick() {
        return $this->onclick;
    }

    /**
     * The value of the $onclickNeeded variable is set automatically based
     * on whether 'action' or 'url' have been set.
     * However, this method allows to manually override it.
     *
     * @param boolean $onclickNeeded
     */
    public function setOnclickNeeded($onclickNeeded) {
        $this->onclickNeeded = $onclickNeeded;
    }
}
