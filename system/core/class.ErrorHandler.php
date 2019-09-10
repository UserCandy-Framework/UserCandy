<?php
/**
* System Error Class
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

class ErrorHandler {

    /** Standard URL Error **/
    static function show($type){
        /** initialise the AuthHelper object */
        $auth = new AuthHelper();

        $data['error_code'] = "404";
        if($type == '404'){
            $data['bodyText'] = "Oops! Looks like something went wrong!";
            $data['bodyText'] .= "<br>The Requested URL Does Not Exist!";
            $data['bodyText'] .= "<br>Please check your spelling and try again.";
        }else{
            $data['bodyText'] = "Oops! Looks like something went wrong!";
        }
        Load::View("Home::Error", $data);
    }

    /** User Profile Error **/
    static function profileError(){

        /** initialise the AuthHelper object */
        $auth = new AuthHelper();

        $data['error_code'] = "User Profile";
        $data['bodyText'] = "Oops! Looks like something went wrong!";
        $data['bodyText'] .= "<br>The Requested User Profile Does Not Exist!";
        $data['bodyText'] .= "<br>Please check your spelling and try again.";

        Load::View("Home::Error", $data);
    }

}
