<?php
/**
* Site Members List Page
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

/** Get data from URL **/
(empty($viewVars[0])) ? $set_order_by = "ID-ASC" : $set_order_by = $viewVars[0];
(empty($viewVars[1])) ? $current_page = "1" : $current_page = $viewVars[1];
(empty($viewVars[2])) ? $search = "" : $search = $viewVars[2];

$pages = new Paginator(USERS_PAGEINATOR_LIMIT);

// Let sidebar Know we are on the members page
$data['members_page'] = true;

// Check for orderby selection
$data['orderby'] = $set_order_by;

// Check to see if member is searching for a user
if(isset($search)){
    // Set total number of rows for paginator
    $total_num_users = $membersModel->getTotalMembersSearch($search);
    $pages->setTotal($total_num_users);
    $search_url = "/".$search;
}else{
    // Set total number of rows for paginator
    $total_num_users = $membersModel->getTotalMembers();
    $pages->setTotal($total_num_users);
    $search_url = "";
}

// Send page links to view
$pageFormat = SITE_URL."Members/$set_order_by/"; // URL page where pages are
$data['pageLinks'] = $pages->pageLinks($pageFormat, $search_url, $current_page);
$data['current_page_num'] = $current_page;

// Check to see if member is searching for a user
if(!empty($search)){
    // Display Search Info
    $data['title'] = Language::show('members_search_title', 'Members');
    $data['welcomeMessage'] = Language::show('search_found', 'Members').' '.$total_num_users.' '.Language::show('matches_for', 'Members').': '.$search;
    // Get list of members that match search criteria
    $data['members'] = $membersModel->getMembers($data['orderby'], $pages->getLimit($current_page, USERS_PAGEINATOR_LIMIT), $search);
    // Let the view know user is searching
    $data['search'] = $search;
}else{
    // Display all members
    $data['title'] = Language::show('members_title', 'Members');
    $data['welcomeMessage'] = Language::show('members_welcomemessage', 'Members');
    // Get list of members
    $data['members'] = $membersModel->getMembers($data['orderby'], $pages->getLimit($current_page, USERS_PAGEINATOR_LIMIT));
    // Let the view know user is searching
    $data['search'] = false;
}

/** Setup Breadcrumbs **/
$data['breadcrumbs'] = "<li class='breadcrumb-item active'>".$data['title']."</li>";

?>

<div class="col-lg-12 col-md-12 col-sm-12">
	<div class="card mb-3">
		<div class="card-header h4">
			<?=$data['title']?>
		</div>
    <table class="table table-striped table-hover responsive">
				<thead><tr><th colspan='4'><?=$data['welcomeMessage'];?></th></tr></thead>
        <thead>
            <tr>
                <th colspan='2'>
					<?php
					if(empty($data['orderby'])){
						$obu_value = "UN-DESC";
						$obu_icon = "";
					}else if($data['orderby'] == "UN-DESC"){
						$obu_value = "UN-ASC";
						$obu_icon = "<i class='fas fa-caret-down'></i>";
					}else if($data['orderby'] == "UN-ASC"){
						$obu_value = "UN-DESC";
						$obu_icon = "<i class='fas fa-caret-up'></i>";
					}else{
						$obu_value = "UN-ASC";
						$obu_icon = "";
					}
					if(isset($search)){
						$search_url = "/$search";
					}else{
						$search_url = "";
					}
					// Setup the order by id button
					echo "<a href='".SITE_URL."Members/$obu_value/".$data['current_page_num'].$search_url."' class=''>".Language::show('members_username', 'Members')." $obu_icon</button>";
					?>
				</th>
                <th><?=Language::show('members_firstname', 'Members'); ?></th>
                <th>
					<?php
					if(empty($data['orderby'])){
						$obg_value = "UG-DESC";
						$obg_icon = "";
					}
					else if($data['orderby'] == "UG-DESC"){
						$obg_value = "UG-ASC";
						$obg_icon = "<i class='fas fa-caret-down'></i>";
					}
					else if($data['orderby'] == "UG-ASC"){
						$obg_value = "UG-DESC";
						$obg_icon = "<i class='fas fa-caret-up'></i>";
					}else{
						$obg_value = "UG-ASC";
						$obg_icon = "";
					}
					// Setup the order by id button
					echo "<a href='".SITE_URL."Members/$obg_value/".$data['current_page_num'].$search_url."' class=''>".Language::show('members_usergroup', 'Members')." $obg_icon</button>";
					?>
								</th>
            </tr>
        </thead>
        <tbody>
        <?php
            foreach($data['members'] as $member){
								$user_online = CurrentUserData::getUserStatusDot($member->userID);
                echo "<tr>
                        <td width='20px'><img src=".SITE_URL.IMG_DIR_PROFILE.$member->userImage." class='rounded' style='height: 25px'></td>
												<td>$user_online<a href='".SITE_URL."Profile/{$member->username}'> {$member->username}</a></td>
                        <td>{$member->firstName}</td>
                        <td><font color='{$member->groupFontColor}' style='font-weight:{$member->groupFontWeight}'>{$member->groupName}</font></td>
											</tr>";
            }
        ?>
        </tbody>
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
