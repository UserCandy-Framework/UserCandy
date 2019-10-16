<?php
/**
* Language Class
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

namespace Core;

/**
 * Language class to load the requested language file.
 */
class Language
{
    /**
     * Variable holds array with language.
     *
     * @var array
     */
    private $lang_array;

    /**
     * Variable for current language code.
     *
     * @var string
     */
    private $code;

    /**
     * Check to see if user changed the language from default
     */
    public function __construct()
    {

    }

    /**
     * Load language function.
     *
     * @param string $name
     * @param string $code
     */
    public function load($name)
    {
        $code = SELF::setLang();
        /** lang file */
        $file = SYSTEMDIR."/language/$code/$name.php";

        /** check if is readable */
        if (is_readable($file)) {
            /** require file */
            $lang_array[$code] = require $file;
        } else {
            /** display error */
            echo ErrorMessages::display_raw("Could not load language file '$code/$name.php'");
            die;
        }
    }

    /**
     * Get element from language array by key.
     *
     * @param  string $value
     *
     * @return string
     */
    public function get($value)
    {
        $code = SELF::setLang();
        if (!empty($lang_array[$code][$value])) {
            return $lang_array[$code][$value];
        } elseif(!empty($lang_array[LANGUAGE_CODE][$value])) {
            return $lang_array[LANGUAGE_CODE][$value];
        } else {
            return $value;
        }
    }

    /**
     * Get lang for views.
     *
     * @param  string $value this is "word" value from language file
     * @param  string $name  name of file with language
     * @param  string $code  optional, language code
     *
     * @return string
     */
    public static function show($value, $name)
    {
        $code = SELF::setLang();

        /** lang file */
        $file = SYSTEMDIR."/language/$code/$name.php";

        /** check if is readable */
        if (is_readable($file)) {
            /** require file */
            $lang_array = require($file);
        } else {
            /** display error */
            echo ErrorMessages::display_raw("Could not load language file '$code/$name.php'");
            die;
        }
        if (!empty($lang_array[$value])) {
            return $lang_array[$value];
        } else {
            /** Selected lang value not in lang file
            *   Log the value so that we know to add
            **/
            $file = SYSTEMDIR.'/logs/missing-lang.log';
            $string = "'$value' => \"\",";
            $url = $_SERVER['REQUEST_URI'];
            $logMessage = "//$name - $url \n $string\n";
            $handle = fopen($file, 'r');
            $valid = false; // init as false
            while (($buffer = fgets($handle)) !== false) {
                if (strpos($buffer, $string) !== false) {
                    $valid = TRUE;
                    break; // Once you find the string, you should break out the loop.
                }
            }
            fclose($handle);
            if($valid == FALSE){
              file_put_contents($file, $logMessage, FILE_APPEND);
            }
            return $value;
        }
    }

    /**
     * Get List of All Enabled Languages from LangList.php
     *
     */
    public static function getlangs()
    {
        $code = SELF::setLang();
        /** lang list file */
        $file = SYSTEMDIR."/language/LangList.php";
        /** check if is readable */
        if (is_readable($file)) {
          /** require file */
          $lang_array = require($file);
          return $lang_array;
        } else {
          /** display error */
          echo ErrorMessages::display_raw("Could not load language file '$code/$name.php'");
          die;
        }
    }

    public static function setLang()
    {
        if(isset($_SESSION['cur_lang'])){
            $code = $_SESSION['cur_lang'];
        }else{
            if(!isset($code)){
                $code = LANGUAGE_CODE;
            }
        }
        return $code;
    }
}
