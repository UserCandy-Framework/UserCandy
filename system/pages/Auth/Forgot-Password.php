<?php
/**
* Account Forgot Password Page
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

if($auth->isLogged())
		Url::redirect();

if(isset($_POST['submit'])){

		if (Csrf::isTokenValid('forgotpassword')) {
				$email = trim( Request::post('email') );

				if($auth->resetPass($email)){
						/** Success Message Display **/
						SuccessMessages::push(Language::show('resetpass_email_sent', 'Auth'), 'Forgot-Password');
				}else{
						/** Error Message Display **/
						ErrorMessages::push(Language::show('resetpass_email_error', 'Auth'), 'Forgot-Password');
				}
		}
}

$data['csrfToken'] = Csrf::makeToken('forgotpassword');
$data['title'] = Language::show('forgotpass_title', 'Auth');
$data['welcomeMessage'] = Language::show('forgotpass_welcomemessage', 'Auth');

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
              <input type="hidden" name="token_forgotpassword" value="<?=$data['csrfToken'];?>" />
              <button class="btn btn-primary" type="submit" name="submit"><?=Language::show('forgotpass_button', 'Auth')?></button>
          </div>
      </form>

    </div>
  </div>
</div>
