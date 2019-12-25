<?php
/**
* Admin Panel Page Permissions View
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
(empty($viewVars[0])) ? $id = 'URL-ASC' : $id = $viewVars[0];

/** Get all Site User Groups */
$data['site_groups'] = $AdminPanelModel->getAllGroups();

/** Get All Pages Data */
$data['page_data'] = $AdminPanelModel->getPage($id);

/** Check to see if Admin is updating System Route */
if(isset($_POST['submit'])){
  /** Check to see if site is a demo site */
  if(DEMO_SITE != 'TRUE'){
    /** Check to make sure the csrf token is good */
    if (Csrf::isTokenValid('pages_permissions')) {
      /** Check to see if admin is editing page */
      if(Request::post('update_page') == "true"){
        /** Catch inputs using the Request helper */
        $page_id = Request::post('page_id');
        $group_id = Request::post('group_id');
        $sitemap = Request::post('sitemap');
        if($sitemap != 'true'){ $sitemap = 'false'; }
        $pagefolder = Request::post('pagefolder');
        $pagefile = Request::post('pagefile');
        $url = Request::post('url');
        $arguments = Request::post('arguments');
        $headfoot = Request::post('headfoot');
        $template = Request::post('template');
        $enable = Request::post('enable');

        var_dump($headfoot);

        /** Updated Sitemap Setting **/
        if($AdminPanelModel->updatePageSiteMap($page_id, $sitemap, $pagefolder, $pagefile, $url, $arguments, $enable, $headfoot, $template)){
          $success[] = " - Changed Settings for page: ".$page_name[0]->url;
          if($AdminPanelModel->updateLinkURL($data['page_data'][0]->url, $url)){$success[] = ' - Updated Site Link URL: '.$url;}
        }
        /** Get all permissions for page */
        $get_page_groups = $AdminPanelModel->getPageGroups($page_id);
        /** Get Page Name */
        $page_name = $AdminPanelModel->getPage($page_id);
        /** Check to see if Public is checked */
        if(isset($group_id[0])){
          /** Add to database if not already done */
          /** Check to see if Permission is already in database */
          if(!$AdminPanelModel->checkForPagePermission($page_id, 0)){
            /** Add Page Permission to database */
            if($AdminPanelModel->addPagePermission($page_id, 0)){
              $success[] = " - Added Public Permission for page: ".$page_name[0]->url;
            }
          }
        }else{
          /** Remove from database if exists */
          /** Check to see if Permission is already in database */
          if($AdminPanelModel->checkForPagePermission($page_id, 0)){
            /** Add Page Permission to database */
            if($AdminPanelModel->removePagePermission($page_id, 0)){
              $success[] = " - Removed Public Permission for page: ".$page_name[0]->url;
            }
          }
        }
        /** Updated pages permissions database for site user groups */
        if(!empty($data['site_groups'])){
          foreach ($data['site_groups'] as $key => $value) {
            /** Get group name for success display */
            $get_group_data = $AdminPanelModel->getGroupData($value->groupID);
            if(isset($group_id[$value->groupID])){
              /** Add to database if not already done */
              /** Check to see if Permission is already in database */
              if(!$AdminPanelModel->checkForPagePermission($page_id, $value->groupID)){
                /** Add Page Permission to database */
                if($AdminPanelModel->addPagePermission($page_id, $value->groupID)){
                  $success[] = " - Added ".$get_group_data[0]->groupName." Permission for page: ".$page_name[0]->url;
                }
              }
            }else{
              /** Remove from database if exists */
              /** Check to see if Permission is already in database */
              if($AdminPanelModel->checkForPagePermission($page_id, $value->groupID)){
                /** Add Page Permission to database */
                if($AdminPanelModel->removePagePermission($page_id, $value->groupID)){
                  $success[] = " - Removed ".$get_group_data[0]->groupName." Permission for page: ".$page_name[0]->url;
                }
              }
            }
          }
        }
      }else if(Request::post('delete_page') == "true"){
        /** Catch inputs using the Request helper */
        $page_id = Request::post('page_id');
        /** Get Page Name */
        $page_name = $AdminPanelModel->getPage($page_id);
        /** Admin wants to delete this page **/
        if($AdminPanelModel->deletePage($page_id)){
          /** Success Message Display */
          SuccessMessages::push('Page Permissions have been updated!<Br><br>The following page has been deleted from database: '.$page_name[0]->url, 'AdminPanel-PagesPermissions');
        }
      }
      /** Check for changes **/
      if(!empty($success)){
        /** Change success from a array to a variable */
        $success_msg = "";
        foreach($success as $sm){
          $success_msg .= "$sm<Br>";
        }
        /** Success Message Display */
        SuccessMessages::push('Page Permissions have been updated!<Br><br>'.$success_msg, 'AdminPanel-PagePermissions/'.$page_id);
      }else{
          /** Error Message Display */
          ErrorMessages::push('Page Permissions were not changed!', 'AdminPanel-PagePermissions/'.$page_id);
      }
    }
  }else{
      /** Error Message Display */
      ErrorMessages::push('Demo Limit - Pages Settings Disabled', 'AdminPanel-PagesPermissions');
  }
}

/** Setup Page Info */
$data['title'] = "Page Permissions";
$data['welcomeMessage'] = "Welcome to the Page Permission Admin Page.";

/** Setup Token for Form */
$data['csrfToken'] = Csrf::makeToken('pages_permissions');

/** Setup Breadcrumbs */
$data['breadcrumbs'] = "<li class='breadcrumb-item'><a href='".SITE_URL."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li><li class='breadcrumb-item active'><a href='".SITE_URL."AdminPanel-PagesPermissions'><i class='fas fa-fw fa-unlock-alt'></i> Pages Permissions</a></li><li class='breadcrumb-item active'><i class='fas fa-fw fa-unlock-alt'></i> ".$data['title']."</li>";

?>

<div class='col-lg-12 col-md-12 col-sm-12'>
	<div class='card mb-3'>
		<div class='card-header h4'>
			<?php echo $data['title']." - ".$data['page_data'][0]->pagefolder." - ".$data['page_data'][0]->pagefile;  ?>
      <?php echo PageFunctions::displayPopover('Page Permissions', 'Page Permissions allows the Admin to limit who can see the page based on user group. Only Users that are members of the Groups checked may view this page.  If set to Public, everyone can view this page.', false, 'btn btn-sm btn-light'); ?>
		</div>
		<div class='card-body'>
      <p><?php echo $data['welcomeMessage'] ?></p>
      <div class="row">

        <div class='col-lg-6 col-md-6 col-sm-12'>

    			<?php echo Form::open(array('pagefile' => 'post')); ?>

            <h4>Permission Levels for Page: <a href='<?php echo SITE_URL.$data['page_data'][0]->url; ?>' target='_blank'><?=$data['page_data'][0]->url?></a></h4>
            Check the User Groups that are Allowed to view this page.
            <hr>
            <?php
              if(PageFunctions::checkPageGroup($data['page_data'][0]->id, '0')){ $checked = "checked"; }

              echo "<div class='custom-control custom-checkbox'>";
                echo "<input type='checkbox' class='custom-control-input' id='group_id_0' name='group_id[0]' value='0' $checked>";
                echo "<label class='custom-control-label' for='group_id_0'>Public</label>";
              echo "</div>";
              unset($checked);

              if(isset($data['site_groups'])){
                foreach ($data['site_groups'] as $key => $value) {
                  $group_display = CurrentUserData::getGroupData($value->groupID);
                  if(PageFunctions::checkPageGroup($data['page_data'][0]->id, $value->groupID)){ $checked = "checked"; }

                  echo "<div class='custom-control custom-checkbox'>";
                    echo "<input type='checkbox' class='custom-control-input' id='group_id_$value->groupID' name='group_id[$value->groupID]' value='$value->groupID' $checked>";
                    echo "<label class='custom-control-label' for='group_id_$value->groupID'>".$group_display."</label>";
                  echo "</div>";
                  unset($checked);
                }
              }
            ?>
            <hr>
            <?php
              if($data['page_data'][0]->sitemap == 'true'){ $sitemap_checked = "checked"; }
              echo "<div class='custom-control custom-checkbox'>";
                echo "<input type='checkbox' class='custom-control-input' id='sitemap' name='sitemap' value='true' $sitemap_checked>";
                echo "<label class='custom-control-label' for='sitemap'>Allow Page to be vissible on SiteMap.xml?</label>";
              echo "</div>";
            ?>
          </div>

        <div class='col-lg-6 col-md-6 col-sm-12'>

          <!-- Folder -->
    			<div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
    				  <span class='input-group-text'><i class='fas fa-fw fa-gamepad'></i> Folder</span>
            </div>
    				<?php echo Form::input(array('type' => 'text', 'name' => 'pagefolder', 'class' => 'form-control', 'value' => $data['page_data'][0]->pagefolder, 'placeholder' => 'Folder Name', 'maxlength' => '255')); ?>
            <?php echo PageFunctions::displayPopover('Folder', 'Folder where the page file is located. This is case sensitive.', true, 'input-group-text'); ?>
    			</div>

    			<!-- File -->
    			<div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
    				  <span class='input-group-text'><i class='fa fa-fw  fa-book'></i> File</span>
            </div>
    				<?php echo Form::input(array('type' => 'text', 'name' => 'pagefile', 'class' => 'form-control', 'value' => $data['page_data'][0]->pagefile, 'placeholder' => 'File Name', 'maxlength' => '255')); ?>
            <?php echo PageFunctions::displayPopover('File', 'File is the filename name within the Folder selected above.  This is case sensitive.', true, 'input-group-text'); ?>
    			</div>

          <!-- URL -->
    			<div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
    				  <span class='input-group-text'><i class='fa fa-fw  fa-book'></i> URL</span>
            </div>
    				<?php echo Form::input(array('type' => 'text', 'name' => 'url', 'class' => 'form-control', 'value' => $data['page_data'][0]->url, 'placeholder' => 'URL Address Name', 'maxlength' => '255')); ?>
            <?php echo PageFunctions::displayPopover('Site URL', 'Site URL is what the System Router looks for to know which Folder and File to load based on the settings within this page. This is case sensitive.', true, 'input-group-text'); ?>
    			</div>

          <!-- Arguments -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-book'></i> Arguments</span>
            </div>
              <?php echo Form::input(array('type' => 'text', 'name' => 'arguments', 'class' => 'form-control', 'value' => $data['page_data'][0]->arguments, 'placeholder' => 'Route Arguments', 'maxlength' => '255')); ?>
              <?php echo PageFunctions::displayPopover('Arguments', 'Arguments lets the System Router what type of arguments and how many can be used for a given controller.  EX: (:any)/(:num)/(:all)', true, 'input-group-text'); ?>
          </div>

          <!-- Page Header/Footer Enabled -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-book'></i> Header/Footer Enabled</span>
            </div>
            <select class='form-control' id='headfoot' name='headfoot'>
              <option value='1' <?php if($data['page_data'][0]->headfoot == 1){echo "SELECTED";}?> >Enabled</option>
              <option value='0' <?php if($data['page_data'][0]->headfoot == 0){echo "SELECTED";}?> >Disabled</option>
            </select>
            <?php echo PageFunctions::displayPopover('Header and Footer Enabled', 'Header and Footer Enabled lets the System Router know if Header and Footer should be loaded from templates folder.', true, 'input-group-text'); ?>
          </div>

          <!-- Page Template -->
          <!-- Todo - Setup to auto detect all templates -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-book'></i> Page Template</span>
            </div>
            <select class='form-control' id='template' name='template'>
              <option value='Default' <?php if($data['page_data'][0]->template == 'Default'){echo "SELECTED";}?> >Default</option>
              <option value='AdminPanel' <?php if($data['page_data'][0]->template == 'AdminPanel'){echo "SELECTED";}?> >AdminPanel</option>
              <?php
                /** Check for Installed Custom Templates **/
                $DispenserEnabledTemplates = $DispenserModel->getDispenserByType('template');
                if(isset($DispenserEnabledTemplates)){
                  foreach ($DispenserEnabledTemplates as $dtemplate) {
                    echo "<option value='{$dtemplate->folder_location}' ";
                    if($data['page_data'][0]->template == $dtemplate->folder_location){echo "SELECTED";}
                    echo " >{$dtemplate->folder_location}</option>";
                  }
                }
              ?>
            </select>
            <?php echo PageFunctions::displayPopover('Page Template', 'Page Template lets the System Router know which template should be used for this page.', true, 'input-group-text'); ?>
          </div>

            <!-- Page Route Enabled -->
    				<div class='input-group mb-3' style='margin-bottom: 25px'>
              <div class="input-group-prepend">
      				  <span class='input-group-text'><i class='fa fa-fw  fa-book'></i> Route Enabled</span>
              </div>
              <select class='form-control' id='enable' name='enable'>
                <option value='true' <?php if($data['page_data'][0]->enable == "true"){echo "SELECTED";}?> >Enabled</option>
                <option value='false' <?php if($data['page_data'][0]->enable == "false"){echo "SELECTED";}?> >Disabled</option>
              </select>
              <?php echo PageFunctions::displayPopover('Page Enabled', 'Page Enabled lets the System Router know if this route can be used or not.  When disabled it give a error page.', true, 'input-group-text'); ?>
    				</div>

          </div>
        </div>
      </div>
      <div class='card-footer'>
				<!-- CSRF Token -->
				<input type="hidden" name="token_pages_permissions" value="<?php echo $data['csrfToken']; ?>" />
				<input type="hidden" name="page_id" value="<?php echo $data['page_data'][0]->id; ?>" />
        <input type="hidden" name="update_page" value="true" />
				<button class="btn btn-md btn-success" name="submit" type="submit">
					Update Page
				</button>
			<?php echo Form::close(); ?>
      <?php
      echo "<a href='#DeleteModal' class='btn btn-sm btn-danger trigger-btn float-right' data-toggle='modal'>Delete</a>";

      echo "
        <div class='modal fade' id='DeleteModal' tabindex='-1' role='dialog' aria-labelledby='DeleteLabel' aria-hidden='true'>
          <div class='modal-dialog' role='document'>
            <div class='modal-content'>
              <div class='modal-header'>
                <h5 class='modal-title' id='DeleteLabel'>Delete Page?</h5>
                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                  <span aria-hidden='true'>&times;</span>
                </button>
              </div>
              <div class='modal-body'>
                Do you want to delete this page?<br><br>
                ".$data['page_data'][0]->url."
              </div>
              <div class='modal-footer'>
                <button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>
                ";
                echo Form::open(array('pagefile' => 'post', 'class' => 'float-right', 'style' => 'display:inline'));
                echo "<input type='hidden' name='token_pages_permissions' value='".$data['csrfToken']."'>";
                echo "<input type='hidden' name='delete_page' value='true' />";
                echo "<input type='hidden' name='page_id' value='".$data['page_data'][0]->id."'>";
                echo "<button class='btn btn-md btn-danger' name='submit' type='submit'>Delete Page Permission</button>";
                echo Form::close();
                echo "
              </div>
            </div>
          </div>
        </div>
      ";
      ?>
		</div>
	</div>
</div>
