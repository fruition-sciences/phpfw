<?php
/*
 * Created on Jan 21, 2008
 * Author: Yoni Rosenbaum
 * 
 */

require_once("classes/core/i18n/LocalizedString.php");

class I18nUtil {
    private static $bundles = array(); // map bundlePath -> Map(string->string)

    public static function lookup($bundleName, $stringId) {
        $bundlePath = "application/i18n/en/" . $bundleName . ".xml";
        if (!isset(self::$bundles[$bundlePath])) {
            self::loadResourceBundle($bundleName, $bundlePath);
        }
        $bundle = self::$bundles[$bundlePath];
        if (!isset($bundle[$stringId])) {
            throw new IllegalArgumentException("Undefined string " . $stringId . " in resource bundle " . $bundlePath);
        }
        return new LocalizedString($bundle[$stringId]);
    }

    public static function lookupString($stringId) {
        return self::lookup('strings', $stringId);
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
    
    public static function stringExist($bundleName, $stringId) {
        if(empty($stringId))
            return false;
        $stringId = (string)$stringId;
        $bundlePath = "application/i18n/en/" . $bundleName . ".xml";
        if (!isset(self::$bundles[$bundlePath])) {
            self::loadResourceBundle($bundleName, $bundlePath);
        }
        $bundle = self::$bundles[$bundlePath];
        if (empty($bundle) || !isset($bundle[$stringId]))
            return false;
        return true;
    }
}