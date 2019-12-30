<?php
// The file has JSON type.
header('Content-Type: application/json');
/** Get Folder Location for updates **/
(empty($viewVars[0])) ? $folder_location = null : $folder_location = $viewVars[0];
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
  echo json_encode(array("update_num" => null, "percent" => null, "message" => null));
}
