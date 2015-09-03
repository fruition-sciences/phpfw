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
     * Checks whether this controller requires the URL to contain the locale
     * parameter.
     * This is determined based on the 'urlLocale' attribute of this controller
     * in the Config file.
     * 
     * @return boolean Whether this controller requires the URL to contain the
     * locale parameter.
     */
    final public function isLocaleSupported() {
        $config = Config::getInstance();
        $result = $config->getBoolean("webapp/controllers/controller[@class='". get_class($this) ."']/@urlLocale", false);
        return $result;
    }
    
}