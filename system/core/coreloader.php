<?php
/**
* System Core Loader
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

use Core\{Router,Language};
use Helpers\{AuthHelper,PageFunctions,SiteStats,CurrentUserData};
use Models\UsersModel;

/** Create Pages Folder if not exist **/
if (!file_exists(ROOTDIR.'custom/pages')) {
    mkdir(ROOTDIR.'custom/pages', 0777, true);
}
/** Check to see if Home, About, and Contact pages exist in custom/pages folder **/
if(!file_exists(CUSTOMDIR.'pages/Home.php')){
  copy(SYSTEMDIR.'pages/Home/Home.php', CUSTOMDIR.'pages/Home.php');
}
if(!file_exists(CUSTOMDIR.'pages/About.php')){
  copy(SYSTEMDIR.'pages/Home/About.php', CUSTOMDIR.'pages/About.php');
}
if(!file_exists(CUSTOMDIR.'pages/Contact.php')){
  copy(SYSTEMDIR.'pages/Home/Contact.php', CUSTOMDIR.'pages/Contact.php');
}

/** Initialise the Router object **/
$routes = Router::extendedRoutes();
/** initialise the AuthHelper object */
$auth = new AuthHelper();
/** initialise the Users object */
$user = new UsersModel();
/** initialise the PageFunctions object */
$PageFunctions = new PageFunctions();
/** Get Current User Data if logged in */
if ($auth->isLogged()) {
    $u_id = $auth->currentSessionInfo()['uid'];
    $user->update($u_id);
}else{
    $u_id = null;
}
/** Log All Activity to Site Logs **/
if($u_id != null){
    SiteStats::log(CurrentUserData::getUserName($u_id));
}else{
    SiteStats::log();
}
/** Clean offline users from DB */
$user->cleanOfflineUsers();
/** initialise the language object */
$language = new Language();
/** Check Page Permissions **/
/** Set userID to 0 if null **/
if(!isset($u_id)){$u_id='0';}
$PageFunctions->systemPagePermission($u_id);
