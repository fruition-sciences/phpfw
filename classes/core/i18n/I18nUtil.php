<?php
/*
 * Created on Jan 21, 2008
 * Author: Yoni Rosenbaum
 * 
 */

class I18nUtil implements ITranslator {
    const UNDEFINED = "___UNDEFINED___";
    const DEFAULT_LOCALE = "en";
    const DIR_LOCALES = '../application/i18n/';
    const DEFAULT_BUNDLENAME = 'strings.xml';
    /**
     * @var Array map bundlePath -> Map(string->string)
     */
    private static $bundles = array();
    private static $bundleLocale = self::DEFAULT_LOCALE;
    /**
     * Retrieve the string corresponding to the given stringId and bundleName.
     * If stringId is not available but $defaultVal is pass return the default value.
     * @param string $bundleName
     * @param string $stringId
     * @param string $defaultVal (optional and prevent exception)
     * @return LocalizedString
     */
    public static function lookup($bundleName, $stringId, $defaultVal=self::UNDEFINED) {
        $bundlePath = self::getBundlePath($bundleName);
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
    
    /**
     * Return the bundle path
     * @param string $bundleName
     * @return string
     */
    private static function getBundlePath($bundleName) {
        $bundlePath = "application/i18n/". self::$bundleLocale .'/'. $bundleName . ".xml";
        // If the locale is "fr_FR", we check if the dir "fr_FR" exists, else we use only the language ("fr")
        if (FileUtils::existsInIncludePath($bundlePath) === false) {
            $bundlePath = "application/i18n/". self::getDefaultLanguage() .'/'. $bundleName . ".xml";
        }
        // if it still doesn't exists, we use the default locale which we're pretty sure it exists.
        if (FileUtils::existsInIncludePath($bundlePath) === false) {
            $bundlePath = "application/i18n/". self::DEFAULT_LOCALE .'/'. $bundleName . ".xml";
        }
        return $bundlePath;
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
    
    /**
     * Return the language defined in the locale.
     * By example if the locale is "en_US", this method returns "en"
     * @return string
     */
    public function getLanguage() {
        return self::getDefaultLanguage();
    }
    
    public static function getDefaultLanguage() {
        $locale = explode('_', self::$bundleLocale);
        return $locale[0];
    }
    
    public function setLocale($locale) {
        self::setDefaultLocale($locale);
    }
    
    public function getLocale() {
        return self::$bundleLocale;
    }
    
    public static function setDefaultLocale($locale) {
        self::$bundleLocale = (string)$locale;
    }
    
    public function _($sentence) {
        return self::lookupString($sentence, $sentence)->__toString();
    }
    
    /**
     * Get all the locales available in the self::DIR_LOCALES directory
     * @return array
     */
    public function getAvailableLocales() {
        $items = glob(self::DIR_LOCALES .'*');
        $locales = array();
        foreach ($items as $item) {
            if (is_dir($item)) {
                $locale = substr($item, strrpos($item, '/') + 1);
                if (file_exists(self::DIR_LOCALES . $locale .'/'. self::DEFAULT_BUNDLENAME)) {
                    $locales[] = $locale;
                }
            }
        }
        return $locales;
    }
}