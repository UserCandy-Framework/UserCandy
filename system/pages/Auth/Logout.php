<?php
/**
* Account Login View
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.3
*/

use Core\Language;
use Helpers\SuccessMessages;

if ($auth->isLogged()) {
    $u_id = $auth->currentSessionInfo()['uid'];
    $usersModel->remove($u_id);
    $auth->logout();
}
/** Success Message Display **/
SuccessMessages::push(Language::show('logout', 'Auth'), '');
