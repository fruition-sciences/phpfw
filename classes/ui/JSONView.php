<?php
/*
 * Created on Feb 5, 2009
 * Author: Yoni Rosenbaum
 *
 */

class JSONView extends BaseView {
    private $doc; // php structure
    
    /**
     * If true, it indicates that we want to send/display the json response
     * immediately and before the end of the current php script.
     * @var boolean
     */
    private $displayResponseImmediately = false;
    
    /**
     * If true display the content-type header.
     * Else, do not display it to resolve issue with IE8/9 when the request is an
     * ajax form submit. In this case IE will display the JSON response as a text file
     * instead of passing it to the JS script.
     * 
     * @var boolean
     */
    private $displayContentTypeHeader = true;
    
    public function __construct($doc) {
        $this->doc = $doc;
    }

    public function render($ctx) {
        if ($this->displayResponseImmediately) {
            header("Connection: close");
            header("Content-Length: " . mb_strlen(json_encode($this->doc)));
        } 
        if ($this->displayContentTypeHeader) {
            header("Content-Type: application/json ; encoding=utf-8");
        }
        echo json_encode($this->doc);
        if ($this->displayResponseImmediately) {
            flush();
        }
    }
    
    /**
     * the render will send the response to the client of the initial request 
     * before the end of the script.
     * This way we don't have to wait until a long script is finished.
     * @example client requests a long php task -> server start the long job.
     *          the script return immediatly to the client: ok I will do the work.
     *          the client know its ok
     *          the php script continue to process the long task.
     * Warning: When used, it is not possible to send an other response to the client.
     * So anything printed after this call will not be send to the client.
     */
    public function displayResponseImmediately() {
        $this->displayResponseImmediately = true;
    }
    
    /**
     * Its avoid to display content-type header.
     */
    public function notDisplayContentTypeHeader() {
        $this->displayContentTypeHeader = false;
    }

    protected function setDoc($doc) {
        $this->doc = $doc;
    }

    protected function getDoc() {
        return $this->doc;
    }
}