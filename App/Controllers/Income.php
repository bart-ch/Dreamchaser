<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
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
		'todaysDate' => $this->getTodaysDate()
		]);
    }

    /**
     * Log in a user
     *
     * @return void
     */
    public function createAction()
    {
        $user = User::authenticate($_POST['email'], $_POST['password']);
        
        $remember_me = isset($_POST['remember_me']);

        if ($user) {

            Auth::login($user, $remember_me);

            Flash::addMessage('Logowanie zakończone sukcesem.');

            $this->redirect('/menu/main');

        } else {

            Flash::addMessage('Niepoprawny e-mail lub hasło.', Flash::DANGER);

            View::renderTemplate('Login/new.html', [
                'email' => $_POST['email'],
                'remember_me' => $remember_me
            ]);
        }
    }
	
	protected function getTodaysDate()
	{
			$todaysDate = new \DateTime();
			$todaysDateFormat = $todaysDate->format('Y-m-d');
			return $todaysDateFormat;
	}


}
