<?php
/*
 * Created on Jan 21, 2008
 * Author: Yoni Rosenbaum
 * 
 */

require_once("classes/core/i18n/LocalizedString.php");

class I18nUtil {
    const UNDEFINED = "___UNDEFINED___";
    private static $bundles = array(); // map bundlePath -> Map(string->string)
    
    /**
     * Retrieve the string corresponding to the given stringId and bundleName.
     * If stringId is not available but $defaultVal is pass return the default value.
     * @param string $bundleName
     * @param string $stringId
     * @param string $defaultVal (optional and prevent exception)
     * @return LocalizedString
     */
    public static function lookup($bundleName, $stringId, $defaultVal=self::UNDEFINED) {
        $bundlePath = "application/i18n/en/" . $bundleName . ".xml";
        if (!isset(self::$bundles[$bundlePath])) {
            self::loadResourceBundle($bundleName, $bundlePath);
        }
        $bundle = self::$bundles[$bundlePath];
        if (!isset($bundle[$stringId])) {
            if ($defaultVal === self::UNDEFINED) {
                throw new IllegalArgumentException("Undefined string " . $stringId . " in resource bundle " . $bundlePath);
            } else {
                return new LocalizedString((string)$defaultVal);
            }
        }
        return new LocalizedString($bundle[$stringId]);
    }

    /**
     * Retrieve the string corresponding to the given stringId in strings bundle.
     * If stringId is not available but $defaultVal is pass return the default value.
     * @param string $bundleName
     * @param string $stringId
     * @param string $defaultVal (optional and prevent exception)
     * @return LocalizedString
     */
    public static function lookupString($stringId, $defaultVal=self::UNDEFINED) {
        return self::lookup('strings', $stringId, $defaultVal);
    }
    
    private static function loadResourceBundle($bundleName, $bundlePath) {
        $map = array();
        $xmlStr = FileUtils::getFileContent($bundlePath);
        $xml = new SimpleXMLElement($xmlStr);
        foreach ($xml->string as $str) {
            $stringId = (string)$str['id'];
            $stringVal = (string)$str['value'];
            $map[$stringId] = $stringVal;
        }
        self::$bundles[$bundlePath] = $map;
    }
    
    /**
     * Check if a stringId is available in the given bundle.
     * @param string $bundleName
     * @param string $stringId
     * @return boolean true on success
     */
    public static function stringExist($bundleName, $stringId) {
        if(empty($stringId))
            return false;
        $string = self::lookup($bundleName, $stringId, "stringExist" . self::UNDEFINED);
        if(strval($string) == "stringExist" . self::UNDEFINED){
            return false;
        }
        return true;
    }
}