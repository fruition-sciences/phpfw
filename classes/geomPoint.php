<?php
/*
 * 
 * Created on Nov 02, 2011
 * Author: Ugo Fillastre
 */

/**
 * 
 * Definition of type Point
 * Associated with the MySQL Point Type (geometric type)
 *
 */
class GeomPoint{
    
    /**
     * Longitude
     * @var Double
     */
    private $X;
    
    /**
     * Latitude
     * @var Double
     */
    private $Y;
    
    /**
     * 
     * @param String $wkt : WKT format
     * @throws IllegalArgumentException : Try to construct a GeomPoint with a null WKT value
     */
    public function __construct($wkt) {
        if(empty($wkt)){
            Logger::error("WKT cannot be null");
            throw new IllegalArgumentException();
        }else{
            $tab = explode(" ",$wkt);
            $this->X = substr($tab[0],6);
            $this->Y = substr($tab[1],0,-1);
        }
    }
    
    /**
     * 
     * Return WKT of the point type
     * http://dev.mysql.com/doc/refman/5.0/en/gis-wkt-format.html
     * 
     * @return String
     */
    public function toWKT() {
        return "POINT(" . $this->X . " " . $this->Y . ")";
    }
    
    public function __toString() {
        return $this->toWKT();
    }
    
    public function getX(){
        return $this->X;
    }
    
    public function getY(){
        return $this->Y;
    }
    
}
