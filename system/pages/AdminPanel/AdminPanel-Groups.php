<?php
/**
* Admin Panel Groups View
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.3
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

// Check for orderby selection
$data['orderby'] = Request::post('orderby');

// Get data for users
$data['current_page'] = $_SERVER['REQUEST_URI'];
$data['title'] = "Groups";
$data['welcomeMessage'] = "Welcome to the Groups Admin Panel";
$data['groups_list'] = $AdminPanelModel->getGroups($data['orderby']);
$data['csrfToken'] = Csrf::makeToken('groups');

// Setup Breadcrumbs
$data['breadcrumbs'] = "<li class='breadcrumb-item'><a href='".SITE_URL."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li><li class='breadcrumb-item active'><i class='fa fa-fw fa-users-cog'></i> ".$data['title']."</li>";

// Check to make sure admin is trying to create group
if(isset($_POST['submit'])){
  /** Check to see if site is a demo site */
  if(DEMO_SITE != 'TRUE'){
    // Check to make sure the csrf token is good
    if (Csrf::isTokenValid('groups')) {
      //Check for create group
      if(Request::post('create_group') == "true"){
        // Catch password inputs using the Request helper
        $ag_groupName = Request::post('ag_groupName');
        if(!empty($ag_groupName)){
          // Run the update group script
          $new_group_id = $AdminPanelModel->createGroup($ag_groupName);
          if($new_group_id){
            /** Group Create Success */
            SuccessMessages::push('You Have Successfully Created a New Group', 'AdminPanel-Group/'.$new_group_id);
          }else{
            /** Group Create Error. Show Error */
            ErrorMessages::push('Group Creation Error!', 'AdminPanel-Groups');
          }
        }else{
          /** Group Name Field Empty. Show Error */
          ErrorMessages::push('Group Creation Error: Group Name Field Empty!', 'AdminPanel-Groups');
        }
      }
    }
  }else{
    /** Error Message Display */
    ErrorMessages::push('Demo Limit - User Group Settings Disabled', 'AdminPanel-Groups');
  }
}

$orderby = $data['orderby'];

?>


  <div class='col-lg-12 col-md-12 col-sm-12'>
    <div class='card mb-3 mw-100'>
      <div class='card-header h4'>
        Create New Group
        <?php echo PageFunctions::displayPopover('Create New User Group', 'New Site User Group can be added and altered to fit your site needs. Click on a group to view and edit that group.', false, 'btn btn-sm btn-light'); ?>
      </div>
      <div class='card-body'>
        <?php echo Form::open(array('method' => 'post')); ?>
        <?php echo Form::input(array('type' => 'text', 'name' => 'ag_groupName', 'class' => 'form-control', 'placeholder' => 'New Group Name', 'maxlength' => '150')); ?>
        <input type='hidden' name='token_groups' value='<?php echo $data['csrfToken'] ?>'>
        <input type='hidden' name='create_group' value='true' />
      </div>
      <div class='card-footer text-muted'>
        <button name='submit' type='submit' class="btn btn-sm btn-success">Create New Group</button>
        <?php echo Form::close(); ?>
      </div>
    </div>
  </div>


  <div class='col-lg-12 col-md-12 col-sm-12'>
  	<div class='card mb-3'>
  		<div class='card-header h4'>
  			All Site Groups
        <?php echo PageFunctions::displayPopover('User Groups', 'Site User Groups can be altered to fit the site needs.', false, 'btn btn-sm btn-light'); ?>
  		</div>
  		<table class='table table-hover responsive'>
  			<tr>
  				<th>
            <?php
              if(empty($data['orderby'])){
                $ob_value = "ID-DESC";
                $ob_icon = "";
              }
              else if($data['orderby'] == "ID-DESC"){
                $ob_value = "ID-ASC";
                $ob_icon = "<i class='fa fa-fw  fa-caret-down'></i>";
              }
              else if($data['orderby'] == "ID-ASC"){
                $ob_value = "ID-DESC";
                $ob_icon = "<i class='fa fa-fw  fa-caret-up'></i>";
              }else{
                  $ob_value = "ID-DESC";
                  $ob_icon = "";
              }
                // Setup the order by id button
                echo "<form action='' method='post'>";
                echo "<input type='hidden' name='orderby' value='$ob_value'>";
                echo "<button type='submit' class='btn btn-info btn-sm'>ID $ob_icon</button>";
                echo "</form>";
            ?>
          </th>
  				<th>
            <?php
              if(empty($data['orderby'])){
                $obu_value = "UN-DESC";
                $obu_icon = "";
              }
              else if($data['orderby'] == "UN-DESC"){
                $obu_value = "UN-ASC";
                $obu_icon = "<i class='fa fa-fw  fa-caret-down'></i>";
              }
              else if($data['orderby'] == "UN-ASC"){
                $obu_value = "UN-DESC";
                $obu_icon = "<i class='fa fa-fw  fa-caret-up'></i>";
              }else{
                  $obu_value = "UN-DESC";
                  $obu_icon = "";
              }
                // Setup the order by id button
                echo "<form action='' method='post'>";
                echo "<input type='hidden' name='orderby' value='$obu_value'>";
                echo "<button type='submit' class='btn btn-info btn-sm'>Group Name $obu_icon</button>";
                echo "</form>";
            ?>
          </th>
          <th>Display</th>
          <th></th>
  			</tr>
  			<?php
  				if(isset($data['groups_list'])){
  					foreach($data['groups_list'] as $row) {
  						echo "<tr>";
              echo "<td>$row->groupID</td>";
  						/** Check to make sure group has a name/title **/
  						$group_name = (!empty($row->groupName) ? $row->groupName : "UnNamed Group");
  						echo "<td><button type='button' class='btn btn-secondary btn-sm' data-toggle='modal' data-target='#myModal-$row->groupID'>$group_name</button></td>";
              echo "<td><font color='$row->groupFontColor' style='font-weight: $row->groupFontWeight'>$row->groupName</font></td>";
              echo "<td align='right'>";
              echo "<a href='".SITE_URL."AdminPanel-Group/$row->groupID' class='btn btn-sm btn-primary'><span class='fas fa-edit'></span></a>";
              echo "</td>";
  						echo "</tr>";
              echo "
                <!-- Modal -->
                <div class='modal fade' id='myModal-$row->groupID' tabindex='-1' role='dialog' aria-labelledby='myModalLabel'>
                  <div class='modal-dialog' role='document'>
                    <div class='modal-content'>
                      <div class='modal-header'>
                        <h4 class='modal-title' id='myModalLabel'><span class='fas fa-fw fa-users'></span> ".$row->groupName." Information</h4>
                        <button type='button' class='close float-right' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
                      </div>
                      <div class='modal-body'>
                        <b style='border-bottom: 1px solid #ccc'>Group Name Display:</b><br>
                        <font color='$row->groupFontColor' style='font-weight: $row->groupFontWeight'>$row->groupName</font>
                        <br><br>
                        <b style='border-bottom: 1px solid #ccc'>Group Description:</b><br>
                        $row->groupDescription
                        <br><br>
                        <b style='border-bottom: 1px solid #ccc'>Total Group Members:</b><br>
                        ".CurrentUserData::getGroupMembersCount($row->groupID)."
                      </div>
                      <div class='modal-footer'>
                        <a class='btn btn-primary btn-sm' href='".SITE_URL."AdminPanel-Group/$row->groupID'>Edit Group Info</a>
                        <button type='button' class='btn btn-secondary btn-sm' data-dismiss='modal'>Close</button>
                      </div>
                    </div>
                  </div>
                </div>
              ";
  					}
  				}
  			?>
  		</table>
    </div>
  </div>
