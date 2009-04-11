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
            $msg = I18nUtil::lookupString('REQUIRE_CONSTRAINT_MSG');
            $msg->set('fieldName', $this->getLabel());
            $this->addFieldError($ctx, $this->getName(), $msg->__toString());
            return false;
        }
        return true;
    }

    public function getType() {
        return ConstraintFactory::REQUIRED;
    }
}