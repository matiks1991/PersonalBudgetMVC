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
        $arguments['oldestDate'] = static::retreiveOldestDate();
        $arguments['yungestDate'] = static::retreiveYungestDate();
        $arguments['jsonPieChart'] = static::generateChartData($dateStart, $dateEnd);
        $arguments['caption'] = 'Bilans od '.$dateStart.' do '.$dateEnd;
        if(empty($arguments['incomes']) && empty($arguments['expenses']))
            Flash::addMessage('Brak wyników z wybranego okresu!', Flash::WARNING);

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
     * @param string: $dateStart, $dateEnd
     * @return array
     */
    public static function generateChartData($dateStart, $dateEnd) {
    
            $expensePieChart = "SELECT ec.name category, SUM(e.amount) total FROM expenses AS e, expenses_category_assigned_to_users AS ec 
            WHERE e.user_id=".$_SESSION['user_id']." AND ec.user_id=".$_SESSION['user_id']." AND ec.id=e.expense_category AND e.date 
            BETWEEN '$dateStart' AND '$dateEnd' GROUP BY expense_category";
    
            $db = static::getDB();
            $stmt = $db->prepare($expensePieChart);
            $stmt->execute();
    
            $expensePie = array();
            $expenseResult = $stmt->fetchAll(\PDO::FETCH_OBJ);
    
            $expensesArray = json_decode(json_encode($expenseResult), True);
    
            foreach ($expensesArray as $expenseChart) {
                array_push($expensePie, array("label"=>$expenseChart['category'], "y"=>$expenseChart['total']));
            }
    
            json_encode($expensePie, JSON_NUMERIC_CHECK);
            
            return $expensesArray;
    }

    /**
     * Retreive oldest date
     * @param int: Session user_id
     * @return array
     */
    private static function retreiveOldestDate()
    {
        $instructionRetrieveOldestDate = 'SELECT LEAST((SELECT MIN(date) FROM incomes WHERE user_id = '.$_SESSION['user_id'].'),(SELECT MIN(date) FROM expenses WHERE user_id = '.$_SESSION['user_id'].')) as date;';

        $db = static::getDB();

        $stmt = $db->prepare($instructionRetrieveOldestDate);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Retreive yungest date
     * @param int: Session user_id
     * @return array
     */
    private static function retreiveYungestDate()
    {
        $instructionRetrieveYoungestDate = 'SELECT GREATEST((SELECT MAX(date) FROM incomes WHERE user_id = '.$_SESSION['user_id'].'),(SELECT MAX(date) FROM expenses WHERE user_id = '.$_SESSION['user_id'].')) as date;';

        $db = static::getDB();

        $stmt = $db->prepare($instructionRetrieveYoungestDate);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Validate a date of balance
     * @param form method post
     * @return boolean success = true
     */
    public static function validateDatesOfBalance()
    {
        $result = true;

        if ((!isset($_POST['dateStart'])) || (!isset($_POST['dateEnd']))) {
            Flash::addMessage('Wypełnij wszystkie wymagane pola!', Flash::WARNING);
            $result = false;
        } elseif ($_POST['dateStart'] > $_POST['dateEnd']){
            Flash::addMessage('Data początkowa nie może być starsza od daty końcowej!', Flash::WARNING);
            $result = false;
        } elseif (!preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $_POST['dateStart']) || !preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $_POST['dateEnd'])) {
            Flash::addMessage('Wprowadź prawidłowy format daty! [DD.MM.RRRR]', Flash::WARNING);
            $result = false;
        } elseif( !Time::checkDate($_POST['dateStart']) || !Time::checkDate($_POST['dateEnd'])) {
            Flash::addMessage('Wprowadź rzeczywistą datę!', Flash::WARNING);
            $result = false;
        }

        return $result;
    }

}