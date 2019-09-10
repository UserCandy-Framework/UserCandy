<?php
/**
* System Core Loader
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

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

/* Run the Core Loader */
require(SYSTEMDIR.'core/customhelperloader.php');
