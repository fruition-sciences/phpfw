<?php
/*
 * Created on Dec 13, 2008
 * Author: Yoni Rosenbaum
 *
 * Substitution for DateTime and DateTimeZone classes, which are available only
 * from PHP 5.2
 */

if (!class_exists('DateTime')) {
    class DateTime {
        private $date; // UNIX timestamp
        private $dateTimeZone; // DateTimeZone

        public function DateTime($formattedDateTime) {
            $this->date = strtotime($formattedDateTime);
        }

        public function setTimezone($dateTimeZone) {
            $this->dateTimeZone = $dateTimeZone;
        }

        public function format($format) {
            return date($format, $this->date);
        }
    }

    class DateTimeZone {
        private $timezoneName;

        public function DateTimeZone($timezoneName) {
            $this->timezoneName = $timezoneName;
        }

        public function getName() {
            return $this->timezoneName;
        }
    }
}
