<?php
/*
 * Functions related to Zend_Measure.
 * 
 * Created on Feb 4, 2011
 * Author: Yoni Rosenbaum
 */

class MeasureUtils {
    /**
     * Creates a new Zend_Measure object.
     * 
     * @param $unit String Full Zend_Measure unit.
     *        For example: 'Zend_Measure_Temperature::CELSIUS'
     * @param $value
     * @param $locale String (Optional) necessary if the value needs to be parsed.
     * @return Zend_Measure_Abstract
     */
    public static function newMeasure($unit, $value=null, $locale=null) {
        $unitInfo = self::getUnitInfo($unit);
        $unitClassName = $unitInfo['className'];
        $unitConstantName = $unitInfo['constantName'];
        $zendMeasureObj = new $unitClassName($value, $unitConstantName, $locale);
        return $zendMeasureObj;
    }

    /**
     * Parse a Zend_Measure unit.
     * 
     * @param $unit String Full Zend_Measure unit.
     *        For example: 'Zend_Measure_Temperature::CELSIUS'
     * @return Map
     */
    public static function getUnitInfo($unit) {
        $unitInfo = preg_split('/::/', $unit);
        $unitClassName = $unitInfo[0];
        $unitConstantName = $unitInfo[1];
        return array(
            'className' => $unitClassName,
            'constantName' => $unitConstantName
        );
    }
}