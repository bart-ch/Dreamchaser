<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\balances;
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
		if(!isset($_POST['periodOfTime'])){	
			View::renderTemplate('Balance/new.html', [
			'incomeCategoriesAmount' => Balances::getIncomeCategoriesAmount(),	
			'expenseCategoriesAmount' => Balances::getExpenseCategoriesAmount(),	
			'incomeCategoriesInDetail' => Balances::getIncomeCategoriesAmuntInDetail(),	
			'expenseCategoriesInDetail' => Balances::getExpenseCategoriesAmuntInDetail(),
			
			]);
		} else 
		{
			
		}
		
		
    }

    /**
     * Log in a user
     *
     * @return void
     */



}
