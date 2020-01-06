<?php
/**
* Account Registration View
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

use Core\Language;
use Helpers\{Url,Request,Csrf,SuccessMessages,ErrorMessages,Form};
use Models\AdminPanelModel;

//Redirect user to home page if he is already logged in
if ($auth->isLogged())
		Url::redirect();

/** Get Terms and Privacy Data **/
$AdminPanelModel = new AdminPanelModel();
$site_terms = $AdminPanelModel->getSettings('site_terms_content');
$site_privacy = $AdminPanelModel->getSettings('site_privacy_content');

//The form is submmited
if (isset($_POST['submit'])) {
		// Get Post Data just in case of fail
		$data['username'] = Request::post('username');
		$data['email'] = Request::post('email');
		$data['agree_terms_policy'] = Request::post('agree_terms_policy');
		//Check the CSRF token first
		if(Csrf::isTokenValid('register')) {
				$captcha_fail = false;
				//Check the reCaptcha if the public and private keys were provided
				if (RECAP_PUBLIC_KEY != "" && RECAP_PRIVATE_KEY != "") {
					if(isset($_POST['g-recaptcha-response'])){
			    	$captcha=$_POST['g-recaptcha-response'];
			    }else{
			    	$captcha = false;
					}
			    if(!$captcha){
			      $captcha_fail = true;
			    }else{
            $secret = RECAP_PRIVATE_KEY;
            $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
						// use json_decode to extract json response
            if($response.'success'==false){
                $captcha_fail = true;
            }
			    }
				}
				/** Check if Terms and Privacy is enabled **/
				if(!empty($site_terms) || !empty($site_privacy)){
					/** Check to see if user agreed to Terms and Policy **/
					if($data['agree_terms_policy'] != "true"){
						/** Error Message Display **/
						ErrorMessages::push(Language::show('register_error', 'Auth'), 'Register');
					}
				}
				/** Check for site user invite code **/
				$site_user_invite_code = strip_tags( trim( Request::post('site_user_invite_code') ) );
				$site_user_invite_code_db = SITE_USER_INVITE_CODE;
				if(!empty($site_user_invite_code_db)){
					if($site_user_invite_code != $site_user_invite_code_db){
						/** Error Message Display **/
						ErrorMessages::push(Language::show('register_error', 'Auth'), 'Register');
					}
				}
				/* Get User Bot Protection Field - Should be empty if Human */
				$ubp_name = Request::post('ubp_name');
				/** Only continue if captcha did not fail **/
				if (!$captcha_fail && empty($ubp_name)) {
						$username = strip_tags( trim( Request::post('username') ) );
						$password = Request::post('password');
						$verifypassword = Request::post('passwordc');
						$email = trim ( Request::post('email') );
						/** Register with our without email verification **/
						$registered = $auth->register($username, $password, $verifypassword, $email);
						/** Check for New User Registration Success **/
						if ($registered == 'registered') {
								/** Check to see if Account Activation is required **/
								$account_activation = ACCOUNT_ACTIVATION;
								if($account_activation == "true"){
										$data['message'] = Language::show('register_success', 'Auth');
								}else{
										$data['message'] = Language::show('register_success_noact', 'Auth');
								}
								/** Success Message Display **/
								SuccessMessages::push($data['message'], 'Login');
						}
						else{
								/** Error Message Display **/
								$data['error'] = Language::show('register_error', 'Auth').$registered;
						}
				}
				else{
						/** Error Message Display **/
						$data['error'] = Language::show('register_error_recap', 'Auth');
				}
		}
		else{
				/** Error Message Display **/
				$data['error'] = Language::show('register_error', 'Auth');
		}
}

$data['csrfToken'] = Csrf::makeToken('register');
$data['title'] = Language::show('register_page_title', 'Auth');
$data['welcomeMessage'] = Language::show('register_page_welcomeMessage', 'Auth');

/** Let Site Know if Invite Code is enabled **/
$site_user_invite_code_db = SITE_USER_INVITE_CODE;
if(!empty($site_user_invite_code_db)){ $data['invite_code'] = true; }

/** needed for recaptcha **/
if (RECAP_PUBLIC_KEY != "" && RECAP_PRIVATE_KEY != "") {
		$js .= "
			<script src='https://www.google.com/recaptcha/api.js?render=".RECAP_PUBLIC_KEY."'></script>
			<script>
			    grecaptcha.ready(function() {
			    // do request for recaptcha token
			    // response is promise with passed token
			        grecaptcha.execute('".RECAP_PUBLIC_KEY."', {action:'validate_captcha'})
			                  .then(function(token) {
			            // add token value to form
			            document.getElementById('g-recaptcha-response').value = token;
			        });
			    });
			</script>
		";
}

/** Get lang Code **/
$langeCode = Language::setLang();
/** Add JS Files requried for live checks **/
$js .= "<script type='text/javascript'>
								var char_limit = {
									username_min: '".MIN_USERNAME_LENGTH."',
									username_max: '".MAX_USERNAME_LENGTH."',
									password_min: '".MIN_PASSWORD_LENGTH."',
									password_max: '".MAX_PASSWORD_LENGTH."',
									email_min: '".MIN_EMAIL_LENGTH."',
									email_max: '".MAX_EMAIL_LENGTH."'
								};
							</script>";
$js .= "<script src='".Url::templatePath()."js/lang.".$langeCode.".js'></script>";
$js .= "<script src='".Url::templatePath()."js/live_email.js'></script>";
$js .= "<script src='".Url::templatePath()."js/live_username_check.js'></script>";
$js .= "<script src='".Url::templatePath()."js/password_strength_match.js'></script>";

/** Display Error Messages **/
if(isset($data['error'])) { echo ErrorMessages::display_raw($data['error']); }

?>

<div class="form-signin col-sm-12">
	<div class="card my-3 text-center">
		<div class="card-header h4">
			<?=$data['title'];?>
		</div>
		<div class="card-body">
			<p><?=$data['welcomeMessage'];?></p>

			<?php echo Form::open(array('method' => 'post')); ?>

				<!-- Username -->
					<div class='form-group'>
						<div class='input-group mb-3'>
							<div class='input-group-prepend'>
								<span class='input-group-text'><i class='fas fa-user'></i></span>
							</div>
							<?php echo Form::input(array('id' => 'username', 'name' => 'username', 'class' => 'form-control', 'placeholder' => Language::show('register_field_username', 'Auth'), 'value' => $data['username'])); ?>
							<div class='input-group-append'>
								<span id='resultun' class='input-group-text'></span>
							</div>
						</div>
					</div>

				<!-- Password 1 -->
					<div class='form-group'>
						<div class='input-group mb-3'>
							<div class='input-group-prepend'>
								<span class='input-group-text'><i class='fas fa-lock'></i></span>
							</div>
							<?php echo Form::input(array('id' => 'passwordInput', 'type' => 'password', 'name' => 'password', 'class' => 'form-control', 'placeholder' => Language::show('register_field_password', 'Auth'))); ?>
							<div class='input-group-append'>
								<span id='password01' class='input-group-text'></span>
							</div>
						</div>
					</div>

				<!-- Password 2 -->
					<div class='form-group'>
						<div class='input-group mb-3'>
							<div class='input-group-prepend'>
								<span class='input-group-text'><i class='fas fa-lock'></i></span>
							</div>
							<?php echo Form::input(array('id' => 'confirmPasswordInput', 'type' => 'password', 'name' => 'passwordc', 'class' => 'form-control', 'placeholder' => Language::show('register_field_confpass', 'Auth'))); ?>
							<div class='input-group-append'>
								<span id='password02' class='input-group-text'></span>
							</div>
						</div>
					</div>

				<!-- Email -->
				<div class='form-group'>
					<div class='input-group mb-3'>
						<div class='input-group-prepend'>
							<span class='input-group-text'><i class='fas fa-envelope'></i></span>
						</div>
						<?php echo Form::input(array('id' => 'email', 'type' => 'text', 'name' => 'email', 'class' => 'form-control', 'placeholder' => Language::show('register_field_email', 'Auth'), 'value' => $data['email'])); ?>
						<div class='input-group-append'>
							<span id='resultemail' class='input-group-text'></span>
						</div>
					</div>
				</div>

				<?php if($data['invite_code']){ ?>
				<!-- Invite Code -->
				<div class='form-group'>
					<div class='input-group mb-3'>
						<div class='input-group-prepend'>
							<span class='input-group-text'><i class='fas fa-envelope'></i></span>
						</div>
						<?php echo Form::input(array('id' => 'site_user_invite_code', 'type' => 'text', 'name' => 'site_user_invite_code', 'class' => 'form-control', 'placeholder' => Language::show('register_field_invite', 'Auth'))); ?>
					</div>
				</div>
				<?php } ?>

				<!-- reCAPTCHA -->
				<input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
    		<input type="hidden" name="action" value="validate_captcha">

				<!-- CSRF Token -->
				<input type="hidden" name="token_register" value="<?=$data['csrfToken'];?>" />

				<!-- UBP Name Protection -->
				<input type="text" name="ubp_name" value="" class="hidden" />

				<!-- Error Msg Display -->
				<span id='resultun2' class='label'></span>
				<span class='label' id='passwordStrength'></span>
				<span id='resultemail2' class='label'></span>

				<?php
					/** Check to see if Terms and Privacy are enabled **/
					if(!empty($site_terms) || !empty($site_privacy)){
				?>
				<label class="control-label">
					<input type="checkbox" name="agree_terms_policy" value="true"> <?php echo Language::show('agree_terms_policy', 'Auth'); ?>
				</label>
				<?php } ?>

				<button class="btn btn-lg btn-success btn-block" name="submit" type="submit">
					<i class="fas fa-user-plus"></i> <?php echo Language::show('register_button', 'Auth'); ?>
				</button>
			<?php echo Form::close(); ?>

    </div>
  </div>
</div>
