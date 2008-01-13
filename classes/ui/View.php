<?php
/*
 * Created on Jul 8, 2007
 * Author: Yoni Rosenbaum
 * 
 */

interface View {
    public function init($ctx);
    
    public function prepare($ctx);

    public function render($ctx);
}