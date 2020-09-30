<?php

namespace App;

class Config
{
    /**
     * Database host
     * @var string
     */
    const DB_HOST = 'localhost';

    /**
     * Database name
     * @var string
     */
    const DB_NAME = 'dreamchaser';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'root';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = 'mysql';

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;
	
	const SECRET_KEY = 'YOUR_CAPTCHA_KEY';
	
	const CAPTCHA_SECRET = 'YOUR_CAPTCHA_SECRET_KEY';
}
