<?php
/*
 * Created on Jun 27, 2008
 * Author: Yoni Rosenbaum
 *
 */

class DateUtils {
    /**
     * Get a number representing the hour of the day [0-23] of the given unix
     * timestamp in the given timezone.
     *
     * @param long $date unix timestamp
     * @param String $timezone
     */
    public static function getHourOfDay($timestamp, $timezone) {
        $date = new DateTime(date('c', $timestamp));
        $tz = new DateTimeZone($timezone);
        $date->setTimezone($tz);
        return intval($date->format("G"));
    }

    /**
     * Add the given number of days to the given timestamp, and optionally, set
     * the time of the day to the given hours, minutes and seconds.
     *
     * @param long $timestamp unix timestamp
     * @param long $daysToAdd number of days to add (or substract, if negative)
     * @param long $hours (optional) the hour of the day to set.
     * @param long $minutes (optional) the minute of the day to set.
     * @param long $seconds (optional) the second of the day to set.
     * @param String $timezone the timezone to evaluate the given time in. If null,
     *        the current user's account's timezone will be used.
     * @return long unix timestamp
     */
    public static function addDays($timestamp, $daysToAdd, $hours=null, $minutes=null, $seconds=null, $timezone=null) {
        if (!$timezone) {
            $timezone = Transaction::getInstance()->getUser()->getTimezone();
        }
        $date = new DateTime("@$timestamp");
        $tz = new DateTimeZone($timezone);
        $date->setTimezone($tz);
        if ($hours !== null || $minutes !== null || $seconds !== null) {
            if ($hours === null) {
                $hours = $date->format('g');
            }
            if ($minutes === null) {
                $minutes = $date->format('i');
            }
            if ($seconds === null) {
                $seconds = $date->format('s');
            }
            $date->setTime($hours, $minutes, $seconds);
        }
        $date->modify("$daysToAdd day");
        return self::dateTimeToTimestamp($date);
    }

    /**
     * Add given quantity of unit to the given time.
     * Unit examples: hour, day, month, year, etc.
     * 
     * @param $timestamp
     * @param $unit String
     * @param $quantity number
     * @param $timezone String
     * @return unix timestamp
     */
    public static function add($timestamp, $unit, $quantity, $timezone=null) {
        $date = self::makeDateFromTimestamp($timestamp, $timezone);
        $date->modify("$quantity $unit");
        return self::dateTimeToTimestamp($date); 
    }

    /**
     * Get a unix timestamp representing 12AM of the given date in the given
     * timezone.
     *
     * @param long $date unix timestamp.
     * @param String $timezone time zone code.
     * @return long unix timestamp
     */
    public static function getBeginningOfDay($timestamp, $timezone) {
        return self::addDays($timestamp, 0, 0, 0, 0, $timezone);
    }

    /**
     * Get a unix timestamp representing 12AM of the previous date in the given
     * timezone.
     *
     * @param long $date unix timestamp.
     * @param String $timezone time zone code.
     * @return long unix timestamp
     */
    public static function getBeginningOfPreviousDay($timestamp, $timezone) {
        return self::addDays($timestamp, -1, 0, 0, 0, $timezone);
    }
    
     /**
     * Get a unix timestamp representing 12AM of the first day of the week of the
     * given date in the the given timezone.
     * Assumes that the first day of the week is Monday.
     * Note: Requires (PHP 5 >= 5.1.0)
     *
     * @param long $date unix timestamp.
     * @param String $timezone time zone code.
     * @return long unix timestamp
     */
    public static function getBeginningOfWeek($timestamp, $timezone) {
        $dt = DateUtils::makeDateFromTimestamp($timestamp, $timezone);
        // 1=Monday,... 7=Sunday
        $dayOfWeek = intval($dt->format('N')); // (PHP 5 >= 5.1.0)
        $daysDiff = $dayOfWeek - 1;
        $dt->modify("-$daysDiff day");
        $dt->setTime(0, 0, 0);
        return self::dateTimeToTimestamp($dt);
    } 

    /**
     * Get the first day of the month of the given timestamp. Time is set to
     * minnight.
     * 
     * @param $timestamp
     * @param $timezone
     * @return unix timestamp
     */
    public static function getBeginningOfMonth($timestamp, $timezone) {
        $date = self::makeDateFromTimestamp($timestamp, $timezone);
        $year = $date->format('Y');
        $month = $date->format('n');        
        $date->setDate($year, $month, 1);
        $date->setTime(0, 0, 0);
        return self::dateTimeToTimestamp($date);
    }

    /**
     * Calculate time difference between 2 time stamps.
     * Returns a formatted string containing the difference.
     *
     * @param long $startTime unix timestamp
     * @param long $endTime (optional) unix timestamp. Default is current time.
     * @return String string containing hours minutes and seconds.
     */
	public static function timeDiff($startTime, $endTime = null) {
	    $endTime = $endTime ? $endTime : time();

        $diff = $endTime - $startTime;
        $sign = "";
        if ($diff < 0) {
            $sign = "-";
            $diff = -$diff;
        }
        $hours = floor($diff/3600);
        $diff = $diff % 3600;

        $minutes = floor($diff/60);
        $diff = $diff % 60;

        $seconds = $diff;

        return $sign . str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT) . ':' . str_pad($seconds, 2, '0', STR_PAD_LEFT);
	}
	
    /**
     * Modify a timestamp using strtotime() textual datetime description.
     * @param $timestamp Timestamp to modify
     * @param $modifier String in a relative format accepted by strtotime(). ex : "+1 day", "+1 week 2 days 4 hours 2 seconds", "next Thursday"
     * @return Long modified Timestamp
     */
    public static function modifyTimestamp($timestamp, $modifier, $timezone=null){
        $dateTime = new DateTime(date('c', $timestamp));
        if($timezone){
            $tz = new DateTimeZone($timezone);
            $dateTime->setTimezone($tz);
        }
        $dateTime->modify($modifier);
        return $dateTime->format("U");
    }
    
    /**
     * Return an array containing all the months
     * The key is the month number and the value is the formatted month
     * @param string $format "abbreviated" ("jan.") or "wide" ("janvier").
     * @return array containing the 12 months. January is in index 1.
     */
    public static function getMonthsArray($format){
        $zendMonthArr = Zend_Locale::getTranslationList("months", Application::getTranslator()->getLocale());
        return $zendMonthArr["format"][$format];
    }

    /**
     * Create a new DateTime object representing the given date/time in the given
     * timezone.
     *
     * @param $year int
     * @param $month int
     * @param $day int
     * @param $hour int
     * @param $minute int
     * @param $second int
     * @param $timezone (String) the timezone to evaluate the given time in. If
     *        null, the current user's account's timezone will be used.
     * @return DateTime The PHP DateTime object. Call DateUtils::dateTimeToTimestamp() to get the unix timestamp. 
     */
    public static function makeDate($year, $month, $day, $hour=0, $minute=0, $second=0, $timezone=null) {
        $date = self::makeDateFromTimestamp(time(), $timezone);
        $date->setDate($year, $month, $day);
        $date->setTime($hour, $minute, $second);
        return $date;
    }

    /**
     * Create a new DateTime object representing the current date/time in the
     * given timezone.
     * 
     * @param $timezone (String) the timezone to evaluate the given time in. If
     *        null, the current user's account's timezone will be used.
     * @return DateTime
     */
    public static function makeDateFromTimestamp($timestamp, $timezone=null) {
        if (!$timezone) {
            $timezone = Transaction::getInstance()->getUser()->getTimezone();
        }
        $date = new DateTime(date('c', $timestamp));
        $tz = new DateTimeZone($timezone);
        $date->setTimezone($tz);
        return $date;
    }

    /**
     * Convert a DateTime object to a unix timestamp.
     * Equivalent to DateTime::getTimestamp (PHP 5 >= 5.3.0)
     * 
     * @param $dateTime
     * @return long unix timestamp
     */
    public static function dateTimeToTimestamp($dateTime) {
        return (int)$dateTime->format('U');
    }
    
    /**
     * Returns the timezone offset from GMT in seconds
     * @param int $timestamp
     * @param string $timezone
     * @return int offset in seconds
     */
    public static function getGmtOffset($timestamp, $timezone){
        $date = self::makeDateFromTimestamp($timestamp, $timezone);
        return $date->getOffset();
    }

    /**
     * Check whether the 2 given timestamp represent the same day in the given
     * timezone.
     * 
     * @param long $timestamp1
     * @param long $timestamp2
     * @param string $timezone
     */
    public static function isSameDay($timestamp1, $timestamp2, $timezone) {
        $date1 = self::makeDateFromTimestamp($timestamp1, $timezone);
        $date2 = self::makeDateFromTimestamp($timestamp2, $timezone);
        return $date1->format('Y-m-d') == $date2->format('Y-m-d');
    }
    
    /**
     * 
     * check if a time is inside a time interval.
     * It can check for time frames that cross midnight too.
     * 
     * @param int $h  the hour of the time you want to check 
     * @param int $m  the minute of the time you want to check 
     * @param int $h1 the hour of the lower endpoint of the timeframe 
     * @param int $m1 the minute of the lower endpoint of the timeframe 
     * @param int $h2 the hour of the upper endpoint of the timeframe
     * @param int $m2 the minute of the upper endpoint of the timeframe
     * @return boolean return true (if our time is in the timeframe) or false.
     */
    public static function timeInInterval($h,$m,$h1,$m1,$h2,$m2){
    	//filter the parameters
    	$h = intval($h);
    	$m = intval($m);
    	$h1 = intval($h1);
    	$m1 = intval($m1);
    	$h2 = intval($h2);
    	$m2 = intval($m2);
    	//cases like 00:00 - 14:00
    	if ($h1 < $h2){
    		if (($h < $h1) || ($h > $h2)){
    			return false;
    		} else if (($h == $h1) || ($h == $h2)) {
    			if ($h == $h1){
    				if ($m < $m1){
    					return false;
    				}
    			} else {
    				if ($m > $m2){
    					return false;
    				}
    			}
    		}
    	}
    	//cases like 12:00 - 12:30 and 12:30-12:00
    	if ($h1 == $h2){
    		if ($m1 > $m2){
    			//split it into 2 intervals 
    			//12:30-12:00 => 12:30-23:59 and 00:00-12:00
    			$tmp1 = self::timeInInterval($h,$m,$h1,$m1,23,59);
    			$tmp2 = self::timeInInterval($h,$m,0,0,$h2,$m2);
    			if (($tmp1 == 0) && ($tmp2 == 0)){
    				return false;
    			}
    		} else {
    			if ($h != $h2){
    				return false;
    			} else if (($m > $m2) || ($m < $m1)) {
    				return false;
    			}
    		}
    	}
    	//cases like 08:00 - 01:00
    	if ($h1 > $h2){
    		//split it into 2 intervals : 08:00 - 23:59 and 00:00-01:00
    		$tmp1 = self::timeInInterval($h,$m,$h1,$m1,23,59);
    		$tmp2 = self::timeInInterval($h,$m,0,0,$h2,$m2);
    		if (($tmp1 == 0) && ($tmp2 == 0)){
    			return false;
    		}
    	}
    	return true;
    }

    

    /**
     * Convert from an Excel decimal datetime number to a unix timestamp.
     * 
     * Excel holds date values as the "real" number of days since a base date, which 
     * can be either 1st January 1900 (the default for Windows versions of Excel) or 
     * 1st January 1904 (the default for Mac versions of Excel): 
     * the time is the fractional part, so midday on any given date is 0.5 greater 
     * than midnight. To add to the misery, Feb29th 1900 is a valid date for the 
     * Windows 1900 calendar.
     * 
     * @see http://stackoverflow.com/questions/9298429/date-from-excel-changes-when-uploaded-into-mysql
     * @param float $excelDateTime
     * @param boolean $isMacExcel
     * @return long Unix Timestamp
     */
    public static function excelToTimestamp($excelDateTime, $isMacExcel=false) {
        $myExcelBaseDate = $isMacExcel ? 24107 : 25569; // 1st jan 1904 or 1st jan 1900
        if (!$isMacExcel && $excelDateTime < 60) {
            //  Adjust for the spurious 29-Feb-1900 (Day 60)
            --$myExcelBaseDate;
        }
        // Perform conversion
        if ($excelDateTime >= 1) {
            $timestampDays = $excelDateTime - $myExcelBaseDate;
            $timestamp = round($timestampDays * 86400);
            if (($timestamp <= PHP_INT_MAX) && ($timestamp >= -PHP_INT_MAX)) {
                $timestamp = intval($timestamp);
            }
        } else {
            $hours = round($excelDateTime * 24);
            $mins = round($excelDateTime * 1440) - round($hours * 60);
            $secs = round($excelDateTime * 86400) - round($hours * 3600) - round($mins * 60);
            $timestamp = (integer) gmmktime($hours, $mins, $secs);
        }
        return $timestamp;
    }
    
    /**
     * Convert the given formatted date/time into a timestamp using the given
     * timezone.
     * The format accepted are described here : 
     * http://www.php.net/manual/en/datetime.formats.php
     * 
     * @param string $formattedDate
     * @param string $timezone
     * @return int unix timestamp
     */
    public static function parseDate($formattedDate, $timezone) {
        $date = new DateTime($formattedDate, new DateTimeZone($timezone));
        return $date->format('U');
    }
}