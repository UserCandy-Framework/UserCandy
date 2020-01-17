<?php
/**
* Admin Panel Group View
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

use Helpers\{ErrorMessages,SuccessMessages,Paginator,Csrf,Request,Url,PageFunctions,Form,CurrentUserData};
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

/** Get data from URL **/
(empty($viewVars[0])) ? $id = null : $id = $viewVars[0];

// Check for orderby selection
$data['orderby'] = Request::post('orderby');

// Get data for users
$data['current_page'] = $_SERVER['REQUEST_URI'];
$data['title'] = "Group";
$data['welcomeMessage'] = "Welcome to the Group Admin Panel";
$data['csrfToken'] = Csrf::makeToken('group');

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

// Check to make sure admin is trying to update group data
if(isset($_POST['submit'])){
  /** Check to see if site is a demo site */
  if(DEMO_SITE != 'TRUE'){
    // Check to make sure the csrf token is good
    if (Csrf::isTokenValid('group')) {
      // Check for update group
      if(Request::post('update_group') == "true"){
        // Catch password inputs using the Request helper
        $ag_groupID = Request::post('ag_groupID');
        $ag_groupName = Request::post('ag_groupName');
        $ag_groupDescription = Request::post('ag_groupDescription');
        $ag_groupFontColor = Request::post('ag_groupFontColor');
        $ag_groupFontWeight = Request::post('ag_groupFontWeight');

        // Run the update group script
        if($AdminPanelModel->updateGroup($ag_groupID, $ag_groupName, $ag_groupDescription, $ag_groupFontColor, $ag_groupFontWeight)){
          /** Success */
          SuccessMessages::push('You Have Successfully Updated a Group', 'AdminPanel-Group/'.$ag_groupID);
        }else{
          /** Error */
          ErrorMessages::push('Group was not Updated', 'AdminPanel-Group/'.$ag_groupID);
        }
      }
      //Check for delete group
      if(Request::post('delete_group') == "true"){
        // Catch password inputs using the Request helper
        $ag_groupID = Request::post('ag_groupID');

        // Run the update group script
        if($AdminPanelModel->deleteGroup($ag_groupID)){
          /** Success */
          SuccessMessages::push('You Have Successfully Deleted a Group', 'AdminPanel-Groups');
        }else{
          /** Error */
          ErrorMessages::push('Group was not Deleted', 'AdminPanel-Group/'.$ag_groupID);
        }
      }
    }
  }else{
    /** Error Message Display */
    ErrorMessages::push('Demo Limit - User Group Settings Disabled', 'AdminPanel-Groups');
  }
}

// Setup Current User data
// Get user data from user's database
$current_group_data = $AdminPanelModel->getGroup($id);
foreach($current_group_data as $group_data){
  $data['g_groupID'] = $group_data->groupID;
  $data['g_groupName'] = $group_data->groupName;
  $data['g_groupDescription'] = $group_data->groupDescription;
  $data['g_groupFontColor'] = $group_data->groupFontColor;
  $data['g_groupFontWeight'] = $group_data->groupFontWeight;
}

// Setup Breadcrumbs
$data['breadcrumbs'] = "<li class='breadcrumb-item'><a href='".SITE_URL."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li><li class='breadcrumb-item'><a href='".SITE_URL."AdminPanel-Groups'><i class='fa fa-fw fa-users-cog'></i> Groups </a></li><li class='breadcrumb-item active'><i class='fas fa-fw fa-users'></i> Group - ".$data['g_groupName']."</li>";

?>

<div class='col-lg-12 col-md-12 col-sm-12'>
	<div class='card mb-3'>
		<div class='card-header h4'>
			<?php echo $data['title']." - ".$data['g_groupName']  ?>
      <?php echo PageFunctions::displayPopover('User Group Admin', 'Site User Group Admin allows editing group styles, details, and limits.', false, 'btn btn-sm btn-light'); ?>
		</div>
		<div class='card-body'>

			<p><?php echo $data['welcomeMessage'] ?></p>

			<?php echo Form::open(array('method' => 'post')); ?>

			<!-- Group Name -->
			<div class='input-group mb-3' style='margin-bottom: 25px'>
        <div class="input-group-prepend">
				  <span class='input-group-text'><i class='fas fa-fw fa-users'></i> Group Name</span>
        </div>
				<?php echo Form::input(array('type' => 'text', 'name' => 'ag_groupName', 'class' => 'form-control', 'value' => $data['g_groupName'], 'placeholder' => 'Group Name', 'maxlength' => '150')); ?>
			</div>

				<!-- Group Description -->
				<div class='input-group mb-3' style='margin-bottom: 25px'>
          <div class="input-group-prepend">
  				  <span class='input-group-text'><i class='fa fa-fw  fa-book'></i> Group Description</span>
          </div>
					<?php echo Form::input(array('type' => 'text', 'name' => 'ag_groupDescription', 'class' => 'form-control', 'value' => $data['g_groupDescription'], 'placeholder' => 'Group Description', 'maxlength' => '255')); ?>
				</div>

				<!-- Group Font Color -->
				<div class='input-group mb-3' style='margin-bottom: 25px'>
          <div class="input-group-prepend">
  				  <span class='input-group-text'><i class='fa fa-fw  fa-paint-brush'></i> Group Font Color</span>
          </div>
					<?php echo Form::input(array('type' => 'text', 'name' => 'ag_groupFontColor', 'class' => 'form-control', 'value' => $data['g_groupFontColor'], 'placeholder' => 'Font Color', 'maxlength' => '20')); ?>
				</div>

        <!-- Group Font Weight -->
				<div class='input-group mb-3' style='margin-bottom: 25px'>
          <div class="input-group-prepend">
  				  <span class='input-group-text'><i class='fa fa-fw  fa-bold'></i> Group Font Weight</span>
          </div>
          <select class='form-control' id='gender' name='ag_groupFontWeight'>
            <option value='Normal' <?php if($data['g_groupFontWeight'] == "Normal"){echo "SELECTED";}?> >Normal</option>
            <option value='Bold' <?php if($data['g_groupFontWeight'] == "Bold"){echo "SELECTED";}?> >Bold</option>
          </select>
				</div>

				<!-- CSRF Token -->
				<input type="hidden" name="token_group" value="<?php echo $data['csrfToken']; ?>" />
				<input type="hidden" name="ag_groupID" value="<?php echo $data['g_groupID']; ?>" />
        <input type="hidden" name="update_group" value="true" />
				<button class="btn btn-md btn-success" name="submit" type="submit">
					<?php // echo Language::show('update_profile', 'Auth'); ?>
					Update Group
				</button>
			<?php echo Form::close(); ?>

      <?php
        if($data['g_groupID'] == "4"){
          echo "<br><div class='alert alert-warning'><b>NOTE</b>: By default this group has full access to the website and can not be deleted. Default Group Name: <b>Administrator</b></div>";
        }else if($data['g_groupID'] == "3"){
          echo "<br><div class='alert alert-warning'><b>NOTE</b>: By default this group has set access to the website and can not be deleted. Default Group Name: <b>Moderator</b></div>";
        }else if($data['g_groupID'] == "2"){
          echo "<br><div class='alert alert-warning'><b>NOTE</b>: By default this group has limited access to the website and can not be deleted. Default Group Name: <b>Member</b></div>";
        }else if($data['g_groupID'] == "1"){
          echo "<br><div class='alert alert-warning'><b>NOTE</b>: By default this group has limited access to the website and can not be deleted. Default Group Name: <b>New Member</b></div>";
        }else{
          echo "<br><br>";
          echo Form::open(array('method' => 'post', 'style' => 'display:inline-block'));
          echo "<input type='hidden' name='token_group' value='".$data['csrfToken']."'>";
          echo "<input type='hidden' name='delete_group' value='true' />";
          echo "<input type='hidden' name='ag_groupID' value='".$data['g_groupID']."'>";
          echo "<button class='btn btn-sm btn-danger' name='submit' type='submit'>Delete Group</button>";
          echo Form::close();
        }
      ?>
		</div>
	</div>
</div>
