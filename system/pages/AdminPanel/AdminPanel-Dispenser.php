<?php
/**
* Admin Panel Dispenser Items Page
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
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
(empty($viewVars[0])) ? $page = null : $page = $viewVars[0];

/** Check to see if Admin is using POST */
if(isset($_POST['action'])){
  /** Check to make sure the csrf token is good */
  if (Csrf::isTokenValid('dispenser')) {
    //var_dump($_POST);die;
    /** Get Data from POST **/
    $action = Request::post('action');
    $folder = Request::post('folder');
    $type = Request::post('type');
  }else{
    /** Error Message Display */
    ErrorMessages::push('There was an error with the token!', 'AdminPanel-Dispenser/'.$page);
  }
}

/** Setup Token for Form */
$data['csrfToken'] = Csrf::makeToken('dispenser');

/** Setup Button Loading Message **/
$data['ownjs'][] = "
<script type='text/javascript'>
	$('form').submit(function(){
	    $(this).find('button[type=submit]').prop('disabled', true);
			$(this).find('button[type=submit]').html(
				`<span class=\"spinner-border spinner-border-sm\" role=\"status\" aria-hidden=\"true\"></span> Loading...`
			);
      $(':button').prop('disabled', true);
	});
</script>
";

$page_lowercase = strtolower($page);

/** Load Models **/
$AdminPanelModel = new AdminPanelModel();
$DispenserModel = new DispenserModel();
$pages = new Paginator(USERS_PAGEINATOR_LIMIT);  // How many rows per page

/** Check to see if Dispenser Framework Page is open then set vars for it **/
if($page_lowercase == "framework"){
  /** Get Currently installed framework version **/
  $cur_framework_version = UCVersion;
  $page_single = $page;
  $page_single_lowercase = $page_lowercase;
}else{
  /** Remove "s" from pagename **/
  $page_single = substr($page, 0, -1);
  $page_single_lowercase = substr($page_lowercase, 0, -1);
}

/** Check to see if Admin is installing or updating a Item **/
if($action == "Install" && !empty($folder)){
  /** Check to see if site is a demo site */
  if(DEMO_SITE == "TRUE"){
    /** Error Message Display */
    ErrorMessages::push('Demo Limit - Dispenser Installs Disabled', 'AdminPanel-Dispenser/'.$page);
  }
  $load_install_file = CUSTOMDIR."$page_lowercase/$folder/install.php";
  if(file_exists($load_install_file)){
    require_once($load_install_file);
    /** Get Data from info.xml file for DB **/
    $load_info_file_i = CUSTOMDIR."$page_lowercase/$folder/info.xml";
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
          $load_update_file = CUSTOMDIR."$page_lowercase/$folder/update.php";
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
    SuccessMessages::push('You Have Successfully Installed a '.$page_single.'<Br><br>'.$db_install_status.$db_update_status.$new_pages , 'AdminPanel-Dispenser/'.$page.'/#'.$folder);
  }else{
    /** Success */
    ErrorMessages::push('There was an Error Installing a '.$page_single.'<Br><br>'.$db_update_status.$new_pages, 'AdminPanel-Dispenser/'.$page.'/#'.$folder);
  }
}else if($action == "UnInstall" && !empty($folder)){
  /** Check to see if site is a demo site */
  if(DEMO_SITE == "TRUE"){
    /** Error Message Display */
    ErrorMessages::push('Demo Limit - Dispenser UnInstalls Disabled', 'AdminPanel-Dispenser/'.$page.'/#'.$folder);
  }
  $load_uninstall_file = CUSTOMDIR."$page_lowercase/$folder/uninstall.php";
  if(file_exists($load_uninstall_file)){
    require_once($load_uninstall_file);
    /** Get Data from info.xml file for DB **/
    $load_info_file_i = CUSTOMDIR."$page_lowercase/$folder/info.xml";
    if(file_exists($load_info_file_i)){
      /** Get list of Downloaded Items **/
      $xmlinstall=simplexml_load_file($load_info_file_i);
    }
    /** Delete Item from Dispenser Database **/
    if($DispenserModel->deleteDispenser($xmlinstall->NAME, $xmlinstall->TYPE, $xmlinstall->FOLDER_LOCATION, $xmlinstall->VERSION)){
      /** Send data to the Database **/
      if(isset($uninstall_db_data)){
        if($db_update_status = $DispenserModel->updateDatabase($uninstall_db_data)){
            $uninstall_status = 'Success';
        }
      }else{
        $uninstall_status = 'Success';
      }
    }
  }
  /** Format Data for Success Message */
  if(!empty($db_install_status)){$db_install_status = implode(" ", $db_install_status);}else{$db_install_status = "";}
  if(!empty($db_update_status)){$db_update_status = implode(" ", $db_update_status);}else{$db_update_status = "";}
  if(!empty($new_pages)){$new_pages = implode(" ", $new_pages);}else{$new_pages = "";}
  /** Check to see if the uninstall was successful **/
  if($uninstall_status == 'Success'){
    /** Success */
    SuccessMessages::push('You Have Successfully UnInstalled a '.$page_single.'<Br><br>'.$db_install_status.$db_update_status.$new_pages , 'AdminPanel-Dispenser/'.$page.'/#'.$folder);
  }else{
    /** Success */
    ErrorMessages::push('There was an Error UnInstalling a '.$page_single.'<Br><br>'.$db_update_status.$new_pages, 'AdminPanel-Dispenser/'.$page.'/#'.$folder);
  }
}else if($action == "Update" && !empty($folder)){
  /** Check to see if site is a demo site */
  if(DEMO_SITE == "TRUE"){
    /** Error Message Display */
    ErrorMessages::push('Demo Limit - Dispenser Updates Disabled', 'AdminPanel-Dispenser/'.$page.'/#'.$folder);
  }
  $load_update_file = CUSTOMDIR."$page_lowercase/$folder/update.php";
  if(file_exists($load_update_file)){
    /** Get Data from info.xml file for DB **/
    $load_info_file_u = CUSTOMDIR."$page_lowercase/$folder/info.xml";
    if(file_exists($load_info_file_u)){
      /** Get list of Downloaded Items **/
      $xmlupdate=simplexml_load_file($load_info_file_u);
    }
    /** Get Item DB Data **/
    $dispenser_db_data = $DispenserModel->getDispenserByName($folder, $page_single_lowercase);
    /** Check to make sure there is an update **/
    if($xmlupdate->VERSION > $dispenser_db_data[0]->version){
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
    SuccessMessages::push('You Have Successfully Updated a '.$page_single.'<Br><br>'.$db_update_status, 'AdminPanel-Dispenser/'.$page.'/#'.$folder);
  }else{
    /** Success */
    ErrorMessages::push('There was an Error Updating a '.$page_single.'<Br><br>'.$db_update_status, 'AdminPanel-Dispenser/'.$page.'/#'.$folder);
  }
}else if($action == "Disable" && !empty($folder)){
  /** Check to see if site is a demo site */
  if(DEMO_SITE == "TRUE"){
    /** Error Message Display */
    ErrorMessages::push('Demo Limit - Dispenser Disables Disabled', 'AdminPanel-Dispenser/'.$page.'/#'.$folder);
  }
  $load_update_file = CUSTOMDIR."$page_lowercase/$folder/update.php";
  if(file_exists($load_update_file)){
    /** Get Data from info.xml file for DB **/
    $load_info_file_u = CUSTOMDIR."$page_lowercase/$folder/info.xml";
    if(file_exists($load_info_file_u)){
      /** Get Item DB Data **/
      $dispenser_db_data = $DispenserModel->getDispenserByName($folder, $page_single_lowercase);
      /** Disable Site Item in DB **/
      if($DispenserModel->updateDispenserEnable($dispenser_db_data[0]->id, 'false')){
        $update_status = "Success";
      }
    }
  }
  if($update_status == 'Success'){
    /** Success */
    SuccessMessages::push('You Have Successfully Disabled a '.$page_single, 'AdminPanel-Dispenser/'.$page.'/#'.$folder);
  }else{
    /** Success */
    ErrorMessages::push('There was an Error Disabled a '.$page_single, 'AdminPanel-Dispenser/'.$page.'/#'.$folder);
  }
}else if($action == "Enable" && !empty($folder)){
  /** Check to see if site is a demo site */
  if(DEMO_SITE == "TRUE"){
    /** Error Message Display */
    ErrorMessages::push('Demo Limit - Dispenser Enables Disabled', 'AdminPanel-Dispenser/'.$page.'/#'.$folder);
  }
  $load_update_file = CUSTOMDIR."$page_lowercase/$folder/update.php";
  if(file_exists($load_update_file)){
    /** Get Data from info.xml file for DB **/
    $load_info_file_u = CUSTOMDIR."$page_lowercase/$folder/info.xml";
    if(file_exists($load_info_file_u)){
      /** Get Item DB Data **/
      $dispenser_db_data = $DispenserModel->getDispenserByName($folder, $page_single_lowercase);
      /** Disable Site Item in DB **/
      if($DispenserModel->updateDispenserEnable($dispenser_db_data[0]->id, 'true')){
        $update_status = "Success";
      }
    }
  }
  if($update_status == 'Success'){
    /** Success */
    SuccessMessages::push('You Have Successfully Enabled a '.$page_single, 'AdminPanel-Dispenser/'.$page.'/#'.$folder);
  }else{
    /** Success */
    ErrorMessages::push('There was an Error Enabled a '.$page_single, 'AdminPanel-Dispenser/'.$page.'/#'.$folder);
  }
}else if($action == "Download" && !empty($folder) && !empty($type)){
  /** Check to see if site is a demo site */
  if(DEMO_SITE == "TRUE"){
    /** Error Message Display */
    ErrorMessages::push('Demo Limit - Dispenser Downloads Disabled', 'AdminPanel-Dispenser/'.$page.'/#'.$folder);
  }
  $Dispenser = new Dispenser();
  /** Get Settings Data */
  $dispenser_api_key = $AdminPanelModel->getSettings('dispenser_api_key');
  /** Get File Name from file_unique_name **/
  $fun_parts = explode("-", $folder);
  if($fun_parts[0] == "framework"){
    $dl_folder = $fun_parts[1]."-".$fun_parts[2];
  }else{
    $dl_folder = $fun_parts[1];
  }
  if($filedata = Dispenser::downloadFromDispensary($dispenser_api_key, $folder, $type, $dl_folder)){
    $download_status = $filedata;
  }else{
    $download_status = false;
  }
  if($download_status == 'Success'){
    /** Delete the zip file **/
    $dl_filepath = SYSTEMDIR."temp/".$dl_folder.".zip";
    if(file_exists($dl_filepath)){ unlink($dl_filepath); }
    /** Success */
    SuccessMessages::push('You Have Successfully Downloaded a '.$page_single, 'AdminPanel-Dispenser/'.$page.'/#'.$dl_folder);
  }else{
    /** Success */
    ErrorMessages::push('There was an Error Downloading a '.$page_single, 'AdminPanel-Dispenser/'.$page.'/#'.$dl_folder);
  }
}else if($action == "Activate" && !empty($folder)){
  /** Check to see if site is a demo site */
  if(DEMO_SITE == "TRUE"){
    /** Error Message Display */
    ErrorMessages::push('Demo Limit - Dispenser Activations Disabled', 'AdminPanel-Dispenser/Themes/#'.$folder);
  }
  $load_update_file = CUSTOMDIR."themes/$folder/update.php";
  if(file_exists($load_update_file)){
    /** Get Data from info.xml file for DB **/
    $load_info_file_u = CUSTOMDIR."themes/$folder/info.xml";
    if(file_exists($load_info_file_u)){
      /** Update Site Theme in DB **/
      if($AdminPanelModel->updateSetting('site_theme', $folder)){
        $update_status = "Success";
      }
    }
  }
  if($update_status == 'Success'){
    /** Success */
    SuccessMessages::push('You Have Successfully Updated a Theme', 'AdminPanel-Dispenser/Themes/#'.$folder);
  }else{
    /** Success */
    ErrorMessages::push('There was an Error Updating a Theme', 'AdminPanel-Dispenser/Themes/#'.$folder);
  }
}

/** Check for Framework Upgrade - Only Show Upgrade Stuff **/
if($action == "Upgrade" && !empty($folder)){
  require("Upgrade/Upgrade.php");
}else{

/** Get data for dashboard */
$data['current_page'] = $_SERVER['REQUEST_URI'];
$data['title'] = "Dispenser ".$page;

/** Get Settings Data */
$dispenser_api_key = $AdminPanelModel->getSettings('dispenser_api_key');

/** Connect to UserCandy Dispensary **/
$get_dd = $Dispenser->getDataFromDispensary($dispenser_api_key, $page_single_lowercase);
$scan_dir = CUSTOMDIR.$page_lowercase;
$get_dirs = array_filter(glob($scan_dir.'/*'), 'is_dir');

if(isset($get_dirs)){
  foreach ($get_dirs as $dir) {
    $use_dir = explode('/', $dir);
    $use_dir = array_values(array_slice($use_dir, -1))[0];
    $load_info_file = CUSTOMDIR."$page_lowercase/$use_dir/info.xml";
    if(file_exists($load_info_file)){
      /** Get list of Downloaded Items **/
      $xml[]=simplexml_load_file($load_info_file);
    }
  }
}

/** Check if Theme **/
if($page == "Themes"){
  $site_theme = $AdminPanelModel->getSettings('site_theme');
}

/** Setup Breadcrumbs */
$data['breadcrumbs'] = "<li class='breadcrumb-item'><a href='".SITE_URL."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li><li class='breadcrumb-item active'><i class='fa fa-fw fa-cog'></i> ".$data['title']."</li>";

$item_displayed = false;

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
      </div>
    </div>

    <div class='card-deck col-12'>
    <?php
      if(isset($xml)){
        $xdn = 0;
        foreach ($xml as $xmldata) {
          /** Check to see if Framework update **/
          if($page == "Framework"){
            /** Downloadable framework upgrade is older than the one installed. **/
            $ver_comp_x = version_compare($cur_framework_version, $xmldata->VERSION);
            if($ver_comp_x == -1 && $xdn == 0){
              $xdn++;
              $display_items = true;
            }else{
              $display_items = false;
            }
          }else{
            $display_items = true;
          }
          if($display_items == true){
            /** Get Item Data from Database if installed **/
            $item_data = $DispenserModel->getDispenserByName($xmldata->FOLDER_LOCATION, $xmldata->TYPE);
            $item_dispensary_data = $Dispenser->getItemDataFromDispensary($dispenser_api_key, $xmldata->TYPE, $xmldata->FOLDER_LOCATION);
            $item_dispensary_version = $item_dispensary_data[0]['version'];
            if(!empty($item_data)){
              if($item_data[0]->enable == "true"){$item_enable = " - <font color='green'>Enabled</font>";}else{$item_enable = " - <font color='red'>Disabled</font>";}
              $item_status = '<font color="green">Installed</font> '.$item_enable;
              $item_uninstall = "<a href='#UnInstallModal{$xmldata->FOLDER_LOCATION}{$xmldata->TYPE}' class='btn btn-sm btn-danger trigger-btn float-right m-2' data-toggle='modal'>UnInstall</a>";
              if($xmldata->VERSION > $item_data[0]->version){
                $item_update = " - <font color='red'>Update Available</font>";
                $item_update_btn = Form::buttonForm(null, [['name'=>'action','value'=>'Update'],['name'=>'folder','value'=>$xmldata->FOLDER_LOCATION],['name'=>'token_dispenser','value'=>$data['csrfToken']]], ['class'=>'btn btn-info btn-sm float-right m-2','name'=>'submit','type'=>'submit','value'=>'Update from version '.$item_data[0]->version.' to '.$xmldata->VERSION]);
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
              $item_update_download = Form::buttonForm(null, [['name'=>'action','value'=>'Download'],['name'=>'folder','value'=>$item_dispensary_data[0]['file_unique_name']],['name'=>'type','value'=>$item_dispensary_data[0]['file_size']],['name'=>'token_dispenser','value'=>$data['csrfToken']]], ['class'=>'btn btn-info btn-sm float-right m-2','name'=>'submit','type'=>'submit','value'=>'Download Latest Version ('.$item_dispensary_version.')']);
            }else{
              $item_update_download = "";
            }

            echo "<div class='col-auto mb-4 pr-0 pl-0'>";
              echo "<div class='card border-dark' style='max-width:362px'>";
                echo "<a name='{$xmldata->FOLDER_LOCATION}' class='anchor'></a>";
                echo "<img src='{$xmldata->IMAGE}' class='card-img-top border-bottom' alt='{$xmldata->TITLE}'>";
                echo "<div class='card-body px-2'>";
                  echo "<p class='card-text border-bottom'>{$xmldata->DESCRIPTION}</p>";
                  echo "<p class='card-text border-bottom'>";
                  echo "Author: {$xmldata->AUTHOR} <Br>";
                  if($xmldata->FOLDER_LOCATION != 'default'){
                    echo "Files Version: {$xmldata->VERSION} <br>";
                    echo "Release Date: {$xmldata->RELEASE_DATE}<br>";
                    if($item_installed == "true"){ echo "Installed Version: {$item_data[0]->version} $item_update<Br>"; }
                    echo "Status: $item_status";
                    echo "</p>";
                    if($item_installed == "true"){
                      if($item_data[0]->enable == 'true'){
                        if($page == "Widgets"){
                          echo " <a href='".SITE_URL."AdminPanel-Dispenser-Widgets-Settings/{$item_data[0]->id}/' class='btn btn-primary btn-sm m-2'>Settings</a> ";
                        }else if($page == "Themes"){
                          if($site_theme == $xmldata->FOLDER_LOCATION){
                            echo " <font color='green'>Active Theme</font> <Br>";
                          }else{
                            echo Form::buttonForm(null, [['name'=>'action','value'=>'Activate'],['name'=>'folder','value'=>$xmldata->FOLDER_LOCATION],['name'=>'token_dispenser','value'=>$data['csrfToken']]], ['class'=>'btn btn-primary btn-sm m-2','name'=>'submit','type'=>'submit','value'=>'Activate']);
                          }
                        }
                        echo Form::buttonForm(null, [['name'=>'action','value'=>'Disable'],['name'=>'folder','value'=>$xmldata->FOLDER_LOCATION],['name'=>'token_dispenser','value'=>$data['csrfToken']]], ['class'=>'btn btn-warning btn-sm m-2','name'=>'submit','type'=>'submit','value'=>'Disable']);
                      }else{
                        echo Form::buttonForm(null, [['name'=>'action','value'=>'Enable'],['name'=>'folder','value'=>$xmldata->FOLDER_LOCATION],['name'=>'token_dispenser','value'=>$data['csrfToken']]], ['class'=>'btn btn-primary btn-sm m-2','name'=>'submit','type'=>'submit','value'=>'Enable']);
                      }
                      echo "$item_update_btn";
                    }else{
                      if($page == "Framework"){
                        echo " <a href='#UpdateModal' class='btn btn-info btn-sm float-right trigger-btn m-2' data-toggle='modal'>Upgrade from version $cur_framework_version to {$xmldata->VERSION}</a> ";
                      }else{
                        echo Form::buttonForm(null, [['name'=>'action','value'=>'Install'],['name'=>'folder','value'=>$xmldata->FOLDER_LOCATION],['name'=>'token_dispenser','value'=>$data['csrfToken']]], ['class'=>'btn btn-success btn-sm m-2','name'=>'submit','type'=>'submit','value'=>'Install']);
                      }
                    }
                    echo $item_update_download;
                  }else{
                    if($site_theme != 'default' && $page == "Themes"){
                      echo Form::buttonForm(null, [['name'=>'action','value'=>'Activate'],['name'=>'folder','value'=>$xmldata->FOLDER_LOCATION],['name'=>'token_dispenser','value'=>$data['csrfToken']]], ['class'=>'btn btn-primary btn-sm m-2','name'=>'submit','type'=>'submit','value'=>'Activate']);
                    }else{
                      echo "<font color='green'>Active Theme</font>";
                    }
                  }
                  echo $item_uninstall;
                  if(!empty($item_dispensary_data[0]['changelog'])){
                    echo "<a href='#ViewChangeLog{$xmldata->FOLDER_LOCATION}{$xmldata->TYPE}' class='btn btn-sm btn-info trigger-btn mx-2' data-toggle='modal'>ChangeLog</a>
                          <div class='modal fade' id='ViewChangeLog{$xmldata->FOLDER_LOCATION}{$xmldata->TYPE}' tabindex='-1' role='dialog' aria-labelledby='DeleteLabel' aria-hidden='true'>
                            <div class='modal-dialog modal-lg' role='document'>
                              <div class='modal-content'>
                                <div class='modal-header'>
                                  <h5 class='modal-title' id='ChangeLogLabel'>{$xmldata->FOLDER_LOCATION} ChangeLog</h5>
                                  <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                    <span aria-hidden='true'>&times;</span>
                                  </button>
                                </div>
                                <div class='modal-body'><pre>{$item_dispensary_data[0]['changelog']}</pre></div>
                                <div class='modal-footer'>
                                  <button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>
                                </div>
                              </div>
                            </div>
                          </div>
                    ";
                  }
                  echo "
                    <div class='modal fade' id='UnInstallModal{$xmldata->FOLDER_LOCATION}{$xmldata->TYPE}' tabindex='-1' role='dialog' aria-labelledby='DeleteLabel' aria-hidden='true'>
                      <div class='modal-dialog' role='document'>
                        <div class='modal-content'>
                          <div class='modal-header'>
                            <h5 class='modal-title' id='DeleteLabel'>UnInstall?</h5>
                            <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                              <span aria-hidden='true'>&times;</span>
                            </button>
                          </div>
                          <div class='modal-body'>
                            Do you want to UnInstall this?<br><br>
                            {$xmldata->FOLDER_LOCATION} - {$xmldata->TYPE}<Br><Br>
                            Note: Files are not deleted, however related database items will be deleted, and CAN NOT be recovered.
                          </div>
                          <div class='modal-footer'>
                            <button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>";
                            echo Form::buttonForm(null, [['name'=>'action','value'=>'UnInstall'],['name'=>'folder','value'=>$xmldata->FOLDER_LOCATION],['name'=>'type','value'=>$xmldata->TYPE],['name'=>'token_dispenser','value'=>$data['csrfToken']]], ['class'=>'btn btn-danger','name'=>'submit','type'=>'submit','value'=>'UnInstall']);
                            echo "
                          </div>
                        </div>
                      </div>
                    </div>
                  ";
                  echo "
                    <div class='modal fade' id='UpdateModal' tabindex='-1' role='dialog' aria-labelledby='DeleteLabel' aria-hidden='true'>
                      <div class='modal-dialog' role='document'>
                        <div class='modal-content'>
                          <div class='modal-header'>
                            <h5 class='modal-title' id='InstallLabel'>Update UserCandy Framework?</h5>
                            <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                              <span aria-hidden='true'>&times;</span>
                            </button>
                          </div>
                          <div class='modal-body'>
                            Are you sure you would like to Update to UserCandy Framework from $cur_framework_version to {$xmldata->VERSION}?<br><br>
                            Note: Make sure to create a full backup as this udpate will replace any stock UserCandy Framework file.
                          </div>
                          <div class='modal-footer'>
                            <button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>";
                            echo Form::buttonForm(null, [['name'=>'action','value'=>'Upgrade'],['name'=>'folder','value'=>$xmldata->FOLDER_LOCATION],['name'=>'token_dispenser','value'=>$data['csrfToken']]], ['class'=>'btn btn-info float-right','name'=>'submit','type'=>'submit','value'=>'Update from version '.$cur_framework_version.' to '.$xmldata->VERSION]);
                            echo "
                          </div>
                        </div>
                      </div>
                    </div>
                  ";
                echo "</div>";
              echo "</div>";
            echo "</div>";
            $item_displayed = true;
          }
        }
      }

    ?>

    <?php
      /** Get all downloadable data from the Dispensary **/
      $gddn = 0;
      if($get_dd){
        foreach ($get_dd as $dd_data) {
          /** Check to see if the item is already downloaded **/
          $check_if_downloaded = CUSTOMDIR."$page_lowercase/{$dd_data['folder_location']}/info.xml";
          if(file_exists($check_if_downloaded)){
            /** Get list of Downloaded Items **/
            $xmldownloadedfile=simplexml_load_file($check_if_downloaded);
            if(isset($xmldownloadedfile)){
              $xmldownloaded = true;
            }
          }else if($page_lowercase == "framework"){
            /** Downloadable framework upgrade is older than the one installed. **/
            $ver_comp = version_compare($cur_framework_version, $dd_data['version']);
            if($ver_comp == -1 && $gddn == 0){
              $gddn++;
              $xmldownloaded = false;
            }else{
              $xmldownloaded = true;
            }
          }else{
            $xmldownloaded = false;
          }
          /** Check to see if Item should be shown **/
          if($xmldownloaded == false){
            /** Get Item Data from Database if installed **/
            $item_data = $DispenserModel->getDispenserByName($dd_data['folder_location'], $dd_data['type']);
            if(empty($item_data)){
              if(!$xmldownloaded){
                $item_status = '<font color="red">Available for Download</font>';
                $item_installed = "false";
              }
            }
            echo "<div class='col-auto mb-4 pr-0 pl-0'>";
              echo "<div class='card border-dark' style='max-width:362px'>";
                echo "<a name='{$dd_data['folder_location']}' class='anchor'></a>";
                echo "<img src='{$dd_data['image']}' class='card-img-top border-bottom' alt='{$dd_data['title']}'>";
                echo "<div class='card-body px-2'>";
                  echo "<p class='card-text border-bottom'>{$dd_data['description']}</p>";
                  echo "<p class='card-text border-bottom'>";
                  echo "Author: {$dd_data['author']} <Br>";
                  if($dd_data['folder_location'] != 'default'){
                    echo "Files Version: {$dd_data['version']} <br>";
                    echo "Release Date: {$dd_data['release_date']}<br>";
                    echo "Status: $item_status";
                    echo "</p>";
                    if($item_installed == "false"){
                      if(!$xmldownloaded){
                        echo Form::buttonForm(null, [['name'=>'action','value'=>'Download'],['name'=>'folder','value'=>$dd_data['file_unique_name']],['name'=>'type','value'=>$dd_data['file_size']],['name'=>'token_dispenser','value'=>$data['csrfToken']]], ['class'=>'btn btn-info btn-sm m-2','name'=>'submit','type'=>'submit','value'=>'Download']);
                      }
                    }
                  }

                  if(!empty($dd_data['changelog'])){
                    echo "<a href='#ViewChangeLogNew{$dd_data['folder_location']}{$dd_data['type']}' class='btn btn-sm btn-info trigger-btn mx-2' data-toggle='modal'>ChangeLog</a>
                          <div class='modal fade' id='ViewChangeLogNew{$dd_data['folder_location']}{$dd_data['type']}' tabindex='-1' role='dialog' aria-labelledby='DeleteLabel' aria-hidden='true'>
                            <div class='modal-dialog modal-lg' role='document'>
                              <div class='modal-content'>
                                <div class='modal-header'>
                                  <h5 class='modal-title' id='ChangeLogLabel'>{$dd_data['folder_location']} ChangeLog</h5>
                                  <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                    <span aria-hidden='true'>&times;</span>
                                  </button>
                                </div>
                                <div class='modal-body'><pre>{$dd_data['changelog']}</pre></div>
                                <div class='modal-footer'>
                                  <button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>
                                </div>
                              </div>
                            </div>
                          </div>
                    ";
                  }

                echo "</div>";
              echo "</div>";
            echo "</div>";
            $item_displayed = true;
          }
        }
      }else{
        $dcon_error = true;
      }
    ?>
    </div>
  </div>
</div>

<?php
/** Display Message if Framework is up to date **/
if($page == "Framework" && $item_displayed == false){
  echo "<div class='col-lg-12 col-md-12 col-sm-12 mb-4'>";
    echo "<div class='card'>";
      echo "<div class='card-footer text-center'>";
        echo "<h4>Your UserCandy Framework is up to date.</h4>";
        echo "No downloads available at this time.<br>";
        echo "Visit <a href='https://www.usercandy.com' target='_blank'>www.UserCandy.com</a> for more information.<br>";
        echo "Currently Installed UserCandy Framework Version: ".$cur_framework_version;
      echo "</div>";
    echo "</div>";
  echo "</div>";
}

if($dcon_error == true){
  echo "<div class='col-lg-12 col-md-12 col-sm-12 mb-4'>";
    echo "<div class='card'>";
      echo "<div class='card-footer text-center'>";
        echo "No Downloadable Items Available or There is a Connection Issue.<br>";
        echo "<a href='".SITE_URL."AdminPanel-Dispenser-Settings/' class='btn btn-warning btn-sm'>Check Connections Settings</a>";
      echo "</div>";
    echo "</div>";
  echo "</div>";
}
}
?>
