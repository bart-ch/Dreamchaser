<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Incomes;
use \App\Auth;
use \App\Dates;
use \App\Flash;

/**
 * Login controller
 *
 * PHP version 7.0
 */
class Income extends Authenticated
{

    /**
     * Show the login page
     *
     * @return void
     */
    public function newAction()
    {
        View::renderTemplate('Income/new.html', [
		'todaysDate' => Dates::getTodaysDate(),
		'userIncomes' => Incomes::getUserIncomeCategories()
		]);
    }

    /**
     * Log in a user
     *
     * @return void
     */
    public function createAction()
    {
		if(isset($_POST['amount'])) {
			$income = new Incomes($_POST);

			if ($income->save()) {

				Flash::addMessage('Sukces! Przychód został dodany.');

				$this->redirect('/income/new');

			} else {
					
				View::renderTemplate('Income/new.html', [
					'income' => $income,
					'todaysDate' => Dates::getTodaysDate(),
					'userIncomes' => Incomes::getUserIncomeCategories()
				]);
				
			} 	
		} else {
			$this->redirect('/income/new');
		}
    }
	
	public function update() 
	{
		if(isset($_POST['amount'])) {
			
			$income = new Incomes($_POST);

			if ($income->update()) {

				Flash::addMessage('Sukces! Przychód został zedytowany.');
			//	var_dump($this->incomeId);
			//	exit();

				$this->redirect('/balance/new');

			} else {
					
				//view z edytowanymi danymi ktore byly zle
				
			} 	
		} else {
			$this->redirect('/balance/new');
		}
		
	}


}
