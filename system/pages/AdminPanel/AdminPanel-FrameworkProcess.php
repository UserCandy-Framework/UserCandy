<?php
use Helpers\{Csrf,Request};

/** Check to see if user is logged in */
if($data['isLoggedIn'] = $auth->isLogged()){
    /** User is logged in - Get their data */
    $u_id = $auth->user_info();
    $data['currentUserData'] = $usersModel->getCurrentUserData($u_id);
    if($data['isAdmin'] = $usersModel->checkIsAdmin($u_id) == 'false'){die;}
}else{die;}

/** Kick user out if demo site **/
if(DEMO_SITE == "TRUE"){die;}

// The file has JSON type.
header('Content-Type: application/json');

/** Check to see if Admin is using POST */
if(isset($_POST['submit']) && $_POST['submit'] == "true"){
  /** Check to make sure the csrf token is good */
  if (Csrf::isTokenValid('dispenser')) {
    //var_dump($_POST);die;
    /** Get Data from POST **/
    $folder = Request::post('folder');
    /** Run the update script **/
    /** Get Folder Location for updates **/
    (empty($folder)) ? $folder_location = null : $folder_location = $folder;
    /** Include the update file **/
    $file = CUSTOMDIR."framework/".$folder_location."/upgrade.php";
    /** Start the json object **/
    $obj = array();
    // Make sure the file is exist.
    if (file_exists($file)) {
      /** Check to see if session update_num is set **/
      if(isset($_SESSION['update_num'])){
        $update_num = $_SESSION['update_num'];
      }else{
        $update_num = "1";
      }
      $obj['update_num'] = $update_num;
      /** Include the Upgrades File **/
      require($file);
      /** Get Percentages of Upgrade Status **/
      $obj['percent'] = ($update_num*100)/$total_updates;
      /** Send Update Info to json output **/
      if(!empty($update_num)){
        echo json_encode($obj);
      }
      /** Add 1 to the session update num **/
      $_SESSION['update_num'] = $update_num + 1;
    }
    else {
      echo json_encode(array("update_num" => null, "percent" => null, "message" => 'file issue'));
    }
  }else{
    echo json_encode(array("update_num" => null, "percent" => null, "message" => 'token issue'));
  }
}else{
  echo json_encode(array("update_num" => null, "percent" => null, "message" => 'no submit'));
}

/** Format the Framework Upgrade status data to send to log file **/
$fw_upgrade_date = date("Y-m-d H:i:s");
$FWU_logMessage = "({$folder_location}) Upgrade information:\n
  Order Number: {$obj['update_num']}\n
  Date: {$fw_upgrade_date}\n
  Message: {$obj['message']}\n
  Action: {$obj['details']}\n
  Status: {$obj['status']}\n
------------------------------------\n\n";

/** Send Data to Log File **/
$fw_upgrade_file = ROOTDIR."system/logs/framework-upgrade.log";
/** Create file is not exists **/
if (is_file($fw_upgrade_file) === false) {
    file_put_contents($fw_upgrade_file, '');
}
/** Add Framework Upgrade data to log file **/
if(isset($FWU_logMessage))
file_put_contents($fw_upgrade_file, $FWU_logMessage, FILE_APPEND);
