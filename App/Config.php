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
    const DB_NAME = 'mvclogin';

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
	
	const MAILGUN_API_KEY = '202feb66ae040c044cfec90f1b6a65b7-0afbfc6c-c92ed07b';
	
	const MAILGUN_DOMAIN = 'sandbox49af102c30714d4fb68f1749c461839f.mailgun.org';
}
