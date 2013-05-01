<?php
/*
 * Created on Apr 11, 2009
 * Author: Yoni Rosenbaum
 *
 */

class NumberConstraint extends Constraint {
    private $minValue = null;
    private $maxValue = null;

    public function doValidate($ctx) {
        $formattedValue = $ctx->getRequest()->getString($this->getName(), null);
        // Validate only if there is a value
        if ($formattedValue === null || $formattedValue === '') {
            return true;
        }
        $format = new Formatter($ctx->getUser()->getTimezone(), $ctx->getUser()->getLocale());
        $value = $format->getNumber($formattedValue);
        if ($value === false) {
            $msg = sprintf(Application::getTranslator()->_('The field %1$s must be a valid number'), $this->getLabel());
            $this->addFieldError($ctx, $this->getName(), $msg);
            return false;
        }
        if ($this->minValue) {
            if ($this->minValue->isExclusive()) {
                if ($value <= $this->minValue->getValue()) {
                    $msg = sprintf(Application::getTranslator()->_('The value of the %1$s field must be greater than %2$s'), $this->getLabel(), $this->minValue->getValue());
                    $this->addFieldError($ctx, $this->getName(), $msg);
                    return false;
                }
            }
            else {
                if ($value < $this->minValue->getValue()) {
                    $msg = sprintf(Application::getTranslator()->_('The value of the %1$s field must be greater or equal to %2$s'), $this->getLabel(), $this->minValue->getValue());
                    $this->addFieldError($ctx, $this->getName(), $msg);
                    return false;
                }
            }
        }
        if ($this->maxValue) {
            if ($this->maxValue->isExclusive()) {
                if ($value >= $this->maxValue->getValue()) {
                    $msg = sprintf(Application::getTranslator()->_('The value of the %1$s field must be smaller than %2$s'), $this->getLabel(), $this->maxValue->getValue());
                    $this->addFieldError($ctx, $this->getName(), $msg);
                    return false;
                }
            }
            else {
                if ($value > $this->maxValue->getValue()) {
                    $msg = sprintf(Application::getTranslator()->_('The value of the %1$s field must be smaller or equal to %2$s'), $this->getLabel(), $this->maxValue->getValue());
                    $this->addFieldError($ctx, $this->getName(), $msg);
                    return false;
                }
            }
        }
        return true;
    }

    public function getType() {
        return ConstraintFactory::NUMBER;
    }

    public function setMin($minValue, $exclusive=false) {
        $this->minValue = new NumberConstraintLimit($minValue, $exclusive);
        return $this;
    }

    public function setMax($maxValue, $exclusive=false) {
        $this->maxValue = new NumberConstraintLimit($maxValue, $exclusive);
        return $this;
    }
}