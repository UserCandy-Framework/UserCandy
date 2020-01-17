<?php
/**
* Account Change Password Page
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

use Core\Language;
use Helpers\{ErrorMessages,Csrf,Request,SuccessMessages,Url,Form};

if (!$auth->isLogged())
	/** User Not logged in - kick them out **/
	ErrorMessages::push(Language::show('user_not_logged_in'), 'Login');

	/* Load Top Extender for Change-Password */
  Core\Extender::load_ext('Change-Password', 'top');

if(isset($_POST['submit'])){

		if (Csrf::isTokenValid('changepassword')) {
				$currentPassword = Request::post('currpassword');
				$newPassword = Request::post('password');
				$confirmPassword = Request::post('passwordc');

				// Get Current User's UserName
				$u_username = $auth->currentSessionInfo()['username'];

				/* Load Form Submit Extender for Change-Password */
        Core\Extender::load_ext('Change-Password', 'formSubmit');

				if($auth->changePass($u_username, $currentPassword, $newPassword, $confirmPassword)){
						/** Success Message Display **/
						SuccessMessages::push(Language::show('resetpass_success', 'Auth'), 'Change-Password');
				}
				else{
						/** Error Message Display **/
						ErrorMessages::push(Language::show('resetpass_error', 'Auth'), 'Change-Password');
				}
		}
}

$data['csrfToken'] = Csrf::makeToken('changepassword');
$data['title'] = Language::show('changepass_title', 'Auth');
$data['welcomeMessage'] = Language::show('changepass_welcomemessage', 'Auth');

/** Setup Breadcrumbs **/
$data['breadcrumbs'] = "<li class='breadcrumb-item'><a href='".SITE_URL."Account-Settings'>".Language::show('account_settings_title', 'Auth')."</a></li><li class='breadcrumb-item active'>".$data['title']."</li>";

/** Get lang Code **/
$langeCode = Language::setLang();

/** Add JS Files requried for live checks **/
$js = "<script type='text/javascript'>
								var char_limit = {
									password_min: '".MIN_PASSWORD_LENGTH."',
									password_max: '".MAX_PASSWORD_LENGTH."'
								};
							</script>";
$js .= "<script src='".Url::templatePath()."js/lang.".$langeCode.".js'></script>";
$js .= "<script src='".Url::templatePath()."js/password_strength_match.js'></script>";

?>

<div class="col-lg-12 col-md-12 col-sm-12">
	<div class="card mb-3">
		<div class="card-header h4">
			<?=$data['title'];?>
		</div>
		<div class="card-body">
			<p><?=$data['welcomeMessage'];?></p>
			<?php echo Form::open(array('method' => 'post')); ?>
				<!-- Current Password -->
				<div class='form-group'>
					<div class='form-group'>
						<div class='input-group mb-3'>
							<div class='input-group-prepend'>
								<span class='input-group-text'><i class='fas fa-lock'></i></span>
							</div>
							<?php echo Form::input(array('type' => 'password', 'name' => 'currpassword', 'class' => 'form-control', 'placeholder' => Language::show('current_password', 'Members'))); ?>
						</div>
					</div>
				</div>

				<!-- Password 1 -->
				<div class='form-group'>
					<div class='form-group'>
						<div class='input-group mb-3'>
							<div class='input-group-prepend'>
								<span class='input-group-text'><i class='fas fa-lock'></i></span>
							</div>
							<?php echo Form::input(array('id' => 'passwordInput', 'type' => 'password', 'name' => 'password', 'class' => 'form-control', 'placeholder' => Language::show('new_password', 'Members'))); ?>
							<span id='password01' class='input-group-text'></span>
						</div>
					</div>
				</div>

				<!-- Password 2 -->
				<div class='form-group'>
					<div class='form-group'>
						<div class='input-group mb-3'>
							<div class='input-group-prepend'>
								<span class='input-group-text'><i class='fas fa-lock'></i></span>
							</div>
							<?php echo Form::input(array('id' => 'confirmPasswordInput', 'type' => 'password', 'name' => 'passwordc', 'class' => 'form-control', 'placeholder' => Language::show('confirm_new_password', 'Members'))); ?>
							<span id='password02' class='input-group-text'></span>
						</div>
					</div>
				</div>

				<?php
				        /* Load Form Extender for Change-Password */
				        Core\Extender::load_ext('Change-Password', 'form');
				?>

				<!-- Display Live Password Status -->
				<span class='label' id='passwordStrength'></span>

				<!-- CSRF Token -->
				<input type="hidden" name="token_changepassword" value="<?=$data['csrfToken'];?>" />
				<button class="btn btn-md btn-success" name="submit" type="submit">
					<?=Language::show('change_password_button', 'Members');?>
				</button>
			<?php echo Form::close(); ?>
    </div>
  </div>
</div>

<?php
/* Load Bottom Extender for Change-Password */
Core\Extender::load_ext('Change-Password', 'Bottom');
?>
