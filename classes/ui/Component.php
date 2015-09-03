<?php
/*
 * Created on Dec 8, 2007
 * Author: Yoni Rosenbaum
 *
 * A Component is a BaseView that can be included in another view.
 * Use the method: BaseView->addComponent() to add a component to a view.
 * The BaseView will then render all its child components as part of rendering
 * itself.
 */

abstract class Component extends BaseView {
    private $parentView;

    public function setParentView($parentView) {
        $this->parentView = $parentView;
    }

    public function getParentView() {
        return $this->parentView;
    }

    public function getPage() {
        return $this->parentView->getPage();
    }

    public function show() {
        $this->render($this->getContext());
    }
}