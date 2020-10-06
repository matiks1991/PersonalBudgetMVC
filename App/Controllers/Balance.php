<?php

namespace App\Controllers;

use App\Models\Balances;
use Core\View;

/**
 * 
 * PHP version 7.0
 * 
 */

//Balance controller

class Balance extends Authenticated
{

    /**
     * Render the current month the balance page
     * 
     * @return void
     */
    public function indexAction()
    {
        $arguments = Balances::getCurrentMonth();
        View::renderTemplate('Balance/index.html', $arguments);
    }

    /**
     * Render the previous month the balance page
     * 
     * @return void
     */
    public function previousMonthAction()
    {
        $arguments = Balances::getPreviousMonth();
        View::renderTemplate('Balance/index.html', $arguments);
    }

    /**
     * Render the current year the balance page
     * 
     * @return void
     */
    public function currentYearAction()
    {
        $arguments = Balances::getcurrentYear();
        View::renderTemplate('Balance/index.html', $arguments);
    }

}