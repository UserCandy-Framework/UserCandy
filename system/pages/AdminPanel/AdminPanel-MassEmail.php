<?php
/**
* Admin Panel Mass E-Mail Page
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
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

/** Setup Title and Welcome Message */
      $data['title'] = "Mass E-mail";
      $data['welcomeMessage'] = "Welcome to the Mass E-mail Admin Feature.  This feature will send an email to All site members that have not disabled the feature.";
      $data['current_page'] = $_SERVER['REQUEST_URI'];
      $data['get_users_massemail_allow'] = $AdminPanelModel->getUsersMassEmail();
      $data['csrfToken'] = Csrf::makeToken('massemail');

      // Setup Breadcrumbs
      $data['breadcrumbs'] = "<li class='breadcrumb-item'><a href='".SITE_URL."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li><li class='breadcrumb-item active'><i class='fas fa-fw fa-mail-bulk'></i> ".$data['title']."</li>";

      (isset($_SESSION['subject'])) ? $data['subject'] = $_SESSION['subject'] : $data['subject'] = "";
      unset($_SESSION['subject']);
      (isset($_SESSION['content'])) ? $data['content'] = $_SESSION['content'] : $data['content'] = "";
      unset($_SESSION['content']);

      // Check to make sure admin is trying to create group
  		if(isset($_POST['submit'])){
        /** Check to see if site is a demo site */
        if(DEMO_SITE != 'TRUE'){
    			// Check to make sure the csrf token is good
    			if (Csrf::isTokenValid('massemail')) {
            // Catch password inputs using the Request helper
            $subject = Request::post('subject');
            $content = Request::post('content');
            if(empty($subject)){ $errormsg[] = "Subject Field Blank!"; }
            if(empty($content)){ $errormsg[] = "Content Field Blank!"; }
            if(!isset($errormsg)){
              // Run the mass email script
              foreach ($data['get_users_massemail_allow'] as $row) {
                if($AdminPanelModel->sendMassEmail($row->userID, $u_id, $subject, $content, $row->username, $row->email)){
                  $count = $count + 1;
                }
              }
              if($count > 0){
                /** Success */
                SuccessMessages::push('You Have Successfully Sent Mass Email to '.$count.' Users', 'AdminPanel-MassEmail');
              }else{
                /** Fail */
                ErrorMessages::push('Mass Email Error', 'AdminPanel-MassEmail');
              }
            }else{
              $me_errors = "<hr>";
              foreach ($errormsg as $row) {
                $me_errors .= $row."<Br>";
              }
              /** Fail */
              $_SESSION['subject'] = $subject;
              $_SESSION['content'] = $content;
              ErrorMessages::push('Mass Email Error'.$me_errors, 'AdminPanel-MassEmail');
            }
          }
        }else{
        	/** Error Message Display */
        	ErrorMessages::push('Demo Limit - Mass Email Settings Disabled', 'AdminPanel-MassEmail');
        }
      }


?>

<div class='col-lg-12 col-md-12 col-sm-12'>
	<div class="card mb-3">
		<div class="card-header h4">
			<?=$data['title'];?>
			<?php echo PageFunctions::displayPopover('Mass E-mail', 'Mass E-mail sends an email to all activated users on the site.  This is best used to give all users important updates related to the site.  Limit the use of this feature to reduce chance of being marked as spam.  Message is sent as plaintext only.', false, 'btn btn-sm btn-light'); ?>
		</div>
		<div class="card-body">
			<p><?=$data['welcomeMessage'];?></p>
			<hr><?php echo count($data['get_users_massemail_allow']) ?> Users Will Be Sent This Email<hr>

			<?php echo Form::open(array('method' => 'post')); ?>

      <!-- Subject -->
      <div class='input-group mb-3' style='margin-bottom: 25px'>
				<div class="input-group-prepend">
        	<span class='input-group-text'><i class='fas fa-fw fa-envelope'></i> </span>
				</div>
        <?php echo Form::input(array('type' => 'text', 'name' => 'subject', 'class' => 'form-control', 'value' => urldecode($data['subject']), 'placeholder' => 'Subject', 'maxlength' => '100')); ?>
      </div>

      <!-- Message Content -->
      <div class='input-group mb-3' style='margin-bottom: 25px'>
				<div class="input-group-prepend">
					<span class='input-group-text'><i class='fas fa-fw fa-envelope'></i> </span>
				</div>
        <?php echo Form::textBox(array('type' => 'text', 'name' => 'content', 'class' => 'form-control', 'value' => $data['content'], 'placeholder' => 'Message Content', 'rows' => '6')); ?>
      </div>

        <!-- CSRF Token -->
        <input type="hidden" name="token_massemail" value="<?=$data['csrfToken']?>" />
        <button class="btn btn-md btn-success" name="submit" type="submit">
          Send Mass Email
        </button>
      <?php echo Form::close(); ?>

		</div>
	</div>
</div>
