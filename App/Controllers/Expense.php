<?php

namespace App\Controllers;

use App\Models\Expenses;
use Core\View;
use \App\Time;

/**
 * 
 * PHP version 7.0
 * 
 */

//Expense controller

class Expense extends Authenticated
{

    /**
     * Render the expense page
     *
     * @return void
     */
    public function indexAction($arguments = [])
    {
        $arguments['paymentMethods'] = Expenses::getPaymentMethods();
        $arguments['expensesCategory'] = Expenses::getExpensesCategory();
        $arguments['currentDate'] = Time::getCurrentDate();
        View::renderTemplate('Expense/index.html', $arguments);
    }

    //Add a new expense
    public function newAction()
    {
        echo "new action";
    }

    //Show an expense
    public function showAction()
    {
        echo "show action";
    }
}