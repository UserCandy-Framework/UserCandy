<?php
/**
 * Auth Cookie Class
 *
 * UserCandy
 * @author David (DaVaR) Sargent <davar@usercandy.com>
 * @version uc 1.0.3
 */

namespace Core;

class Cookie {

    public static function exists($key) {
        return isset($_COOKIE[$key]);
    }

    public static function set($key, $value, $expiry = "", $path = "/", $domain = false) {
        $retval = false;
        if (!headers_sent()) {
            if ($domain === false)
                $domain = $_SERVER['HTTP_HOST'];

            $retval = setcookie($key, $value, $expiry, $path, $domain);
            if ($retval)
                $_COOKIE[$key] = $value;
        }
        return $retval;
    }

    public static function get($key, $default = '') {
        return $_COOKIE[$key] ?? $default;
    }

    public static function display() {
        return $_COOKIE;
    }

    public static function destroy($key, $value = '', $path = "/", $domain = "") {
        if (isset($_COOKIE[$key])) {
            unset($_COOKIE[$key]);
            setcookie($key, $value, time() - 3600, $path, $domain);
        }
    }

}
