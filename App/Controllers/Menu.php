<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\User;

class Menu extends Authenticated
{
	
	protected function before()
	{	
		parent::before();
		$this->user = Auth::getUser();
	}
	
	public function indexAction()
	{
		View::renderTemplate('Menu/index.html', [
			'user' => $this->user
		]);
	}

}
