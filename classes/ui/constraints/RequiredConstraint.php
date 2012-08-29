<?php
/*
 * Created on Oct 12, 2007
 * Author: Yoni Rosenbaum
 *
 */

class RequiredConstraint extends Constraint {

    public function doValidate($ctx) {
        $value = $ctx->getRequest()->getString($this->getName(), null);
        if($value === null){
            $value = '';
            if(isset($_FILES[$this->getName()]['name'])){
                $value = $_FILES[$this->getName()]['name'];
            }
        }
        if ($value == '') {
            $msg = sprintf(Application::getTranslator()->_('The field %1$s is required'), $this->getLabel());
            $this->addFieldError($ctx, $this->getName(), $msg);
            return false;
        }
        return true;
    }

    public function getType() {
        return ConstraintFactory::REQUIRED;
    }
}