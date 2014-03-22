<?php
/*
 * Created on Oct 14, 2007
 * Author: Yoni Rosenbaum
 *
 */

class DateConstraint extends Constraint {
    /**
     * (non-PHPdoc)
     * @see Constraint::doValidate()
     */
    public function doValidate($ctx) {
        $value = $ctx->getRequest()->getString($this->getName(), '');
        // Validate only if there is a value
        if (!$value) {
            return true;
        }
        $converter = DataConverter::getInstance();
        try {
            $dateValue = $converter->parseDate($value, Zend_Date::DATETIME_SHORT);
        }
        catch (Exception $e) {
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