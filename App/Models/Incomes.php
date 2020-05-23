<?php

namespace App\Models;

use PDO;
use \App\Auth;
use \App\Dates;
use \Core\View;
use \App\Flash;

/**
 * User model
 *
 * PHP version 7.0
 */
class Incomes extends \Core\Model
{


    public $errors = [];


    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }
	
	public static function getUserIncomeCategories()
	{
		$sql = "SELECT name, id FROM incomes_categories_assigned_to_users WHERE user_id = :user_id";
	
		$db = static::getDB();
		$incomeCategories = $db->prepare($sql);
        $incomeCategories->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$incomeCategories->execute();

		return $incomeCategories->fetchAll(PDO::FETCH_ASSOC);
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
			
			$sql = "INSERT INTO incomes VALUES (NULL, :user_id, :idOfIncomeCategoryAssignedToUser, :convertedPrice, :date, :comment)";
									
			$db = static::getDB();
            $stmt = $db->prepare($sql);


	
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':idOfIncomeCategoryAssignedToUser', $this->getIdOfIncomeCategoryAssignedToUser(), PDO::PARAM_INT);
            $stmt->bindValue(':convertedPrice', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date', $this->incomeDate, PDO::PARAM_STR);
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
			$sql = "UPDATE incomes SET income_category_assigned_to_user_id = :idOfIncomeCategoryAssignedToUser, amount = :convertedPrice, date_of_income = :date, comment = :comment WHERE id = $this->incomeId";
			
			$db = static::getDB();
            $stmt = $db->prepare($sql);
			
			$stmt->bindValue(':idOfIncomeCategoryAssignedToUser', $this->getIdOfIncomeCategoryAssignedToUser(), PDO::PARAM_INT);
            $stmt->bindValue(':convertedPrice', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date', $this->incomeDate, PDO::PARAM_STR);
            $stmt->bindValue(':comment', $this->comment, PDO::PARAM_STR);

            return $stmt->execute();
		}
		return false;
	}	
	
	public function updateCategory() 
	{	
        $this->validateCategoryName();

        if (empty($this->errors)) {
			$sql = "UPDATE incomes_categories_assigned_to_users SET name = :name WHERE id = :id";
			
			$db = static::getDB();
            $stmt = $db->prepare($sql);
			
			$stmt->bindValue(':id', $this->incomeCategoryId, PDO::PARAM_INT);
            $stmt->bindValue(':name', $this->incomeCategory, PDO::PARAM_STR);

            return $stmt->execute();
		}
		return false;
	}
	
	protected function validateCategoryName()
	{
		if(strlen($this->incomeCategory)<1 || strlen($this->incomeCategory)>40) {
		$this->errors['incomeCategory'] = "Kategoria przychodu musi zawierać od 1 do 40 znaków.";
		//zwalidować jeszcze czy już taka przypisana nie istnieje
		}
		
	//	$sql = "SELECT name FROM incomes_categories_assigned_to_users WHERE user_id = :user_id AND name = :incomeName";
		
	//	$db = static::getDB();
		
	//	$stmt = $db->prepare($sql);
		
		
	//	$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
	//	$stmt->bindValue(':incomeName', $this->incomeCategory, PDO::PARAM_STR);
		
	//	$stmt->execute();
	//	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	//	if(count($result)==1 && $result[0]['name']!=$this->incomeCategory){
	//		$this->errors['incomeCategory'] = "Podana kategoria już istnieje.";
	
			
	//		Flash::addMessage('Podana kategoria już istnieje.',Flash::DANGER);	
	//	}

		
	}
	
	public function delete() 
	{
		$sql = "DELETE FROM incomes WHERE id = $this->incomeId";
								
		$db = static::getDB();
		
		return $db->query($sql);

	}
	
	public static function deleteAllUserIncomes()
	{
		$sql = "DELETE FROM incomes WHERE user_id = {$_SESSION['user_id']}";
								
		$db = static::getDB();
		
		return $db->query($sql);
	}
	
	
	protected function getIdOfIncomeCategoryAssignedToUser()
	{	
		$sql = "SELECT ica.id FROM incomes_categories_assigned_to_users AS ica, incomes_categories AS ic WHERE ica.user_id = :user_id AND ic.name= :incomeName AND ic.name=ica.name";
		
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		
		
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':incomeName', $this->incomeCategory, PDO::PARAM_STR);
		
		$stmt->execute();
		
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		
		return $result['id'];		
	
	}
	
	public function validate()
    {

		
       if($this->incomeDate < '2000-01-01' || $this->incomeDate > Dates::getDateOfLastDayOfNextMonth()) {

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

 
}
