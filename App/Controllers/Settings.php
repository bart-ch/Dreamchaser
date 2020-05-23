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
	
	public function deleteAccount()
	{
		if(isset($_POST['deleteAccount'])) {
			
		$user = new User();
		$user->deleteAccount();

		Auth::logout();
		
		$this->redirect('/login/new');
		} else {
			$this->redirect('/settings/index');
		}
	
	}	
	
	public function resetAccountTransactions()
	{
		if(isset($_POST['resetAccount'])) {
			
		Incomes::deleteAllUserIncomes();
		Expenses::deleteAllUserExpenses();
		
		Flash::addMessage('Wszystkie Twoje transakcje zostały usunięte.');

		$this->redirect('/settings/index');
		} else {
			$this->redirect('/settings/index');
		}
	
	}	
	
	public function changePassword()
	{
		if(isset($_POST['password'])) {
			
			$user = new User($_POST);

			if ($user->changeUserPassword()) {

				Flash::addMessage('Twoje hasło zostało zmienione.');

				$this->redirect('/settings/index');

			} else {
					
				Flash::addMessage('Nie udało się zmienić hasła.',Flash::DANGER);	
					
				$this->redirect('/settings/index');	
			} 	
		} else {
			$this->redirect('/settings/index');
		}
	
	}
	
	public function updateIncomeCategory() 
	{
		if(isset($_POST['incomeCategory'])) {
			
			$income = new Incomes($_POST);

			if ($income->updateCategory()) {

				Flash::addMessage('Kategoria przychodu została zedytowana.');

				$this->redirect('/settings/index');

			} else {
					
		//		Flash::addMessage('Nie udało się edytować przychodu.',Flash::DANGER);	
					
				$this->redirect('/settings/index');
			} 	
		} else {
			$this->redirect('/settings/index');
		}
		
	}
	
	public function deleteIncomeCategory() 
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


}
