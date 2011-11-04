<?php
/*
 * 
 * Created on Nov 02, 2011
 * Author: Ugo Fillastre
 */

/**
 * 
 * Definition of type Polygon
 * Associated with the MySQL Polygon Type (geometric type)
 *
 */
class GeomPolygon{
    /**
     * WKT format
     * @var String 
     */
    private $wkt; 
    
    /**
     * .
     * @param String $strWKT : WKT Format
     * @throws IllegalArgumentException : Try to construct a GeomPolygon with a null WKT value
     */
    public function __construct($strWKT) {
        if(empty($strWKT)){
            Logger::error("WKT cannot be null");
            throw new IllegalArgumentException();
        }
        $this->wkt = $strWKT;
    }
    
    /**
     * 
     * Return WKT of the polygon type
     * http://dev.mysql.com/doc/refman/5.0/en/gis-wkt-format.html
     * 
     * @return string
     */
    public function toWKT(){
        return $this->wkt;
    }

    public function __toString(){
        return $this->toWKT();
    } 
}
