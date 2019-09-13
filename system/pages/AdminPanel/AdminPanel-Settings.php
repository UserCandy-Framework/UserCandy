<?php
/**
* Admin Panel Settings Page
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
$data['title'] = "Main Settings";
$data['welcomeMessage'] = "Welcome to the Admin Panel Site Main Settings!";

/** Check to see if Admin is submiting form data */
if(isset($_POST['submit'])){
  /** Check to see if site is a demo site */
  if(DEMO_SITE != 'TRUE'){
    /** Check to make sure the csrf token is good */
    if (Csrf::isTokenValid('settings')) {
        /** Check to make sure Admin is updating settings */
        if(Request::post('update_settings') == "true"){
            /** Get data sbmitted by form */
            $site_title = Request::post('site_title');
            $site_description = Request::post('site_description');
            $site_keywords = Request::post('site_keywords');
            $site_recapcha_public = Request::post('site_recapcha_public');
            $site_recapcha_private = Request::post('site_recapcha_private');
            $site_message = Request::post('site_message');
            if(!$AdminPanelModel->updateSetting('site_title', $site_title)){ $errors[] = 'Site Title Error'; }
            if(!$AdminPanelModel->updateSetting('site_description', $site_description)){ $errors[] = 'Site Description Error'; }
            if(!$AdminPanelModel->updateSetting('site_keywords', $site_keywords)){ $errors[] = 'Site Keywords Error'; }
            if(!$AdminPanelModel->updateSetting('site_recapcha_public', $site_recapcha_public)){ $errors[] = 'Site reCAPCHA Public Error'; }
            if(!$AdminPanelModel->updateSetting('site_recapcha_private', $site_recapcha_private)){ $errors[] = 'Site reCAPCHA Private Error'; }
            if(!$AdminPanelModel->updateSetting('site_message', $site_message)){ $errors[] = 'Site Wide Message Error'; }

            // Run the update settings script
            if(!isset($errors) || count($errors) == 0){
                /** Success */
                SuccessMessages::push('You Have Successfully Updated Site Settings', 'AdminPanel-Settings');
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
                ErrorMessages::push('Error Updating Site Settings'.$error_data, 'AdminPanel-Settings');
            }
        }else{
            /** Error Message Display */
            ErrorMessages::push('Error Updating Site Settings', 'AdminPanel-Settings');
        }
    }else{
        /** Error Message Display */
        ErrorMessages::push('Error Updating Site Settings', 'AdminPanel-Settings');
    }
  }else{
    /** Error Message Display */
    ErrorMessages::push('Demo Limit - Settings Disabled', 'AdminPanel-Settings');
  }
}

/** Get Settings Data */
$site_title = $AdminPanelModel->getSettings('site_title');
$site_description = $AdminPanelModel->getSettings('site_description');
$site_keywords = $AdminPanelModel->getSettings('site_keywords');
$site_recapcha_public = $AdminPanelModel->getSettings('site_recapcha_public');
$site_recapcha_private = $AdminPanelModel->getSettings('site_recapcha_private');
$site_message = $AdminPanelModel->getSettings('site_message');


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
          <?php echo PageFunctions::displayPopover('Site Main Settings', 'Site Main Settings are mainly used for SEO use.', false, 'btn btn-sm btn-light'); ?>
    		</div>
    		<div class='card-body'>
    			<p>
            <?php echo $data['welcomeMessage'] ?>
          </p>

    			<?php echo Form::open(array('method' => 'post')); ?>

    			<!-- Site Title -->
    			<div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
    				  <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Site Title</span>
            </div>
    				<?php echo Form::input(array('type' => 'text', 'name' => 'site_title', 'class' => 'form-control', 'value' => $site_title, 'placeholder' => 'Site Title', 'maxlength' => '255')); ?>
            <?php echo PageFunctions::displayPopover('Site Title', 'Site Title is displayed throughout the site where requested. Also displays in the browser title area, and in the Navbar.', true, 'input-group-text'); ?>
    			</div>

          <!-- Site Description -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Site Description</span>
            </div>
              <?php echo Form::textarea(array('type' => 'text', 'name' => 'site_description', 'class' => 'form-control', 'value' => $site_description, 'placeholder' => 'Site Description')); ?>
              <?php echo PageFunctions::displayPopover('Site Description', 'Site Description is used in the description meta tag. Mainly used for Search Engines.', true, 'input-group-text'); ?>
          </div>

          <!-- Site Keywords -->
    			<div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
    				  <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Site Keywords</span>
            </div>
    				<?php echo Form::input(array('type' => 'text', 'name' => 'site_keywords', 'class' => 'form-control', 'value' => $site_keywords, 'placeholder' => 'Site Keywords', 'maxlength' => '255')); ?>
            <?php echo PageFunctions::displayPopover('Site Keywords', 'Site Keywords are used in the keywords meta tag. Mainly used for Search Engines.', true, 'input-group-text'); ?>
    			</div>
        </div>
    	</div>
    </div>

    <div class='col-lg-12 col-md-12 col-sm-12'>
    	<div class='card mb-3'>
    		<div class='card-header h4'>
    			Site reCAPCHA v3 Settings
          <?php echo PageFunctions::displayPopover('Google reCAPCHA', 'Visit Google reCAPCHA website to setup your keys and add security to your website.  Make sure to use reCAPCHA v3 keys or it will not work.', false, 'btn btn-sm btn-light'); ?>
    		</div>
    		<div class='card-body'>
          <p>
            Site reCAPCHA Settings. <a href='https://www.google.com/recaptcha/'>Get reCAPCHA</a>
          </p>
          <!-- reCAPCHA Public Key -->
    			<div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
    				  <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> reCAPCHA Site Key</span>
            </div>
    				<?php echo Form::input(array('type' => 'text', 'name' => 'site_recapcha_public', 'class' => 'form-control', 'value' => $site_recapcha_public, 'placeholder' => 'reCAPCHA Site Key', 'maxlength' => '100')); ?>
            <?php echo PageFunctions::displayPopover('reCAPCHA Site Key', 'Google reCAPCHA Site Key for robot check.', true, 'input-group-text'); ?>
    			</div>

          <!-- reCAPCHA Private Key -->
    			<div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
      				<span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> reCAPCHA Secret Key</span>
            </div>
    				<?php echo Form::input(array('type' => 'text', 'name' => 'site_recapcha_private', 'class' => 'form-control', 'value' => $site_recapcha_private, 'placeholder' => 'reCAPCHA Secret Key', 'maxlength' => '100')); ?>
            <?php echo PageFunctions::displayPopover('reCAPCHA Secret Key', 'Google reCAPCHA Secret Key for robot check.', true, 'input-group-text'); ?>
    			</div>

    		</div>
    	</div>
    </div>

    <div class='col-lg-12 col-md-12 col-sm-12'>
    	<div class='card mb-3'>
    		<div class='card-header h4'>
    			Site Wide Message
          <?php echo PageFunctions::displayPopover('Site Wide Message', 'Site Wide Messages settings allows Admin to share important data with all users.', false, 'btn btn-sm btn-light'); ?>
    		</div>
    		<div class='card-body'>

          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Site Wide Message</span>
            </div>
              <?php echo Form::textarea(array('type' => 'text', 'name' => 'site_message', 'class' => 'form-control', 'value' => $site_message, 'placeholder' => 'Site Wide Message')); ?>
              <?php echo PageFunctions::displayPopover('Site Wide Message', 'This message will show to all users on the site.  Let them know about downtime or other site related messages. Info box will not show if the field below is blank.', true, 'input-group-text'); ?>
          </div>
        </div>
      </div>
    </div>


    <div class='col-lg-12 col-md-12 col-sm-12'>
        <button class="btn btn-md btn-success" name="submit" type="submit">
            <?php // echo Language::show('update_profile', 'Auth'); ?>
            Update Site Settings
        </button>
        <!-- CSRF Token and What is Being Updated -->
        <input type="hidden" name="token_settings" value="<?php echo $data['csrfToken']; ?>" />
        <input type="hidden" name="update_settings" value="true" />
        <?php echo Form::close(); ?><Br><br>
    </div>
  </div>
</div>
