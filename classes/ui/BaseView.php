<?php
/*
 * Created on Jul 8, 2007
 * Author: Yoni Rosenbaum
 * 
 */

require_once("classes/ui/View.php");
require_once("classes/ui/Template.php");
require_once("classes/ui/Page.php");
require_once("classes/ui/ui.php");
require_once("classes/ui/Form.php");
require_once("classes/core/Href.php");
require_once("classes/ui/controls/Link.php");
require_once("classes/ui/Formatter.php");
require_once("classes/ui/DataConverter.php");

abstract class BaseView implements View {
    protected $template;
    private $components = array(); // Map component_name -> component
    private $ctx;
    private $page;

    public function init($ctx) {
        $this->ctx = $ctx;
        $this->prepare($ctx);
        $this->initChildComponents($ctx);
    }

    public function prepare($ctx) {
    }

    public function render($ctx) {
        $this->ctx = $ctx;
        $templateName = get_class($this) . ".php"; 
        $path = "application/templates/controller/" . $ctx->getControllerAlias() . "/" . $templateName;
        global $form, $format;
        $form = $ctx->getForm();
        $timezone = $ctx->getUser()->getTimezone();
        $format = new Formatter($timezone);
        // Make $page and $ui globals, so they can be accessed by the view template.
        global $page, $ui;
        $page = $this->getPage(); 
        $ui = $this->ctx->getUIManager(); 
        include($path);
    }

    protected function getContext() {
        return $this->ctx;
    }

    protected function getTemplate() {
        if (!isset($this->template)) {
            $this->template = new Template($this->getPage());
        }
        return $this->template;
    }

    protected function get($key) {
        return $this->getTemplate()->get($key);
    }

    protected function containsKey($key) {
        return $this->getTemplate()->containsKey($key);
    }

    public function addComponent($name, $component) {
        $this->components[$name] = $component;
        $component->setParentView($this);
    }

    /**
     * Render the child component with the given name.
     * @param String $name the component's name
     */
    public function component($name) {
        $this->components[$name]->render($this->ctx);
    }

    public function getComponent($name) {
        return $this->components[$name];
    }

    private function initChildComponents($ctx) {
        foreach ($this->components as $name=>$component) {
            $component->init($ctx);
        }
    }

    public function getPage() {
        if (!$this->page) {
            $this->page = $this->newPage();
            $this->page->ctx = $this->ctx;
        }
        return $this->page;
    }

    protected function newPage() {
        global $ui;
        $ui = $this->ctx->getUIManager();
        return $ui->newPage();
    }
    
    public function __toString() {
        return get_class($this);
    }
}