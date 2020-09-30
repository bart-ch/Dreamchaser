<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Incomes;
use \App\Auth;
use \App\Dates;
use \App\Flash;

class Income extends Authenticated
{

    public function indexAction()
    {
        View::renderTemplate('Income/index.html', [
		'todaysDate' => Dates::getTodaysDate(),
		'userIncomes' => Incomes::getUserIncomeCategories(),
		'lastDate' => Dates::getLastDayOfNextMonth()
		]);
    }

    public function createAction()
    {
		if(isset($_POST['amount'])) {
			$income = new Incomes($_POST);

			if ($income->save()) {

				Flash::addMessage('Sukces! Przychód został dodany.');

				$this->redirect('/income/index');

			} else {
					
				View::renderTemplate('Income/index.html', [
					'income' => $income,
					'todaysDate' => Dates::getTodaysDate(),
					'userIncomes' => Incomes::getUserIncomeCategories(),
					'lastDate' => Dates::getLastDayOfNextMonth()
				]);
				
			} 	
		} else {
			$this->redirect('/income/index');
		}
    }
}
