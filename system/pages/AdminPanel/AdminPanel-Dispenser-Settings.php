<?php
/**
* Admin Panel Dispenser Settings Page
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

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
$data['title'] = "Dispenser Settings";
$data['welcomeMessage'] = "Welcome to the Admin Panel Dispenser Settings!";

/** Check to see if Admin is submiting form data */
if(isset($_POST['submit'])){
  /** Check to see if site is a demo site */
  if(DEMO_SITE != 'TRUE'){
    /** Check to make sure the csrf token is good */
    if (Csrf::isTokenValid('settings')) {
        /** Check to make sure Admin is updating settings */
        if(Request::post('update_settings') == "true"){
            /** Get data sbmitted by form */
            $dispenser_api_key = Request::post('dispenser_api_key');
            if(!$AdminPanelModel->updateSetting('dispenser_api_key', $dispenser_api_key)){ $errors[] = 'UserCandy.com Dispensary Key Error'; }

            // Run the update settings script
            if(!isset($errors) || count($errors) == 0){
                /** Success */
                SuccessMessages::push('You Have Successfully Updated Dispenser Settings', 'AdminPanel-Dispenser-Settings');
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
                ErrorMessages::push('Error Updating Dispenser Settings'.$error_data, 'AdminPanel-Dispenser-Settings');
            }
        }else{
            /** Error Message Display */
            ErrorMessages::push('Error Updating Dispenser Settings', 'AdminPanel-Dispenser-Settings');
        }
    }else{
        /** Error Message Display */
        ErrorMessages::push('Error Updating Dispenser Settings', 'AdminPanel-Dispenser-Settings');
    }
  }else{
    /** Error Message Display */
    ErrorMessages::push('Demo Limit - Settings Disabled', 'AdminPanel-Dispenser-Settings');
  }
}

/** Get Settings Data */
$dispenser_api_key = $AdminPanelModel->getSettings('dispenser_api_key');

/** Connect to UserCandy Dispensary **/
if(!empty($dispenser_api_key)){
  $url = "https://www.usercandy.com/Dispensary/connect/".$dispenser_api_key;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL,$url);
  $result=curl_exec($ch);
  curl_close($ch);
  if($result){
    $get_dd = json_decode($result, true);
  }
}

/** Setup Token for Form */
$data['csrfToken'] = Csrf::makeToken('settings');

/** Setup Breadcrumbs */
$data['breadcrumbs'] = "<li class='breadcrumb-item'><a href='".SITE_URL."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li><li class='breadcrumb-item active'><i class='fa fa-fw fa-cog'></i> ".$data['title']."</li>";

?>
<div class='col-lg-12 col-md-12 col-sm-12'>
  <div class='row'>
    <div class='col-lg-12 col-md-12 col-sm-12'>
    	<div class='card mb-3'>
    		<div class='card-header h4'>
    			<?php echo $data['title'];  ?>
          <?php echo PageFunctions::displayPopover('Dispenser Settings', 'Dispenser Settings are used to connect and manage downloads from UserCandy.com.', false, 'btn btn-sm btn-light'); ?>
    		</div>
    		<div class='card-body'>
    			<p>
            <?php echo $data['welcomeMessage'] ?>
          </p>

    			<?php echo Form::open(array('method' => 'post')); ?>

    			<!-- Dispenser API Key -->
    			<div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
    				  <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> UserCandy Dispensary API Key</span>
            </div>
    				<?php echo Form::input(array('type' => 'text', 'name' => 'dispenser_api_key', 'class' => 'form-control', 'value' => $dispenser_api_key, 'placeholder' => 'UserCandy Dispensary API Key', 'maxlength' => '255')); ?>
            <?php echo PageFunctions::displayPopover('UserCandy Dispenser API Key', 'UserCandy.com Dispensary API Key is required to connect to UserCandy.com Dispensary to View and Download Files.', true, 'input-group-text'); ?>
    			</div>

          <p>
            <strong>Connection Status</strong><Br>
              <?php
                if($get_dd['success'] == "true"){
                  echo "<font color='green'>Connected</font>";
                }else{
                  echo "<font color='red'>Not Connected</font>";
                }
              ?>
          </p>

          <p>
            <strong>Custom Folder Writeable</strong><Br>
              <?php
                /** Folder Writeable Check **/
                  if (is_writable(CUSTOMDIR)) {
                    echo "<font color='green'>/custom/ Folder Writeable</font> - Installs should work.";
                  } else {
                    echo "<font color='red'>/custom/ Folder Not Writeable</font> - Installs may fail.";
                  }
              ?>
          </p>

          <p>
            <strong>How to Connect?</strong><br>
            1 - Register for <a href="https://www.usercandy.com" target="_blank">www.UserCandy.com</a><Br>
            2 - Login and go to your <a href="https://www.usercandy.com/Account-Settings" target="_blank">Account Settings</a><br>
            3 - Open <a href="https://www.usercandy.com/Dispensary-API" target="_blank">Dispensary API</a><br>
            4 - Generate a Dispensary API Key<br>
            5 - Copy that key to the UserCandy Dispensary API Key field above<br>
            6 - Click Update Dispenser Settings
          </p>

        </div>
    	</div>
    </div>

    <div class='col-lg-12 col-md-12 col-sm-12'>
        <button class="btn btn-md btn-success" name="submit" type="submit">
            Update Dispenser Settings
        </button>
        <!-- CSRF Token and What is Being Updated -->
        <input type="hidden" name="token_settings" value="<?php echo $data['csrfToken']; ?>" />
        <input type="hidden" name="update_settings" value="true" />
        <?php echo Form::close(); ?><Br><br>
    </div>
  </div>
</div>
