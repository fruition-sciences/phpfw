<?php
/*
 * Created on Oct 12, 2007
 * Author: Yoni Rosenbaum
 * 
 */

require_once("Constraint.php");
require_once("DateConstraint.php");
require_once("RequiredConstraint.php");

class ConstraintFactory {
    const REQUIRED = "required";
    const DATE = "date";

    public static function newConstraint($name, $type) {
        if ($type == self::REQUIRED) {
            return new RequiredConstraint($name);
        }
        if ($type == self::DATE) {
            return new DateConstraint($name);
        }
        throw new Exception("Unknown constraint type: " . $type);
    }

    public static function deserialize($text) {
        $tokens = split(':', $text);
        $type = $tokens[0];
        if ($type == self::REQUIRED) {
            $constraint = new RequiredConstraint();
            $constraint->deseralize($text);
            return $constraint;
        }
    }
}