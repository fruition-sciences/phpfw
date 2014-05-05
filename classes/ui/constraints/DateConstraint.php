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
        if (!$converter->parseDate($value) && !$converter->parseDate($value, IntlDateFormatter::SHORT, IntlDateFormatter::NONE)) {
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