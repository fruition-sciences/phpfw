<?php
/*
 * Created on Feb 26, 2010
 * Author: Yoni Rosenbaum
 *
 * Thrown after redirecting.
 * Should be caught at the application level and should be ignored.
 */

class EndOfResponseException extends Exception {
    
}