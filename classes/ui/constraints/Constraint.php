<?php
/*
 * Created on Oct 14, 2007
 * Author: Yoni Rosenbaum
 * 
 */

abstract class Constraint {
    private $name;
    private $label;

    public function __construct($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setLabel($label) {
        $this->label = $label;
    }

    public function getLabel() {
        return $this->label;
    }

    /**
     * Validate that the submission meets this constraint.
     * 
     * @param Context ctx the context
     * @return boolean true if validation passed. Otherwise false.
     */
    public abstract function validate($ctx);

    /**
     * Get the type of this constraint. The types are defined as constants in
     * ConstraintFactory.
     * 
     * @return String the constraint type
     */
    public abstract function getType();

    protected function addFieldError($ctx, $fieldName, $msg) {
        $form = $ctx->getForm();
        $form->addFieldError($fieldName, $msg);
        $ctx->getUIManager()->getErrorManager()->addErrorMessage($msg);
    }
}