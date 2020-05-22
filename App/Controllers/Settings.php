<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\Incomes;
use \App\Models\Expenses;
use \App\Models\User;

/**
 * Login controller
 *
 * PHP version 7.0
 */
class Settings extends Authenticated
{

	protected function before()
	{	
		parent::before();
		$this->user = Auth::getUser();
	}
	
    public function indexAction()
    {	
			View::renderTemplate('Settings/index.html', [
			'userIncomes' => Incomes::getUserIncomeCategories(),
			'userExpenses' => Expenses::getUserExpenseCategories(),
			'paymentMethods' => Expenses::getUserPaymentMethods(),
			'user' => $this->user
			]);
	}
	
	public function updateProfileData()
	{
		if(isset($_POST['name'])) {
			
			$user = new User($_POST);

			if ($user->updateProfile()) {

				Flash::addMessage('Profil został zedytowany.');

				$this->redirect('/settings/index');

			} else {
					
				Flash::addMessage('Nie udało się edytować profilu.',Flash::DANGER);	
					
				View::renderTemplate('Settings/index.html', [
				'userIncomes' => Incomes::getUserIncomeCategories(),
				'userExpenses' => Expenses::getUserExpenseCategories(),
				'paymentMethods' => Expenses::getUserPaymentMethods(),
				'user' => $user
				]);
					
			} 	
		} else {
			$this->redirect('/settings/index');
		}
	}


}
