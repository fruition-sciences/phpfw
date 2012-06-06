<?php
/* 
 * Created on Nov 02, 2011
 * Author: Ugo Fillastre
 */

/**
 * Represents a 2D Point.
 * Associated with the MySQL Point Type (geometric type)
 *
 */
class GeomPoint {
    const NORTHERN_HEMISPHERE = 1;
    const SOUTHERN_HEMISPHERE = 2;
    
    /**
     * Longitude
     * @var Double
     */
    private $x;
    
    /**
     * Latitude
     * @var Double
     */
    private $y;

    /**
     * Private constrctor. Use fromXY or fromWKT to create a new GeomPoint
     */
    private function __construct() {
    }

    public static function fromXY($x, $y) {
        $point = new GeomPoint();
        $point->x = $x;
        $point->y = $y;
        return $point;
    }

    public static function fromWKT($wkt) {
        if (empty($wkt)){
            Logger::error("WKT cannot be null");
            throw new IllegalArgumentException();
        } else {
            $tab = explode(" ",$wkt);
            $point = new GeomPoint();
            $point->x = substr($tab[0], 6);
            $point->y = substr($tab[1], 0, -1);
            return $point;
        }
    }

    /** 
     * Return WKT of the point type
     * http://dev.mysql.com/doc/refman/5.0/en/gis-wkt-format.html
     * 
     * @return String
     */
    public function toWKT() {
        return "POINT(" . $this->x . " " . $this->y . ")";
    }
    
    public function __toString() {
        return $this->toWKT();
    }
    
    public function getX() {
        return $this->x;
    }
    
    public function getY() {
        return $this->y;
    }
    
    public function getHemisphere(){
        if($this->getY() >= 0){
            return self::NORTHERN_HEMISPHERE;
        }
        return self::SOUTHERN_HEMISPHERE;
    }
}
