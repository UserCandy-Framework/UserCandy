<?php
/**
* Admin Panel Dispenser Helpers Settings Page
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

/** Check to see if Admin is installing or updating a Helper **/
if($action == "Install" && !empty($folder)){
  $load_install_file = CUSTOMDIR."helpers/$folder/install.php";
  if(file_exists($load_install_file)){
    require($load_install_file);
    /** Get Data from info.xml file for DB **/
    $load_info_file_i = CUSTOMDIR."helpers/$folder/info.xml";
    if(file_exists($load_info_file_i)){
      /** Get list of Downloaded Helpers **/
      $xmlinstall=simplexml_load_file($load_info_file_i);
    }
    /** Insert Helper to Dispenser Database **/
    if($DispenserModel->insertDispenser($xmlinstall->NAME, $xmlinstall->TYPE, $xmlinstall->FOLDER_LOCATION, $xmlinstall->VERSION)){
      $install_status = 'Success';
    }
  }
  if($install_status == 'Success'){
    /** Success */
    SuccessMessages::push('You Have Successfully Installed a Helper', 'AdminPanel-Dispenser-Helpers');
  }else{
    /** Success */
    ErrorMessages::push('There was an Error Installing a Helper', 'AdminPanel-Dispenser-Helpers');
  }
}else if($action == "Update" && !empty($folder)){
  $load_update_file = CUSTOMDIR."helpers/$folder/update.php";
  if(file_exists($load_update_file)){
    /** Get Data from info.xml file for DB **/
    $load_info_file_u = CUSTOMDIR."helpers/$folder/info.xml";
    if(file_exists($load_info_file_u)){
      /** Get list of Downloaded Helpers **/
      $xmlupdate=simplexml_load_file($load_info_file_u);
    }
    /** Get Helper DB Data **/
    $dispenser_db_data = $DispenserModel->getDispenserByName($folder, 'helper');
    /** Include Update File **/
    require($load_update_file);
  }
  if($update_status == 'Success'){
    /** Success */
    SuccessMessages::push('You Have Successfully Updated a Helper', 'AdminPanel-Dispenser-Helpers');
  }else{
    /** Success */
    ErrorMessages::push('There was an Error Updating a Helper', 'AdminPanel-Dispenser-Helpers');
  }
}else if($action == "Disable" && !empty($folder)){
  $load_update_file = CUSTOMDIR."helpers/$folder/update.php";
  if(file_exists($load_update_file)){
    /** Get Data from info.xml file for DB **/
    $load_info_file_u = CUSTOMDIR."helpers/$folder/info.xml";
    if(file_exists($load_info_file_u)){
      /** Get Helper DB Data **/
      $dispenser_db_data = $DispenserModel->getDispenserByName($folder, 'helper');
      /** Disable Site Helper in DB **/
      if($DispenserModel->updateDispenserEnable($dispenser_db_data[0]->id, 'false')){
        $update_status = "Success";
      }
    }
  }
  if($update_status == 'Success'){
    /** Success */
    SuccessMessages::push('You Have Successfully Disabled a Helper', 'AdminPanel-Dispenser-Helpers');
  }else{
    /** Success */
    ErrorMessages::push('There was an Error Disabled a Helper', 'AdminPanel-Dispenser-Helpers');
  }
}else if($action == "Enable" && !empty($folder)){
  $load_update_file = CUSTOMDIR."helpers/$folder/update.php";
  if(file_exists($load_update_file)){
    /** Get Data from info.xml file for DB **/
    $load_info_file_u = CUSTOMDIR."helpers/$folder/info.xml";
    if(file_exists($load_info_file_u)){
      /** Get Helper DB Data **/
      $dispenser_db_data = $DispenserModel->getDispenserByName($folder, 'helper');
      /** Disable Site Helper in DB **/
      if($DispenserModel->updateDispenserEnable($dispenser_db_data[0]->id, 'true')){
        $update_status = "Success";
      }
    }
  }
  if($update_status == 'Success'){
    /** Success */
    SuccessMessages::push('You Have Successfully Enabled a Helper', 'AdminPanel-Dispenser-Helpers');
  }else{
    /** Success */
    ErrorMessages::push('There was an Error Enabled a Helper', 'AdminPanel-Dispenser-Helpers');
  }
}else if($action == "Download" && !empty($folder) && !empty($type)){
  $Dispenser = new Dispenser();
  /** Get Settings Data */
  $dispenser_api_key = $AdminPanelModel->getSettings('dispenser_api_key');

  if($filedata = Dispenser::downloadFromDispensary($dispenser_api_key, $folder, $type)){
    $download_status = $filedata;
  }else{
    $download_status = false;
  }
  if($download_status == 'Success'){
    /** Success */
    SuccessMessages::push('You Have Successfully Downloaded a Helper', 'AdminPanel-Dispenser-Helpers');
  }else{
    /** Success */
    ErrorMessages::push('There was an Error Downloading a Helper', 'AdminPanel-Dispenser-Helpers');
  }
}

/** Get data for dashboard */
$data['current_page'] = $_SERVER['REQUEST_URI'];
$data['title'] = "Dispenser Helpers";

/** Get Settings Data */
$dispenser_api_key = $AdminPanelModel->getSettings('dispenser_api_key');

/** Connect to UserCandy Dispensary **/
$get_dd = $Dispenser->getDataFromDispensary($dispenser_api_key, 'helper');

$scan_dir = CUSTOMDIR.'helpers';
$get_dirs = array_filter(glob($scan_dir.'/*'), 'is_dir');

if(isset($get_dirs)){
  foreach ($get_dirs as $dir) {
    $use_dir = explode('/', $dir);
    $use_dir = array_values(array_slice($use_dir, -1))[0];
    $load_info_file = CUSTOMDIR."helpers/$use_dir/info.xml";
    if(file_exists($load_info_file)){
      /** Get list of Downloaded Helpers **/
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

<div class='col-lg-12 col-md-12 col-sm-12'>
  <div class='row'>
    <div class='col-lg-12 col-md-12 col-sm-12'>
    	<div class='card mb-3'>
    		<div class='card-header h4'>
    			<?php echo $data['title'];  ?>
          <?php echo PageFunctions::displayPopover('Dispenser Helpers', 'Displays list of UserCandy Helpers.', false, 'btn btn-sm btn-light'); ?>
    		</div>
    		<div class='card-body'>
    			<h4>Downloaded Helpers</h4>
          <?php
            if(isset($xml)){
              foreach ($xml as $xmldata) {
                /** Get Helper Data from Database if installed **/
                $helper_data = $DispenserModel->getDispenserByName($xmldata->FOLDER_LOCATION, $xmldata->TYPE);
                if(!empty($helper_data)){
                  if($helper_data[0]->enable == "true"){$helper_enable = " - <font color='green'>Enabled</font>";}else{$helper_enable = " - <font color='red'>Disabled</font>";}
                  $helper_status = '<font color="green">Installed</font> '.$helper_enable;
                  if($xmldata->VERSION > $helper_data[0]->version){
                    $helper_update = " - <font color='red'>Update Available</font>";
                    $helper_update_btn = " <a href='".SITE_URL."AdminPanel-Dispenser-Helpers/Update/{$xmldata->FOLDER_LOCATION}/' class='btn btn-warning btn-sm'>Update</a> ";
                  }else{
                    $helper_update = "";
                    $helper_update_btn = "";
                  }
                  $helper_installed = "true";
                }else{
                  $helper_status = '<font color="red">Downloaded but Not Installed</font>';
                  $helper_installed = "false";
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
                            if($xmldata->FOLDER_LOCATION != 'default'){
                              echo "Files Version: {$xmldata->VERSION} <br>";
                              echo "Release Date: {$xmldata->RELEASE_DATE}<br>";
                              if($helper_installed == "true"){ echo "Installed Version: {$helper_data[0]->version} $helper_update<Br>"; }
                              echo "Status: $helper_status";
                              echo "</p>";
                              if($helper_installed == "true"){
                                if($helper_data[0]->enable == 'true'){
                                  echo "<a href='".SITE_URL."AdminPanel-Dispenser-Helpers/Disable/{$xmldata->FOLDER_LOCATION}/' class='btn btn-warning btn-sm'>Disable</a>";
                                }else{
                                  echo "<a href='".SITE_URL."AdminPanel-Dispenser-Helpers/Enable/{$xmldata->FOLDER_LOCATION}/' class='btn btn-primary btn-sm'>Enable</a>";
                                }
                                echo "$helper_update_btn";
                              }else{
                                echo "<a href='".SITE_URL."AdminPanel-Dispenser-Helpers/Install/{$xmldata->FOLDER_LOCATION}/' class='btn btn-success btn-sm'>Install</a>";
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
    			UserCandy Dispensary Helpers
          <?php echo PageFunctions::displayPopover('UserCandy Dispensary Helpers', 'Displays list of UserCandy Helpers from UserCandy.com Dispensary.', false, 'btn btn-sm btn-light'); ?>
    		</div>
    		<div class='card-body'>
    			<h4>Available Helpers</h4>

          <?php
            if($get_dd){
              foreach ($get_dd as $dd_data) {
                /** Check to see if the helper is already downloaded **/
                $check_if_downloaded = CUSTOMDIR."helpers/{$dd_data['folder_location']}/info.xml";
                if(file_exists($check_if_downloaded)){
                  /** Get list of Downloaded Helpers **/
                  $xmldownloaded=simplexml_load_file($check_if_downloaded);
                }else{
                  $xmldownloaded = false;
                  /** Get Plugin Data from Database if installed **/
                  $helper_data = $DispenserModel->getDispenserByName($dd_data['folder_location'], $dd_data['type']);
                  if(!empty($helper_data)){
                    if($helper_data[0]->enable == "true"){$helper_enable = " - <font color='green'>Enabled</font>";}else{$helper_enable = " - <font color='red'>Disabled</font>";}
                    $helper_status = '<font color="green">Installed</font> '.$helper_enable;
                    if($dd_data['version'] > $helper_data[0]->version){
                      $helper_update = " - <font color='red'>Update Available</font>";
                      $helper_update_btn = " <a href='".SITE_URL."AdminPanel-Dispenser-Helpers/Update/{$dd_data['folder_location']}/' class='btn btn-warning btn-sm'>Update</a> ";
                    }else{
                      $helper_update = "";
                      $helper_update_btn = "";
                    }
                    $helper_installed = "true";
                  }else{
                    if($xmldownloaded){
                      $helper_status = '<font color="red">Downloaded but Not Installed</font>';
                      $helper_installed = "false";
                    }else{
                      $helper_status = '<font color="red">Available for Download</font>';
                      $helper_installed = "false";
                    }
                  }
                  echo "<div class='card mb-3 border-dark'>";
                    echo "<div class='card-header h4'>";
                      echo "{$dd_data['title']}";
                    echo "</div>";
                    echo "<div class='row no-gutters'>";
                      echo "<div class='col-auto border-right'>";
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
                                if($helper_installed == "true"){ echo "Installed Version: {$helper_data[0]->version} $helper_update<Br>"; }
                                echo "Status: $helper_status";
                                echo "</p>";
                                if($helper_installed == "true"){
                                  if($helper_data[0]->enable == 'true'){
                                    echo "<a href='".SITE_URL."AdminPanel-Dispenser-Helpers/Disable/{$dd_data['folder_location']}/' class='btn btn-warning btn-sm'>Disable</a>";
                                  }else{
                                    echo "<a href='".SITE_URL."AdminPanel-Dispenser-Helpers/Enable/{$dd_data['folder_location']}/' class='btn btn-primary btn-sm'>Enable</a>";
                                  }
                                  echo "$helper_update_btn";
                                }else{
                                  if($xmldownloaded){
                                    echo "<a href='".SITE_URL."AdminPanel-Dispenser-Helpers/Install/{$dd_data['folder_location']}/' class='btn btn-success btn-sm'>Install</a>";
                                  }else{
                                    echo "<a href='".SITE_URL."AdminPanel-Dispenser-Helpers/Download/{$dd_data['folder_location']}/{$dd_data['type']}/' class='btn btn-info btn-sm'>Download</a>";
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
