<?php
/*
 * Created on Oct 14, 2007
 * Author: Yoni Rosenbaum
 *
 */

abstract class Constraint {
    private $name;
    private $label;
    private $restrictionPair; // name & value
    private $forAction;

    public function __construct($name, $forAction=null) {
        $this->name = $name;
        $this->forAction = $forAction;
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
     * Restrict this constraint to be checked only if the URL (or form) contains
     * a parameter with a given value.
     *
     * @param String $paramName parameter name
     * @param String $paramValue parameter value
     */
    public function restrict($paramName, $paramValue) {
        $this->restrictionPair = array($paramName, $paramValue);
        return $this;
    }

    /**
     * Validate that the submission meets this constraint.
     *
     * @param Context ctx the context
     * @return boolean true if validation passed. Otherwise false.
     */
    public function validate($ctx) {
        if (!$this->passedRestriction($ctx)) {
            return false;
        }
        // If the constraint is bound to an action and this is not the current action, validation passes.
        if ($this->forAction && !$ctx->actionIs($this->forAction, true)) {
            return true;
        }
        return $this->doValidate($ctx);
    }

    /**
     * Check if the restriction (if there is one) is met. A restriction consist
     * of a name-value pair, which are checked against URL (or form) parameters.
     * If there is no restriction, it is assumed to be met.
     */
    private function passedRestriction($ctx) {
        if (!$this->restrictionPair) {
            return true;
        }
        $paramName = $this->restrictionPair[0];
        $paramVal = $this->restrictionPair[1];
        return $ctx->getRequest()->getString($paramName, '') == $paramVal;
    }

    public abstract function doValidate($ctx);

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