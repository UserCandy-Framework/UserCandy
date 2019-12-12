<?php
/**
* Account Change E-Mail Page
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

use Core\Language;
use Helpers\{ErrorMessages,Csrf,Request,SuccessMessages,Form,Url};

if (!$auth->isLogged())
  /** User Not logged in - kick them out **/
  ErrorMessages::push(Language::show('user_not_logged_in', 'Auth'), 'Login');

  /* Load Top Extender for Change-Email */
  Core\Extender::load_ext('Change-Email', 'top');


if(isset($_POST['submit'])){

    if(Csrf::isTokenValid('changeemail')) {
        $password = Request::post('passwordemail');
        $newEmail = trim( Request::post('email') );
        $username = $auth->currentSessionInfo()['username'];

        /* Load Form Submit Extender for Change-Email */
        Core\Extender::load_ext('Change-Email', 'formSubmit');

        if($auth->changeEmail($username, $newEmail, $password)){
            /** Success Message Display **/
            SuccessMessages::push(Language::show('changeemail_success', 'Auth'), 'Change-Email');
        }
        else{
            /** Error Message Display **/
            ErrorMessages::push(Language::show('changeemail_error', 'Auth'), 'Change-Email');
        }
    }
}

$data['csrfToken'] = Csrf::makeToken('changeemail');
$data['title'] = Language::show('changeemail_title', 'Auth');
$data['welcomeMessage'] = Language::show('changeemail_welcomemessage', 'Auth');

/** Get Current User's userID and Email **/
$u_id = $auth->user_info();
$data['email'] = $usersModel->getUserEmail($u_id);

/** Setup Breadcrumbs **/
$data['breadcrumbs'] = "<li class='breadcrumb-item'><a href='".SITE_URL."Account-Settings'>".Language::show('account_settings_title', 'Auth')."</a></li><li class='breadcrumb-item active'>".$data['title']."</li>";

/** Get lang Code **/
$langeCode = Language::setLang();

/** Add JS Files requried for live checks **/
$js = "<script type='text/javascript'>
                var char_limit = {
                  email_min: '".MIN_EMAIL_LENGTH."',
                  email_max: '".MAX_EMAIL_LENGTH."'
                };
              </script>";
$js .= "<script src='".Url::templatePath()."js/lang.".$langeCode.".js'></script>";
$js .= "<script src='".Url::templatePath()."js/live_email.js'></script>";


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
						<div class='input-group mb-3'>
							<div class='input-group-prepend'>
								<span class='input-group-text'><i class='fas fa-lock'></i></span>
							</div>
							<?php echo Form::input(array('type' => 'password', 'name' => 'passwordemail', 'class' => 'form-control', 'placeholder' => Language::show('current_password', 'Members'))); ?>
						</div>
				</div>

				<!-- Email -->
				<div class='form-group'>
						<div class='input-group mb-3'>
							<div class='input-group-prepend'>
								<span class='input-group-text'><i class='fas fa-envelope'></i></span>
							</div>
							<?php echo Form::input(array('id' => 'email', 'type' => 'text', 'name' => 'email', 'class' => 'form-control', 'placeholder' => $data['email'])); ?>
							<div class='input-group-prepend'>
								<span id='resultemail' class='input-group-text'></span>
							</div>
						</div>
				</div>

<?php
        /* Load Form Extender for Change-Email */
        Core\Extender::load_ext('Change-Email', 'form');
?>

				<!-- Error Message Display -->
				<span id='resultemail2' class='label'></span>

				<!-- CSRF Token -->
				<input type="hidden" name="token_changeemail" value="<?php echo $data['csrfToken']; ?>" />
				<button class="btn btn-md btn-success" name="submit" type="submit">
					<?=Language::show('change_email_button', 'Members'); ?>
				</button>
			<?php echo Form::close(); ?>
    </div>
  </div>
</div>

<?php
/* Load Bottom Extender for Change-Email */
Core\Extender::load_ext('Change-Email', 'Bottom');
?>
