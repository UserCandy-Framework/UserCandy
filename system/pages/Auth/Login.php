<?php
/**
* Account Login View
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

use Core\Language;
use Helpers\{Url,Request,SuccessMessages,ErrorMessages,Csrf,SiteStats};

/* Check to see if user is already logged in */
        if ($auth->isLogged())
            Url::redirect();

        /* Get User Bot Protection Field - Should be empty if Human */
        $ubp_name = Request::post('ubp_name');

        /* Start the Login Process */
        if (isset($_POST['submit']) && Csrf::isTokenValid('login') && empty($ubp_name)) {

            $username = strip_tags( trim( Request::post('username') ) );
            $password = Request::post('password');
            $rememberMe = null !=  strip_tags( trim( Request::post('rememberMe') ) );

            $email = $auth->checkIfEmail($username);
            $username = $email && (count($email) != 0 ) ? $email[0]->username : $username;

            if ($auth->login($username, $password, $rememberMe)) {
                $userId = $auth->currentSessionInfo()['uid'];

                /** Update the last login timestamp for user to now **/
                $info = array('LastLogin' => date('Y-m-d G:i:s'));
                $where = array('userID' => $userId);
                $auth->updateUser($info,$where);

                $usersModel->update($userId);

                /** Check if user is on new device, if so then add to database **/
                $device_data = SiteStats::updateUserDeviceInfo($userId);

                /** Check if Device is enabled for user **/
                if($device_data[0]->allow == "0"){
                  /** Send Email letting user know someone that was blocked tried to access their account **/
                  $email = \Helpers\CurrentUserData::getUserEmail($userId);
                  $mail = new \Helpers\Mail();
                  $mail->addAddress($email);
                  $mail->setFrom(SITEEMAIL, EMAIL_FROM_NAME);
                  $mail->subject(SITE_TITLE. " - ".\Core\Language::show('login_device_email_sub', 'Auth'));
                  $body = \Helpers\PageFunctions::displayEmailHeader();
                  $body .= sprintf(Language::show('login_blocked_device_email', 'Auth'), $username);
                  $body .= "<hr><b>".Language::show('device_device', 'Members')."</b>";
                  $body .= "<br>".$device_data[0]->browser." - ".$device_data[0]->os;
                  $body .= "<hr><b>".Language::show('device_location', 'Members')."</b>";
                  $body .= "<br>".$device_data[0]->city.", ".$device_data[0]->state.", ".$device_data[0]->country;
                  $body .= Language::show('login_device_footer_email', 'Auth');
                  $body .= \Helpers\PageFunctions::displayEmailFooter();
                  $mail->body($body);
                  $mail->send();
                  /** Device is disabled.  Kick user out and show error **/
                  $usersModel->remove($u_id);
                  $auth->logout();
                  /* Error Message Display */
                  ErrorMessages::push(Language::show('login_lockedout', 'Auth'), 'Login');
                }

                /**
                * Login Success
                * Redirect to user
                * Check to see if user came from another page within the site
                */
                if(isset($_SESSION['login_prev_page'])){ $login_prev_page = $_SESSION['login_prev_page']; }else{ $login_prev_page = ""; }
                /**
                * Checking to see if user user was viewing anything before login
                * If they were viewing a page on this site, then after login
                * send them to that page they were on.
                */
                if(!empty($login_prev_page)){
                  /* Send member to previous page */
                  /* Clear the prev page session if set */
                  if(isset($_SESSION['login_prev_page'])){
                    unset($_SESSION['login_prev_page']);
                  }
                  $prev_page = "$login_prev_page";
                  /* Send user back to page they were at before login */
                  /* Success Message Display */
                  SuccessMessages::push(Language::show('login_success', 'Auth'), $prev_page);
                }else{
                  /* No previous page, send member to home page */
                  //echo " send user to home page "; // Debug

                  /* Clear the prev page session if set */
                  if(isset($_SESSION['login_prev_page'])){
                    unset($_SESSION['login_prev_page']);
                  }

                  /* Redirect member to home page */
                  /* Success Message Display */
                 SuccessMessages::push(Language::show('login_success', 'Auth'), '');
                }
            }
            else{
                /* Error Message Display */
                ErrorMessages::push(Language::show('login_incorrect', 'Auth'), 'Login');
            }
        }

        $data['csrfToken'] = Csrf::makeToken('login');
        $data['title'] = Language::show('login_page_title', 'Auth');
        $data['welcomeMessage'] = Language::show('login_page_welcomeMessage', 'Auth');

        /** Check to see if user is logged in **/
        if($data['isLoggedIn'] = $auth->isLogged()){
            /** User is logged in - Get their data **/
            $u_id = $auth->user_info();
            $data['currentUserData'] = $usersModel->getCurrentUserData($u_id);
            $data['isAdmin'] = $usersModel->checkIsAdmin($u_id);
        }

?>

<div class="form-signin col-sm-12">
	<div class="card my-3 text-center">
		<div class="card-header h4">
			<?=$data['title'];?>
		</div>
		<div class="card-body">
      <form class="" method="post">
        <div class="form-group">
					<div class="input-group mb-3">
						<div class='input-group-prepend'>
							<span class='input-group-text'>
								<i class="fas fa-user"></i>
							</span>
						</div>
          	<input  class="form-control" type="text" id="username" name="username" placeholder="<?=Language::show('login_field_username', 'Auth')?>">
					</div>
        </div>
				<div class="form-group">
					<div class="input-group mb-3">
						<div class='input-group-prepend'>
							<span class='input-group-text'>
								<i class="fas fa-lock"></i>
							</span>
						</div>
						<input class="form-control" type="password" id="password" name="password" placeholder="<?=Language::show('login_field_password', 'Auth')?>">
					</div>
				</div>
        <label class="control-label">
          <input type="checkbox" id="rememberMe" name="rememberMe">
          <?=Language::show('login_field_rememberme', 'Auth')?>
        </label>
        <input type="hidden" name="token_login" value="<?=$data['csrfToken'];?>" />
				<!-- UBP Name Protection -->
				<input type="text" name="ubp_name" value="" class="hidden" />
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit"><i class="fas fa-sign-in-alt"></i> <?=Language::show('login_button', 'Auth')?></button>
      </form>

		</div>
		<div class="card-footer text-muted">
				<?=Language::show('dont_have_an_account', 'Auth')?> <a class="" name="" href="<?=SITE_URL?>Register"><?=Language::show('register_button', 'Auth')?></a>
        <?php $email_host = SITEEMAIL; if($email_host != ''){ ?>
					<br><a class="" name="" href="<?=SITE_URL?>Forgot-Password"><?=Language::show('forgotpass_button', 'Auth')?></a>
					<br><a class="" name="" href="<?=SITE_URL?>Resend-Activation-Email"><?=Language::show('resendactivation_button', 'Auth')?></a>
        <?php } ?>
    </div>
  </div>
</div>
