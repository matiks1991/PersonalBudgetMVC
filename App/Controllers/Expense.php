<?php

namespace App\Controllers;

use App\Flash;
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
     * @param arguments
     * @return void
     */
    public function indexAction($arguments = [])
    {
        $arguments['paymentMethods'] = Expenses::getPaymentMethods();
        $arguments['expensesCategory'] = Expenses::getExpensesCategory();
        $arguments['date'] = Time::getCurrentDate();
        View::renderTemplate('Expense/index.html', $arguments);
    }

    /**
     * Add a new expense
     * @param arguments
     * @return void
     */
    public function newAction($arguments = [])
    {
        $success = Expenses::saveExpense();

        if ($success)
            Flash::addMessage('Wydatek dodano pomyślenie!');

        $arguments['paymentMethods'] = Expenses::getPaymentMethods();
        $arguments['expensesCategory'] = Expenses::getExpensesCategory();
        $arguments['amount'] = $_POST['amount'];
        $arguments['date'] = $_POST['date'];
        $arguments['comment'] = $_POST['comment'];

        View::renderTemplate('Expense/index.html', $arguments);
    }

}