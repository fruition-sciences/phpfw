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
        //$form->setValues($_REQUEST);
    }

    /**
     * Add an error associated with the given field name. The message is defined
     * by the given error tag and will be defined in the form.
     *
     * @param Context $ctx
     * @param String $fieldName the name of the field
     * @param String $errorTag the tag identifying the error message
     */
    protected function addFieldErrorTag($ctx, $fieldName, $errorTag) {
        $form = $ctx->getForm();
        $form->addFieldError($fieldName, $errorTag);
        $ctx->getUIManager()->getErrorManager()->addError($errorTag);
    }
    
    /**
     * This method can not be overrided, if you want to enable/disable locale support,
     * please change the urlLocale parameter value in the config.xml file.
     * This method returns true if this controller must have the locale in the url and false if not.
     * @return boolean
     */
    final public function getLocaleSupport() {
        $config = Config::getInstance();
        $result = $config->getBoolean("webapp/controllers/controller[@class='". get_class($this) ."']/@urlLocale", false);
        return $result;
    }
    
}