<?php
/**
* UserCandy Auto Loader
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

/**
* UC Autoloader loads all Classes within specified folders
*/

spl_autoload_register(function($class_name) {

    // Define an array of directories in the order of their priority to iterate through.
    $dirs = array(
        SYSTEMDIR.'core/', // Core Classes Required for Framework
        SYSTEMDIR.'models/', // Model Classes used for database goods
        SYSTEMDIR.'helpers/', // Helper Classes used to extend the Framework
        SYSTEMDIR.'helpers/PhpMailer/', // PhpMailer Classes
    );

    // Looping through each directory to load all the class files. It will only require a file once.
    // If it finds the same class in a directory later on, IT WILL IGNORE IT! Because of that require once!
    foreach( $dirs as $dir ) {
        if (file_exists($dir.'class.'.$class_name.'.php')) {
            require_once($dir.'class.'.$class_name.'.php');
            return;
        }
        if (file_exists($dir.'model.'.$class_name.'.php')) {
            require_once($dir.'model.'.$class_name.'.php');
            return;
        }
        if (file_exists($dir.'helper.'.$class_name.'.php')) {
            require_once($dir.'helper.'.$class_name.'.php');
            return;
        }
    }
});
