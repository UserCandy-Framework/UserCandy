<?php
/**
* Admin Panel Advanced Settings View
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.3
*/

use Helpers\{ErrorMessages,SuccessMessages,Paginator,Csrf,Request,Url,PageFunctions,Form};
use Models\AdminPanelModel;

/** Check to see if user is logged in */
if($data['isLoggedIn'] = $auth->isLogged()){
    /** User is logged in - Get their data */
    $u_id = $auth->user_info();
    $data['currentUserData'] = $usersModel->getCurrentUserData($u_id);
    if($data['isAdmin'] = $usersModel->checkIsAdmin($u_id) == 'false'){
        /** User Not Admin - kick them out */
        ErrorMessages::push('You are Not Admin', '');
    }
}else{
    /** User Not logged in - kick them out */
    ErrorMessages::push('You are Not Logged In', 'Login');
}

/** Load Models **/
$AdminPanelModel = new AdminPanelModel();
$pages = new Paginator(USERS_PAGEINATOR_LIMIT);  // How many rows per page

/** Get data for dashboard */
$data['current_page'] = $_SERVER['REQUEST_URI'];
$data['title'] = "Advanced Settings";
$data['welcomeMessage'] = "Welcome to the Admin Panel Site Advanced Settings!";

/** Check to see if Admin is submiting form data */
if(isset($_POST['submit'])){
  /** Check to see if site is a demo site */
  if(DEMO_SITE != 'TRUE'){
    /** Check to make sure the csrf token is good */
    if (Csrf::isTokenValid('settings')) {
        /** Check to make sure Admin is updating settings */
        if(Request::post('update_advanced_settings') == "true"){

            /** Get data sbmitted by form */
            $site_user_activation = Request::post('site_user_activation');
            if($site_user_activation != 'true'){ $site_user_activation = 'false'; }
            $site_user_invite_code = Request::post('site_user_invite_code');
            $max_attempts = Request::post('max_attempts');
            $security_duration = Request::post('security_duration');
            $session_duration = Request::post('session_duration');
            $session_duration_rm = Request::post('session_duration_rm');
            $min_username_length = Request::post('min_username_length');
            $max_username_length = Request::post('max_username_length');
            $min_password_length = Request::post('min_password_length');
            $max_password_length = Request::post('max_password_length');
            $min_email_length = Request::post('min_email_length');
            $max_email_length = Request::post('max_email_length');
            $random_key_length = Request::post('random_key_length');
            $default_timezone = Request::post('default_timezone');
            $users_pageinator_limit = Request::post('users_pageinator_limit');
            $friends_pageinator_limit = Request::post('friends_pageinator_limit');
            $message_quota_limit = Request::post('message_quota_limit');
            $message_pageinator_limit = Request::post('message_pageinator_limit');
            $sweet_title_display = Request::post('sweet_title_display');
            $sweet_button_display = Request::post('sweet_button_display');
            $image_max_size = Request::post('image_max_size');
            $online_bubble = Request::post('online_bubble');
            if($online_bubble != 'true'){ $online_bubble = 'false'; }
            $site_auto_friend = Request::post('site_auto_friend');
            if($site_auto_friend != 'TRUE'){ $site_auto_friend = 'FALSE'; }
            $site_auto_friend_id = Request::post('site_auto_friend_id');
            $default_home_page = Request::post('default_home_page');
            $default_home_page_folder = Request::post('default_home_page_folder');
            $default_home_page_login = Request::post('default_home_page_login');
            $default_home_page_folder_login = Request::post('default_home_page_folder_login');
            $site_profile_notifi_check = Request::post('site_profile_notifi_check');
            if($site_profile_notifi_check != 'true'){ $site_profile_notifi_check = 'false'; }

            if(!$AdminPanelModel->updateSetting('site_user_activation', $site_user_activation)){ $errors[] = 'Site User Activation Error'; }
            if(!$AdminPanelModel->updateSetting('site_user_invite_code', $site_user_invite_code)){ $errors[] = 'site_user_invite_code Error'; }
            if(!$AdminPanelModel->updateSetting('max_attempts', $max_attempts)){ $errors[] = 'max_attempts Error'; }
            if(!$AdminPanelModel->updateSetting('security_duration', $security_duration)){ $errors[] = 'security_duration Error'; }
            if(!$AdminPanelModel->updateSetting('session_duration', $session_duration)){ $errors[] = 'session_duration Error'; }
            if(!$AdminPanelModel->updateSetting('session_duration_rm', $session_duration_rm)){ $errors[] = 'session_duration_rm Error'; }
            if(!$AdminPanelModel->updateSetting('min_username_length', $min_username_length)){ $errors[] = 'min_username_length Error'; }
            if(!$AdminPanelModel->updateSetting('max_username_length', $max_username_length)){ $errors[] = 'max_username_length Error'; }
            if(!$AdminPanelModel->updateSetting('min_password_length', $min_password_length)){ $errors[] = 'min_password_length Error'; }
            if(!$AdminPanelModel->updateSetting('max_password_length', $max_password_length)){ $errors[] = 'max_password_length Error'; }
            if(!$AdminPanelModel->updateSetting('min_email_length', $min_email_length)){ $errors[] = 'min_email_length Error'; }
            if(!$AdminPanelModel->updateSetting('max_email_length', $max_email_length)){ $errors[] = 'max_email_length Error'; }
            if(!$AdminPanelModel->updateSetting('random_key_length', $random_key_length)){ $errors[] = 'random_key_length Error'; }
            if(!$AdminPanelModel->updateSetting('default_timezone', $default_timezone)){ $errors[] = 'default_timezone Error'; }
            if(!$AdminPanelModel->updateSetting('users_pageinator_limit', $users_pageinator_limit)){ $errors[] = 'users_pageinator_limit Error'; }
            if(!$AdminPanelModel->updateSetting('friends_pageinator_limit', $friends_pageinator_limit)){ $errors[] = 'friends_pageinator_limit Error'; }
            if(!$AdminPanelModel->updateSetting('message_quota_limit', $message_quota_limit)){ $errors[] = 'message_quota_limit Error'; }
            if(!$AdminPanelModel->updateSetting('message_pageinator_limit', $message_pageinator_limit)){ $errors[] = 'message_pageinator_limit Error'; }
            if(!$AdminPanelModel->updateSetting('sweet_title_display', $sweet_title_display)){ $errors[] = 'sweet_title_display Error'; }
            if(!$AdminPanelModel->updateSetting('sweet_button_display', $sweet_button_display)){ $errors[] = 'sweet_button_display Error'; }
            if(!$AdminPanelModel->updateSetting('image_max_size', $image_max_size)){ $errors[] = 'image_max_size Error'; }
            if(!$AdminPanelModel->updateSetting('online_bubble', $online_bubble)){ $errors[] = 'online_bubble Error'; }
            if(!$AdminPanelModel->updateSetting('site_auto_friend', $site_auto_friend)){ $errors[] = 'site_auto_friend Error'; }
            if(!$AdminPanelModel->updateSetting('site_auto_friend_id', $site_auto_friend_id)){ $errors[] = 'site_auto_friend_id Error'; }
            if(!$AdminPanelModel->updateSetting('default_home_page', $default_home_page)){ $errors[] = 'default_home_page Error'; }
            if(!$AdminPanelModel->updateSetting('default_home_page_folder', $default_home_page_folder)){ $errors[] = 'default_home_page_folder Error'; }
            if(!$AdminPanelModel->updateSetting('default_home_page_login', $default_home_page_login)){ $errors[] = 'default_home_page_login Error'; }
            if(!$AdminPanelModel->updateSetting('default_home_page_folder_login', $default_home_page_folder_login)){ $errors[] = 'default_home_page_folder_login Error'; }
            if(!$AdminPanelModel->updateSetting('site_profile_notifi_check', $site_profile_notifi_check)){ $errors[] = 'site_profile_notifi_check Error'; }

            // Run the update settings script
            if(!isset($errors) || count($errors) == 0){
                /** Success */
                SuccessMessages::push('You Have Successfully Updated Site Advanced Settings', 'AdminPanel-AdvancedSettings');
            }else{
                // Error
                if(isset($errors)){
                    $error_data = "<hr>";
                    foreach($errors as $row){
                        $error_data .= " - ".$row."<br>";
                    }
                }else{
                    $error_data = "";
                }
                /** Error Message Display */
                ErrorMessages::push('Error Updating Site Advanced Settings'.$error_data, 'AdminPanel-AdvancedSettings');
            }
        }else{
            /** Error Message Display */
            ErrorMessages::push('Error Updating Site Advanced Settings', 'AdminPanel-AdvancedSettings');
        }
    }else{
        /** Error Message Display */
        ErrorMessages::push('Error Updating Site Advanced Settings', 'AdminPanel-AdvancedSettings');
    }
  }else{
    /** Error Message Display */
    ErrorMessages::push('Demo Limit - Advanced Settings Disabled', 'AdminPanel-AdvancedSettings');
  }
}

/** Get Advanced Settings Data */
$site_user_activation = $AdminPanelModel->getSettings('site_user_activation');
$site_user_invite_code = $AdminPanelModel->getSettings('site_user_invite_code');
$max_attempts = $AdminPanelModel->getSettings('max_attempts');
$security_duration = $AdminPanelModel->getSettings('security_duration');
$session_duration = $AdminPanelModel->getSettings('session_duration');
$session_duration_rm = $AdminPanelModel->getSettings('session_duration_rm');
$min_username_length = $AdminPanelModel->getSettings('min_username_length');
$max_username_length = $AdminPanelModel->getSettings('max_username_length');
$min_password_length = $AdminPanelModel->getSettings('min_password_length');
$max_password_length = $AdminPanelModel->getSettings('max_password_length');
$min_email_length = $AdminPanelModel->getSettings('min_email_length');
$max_email_length = $AdminPanelModel->getSettings('max_email_length');
$random_key_length = $AdminPanelModel->getSettings('random_key_length');
$default_timezone = $AdminPanelModel->getSettings('default_timezone');
$users_pageinator_limit = $AdminPanelModel->getSettings('users_pageinator_limit');
$friends_pageinator_limit = $AdminPanelModel->getSettings('friends_pageinator_limit');
$message_quota_limit = $AdminPanelModel->getSettings('message_quota_limit');
$message_pageinator_limit = $AdminPanelModel->getSettings('message_pageinator_limit');
$sweet_title_display = $AdminPanelModel->getSettings('sweet_title_display');
$sweet_button_display = $AdminPanelModel->getSettings('sweet_button_display');
$image_max_size = $AdminPanelModel->getSettings('image_max_size');
$online_bubble = $AdminPanelModel->getSettings('online_bubble');
$site_auto_friend = $AdminPanelModel->getSettings('site_auto_friend');
$site_auto_friend_id = $AdminPanelModel->getSettings('site_auto_friend_id');
$default_home_page = $AdminPanelModel->getSettings('default_home_page');
$default_home_page_folder = $AdminPanelModel->getSettings('default_home_page_folder');
$default_home_page_login = $AdminPanelModel->getSettings('default_home_page_login');
$default_home_page_folder_login = $AdminPanelModel->getSettings('default_home_page_folder_login');
$site_profile_notifi_check = $AdminPanelModel->getSettings('site_profile_notifi_check');

/** Setup Token for Form */
$data['csrfToken'] = Csrf::makeToken('settings');

/** Setup Breadcrumbs */
$data['breadcrumbs'] = "<li class='breadcrumb-item'><a href='".SITE_URL."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li><li class='breadcrumb-item active'><i class='fa fa-fw fa-cog'></i> ".$data['title']."</li>";

/** Get list of All php time zones **/
$list = \DateTimeZone::listAbbreviations();
$idents = \DateTimeZone::listIdentifiers();

$tzdata = $offset = $added = array();
foreach ($list as $abbr => $info) {
    foreach ($info as $zone) {
        if ( ! empty($zone['timezone_id'])
            AND
            ! in_array($zone['timezone_id'], $added)
            AND
              in_array($zone['timezone_id'], $idents)) {
            $z = new \DateTimeZone($zone['timezone_id']);
            $c = new \DateTime(null, $z);
            $zone['time'] = $c->format('H:i a');
            $offset[] = $zone['offset'] = $z->getOffset($c);
            $tzdata[] = $zone;
            $added[] = $zone['timezone_id'];
        }
    }
}

array_multisort($offset, SORT_ASC, $tzdata);
$tzoptions = array();
foreach ($tzdata as $key => $row) {
    $tzoptions[$row['timezone_id']] = $row['time'] . ' - '
                                    . formatOffset($row['offset'])
                                    . ' ' . $row['timezone_id'];
}

// now you can use $options;

function formatOffset($offset) {
    $hours = $offset / 3600;
    $remainder = $offset % 3600;
    $sign = $hours > 0 ? '+' : '-';
    $hour = (int) abs($hours);
    $minutes = (int) abs($remainder / 60);

    if ($hour == 0 AND $minutes == 0) {
        $sign = ' ';
    }
    return 'GMT' . $sign . str_pad($hour, 2, '0', STR_PAD_LEFT)
            .':'. str_pad($minutes,2, '0');

}


?>
<div class='col-lg-12 col-md-12 col-sm-12'>
  <div class='row'>
    <div class='col-lg-12 col-md-12 col-sm-12'>
    	<div class='card mb-3'>
    		<div class='card-header h4'>
    			Site Registration Settings
          <?php echo PageFunctions::displayPopover('Site Registration Settings', 'Site Registration Settings are used to set the security levels for the Registration page.', false, 'btn btn-sm btn-light'); ?>
    		</div>
    		<div class='card-body'>
    			<p><?php echo $data['welcomeMessage'] ?></p>

    			<?php echo Form::open(array('method' => 'post')); ?>

          <!-- Site Activation -->
      	  <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
    				  <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> New User Activation</span>
            </div>
            <label class='switch form-control'>
              <input type="checkbox" class='form-control' id='site_user_activation' name='site_user_activation' value="true" <?php if($site_user_activation == "true"){echo "CHECKED";}?> />
              <span class="slider block"></span>
            </label>
            <?php echo PageFunctions::displayPopover('New User Account Activation', 'Default: Disabled - Requires new users to confirm their account via E-Mail activation link.', true, 'input-group-text'); ?>
    			</div>

    			<!-- Site Invite Code -->
    			<div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
    				  <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Site Invitation Code</span>
            </div>
    				<?php echo Form::input(array('type' => 'text', 'name' => 'site_user_invite_code', 'class' => 'form-control', 'value' => $site_user_invite_code, 'placeholder' => 'Site Invitation Code', 'maxlength' => '255')); ?>
            <?php echo PageFunctions::displayPopover('Site Invitation Code', 'Default: blank - Requires new users to use correct Invitation Code to Register for site.  Site does not require if left blank.', true, 'input-group-text'); ?>
    			</div>

          <!-- Min Username Length -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Min Username Length</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'min_username_length', 'class' => 'form-control', 'value' => $min_username_length, 'placeholder' => 'Min Username Length', 'maxlength' => '255')); ?>
            <?php echo PageFunctions::displayPopover('Minimum Username Length', 'Default: 5 - Minimum character length for Usernames.', true, 'input-group-text'); ?>
          </div>

          <!-- Max Username Length -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Max Username Length</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'max_username_length', 'class' => 'form-control', 'value' => $max_username_length, 'placeholder' => 'Max Username Length', 'maxlength' => '255')); ?>
            <?php echo PageFunctions::displayPopover('Maximum Username Length', 'Default: 30 - Maximum character length for Usernames.', true, 'input-group-text'); ?>
          </div>

          <!-- Min Username Length -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Min Password Length</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'min_password_length', 'class' => 'form-control', 'value' => $min_password_length, 'placeholder' => 'Min Password Length', 'maxlength' => '255')); ?>
            <?php echo PageFunctions::displayPopover('Minimum Password Length', 'Default: 5 - Minimum character length for Passwords.', true, 'input-group-text'); ?>
          </div>

          <!-- Max Username Length -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Max Password Length</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'max_password_length', 'class' => 'form-control', 'value' => $max_password_length, 'placeholder' => 'Max Password Length', 'maxlength' => '255')); ?>
            <?php echo PageFunctions::displayPopover('Maximum Password Length', 'Default: 30 - Maximum character length for Passwords.', true, 'input-group-text'); ?>
          </div>

          <!-- Min Email Length -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Min Email Length</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'min_email_length', 'class' => 'form-control', 'value' => $min_email_length, 'placeholder' => 'Min Email Address Length', 'maxlength' => '255')); ?>
            <?php echo PageFunctions::displayPopover('Minimum Email Length', 'Default: 5 - Minimum character length for Email Addresses.', true, 'input-group-text'); ?>
          </div>

          <!-- Max Email Length -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Max Email Length</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'max_email_length', 'class' => 'form-control', 'value' => $max_email_length, 'placeholder' => 'Max Email Address Length', 'maxlength' => '255')); ?>
            <?php echo PageFunctions::displayPopover('Maximum Email Length', 'Default: 100 - Maximum character length for Email Addresses.', true, 'input-group-text'); ?>
          </div>

          <!-- New User Activation Token -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Activation Token Length</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'random_key_length', 'class' => 'form-control', 'value' => $random_key_length, 'placeholder' => 'Activation Token Length', 'maxlength' => '255')); ?>
            <?php echo PageFunctions::displayPopover('Account Activation Token Length', 'Default: 15 - Character length for tokens that are generated for new users when required to activate via email.', true, 'input-group-text'); ?>
          </div>
        <?php
        /** Check if Friends Plugin is installed **/
        if($DispenserModel->checkDispenserEnabled('Friends')){
        ?>
          <!-- Site Auto Friend -->
      	  <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
    				  <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Site Auto Friend</span>
            </div>
            <label class='switch form-control'>
              <input type="checkbox" class='form-control' id='site_auto_friend' name='site_auto_friend' value="TRUE" <?php if($site_auto_friend == "TRUE"){echo "CHECKED";}?> />
              <span class="slider block"></span>
            </label>
            <?php echo PageFunctions::displayPopover('Site Auto Friend', 'Default: Disabled - When enabled, when a new user registers for the site they will be added as friends with set User ID.', true, 'input-group-text'); ?>
    			</div>

          <!-- Site Auto Friend ID -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Site Auto Friend ID</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'site_auto_friend_id', 'class' => 'form-control', 'value' => $site_auto_friend_id, 'placeholder' => 'Site Auto Friend User ID', 'maxlength' => '11')); ?>
            <?php echo PageFunctions::displayPopover('Site Auto Friend User ID', 'Default: 1 - UserID that new users will be automatically friends with when registering a new account.', true, 'input-group-text'); ?>
          </div>
        <?php } ?>

        </div>
      </div>
    </div>

    <div class='col-lg-12 col-md-12 col-sm-12'>
      <div class='card mb-3'>
        <div class='card-header h4'>
          Site User Login Settings
          <?php echo PageFunctions::displayPopover('Site User Settings', 'Site User Settings are used to set the security levels for the Login page.', false, 'btn btn-sm btn-light'); ?>
        </div>
        <div class='card-body'>
          <!-- Max Login Attempts -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Max Login Attempts</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'max_attempts', 'class' => 'form-control', 'value' => $max_attempts, 'placeholder' => 'Max Login Attempts', 'maxlength' => '255')); ?>
            <?php echo PageFunctions::displayPopover('Max Login Attempts', 'Default: 5 - Sets total number of login attempts before user is locked for a set time.', true, 'input-group-text'); ?>
          </div>

          <!-- Failed Login Attempts Block Time -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Block Failed Login in Minutes</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'security_duration', 'class' => 'form-control', 'value' => $security_duration, 'placeholder' => 'Block Failed Login User Duration in Minutes', 'maxlength' => '255')); ?>
            <?php echo PageFunctions::displayPopover('Block Failed Login User Duration in Minutes', 'Default: 5 - Sets amount of Minutes user is blocked from being able to login.', true, 'input-group-text'); ?>
          </div>

          <!-- Basic User Session Duration -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Basic User Session in Days</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'session_duration', 'class' => 'form-control', 'value' => $session_duration, 'placeholder' => 'How Many Days a User Stays Logged In', 'maxlength' => '255')); ?>
            <?php echo PageFunctions::displayPopover('Basic User Session Duration in Days', 'Default: 1 - Sets amount of Days users stay logged in to a basic session.', true, 'input-group-text'); ?>
          </div>

          <!-- Remember Me User Session Duration -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Remember Me in Months</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'session_duration_rm', 'class' => 'form-control', 'value' => $session_duration_rm, 'placeholder' => 'How Many Months a User Stays Logged In', 'maxlength' => '255')); ?>
            <?php echo PageFunctions::displayPopover('Remember Me Session Duration in Months', 'Default: 1 - Sets amount of Months users stay logged in when they check Remember Me.', true, 'input-group-text'); ?>
          </div>

        </div>
      </div>
    </div>

    <div class='col-lg-12 col-md-12 col-sm-12'>
      <div class='card mb-3'>
        <div class='card-header h4'>
          Members Settings
          <?php echo PageFunctions::displayPopover('Members Settings', 'Site Members Settings allows admin to edit members settings site wide.', false, 'btn btn-sm btn-light'); ?>
        </div>
        <div class='card-body'>
          <!-- Online Bubble -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Online Bubble</span>
            </div>
            <label class='switch form-control'>
              <input type="checkbox" class='form-control' id='online_bubble' name='online_bubble' value="true" <?php if($online_bubble == "true"){echo "CHECKED";}?> />
              <span class="slider block"></span>
            </label>
            <?php echo PageFunctions::displayPopover('Online Bubble', 'Default: Enabled - When Enabled a small bubble displays next to each username with online status. Green = Online. Red = Offline.', true, 'input-group-text'); ?>
          </div>
          <!-- Profile notifi checker -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Profile Notifications</span>
            </div>
            <label class='switch form-control'>
              <input type="checkbox" class='form-control' id='site_profile_notifi_check' name='site_profile_notifi_check' value="true" <?php if($site_profile_notifi_check == "true"){echo "CHECKED";}?> />
              <span class="slider block"></span>
            </label>
            <?php echo PageFunctions::displayPopover('Profile Notifi Checker', 'Default: Enabled - When Enabled New Users will see a info alert letting them know to update their profile.', true, 'input-group-text'); ?>
          </div>
        </div>
      </div>
    </div>

    <div class='col-lg-12 col-md-12 col-sm-12'>
      <div class='card mb-3'>
        <div class='card-header h4'>
          Site Time Zone Settings
          <?php echo PageFunctions::displayPopover('Time Zone Settings', 'Site Time Zone Settings are used to set default Time Zone settings.', false, 'btn btn-sm btn-light'); ?>
        </div>
        <div class='card-body'>

          <!-- Site Default Time Zone -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Default Time Zone</span>
            </div>

            <select class='form-control' id='default_timezone' name='default_timezone'>
              <?php
                foreach ($tzoptions as $key => $value) {
                  if($key == $default_timezone){ $selected = "SELECTED"; }else{ $selected = ""; }
                  echo "<option value='$key' $selected />$value</option>";
                }


              ?>
            </select>

            <?php echo PageFunctions::displayPopover('Default Site Time Zone', 'Default: America/Chicago - Default Site Time Zone. There is a list of time zones in the correct format on https://www.php.net/manual/en/timezones.php', true, 'input-group-text'); ?>
          </div>

        </div>
      </div>
    </div>

    <div class='col-lg-12 col-md-12 col-sm-12'>
      <div class='card mb-3'>
        <div class='card-header h4'>
          Paginator Limits
          <?php echo PageFunctions::displayPopover('Site Paginator Settings', 'Site Paginator Settings are used to set limits on Member pages and Friends pages if installed.', false, 'btn btn-sm btn-light'); ?>
        </div>
        <div class='card-body'>


          <!-- Members Paginator Limit -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Members Paginator</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'users_pageinator_limit', 'class' => 'form-control', 'value' => $users_pageinator_limit, 'placeholder' => 'Members Paginator Limit', 'maxlength' => '255')); ?>
            <?php echo PageFunctions::displayPopover('Members Paginator Limit', 'Default: 20 - How many Members to list per page on Members Pages.', true, 'input-group-text'); ?>
          </div>

          <?php
          /** Check to see if Friends Plugin is installed, if it is show link **/
          if($DispenserModel->checkDispenserEnabled('Friends')){
          ?>
          <!-- Friends Paginator Limit -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Friends Paginator</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'friends_pageinator_limit', 'class' => 'form-control', 'value' => $friends_pageinator_limit, 'placeholder' => 'Friends Paginator Limit', 'maxlength' => '255')); ?>
            <?php echo PageFunctions::displayPopover('Friends Paginator Limit', 'Default: 20 - How many Friends to list per page on Friends Pages.', true, 'input-group-text'); ?>
          </div>
          <?php } ?>

        </div>
      </div>
    </div>

    <?php
    /** Check to see if Private Message Plugin is installed, if it is show link **/
    if($DispenserModel->checkDispenserEnabled('Messages')){
    ?>

    <div class='col-lg-12 col-md-12 col-sm-12'>
      <div class='card mb-3'>
        <div class='card-header h4'>
          Messages Plugin Settings
          <?php echo PageFunctions::displayPopover('Messages Plugin Settings', 'Messages Plugin Settings are used to set user limits for private messages.', false, 'btn btn-sm btn-light'); ?>
        </div>
        <div class='card-body'>

          <!-- Messages Quota -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Messages Quota</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'message_quota_limit', 'class' => 'form-control', 'value' => $message_quota_limit, 'placeholder' => 'Messages Quota', 'maxlength' => '255')); ?>
            <?php echo PageFunctions::displayPopover('Messages Quota', 'Default: 50 - Messages Quota Limits how many messages each user can have in their Inbox.', true, 'input-group-text'); ?>
          </div>

          <!-- Messages Paginator Limit -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Messages Paginator Limit</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'message_pageinator_limit', 'class' => 'form-control', 'value' => $message_pageinator_limit, 'placeholder' => 'Messages Paginator Limit', 'maxlength' => '255')); ?>
            <?php echo PageFunctions::displayPopover('Messages Paginator Limit', 'Default: 10 - How many Messages to list per page on Messages Pages.', true, 'input-group-text'); ?>
          </div>

        </div>
      </div>
    </div>

  <?php } ?>
  <?php
  /** Check to see if Sweets Helper is installed, if it is show link **/
  if($DispenserModel->checkDispenserEnabled('Sweets')){
  ?>

  <div class='col-lg-12 col-md-12 col-sm-12'>
    <div class='card mb-3'>
      <div class='card-header h4'>
        Sweets Settings
        <?php echo PageFunctions::displayPopover('Sweet Settings', 'Sweet Settings are used to set the title of all Sweets within the site.', false, 'btn btn-sm btn-light'); ?>
      </div>
      <div class='card-body'>

        <!-- Sweets Title -->
        <div class='input-group mb-3' style='margin-bottom: 25px'>
          <div class="input-group-prepend">
            <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Sweets Title Display</span>
          </div>
          <?php echo Form::input(array('type' => 'text', 'name' => 'sweet_title_display', 'class' => 'form-control', 'value' => $sweet_title_display, 'placeholder' => 'Sweets Title', 'maxlength' => '255')); ?>
          <?php echo PageFunctions::displayPopover('Sweets Title Display', 'Default: Sweets - Text shown on sweets count displays. EX: Likes/+1s/Hearts', true, 'input-group-text'); ?>
        </div>
        <div style='margin-bottom: 25px'>

        </div>

        <!-- Sweets Button -->
        <div class='input-group mb-3' style='margin-bottom: 25px'>
          <div class="input-group-prepend">
            <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Sweets Button Display</span>
          </div>
          <?php echo Form::input(array('type' => 'text', 'name' => 'sweet_button_display', 'class' => 'form-control', 'value' => $sweet_button_display, 'placeholder' => 'Sweets Button', 'maxlength' => '255')); ?>
          <?php echo PageFunctions::displayPopover('Sweets Button Display', 'Default: Sweet - Text shown on Button for sweets. EX: Like/+1/Heart', true, 'input-group-text'); ?>
        </div>

      </div>
    </div>
  </div>

<?php } ?>

  <div class='col-lg-12 col-md-12 col-sm-12'>
    <div class='card mb-3'>
      <div class='card-header h4'>
        Max Profile Image Size
        <?php echo PageFunctions::displayPopover('Max Profile Image Settings', 'Max Profile Image Settings are used to set the max image size once converted for site.', false, 'btn btn-sm btn-light'); ?>
      </div>
      <div class='card-body'>
        <!-- Max Image Size when uploaded to server -->
        <div class='input-group mb-3'>
          <div class='input-group-prepend'>
            <span class='input-group-text' id='basic-addon1'><i class='fa fa-fw fa-image'></i> Max Image Size</span>
          </div>
          <select class='form-control' id='image_max_size' name='image_max_size'>
            <option value='240,160' <?php if($image_max_size == "240,160"){echo "SELECTED";}?> >240 x 160</option>
            <option value='320,240' <?php if($image_max_size == "320,240"){echo "SELECTED";}?> >320 x 160</option>
            <option value='460,309' <?php if($image_max_size == "460,309"){echo "SELECTED";}?> >460 x 309</option>
            <option value='800,600' <?php if($image_max_size == "800,600"){echo "SELECTED";}?> >800 x 600</option>
            <option value='1024,768' <?php if($image_max_size == "1024,768"){echo "SELECTED";}?> >1024 x 768</option>
            <option value='1920,1080' <?php if($image_max_size == "1920,1080"){echo "SELECTED";}?> >1920 x 1080</option>
          </select>
          <?php echo PageFunctions::displayPopover('Max Profile Image Size', 'Default: 800x600 - Select the default image max resize limit.  The larger the size, the larger the file. Used for User Images, but can be used elsewhere if needed.', true, 'input-group-text'); ?>
        </div>

      </div>
    </div>
  </div>

  <div class='col-lg-12 col-md-12 col-sm-12'>
    <div class='card mb-3'>
      <div class='card-header h4'>
        Main Home Page Settings
        <?php echo PageFunctions::displayPopover('Main Home Page Settings', 'Setting to set the default Home page based on Page Name and Page Folder.  Refer to Page permissions for correct data.', false, 'btn btn-sm btn-light'); ?>
      </div>
      <div class='card-body'>

        <?php
          /** Get All Pages Data */
          $all_pages_data = $AdminPanelModel->getAllPages('URL-ASC');
        ?>

        <!-- Default Home Page -->
        <div class='input-group mb-3' style='margin-bottom: 25px'>
          <div class="input-group-prepend">
            <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Home Page</span>
          </div>
          <select class='form-control' id='default_home_page' name='default_home_page'>
            <option value='' <?php if($default_home_page == ""){echo "SELECTED";}?> >Default to Home Page</option>
            <?php if(!empty($all_pages_data)){ foreach ($all_pages_data as $pagedata) { ?>
              <option value='<?=$pagedata->id?>' <?php if($default_home_page == $pagedata->id){echo "SELECTED";}?> >URL: <?=$pagedata->url?></option>
            <?php }} ?>
          </select>
          <?php echo PageFunctions::displayPopover('Default Home Page', 'Default: Default to Home Page.  Loads the Home.php page from /system/pages/ folder.', true, 'input-group-text'); ?>
        </div>

        <!-- Default Home Page Logged In -->
        <div class='input-group mb-3' style='margin-bottom: 25px'>
          <div class="input-group-prepend">
            <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Home Page Logged In</span>
          </div>
          <select class='form-control' id='default_home_page_login' name='default_home_page_login'>
            <option value='' <?php if($default_home_page_login == ""){echo "SELECTED";}?> >Default to Home Page</option>
            <?php if(!empty($all_pages_data)){ foreach ($all_pages_data as $pagedata) { ?>
              <option value='<?=$pagedata->id?>' <?php if($default_home_page_login == $pagedata->id){echo "SELECTED";}?> >URL: <?=$pagedata->url?></option>
            <?php }} ?>
          </select>
          <?php echo PageFunctions::displayPopover('Default Home Page Logged In', 'Default: Default to Home Page.  Loads the Home.php page from /system/pages/ folder.', true, 'input-group-text'); ?>
        </div>

      </div>
    </div>
  </div>

    <div class='col-lg-12 col-md-12 col-sm-12'>
        <button class="btn btn-md btn-success" name="submit" type="submit">
            Update Site Advanced Settings
        </button>
        <!-- CSRF Token and What is Being Updated -->
        <input type="hidden" name="token_settings" value="<?php echo $data['csrfToken']; ?>" />
        <input type="hidden" name="update_advanced_settings" value="true" />
        <?php echo Form::close(); ?><Br><br>
    </div>
  </div>
</div>
