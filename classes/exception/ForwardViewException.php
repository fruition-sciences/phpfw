<?php
/*
 * May be thrown by controller methods in order to force forwarding to a
 * specific View.
 * 
 * Controller methods return a View. However, they can also throw this Exception
 * which contains a view. The result will be the same.
 * 
 * The reason a controller method may throw a 'view' rather than returning it
 * is in case the decision to 'forward' to this view is made deeper in the call
 * path and not at the top level of the controller method.
 * 
 * This is similar to the EndOfResponseException which handles redirect.
 *  
 * Created on Jan 9, 2011
 * Author: Yoni Rosenbaum
 */

class ForwardViewException extends Exception {
    private $view;

    /** 
     * @param $view View
     * @return ForwardViewException
     */
    public function __construct($view) {
        $this->view=$view;
    }

    public function getView() {
        return $this->view;
    }
}