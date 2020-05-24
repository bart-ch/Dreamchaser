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
class Expenses extends \Core\Model
{


    public $errors = [];


    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }
	
	public static function getUserExpenseCategories()
	{
		$sql = "SELECT name,id FROM expenses_categories_assigned_to_users WHERE user_id = :user_id";
	
		$db = static::getDB();
		$incomeCategories = $db->prepare($sql);
        $incomeCategories->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$incomeCategories->execute();

		return $incomeCategories->fetchAll(PDO::FETCH_ASSOC);
	}	
	
	public static function getUserPaymentMethods()
	{
		$sql = "SELECT name, id FROM payment_methods_assigned_to_users WHERE user_id = :user_id";
	
		$db = static::getDB();
		$paymentMethods = $db->prepare($sql);
        $paymentMethods->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$paymentMethods->execute();

		return $paymentMethods->fetchAll(PDO::FETCH_ASSOC);
	}

    /**
     * Save the user model with the current property values
     *
     * @return boolean  True if the user was saved, false otherwise
     */
    public function save()
    {
		$this->amount = $this->validateAndConvertPriceFormat();
        $this->validate();

        if (empty($this->errors)) {

			$sql = "INSERT INTO expenses VALUES (NULL, :user_id, :idOfIncomeCategoryAssignedToUser, :idOfPaymentMethodAssignedToUser, :convertedPrice, :date, :comment)";

            $db = static::getDB();
            $stmt = $db->prepare($sql);
		
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':idOfIncomeCategoryAssignedToUser', $this->getIdOfExpenseCategoryAssignedToUser(), PDO::PARAM_INT);
            $stmt->bindValue(':idOfPaymentMethodAssignedToUser', $this->getIdOfPaymentMethodAssignedToUser(), PDO::PARAM_INT);
            $stmt->bindValue(':convertedPrice', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date', $this->expenseDate, PDO::PARAM_STR);
            $stmt->bindValue(':comment', $this->comment, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }
	
	public function update() 
	{	
		$this->amount = $this->validateAndConvertPriceFormat();
        $this->validate();

        if (empty($this->errors)) {
			$sql = "UPDATE expenses SET expense_category_assigned_to_user_id = :idOfExpenseCategoryAssignedToUser, payment_method_assigned_to_user_id = :payment_method_assigned_to_user_id, amount = :convertedPrice, date_of_expense = :date, comment = :comment WHERE id = $this->expenseId";
			
			$db = static::getDB();
            $stmt = $db->prepare($sql);
			
			$stmt->bindValue(':idOfExpenseCategoryAssignedToUser', $this->getIdOfExpenseCategoryAssignedToUser(), PDO::PARAM_INT);
			$stmt->bindValue(':payment_method_assigned_to_user_id', $this->getIdOfPaymentMethodAssignedToUser(), PDO::PARAM_INT);
            $stmt->bindValue(':convertedPrice', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date', $this->expenseDate, PDO::PARAM_STR);
            $stmt->bindValue(':comment', $this->comment, PDO::PARAM_STR);

            return $stmt->execute();
		}
		return false;
	}
	
	public function delete() 
	{
		var_dump($this->expenseId);
		$sql = "DELETE FROM expenses WHERE id = $this->expenseId";
								
		$db = static::getDB();
		
		return $db->query($sql);

	}
	
	public static function deleteAllUserExpenses()
	{
		$sql = "DELETE FROM expenses WHERE user_id = {$_SESSION['user_id']}";
								
		$db = static::getDB();
		
		return $db->query($sql);
	}
	
	protected function getIdOfExpenseCategoryAssignedToUser()
	{		
		$sql = "SELECT eca.id FROM expenses_categories_assigned_to_users AS eca, expenses_categories AS ec WHERE eca.user_id = :user_id AND ec.name= :expenseName AND ec.name=eca.name";

		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':expenseName', $this->expenseCategory, PDO::PARAM_STR);
		$stmt->execute();	
		
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		
		return $result['id'];
	}	
	
	protected function getIdOfPaymentMethodAssignedToUser()
	{		
		$sql = "SELECT pma.id FROM payment_methods_assigned_to_users AS pma, payment_methods AS pm WHERE pma.user_id = :user_id AND pm.name= :paymentMethod AND  pm.name = pma.name";

		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':paymentMethod', $this->paymentMethod, PDO::PARAM_STR);
		
		$stmt->execute();
		
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		
		return $result['id'];
	
	}
	
	public function validate()
    {

		
       if($this->expenseDate < '2000-01-01' || $this->expenseDate > Dates::getDateOfLastDayOfNextMonth()) {

			$this->errors['date'] = "Data musi mieścić się w przedziale od 2000-01-01 do ".Dates::getDateOfLastDayOfNextMonth().".";
				
		}
		
		if(strlen($this->comment) > 100) {
			$this->errors['comment'] = "Komentarz jest zbyt długi.";
		}	
		
    }
	
	protected function validateAndConvertPriceFormat() 
	{
		if(preg_match("/^\-?[0-9]*\.?[0-9]+\z/", $this->amount)) {
		
			$this->amount = str_replace(['-', ',', '$', ' '], '', $this->amount);

			if(strpos($this->amount, '.') !== false) {
				$dollarExplode = explode('.', $this->amount);
				$dollar = $dollarExplode[0];
				$cents = $dollarExplode[1];
				if(strlen($cents) === 0) {
					$cents = '00';
				} elseif(strlen($cents) === 1) {
					$cents = $cents.'0';
				} elseif(strlen($cents) > 2) {
					$cents = substr($cents, 0, 2);
				}
				$this->amount = $dollar.'.'.$cents;
			} else {
				$cents = '00';
				$this->amount = $this->amount.'.'.$cents;
			}	


			if($this->amount <0 || $this->amount >=1000000) {
				$this->errors['amount'] = 'Podana kwota musi mieścić się w przedziale od 0 do 1 miliona.';
			}
			
			return $this->amount;
		
		} else {
			$this->errors['amount'] = 'Podana kwota musi być liczbą w poprawnym formacie i być mniejsza niż 1 milion.';
		}
		
		return false;
	}

	public function updateCategory() 
	{	
        if(strlen($this->expenseCategory)<1 || strlen($this->expenseCategory)>40) {
		$this->errors['expenseCategory'] = "Kategoria przychodu musi zawierać od 1 do 40 znaków.";
		//zwalidować jeszcze czy już taka przypisana nie istnieje
		}
		
		$sql = "SELECT * FROM expenses_categories_assigned_to_users WHERE user_id = :user_id AND name = :expenseName AND id <> :id";
		
		$db = static::getDB();

		$stmt = $db->prepare($sql);


		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':id', $this->expenseCategoryId, PDO::PARAM_INT);
		$stmt->bindValue(':expenseName', $this->expenseCategory, PDO::PARAM_STR);

		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if(count($result)==1){
		$this->errors['newExpenseCategory'] = "Podana kategoria już istnieje.";	
		}

        if (empty($this->errors)) {
			$sql = "UPDATE expenses_categories_assigned_to_users SET name = :name WHERE id = :id";
			
			$db = static::getDB();
            $stmt = $db->prepare($sql);
			
			$stmt->bindValue(':id', $this->expenseCategoryId, PDO::PARAM_INT);
            $stmt->bindValue(':name', $this->expenseCategory, PDO::PARAM_STR);

            return $stmt->execute();
		}
		return false;
	}
	
	public function updatePaymentMethod() 
	{	
        if(strlen($this->paymentCategory)<1 || strlen($this->paymentCategory)>30) {
			return false;
		}

		$sql = "SELECT * FROM payment_methods_assigned_to_users WHERE user_id = :user_id AND name = :paymentName AND id <> :id";
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':id', $this->paymentId, PDO::PARAM_INT);
		$stmt->bindValue(':paymentName', $this->paymentCategory, PDO::PARAM_STR);

		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if(count($result)==1){
			return false;
		}

		$sql = "UPDATE payment_methods_assigned_to_users SET name = :name WHERE id = :id";
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);	
		
		$stmt->bindValue(':id', $this->paymentId, PDO::PARAM_INT);
		$stmt->bindValue(':name', $this->paymentCategory, PDO::PARAM_STR);

		return $stmt->execute();

	}	

	public function deleteCategory()
	{		
			$sql = "DELETE FROM expenses_categories_assigned_to_users WHERE id = :id";
									
			$db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':id', $this->expenseCategoryId, PDO::PARAM_INT);

            return $stmt->execute();		
	}	
	
	public function deletePaymentMethod()
	{		
			$sql = "DELETE FROM payment_methods_assigned_to_users WHERE id = :id";
									
			$db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':id', $this->paymentId, PDO::PARAM_INT);

            return $stmt->execute();		
	}	
	
	public function addExpenseCategory()
	{		
		if ($this->validateNewCategoryName()) {
			
			$sql = "INSERT INTO expenses_categories_assigned_to_users VALUES (NULL, :user_id, :name)";
									
			$db = static::getDB();
            $stmt = $db->prepare($sql);
	
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':name', $this->newExpenseCategory, PDO::PARAM_STR);

            return $stmt->execute();		
			
		}
		return false;
	}	
	
	public function addPaymentMethod()
	{	
		if ($this->validateNewPaymentMethodName()) {
			
			$sql = "INSERT INTO payment_methods_assigned_to_users VALUES (NULL, :user_id, :name)";
									
			$db = static::getDB();
            $stmt = $db->prepare($sql);
	
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':name', $this->paymentCategory, PDO::PARAM_STR);

            return $stmt->execute();		
			
		}
		return false;
	}		
	
	protected function validateNewCategoryName()
	{
		if(strlen($this->newExpenseCategory)<1 || strlen($this->newExpenseCategory)>40) {
			return false;
		}

		$sql = "SELECT * FROM expenses_categories_assigned_to_users WHERE user_id = :user_id AND name = :expenseName";
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);

		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':expenseName', $this->newExpenseCategory, PDO::PARAM_STR);

		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if(count($result)==1){
			return false;
		}
		return true;
	}		
	
	protected function validateNewPaymentMethodName()
	{
		if(strlen($this->paymentCategory)<1 || strlen($this->paymentCategory)>30) {
			return false;	
		}

		$sql = "SELECT * FROM payment_methods_assigned_to_users WHERE user_id = :user_id AND name = :paymentCategoryName";
		
		$db = static::getDB();

		$stmt = $db->prepare($sql);


		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':paymentCategoryName', $this->paymentCategory, PDO::PARAM_STR);

		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if(count($result)==1){
			return false;
		}
		
		return true;
			
	}	
	

 
}
