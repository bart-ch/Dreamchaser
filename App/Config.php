<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 7.0
 */
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
	
	const SECRET_KEY = '1BDEGArLbHIRiruVJGGZCqJ6Mx7xHWgp';
	
	const CAPTCHA_SECRET = '6Lf7teYUAAAAAGZCgEQKBBhw15BHVOTO1gbu6_15';
	
}
