<?php
/**
* Admin Panel Users Page
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

/** Get data from URL **/
(empty($viewVars[0])) ? $set_order_by = 'ID-ASC' : $set_order_by = $viewVars[0];
(empty($viewVars[1])) ? $current_page = "1" : $current_page = $viewVars[1];
(empty($viewVars[2])) ? $url_search_users = '' : $url_search_users = $viewVars[2];

// Check for orderby selection
      $data['orderby'] = $set_order_by;
      /** Get data from search */
      if(!empty($url_search_users)){
        $data['search_users_data'] = $url_search_users;
      }else if(isset($_POST['submit'])){
        /** Check to make sure the csrf token is good */
        if (Csrf::isTokenValid('user')) {
          /** Check to see if user is trying to search */
          if(Request::post('search_users') == "true"){
            /** Get data from POST */
            $data['search_users_data'] = Request::post('search_users_data');
          }
        }
      }
      // Set total number of rows for paginator
      $total_num_users = $AdminPanelModel->getTotalUsers($data['search_users_data']);
      $pages->setTotal($total_num_users);
      // Send page links to view
      if(!empty($data['search_users_data'])){ $link_search_users = "/".$data['search_users_data']; }
      $pageFormat = SITE_URL."AdminPanel-Users/$set_order_by/"; // URL page where pages are
      $data['pageLinks'] = $pages->pageLinks($pageFormat, $link_search_users, $current_page);
      $data['current_page_num'] = $current_page;
      // Get data for users
      $data['current_page'] = $_SERVER['REQUEST_URI'];
      $data['title'] = "Users";
      $data['welcomeMessage'] = "Welcome to the Users Admin Panel";
      $data['users_list'] = $AdminPanelModel->getUsers($data['orderby'], $pages->getLimit($current_page, USERS_PAGEINATOR_LIMIT), $data['search_users_data']);
      $data['csrfToken'] = Csrf::makeToken('user');
      // Setup Breadcrumbs
      $data['breadcrumbs'] = "<li class='breadcrumb-item'><a href='".SITE_URL."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li><li class='breadcrumb-item active'><i class='fa fa-fw fa-users'></i> ".$data['title']."</li>";


$orderby = $data['orderby'];
/** Setup Sort By User ID **/
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
	$ob_value = "ID-ASC";
	$ob_icon = "";
}
/** Setup Sort By Username **/
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
	$obu_value = "UN-ASC";
	$obu_icon = "";
}
?>


<div class='col-lg-12 col-md-12 col-sm-12'>

	<!-- User Search -->
  <div class="card mb-3">
    <div class="card-header h4" role="tab" id="headingUnfiled">
			<a class="collapsed d-block search-users" data-toggle="collapse" href="#collapse-collapsed" aria-expanded="true" aria-controls="collapse-collapsed" id="heading-collapsed">
					<i class="fa fa-fw fa-users"></i> <span>Search Users</span>
			</a>
		</div>
		<div id="collapse-collapsed" class="collapse <?php if(!empty($data['search_users_data'])){echo "show";} ?>" aria-labelledby="heading-collapsed">
			<div class="card-body">
				Use this Search form to find the user your looking for.

				<?php echo Form::open(array('method' => 'post', 'action' => SITE_URL.'AdminPanel-Users')); ?>
				<div class='row'>
					<div class='col-12'>
						<?php echo Form::input(array('type' => 'text', 'name' => 'search_users_data', 'class' => 'form-control', 'value' => $search_users_data, 'placeholder' => 'Type Username, First Name, or Last Name.', 'maxlength' => '255')); ?>
					</div>
				</div>
				<!-- CSRF Token -->
				<?php echo Form::input(array('type' => 'hidden', 'name' => 'token_user', 'value' => $data['csrfToken'])); ?>
				<?php echo Form::input(array('type' => 'hidden', 'name' => 'search_users', 'value' => 'true')); ?>
				<br>
				<?php echo Form::button(array('class' => 'btn btn-success', 'name' => 'submit', 'type' => 'submit', 'value' => 'Search Users')); ?>
				<?php echo Form::close(); ?>

			</div>
		</div>
	</div>


	<div class='card mb-3'>
		<div class='card-header h4'>
			<?php echo $data['title'] ?>
			<?php echo PageFunctions::displayPopover('Site Users Admin', 'Site Users Admin list all users that are registered for the site.  Click on the UserName for more info.  Click the Edit button to edit the user.', false, 'btn btn-sm btn-light'); ?>
		</div>
			<table class='table table-hover responsive'>
				<tr>
					<th>
            <?php
                // Setup the order by id button
								echo "<a href='".SITE_URL."AdminPanel-Users/$ob_value/".$data['current_page_num']."/$search_users_data' class='btn btn-info btn-sm'>UID $ob_icon</button>";
            ?>
          </th>
					<th>
            <?php
              // Setup the order by id button
              echo "<a href='".SITE_URL."AdminPanel-Users/$obu_value/".$data['current_page_num']."/$search_users_data' class='btn btn-info btn-sm'>UserName $obu_icon</button>";
            ?>
          </th>
          <th>Name</th>
          <th class='d-none d-md-table-cell'>LastLogin</th>
					<th class='d-none d-md-table-cell'>SignUp</th>
					<th></th>
				</tr>
				<?php
					if(isset($data['users_list'])){
						foreach($data['users_list'] as $row) {
							$online_check = CurrentUserData::getUserStatusDot($row->userID);
							$online_check_status = CurrentUserData::getUserStatus($row->userID);
							echo "<tr>";
              echo "<td>$row->userID</td>";
							echo "<td><button type='button' class='btn btn-secondary btn-sm' data-toggle='modal' data-target='#myModal-$row->userID'> $online_check $row->username</button></td>";
							echo "<td>$row->firstName $row->lastName</td>";
              echo "<td class='d-none d-md-table-cell'>";
								if($row->LastLogin){ echo date("M d, y",strtotime($row->LastLogin)); }else{ echo "Never"; }
							echo "</td>";
							echo "<td class='d-none d-md-table-cell'>";
							echo date("M d, y",strtotime($row->SignUp));
							echo "</td>";
							echo "<td align='right'>";
							echo "<a href='".SITE_URL."AdminPanel-User/$row->userID' class='btn btn-sm btn-primary'><span class='fas fa-edit'></span></a>";
							echo "</td>";
							echo "</tr>";
							echo "
								<!-- Modal -->
								<div class='modal fade' id='myModal-$row->userID' tabindex='-1' role='dialog' aria-labelledby='myModalLabel'>
								  <div class='modal-dialog' role='document'>
								    <div class='modal-content'>
								      <div class='modal-header'>
								        <h4 class='modal-title' id='myModalLabel'><span class='fa fa-fw  fa-user'></span> ".$row->username."&#39;s Information</h4>
												<button type='button' class='close float-right' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
								      </div>
								      <div class='modal-body'>
												<div class='row'>
							";
													$user_image_display = CurrentUserData::getUserImage($row->userID);
													if(!empty($user_image_display)){
														echo "<div class='col-lg-6 col-md-6 col-sm-6'>";
														echo "<img alt='$row->username's Profile Picture' src='".SITE_URL.IMG_DIR_PROFILE.$user_image_display."' class='rounded img-fluid'>";
														echo "</div>";
														echo "<div class='col-lg-6 col-md-6 col-sm-6'>";
													}else{
														echo "<div class='col-lg-12 col-md-12 col-sm-12'>";
													}
							echo "
														<b style='border-bottom: 1px solid #ccc'>User's Groups</b><Br>
							";
														$users_groups = CurrentUserData::getUserGroups($row->userID);
														if(isset($users_groups)){
															foreach($users_groups as $ug_row){ echo " - <font size='2'>".$ug_row."</font> <br>"; };
														}else{
															echo " - <font size='2'>User Not a Member of Any Groups</font> <br>";
														}
							echo "
														<br><b style='border-bottom: 1px solid #ccc'>Account Status:</b><br>
							";
														if($row->isactive == 1){ echo "- Account is <font color=green>Active</font>"; }else{ echo "- Account is <font color=red>Not Active</font>"; }
														echo "<br>- User is $online_check_status";
							echo "
													</div>
												</div>
								      </div>
								      <div class='modal-footer'>
												<a class='btn btn-primary btn-sm' href='".SITE_URL."AdminPanel-User/$row->userID'>Edit ".$row->username."&#39;s Info</a>
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
		<?php
			// Check to see if there is more than one page
			if($data['pageLinks'] > "1"){
				echo "<div class='card-footer text-muted' style='text-align: center'>";
				echo $data['pageLinks'];
				echo "</div>";
			}
		?>
	</div>
</div>
