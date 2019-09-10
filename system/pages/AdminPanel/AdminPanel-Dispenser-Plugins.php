<?php
/**
* Admin Panel Dispenser Plugins Settings Page
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

/** Get data from URL **/
(empty($viewVars[0])) ? $action = null : $action = $viewVars[0];
(empty($viewVars[1])) ? $folder = null : $folder = $viewVars[1];
(empty($viewVars[2])) ? $type = null : $type = $viewVars[2];

/** Load Models **/
$AdminPanelModel = new AdminPanelModel();
$DispenserModel = new DispenserModel();
$pages = new Paginator(USERS_PAGEINATOR_LIMIT);  // How many rows per page

/** Check to see if Admin is installing or updating a Plugin **/
if($action == "Install" && !empty($folder)){
  $load_install_file = CUSTOMDIR."plugins/$folder/install.php";
  if(file_exists($load_install_file)){
    require($load_install_file);
    /** Get Data from info.xml file for DB **/
    $load_info_file_i = CUSTOMDIR."plugins/$folder/info.xml";
    if(file_exists($load_info_file_i)){
      /** Get list of Downloaded Plugins **/
      $xmlinstall=simplexml_load_file($load_info_file_i);
    }
    /** Insert Plugin to Dispenser Database **/
    if($DispenserModel->insertDispenser($xmlinstall->NAME, $xmlinstall->TYPE, $xmlinstall->FOLDER_LOCATION, $xmlinstall->VERSION)){
      /** Send data to the Database **/
      if(isset($install_db_data)){
        if($db_update_status = $DispenserModel->updateDatabase($install_db_data)){
            $install_status = 'Success';
        }
      }else{
        $install_status = 'Success';
      }
    }
  }
  if($install_status == 'Success'){
    /** Success */
    SuccessMessages::push('You Have Successfully Installed a Plugin' , 'AdminPanel-Dispenser-Plugins');
  }else{
    /** Success */
    ErrorMessages::push('There was an Error Installing a Plugin', 'AdminPanel-Dispenser-Plugins');
  }
}else if($action == "Update" && !empty($folder)){
  $load_update_file = CUSTOMDIR."plugins/$folder/update.php";
  if(file_exists($load_update_file)){
    /** Get Data from info.xml file for DB **/
    $load_info_file_u = CUSTOMDIR."plugins/$folder/info.xml";
    if(file_exists($load_info_file_u)){
      /** Get list of Downloaded Plugins **/
      $xmlupdate=simplexml_load_file($load_info_file_u);
    }
    /** Get Plugin DB Data **/
    $dispenser_db_data = $DispenserModel->getDispenserByName($folder, 'plugin');
    /** Include Update File **/
    require($load_update_file);
    /** Check to make sure there is an update **/
    if($xmlupdate->VERSION > $widget_db_data[0]->version){
      /** No DB Changes for this Widget - Update Version in DB only **/
      if($DispenserModel->updateDispenserVersion($widget_db_data[0]->id, $xmlupdate->VERSION)){
        /** Send data to the Database **/
        if(isset($install_db_data)){
          if($db_update_status = $DispenserModel->updateDatabase($install_db_data)){
              $install_status = 'Success';
          }
        }else{
          $install_status = 'Success';
        }
      }
    }
  }
  if($update_status == 'Success'){
    /** Success */
    SuccessMessages::push('You Have Successfully Updated a Plugin', 'AdminPanel-Dispenser-Plugins');
  }else{
    /** Success */
    ErrorMessages::push('There was an Error Updating a Plugin', 'AdminPanel-Dispenser-Plugins');
  }
}else if($action == "Disable" && !empty($folder)){
  $load_update_file = CUSTOMDIR."plugins/$folder/update.php";
  if(file_exists($load_update_file)){
    /** Get Data from info.xml file for DB **/
    $load_info_file_u = CUSTOMDIR."plugins/$folder/info.xml";
    if(file_exists($load_info_file_u)){
      /** Get Plugin DB Data **/
      $dispenser_db_data = $DispenserModel->getDispenserByName($folder, 'plugin');
      /** Disable Site Plugin in DB **/
      if($DispenserModel->updateDispenserEnable($dispenser_db_data[0]->id, 'false')){
        $update_status = "Success";
      }
    }
  }
  if($update_status == 'Success'){
    /** Success */
    SuccessMessages::push('You Have Successfully Disabled a Plugin', 'AdminPanel-Dispenser-Plugins');
  }else{
    /** Success */
    ErrorMessages::push('There was an Error Disabled a Plugin', 'AdminPanel-Dispenser-Plugins');
  }
}else if($action == "Enable" && !empty($folder)){
  $load_update_file = CUSTOMDIR."plugins/$folder/update.php";
  if(file_exists($load_update_file)){
    /** Get Data from info.xml file for DB **/
    $load_info_file_u = CUSTOMDIR."plugins/$folder/info.xml";
    if(file_exists($load_info_file_u)){
      /** Get Plugin DB Data **/
      $dispenser_db_data = $DispenserModel->getDispenserByName($folder, 'plugin');
      /** Disable Site Plugin in DB **/
      if($DispenserModel->updateDispenserEnable($dispenser_db_data[0]->id, 'true')){
        $update_status = "Success";
      }
    }
  }
  if($update_status == 'Success'){
    /** Success */
    SuccessMessages::push('You Have Successfully Enabled a Plugin', 'AdminPanel-Dispenser-Plugins');
  }else{
    /** Success */
    ErrorMessages::push('There was an Error Enabled a Plugin', 'AdminPanel-Dispenser-Plugins');
  }
}else if($action == "Download" && !empty($folder) && !empty($type)){
  $Dispenser = new Dispenser();

  if($filedata = Dispenser::downloadFromDispensary($folder, $type)){
    $download_status = $filedata;
  }else{
    $download_status = false;
  }
  if($download_status == 'Success'){
    /** Success */
    SuccessMessages::push('You Have Successfully Downloaded a Plugin', 'AdminPanel-Dispenser-Plugins');
  }else{
    /** Success */
    ErrorMessages::push('There was an Error Downloading a Plugin', 'AdminPanel-Dispenser-Plugins');
  }
}

/** Get data for dashboard */
$data['current_page'] = $_SERVER['REQUEST_URI'];
$data['title'] = "Dispenser Plugins";

/** Get Settings Data */
$dispenser_api_key = $AdminPanelModel->getSettings('dispenser_api_key');

/** Connect to UserCandy Dispensary **/
$get_dd = $Dispenser->getDataFromDispensary($dispenser_api_key, 'plugin');

$scan_dir = CUSTOMDIR.'plugins';
$get_dirs = array_filter(glob($scan_dir.'/*'), 'is_dir');

if(isset($get_dirs)){
  foreach ($get_dirs as $dir) {
    $use_dir = explode('/', $dir);
    $use_dir = array_values(array_slice($use_dir, -1))[0];
    $load_info_file = CUSTOMDIR."plugins/$use_dir/info.xml";
    if(file_exists($load_info_file)){
      /** Get list of Downloaded Plugins **/
      $xml[]=simplexml_load_file($load_info_file);
    }
  }
}

/** Setup Breadcrumbs */
$data['breadcrumbs'] = "<li class='breadcrumb-item'><a href='".SITE_URL."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li><li class='breadcrumb-item active'><i class='fa fa-fw fa-cog'></i> ".$data['title']."</li>";

?>
<style>
.fit-image{
width: 250px;
object-fit: cover;
height: 250px; /* only if you want fixed height */
}
</style>

<!-- Local Files -->
<div class='col-lg-12 col-md-12 col-sm-12'>
  <div class='row'>
    <div class='col-lg-12 col-md-12 col-sm-12'>
    	<div class='card mb-3'>
    		<div class='card-header h4'>
    			<?php echo $data['title'];  ?>
          <?php echo PageFunctions::displayPopover('Dispenser Plugins', 'Displays list of UserCandy Plugins.', false, 'btn btn-sm btn-light'); ?>
    		</div>
    		<div class='card-body'>
    			<h4>Downloaded Plugins</h4>
          <?php
            if(isset($xml)){
              foreach ($xml as $xmldata) {
                /** Get Plugin Data from Database if installed **/
                $plugin_data = $DispenserModel->getDispenserByName($xmldata->FOLDER_LOCATION, $xmldata->TYPE);
                if(!empty($plugin_data)){
                  if($plugin_data[0]->enable == "true"){$plugin_enable = " - <font color='green'>Enabled</font>";}else{$plugin_enable = " - <font color='red'>Disabled</font>";}
                  $plugin_status = '<font color="green">Installed</font> '.$plugin_enable;
                  if($xmldata->VERSION > $plugin_data[0]->version){
                    $plugin_update = " - <font color='red'>Update Available</font>";
                    $plugin_update_btn = " <a href='".SITE_URL."AdminPanel-Dispenser-Plugins/Update/{$xmldata->FOLDER_LOCATION}/' class='btn btn-warning btn-sm'>Update</a> ";
                  }else{
                    $plugin_update = "";
                    $plugin_update_btn = "";
                  }
                  $plugin_installed = "true";
                }else{
                  $plugin_status = '<font color="red">Downloaded but Not Installed</font>';
                  $plugin_installed = "false";
                }
                echo "<div class='card mb-3 border-dark'>";
                  echo "<div class='card-header h4'>";
                    echo "{$xmldata->TITLE}";
                  echo "</div>";
                  echo "<div class='row no-gutters'>";
                    echo "<div class='col-auto'>";
                      echo "<img src='{$xmldata->IMAGE}' class='img-responsive fit-image' alt=''>";
                    echo "</div>";
                    echo "<div class='col'>";
                        echo "<div class='card-block px-2'>";
                            echo "<p class='card-text'>{$xmldata->DESCRIPTION}</p>";
                            echo "<p class='card-text'>";
                            echo "Author: {$xmldata->AUTHOR} <Br>";
                            if($xmldata->FOLDER_LOCATION != 'default'){
                              echo "Files Version: {$xmldata->VERSION} <br>";
                              echo "Release Date: {$xmldata->RELEASE_DATE}<br>";
                              if($plugin_installed == "true"){ echo "Installed Version: {$plugin_data[0]->version} $plugin_update<Br>"; }
                              echo "Status: $plugin_status";
                              echo "</p>";
                              if($plugin_installed == "true"){
                                if($plugin_data[0]->enable == 'true'){
                                  echo "<a href='".SITE_URL."AdminPanel-Dispenser-Plugins/Disable/{$xmldata->FOLDER_LOCATION}/' class='btn btn-warning btn-sm'>Disable</a>";
                                }else{
                                  echo "<a href='".SITE_URL."AdminPanel-Dispenser-Plugins/Enable/{$xmldata->FOLDER_LOCATION}/' class='btn btn-primary btn-sm'>Enable</a>";
                                }
                                echo "$plugin_update_btn";
                              }else{
                                echo "<a href='".SITE_URL."AdminPanel-Dispenser-Plugins/Install/{$xmldata->FOLDER_LOCATION}/' class='btn btn-success btn-sm'>Install</a>";
                              }
                            }
                        echo "</div>";
                    echo "</div>";
                  echo "</div>";
                echo "</div>";
              }
            }
          ?>

        </div>
    	</div>
    </div>


  </div>
</div>

<!-- Remote Files -->
<div class='col-lg-12 col-md-12 col-sm-12'>
  <div class='row'>
    <div class='col-lg-12 col-md-12 col-sm-12'>
    	<div class='card mb-3'>
    		<div class='card-header h4'>
    			UserCandy Dispensary Plugins
          <?php echo PageFunctions::displayPopover('UserCandy Dispensary Plugins', 'Displays list of UserCandy Plugins from UserCandy.com Dispensary.', false, 'btn btn-sm btn-light'); ?>
    		</div>
    		<div class='card-body'>
    			<h4>Available Plugins</h4>

          <?php
            if($get_dd){
              foreach ($get_dd as $dd_data) {
                /** Check to see if the plugin is already downloaded **/
                $check_if_downloaded = CUSTOMDIR."plugins/{$dd_data['folder_location']}/info.xml";
                if(file_exists($check_if_downloaded)){
                  /** Get list of Downloaded Plugins **/
                  $xmldownloaded=simplexml_load_file($check_if_downloaded);
                }else{
                  $xmldownloaded = false;
                  /** Get Plugin Data from Database if installed **/
                  $plugin_data = $DispenserModel->getDispenserByName($dd_data['folder_location'], $dd_data['type']);
                  if(!empty($plugin_data)){
                    if($plugin_data[0]->enable == "true"){$plugin_enable = " - <font color='green'>Enabled</font>";}else{$plugin_enable = " - <font color='red'>Disabled</font>";}
                    $plugin_status = '<font color="green">Installed</font> '.$plugin_enable;
                    if($dd_data['version'] > $plugin_data[0]->version){
                      $plugin_update = " - <font color='red'>Update Available</font>";
                      $plugin_update_btn = " <a href='".SITE_URL."AdminPanel-Dispenser-Plugins/Update/{$dd_data['folder_location']}/' class='btn btn-warning btn-sm'>Update</a> ";
                    }else{
                      $plugin_update = "";
                      $plugin_update_btn = "";
                    }
                    $plugin_installed = "true";
                  }else{
                    if($xmldownloaded){
                      $plugin_status = '<font color="red">Downloaded but Not Installed</font>';
                      $plugin_installed = "false";
                    }else{
                      $plugin_status = '<font color="red">Available for Download</font>';
                      $plugin_installed = "false";
                    }
                  }
                  echo "<div class='card mb-3 border-dark'>";
                    echo "<div class='card-header h4'>";
                      echo "{$dd_data['title']}";
                    echo "</div>";
                    echo "<div class='row no-gutters'>";
                      echo "<div class='col-auto'>";
                        echo "<img src='{$dd_data['image']}' class='img-responsive fit-image' alt=''>";
                      echo "</div>";
                      echo "<div class='col'>";
                          echo "<div class='card-block px-2'>";
                              echo "<p class='card-text'>{$dd_data['description']}</p>";
                              echo "<p class='card-text'>";
                              echo "Author: {$dd_data['author']} <Br>";
                              if($dd_data['folder_location'] != 'default'){
                                echo "Files Version: {$dd_data['version']} <br>";
                                echo "Release Date: {$dd_data['release_date']}<br>";
                                if($plugin_installed == "true"){ echo "Installed Version: {$plugin_data[0]->version} $plugin_update<Br>"; }
                                echo "Status: $plugin_status";
                                echo "</p>";
                                if($plugin_installed == "true"){
                                  if($plugin_data[0]->enable == 'true'){
                                    echo "<a href='".SITE_URL."AdminPanel-Dispenser-Plugins/Disable/{$dd_data['folder_location']}/' class='btn btn-warning btn-sm'>Disable</a>";
                                  }else{
                                    echo "<a href='".SITE_URL."AdminPanel-Dispenser-Plugins/Enable/{$dd_data['folder_location']}/' class='btn btn-primary btn-sm'>Enable</a>";
                                  }
                                  echo "$plugin_update_btn";
                                }else{
                                  if($xmldownloaded){
                                    echo "<a href='".SITE_URL."AdminPanel-Dispenser-Plugins/Install/{$dd_data['folder_location']}/' class='btn btn-success btn-sm'>Install</a>";
                                  }else{
                                    echo "<a href='".SITE_URL."AdminPanel-Dispenser-Plugins/Download/{$dd_data['folder_location']}/{$dd_data['type']}/' class='btn btn-info btn-sm'>Download</a>";
                                  }
                                }
                              }
                          echo "</div>";
                      echo "</div>";
                    echo "</div>";
                  echo "</div>";
                }
              }
            }else{
              echo "No Items Available or There is a Connection Issue.<br>";
              echo "<a href='".SITE_URL."AdminPanel-Dispenser-Settings/' class='btn btn-warning btn-sm'>Check Connections Settings</a>";
            }
          ?>

        </div>
    	</div>
    </div>


  </div>
</div>
