<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\User;

/**
 * Signup controller
 *
 * PHP version 7.0
 */
class Menu extends Authenticated
{
	
	protected function before()
	{	
		parent::before();
		$this->user = Auth::getUser();
	}
	
	public function mainAction()
	{
		View::renderTemplate('Menu/index.html', [
			'user' => $this->user
		]);
	}
	


}
