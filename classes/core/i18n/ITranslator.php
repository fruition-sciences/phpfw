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
     * Return the translated sentence
     * @param string $sentence
     * @return string
     */
    public function _($sentence);
}
?>