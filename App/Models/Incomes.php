<?php

namespace App\Models;

use App\Flash;
use App\Time;
use PDO;

/**
 * Incomes Model
 *
 * PHP 7.0
 */

 class Incomes extends \Core\Model
 {
    /**
     * Get category incomes
     * @param Session user_id
     * @return array
     */
    public static function getIncomesCategory()
    {
        $sql = 'SELECT id, name FROM incomes_category_assigned_to_users WHERE user_id='.$_SESSION['user_id'];

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Save a income
     * @param form method post
     * @return boolean success = true
     */
    public static function saveIncome()
    {
        if (static::validateIncome()) {
            $comment = htmlentities($_POST['comment']);

            $sql = "INSERT INTO incomes ( id, user_id, income_category, amount, date, comment) VALUES ( 'NULL', :user_id, :category, :amount, :date, :comment)";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':amount', $_POST['amount'], PDO::PARAM_STR);
            $stmt->bindValue(':date', $_POST['date'], PDO::PARAM_STR);
            $stmt->bindValue(':category', $_POST['incomeCategory'], PDO::PARAM_INT);
            $stmt->bindValue(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

            return $stmt->execute();
        }

        return false;
    }

    /**
     * Validate a income
     * @param form method post
     * @return boolean success = true
     */
    protected static function validateIncome()
    {
        $result = true;

        if ((!isset($_POST['amount'])) || (!isset($_POST['date'])) || (!isset($_POST['incomeCategory']))) {
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
        } elseif( !Time::checkDate($_POST['date'] )) {
            Flash::addMessage('Wprowadź rzeczywistą datę!', Flash::WARNING);
            $result = false;
        }

        return $result;
    }
 }