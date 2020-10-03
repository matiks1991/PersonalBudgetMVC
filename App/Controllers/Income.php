<?php

namespace App\Controllers;

use App\Flash;
use App\Models\Incomes;
use Core\View;
use \App\Time;

/**
 * 
 * PHP version 7.0
 * 
 */

//Income controller

class Income extends Authenticated
{

    /**
     * Render the income page
     * @param arguments
     * @return void
     */
    public function indexAction($arguments = [])
    {
        $arguments['incomesCategory'] = Incomes::getIncomesCategory();
        $arguments['date'] = Time::getCurrentDate();
        View::renderTemplate('Income/index.html', $arguments);
    }

    /**
     * Add a new income
     * @param arguments
     * @return void
     */
    public function newAction($arguments = [])
    {
        $success = Incomes::saveIncome();

        if ($success)
            Flash::addMessage('Przychód dodano pomyślenie!');

        $arguments['incomesCategory'] = Incomes::getIncomesCategory();
        $arguments['amount'] = $_POST['amount'];
        $arguments['date'] = $_POST['date'];
        $arguments['comment'] = $_POST['comment'];

        View::renderTemplate('Income/index.html', $arguments);
    }

}