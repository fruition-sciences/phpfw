<?php
/*
 * Created on Oct 14, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class DateConstraint extends Constraint {
    public function doValidate($ctx) {
        $value = $ctx->getRequest()->getString($this->getName(), '');
        // Validate only if there is a value
        if (!$value) {
            return true;
        }
        $dateValue = strtotime($value);
        if (!$dateValue) {
            $this->addFieldError($ctx, $this->getName(), "The field '" . $this->getLabel() . "' must be a valid date");
            return false;
        }
        return true;
    }

    public function getType() {
        return ConstraintFactory::DATE;
    }
}