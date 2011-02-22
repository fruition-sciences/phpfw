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
            $msg = I18nUtil::lookupString('NUMBER_CONSTRAINT_MSG');
            $msg->set('fieldName', $this->getLabel());
            $this->addFieldError($ctx, $this->getName(), $msg->__toString());
            return false;
        }
        if ($this->minValue) {
            if ($this->minValue->isExclusive()) {
                if ($value <= $this->minValue->getValue()) {
                    $msg = I18nUtil::lookupString('NUMBER_CONSTRAINT_MIN_VALUE_EXCLUSIVE_MSG');
                    $msg->set('fieldName', $this->getLabel());
                    $msg->set('value', $this->minValue->getValue());
                    $this->addFieldError($ctx, $this->getName(), $msg->__toString());
                    return false;
                }
            }
            else {
                if ($value < $this->minValue->getValue()) {
                    $msg = I18nUtil::lookupString('NUMBER_CONSTRAINT_MIN_VALUE_INCLUSIVE_MSG');
                    $msg->set('fieldName', $this->getLabel());
                    $msg->set('value', $this->minValue->getValue());
                    $this->addFieldError($ctx, $this->getName(), $msg->__toString());
                    return false;
                }
            }
        }
        if ($this->maxValue) {
            if ($this->maxValue->isExclusive()) {
                if ($value >= $this->maxValue->getValue()) {
                    $msg = I18nUtil::lookupString('NUMBER_CONSTRAINT_MAX_VALUE_EXCLUSIVE_MSG');
                    $msg->set('fieldName', $this->getLabel());
                    $msg->set('value', $this->maxValue->getValue());
                    $this->addFieldError($ctx, $this->getName(), $msg->__toString());
                    return false;
                }
            }
            else {
                if ($value > $this->maxValue->getValue()) {
                    $msg = I18nUtil::lookupString('NUMBER_CONSTRAINT_MAX_VALUE_INCLUSIVE_MSG');
                    $msg->set('fieldName', $this->getLabel());
                    $msg->set('value', $this->maxValue->getValue());
                    $this->addFieldError($ctx, $this->getName(), $msg->__toString());
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

class NumberConstraintLimit {
    private $value;
    private $exclusive;

    public function __construct($value, $exclusive=false) {
        if (!is_numeric($value)) {
            throw new IllegalArgumentException("value must be a number");
        }
        $this->value = $value;
        $this->exclusive = $exclusive;
    }

    public function getValue() {
        return $this->value;
    }

    public function isExclusive() {
        return $this->exclusive;
    }
}