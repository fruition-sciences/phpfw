<?php
/*
 * Created on Aug 10, 2012
* Author: Julien Fouilhé
*
* Translator using Gettext and Zend_Locale.
* To use it just put the class name in a "translator" property.
*/

require_once('ITranslator.php');

class GettextZendTranslator extends Zend_Translate implements ITranslator {

    const GETTEXT = 'gettext';
    /**
     * Relative (from your application root directory) path to the locale directory
     * @var string
     */
    const DIR_LOCALES = '/application/i18n/';
    const DEFAULT_LOCALE = 'en';
    protected $filename = 'default.mo';
    protected $locale = self::DEFAULT_LOCALE;

    private static $instance = null;
    protected static $absolutePath = self::DIR_LOCALES;

    /**
     * Create a new GettextZendTranslator and configure it with passed params
     * @param string|Zend_Locale $given_locale
     * @param string $filename
     * @param boolean $default_instance If none instance yet, this instance will be used whatever this param value is
     */
    public function __construct($given_locale=null, $filename=null, $default_instance=true) {
        if ($filename != null) {
            $this->filename = $filename;
        }
        self::$absolutePath = Config::getInstance()->getString('appRootDir') . self::DIR_LOCALES;
        $path = self::$absolutePath . self::DEFAULT_LOCALE .'/'. $this->filename;
        parent::__construct(self::GETTEXT, $path, self::DEFAULT_LOCALE);
        // Adding other existing locales
        $locales = $this->getAvailableLocales();
        foreach ($locales as $locale) {
            if ($locale != self::DEFAULT_LOCALE) {
                parent::addTranslation(self::$absolutePath . $locale .'/'. $this->filename, $locale);
            }
        }
        if ($given_locale == null) {
            if (($given_locale = Zend_Registry::get('Zend_Locale')) == null) {
                $given_locale = self::DEFAULT_LOCALE;
            }
        }
        $this->setLocale($given_locale);
        Zend_Registry::set('Zend_Translator', $this);
        if ($default_instance || self::$instance == null) {
            self::$instance = $this;
        }
    }
    
    /**
     * Get all the locales available in the self::DIR_LOCALES directory
     * @return array
     */
    public function getAvailableLocales() {
        $items = glob(self::$absolutePath .'*');
        $locales = array();
        foreach ($items as $item) {
            if (is_dir($item)) {
                $locale = substr($item, strrpos($item, '/') + 1);
                if (file_exists(self::$absolutePath . $locale .'/'. $this->filename)) {
                    $locales[] = $locale;
                }
            }
        }
        return $locales;
    }
    
    public function getLocale() {
        return $this->locale;
    }
    
    /**
     * Return the language defined in the locale. 
     * By example if the locale is "en_US", this method returns "en"
     * @return string
     */
    public function getLanguage() {
        $locale = explode('_', $this->locale);
        return $locale[0];
    }
    
    public function setLocale($locale) {
        if (!empty($locale)) {
            $this->locale = $locale;
            parent::setLocale($locale);
        }
    }

    public function _($sentence) {
        return parent::_($sentence);
    }
    
    /**
     * @return GettextZendTranslator
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new GettextZendTranslator();
        }
        return self::$_instance;
    }
}