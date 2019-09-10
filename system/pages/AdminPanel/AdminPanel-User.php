<?php
/**
* Admin Panel User View
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

/** Get data from URL **/
(empty($viewVars[0])) ? $id = null : $id = $viewVars[0];

// Check for orderby selection
      $data['orderby'] = Request::post('orderby');
      // Get data for users
      $data['current_page'] = $_SERVER['REQUEST_URI'];
      $data['title'] = "User";
      $data['welcomeMessage'] = "Welcome to the User Admin Panel";
      $data['csrfToken'] = Csrf::makeToken('user');
      // Get user groups data
      $data_groups = $AdminPanelModel->getAllGroups();
      // Get groups user is and is not member of
      foreach ($data_groups as $value) {
        $data_user_groups = $AdminPanelModel->checkUserGroup($id, $value->groupID);
        if($data_user_groups){
          $group_member[] = $value->groupID;
        }else{
          $group_not_member[] = $value->groupID;
        }
      }
      // Gether group data for group user is member of
      if(isset($group_member)){
        foreach ($group_member as $value) {
          $group_member_data[] = $AdminPanelModel->getGroupData($value);
        }
      }
      // Push group data to view
      $data['user_member_groups'] = $group_member_data;
      // Gether group data for group user is not member of
      if(isset($group_not_member)){
        foreach ($group_not_member as $value) {
          $group_notmember_data[] = $AdminPanelModel->getGroupData($value);
        }
      }
      // Push group data to view
      $data['user_notmember_groups'] = $group_notmember_data;
      // Check to make sure admin is trying to update user profile
  		if(isset($_POST['submit'])){
        /** Check to see if site is a demo site */
        if(DEMO_SITE != 'TRUE'){
    			// Check to make sure the csrf token is good
    			if (Csrf::isTokenValid('user')) {
            if(Request::post('update_profile') == "true"){
              // Catch password inputs using the Request helper
              $au_id = Request::post('au_id');
              $au_username = Request::post('au_username');
              $au_email = Request::post('au_email');
              $au_firstName = Request::post('au_firstName');
              $au_lastName = Request::post('au_lastName');
              $au_gender = Request::post('au_gender');
              $au_website = Request::post('au_website');
              $au_aboutme = Request::post('au_aboutme');
              $au_signature = Request::post('au_signature');

              // Run the update profile script
              if($AdminPanelModel->updateProfile($au_id, $au_username, $au_firstName, $au_lastName, $au_email, $au_gender, $au_website, $au_aboutme, $au_signature)){
                  /** Success */
                  SuccessMessages::push('You Have Successfully Updated User Profile', 'AdminPanel-User/'.$au_id);
              }else{
                  /** User Update Fail. Show Error */
                  ErrorMessages::push('Profile Update Failed!', 'AdminPanel-User/'.$au_id);
              }
            }

            // Check to see if admin is removing user from group
            if(Request::post('remove_group') == "true"){
                // Get data from post
                $au_userID = Request::post('au_userID');
                $au_groupID = Request::post('au_groupID');
                // Check to make sure Admin is not trying to remove user's last group
                if($AdminPanelModel->checkUserGroupsCount($au_userID)){
                    // Updates current user's group
                    if($AdminPanelModel->removeFromGroup($au_userID, $au_groupID)){
                    	/** Success */
                        SuccessMessages::push('You Have Successfully Removed User From Group', 'AdminPanel-User/'.$au_userID);
                    }else{
                        /** User Update Fail. Show Error */
                        ErrorMessages::push('Remove From Group Failed!', 'AdminPanel-User/'.$au_userID);
                    }
                }else{
                    /** User Update Fail. Show Error */
                    ErrorMessages::push('User Must Be a Member of at least ONE Group!', 'AdminPanel-User/'.$au_userID);
                }
            }

            // Check to see if admin is adding user to group
            if(Request::post('add_group') == "true"){
              // Get data from post
              $au_userID = Request::post('au_userID');
              $au_groupID = Request::post('au_groupID');
              // Updates current user's group
      				if($AdminPanelModel->addToGroup($au_userID, $au_groupID)){
      					/** Success */
                SuccessMessages::push('You Have Successfully Added User to Group', 'AdminPanel-User/'.$au_userID);
      				}else{
                        /** User Update Fail. Show Error */
                        ErrorMessages::push('Add to Group Failed!', 'AdminPanel-User/'.$au_id);
      				}
            }

            // Check to see if admin wants to activate user
            if(Request::post('activate_user') == "true"){
              $au_id = Request::post('au_id');
              // Run the Activation script
      				if($AdminPanelModel->activateUser($au_id)){
      					/** Success */
                SuccessMessages::push('You Have Successfully Activated User', 'AdminPanel-User/'.$au_id);
      				}else{
                        /** User Update Fail. Show Error */
                        ErrorMessages::push('Activate User Failed!', 'AdminPanel-User/'.$au_id);
      				}
            }

            // Check to see if admin wants to deactivate user
            if(Request::post('deactivate_user') == "true"){
              $au_id = Request::post('au_id');
              // Run the Activation script
      				if($AdminPanelModel->deactivateUser($au_id)){
      					/** Success */
                SuccessMessages::push('You Have Successfully Deactivated User', 'AdminPanel-User/'.$au_id);
      				}else{
                        /** User Update Fail. Show Error */
                        ErrorMessages::push('Deactivate User Failed!', 'AdminPanel-User/'.$au_id);
      				}
            }
          }
        }else{
        	/** Error Message Display */
        	ErrorMessages::push('Demo Limit - User Settings Disabled', 'AdminPanel-Users');
        }
  		}

      // Setup Current User data
  		// Get user data from user's database
  		$user_data = $AdminPanelModel->getUser($id);

      // Setup Breadcrumbs
      $data['breadcrumbs'] = "<li class='breadcrumb-item'><a href='".SITE_URL."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li><li class='breadcrumb-item'><a href='".SITE_URL."AdminPanel-Users'><i class='fa fa-fw fa-users'></i> Users </a></li><li class='breadcrumb-item active'><i class='fa fa-fw fa-user'></i>User - ".$user_data[0]->username."</li>";


?>
<div class='col-lg-12 col-md-12 col-sm-12'>
  <div class='row'>
    <div class='col-lg-8 col-md-8 col-sm-8'>
    	<div class='card mb-3'>
    		<div class='card-header h4'>
    			<?php echo $data['title']." - ".$user_data[0]->username;  ?>
          <?php echo PageFunctions::displayPopover('User Admin', 'Site User Admin allows the user profile data to be altered, set group, etc.', false, 'btn btn-sm btn-light'); ?>
          <font class='float-right' size='2'><?php echo CurrentUserData::getUserStatus($user_data[0]->userID); ?></font>
    		</div>
    		<div class='card-body'>
    			<p><?php echo $data['welcomeMessage'] ?></p>

    			<?php echo Form::open(array('method' => 'post')); ?>

    			<!-- User Name -->
    			<div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
    				  <span class='input-group-text'><i class='fa fa-fw  fa-user'></i> UserName</span>
            </div>
    				<?php echo Form::input(array('type' => 'text', 'name' => 'au_username', 'class' => 'form-control', 'value' => $user_data[0]->username, 'placeholder' => 'UserName', 'maxlength' => '100')); ?>
    			</div>

    				<!-- First Name -->
    				<div class='input-group mb-3' style='margin-bottom: 25px'>
              <div class="input-group-prepend">
      				  <span class='input-group-text'><i class='fa fa-fw  fa-user'></i> First Name</span>
              </div>
    					<?php echo Form::input(array('type' => 'text', 'name' => 'au_firstName', 'class' => 'form-control', 'value' => $user_data[0]->firstName, 'placeholder' => 'First Name', 'maxlength' => '100')); ?>
    				</div>

            <!-- First Name -->
    				<div class='input-group mb-3' style='margin-bottom: 25px'>
              <div class="input-group-prepend">
      				  <span class='input-group-text'><i class='fa fa-fw  fa-user'></i> Last Name</span>
              </div>
    					<?php echo Form::input(array('type' => 'text', 'name' => 'au_lastName', 'class' => 'form-control', 'value' => $user_data[0]->lastName, 'placeholder' => 'Last Name', 'maxlength' => '100')); ?>
    				</div>

    				<!-- Email -->
    				<div class='input-group mb-3' style='margin-bottom: 25px'>
              <div class="input-group-prepend">
      				  <span class='input-group-text'><i class='fa fa-fw  fa-envelope'></i> Email</span>
              </div>
    					<?php echo Form::input(array('type' => 'text', 'name' => 'au_email', 'class' => 'form-control', 'value' => $user_data[0]->email, 'placeholder' => 'Email Address', 'maxlength' => '100')); ?>
    				</div>

    				<!-- Gender -->
    				<div class='input-group mb-3' style='margin-bottom: 25px'>
              <div class="input-group-prepend">
      				  <span class='input-group-text'><i class='fa fa-fw  fa-user'></i> Gender</span>
              </div>
    					<select class='form-control' id='gender' name='au_gender'>
    				    <option value='Male' <?php if($user_data[0]->gender == "Male"){echo "SELECTED";}?> >Male</option>
    				    <option value='Female' <?php if($user_data[0]->gender == "Female"){echo "SELECTED";}?> >Female</option>
    				  </select>
    				</div>

    				<!-- Website -->
    				<div class='input-group mb-3' style='margin-bottom: 25px'>
              <div class="input-group-prepend">
      				  <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Website</span>
              </div>
    					<?php echo Form::input(array('type' => 'text', 'name' => 'au_website', 'class' => 'form-control', 'value' => $user_data[0]->website, 'placeholder' => 'Website URL', 'maxlength' => '100')); ?>
    				</div>

    				<!-- About Me -->
    				<div class='input-group mb-3' style='margin-bottom: 25px'>
              <div class="input-group-prepend">
      				  <span class='input-group-text'><i class='fa fa-fw  fa-book'></i> About Me</span>
              </div>
    					<?php echo Form::textBox(array('type' => 'text', 'name' => 'au_aboutme', 'class' => 'form-control', 'value' => str_replace("<br />", "", $user_data[0]->aboutme), 'placeholder' => 'About Me', 'rows' => '6')); ?>
    				</div>

            <!-- About Me -->
    				<div class='input-group mb-3' style='margin-bottom: 25px'>
              <div class="input-group-prepend">
      				  <span class='input-group-text'><i class='fa fa-fw  fa-book'></i> About Me</span>
              </div>
    					<?php echo Form::textBox(array('type' => 'text', 'name' => 'au_signature', 'class' => 'form-control', 'value' => str_replace("<br />", "", $user_data[0]->signature), 'placeholder' => 'Forum Signature', 'rows' => '6')); ?>
    				</div>

    				<!-- CSRF Token -->
    				<input type="hidden" name="token_user" value="<?php echo $data['csrfToken']; ?>" />
    				<input type="hidden" name="au_id" value="<?php echo $user_data[0]->userID; ?>" />
            <input type="hidden" name="update_profile" value="true" />
    				<button class="btn btn-md btn-success" name="submit" type="submit">
    					<?php // echo Language::show('update_profile', 'Auth'); ?>
    					Update Profile
    				</button>
    			<?php echo Form::close(); ?>

    		</div>
    	</div>
    </div>

    <div class='col-lg-4 col-md-4 col-sm-4'>
      <div class='card mb-3'>
        <div class='card-header h4'>
          User Stats
        </div>
        <div class='card-body'>
          <b>Last Login</b>: <?php if($user_data[0]->LastLogin){ echo date("F d, Y",strtotime($user_data[0]->LastLogin)); }else{ echo "Never"; } ?><br>
          <b>SignUp</b>: <?php echo date("F d, Y",strtotime($user_data[0]->SignUp)) ?>
          <hr>
          <b>PM Privacy</b>: <?=$user_data[0]->privacy_pm?><br>
          <b>MassEmail Privacy</b>: <?=$user_data[0]->privacy_massemail?><br>
          <hr>
          <?php
            if($user_data[0]->isactive == "1"){
              echo "User Account Is Active";
              echo Form::open(array('method' => 'post'));
              echo "<input type='hidden' name='token_user' value='".$data['csrfToken']."'>";
              echo "<input type='hidden' name='deactivate_user' value='true' />";
              echo "<input type='hidden' name='au_id' value='".$user_data[0]->userID."'>";
              echo "<button class='btn btn-sm btn-danger' name='submit' type='submit'>Deactivate User</button>";
              echo Form::close();
            }else{
              echo "User Account Is Not Active";
              echo Form::open(array('method' => 'post'));
              echo "<input type='hidden' name='token_user' value='".$data['csrfToken']."'>";
              echo "<input type='hidden' name='activate_user' value='true' />";
              echo "<input type='hidden' name='au_id' value='".$user_data[0]->userID."'>";
              echo "<button class='btn btn-sm btn-success' name='submit' type='submit'>Activate User</button>";
              echo Form::close();
            }
          ?>
        </div>
      </div>

      <div class='card mb-3'>
        <div class='card-header h4'>
          User Groups
        </div>
          <?php
            echo "<table class='table table-hover responsive'>";
              // Displays User's Groups they are a member of
              if(isset($data['user_member_groups'])){
                echo "<th>Member of Following Groups</th>";
                foreach($data['user_member_groups'] as $member){
                  echo "<tr><td>";
                  echo Form::open(array('method' => 'post', 'style' => 'display:inline-block'));
                  echo "<input type='hidden' name='token_user' value='".$data['csrfToken']."'>";
                  echo "<input type='hidden' name='remove_group' value='true' />";
                  echo "<input type='hidden' name='au_userID' value='".$user_data[0]->userID."'>";
                  echo "<input type='hidden' name='au_groupID' value='".$member[0]->groupID."'>";
                  echo "<button class='btn btn-sm btn-danger' name='submit' type='submit'>Remove</button>";
                  echo Form::close();
                  echo " - <font color='".$member[0]->groupFontColor."' style='font-weight: ".$member[0]->groupFontWeight."'>".$member[0]->groupName."</font>";
                  echo "</td></tr>";
                }
              }else{
                echo "<th style='background-color: #EEE'>Member of Following Groups: </th>";
                echo "<tr><td> User Not Member of Any Groups </td></tr>";
              }
            echo "</table>";

            echo "<table class='table table-hover responsive'>";
              // Displays User's Groups they are not a member of
              if(isset($data['user_notmember_groups'])){
                echo "<th>Not Member of Following Groups</th>";
                foreach($data['user_notmember_groups'] as $notmember){
                  echo "<tr><td>";
                  echo Form::open(array('method' => 'post', 'style' => 'display:inline-block'));
                  echo "<input type='hidden' name='token_user' value='".$data['csrfToken']."'>";
                  echo "<input type='hidden' name='add_group' value='true' />";
                  echo "<input type='hidden' name='au_userID' value='".$user_data[0]->userID."'>";
                  echo "<input type='hidden' name='au_groupID' value='".$notmember[0]->groupID."'>";
                  echo "<button class='btn btn-sm btn-success' name='submit' type='submit'>Add</button>";
                  echo Form::close();
                  echo " - <font color='".$notmember[0]->groupFontColor."' style='font-weight: ".$notmember[0]->groupFontWeight."'>".$notmember[0]->groupName."</font> ";
                  echo "</td></tr>";
                }
              }else{
                echo "<th style='background-color: #EEE'>Not Member of Following Groups: </th>";
                echo "<tr><td> User is Member of All Groups </td></tr>";
              }
            echo "</table>";
          ?>
      </div>
    </div>
  </div>
</div>
