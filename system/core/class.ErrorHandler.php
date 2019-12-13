<?php
/**
* System Error Class
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

namespace Core;

use Helpers\AuthHelper;

class ErrorHandler {

    /** Standard URL Error **/
    static function show($type){
        /** initialise the AuthHelper object */
        $auth = new AuthHelper();

        $data['error_code'] = "404";
        if($type == '404'){
          $data['errorTitle'] = Language::show('404title', '404');
          $data['bodyText'] = Language::show('404content', '404');
        }else{
          $data['errorTitle'] = Language::show('404title', '404');
          $data['bodyText'] = Language::show('404content', '404');
        }
        Load::View("Home::Error", $data);
    }

    /** User Profile Error **/
    static function profileError(){

        /** initialise the AuthHelper object */
        $auth = new AuthHelper();

        $data['errorTitle'] = Language::show('profileErrorTitle', '404');
        $data['bodyText'] = Language::show('profileError', '404');

        Load::View("Home::Error", $data);
    }

}
