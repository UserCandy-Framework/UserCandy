<?php
/**
* Site Change Language Page
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

use Helpers\Url;

/** Get data from URL **/
(empty($viewVars[0])) ? $new_lang_code = null : $new_lang_code = $viewVars[0];

if(isset($new_lang_code)){
  $_SESSION['cur_lang'] = $new_lang_code;
  /**
  * Check to see if user came from another page within the site
  */
  if(isset($_SESSION['login_prev_page'])){ $lang_prev_page = $_SESSION['login_prev_page']; }else{ $lang_prev_page = SITE_URL; }
  /**
  * Checking to see if user user was viewing anything before lang change
  * If they were viewing a page on this site, then after lange change
  * send them to that page they were on.
  */
  if(!empty($lang_prev_page)){
    /* Clear the prev page session if set */
    if(isset($_SESSION['login_prev_page'])){
      unset($_SESSION['login_prev_page']);
    }
    $prev_page = $lang_prev_page;
    /* Send user back to page they were at before lang change */
    Url::redirect($prev_page);
  }else{
    Url::redirect();
  }
}else{
  Url::redirect();
}
