<?php
/**
* System Error Class
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
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
    public function downloadFromDispensary($token, $folder, $type){
      if(is_writable(SYSTEMDIR."temp/")){
        if(isset($folder) && isset($type)){
          $url = "https://www.usercandy.com/Dispensary/download/".$token."/".$type."s/".$folder."/";
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
          if(is_resource($zip = zip_open($filepath))){
            zip_close($zip);
            $zip = new \ZipArchive;
            $res = $zip->open($filepath);
            if ($res === TRUE) {
              $zip->extractTo(CUSTOMDIR.'/'.$type.'s/');
              $zip->close();
              unlink($filepath);
              return true;
            } else {
              return false;
            }
            return true;
          }else{
            unlink($filepath);
            return false;
          }
        }
      }else{
        return false;
      }
    }

    /** Function to download file by URL **/
    public function downloadFrameworkFromDispensary($token, $folder, $type){
      if(is_writable(SYSTEMDIR."temp/")){
        if(isset($folder) && isset($type)){
          $url = "https://www.usercandy.com/Dispensary/download/".$token."/".$type."/".$folder."/";
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
          if(is_resource($zip = zip_open($filepath))){
            zip_close($zip);
            $zip = new \ZipArchive;
            $res = $zip->open($filepath);
            if ($res === TRUE) {
              $zip->extractTo(CUSTOMDIR.'/'.$type.'/');
              $zip->close();
              unlink($filepath);
              return true;
            } else {
              return false;
            }
            return true;
          }else{
            unlink($filepath);
            return false;
          }
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

    /** Copies all files from one dir to another **/
    public function copyAllFiles($src,$dst) {
      $dir = opendir($src);
      @mkdir($dst);
      while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
          if ( is_dir($src . '/' . $file) ) {
            recurse_copy($src . '/' . $file,$dst . '/' . $file);
          }
          else {
            copy($src . '/' . $file,$dst . '/' . $file);
          }
        }
      }
      closedir($dir);
      return true;
    }

}
