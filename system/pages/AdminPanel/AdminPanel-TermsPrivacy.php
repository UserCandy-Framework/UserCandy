<?php
/**
* Admin Panel Terms and Policy View
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
$data['title'] = "Terms and Policy";
$data['welcomeMessage'] = "Welcome to the Admin Panel Terms and Policy Editor!";

/** Check to see if Admin is submiting form data */
if(isset($_POST['submit'])){
  /** Check to see if site is a demo site */
  if(DEMO_SITE != 'TRUE'){
    /** Check to make sure the csrf token is good */
    if (Csrf::isTokenValid('TermsPrivacy')) {
        /** Check to make sure Admin is updating settings */
        if(Request::post('update_settings') == "true"){
            /** Get data sbmitted by form */
            $site_terms_content = Request::post('site_terms_content');
            $site_privacy_content = Request::post('site_privacy_content');
            if(!$AdminPanelModel->updateSetting('site_terms_content', $site_terms_content)){ $errors[] = 'Site Terms Error'; }
            if(!$AdminPanelModel->updateSetting('site_privacy_content', $site_privacy_content)){ $errors[] = 'Site Policy Error'; }

            // Run the update settings script
            if(!isset($errors) || count($errors) == 0){
                /** Success */
                SuccessMessages::push('You Have Successfully Updated Site Terms and Policy Content', 'AdminPanel-TermsPrivacy');
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
                ErrorMessages::push('Error Updating Site Terms and Policy Content'.$error_data, 'AdminPanel-TermsPrivacy');
            }
        }else{
            /** Error Message Display */
            ErrorMessages::push('Error Updating Site Terms and Policy Content', 'AdminPanel-TermsPrivacy');
        }
    }else{
        /** Error Message Display */
        ErrorMessages::push('Error Updating Site Terms and Policy Content', 'AdminPanel-TermsPrivacy');
    }
  }else{
    /** Error Message Display */
    ErrorMessages::push('Demo Limit - Settings Disabled', 'AdminPanel-TermsPrivacy');
  }
}

/** Get Settings Data */
$data['site_terms_content'] = $AdminPanelModel->getSettings('site_terms_content');
$data['site_privacy_content'] = $AdminPanelModel->getSettings('site_privacy_content');


/** Setup Token for Form */
$data['csrfToken'] = Csrf::makeToken('TermsPrivacy');

/** Setup Breadcrumbs */
$data['breadcrumbs'] = "
  <li class='breadcrumb-item'><a href='".SITE_URL."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li>
  <li class='breadcrumb-item active'><i class='fas fa-fw fa-info-circle'></i> ".$data['title']."</li>
";

?>
<div class='col-lg-12 col-md-12 col-sm-12'>
  <div class='row'>
    <div class='col-lg-12 col-md-12 col-sm-12'>
    	<div class='card mb-3'>
    		<div class='card-header h4'>
    			<?php echo $data['title'];  ?>
          <?php echo PageFunctions::displayPopover('Terms and Policy Content', 'Site Terms and Policy content allows Owner of the site to setup their Terms and Policy data.  We suggest researching how to use Terms and Policy content on your site via Google.  If there are blank, links will not display in footer. HTML can be used.', false, 'btn btn-sm btn-light'); ?>
    		</div>
    		<div class='card-body'>
    			<p><?php echo $data['welcomeMessage'] ?></p>

    			<?php echo Form::open(array('method' => 'post')); ?>

          <!-- Site Terms Content -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
        		<div class='input-group-prepend'>
        			<span class='input-group-text'>Site Terms Content</span>
        		</div>
            <?php echo Form::textBox(array('type' => 'text', 'name' => 'site_terms_content', 'class' => 'form-control', 'value' => $data['site_terms_content'], 'placeholder' => 'Site Terms and Conditions Content', 'rows' => '8')); ?>
          </div>

          <!-- Site Privacy Content -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class='input-group-prepend'>
              <span class='input-group-text'>Site Privacy Content</span>
            </div>
            <?php echo Form::textBox(array('type' => 'text', 'name' => 'site_privacy_content', 'class' => 'form-control', 'value' => $data['site_privacy_content'], 'placeholder' => 'Site Privacy Content', 'rows' => '8')); ?>
          </div>

        </div>
    	</div>
    </div>

    <div class='col-lg-12 col-md-12 col-sm-12'>
        <button class="btn btn-md btn-success" name="submit" type="submit">
            Update Terms and Policy Content
        </button>
        <!-- CSRF Token and What is Being Updated -->
        <input type="hidden" name="token_TermsPrivacy" value="<?php echo $data['csrfToken']; ?>" />
        <input type="hidden" name="update_settings" value="true" />
        <?php echo Form::close(); ?><Br><br>
    </div>
  </div>
</div>
