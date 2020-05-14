<?php

namespace App\Models;

use PDO;
use \App\Auth;
use \App\Dates;
use \Core\View;

/**
 * User model
 *
 * PHP version 7.0
 */
class Balances extends \Core\Model
{

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }
	
	private static function getStartDate()
	{
		if(isset($_POST['periodOfTime'])) {
			$periodOfTime = $_POST['periodOfTime'];

			if($periodOfTime == "currentMonth")
			{
				$startDate = "DATE_FORMAT(NOW() ,'%Y-%m-01')";
			} 
			else if ($periodOfTime == "previousMonth") {
				$startDate = "last_day(curdate() - interval 2 month) + interval 1 day";
			}	
			else if ($periodOfTime == "currentYear") {
				$startDate = "DATE_FORMAT(NOW() ,'%Y-01-01')";
			}	
			else if ($periodOfTime == "customPeriod") { 
			
				$firstDate = date("Ymd", strtotime($_POST['startDate']));      
				$secondDate = date("Ymd", strtotime($_POST['endDate']));  
				
				if($firstDate < $secondDate) {
					$startDate = $_POST['startDate'];
				} else if($firstDate > $secondDate) {
					$startDate = $_POST['endDate'];
				}
			}
		} else {
			$startDate = "DATE_FORMAT(NOW() ,'%Y-%m-01')";
		}
		return $startDate;
	}	
	
	private static function getEndDate()
	{
		if(isset($_POST['periodOfTime'])) {
			
			$periodOfTime = $_POST['periodOfTime'];

			if($periodOfTime == "currentMonth")
			{
				$endDate = "NOW()";
			} 
			else if ($periodOfTime == "previousMonth") {
				$endDate = "last_day(curdate() - interval 1 month)";
			}	
			else if ($periodOfTime == "currentYear") {
					$endDate = "NOW()";
			}	
			else if ($periodOfTime == "customPeriod") { 
			
				$firstDate = date("Ymd", strtotime($_POST['startDate']));      
				$secondDate = date("Ymd", strtotime($_POST['endDate']));  
				
				if($firstDate < $secondDate) {
					$endDate = $_POST['endDate'];;
				} else if($firstDate > $secondDate) {
					$endDate = $_POST['startDate'];
				}
			}
		} else {
			$endDate = "NOW()";
		}
		return $endDate;
	}
		
	public static function getIncomeCategoriesAmount()
	{			
		$db = static::getDB();
		$startDate = static::getStartDate();
		$endDate = static::getEndDate();

		
		$incomeCategoriesAmount = $db->query("SELECT SUM(i.amount) AS amount, ica.name FROM incomes AS i, incomes_categories_assigned_to_users as ica WHERE i.user_id = {$_SESSION['user_id']} AND i.income_category_assigned_to_user_id = ica.id AND i.date_of_income BETWEEN $startDate AND $endDate GROUP BY i.income_category_assigned_to_user_id")->fetchAll(PDO::FETCH_ASSOC);

		return $incomeCategoriesAmount;
		
	}	
	
	public static function getIncomeCategoriesAmuntInDetail()
	{
		$db = static::getDB();
		
		$startDate = static::getStartDate();
		$endDate = static::getEndDate();
		
		$incomeCategoriesAmountInDetail = $db->query("SELECT i.amount, i.date_of_income, i.comment, ica.name FROM incomes AS i, incomes_categories_assigned_to_users as ica WHERE i.user_id={$_SESSION['user_id']} AND i.income_category_assigned_to_user_id = ica.id AND i.date_of_income BETWEEN $startDate AND $endDate")->fetchAll(PDO::FETCH_ASSOC);
		
		return $incomeCategoriesAmountInDetail;
	}
	
	public static function getExpenseCategoriesAmount()
	{
		$db = static::getDB();
		
		$startDate = static::getStartDate();
		$endDate = static::getEndDate();
		
		$expenseCategoriesAmount = $db->query("SELECT SUM(e.amount)  AS amount, eca.name FROM expenses AS e, expenses_categories_assigned_to_users as eca WHERE e.user_id={$_SESSION['user_id']} AND e.expense_category_assigned_to_user_id = eca.id AND e.date_of_expense BETWEEN $startDate AND $endDate GROUP BY e.expense_category_assigned_to_user_id")->fetchAll(PDO::FETCH_ASSOC);
		
		return $expenseCategoriesAmount;
	}

	public static function getExpenseCategoriesAmuntInDetail()
	{
		$db = static::getDB();
		
		$startDate = static::getStartDate();
		$endDate = static::getEndDate();
	
		$expenseCategoriesAmountInDetail = $db->query("SELECT e.amount, e.date_of_expense, e.comment, eca.name FROM expenses AS e, expenses_categories_assigned_to_users as eca WHERE e.user_id={$_SESSION['user_id']} AND e.expense_category_assigned_to_user_id = eca.id AND e.date_of_expense BETWEEN $startDate AND $endDate")->fetchAll(PDO::FETCH_ASSOC);
		
		return $expenseCategoriesAmountInDetail;
	}	
	
	public static function getDefaultBalanceData()
	{
		$db = static::getDB();
		
	}

 
}
