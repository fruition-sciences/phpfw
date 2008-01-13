<?php
/*
 * Created on Jul 8, 2007
 * Author: Yoni Rosenbaum
 * 
 */

abstract class Controller {
    /**
     * Override this method to allow or dissallow access to this controller.
     * @param context.
     * @return boolean whether access to this controller is allowed for this 
     *         context or not.
     */
    public function checkAccess($ctx) {
        return true;
    }

//    protected function validateRequiredField($ctx, $name, $title) {
//        $value = $_REQUEST[$name];
//        if ($value == '') {
//            $this->addFieldError($ctx, $name, "The field '" . $title . "' is required");
//        }
//    }
//
    protected function addFieldError($ctx, $fieldName, $msg) {
        $form = $ctx->getForm();
        $form->addFieldError($fieldName, $msg);
        $ctx->getUIManager()->getErrorManager()->addErrorMessage($msg);
        $form->setValues($_REQUEST);
    }
}