<?php
/**
* Account Reset Password View
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

if($auth->isLogged())
		Url::redirect();

		/** Get data from URL **/
		(empty($viewVars[0])) ? $val1 = null : $val1 = $viewVars[0];
		(empty($viewVars[1])) ? $username = "" : $username = $viewVars[1];
		(empty($viewVars[2])) ? $val3 = null : $val3 = $viewVars[2];
		(empty($viewVars[3])) ? $resetkey = "" : $resetkey = $viewVars[3];

if($auth->checkResetKey($username, $resetkey)){
		if(isset($_POST['submit'])){
				if (Csrf::isTokenValid('resetpassword')) {
						$password = Request::post('password');
						$confirm_password = Request::post('confirmPassword');

						if($auth->resetPass('', $username, $resetkey, $password, $confirm_password)){
								/** Success Message Display **/
								SuccessMessages::push(Language::show('resetpass_success', 'Auth'), 'Login');
						}
						else{
								/** Error Message Display **/
								ErrorMessages::push(Language::show('resetpass_error', 'Auth'), 'Forgot-Password');
						}
				}
		}
}
else{
		$data['message'] = "Some Error Occurred";
		/** Error Message Display **/
		ErrorMessages::push($data['message'], 'Forgot-Password', 'Auth');
}

$data['csrfToken'] = Csrf::makeToken('resetpassword');
$data['title'] = Language::show('resetpass_title', 'Auth');
$data['welcomeMessage'] = Language::show('resetpass_welcomemessage', 'Auth');

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
        <div class="row">
            <form class="form" method="post">
                <div class="col-xs-12">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class='input-group-prepend'>
												<span class='input-group-text'>
													<?=Language::show('new_password_label', 'Auth'); ?>
												</span>
											</div>
											<input  class="form-control" type="password" id="password" name="password" placeholder="<?=Language::show('new_password_label', 'Auth'); ?>">
										</div>
									</div>
									<div class="form-group">
										<div class="input-group mb-3">
											<div class='input-group-prepend'>
												<span class='input-group-text'>
													<?=Language::show('confirm_new_password_label', 'Auth'); ?>
												</span>
											</div>
											<input  class="form-control" type="password" id="confirmPassword" name="confirmPassword" placeholder="<?=Language::show('confirm_new_password_label', 'Auth'); ?>">
										</div>
									</div>

                    <input type="hidden" name="token_resetpassword" value="<?=$data['csrfToken'];?>" />
                    <button class="btn btn-primary" type="submit" name="submit"><?=Language::show('change_my_password_button', 'Auth'); ?></button>
                </div>

            </form>
        </div>
      </div>
    </div>
  </div>
