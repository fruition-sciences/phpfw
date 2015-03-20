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
        $point->x = (double)$x;
        $point->y = (double)$y;
        return $point;
    }

    /**
     * Parse a string in format such as: POINT(-122.3340921148 38.421022632969)
     *
     * @param String $wkt
     * @return NULL|GeomPoint
     * @throws IllegalArgumentException if the given string cannot be parsed as a point.
     */
    public static function fromWKT($wkt) {
        if (empty($wkt)) {
            return null;
        }
        $ret = preg_match('/^POINT\((.*) (.*)\)$/', $wkt, $matches);
        if (!ret) {
            throw new IllegalArgumentException("Invalid POINT format: $wkt");
        }
        return self::fromXY($matches[1], $matches[2]);
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
