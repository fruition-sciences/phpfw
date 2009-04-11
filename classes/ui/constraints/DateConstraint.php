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
            $msg = I18nUtil::lookupString('DATE_CONSTRAINT_MSG');
            $msg->set('fieldName', $this->getLabel());
            $this->addFieldError($ctx, $this->getName(), $msg->__toString());
            return false;
        }
        return true;
    }

    public function getType() {
        return ConstraintFactory::DATE;
    }
}