<?php
/*
 * Created on Jun 22, 2007
 * Author: Yoni Rosenbaum
 *
 */

class Dropdown extends Control {
    /**
     * @var Dropdown_Option[]
     */
    private $options = array();
    /**
     * @var Dropdown_Optgroup[]
     */
    private $optgroups = array();
    private $values = array();
    /**
     * @var Link
     */
    private $readonlyLink;
    /**
     * The separator to use in read-only mode for
     * multi select dropdown.
     * @var String
     */
    private $multiSelectReadonlySeparator = ", ";

    public function __construct($name) {
        parent::__construct("select", $name);
    }

    /**
     * @deprecated use addOption
     */
    public function add_option($name, $value=null) {
        return $this->addOption($name, $value);
    }

    public function addOption($name, $value=null, $tooltip=null) {
        $option = new Dropdown_Option($name, $value);
        if ($tooltip) {
            $option->set("title", $tooltip);
        }
        $option->setForm($this->getForm());
        $this->options[] = $option;
        return $this;
    }
    
    /**
     * @param String $label
     * @return Dropdown
     */
    public function addOptgroup($label) {
        $optgroup = new Dropdown_Optgroup($label);
        $optgroup->setForm($this->getForm());
        return $this->addOptgroupObject($optgroup);
    }
    
    /**
     * @param Dropdown_Optgroup $optgroup
     * @return Dropdown
     */
    public function addOptgroupObject($optgroup) {
        $optgroup->setForm($this->getForm());
        $this->optgroups[] = $optgroup;
        return $this;
    }
    

    /**
     * Set a link to be shown instead of the regular title in readonly mode.
     * The title to this link will be set as the option name.
     *
     * @param Link $readonlyLink the link to show in readonly mode.
     */
    public function setReadonlyLink($readonlyLink) {
        $this->readonlyLink = $readonlyLink;
    }

    public function __toString() {
        $this->setBody($this->options_as_string());
        return parent::__toString();
    }

    public function toString() {
        $ret = "";
        $options = $this->options;
        foreach ($this->optgroups as $optgroup) {
            $options = array_merge($options, $optgroup->getOptions());
        }
        foreach($options as $option) {
            if (in_array($option->get("value"), $this->values)) {
                $separator = empty($ret) ? "" : $this->multiSelectReadonlySeparator;
                if ($this->readonlyLink) {
                    $ret .= $separator . $this->readonlyLink->setTitle($option->__toString())->__toString();
                } else {
                    $ret .= $separator . $option->__toString();
                }
            }
        }
        return $ret;
    }

    /**
     * @return string
     */
    private function options_as_string() {
        $html = "";
        for ($i = 0; $i < sizeof($this->options); $i++) {
            $html .= $this->options[$i]->asString($this->values);
        }
        for ($i = 0; $i < sizeof($this->optgroups); $i++) {
            $html .= $this->optgroups[$i]->asString($this->values);
        }
        return $html;
    }

    /**
     * Overriden. Instead of setting the HTML value attribute, keep it as a
     * private. It will be used in order to mark the 'option' who's got this
     * value as selected.
     *
     * @param array | String $values the value for a select or the array of values
     * for a multi select.
     */
    public function setValue($values) {
        $this->values = is_array($values) ? $values : array($values);
        return $this;
    }
    
    /**
     * The Multi Select Readonly separator is used to separate all the selected values
     * of a multi select field in read-only mode.
     * 
     * @param String $multiSelectReadonlySeparator
     */
    public function setMultiSelectReadonlySeparator($multiSelectReadonlySeparator){
        $this->multiSelectReadonlySeparator = $multiSelectReadonlySeparator;
    }

    /**
     * @return Dropdown_Option[]
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * @return Dropdown_Optgroup[]
     */
    public function getOptgroups() {
        return $this->optgroups;
    }

    /**
     * @return array
     */
    public function getValues() {
        return $this->values;
    }

    /**
     * @return Link
     */
    public function getReadonlyLink() {
        return $this->readonlyLink;
    }

    /**
     * @return String
     */
    public function getMultiSelectReadonlySeparator() {
        return $this->multiSelectReadonlySeparator;
    }
}