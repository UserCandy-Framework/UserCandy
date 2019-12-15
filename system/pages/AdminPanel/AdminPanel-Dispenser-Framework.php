<?php
/**
* Admin Panel Dispenser Framework Page
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

use Core\Dispenser;
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
(empty($viewVars[0])) ? $action = null : $action = $viewVars[0];
(empty($viewVars[1])) ? $folder = null : $folder = $viewVars[1];
(empty($viewVars[2])) ? $type = null : $type = $viewVars[2];

$page = "Framework";
$page_lowercase = strtolower($page);
$page_single = $page;
$page_single_lowercase = $page_lowercase;

/** Load Models **/
$AdminPanelModel = new AdminPanelModel();
$DispenserModel = new DispenserModel();
$pages = new Paginator(USERS_PAGEINATOR_LIMIT);  // How many rows per page

/** Check to see if Admin is installing or updating a Item **/
if($action == "Install" && !empty($folder)){
  /** Unzip the framework files to ROOTDIR **/
  $fw_zip = CUSTOMDIR.'framework/'.$folder.'.zip';
  if(file_exists($fw_zip)){
    Dispenser::updateFramework($fw_zip, $folder);
  }
  $load_install_file = ROOTDIR."install.php";
  if(file_exists($load_install_file)){
    require_once($load_install_file);
    /** Get Data from info.xml file for DB **/
    $load_info_file_i = ROOTDIR."info.xml";
    if(file_exists($load_info_file_i)){
      /** Get list of Downloaded Items **/
      $xmlinstall=simplexml_load_file($load_info_file_i);
    }
    /** Insert Item to Dispenser Database **/
    if($DispenserModel->insertDispenser($xmlinstall->NAME, $xmlinstall->TYPE, $xmlinstall->FOLDER_LOCATION, $xmlinstall->VERSION)){
      /** Send data to the Database **/
      if(isset($install_db_data)){
        if($db_install_status = $DispenserModel->updateDatabase($install_db_data)){
          /** Run Updates to Make sure Item is up to date **/
          $load_update_file = ROOTDIR."update.php";
          if(file_exists($load_update_file)){
            unset($install_db_data);
            require_once($load_update_file);
            /** Send data to the Database **/
            if(isset($install_db_data)){
              if($db_update_status = $DispenserModel->updateDatabase($install_db_data, '0.0.0', $xmlinstall->VERSION)){
    					  $install_status = 'Success';
              }
            }else{
              $install_status = 'Success';
            }
          }
        }
      }else{
        $install_status = 'Success';
      }
    }
  }
  /** Format Data for Success Message */
  if(!empty($db_install_status)){$db_install_status = implode(" ", $db_install_status);}else{$db_install_status = "";}
  if(!empty($db_update_status)){$db_update_status = implode(" ", $db_update_status);}else{$db_update_status = "";}
  if(!empty($new_pages)){$new_pages = implode(" ", $new_pages);}else{$new_pages = "";}
  /** Check to see if the install was successful **/
  if($install_status == 'Success'){
    /** Success */
    SuccessMessages::push('You Have Successfully Installed '.$page_single.'<Br><br>'.$db_install_status.$db_update_status.$new_pages , 'AdminPanel-Dispenser-Framework');
  }else{
    /** Success */
    ErrorMessages::push('There was an Error Installing '.$page_single.'<Br><br>'.$db_update_status.$new_pages, 'AdminPanel-Dispenser-Framework');
  }
}else if(($action == "Update") && !empty($folder)){
  /** Unzip the framework files to ROOTDIR **/
  $fw_zip = CUSTOMDIR.'framework/'.$folder.'.zip';
  if(file_exists($fw_zip)){
    Dispenser::updateFramework($fw_zip, $folder);
  }
  $load_update_file = ROOTDIR."update.php";
  if(file_exists($load_update_file)){

    /** Get Data from info.xml file for DB **/
    $load_info_file_u = ROOTDIR."info.xml";
    if(file_exists($load_info_file_u)){
      /** Get list of Downloaded Items **/
      $xmlupdate=simplexml_load_file($load_info_file_u);
    }

    /** Get Item DB Data **/
    $dispenser_db_data = $DispenserModel->getDispenserByName($folder, $page_single_lowercase);

    /** Check to make sure there is an update **/
    if($xmlupdate->VERSION > $dispenser_db_data[0]->version || empty($dispenser_db_data)){
      /** No DB Changes for this Widget - Update Version in DB only **/
      if($DispenserModel->updateDispenserVersion($dispenser_db_data[0]->id, $xmlupdate->VERSION)){
        if(file_exists($load_update_file)){

          /** Include Update File **/
          require_once($load_update_file);
          /** Send data to the Database **/
          if(isset($install_db_data)){
            if($db_update_status = $DispenserModel->updateDatabase($install_db_data, $dispenser_db_data[0]->version, $xmlupdate->VERSION)){
              $install_status = 'Success';
            }
          }else{
            $install_status = 'Success';
          }
        }else{
          $install_status = 'Success';
        }
      }
    }
  }
  /** Format Data for Success Message */
  if(!empty($db_update_status)){$db_update_status = implode(" ", $db_update_status);}
  /** Check to see if everything went well **/
  if($install_status == 'Success'){
    /** Success */
    SuccessMessages::push('You Have Successfully Updated '.$page_single.'<Br><br>'.$db_update_status, 'AdminPanel-Dispenser-Framework');
  }else{
    /** Success */
    ErrorMessages::push('There was an Error Updating '.$page_single.'<Br><br>'.$db_update_status, 'AdminPanel-Dispenser-Framework');
  }
}else if($action == "Download" && !empty($folder) && !empty($type)){
  /** Get Settings Data */
  $dispenser_api_key = $AdminPanelModel->getSettings('dispenser_api_key');

  if($filedata = Dispenser::downloadFrameworkFromDispensary($dispenser_api_key, $folder, $type)){
    $download_status = $filedata;
  }else{
    $download_status = false;
  }
  if($download_status == 'Success'){
    /** Success */
    SuccessMessages::push('You Have Successfully Downloaded '.$page_single, 'AdminPanel-Dispenser-Framework');
  }else{
    /** Success */
    ErrorMessages::push('There was an Error Downloading '.$page_single, 'AdminPanel-Dispenser-Framework');
  }
}

/** Get data for dashboard */
$data['current_page'] = $_SERVER['REQUEST_URI'];
$data['title'] = "Dispenser ".$page;

/** Get Settings Data */
$dispenser_api_key = $AdminPanelModel->getSettings('dispenser_api_key');

/** Connect to UserCandy Dispensary **/
$get_dd = $Dispenser->getDataFromDispensary($dispenser_api_key, $page_single_lowercase);

/** Check for all frameworks zip files and pull xml files. **/
$scan_dir = CUSTOMDIR.$page_lowercase;
$dir = $scan_dir.'/';
$files = array_diff(scandir($dir), array('.', '..'));
$folder_location = substr($dir, strrpos($dir, '/') + 1);
foreach ($files as $file) {
  $file_clean = str_replace(".zip", "", $file);
  $xml[] = Dispenser::read_zip_xml($folder_location, $file_clean);
}

/** Check if Theme **/
if($page == "Themes"){
  $site_theme = $AdminPanelModel->getSettings('site_theme');
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
          <?php echo PageFunctions::displayPopover('Dispenser '.$page, 'Displays list of UserCandy '.$page.'.', false, 'btn btn-sm btn-light'); ?>
    		</div>
    		<div class='card-body'>
    			<h4>Downloaded <?=$page?></h4>
          <?php
            if(isset($xml)){
              foreach ($xml as $xmldata) {
                /** Get Item Data from Database if installed **/
                $item_data = $DispenserModel->getDispenserByName($xmldata->FOLDER_LOCATION, $xmldata->TYPE);
                $item_dispensary_data = $Dispenser->getItemDataFromDispensary($dispenser_api_key, $xmldata->TYPE, $xmldata->FOLDER_LOCATION);
                $item_dispensary_version = $item_dispensary_data[0]['version'];
                if(!empty($item_data)){
                  if($item_data[0]->enable == "true"){$item_enable = " - <font color='green'>Enabled</font>";}else{$item_enable = " - <font color='red'>Disabled</font>";}
                  $item_status = '<font color="green">Installed</font> '.$item_enable;
                  $item_uninstall = "<a href='#UnInstallModal{$xmldata->FOLDER_LOCATION}{$xmldata->TYPE}' class='btn btn-sm btn-danger trigger-btn float-right' data-toggle='modal'>UnInstall</a>";
                  if($xmldata->VERSION > $item_data[0]->version){
                    $item_update = " - <font color='red'>Update Available</font>";
                    $item_update_btn = " <a href='".SITE_URL."AdminPanel-Dispenser-Framework/Update/{$xmldata->FOLDER_LOCATION}/' class='btn btn-info btn-sm float-right'>Update from version {$item_data[0]->version} to {$xmldata->VERSION}</a> ";
                  }else{
                    $item_update = "";
                    $item_update_btn = "";
                  }
                  $item_installed = "true";
                }else{
                  $item_status = '<font color="red">Downloaded but Not Installed</font>';
                  $item_installed = "false";
                  $item_uninstall = "";
                }
                if($item_dispensary_version > $xmldata->VERSION){
                  $item_update_download = "<a href='".SITE_URL."AdminPanel-Dispenser-Framework/Download/{$xmldata->FOLDER_LOCATION}/{$xmldata->TYPE}/' class='btn btn-info btn-sm float-right'>Download Latest Version ($item_dispensary_version)</a>";
                }else{
                  $item_update_download = "";
                }
                echo "<div class='card mb-3 border-dark'>";
                  echo "<div class='card-header h4'>";
                    echo "{$xmldata->TITLE}";
                    echo $item_uninstall;
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
                              if($item_installed == "true"){ echo "Installed Version: {$item_data[0]->version} $item_update<Br>"; }
                              echo "Status: $item_status";
                              echo "</p>";
                              if($item_installed == "true"){
                                echo "$item_update_btn";
                              }else{
                                echo "<a href='".SITE_URL."AdminPanel-Dispenser-Framework/Install/{$xmldata->FOLDER_LOCATION}/' class='btn btn-success btn-sm'>Install</a>";
                              }
                              echo $item_update_download;
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
    			UserCandy Dispensary <?=$page?>
          <?php echo PageFunctions::displayPopover('UserCandy Dispensary '.$page, 'Displays list of UserCandy '.$page.' from UserCandy.com Dispensary.', false, 'btn btn-sm btn-light'); ?>
    		</div>
    		<div class='card-body'>
    			<h4>Available <?=$page?></h4>

          <?php
            if($get_dd){
              foreach ($get_dd as $dd_data) {
                /** Check to see if the item is already downloaded **/
                $check_if_downloaded = CUSTOMDIR."$page_lowercase/{$dd_data['folder_location']}.zip";
                if(file_exists($check_if_downloaded)){
                  /** Get list of Downloaded Items **/
                  $xmldownloaded = true;
                }else{
                  $xmldownloaded = false;
                  /** Get Item Data from Database if installed **/
                  $item_data = $DispenserModel->getDispenserByName($dd_data['folder_location'], $dd_data['type']);
                  if(!empty($item_data)){
                    if($item_data[0]->enable == "true"){$item_enable = " - <font color='green'>Enabled</font>";}else{$item_enable = " - <font color='red'>Disabled</font>";}
                    $item_status = '<font color="green">Installed</font> '.$item_enable;
                    if($dd_data['version'] > $item_data[0]->version){
                      $item_update = " - <font color='red'>Update Available</font>";
                      $item_update_btn = " <a href='".SITE_URL."AdminPanel-Dispenser-Framework/Update/{$dd_data['folder_location']}/' class='btn btn-warning btn-sm'>Update</a> ";
                    }else{
                      $item_update = "";
                      $item_update_btn = "";
                    }
                    $item_installed = "true";
                  }else{
                    if($xmldownloaded){
                      $item_status = '<font color="red">Downloaded but Not Installed</font>';
                      $item_installed = "false";
                    }else{
                      $item_status = '<font color="red">Available for Download</font>';
                      $item_installed = "false";
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
                                if($item_installed == "true"){ echo "Installed Version: {$item_data[0]->version} $item_update<Br>"; }
                                echo "Status: $item_status";
                                echo "</p>";
                                if($item_installed == "true"){
                                  echo "$item_update_btn";
                                }else{
                                  if($xmldownloaded){
                                    echo "<a href='".SITE_URL."AdminPanel-Dispenser-Framework/Install/{$dd_data['folder_location']}/' class='btn btn-success btn-sm'>Install</a>";
                                  }else{
                                    echo "<a href='".SITE_URL."AdminPanel-Dispenser-Framework/Download/{$dd_data['folder_location']}/{$dd_data['type']}/' class='btn btn-info btn-sm'>Download</a>";
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
