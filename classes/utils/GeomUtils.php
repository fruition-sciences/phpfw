<?php
/*
 * 
 * Created on Oct 25, 2011
 * Author: Ugo Fillastre
 */

/**
 * 
 *Definition of utils used by geometric types : Point and Polygon
 *
 */
class GeomUtils{
    
    /**
     * Find longitude and latitude from point
     * 
     * @param String $strPoint, format WKT : POINT(X Y) or POLYGON((X1 Y1, .... , Xn Yn , X1 Y1)) 
     * 
     * @return array $XY [longitude,latitude]
     */
    public static function findXYfromPoint($strPoint) {
        if($strPoint ==  Null || $strPoint ==  ""){
            return null;
        }
        $XY = array();
        $tab = explode(" ",$strPoint);
        //get Longitude
        $XY[0] = substr($tab[0],6);
        //get Latitude
        $XY[1] = substr($tab[1],0,-1);
        return $XY;
    }
    
}
