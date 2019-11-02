<?php
/**
* Main Config File
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

class Config {

  public function __construct() {
    /* Enable output buffering */
    ob_start();

    /********************
     *                  *
     *     BASICS       *
     *                  *
     ********************/
    /* Define Site Url Address */
    define('SITE_URL', 'https://localhost/');

    /* Default Template */
    define('DEFAULT_TEMPLATE', 'Default');

    /* Default Language Code */
    define('LANGUAGE_CODE', 'En');

    /* Default Session Prefix */
    define('SESSION_PREFIX', 'uc_');

    /********************
     *                  *
     *     DATABASE     *
     *                  *
     ********************/
    /**
     * Database engine default is mysql.
     */
    define('DB_TYPE', 'mysql');
    /**
     * Database host default is localhost.
     */
    define('DB_HOST', 'uc_db_host');
    /**
     * Database name.
     */
    define('DB_NAME', 'uc_db_name');
    /**
     * Database username.
     */
    define('DB_USER', 'uc_db_user');
    /**
     * Database password.
     */
    define('DB_PASS', 'uc_db_pass');
    /**
     * PREFIX to be used in database calls default is uc_
     */
    define('PREFIX', 'uc_');


    /*****************
     *                *
     *     Account    *
     *                *
     *****************/
    // Account activation route
    define("ACTIVATION_ROUTE", 'Activate');
    // Account password reset route
    define("RESET_PASSWORD_ROUTE", 'Reset-Password');
    //INT cost of BCRYPT algorithm
    define("COST", 10);
    //INT hash length of BCRYPT algorithm
    define("HASH_LENGTH", 22);

    /**
     * Image Settings
     */
    // User's Profile Image Directory
    define('IMG_DIR_PROFILE', 'assets/images/profile-pics/');

    /**
    * Demo Settings
    * Enable (TRUE) or disable (FALSE) demo site
    */
    define('DEMO_SITE', 'FALSE');

    /**
     * Turn on custom error handling.
     */
    set_exception_handler('Core\ErrorLogger::ExceptionHandler');
    set_error_handler('Core\ErrorLogger::ErrorHandler');

    $GLOBALS["instances"] = array();

  }

}

/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
//////////////////UserCandy//////////////////////////
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
