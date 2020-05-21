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
			'paymentMethods' => Expenses::getUserPaymentMethods()		
			]);

		
		
    }

    /**
     * Log in a user
     *
     * @return void
     */



}
