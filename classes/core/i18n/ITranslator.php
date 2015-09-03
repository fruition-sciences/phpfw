<?php
/**
 * 
 * Created on Aug 13, 2012
 * @author Julien Fouilhé
 * 
 * Interface to implement for all translator classes.
 *
 */
interface ITranslator {

    /**
     * Set the wanted locale
     */
    public function setLocale($locale);
    
    /**
     * Get the current locale
     */
    public function getLocale();
    
    /**
     * Return the language defined in the locale.
     * By example if the locale is "en_US", this method returns "en"
     * @return string
     */
    public function getLanguage();

    /**
     * Return the translated sentence
     * @param string $sentence
     * @return string
     */
    public function _($sentence);
    
    /**
     * Return an array which contains all the available locales
     * @return array of locales
     */
    public function getAvailableLocales();
    
}
?>