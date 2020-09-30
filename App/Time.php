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

    //Return curren date
    public static function getCurrentDate()
    {
        $date = new DateTime();
        return $date->format('Y-m-d');
    }
}
