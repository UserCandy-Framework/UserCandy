<?php
/**
* Admin Panel Site Links Page
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
(empty($viewVars[0])) ? $action = null : $action = $viewVars[0];
(empty($viewVars[1])) ? $link_location = null : $link_location = $viewVars[1];
(empty($viewVars[2])) ? $link_id = null : $link_id = $viewVars[2];

// Get data for users
      $data['title'] = "Site Links";
      $data['welcomeMessage'] = "Welcome to the Admin Panel Site Links Editor.  You can edit links shown within assigned arears of the web site.";
      $data['current_page'] = $_SERVER['REQUEST_URI'];

      // Setup Breadcrumbs
      $data['breadcrumbs'] = "<li class='breadcrumb-item'><a href='".SITE_URL."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li><li class='breadcrumb-item active'><i class='fa fa-fw fa-globe'></i> ".$data['title']."</li>";

      /** Check to see if site is a demo site */
      if($action == "LinkUp"){
        /** Check to see if site is a demo site */
        if(DEMO_SITE != 'TRUE'){
          if($AdminPanelModel->moveUpLink($link_location,$link_id)){
            /** Success */
            SuccessMessages::push('You Have Successfully Moved Up Site Link', 'AdminPanel-SiteLinks');
          }else{
            /** Error */
            ErrorMessages::push('Moved Up Site Link Failed', 'AdminPanel-SiteLinks');
          }
        }else{
            /** Error Message Display */
            ErrorMessages::push('Demo Limit - Site Routes Settings Disabled', 'AdminPanel-SiteLinks');
        }
      }else if($action == "LinkDown"){
        /** Check to see if site is a demo site */
        if(DEMO_SITE != 'TRUE'){
          if($AdminPanelModel->moveDownLink($link_location,$link_id)){
            /** Success */
            SuccessMessages::push('You Have Successfully Moved Down Site Link', 'AdminPanel-SiteLinks');
          }else{
            /** Error */
            ErrorMessages::push('Moved Down Site Link Failed', 'AdminPanel-SiteLinks');
          }
        }else{
            /** Error Message Display */
            ErrorMessages::push('Demo Limit - Site Routes Settings Disabled', 'AdminPanel-SiteLinks');
        }
      }

      /** Get all Main Site Links */
      $main_site_links = $AdminPanelModel->getSiteLinks('header_main');
      $link_order_last = $AdminPanelModel->getSiteLinksLastID('header_main');
      $footer_site_links = $AdminPanelModel->getSiteLinks('footer_main');
      $admin_site_links = $AdminPanelModel->getSiteLinks('nav_admin');


?>

<div class='col-lg-12 col-md-12 col-sm-12'>
	<div class='card mb-3'>
		<div class='card-header h4'>
			<?php echo $data['title'];  ?>
      <?php echo PageFunctions::displayPopover('Site Links', 'Site Links allow the Admin to edit links within a given area of the site and set who can view the links based on groups.', false, 'btn btn-sm btn-light'); ?>
		</div>
		<div class='card-body'>
			<p><?php echo $data['welcomeMessage'] ?></p>
      <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="#">
        <img class='navbar-brand' src='<?php echo Url::templatePath(); ?>images/logo.png'>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav mr-auto">
            <?php echo PageFunctions::getLinks('header_main', $currentUserData[0]->userID); ?>
          </ul>
        </div>
      </nav>
    </div>
	</div>

  <div class='card mb-3'>
    <div class='card-header h4'>
      Header Main Site Links
      <?php echo PageFunctions::displayPopover('Site Header Links', 'Site Header Links are located in the top Navbar on the site.  They can be edited here.', false, 'btn btn-sm btn-light'); ?>
    </div>
    <table class='table table-hover responsive'>
      <tr>
        <th>Link Title</th>
        <th>URL</th>
        <th class='d-none d-md-table-cell'>Alt Text</th>
        <th class='d-none d-md-table-cell'>Drop Down</th>
        <th class='d-none d-md-table-cell'>Require Plugin</th>
        <th class='d-none d-md-table-cell'>Permission</th>
        <th class='d-none d-md-table-cell'>Icon</th>
        <th></th>
      </tr>
      <?php
        if(isset($main_site_links)){
          foreach ($main_site_links as $link) {
            echo "<tr>";
            echo "<td>".$link->title."</td>";
            echo "<td>".$link->url."</td>";
            echo "<td class='d-none d-md-table-cell'>".$link->alt_text."</td>";
            echo "<td class='d-none d-md-table-cell'>";
              if($link->drop_down == "1"){ echo "Drop Down Link"; }
            echo "</td>";
            echo "<td class='d-none d-md-table-cell'>".$link->require_plugin."</td>";
            echo "<td class='d-none d-md-table-cell'>".CurrentUserData::getGroupData($link->permission)."</td>";
            echo "<td class='d-none d-md-table-cell'> <i class='$link->icon'></i> </td>";
            echo "<td align='right'>";
            /** Check to see if object is at top **/
            if($link->link_order > 1){
              echo "<a href='".SITE_URL."AdminPanel-SiteLinks/LinkUp/$link->location/$link->id/' class='btn btn-primary btn-sm' role='button'><span class='fa fa-fw fa-caret-up' aria-hidden='true'></span></a> ";
            }
            /** Check to see if object is at bottom **/
            if($link_order_last != $link->link_order){
              echo "<a href='".SITE_URL."AdminPanel-SiteLinks/LinkDown/$link->location/$link->id/' class='btn btn-primary btn-sm' role='button'><span class='fa fa-fw fa-caret-down' aria-hidden='true'></span></a> ";
            }
            echo "<a href='".SITE_URL."AdminPanel-SiteLink/$link->id' class='btn btn-sm btn-success'><span class='fas fa-edit'></span></a>";
            echo "</td>";
            echo "</tr>";
          }
        }
      ?>
    </table>
    <div class='card-footer'>
      <a href='<?=SITE_URL?>AdminPanel-SiteLink/New' class='btn btn-sm btn-success'>Add New Site Link</a>
    </div>
  </div>

  <div class='card mb-3'>
    <div class='card-header h4'>
      Footer Main Site Links
      <?php echo PageFunctions::displayPopover('Site Footer Links', 'Site Footer Links are located in the footer on the site.  They can be edited here.', false, 'btn btn-sm btn-light'); ?>
    </div>
    <table class='table table-hover responsive'>
      <tr>
        <th>Link Title</th>
        <th>URL</th>
        <th class='d-none d-md-table-cell'>Alt Text</th>
        <th class='d-none d-md-table-cell'>Drop Down</th>
        <th class='d-none d-md-table-cell'>Require Plugin</th>
        <th class='d-none d-md-table-cell'>Permission</th>
        <th class='d-none d-md-table-cell'>Icon</th>
        <th></th>
      </tr>
      <?php
        if(isset($footer_site_links)){
          foreach ($footer_site_links as $link) {
            echo "<tr>";
            echo "<td>".$link->title."</td>";
            echo "<td>".$link->url."</td>";
            echo "<td class='d-none d-md-table-cell'>".$link->alt_text."</td>";
            echo "<td class='d-none d-md-table-cell'>";
              if($link->drop_down == "1"){ echo "Drop Down Link"; }
            echo "</td>";
            echo "<td class='d-none d-md-table-cell'>".$link->require_plugin."</td>";
            echo "<td class='d-none d-md-table-cell'>".CurrentUserData::getGroupData($link->permission)."</td>";
            echo "<td class='d-none d-md-table-cell'> <i class='$link->icon'></i> </td>";
            echo "<td align='right'>";
            /** Check to see if object is at top **/
            if($link->link_order > 1){
              echo "<a href='".SITE_URL."AdminPanel-SiteLinks/LinkUp/$link->location/$link->id/' class='btn btn-primary btn-sm' role='button'><span class='fa fa-fw fa-caret-up' aria-hidden='true'></span></a> ";
            }
            /** Check to see if object is at bottom **/
            if($link_order_last != $link->link_order){
              echo "<a href='".SITE_URL."AdminPanel-SiteLinks/LinkDown/$link->location/$link->id/' class='btn btn-primary btn-sm' role='button'><span class='fa fa-fw fa-caret-down' aria-hidden='true'></span></a> ";
            }
            echo "<a href='".SITE_URL."AdminPanel-SiteLink/$link->id' class='btn btn-sm btn-success'><span class='fas fa-edit'></span></a>";
            echo "</td>";
            echo "</tr>";
          }
        }
      ?>
    </table>
    <div class='card-footer'>
      <a href='<?=SITE_URL?>AdminPanel-SiteLink/New' class='btn btn-sm btn-success'>Add New Site Link</a>
    </div>
  </div>

  <div class='card mb-3'>
    <div class='card-header h4'>
      AdminPanel Links
      <?php echo PageFunctions::displayPopover('AdminPanel Links', 'AdminPanel Links are located in the Sidebar within the AdminPanel Template.  They can be edited here.', false, 'btn btn-sm btn-light'); ?>
    </div>
    <table class='table table-hover responsive'>
      <tr>
        <th>Link Title</th>
        <th>URL</th>
        <th class='d-none d-md-table-cell'>Alt Text</th>
        <th class='d-none d-md-table-cell'>Drop Down</th>
        <th class='d-none d-md-table-cell'>Require Plugin</th>
        <th class='d-none d-md-table-cell'>Permission</th>
        <th class='d-none d-md-table-cell'>Icon</th>
        <th></th>
      </tr>
      <?php
        if(isset($admin_site_links)){
          foreach ($admin_site_links as $link) {
            echo "<tr>";
            echo "<td>".$link->title."</td>";
            echo "<td>".$link->url."</td>";
            echo "<td class='d-none d-md-table-cell'>".$link->alt_text."</td>";
            echo "<td class='d-none d-md-table-cell'>";
              if($link->drop_down == "1"){ echo "Drop Down Link"; }
            echo "</td>";
            echo "<td class='d-none d-md-table-cell'>".$link->require_plugin."</td>";
            echo "<td class='d-none d-md-table-cell'>".CurrentUserData::getGroupData($link->permission)."</td>";
            echo "<td class='d-none d-md-table-cell'> <i class='$link->icon'></i> </td>";
            echo "<td align='right'>";
            /** Check to see if object is at top **/
            if($link->link_order > 1){
              echo "<a href='".SITE_URL."AdminPanel-SiteLinks/LinkUp/$link->location/$link->id/' class='btn btn-primary btn-sm' role='button'><span class='fa fa-fw fa-caret-up' aria-hidden='true'></span></a> ";
            }
            /** Check to see if object is at bottom **/
            if($link_order_last != $link->link_order){
              echo "<a href='".SITE_URL."AdminPanel-SiteLinks/LinkDown/$link->location/$link->id/' class='btn btn-primary btn-sm' role='button'><span class='fa fa-fw fa-caret-down' aria-hidden='true'></span></a> ";
            }
            echo "<a href='".SITE_URL."AdminPanel-SiteLink/$link->id' class='btn btn-sm btn-success'><span class='fas fa-edit'></span></a>";
            echo "</td>";
            echo "</tr>";
          }
        }
      ?>
    </table>
    <div class='card-footer'>
      <a href='<?=SITE_URL?>AdminPanel-SiteLink/New' class='btn btn-sm btn-success'>Add New Site Link</a>
    </div>
  </div>

</div>
