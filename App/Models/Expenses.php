<?php

namespace App\Models;

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
     *
     * @return array
     */
    public static function getPaymentMethods()
    {
        $sql = 'SELECT id, name FROM payment_methods_assigned_to_users WHERE user_id='.$_SESSION['user_id'];

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        // $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get category expenses
     *
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
 }