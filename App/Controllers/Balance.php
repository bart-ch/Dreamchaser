<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Balances;
use \App\Models\Incomes;
use \App\Models\Expenses;
use \App\Auth;
use \App\Dates;
use \App\Flash;

/**
 * Login controller
 *
 * PHP version 7.0
 */
class Balance extends Authenticated
{

    /**
     * Show the login page
     *
     * @return void
     */
    public function newAction()
    {				
			View::renderTemplate('Balance/new.html', [
			'incomeCategoriesAmount' => Balances::getIncomeCategoriesAmount(),	
			'expenseCategoriesAmount' => Balances::getExpenseCategoriesAmount(),	
			'incomeCategoriesInDetail' => Balances::getIncomeCategoriesAmuntInDetail(),	
			'expenseCategoriesInDetail' => Balances::getExpenseCategoriesAmuntInDetail(),
			'todaysDate' => Dates::getTodaysDate(),
			'yesterdaysDate' => Dates::getYesterdaysDate(),
			'firstDate' => Balances::getFirstEchoDate(),
			'secondDate' => Balances::getSecondEchoDate(),
			'userIncomes' => Incomes::getUserIncomeCategories(),
			'userExpenses' => Expenses::getUserExpenseCategories(),
			'paymentMethods' => Expenses::getUserPaymentMethods(),
			'lastDate' => Dates::getLastDayOfNextMonth()
			]);
	
    }
	
	public function updateIncome() 
	{
		if(isset($_POST['amount'])) {
			
			$income = new Incomes($_POST);

			if ($income->update()) {

				Flash::addMessage('Przychód został zedytowany.');

				$this->redirect('/balance/new');

			} else {
					
				Flash::addMessage('Nie udało się edytować przychodu.',Flash::DANGER);	
					
				View::renderTemplate('Balance/new.html', [
				'incomeCategoriesAmount' => Balances::getIncomeCategoriesAmount(),	
				'expenseCategoriesAmount' => Balances::getExpenseCategoriesAmount(),	
				'incomeCategoriesInDetail' => Balances::getIncomeCategoriesAmuntInDetail(),	
				'expenseCategoriesInDetail' => Balances::getExpenseCategoriesAmuntInDetail(),
				'todaysDate' => Dates::getTodaysDate(),
				'yesterdaysDate' => Dates::getYesterdaysDate(),
				'firstDate' => Balances::getFirstEchoDate(),
				'secondDate' => Balances::getSecondEchoDate(),
				'userIncomes' => Incomes::getUserIncomeCategories(),
				'userExpenses' => Expenses::getUserExpenseCategories(),
				'paymentMethods' => Expenses::getUserPaymentMethods(),
				'income' => $income				
				]);
				
			} 	
		} else {
			$this->redirect('/balance/new');
		}
		
	}
	
	public function deleteIncome() 
	{	
		if(isset($_POST['amount'])) {
			
			$income = new Incomes($_POST);

			$income->delete();

			Flash::addMessage('Przychód został usunięty.');

			$this->redirect('/balance/new');
			
		} else {
			$this->redirect('/balance/new');
		}

	}
	
	public function updateExpense() 
	{
		if(isset($_POST['amount'])) {
			
			$expense = new Expenses($_POST);

			if ($expense->update()) {

				Flash::addMessage('Wydatek został zedytowany.');

				$this->redirect('/balance/new');

			} else {
					
				Flash::addMessage('Nie udało się edytować wydatku.',Flash::DANGER);	
					
				View::renderTemplate('Balance/new.html', [
				'incomeCategoriesAmount' => Balances::getIncomeCategoriesAmount(),	
				'expenseCategoriesAmount' => Balances::getExpenseCategoriesAmount(),	
				'incomeCategoriesInDetail' => Balances::getIncomeCategoriesAmuntInDetail(),	
				'expenseCategoriesInDetail' => Balances::getExpenseCategoriesAmuntInDetail(),
				'todaysDate' => Dates::getTodaysDate(),
				'yesterdaysDate' => Dates::getYesterdaysDate(),
				'firstDate' => Balances::getFirstEchoDate(),
				'secondDate' => Balances::getSecondEchoDate(),
				'userIncomes' => Incomes::getUserIncomeCategories(),
				'userExpenses' => Expenses::getUserExpenseCategories(),
				'paymentMethods' => Expenses::getUserPaymentMethods(),
				'expense' => $expense				
				]);
				
			} 	
		} else {
			$this->redirect('/balance/new');
		}
		
	}
	
	public function deleteExpense() 
	{	
		if(isset($_POST['amount'])) {
			
			$expense = new Expenses($_POST);

			$expense->delete();

			Flash::addMessage('Wydatek został usunięty.');

			$this->redirect('/balance/new');
			
		} else {
			$this->redirect('/balance/new');
		}

	}
	
	
}


