<?php
/**
* Extender Core
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

namespace Core;

use Models\DispenserModel;

class Extender {

  public function load_ext($page = null, $location = null){
    /** Check to see extender files exist in custom extenders folder **/
    $load_files[] = CUSTOMDIR.'extenders/ext.'.$page.'.'.$location.'.php';
    /** Get list of installed dispensary items **/
    $DispenserModel = new DispenserModel();
    $get_dispensary_items = $DispenserModel->getDispenserItemsAll();
    /** Search each dispensary item to see if file exist, if so then include file **/
    if(!empty($get_dispensary_items)){
      foreach ($get_dispensary_items as $row) {
        $item_type = $row->type."s";
        $item_folder = $row->folder_location;
        $load_files[] = CUSTOMDIR.$item_type.'/'.$item_folder.'/extenders/ext.'.$page.'.'.$location.'.php';
      }
    }
    /** Check to see if each file exist for each dispensary item **/
    if(!empty($load_files)){
      foreach( $load_files as $load_file ) {
        if (file_exists($load_file)) {
            require_once($load_file);
        }
      }
    }
  }

}
