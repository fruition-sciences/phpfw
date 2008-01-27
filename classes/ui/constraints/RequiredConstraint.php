<?php
/*
 * Created on Oct 12, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class RequiredConstraint extends Constraint {

    public function doValidate($ctx) {
        $value = $ctx->getRequest()->getString($this->getName(), '');
        if ($value == '') {
            $this->addFieldError($ctx, $this->getName(), "The field '" . $this->getLabel() . "' is required");
            return false;
        }
        return true;
    }

    public function getType() {
        return ConstraintFactory::REQUIRED;
    }
}