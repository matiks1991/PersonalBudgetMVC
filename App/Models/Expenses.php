<?php

namespace App\Models;

use App\Flash;
use App\Time;
use PDO;

/**
 * Expenses Model
 *
 * PHP 7.0
 */

 class Expenses extends \Core\Model
 {

    /**
     * Get payment methods
     * @param Session user_id
     * @return array
     */
    public static function getPaymentMethods()
    {
        $sql = 'SELECT id, name FROM payment_methods_assigned_to_users WHERE user_id='.$_SESSION['user_id'];

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get category expenses
     * @param Session user_id
     * @return array
     */
    public static function getExpensesCategory()
    {
        $sql = 'SELECT id, name FROM expenses_category_assigned_to_users WHERE user_id='.$_SESSION['user_id'];

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Save a expense
     * @param form method post
     * @return boolean success = true
     */
    public static function saveExpense()
    {
        if (static::validateExpense()) {
            $comment = htmlentities($_POST['comment']);

            $sql = "INSERT INTO expenses ( id, user_id, expense_category, payment_method, amount, date, comment) VALUES ( 'NULL', :user_id, :category, :paymentMethod, :amount, :date, :comment)";

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            
            $stmt->bindValue(':amount', $_POST['amount'], PDO::PARAM_STR);
            $stmt->bindValue(':date', $_POST['date'], PDO::PARAM_STR);
            $stmt->bindValue(':paymentMethod', $_POST['paymentMethod'], PDO::PARAM_INT);
            $stmt->bindValue(':category', $_POST['expenseCategory'], PDO::PARAM_INT);
            $stmt->bindValue(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            
            return $stmt->execute();
        }

        return false;
    }

    /**
     * Validate a expense
     * @param form method post
     * @return boolean success = true
     */
    protected static function validateExpense()
    {
        $result = true;

        if ((!isset($_POST['amount'])) || (!isset($_POST['date'])) || (!isset($_POST['paymentMethod'])) || (!isset($_POST['expenseCategory']))) {
            Flash::addMessage('Wypełnij wszystkie wymagane pola!', Flash::WARNING);
            $result = false;
        }

        if(!is_numeric($_POST['amount'])) {
            Flash::addMessage('Wprowadzona kwota musi być liczbą!', Flash::WARNING);
            $result = false;
        }

        if(strlen($_POST['amount']) > 8) {
            Flash::addMessage('Wprowadzona kwota może mieć maksymalnie 8 znaków!', Flash::WARNING);
            $result = false;
        }

        if (!preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $_POST['date']))
        {
            Flash::addMessage('Wprowadź prawidłowy format daty! [DD.MM.RRRR]', Flash::WARNING);
            $result = false;
        } elseif( !Time::checkDate($_POST['date']) ) {
            Flash::addMessage('Wprowadź rzeczywistą datę!', Flash::WARNING);
            $result = false;
        }

        return $result;
    }
 }