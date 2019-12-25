<?php
/**
* Account Activate Page
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

use Core\Language;
use Helpers\{Csrf,Request,SuccessMessages,ErrorMessages,Url};

if ($auth->isLogged())
		Url::redirect();

/** Get data from URL **/
(empty($viewVars[0])) ? $val1 = null : $val1 = $viewVars[0];
(empty($viewVars[1])) ? $username = "" : $username = $viewVars[1];
(empty($viewVars[2])) ? $val3 = null : $val3 = $viewVars[2];
(empty($viewVars[3])) ? $activekey = "" : $activekey = $viewVars[3];

$activekey = trim( $activekey );

if($auth->activateAccount($username, $activekey)) {
		/** Success Message Display **/
		SuccessMessages::push(Language::show('activate_success', 'Auth'), 'Login');
}
else{
		/** Error Message Display **/
		ErrorMessages::push(Language::show('activate_fail', 'Auth'), 'Resend-Activation-Email');
}

$data['title'] = Language::show('activate_title', 'Auth');
$data['welcomeMessage'] = Language::show('activate_welcomemessage', 'Auth');

/** Setup Breadcrumbs **/
$data['breadcrumbs'] = "<li class='breadcrumb-item active'>".$data['title']."</li>";


?>

<div class="col-lg-12">
	<div class="card mb-3">
		<div class="card-header h4">
        <h1><?php echo $data['title']; ?></h1>
    </div>
  </div>
</div>
