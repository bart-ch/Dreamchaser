<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;


class Login extends \Core\Controller
{


    public function indexAction()
    {
        if(Auth::isLoggedIn()) {
			$this->redirect('/menu/index');
		} else {
        View::renderTemplate('Login/index.html');
		}
    }

    public function createAction()
    {
		if(isset($_POST['email'])) {
			$user = User::authenticate($_POST['email'], $_POST['password']);
			
			$remember_me = isset($_POST['remember_me']);

			if ($user) {

				Auth::login($user, $remember_me);

				Flash::addMessage('Logowanie zakończone sukcesem.');

				$this->redirect(Auth::getReturnToPage());

			} else {

				Flash::addMessage('Niepoprawny e-mail lub hasło.', Flash::DANGER);

				View::renderTemplate('Login/index.html', [
					'email' => $_POST['email'],
					'remember_me' => $remember_me
				]);
			}
		} else {
			$this->redirect('/login/index');
		}
    }

    public function destroyAction()
    {
        Auth::logout();

        $this->redirect('/login/show-logout-message');
    }

    public function showLogoutMessageAction()
    {
        Flash::addMessage('Wylogowanie powiodło się.');

        $this->redirect('/');
    }
}
