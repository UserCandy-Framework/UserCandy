<?php
/**
* Admin Panel Dispenser Settings Page
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

use Helpers\{ErrorMessages,SuccessMessages,Paginator,Csrf,Request,Url,PageFunctions,Form};
use Models\{AdminPanelModel,DispenserModel};

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

/** Get data from URL **/
(empty($viewVars[0])) ? $id = null : $id = $viewVars[0];
(empty($viewVars[1])) ? $edit_widget_id = null : $edit_widget_id = $viewVars[1];

/** Load Models **/
$AdminPanelModel = new AdminPanelModel();
$DispenserModel = new DispenserModel();
$pages = new Paginator(USERS_PAGEINATOR_LIMIT);  // How many rows per page

/** Check to see if Admin is submiting form data */
if(isset($_POST['submit'])){
  /** Check to see if site is a demo site */
  if(DEMO_SITE != 'TRUE'){
    /** Check to make sure the csrf token is good */
    if (Csrf::isTokenValid('settings')) {
        /** Check to make sure Admin is updating settings */
        if(Request::post('update_settings') == "true"){
            /** Get data sbmitted by form */
            $name = Request::post('name');
            $type = Request::post('type');
            $folder_location = Request::post('folder_location');
            $version = Request::post('version');
            $enable = Request::post('enable');
            if(!$DispenserModel->updateDispenserSettings($id, $name, $type, $folder_location, $version, $enable)){ $errors[] = 'Widget Update Error'; }

            // Run the update settings script
            if(!isset($errors) || count($errors) == 0){
                /** Success */
                SuccessMessages::push('You Have Successfully Updated Widget Settings', 'AdminPanel-Dispenser-Widgets-Settings/'.$id);
            }else{
                // Error
                if(isset($errors)){
                    $error_data = "<hr>";
                    foreach($errors as $row){
                        $error_data .= " - ".$row."<br>";
                    }
                }else{
                    $error_data = "";
                }
                /** Error Message Display */
                ErrorMessages::push('Error Updating Dispenser Settings'.$error_data, 'AdminPanel-Dispenser-Widgets-Settings/'.$id);
            }
        }else if(Request::post('update_widget') == "true"){
          /** Get data sbmitted by form */
          $display_type = Request::post('display_type');
          $display_location = Request::post('display_location');
          $page_id = Request::post('page_id');
          if(!$DispenserModel->updateWidgetSetting($edit_widget_id, $display_type, $display_location, $page_id)){ $errors[] = 'Widget Setting Add Error'; }

          // Run the update settings script
          if(!isset($errors) || count($errors) == 0){
              /** Success */
              SuccessMessages::push('You Have Successfully Updated Widget Settings', 'AdminPanel-Dispenser-Widgets-Settings/'.$id);
          }else{
              // Error
              if(isset($errors)){
                  $error_data = "<hr>";
                  foreach($errors as $row){
                      $error_data .= " - ".$row."<br>";
                  }
              }else{
                  $error_data = "";
              }
              /** Error Message Display */
              ErrorMessages::push('Error Updating Dispenser Settings'.$error_data, 'AdminPanel-Dispenser-Widgets-Settings/'.$id);
          }
        }else if(Request::post('insert_widget') == "true"){
          /** Get data sbmitted by form */
          $display_type = Request::post('display_type');
          $display_location = Request::post('display_location');
          $page_id = Request::post('page_id');
          if(!$DispenserModel->insertWidgetSetting($id, $display_type, $display_location, $page_id)){ $errors[] = 'Widget Setting Add Error'; }

          // Run the update settings script
          if(!isset($errors) || count($errors) == 0){
              /** Success */
              SuccessMessages::push('You Have Successfully Updated Widget Settings', 'AdminPanel-Dispenser-Widgets-Settings/'.$id);
          }else{
              // Error
              if(isset($errors)){
                  $error_data = "<hr>";
                  foreach($errors as $row){
                      $error_data .= " - ".$row."<br>";
                  }
              }else{
                  $error_data = "";
              }
              /** Error Message Display */
              ErrorMessages::push('Error Updating Dispenser Settings'.$error_data, 'AdminPanel-Dispenser-Widgets-Settings/'.$id);
          }
        }else if(Request::post('delete_widget') == "true"){
          /** Get data sbmitted by form */
          $delete_id = Request::post('delete_id');
          if(!$DispenserModel->deleteWidgetSetting($delete_id, $id)){ $errors[] = 'Widget Setting Delete Error'; }

          // Run the update settings script
          if(!isset($errors) || count($errors) == 0){
              /** Success */
              SuccessMessages::push('You Have Successfully Updated Widget Settings', 'AdminPanel-Dispenser-Widgets-Settings/'.$id);
          }else{
              // Error
              if(isset($errors)){
                  $error_data = "<hr>";
                  foreach($errors as $row){
                      $error_data .= " - ".$row."<br>";
                  }
              }else{
                  $error_data = "";
              }
              /** Error Message Display */
              ErrorMessages::push('Error Updating Dispenser Settings'.$error_data, 'AdminPanel-Dispenser-Widgets-Settings/'.$id);
          }
        }else{
            /** Error Message Display */
            ErrorMessages::push('Error Updating Dispenser Settings', 'AdminPanel-Dispenser-Widgets-Settings/'.$id);
        }
    }else{
        /** Error Message Display */
        ErrorMessages::push('Error Updating Dispenser Settings', 'AdminPanel-Dispenser-Widgets-Settings/'.$id);
    }
  }else{
    /** Error Message Display */
    ErrorMessages::push('Demo Limit - Settings Disabled', 'AdminPanel-Dispenser-Widgets-Settings/'.$id);
  }
}

/** Get Settings Data */
$dispenser_api_key = $AdminPanelModel->getSettings('dispenser_api_key');

/** Get Widget Data **/
$widget_data = $DispenserModel->getDispenserDataAll($id);
$widget_pages_data = $DispenserModel->getWidgetData($id);
$get_pages = $DispenserModel->getPages();
$edit_widget_pages_data = $DispenserModel->getWidgetEditData($edit_widget_id);

if(isset($widget_data[0]->folder_location)){
  $load_info_file = CUSTOMDIR."widgets/".$widget_data[0]->folder_location."/info.xml";
  if(file_exists($load_info_file)){
    /** Get list of Downloaded Widgets **/
    $xml[]=simplexml_load_file($load_info_file);
  }
}

/** Get data for dashboard */
$data['current_page'] = $_SERVER['REQUEST_URI'];
$data['title'] = "Widget Settings - ".$widget_data[0]->name;

/** Setup Token for Form */
$data['csrfToken'] = Csrf::makeToken('settings');

/** Setup Breadcrumbs */
$data['breadcrumbs'] = "<li class='breadcrumb-item'><a href='".SITE_URL."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li><li class='breadcrumb-item'><a href='".SITE_URL."AdminPanel-Dispenser/Widgets'><i class='fa fa-fw fa-cog'></i> Dispenser Widgets</a></li><li class='breadcrumb-item active'><i class='fa fa-fw fa-cog'></i> ".$data['title']."</li>";

?>
<style>
.fit-image{
width: 250px;
object-fit: cover;
height: 250px; /* only if you want fixed height */
}
</style>
<div class='col-lg-12 col-md-12 col-sm-12'>
	<div class='card mb-3'>
		<div class='card-header h4'>
			Widget Data From Downloaded Files
      <?php echo PageFunctions::displayPopover('Widget Data From Downloaded Files', 'Displays data from the info.xml file within the widget\'s folder.', false, 'btn btn-sm btn-light'); ?>
		</div>
		<div class='card-body'>
      <?php
        if(isset($xml)){
          foreach ($xml as $xmldata) {
            /** Get Widget Data from Database if installed **/
            $widget_data = $DispenserModel->getDispenserByName($xmldata->FOLDER_LOCATION, $xmldata->TYPE);
            if(!empty($widget_data)){
              if($widget_data[0]->enable == "true"){$widget_enable = " - <font color='green'>Enabled</font>";}else{$widget_enable = " - <font color='red'>Disabled</font>";}
              $widget_status = '<font color="green">Installed</font> '.$widget_enable;
              if($xmldata->VERSION > $widget_data[0]->version){
                $widget_update = " - <font color='red'>Update Available</font>";
                $widget_update_btn = " <a href='".SITE_URL."AdminPanel-Dispenser-Widgets/Update/{$xmldata->FOLDER_LOCATION}/' class='btn btn-warning btn-sm'>Update</a> ";
              }else{
                $widget_update = "";
                $widget_update_btn = "";
              }
              $widget_installed = "true";
            }else{
              $widget_status = '<font color="red">Downloaded but Not Installed</font>';
              $widget_installed = "false";
            }
            echo "<div class='card mb-3 border-dark'>";
              echo "<div class='card-header h4'>";
                echo "{$xmldata->TITLE}";
              echo "</div>";
              echo "<div class='row no-gutters'>";
                echo "<div class='col-auto border-right'>";
                  echo "<img src='{$xmldata->IMAGE}' class='img-responsive fit-image' alt=''>";
                echo "</div>";
                echo "<div class='col'>";
                    echo "<div class='card-block px-2'>";
                        echo "<p class='card-text'>{$xmldata->DESCRIPTION}</p>";
                        echo "<p class='card-text'>";
                        echo "Author: {$xmldata->AUTHOR} <Br>";
                        echo "Version: {$xmldata->VERSION} <br>";
                        echo "Release Date: {$xmldata->RELEASE_DATE}<br>";
                        echo "Status: $widget_status";
                        echo "</p>";
                    echo "</div>";
                echo "</div>";
              echo "</div>";
            echo "</div>";
          }
        }else{
          echo "<font color='red'>Error Reading Widget info.xml file.</font><Br><br>";
        }
      ?>
    </div>
  </div>
</div>
<div class='col-lg-12 col-md-12 col-sm-12'>
	<div class='card mb-3'>
		<div class='card-header h4'>
			Widget Settings
      <?php echo PageFunctions::displayPopover('Widget Settings', 'Admin Change settings for UserCandy Widgets.', false, 'btn btn-sm btn-light'); ?>
		</div>
		<div class='card-body'>
      <?php echo Form::open(array('method' => 'post')); ?>

			<!-- Widget Name -->
			<div class='input-group mb-3' style='margin-bottom: 25px'>
        <div class="input-group-prepend">
				  <span class='input-group-text'><i class='fa fa-fw fa-globe'></i> Widget Name</span>
        </div>
				<?php echo Form::input(array('type' => 'text', 'name' => 'name', 'class' => 'form-control', 'value' => $widget_data[0]->name, 'placeholder' => 'Widget Name', 'maxlength' => '255')); ?>
        <?php echo PageFunctions::displayPopover('Widget Name', 'Use Caution when editing this data.  It may break things.', true, 'input-group-text'); ?>
			</div>

      <!-- Widget Type -->
      <div class='input-group mb-3'>
        <div class='input-group-prepend'>
          <span class='input-group-text' id='basic-addon1'><i class='fa fa-fw fa-globe'></i> Widget Type</span>
        </div>
        <select class='form-control' id='type' name='type'>
          <option value='widget' <?php if($widget_data[0]->type == "widget"){echo "SELECTED";}?> >Widget</option>
          <option value='plugin' <?php if($widget_data[0]->type == "plugin"){echo "SELECTED";}?> >Plug-In</option>
          <option value='theme' <?php if($widget_data[0]->type == "theme"){echo "SELECTED";}?> >Theme</option>
          <option value='helper' <?php if($widget_data[0]->type == "helper"){echo "SELECTED";}?> >Helper</option>
        </select>
        <?php echo PageFunctions::displayPopover('Widget Name', 'Use Caution when editing this data.  It may break things.', true, 'input-group-text'); ?>
      </div>

      <!-- Widget Folder -->
			<div class='input-group mb-3' style='margin-bottom: 25px'>
        <div class="input-group-prepend">
				  <span class='input-group-text'><i class='fa fa-fw fa-globe'></i> Widget Folder</span>
        </div>
				<?php echo Form::input(array('type' => 'text', 'name' => 'folder_location', 'class' => 'form-control', 'value' => $widget_data[0]->folder_location, 'placeholder' => 'Widget Folder', 'maxlength' => '255')); ?>
        <?php echo PageFunctions::displayPopover('Widget Name', 'Use Caution when editing this data.  It may break things.', true, 'input-group-text'); ?>
			</div>

      <!-- Widget Version -->
			<div class='input-group mb-3' style='margin-bottom: 25px'>
        <div class="input-group-prepend">
				  <span class='input-group-text'><i class='fa fa-fw fa-globe'></i> Widget Version</span>
        </div>
				<?php echo Form::input(array('type' => 'text', 'name' => 'version', 'class' => 'form-control', 'value' => $widget_data[0]->version, 'placeholder' => 'Widget Version', 'maxlength' => '255')); ?>
        <?php echo PageFunctions::displayPopover('Widget Name', 'Use Caution when editing this data.  It may break things.', true, 'input-group-text'); ?>
			</div>

      <!-- Widget Type -->
      <div class='input-group mb-3'>
        <div class='input-group-prepend'>
          <span class='input-group-text' id='basic-addon1'><i class='fa fa-fw fa-globe'></i> Widget Enable</span>
        </div>
        <select class='form-control' id='enable' name='enable'>
          <option value='true' <?php if($widget_data[0]->enable == "true"){echo "SELECTED";}?> >Enable</option>
          <option value='false' <?php if($widget_data[0]->enable == "false"){echo "SELECTED";}?> >Disable</option>
        </select>
        <?php echo PageFunctions::displayPopover('Widget Name', 'Use Caution when editing this data.  It may break things.', true, 'input-group-text'); ?>
      </div>

      <button class="btn btn-md btn-success" name="submit" type="submit">
          Update Dispenser Settings
      </button>
      <!-- CSRF Token and What is Being Updated -->
      <input type="hidden" name="token_settings" value="<?php echo $data['csrfToken']; ?>" />
      <input type="hidden" name="update_settings" value="true" />
      <?php echo Form::close(); ?>

    </div>
  </div>
</div>
<div class='col-lg-12 col-md-12 col-sm-12'>
	<div class='card mb-3'>
		<div class='card-header h4'>
			Widget Display Pages
      <?php echo PageFunctions::displayPopover('Widget Display Pages', 'Add or remove which pages this widgit is displayed on, and edit where the widget is displayed on each page.', false, 'btn btn-sm btn-light'); ?>
		</div>
    <?php if(isset($widget_pages_data)){ ?>
    <table class='table table-hover responsive'>
      <tr>
        <th>Page Name</th><th>Type</th><th>Location</th><th></th>
      </tr>
      <?php foreach($widget_pages_data as $row) { ?>
      <tr>
        <td><?php echo $DispenserModel->getPageName($row->page_id); ?></td>
        <td><?=$row->display_type ?></td>
        <td><?=$row->display_location ?></td>
        <td align='right'><a href='<?=SITE_URL?>AdminPanel-Dispenser-Widgets-Settings/<?=$id ?>/<?=$row->id ?>/#dispenser_widget<?=$row->id ?>' class='btn btn-sm btn-success'><span class='fas fa-edit'></span></a></td>
      </tr>
      <?php } ?>
    </table>
  <?php } ?>
		<div class='card-body'>
      <hr>
      <h4><?php if(!empty($edit_widget_pages_data)){echo "Update Widget Setting (ID:{$edit_widget_pages_data[0]->id})";}else{echo "Create New Widget Setting";} ?></h4><Br>

      <?php echo Form::open(array('method' => 'post', 'style' => 'display:inline')); ?>

      <!-- Widget Page ID -->
      <div class='input-group mb-3'>
        <div class='input-group-prepend'>
          <span class='input-group-text' id='basic-addon1'><i class='fa fa-fw fa-globe'></i> Display on Page</span>
        </div>
        <select class='form-control' id='page_id' name='page_id'>
          <?php
          if(isset($get_pages)){
            foreach ($get_pages as $value) {
              if($edit_widget_pages_data[0]->page_id == $value->id){$selected = "SELECTED";}else{$selected = "";}
              echo "<option value='{$value->id}' $selected >{$value->url}</option>";
            }
          }
          ?>
        </select>
        <?php echo PageFunctions::displayPopover('Display on Page', 'Select the Page that this Widget will display on.', true, 'input-group-text'); ?>
      </div>

      <!-- Widget Display Type -->
      <div class='input-group mb-3'>
        <div class='input-group-prepend'>
          <span class='input-group-text' id='basic-addon1'><i class='fa fa-fw fa-globe'></i> Widget Display Type</span>
        </div>
        <select class='form-control' id='display_type' name='display_type'>
          <option value='sidebar' <?php if($edit_widget_pages_data[0]->display_type == "sidebar"){echo "SELECTED";}?> >Sidebar</option>
        </select>
        <?php echo PageFunctions::displayPopover('Widget Display Type', 'Select the Widget Display Type so the site knows how to place the widger in the site.', true, 'input-group-text'); ?>
      </div>

      <!-- Widget Display Type -->
      <a id='dispenser_widget<?=$edit_widget_pages_data[0]->id ?>'></a>
      <div class='input-group mb-3'>
        <div class='input-group-prepend'>
          <span class='input-group-text' id='basic-addon1'><i class='fa fa-fw fa-globe'></i> Widget Display Type</span>
        </div>
        <select class='form-control' id='display_location' name='display_location'>
          <option value='sidebar_right' <?php if($edit_widget_pages_data[0]->display_location == "sidebar_right"){echo "SELECTED";}?> >Sidebar Right</option>
          <option value='sidebar_left' <?php if($edit_widget_pages_data[0]->display_location == "sidebar_left"){echo "SELECTED";}?> >Sidebar Left</option>
        </select>
        <?php echo PageFunctions::displayPopover('Widget Display Type', 'Select the Widget Display Type so the site knows how to place the widger in the site.', true, 'input-group-text'); ?>
      </div>

      <button class="btn btn-md btn-success" name="submit" type="submit">
          <?php if(!empty($edit_widget_pages_data)){echo "Update Widget Setting (ID:{$edit_widget_pages_data[0]->id})";}else{echo "Add Widget Display Setting";} ?>
      </button>
      <!-- CSRF Token and What is Being Updated -->
      <input type="hidden" name="token_settings" value="<?php echo $data['csrfToken']; ?>" />
      <?php
        if(!empty($edit_widget_pages_data)){
          echo "<input type='hidden' name='update_widget' value='true' />";
        }else{
          echo "<input type='hidden' name='insert_widget' value='true' />";
        }
      ?>
      <?php echo Form::close(); ?>

      <?php
        if(!empty($edit_widget_pages_data)){
          echo "<a href='#DeleteModal' class='btn btn-sm btn-danger trigger-btn float-right' data-toggle='modal'>Delete (ID:{$edit_widget_pages_data[0]->id})</a>";

          echo "
            <div class='modal fade' id='DeleteModal' tabindex='-1' role='dialog' aria-labelledby='DeleteLabel' aria-hidden='true'>
              <div class='modal-dialog' role='document'>
                <div class='modal-content'>
                  <div class='modal-header'>
                    <h5 class='modal-title' id='DeleteLabel'>Delete Widget Setting?</h5>
                    <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                      <span aria-hidden='true'>&times;</span>
                    </button>
                  </div>
                  <div class='modal-body'>
                    Do you want to delete this Widget Setting?<br><br>
                    ID:".$edit_widget_pages_data[0]->id."
                  </div>
                  <div class='modal-footer'>
                    <button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>
                    ";
                    echo Form::open(array('method' => 'post', 'class' => 'float-right', 'style' => 'display:inline'));
                    echo "<input type='hidden' name='token_settings' value='".$data['csrfToken']."'>";
                    echo "<input type='hidden' name='delete_widget' value='true' />";
                    echo "<input type='hidden' name='delete_id' value='".$edit_widget_pages_data[0]->id."'>";
                    echo "<button class='btn btn-md btn-danger' name='submit' type='submit'>Delete Widget Setting</button>";
                    echo Form::close();
                    echo "
                  </div>
                </div>
              </div>
            </div>
          ";
        }
      ?>

    </div>
	</div>
</div>
