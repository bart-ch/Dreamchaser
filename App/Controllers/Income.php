<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Incomes;
use \App\Auth;
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
		'todaysDate' => $this->getTodaysDate(),
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
  
    }
	
	protected function getTodaysDate()
	{
			$todaysDate = new \DateTime();
			$todaysDateFormat = $todaysDate->format('Y-m-d');
			return $todaysDateFormat;
	}


}
