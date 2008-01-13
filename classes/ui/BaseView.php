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

abstract class BaseView implements View {
    protected $template;
    private $components = array(); // Map component_name -> component
    private $ctx;

    public function init($ctx) {
        $this->prepare($ctx);
        $this->initChildComponents($ctx);
    }

    public function prepare($ctx) {
    }

    public function render($ctx) {
        $this->ctx = $ctx;
        $templateName = get_class($this) . ".php"; 
        $path = "application/templates/controller/" . $ctx->getControllerAlias() . "/" . $templateName;
        global $ui, $page, $form, $page, $format;
        $ui = $ctx->getUIManager();
        $page = $ui->newPage();
        $page->ctx = $ctx;
        $form = $ctx->getForm();
        $format = new Formatter();
        include($path);
    }

    protected function getTemplate() {
        if (!isset($this->template)) {
            $this->template = new Template();
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
    }

    /**
     * Render the child component with the given name.
     * @param String $name the component's name
     */
    public function component($name) {
        $this->components[$name]->render($this->ctx);
    }

    private function initChildComponents($ctx) {
        foreach ($this->components as $name=>$component) {
            $component->init($ctx);
        }
    }
}