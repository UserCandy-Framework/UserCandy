<?php
/**
* Admin Panel Site Link Page
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
(empty($viewVars[0])) ? $action = null : $action = $viewVars[0];
(empty($viewVars[1])) ? $main_link_id = null : $main_link_id = $viewVars[1];
(empty($viewVars[2])) ? $dd_link_id = null : $dd_link_id = $viewVars[2];

$data['current_page'] = $_SERVER['REQUEST_URI'];

     if($action == 'New'){
       /** Admin is Creating a New Link */
       $data['title'] = "Site Link Editor - New";
       $data['welcomeMessage'] = "You are creating a new site link.  Fill out the form below.";
       $data['edit_type'] = "new";
       $data['csrfToken'] = Csrf::makeToken('SiteLink');
     }else if($action == "LinkDDUp"){
       /** Check to see if site is a demo site */
       if(DEMO_SITE != 'TRUE'){
         if($AdminPanelModel->moveUpDDLink($main_link_id,$dd_link_id)){
           /** Success */
           SuccessMessages::push('You Have Successfully Moved Up Drop Down Link', 'AdminPanel-SiteLink/'.$main_link_id.'/');
         }else{
           /** Error */
           ErrorMessages::push('Move Up Drop Down Link Failed', 'AdminPanel-SiteLink/'.$main_link_id.'/');
         }
       }else{
           /** Error Message Display */
           ErrorMessages::push('Demo Limit - Site Routes Settings Disabled', 'AdminPanel-SiteLink/'.$main_link_id.'/');
       }
     }else if($action == "LinkDDDown"){
       /** Check to see if site is a demo site */
       if(DEMO_SITE != 'TRUE'){
         if($AdminPanelModel->moveDownDDLink($main_link_id,$dd_link_id)){
           /** Success */
           SuccessMessages::push('You Have Successfully Moved Down Drop Down Link', 'AdminPanel-SiteLink/'.$main_link_id.'/');
         }else{
           /** Error */
           ErrorMessages::push('Move Down Drop Down Link Failed', 'AdminPanel-SiteLink/'.$main_link_id.'/');
         }
       }else{
           /** Error Message Display */
           ErrorMessages::push('Demo Limit - Site Routes Settings Disabled', 'AdminPanel-SiteLink/'.$main_link_id.'/');
       }
     }else if($action == 'LinkDelete'){
         /** Admin is Creating a New Link */
         $main_link_title = $AdminPanelModel->getMainLinkTitle($main_link_id);
         $data['title'] = "Site Link Editor - Delete Link: $main_link_title";
         $data['welcomeMessage'] = "Do you want to delete link: $main_link_title <Br><Br>";
         $data['welcomeMessage'] .= "<font color=red><b>NOTE</b>: This also deletes all drop down links assigned to this link if dropdown.</font>";
         $data['main_link_id'] = $main_link_id;
         $data['edit_type'] = "deletelink";
         $data['link_data'] = $AdminPanelModel->getSiteLinkData($main_link_id);
         $data['csrfToken'] = Csrf::makeToken('SiteLink');
     }else if($action == 'DropDownUpdate'){
         /** Admin is Creating a New Link */
         $main_link_title = $AdminPanelModel->getMainLinkTitle($main_link_id);
         $data['title'] = "Site Link Editor - Update Drop Down Link for $main_link_title";
         $data['welcomeMessage'] = "You are updating a drop down link.  Fill out the form below.";
         $data['dd_link_id'] = $dd_link_id;
         $data['main_link_id'] = $main_link_id;
         $data['edit_type'] = "dropdownupdate";
         $data['link_data'] = $AdminPanelModel->getSiteLinkData($dd_link_id);
         $data['csrfToken'] = Csrf::makeToken('SiteLink');
     }else if($action == 'DropDownNew'){
         /** Admin is Creating a New Link */
         $main_link_title = $AdminPanelModel->getMainLinkTitle($main_link_id);
         $data['title'] = "Site Link Editor - New Drop Down Link for $main_link_title";
         $data['welcomeMessage'] = "You are creating a new drop down link.  Fill out the form below.";
         $data['main_link_id'] = $main_link_id;
         $data['edit_type'] = "dropdownnew";
         $data['csrfToken'] = Csrf::makeToken('SiteLink');
     }else if(ctype_digit(strval($action))){
       /** Admin is Editing a Link */
       $data['title'] = "Site Link Editor - Update";
       $data['welcomeMessage'] = "You are updating a site link.";
       $data['edit_type'] = "update";
       $data['link_data'] = $AdminPanelModel->getSiteLinkData($action);
       $data['csrfToken'] = Csrf::makeToken('SiteLink');
       /** Get all Drop Down Links */
       $data['drop_down_links'] = $AdminPanelModel->getSiteDropDownLinks($action);
       $data['drop_down_order_last'] = $AdminPanelModel->getSiteDropDownLinksLastID($action);
     }else{
       /** Send User Back because the URL Input is invalid */
       ErrorMessages::push('Invalid URL Input!', 'AdminPanel-SiteLinks');
     }

     /** Check to see if Admin is updating System Route */
     if(isset($_POST['submit'])){
       /** Check to see if site is a demo site */
       if(DEMO_SITE != 'TRUE'){
         /** Check to make sure the csrf token is good */
         if (Csrf::isTokenValid('SiteLink')) {
           /** Get Form Data */
           $link_action = Request::post('link_action');
           $id = Request::post('id');
           $title = Request::post('title');
           $url = Request::post('url');
           $alt_text = Request::post('alt_text');
           $location = Request::post('location');
           $drop_down = Request::post('drop_down');
           $drop_down_for = Request::post('drop_down_for');
           $dd_link_id = Request::post('dd_link_id');
           $permission = Request::post('permission');
           $icon = Request::post('icon');
           if(empty($drop_down)){ $drop_down = "0"; }
           /** Check if update or new */
           if($link_action == "update"){
             if($AdminPanelModel->updateSiteLink($id, $title, $url, $alt_text, $location, $drop_down, $permission, $icon)){
               /** Update URL in Page Permissions and Site Routes */
               if($AdminPanelModel->updatePagePermURL($data['link_data'][0]->url, $url)){$success_msg .= '<br> - Updated Page URL: '.$url;}
               if($AdminPanelModel->updateLinkURL($data['link_data'][0]->url, $url)){$success_msg .= '<br> - Updated  URL: '.$url;}
               /** Success */
               SuccessMessages::push('You Have Successfully Updated Site Link '.$success_msg, 'AdminPanel-SiteLink/'.$id);
             }else{
               /** Error */
               ErrorMessages::push('Update Site Link Failed', 'AdminPanel-SiteLinks');
             }
           }else if($link_action == "new"){
             if($AdminPanelModel->addSiteLink($title, $url, $alt_text, $location, $drop_down, $require_plugin, $permission, $icon)){
               /** Success */
               SuccessMessages::push('You Have Successfully Added New Site Link', 'AdminPanel-SiteLinks');
             }else{
               /** Error */
               ErrorMessages::push('Create New Site Link Failed', 'AdminPanel-SiteLinks');
             }
           }else if($link_action == "delete"){
             if($AdminPanelModel->deleteSiteLink($id)){
               /** Success */
               SuccessMessages::push('You Have Successfully Deleted Site Link', 'AdminPanel-SiteLinks');
             }else{
               /** Error */
               ErrorMessages::push('Delete Site Link Failed', 'AdminPanel-SiteLinks');
             }
           }else if($link_action == "dropdownnew"){
             if($AdminPanelModel->addSiteDDLink($title, $url, $alt_text, $location, $drop_down, $require_plugin, $drop_down_for, $permission, $icon)){
               /** Success */
               SuccessMessages::push('You Have Successfully Added New Site Drop Down Link', 'AdminPanel-SiteLink/'.$drop_down_for);
             }else{
               /** Error */
               ErrorMessages::push('Create New Site Drop Down Link Failed', 'AdminPanel-SiteLink/'.$drop_down_for);
             }
           }else if($link_action == "dropdownupdate"){
             if($AdminPanelModel->updateSiteDDLink($dd_link_id, $title, $url, $alt_text, $location, $drop_down, $require_plugin, $permission, $icon)){
               /** Update URL in Page Permissions and Site Routes */
               if($AdminPanelModel->updatePagePermURL($data['link_data'][0]->url, $url)){$success_msg .= '<br> - Updated Page URL: '.$url;}
               if($AdminPanelModel->updateLinkURL($data['link_data'][0]->url, $url)){$success_msg .= '<br> - Updated  URL: '.$url;}
               /** Success */
               SuccessMessages::push('You Have Successfully Updated Site Drop Down Link '.$success_msg, 'AdminPanel-SiteLink/'.$main_link_id);
             }else{
               /** Error */
               ErrorMessages::push('Update Site Drop Down Link Failed', 'AdminPanel-SiteLink/'.$main_link_id);
             }
           }
         }
       }else{
           /** Error Message Display */
           ErrorMessages::push('Demo Limit - Site Links Settings Disabled', 'AdminPanel-SiteLinks');
       }
     }

     // Setup Breadcrumbs
     $data['breadcrumbs'] = "<li class='breadcrumb-item'><a href='".SITE_URL."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li><li class='breadcrumb-item active'><a href='".SITE_URL."AdminPanel-SiteLinks'><i class='fa fa-fw fa-globe'></i>Site Links</a></li><li class='breadcrumb-item active'><i class='fa fa-fw fa-globe'></i> ".$data['title']."</li>";

?>

<?php if($data['edit_type'] == "deletelink"){ ?>

  <div class='col-lg-12 col-md-12 col-sm-12'>
  	<div class='card mb-3'>
  		<div class='card-header h4'>
  			<?php echo $data['title'];  ?>
        <?php echo PageFunctions::displayPopover('Site Link Delete', 'Site Link Delete will remove selected link from the database, therefore removed from the site. This CANNOT be undone.', false, 'btn btn-sm btn-light'); ?>
  		</div>
  		<div class='card-body'>
  			<p><?php echo $data['welcomeMessage'] ?></p>
        <?php echo Form::open(array('method' => 'post')); ?>
        <!-- CSRF Token -->
        <input type="hidden" name="token_SiteLink" value="<?php echo $data['csrfToken']; ?>" />
        <input type="hidden" name="id" value="<?php echo $data['link_data'][0]->id; ?>" />
        <input type="hidden" name="link_action" value="delete" />
        <button class="btn btn-sm btn-danger" name="submit" type="submit">
          Yes
        </button>
        <a class="btn btn-sm btn-success" href="<?=SITE_URL?>AdminPanel-SiteLinks/">
          No
        </a>
        <?php echo Form::close(); ?>
      </div>
    </div>
  </div>

<?php }else{ ?>

<div class='col-lg-12 col-md-12 col-sm-12'>
	<div class='card mb-3'>
		<div class='card-header h4'>
			<?php echo $data['title'];  ?>
      <?php echo PageFunctions::displayPopover('Site Link', 'Site Link can be created, and edited to fit the site needs.  Admin can set the link data, style, and permissions here.  Settings are Case Sensitive.', false, 'btn btn-sm btn-light'); ?>
		</div>
		<div class='card-body'>
			<p><?php echo $data['welcomeMessage'] ?></p>

      <?php echo Form::open(array('method' => 'post')); ?>

        <!-- Link Title -->
        <div class='input-group mb-3' style='margin-bottom: 25px'>
          <div class="input-group-prepend">
            <span class='input-group-text'><i class='fas fa-fw fa-link'></i> Link Title</span>
          </div>
          <?php echo Form::input(array('type' => 'text', 'name' => 'title', 'class' => 'form-control', 'value' => $data['link_data'][0]->title, 'placeholder' => 'Title For Link Display', 'maxlength' => '100')); ?>
          <?php echo PageFunctions::displayPopover('Link Title', 'Link Title is displayed on the site in the format it is entered here.  Dispaly matches the case used here.', true, 'input-group-text'); ?>
        </div>

        <!-- URL -->
        <div class='input-group mb-3' style='margin-bottom: 25px'>
          <div class="input-group-prepend">
            <span class='input-group-text'><i class='fas fa-fw fa-link'></i> Link URL</span>
          </div>
          <?php echo Form::input(array('type' => 'text', 'name' => 'url', 'class' => 'form-control', 'value' => $data['link_data'][0]->url, 'placeholder' => 'Site URL For Link', 'maxlength' => '100')); ?>
          <?php echo PageFunctions::displayPopover('Link URL', 'Link URL is what the URL for the link will be after the SITE_URL.  The URL used here will let the site know which site route to load.  This is Case Sensitive.', true, 'input-group-text'); ?>
        </div>

        <!-- Alt Text -->
        <div class='input-group mb-3' style='margin-bottom: 25px'>
          <div class="input-group-prepend">
            <span class='input-group-text'><i class='fas fa-fw fa-link'></i> Link Alt Text</span>
          </div>
          <?php echo Form::input(array('type' => 'text', 'name' => 'alt_text', 'class' => 'form-control', 'value' => $data['link_data'][0]->alt_text, 'placeholder' => 'Alt Text to Display on Hover', 'maxlength' => '255')); ?>
          <?php echo PageFunctions::displayPopover('Link Alt Text', 'Link Alt Text is used to give a little more information about the link.  For example if a user hovers over the link, most browsers will show a small popup with this data.', true, 'input-group-text'); ?>
        </div>

        <?php
          /** Check to see if this is a drop down link **/
          if($data['edit_type'] == "new" || $data['edit_type'] == "update"){
        ?>
          <!-- Link For Drop Down Menu -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fas fa-fw fa-caret-down'></i> Drop Down Menu</span>
            </div>
            <select class='form-control' id='drop_down' name='drop_down'>
              <option value='0' <?php if($data['link_data'][0]->drop_down == "0"){echo "SELECTED";}?> >No</option>
              <option value='1' <?php if($data['link_data'][0]->drop_down == "1"){echo "SELECTED";}?> >Yes</option>
            </select>
            <?php echo PageFunctions::displayPopover('Drop Down Menu Enable', 'Drop Down Menu Enable will set the link as a drop down menu.  URL is not needed if Enabled.  Once Enabled, Drop Down Links will apear below.', true, 'input-group-text'); ?>
          </div>

          <!-- Link For Drop Down Menu -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fas fa-fw fa-caret-down'></i> Link Location</span>
            </div>
            <select class='form-control' id='location' name='location'>
              <option value='header_main' <?php if($data['link_data'][0]->location == "header_main"){echo "SELECTED";}?> >Header Main</option>
              <option value='footer_main' <?php if($data['link_data'][0]->location == "footer_main"){echo "SELECTED";}?> >Footer Main</option>
              <option value='nav_admin' <?php if($data['link_data'][0]->location == "nav_admin"){echo "SELECTED";}?> >Navbar AdminPanel</option>
            </select>
            <?php echo PageFunctions::displayPopover('Link Location', 'Link location sets where the link is displayed within the site.', true, 'input-group-text'); ?>
          </div>

        <?php } ?>

        <!-- Permission -->
        <div class='input-group mb-3' style='margin-bottom: 25px'>
          <div class="input-group-prepend">
            <span class='input-group-text'><i class='fas fa-fw fa-users-cog'></i> Permission</span>
          </div>
          <select class='form-control' id='permission' name='permission'>
            <option value='0' <?php if($data['link_data'][0]->permission == "0"){echo "SELECTED";}?> >Public</option>
            <?php
              $getGroups = CurrentUserData::getGroups();
              foreach ($getGroups as $group) {
                echo "<option value='$group->groupID' "; if($data['link_data'][0]->permission == $group->groupID){echo "SELECTED";} echo ">$group->groupName</option>";
              }

            ?>
          </select>
          <?php echo PageFunctions::displayPopover('Link Permission', 'Link Permission sets which user groups can see the link.  If set to Public, then all visitors to the site can view the link.', true, 'input-group-text'); ?>
        </div>

        <!-- Fontawesome icon class -->
        <div class='input-group mb-3' style='margin-bottom: 25px'>
          <div class="input-group-prepend">
            <span class='input-group-text'><i class='fab fa-fw fa-font-awesome'></i> Icon Class</span>
          </div>
          <?php echo Form::input(array('type' => 'text', 'name' => 'icon', 'class' => 'form-control', 'value' => $data['link_data'][0]->icon, 'placeholder' => 'Fontawesome Icon Class : fab fa-font-awesome ', 'maxlength' => '255')); ?>
          <?php echo PageFunctions::displayPopover('Icon Class', 'Icon Class uses Fontawesome 5 icons as listed in www.fontawesome.com.  Copy and paste the class here.  For example: "fas fa-tools" will display the tools icon next to the link.', true, 'input-group-text'); ?>
        </div>

    </div>
    <div class='card-footer'>
        <!-- CSRF Token -->
        <input type="hidden" name="token_SiteLink" value="<?php echo $data['csrfToken']; ?>" />

        <?php if($data['edit_type'] == "update"){ ?>
          <input type="hidden" name="id" value="<?php echo $data['link_data'][0]->id; ?>" />
          <input type="hidden" name="link_action" value="update" />
          <button class="btn btn-md btn-success" name="submit" type="submit">
            Update Link
          </button>
        <?php }else if($data['edit_type'] == "dropdownnew"){ ?>
          <input type="hidden" name="drop_down_for" value="<?php echo $main_link_id; ?>" />
          <input type="hidden" name="link_action" value="dropdownnew" />
          <button class="btn btn-md btn-success" name="submit" type="submit">
            Create New Drop Down Link
          </button>
        <?php }else if($data['edit_type'] == "dropdownupdate"){ ?>
          <input type="hidden" name="dd_link_id" value="<?php echo $dd_link_id; ?>" />
          <input type="hidden" name="link_action" value="dropdownupdate" />
          <button class="btn btn-md btn-success" name="submit" type="submit">
            Update Drop Down Link
          </button>
        <?php }else{ ?>
          <input type="hidden" name="link_action" value="new" />
          <button class="btn btn-sm btn-success" name="submit" type="submit">
            Create Link
          </button>
        <?php } ?>
      <?php echo Form::close(); ?>
      <a href='<?=SITE_URL?>AdminPanel-SiteLink/LinkDelete/<?php echo $data['link_data'][0]->id; ?>/' class='btn btn-sm btn-danger float-right'>Delete</a>
    </div>
	</div>

  <?php
    /** Check if above link has drop down enabled **/
    if($data['link_data'][0]->drop_down == "1"){
  ?>
    <div class='card mb-3'>
      <div class='card-header h4'>
        Drop Down Links for <?php echo $data['link_data'][0]->title; ?>
      </div>
      <table class='table table-hover responsive'>
        <tr>
          <th>Link Title</th><th>URL</th><th>Alt Text</th><th>Location</th><th>Require Plugin</th><th>Permission</th><th>Icon</th><th></th>
        </tr>
        <?php
          if(isset($data['drop_down_links'])){
            foreach ($data['drop_down_links'] as $link) {
              echo "<tr>";
              echo "<td>".$link->title."</td>";
              echo "<td>".$link->url."</td>";
              echo "<td>".$link->alt_text."</td>";
              echo "<td>".$link->location."</td>";
              echo "<td>".$link->require_plugin."</td>";
              echo "<td>".CurrentUserData::getGroupData($link->permission)."</td>";
              echo "<td> <i class='$link->icon'></i> </td>";
              echo "<td align='right'>";
              /** Check to see if object is at top **/
              if($link->link_order_drop_down > 1){
                echo "<a href='".SITE_URL."AdminPanel-SiteLink/LinkDDUp/".$data['link_data'][0]->id."/$link->id/' class='btn btn-primary btn-sm' role='button'><span class='fa fa-fw fa-caret-up' aria-hidden='true'></span></a> ";
              }
              /** Check to see if object is at bottom **/
              if($drop_down_order_last != $link->link_order_drop_down){
                echo "<a href='".SITE_URL."AdminPanel-SiteLink/LinkDDDown/".$data['link_data'][0]->id."/$link->id/' class='btn btn-primary btn-sm' role='button'><span class='fa fa-fw fa-caret-down' aria-hidden='true'></span></a> ";
              }
              echo "<a href='".SITE_URL."AdminPanel-SiteLink/DropDownUpdate/".$data['link_data'][0]->id."/$link->id/' class='btn btn-sm btn-success'><span class='fas fa-edit'></span></a>";
              echo "</td>";
              echo "</tr>";
            }
          }
        ?>
      </table>
      <div class='card-footer'>
        <a href='<?=SITE_URL?>AdminPanel-SiteLink/DropDownNew/<?php echo $data['link_data'][0]->id; ?>/' class='btn btn-sm btn-success'>Add New Drop Down Link</a>
      </div>
    </div>
  <?php } ?>
</div>
<?php } ?>
