<?php

namespace App;

use DateTime;

/**
 * 
 * PHP version 7.0
 * 
 */

class Time
{

    /** Return current date
     * 
     * @return date (Y-m-d)
     */
    public static function getCurrentDate()
    {
        $date = new DateTime();
        return $date->format('Y-m-d');
    }

    /** Validate date
     * @param date, string format
     * @return boolean
     */
    static function checkDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}
