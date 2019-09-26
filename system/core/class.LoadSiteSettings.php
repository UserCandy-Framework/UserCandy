<?php
/**
* System Site Settings Class
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

class LoadSiteSettings {

    private $model;

    public function __construct() {

        /* Load the Admin Panel Model */
        $this->model = new AdminPanelModel();

        /********************
         *                  *
         *     BASICS       *
         *                  *
         ********************/

        /* Define Site Title */
        define('SITE_TITLE', $this->model->getSettings('site_title'));

        /* Define Site Description */
        define('SITE_DESCRIPTION', $this->model->getSettings('site_description'));

        /* Define Site Keywords */
        define('SITE_KEYWORDS', $this->model->getSettings('site_keywords'));

        /* Define Default Home Page **/
        $default_home_page = $this->model->getSettings('default_home_page');
        if(!empty($default_home_page)){
          define('DEFAULT_HOME_PAGE', $default_home_page);
        }else{
          define('DEFAULT_HOME_PAGE', "Home");
        }

        /* Define Default Home Page Folder */
        $default_home_page_folder = $this->model->getSettings('default_home_page_folder');
        if(!empty($default_home_page_folder)){
          define('DEFAULT_HOME_PAGE_FOLDER', $default_home_page_folder);
        }else{
          define('DEFAULT_HOME_PAGE_FOLDER', "Home");
        }

        /** Define Default Home Page and Folder **/
        define('DEFAULT_HOME_PAGE_LOGIN', $this->model->getSettings('default_home_page_login'));
        define('DEFAULT_HOME_PAGE_FOLDER_LOGIN', $this->model->getSettings('default_home_page_folder_login'));

        /********************
         *                  *
         *      EMAIL       *
         *     uses SMTP    *
         ********************/
         /**
         * SMTP Email Username
         */
         define('EMAIL_USERNAME', $this->model->getSettings('site_email_username'));

         /**
         * SMTP Email Password
         */
         define('EMAIL_PASSWORD', $this->model->getSettings('site_email_password'));

         /**
         * SMTP Email sent from whom? a name
         */
         define('EMAIL_FROM_NAME', $this->model->getSettings('site_email_fromname'));

         /* Check to see if email settings are set */
         if($get_site_host = $this->model->getSettings('site_email_host')){
          /**
          * SMTP Email host
          * Example : Google (smtp.gmail.com), Yahoo (smtp.mail.yahoo.com)
          */
          define('EMAIL_HOST', $this->model->getSettings('site_email_host'));

          /**
          * SMTP Email port
          * default : 25 (https://www.arclab.com/en/kb/email/list-of-smtp-and-pop3-servers-mailserver-list.html)
          */
          define('EMAIL_PORT', $this->model->getSettings('site_email_port'));

          /**
          * SMTP Email authentication
          * default : ssl
          * choices : ssl, tls, (leave it empty)
          */
          define('EMAIL_STMP_SECURE', $this->model->getSettings('site_email_smtp'));

          /**
          * Optionall set a site email address.
          */
          define('SITEEMAIL', $this->model->getSettings('site_email_site'));
        }else{
          /**
          * SMTP Email Set Blank if not setup in system
          */
          define('EMAIL_HOST', 'localhost');
          define('EMAIL_PORT', '');
          define('EMAIL_STMP_SECURE', '');
          define('SITEEMAIL', '');
        }
        /********************
         *                  *
         *     RECAPTCHA    *
         *                  *
         ********************/
        // reCAPCHA site key provided by google for testing purposes
        define("RECAP_PUBLIC_KEY", $this->model->getSettings('site_recapcha_public'));
        // reCAPCHA secret key provided by google for testing purposes
        define("RECAP_PRIVATE_KEY", $this->model->getSettings('site_recapcha_private'));
        /*****************
         *                *
         *     Accounts    *
         *                *
         *****************/
        // New users need invite code to sign up if not null.
        define("SITE_USER_INVITE_CODE", $this->model->getSettings('site_user_invite_code'));
        // Account needs email activation, false=no true=yes
        define("ACCOUNT_ACTIVATION", $this->model->getSettings('site_user_activation'));
        // Max attempts for login before user is locked out
        define("MAX_ATTEMPTS", $this->model->getSettings('max_attempts'));
        // How long a user is locked out after they reach the max attempts
        define("SECURITY_DURATION", "+".$this->model->getSettings('security_duration')." minutes");
		    // this is the same as SECURITY_DURATION but in number format
		    $waittime = preg_replace("/[^0-9]/", "", SECURITY_DURATION); //DO NOT MODIFY
		    define('WAIT_TIME', $waittime); //DO NOT MODIFY
        //How long a session lasts : Default = +1 day
        define("SESSION_DURATION", "+".$this->model->getSettings('session_duration')." day");
        //How long a REMEMBER ME SESSION lasts : Default = +1 month
        define("SESSION_DURATION_RM", "+".$this->model->getSettings('session_duration_rm')." month");
        // min length of username
        define('MIN_USERNAME_LENGTH', $this->model->getSettings('min_username_length'));
        // max length of username
        define('MAX_USERNAME_LENGTH', $this->model->getSettings('max_username_length'));
        // min length of password
        define('MIN_PASSWORD_LENGTH', $this->model->getSettings('min_password_length'));
        // max length of password
        define('MAX_PASSWORD_LENGTH', $this->model->getSettings('max_password_length'));
        // min length of email
        define('MIN_EMAIL_LENGTH', $this->model->getSettings('min_email_length'));
        //max length of email
        define('MAX_EMAIL_LENGTH', $this->model->getSettings('max_email_length'));
        //random key used for password reset or account activation
        define('RANDOM_KEY_LENGTH', $this->model->getSettings('random_key_length'));
        /*****************
         *                *
         *     Theme      *
         *                *
         *****************/
        // Get them from settings
        define("SITE_THEME", $this->model->getSettings('site_theme'));
        /*****************
         *                *
         * Set Timezone   *
         *                *
         *****************/
        date_default_timezone_set($this->model->getSettings('default_timezone'));
        /*****************
         *                *
         *   Paginator    *
         *                *
         *****************/
        // Sets up users listing page limit
        $users_pageinator_limit = $this->model->getSettings('users_pageinator_limit');
        if(empty($users_pageinator_limit)){ $users_pageinator_limit = 20; }
        define('USERS_PAGEINATOR_LIMIT', $users_pageinator_limit);
        // Sets up friends listing page limit
        $friends_pageinator_limit = $this->model->getSettings('friends_pageinator_limit');
        if(empty($friends_pageinator_limit)){ $friends_pageinator_limit = 20; }
        define('FRIENDS_PAGEINATOR_LIMIT', $friends_pageinator_limit);
        /*****************
         *                *
         *   Messages     *
         *                *
         *****************/
         // Inbox and Outbox total Limit
         $message_quota_limit = $this->model->getSettings('message_quota_limit');
         if(empty($message_quota_limit)){ $message_quota_limit = 50; }
         define('MESSAGE_QUOTA_LIMIT', $message_quota_limit);
         // How many message to display per page
         $message_pageinator_limit = $this->model->getSettings('message_pageinator_limit');
         if(empty($message_pageinator_limit)){ $message_pageinator_limit = 20; }
         define('MESSAGE_PAGEINATOR_LIMIT', $message_pageinator_limit);
         /*****************
          *                *
          *    Sweets      *
          *                *
          *****************/
          // Sweets Purl Display
          define('SWEET_TITLE_DISPLAY', $this->model->getSettings('sweet_title_display'));
          // Sweets Singular Button Display
          define('SWEET_BUTTON_DISPLAY', $this->model->getSettings('sweet_button_display'));
          /*****************
           *                *
           *    Images      *
           *                *
           *****************/
           // Get Max Image Size
           define('IMG_MAX_SIZE', $this->model->getSettings('image_max_size'));
           /*****************
            *                *
            *  Site Message  *
            *                *
            *****************/
            // Get Site Wide Message
            define('SITE_WIDE_MESSAGE', $this->model->getSettings('site_message'));
            /*****************
             *                *
             *  Online Bubble *
             *                *
             *****************/
             // Get Site Wide Message
             define('ONLINE_BUBBLE', $this->model->getSettings('online_bubble'));
             /*****************
              *                *
              *  Auto Friend   *
              *                *
              *****************/
              // Enable Site Auto Friend and Provide User ID
              define('SITE_AUTO_FRIEND', $this->model->getSettings('site_auto_friend'));
              if(!empty($check_for_safi = $this->model->getSettings('site_auto_friend_id'))){
                define('SITE_AUTO_FRIEND_ID', $check_for_safi);
              }else{
                define('SITE_AUTO_FRIEND_ID', '1');
              }
    }

}
