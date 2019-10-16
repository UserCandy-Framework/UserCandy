<?php

/**
 * Cross Site Request Forgery Helper.
 *
 * UserCandy
 * @author David (DaVaR) Sargent <davar@usercandy.com>
 * @version 1.0.0
 */

/**
 * Instructions:
 * At the top of the controller where the other "use" statements are, place:
 * use Libs\Csrf;
 *
 * Just prior to rendering the view for adding or editing data, create the CSRF token:
 * $data['csrfToken'] = Csrf::makeToken();
 * $this->view->renderTemplate('header', $data);
 * $this->view->render('pet/edit', $data, $error); // as an example
 * $this->view->renderTemplate('footer', $data);
 *
 * At the bottom of your form, before the submit button put:
 * <input type="hidden" name="csrfToken" value="<?= $data['csrfToken']; ?>" />
 *
 * These lines need to be placed in the controller action to validate CSRF token submitted with the form:
 * if (!Csrf::isTokenValid()) {
 *      Url::redirect('admin/login'); // or wherever you want to redirect to.
 *    }
 * And that's all.
 */

namespace Helpers;

class Csrf {
    /**
     * Retrieve the CSRF token and generate a new one if expired.
     *
     * @access public
     * @static static method
     * @return string
     */
    public static function makeToken($name = 'csrfToken') {
        $max_time = 60 * 60 * 24; // token is valid for 1 day.
        $csrfToken = Session::get($name);
        $stored_time = Session::get($name . '_time');

        if ($max_time + $stored_time <= time() || empty($csrfToken)) {
            $hash = hash('sha512', self::genRandomNumber());
            Session::set($name, $hash);
            Session::set($name . '_time', time());
        }

        return Session::get($name);
    }

    /**
     * Check to see if the CSRF token in session is the same as submitted form.
     *
     * @access public
     * @static static method
     * @return bool
     */
    public static function isTokenValid($name = 'csrfToken') {
      if ((isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']))) {
        if (strtolower(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST)) != strtolower($_SERVER['HTTP_HOST'])) {
          /* referer not from the same domain */
          return false;
        }else{
          return Request::post('token_'.$name) === Session::get($name);
        }
      }else{
        return false;
      }

    }
    /**
     * Generate a random number using any available function on the system.
     *
     * @access public
     * @static static method
     * @return integer
     */

    public static function genRandomNumber($size = 32) {
        if (extension_loaded('openssl')) {
            return openssl_random_pseudo_bytes($size);
        }
        if (extension_loaded('mcrypt')) {
            return mcrypt_create_iv($size, MCRYPT_DEV_URANDOM);
        }
        if (function_exists('random_bytes')) {
            return random_bytes($size);
        }
        return mt_rand(0,mt_getrandmax());

    }

}
