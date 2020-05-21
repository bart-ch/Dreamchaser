<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Expenses;
use \App\Auth;
use \App\Dates;
use \App\Flash;

/**
 * Login controller
 *
 * PHP version 7.0
 */
class Expense extends Authenticated
{

    /**
     * Show the login page
     *
     * @return void
     */
    public function newAction()
    {
        View::renderTemplate('Expense/new.html', [
		'todaysDate' => Dates::getTodaysDate(),
		'userExpenses' => Expenses::getUserExpenseCategories(),
		'paymentMethods' => Expenses::getUserPaymentMethods()
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
			$expense = new Expenses($_POST);

			if ($expense->save()) {

				Flash::addMessage('Sukces! Wydatek został dodany.');

				$this->redirect('/expense/new');

			} else {
					
				View::renderTemplate('Expense/new.html', [
					'expense' => $expense,
					'todaysDate' => Dates::getTodaysDate(),
					'userExpenses' => Expenses::getUserExpenseCategories(),
					'paymentMethods' => Expenses::getUserPaymentMethods()
				]);
				
			} 	
		} else {
			$this->redirect('/expense/new');
		}
    }


}
