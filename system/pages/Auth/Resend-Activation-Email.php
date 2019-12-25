<?php
/**
* Account Resend Activation Page
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

use Core\Language;
use Helpers\{Csrf,Request,SuccessMessages,ErrorMessages,Url};

if ($auth->isLogged())
		Url::redirect();

if (isset($_POST['submit']) && Csrf::isTokenValid('resendactivation')) {
		$email = trim( Request::post('email') );

		if($auth->resendActivation($email)){
				/** Success Message Display **/
				SuccessMessages::push(Language::show('resendactivation_success', 'Auth'), 'Login');
		}
		else{
				/** Error Message Display **/
				ErrorMessages::push(Language::show('resendactivation_error', 'Auth'), 'Resend-Activation-Email');
		}
}

$data['csrfToken'] = Csrf::makeToken('resendactivation');
$data['title'] = Language::show('resendactivation_title', 'Auth');
$data['welcomeMessage'] = Language::show('resendactivation_welcomemessage', 'Auth');

/** Check to see if user is logged in **/
$data['isLoggedIn'] = $auth->isLogged();

/** Setup Breadcrumbs **/
$data['breadcrumbs'] = "<li class='breadcrumb-item active'>".$data['title']."</li>";

?>

<div class="col-lg-12 col-md-12 col-sm-12">
	<div class="card mb-3">
		<div class="card-header h4">
			<?=$data['title'];?>
		</div>
		<div class="card-body">
			<p><?=$data['welcomeMessage'];?></p>

      <form class="form" method="post">
          <div class="col-xs-12">
						<div class="form-group">
							<div class="input-group mb-3">
								<div class='input-group-prepend'>
									<span class='input-group-text'>
										<?=Language::show('register_field_email', 'Auth'); ?>
									</span>
								</div>
								<input  class="form-control" type="email" id="email" name="email" placeholder="<?=Language::show('register_field_email', 'Auth'); ?>">
							</div>
						</div>
              <input type="hidden" name="token_resendactivation" value="<?=$data['csrfToken'];?>" />
              <button class="btn btn-primary" type="submit" name="submit"><?=Language::show('activate_send_button', 'Auth')?></button>
          </div>
      </form>

    </div>
  </div>
</div>
