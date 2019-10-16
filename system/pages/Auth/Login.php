<?php
/**
* Account Login View
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

use Core\Language;
use Helpers\{Url,Request,SuccessMessages,ErrorMessages,Csrf};

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

                $info = array('LastLogin' => date('Y-m-d G:i:s'));
                $where = array('userID' => $userId);
                $auth->updateUser($info,$where);

                $usersModel->update($userId);

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

        /** Setup Breadcrumbs **/
    		$data['breadcrumbs'] = "
    			<li class='breadcrumb-item active'>".$data['title']."</li>
        ";

        /** Check to see if user is logged in **/
        if($data['isLoggedIn'] = $auth->isLogged()){
            /** User is logged in - Get their data **/
            $u_id = $auth->user_info();
            $data['currentUserData'] = $usersModel->getCurrentUserData($u_id);
            $data['isAdmin'] = $usersModel->checkIsAdmin($u_id);
        }

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
											<?=Language::show('login_field_username', 'Auth')?>
										</span>
									</div>
                	<input  class="form-control" type="text" id="username" name="username" placeholder="<?=Language::show('login_field_username', 'Auth')?>">
								</div>
              </div>
							<div class="form-group">
								<div class="input-group mb-3">
									<div class='input-group-prepend'>
										<span class='input-group-text'>
											<?=Language::show('login_field_password', 'Auth')?>
										</span>
									</div>
									<input class="form-control" type="password" id="password" name="password" placeholder="<?=Language::show('login_field_password', 'Auth')?>">
								</div>
							</div>
              <div class="form-inline">
                  <label class="control-label"><?=Language::show('login_field_rememberme', 'Auth')?></label>
                  <input class="form-control" type="checkbox" id="rememberMe" name="rememberMe">
              </div>
              <input type="hidden" name="token_login" value="<?=$data['csrfToken'];?>" />
							<!-- UBP Name Protection -->
							<input type="text" name="ubp_name" value="" class="hidden" />
              <button class="btn btn-primary" type="submit" name="submit"><?=Language::show('login_button', 'Auth')?></button>
          </div>

      </form>

		</div>
		<div class="card-footer text-muted">
					<a class="btn btn-primary btn-sm" name="" href="<?=SITE_URL?>Register"><?=Language::show('register_button', 'Auth')?></a>
          <?php $email_host = SITEEMAIL; if($email_host != ''){ ?>
					<a class="btn btn-primary btn-sm" name="" href="<?=SITE_URL?>Forgot-Password"><?=Language::show('forgotpass_button', 'Auth')?></a>
					<a class="btn btn-primary btn-sm" name="" href="<?=SITE_URL?>Resend-Activation-Email"><?=Language::show('resendactivation_button', 'Auth')?></a>
        <?php } ?>
    </div>
  </div>
</div>
