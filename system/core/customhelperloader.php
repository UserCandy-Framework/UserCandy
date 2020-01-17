<?php
/**
* System Core Custom Helper Loader
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

use Models\DispenserModel;

/** Load all Classes for Enabled Helpers **/
/**
* UC Autoloader loads all Classes within specified folders
*/
spl_autoload_register(
  function($class_name) {
    /** Load the Dispenser Model **/
    $DispenserModel = new DispenserModel();
    /** Get Enabled Helper Folders from Dispenser DB **/
    $DispenserEnabledHelpers = $DispenserModel->getDispenserByType('helper');
    $dirs = array();
    // Define an array of directories in the order of their priority to iterate through.
    foreach($DispenserEnabledHelpers as $deh) {
      $dirs[] = CUSTOMDIR.'helpers/'.$deh->folder_location.'/';
    }
    // Looping through each directory to load all the class files. It will only require a file once.
    // If it finds the same class in a directory later on, IT WILL IGNORE IT! Because of that require once!
    foreach( $dirs as $dir ) {
      // Get the File Name based on the class by taking out the namespace
      $class_name = explode('\\', $class_name);
      $class_name = end($class_name);
      if (file_exists($dir.'helper.'.$class_name.'.php')) {
          require_once($dir.'helper.'.$class_name.'.php');
          return;
      }
    }
  }
);
