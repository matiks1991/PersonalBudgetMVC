<?php

namespace App\Models;

use App\Flash;
use App\Time;
use DateTime;
use PDO;

/**
 * Balances Model
 *
 * PHP 7.0
 */

 class Balances extends \Core\Model
 {
     /**
     * Get custom period
     * @param string: $dateStart, $dateEnd
     * @return array
     */
    public static function getCustomPeriod($dateStart, $dateEnd)
    {
        $arguments['incomes'] = static::getIncomes($dateStart, $dateEnd);
        $arguments['expenses'] = static::getExpenses($dateStart, $dateEnd);
        $arguments['incomesDetail'] = static::getIncomesDetail($dateStart, $dateEnd);
        $arguments['expensesDetail'] = static::getExpensesDetail($dateStart, $dateEnd);
        $arguments['jsonPieChart'] = static::generateChartData($arguments['expenses']);

        return $arguments;
    }

     /**
     * Get current month
     * @param string: $dateStart, $dateEnd
     * @return array
     */
    public static function getCurrentMonth()
    {
        $date = new DateTime();
        $dateStart = $date->format('Y-m-01');
        $dateEnd = $date->format('Y-m-t');

        $arguments = static::getCustomPeriod($dateStart, $dateEnd);
        $arguments['caption'] = 'Bieżący miesiąc';

        return $arguments;
    }

     /**
     * Get previous month
     * @param string: $dateStart, $dateEnd
     * @return array
     */
    public static function getPreviousMonth()
    {
        $date = new DateTime();
        $date->modify('-1 month');

        $dateStart = $date->format('Y-m-01');
        $dateEnd = $date->format('Y-m-t');

        $arguments = static::getCustomPeriod($dateStart, $dateEnd);
        $arguments['caption'] = 'Poprzedni miesiąc';

        return $arguments;
    }

    /**
    * Get current year
    * @param string: $dateStart, $dateEnd
    * @return array
    */
   public static function getCurrentYear($arguments = [])
   {
       $date = new DateTime();
       $dateStart = $date->format('Y-01-01');
       $dateEnd = $date->format('Y-m-t');

       $arguments = static::getCustomPeriod($dateStart, $dateEnd);
       $arguments['caption'] = 'Bieżący rok';

       return $arguments;
   }

    /**
     * Get incomes for the selected period
     * @param int: Session user_id
     * @param string: $dateStart, $dateEnd
     * @return array
     */
    private static function getIncomes($dateStart, $dateEnd)
    {
        $instructionRetrieveIncomes = 'SELECT c.name as category, SUM(i.amount) as total FROM incomes i INNER JOIN incomes_category_assigned_to_users c ON i.income_category=c.id WHERE c.user_id='.$_SESSION['user_id'].' AND i.date >= STR_TO_DATE(:dateStart,"%Y-%m-%d") AND i.date <= STR_TO_DATE(:dateEnd,"%Y-%m-%d") AND i.user_id = '.$_SESSION['user_id'].' GROUP BY category ORDER BY total DESC;';

        $db = static::getDB();

        $stmt = $db->prepare($instructionRetrieveIncomes);

        $stmt->bindValue(':dateStart', $dateStart, PDO::PARAM_STR);
        $stmt->bindValue(':dateEnd', $dateEnd, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get expenses for the selected period
     * @param int: Session user_id 
     * @param string: $dateStart, $dateEnd
     * @return array
     */
    private static function getExpenses($dateStart, $dateEnd)
    {
        $instructionRetrieveExpenses = 'SELECT c.name as category, SUM(i.amount) as total FROM expenses i INNER JOIN expenses_category_assigned_to_users c ON i.expense_category=c.id WHERE c.user_id='.$_SESSION['user_id'].' AND i.date >= STR_TO_DATE(:dateStart,"%Y-%m-%d") AND i.date <= STR_TO_DATE(:dateEnd,"%Y-%m-%d") AND i.user_id = '.$_SESSION['user_id'].' GROUP BY category ORDER BY total DESC;';

        $db = static::getDB();

        $stmt = $db->prepare($instructionRetrieveExpenses);

        $stmt->bindValue(':dateStart', $dateStart, PDO::PARAM_STR);
        $stmt->bindValue(':dateEnd', $dateEnd, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get incomes detail for the selected period
     * @param int: Session user_id
     * @param string: $dateStart, $dateEnd
     * @return array
     */
    private static function getIncomesDetail($dateStart, $dateEnd)
    {
        $instructionRetrieveIncomesDetail = 'SELECT i.id, i.date, i.amount, c.name category, i.comment FROM incomes i INNER JOIN incomes_category_assigned_to_users c ON i.income_category=c.id  WHERE c.user_id='.$_SESSION['user_id'].' AND i.date >= STR_TO_DATE(:dateStart,"%Y-%m-%d") AND i.date <= STR_TO_DATE(:dateEnd,"%Y-%m-%d") AND i.user_id = '.$_SESSION['user_id'].' ORDER BY i.date DESC;';

        $db = static::getDB();

        $stmt = $db->prepare($instructionRetrieveIncomesDetail);

        $stmt->bindValue(':dateStart', $dateStart, PDO::PARAM_STR);
        $stmt->bindValue(':dateEnd', $dateEnd, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get expenses detail for the selected period
     * @param int: Session user_id
     * @param string: $dateStart, $dateEnd
     * @return array
     */
    private static function getExpensesDetail($dateStart, $dateEnd)
    {
        $instructionRetrieveExpensesDetail = 'SELECT i.id, i.date, i.amount, c.name category, p.name paymentMethod, i.comment FROM expenses i INNER JOIN expenses_category_assigned_to_users c ON i.expense_category=c.id  INNER JOIN payment_methods_assigned_to_users p ON i.payment_method = p.id WHERE c.user_id='.$_SESSION['user_id'].' AND p.user_id='.$_SESSION['user_id'].' AND i.date >= STR_TO_DATE(:dateStart,"%Y-%m-%d") AND i.date <= STR_TO_DATE(:dateEnd,"%Y-%m-%d") AND i.user_id = '.$_SESSION['user_id'].' ORDER BY i.date DESC;';

        $db = static::getDB();

        $stmt = $db->prepare($instructionRetrieveExpensesDetail);

        $stmt->bindValue(':dateStart', $dateStart, PDO::PARAM_STR);
        $stmt->bindValue(':dateEnd', $dateEnd, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Generate piechart data
     * @param array: $expenses
     * @return json
     */
    private static function generateChartData($expenses)
    {
        $pieData = array(array('Category', "Total"));
        foreach($expenses as $expense){
            $pieData[] = array($expense['category'], (double)$expense['total']);
        }

        return json_encode($pieData);
    }

}