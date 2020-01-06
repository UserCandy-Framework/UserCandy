<?php
/**
* System Error Class
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

namespace Core;

use Models\DispenserModel;

class Dispenser {

    protected $DispenserModel;

    function __construct(){
        /** Load the Dispenser Model */
        $DispenserModel = new DispenserModel();
    }

    /** Get Widgets Based on page_id **/
    static function getWidgets($page_id = null){
      /** Get Widgets Based on page_id **/
      if($widgets = $DispenserModel->getWidgetByPage($page_id)){
        return $widgets;
      }
    }

    /** Function to download file by URL **/
    public function downloadFromDispensary($token, $file_unique_name, $file_size, $folder){
      if(is_writable(SYSTEMDIR."temp/")){
        if(isset($token) && isset($file_unique_name) && isset($file_size) && isset($folder)){
          $url = "https://www.usercandy.com/Dispensary/download/".$token."/".$file_unique_name."/";
          $filepath = SYSTEMDIR."temp/".$folder.".zip";
          $fp = fopen($filepath, 'w+');
          $ch = curl_init($url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
          curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
          //curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
          curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
          curl_setopt($ch, CURLOPT_FILE, $fp);
          curl_exec($ch);
          curl_close($ch);
          fclose($fp);
          /** Check to see if file was downloaded to temp folder **/
          if(file_exists($filepath)){
            $fun_parts = explode("-", $file_unique_name);
            $filesize = filesize($filepath);
            /** Check if Framework and check has based on that **/
            if($fun_parts[0] == "framework"){
              $check_file_hash = $fun_parts[3];
            }else{
              $check_file_hash = $fun_parts[2];
            }
            /** Check the file Hash and Size and compair to remote before unzipping **/
            if (($check_file_hash == sha1_file($filepath)) && $file_size == $filesize){
              /** Validate the ZIP file **/
              $zip_check = new \ZipArchive();
              $res = $zip_check->open($filepath, \ZipArchive::CHECKCONS);
              if ($res !== TRUE) {
                switch($res) {
                  case \ZipArchive::ER_NOZIP:
                    unlink($filepath);
                    return false;
                  case \ZipArchive::ER_INCONS :
                    unlink($filepath);
                    return false;
                  case \ZipArchive::ER_CRC :
                    unlink($filepath);
                    return false;
                  default:
                    unlink($filepath);
                    return false;
                }
              }
          	  /** File Good - Unzip the file to custom folder **/
              if(is_resource($zip = zip_open($filepath))){
                zip_close($zip);
                $zip = new \ZipArchive;
                $res = $zip->open($filepath);
                if ($res === TRUE) {
                  $zip->extractTo(CUSTOMDIR.'/'.$fun_parts[0].'/');
                  $zip->close();
                  return true;
                } else {
                  unlink($filepath);
                  return false;
                }
              }else{
                unlink($filepath);
                return false;
              }
            }else{
            	/** File Bad - Delete the file **/
            	unlink($filepath);
              return false;
            }
          }else{
            return false;
          }
        }else{
          return false;
        }
      }else{
        return false;
      }
    }

    /** Function to get data from UserCandy Dispensary **/
    public function getDataFromDispensary($dispenser_api_key, $type){
      if(!empty($dispenser_api_key)){
        $url = "https://www.usercandy.com/Dispensary/listdownloads/".$dispenser_api_key."/".$type;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$url);
        $result=curl_exec($ch);
        curl_close($ch);
        if($result){
          $server_data = json_decode($result, true);
          if($server_data['error']){
            return false;
          }else{
            return $server_data;
          }
        }else{
          return false;
        }
      }else{
        return false;
      }
    }

    /** Function to get item data from UserCandy Dispensary **/
    public function getItemDataFromDispensary($dispenser_api_key, $type, $folder_location){
      if(!empty($dispenser_api_key)){
        $url = "https://www.usercandy.com/Dispensary/currentversion/".$dispenser_api_key."/".$type."/".$folder_location;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$url);
        $result=curl_exec($ch);
        curl_close($ch);
        if($result){
          $server_data = json_decode($result, true);
          if($server_data['error']){
            return false;
          }else{
            return $server_data;
          }
        }else{
          return false;
        }
      }else{
        return false;
      }
    }

    /** Open Zip File and Read the data of the info.xml file **/
    public function read_zip_xml($type = null, $filename = null){
      $handle = fopen('zip://'.CUSTOMDIR.'framework/'.$filename.'.zip#'.$filename.'/info.xml', 'r');
      $result = '';
      while (!feof($handle)) {
        $result .= fread($handle, 8192);
      }
      fclose($handle);
      return simplexml_load_string($result);
    }

    /** Function that creates a backup, copys, and deltes temp file **/
    public function updateFrameworkFile($dl_location=null,$cp_location=null,$bu_location=null,$file_name=null){
      if(isset($dl_location) && isset($cp_location) && isset($bu_location)){
        /** Check if copy to folder exist - create if not **/
        if (!file_exists($cp_location)) {
            mkdir($cp_location, 0777, true);
        }
        /** Check if backup folder exist - create if not **/
        if (!file_exists($bu_location)) {
            mkdir($bu_location, 0777, true);
        }
        /** Make sure file is downloaded **/
        if(file_exists($dl_location.$file_name)){
          /** If File is not new then create a backup **/
          if(file_exists($cp_location.$file_name)){
            copy($cp_location.$file_name, $bu_location.$file_name);
          }
          /** Copy file to Final Location **/
          copy($dl_location.$file_name, $cp_location.$file_name);
          return true;
        }else{
          return false;
        }
      }else{
        return false;
      }
    }

}
