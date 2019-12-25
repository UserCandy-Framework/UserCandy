<?php
/**
* Admin Panel Auth Log Viewer
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

/** Get data from URL **/
(empty($viewVars[0])) ? $current_page = null : $current_page = $viewVars[0];

// Set total number of rows for paginator
$total_num_auth_logs = $AdminPanelModel->getTotalAuthLogs();
$pages->setTotal($total_num_auth_logs);

// Send page links to view
$pageFormat = SITE_URL."AdminPanel-AuthLogs/"; // URL page where pages are
$data['pageLinks'] = $pages->pageLinks($pageFormat, null, $current_page);
$data['current_page_num'] = $current_page;

// Get data for users
$data['current_page'] = $_SERVER['REQUEST_URI'];
$data['title'] = "Auth Logs";
$data['welcomeMessage'] = "Welcome to the Admin Panel Auth Logs";
$data['auth_logs'] = $AdminPanelModel->getAuthLogs($pages->getLimit($current_page, USERS_PAGEINATOR_LIMIT));

// Setup Breadcrumbs
$data['breadcrumbs'] = "<li class='breadcrumb-item'><a href='".SITE_URL."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li><li class='breadcrumb-item active'><i class='fa fa-fw fa-server'></i> ".$data['title']."</li>";

?>
<div class='col-lg-12 col-md-12 col-sm-12'>
	<div class='card mb-3'>
		<div class='card-header h4'>
			<?php echo $data['title'] ?>
			<?php echo PageFunctions::displayPopover('Site Auth Logs', 'Site Auth Logs displays all logs related to user registration and login.  This is best used to detect an attack on the site, and enable the admin to fix possible security issues.', false, 'btn btn-sm btn-light'); ?>
		</div>
			<table class='table table-hover responsive'>
				<tr>
					<th class='d-none d-md-table-cell'>Date</th>
					<th>Username</th>
          <th>Action</th>
          <th class='d-none d-md-table-cell'>Info</th>
					<th class='d-none d-md-table-cell'>IP</th>
				</tr>
				<?php
					if(isset($data['auth_logs'])){
						foreach($data['auth_logs'] as $row) {
							echo "<tr>";
              echo "<td class='d-none d-md-table-cell'>$row->date</td>";
							echo "<td>$row->username</td>";
							echo "<td>$row->action</td>";
              echo "<td class='d-none d-md-table-cell'>$row->additionalinfo</td>";
							echo "<td class='d-none d-md-table-cell'>$row->ip</td>";
							echo "</tr>";
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
