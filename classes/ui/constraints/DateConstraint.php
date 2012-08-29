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
            $msg = sprintf(Application::getTranslator()->_('The field %1$s must be a valid date'), $this->getLabel());
            $this->addFieldError($ctx, $this->getName(), $msg);
            return false;
        }
        return true;
    }

    public function getType() {
        return ConstraintFactory::DATE;
    }
}