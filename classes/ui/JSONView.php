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
    
    public function __construct($doc = null) {
        $this->doc = $doc;
    }

    public function render($ctx) {
        $jsonDoc = json_encode($this->doc);
        if ($this->displayResponseImmediately) {
           $jsonDoc = $this->headerDisplayResponseImmediately($jsonDoc);
        } 
        if ($this->displayContentTypeHeader) {
            header("Content-Type: application/json ; encoding=utf-8");
        }
        echo $jsonDoc;
        if ($this->displayResponseImmediately) {
            flush();
            ob_flush();
            ob_implicit_flush(false);
        }
    }
    
    /**
     * The render will send the response to the client of the initial request 
     * before the end of the script.
     * This way we don't have to wait until a long script is finished.
     * @example client requests a long php task -> server start the long job.
     *          the script return immediatly to the client: ok I will do the work.
     *          the client knows its ok
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
    
    /**
	 * Display headers and compress jsonDoc to send it immediately
	 * to the client. Withou that the response is only received when the 
	 * long job is completely done. 
	 * 
	 * Important: if this doesn't work properly, the 
	 * cause could be the output buffer defined in php.ini.
	 *
     * We have to be sure that if there is an output buffer,
     * it has to be flushed when the response is displayed. To do that,
     * We have to set the ob_implicit_flush to true to ensure each echo
     * will flush  buffer. As there are differents buffers, we have to
     * manually flush the global output buffer and ob_buffer after the display.
     *
     * About headers,
     *  -Content-Encoding is to force gzip. Depending on the size of the content to send
     *      Apache will set the encoding to gzip, but without sending the content-Length
     *      that cause the Transfer-Encoding to chang to chunked and the client can't
     *      get the response before the end of the php script.
     *  -Content-Length allow the client to know when the response is received
     *      and stop waiting for data.
     *  -All those lines are mandatory to the proper process.
     *  @param String $jsonDoc the json-encoded php structure
     *  @return String $jsonDoc the gziped json-encoded php structure
     */
    protected function headerDisplayResponseImmediately($jsonDoc) {
        ob_implicit_flush(true);
        header("Connection: close");
        if(!empty($_SERVER["HTTP_ACCEPT_ENCODING"]) && strpos("gzip",$_SERVER["HTTP_ACCEPT_ENCODING"]) !== NULL) {
            header("Content-Encoding: gzip");
            $jsonDoc = gzencode($jsonDoc, -1);
        }
        header("Content-Length: " . mb_strlen($jsonDoc));
        return $jsonDoc;
    }
}