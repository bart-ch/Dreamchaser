<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\User;

/**
 * Signup controller
 *
 * PHP version 7.0
 */
class Signup extends \Core\Controller
{

    /**
     * Show the signup page
     *
     * @return void
     */
    public function indexAction()
    {
		if(Auth::isLoggedIn()) {
			$this->redirect('/menu/index');
		} else {
        View::renderTemplate('Signup/index.html');
		}
    }

    /**
     * Sign up a new user
     *
     * @return void
     */
    public function createAction()
    {
	
        $user = new User($_POST);

		
		if(!isset($_POST['email'])) {
			$this->redirect('/signup/index');
		}

        if ($user->save()) {

            $this->redirect('/signup/success');

        } else {

            View::renderTemplate('Signup/index.html', [
                'user' => $user
            ]);

        }
    }

    /**
     * Show the signup success page
     *
     * @return void
     */
    public function successAction()
    {
        View::renderTemplate('Signup/success.html');
    }
}
