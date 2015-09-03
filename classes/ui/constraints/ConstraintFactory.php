<?php
/*
 * Created on Oct 12, 2007
 * Author: Yoni Rosenbaum
 *
 */

class ConstraintFactory {
    const REQUIRED = "required";
    const DATE = "date";
    const NUMBER = "number";

    public static function newConstraint($name, $type, $forAction=null) {
        if ($type == self::REQUIRED) {
            return new RequiredConstraint($name, $forAction);
        }
        if ($type == self::DATE) {
            return new DateConstraint($name, $forAction);
        }
        if ($type == self::NUMBER) {
            return new NumberConstraint($name, $forAction);
        }
        throw new Exception("Unknown constraint type: " . $type);
    }
}