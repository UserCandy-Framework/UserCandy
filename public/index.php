<?php
/**
* Site Index File
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

/* Define the absolute paths for configured directories */
define('ROOTDIR', realpath(__DIR__.'/../').'/');
define('SYSTEMDIR', realpath(__DIR__.'/../system/').'/');
define('CUSTOMDIR', realpath(__DIR__.'/../custom/').'/');

/** Define Current Version of UC **/
define('UCVersion', '1.0.0');

/* load UC Autoloader */
if (file_exists(SYSTEMDIR.'core/autoloader.php')) {
    require SYSTEMDIR.'core/autoloader.php';
    if (is_readable(SYSTEMDIR.'Config.php') && file_exists(SYSTEMDIR.'core/customhelperloader.php')) {
      require SYSTEMDIR.'core/customhelperloader.php';
    }
} else {
    echo "<h1>Error With UserCandy Auto Loader</h1>";
    echo "<p>Contact Administrator for Support</p>";
    exit;
}

/* Start the Session */
session_start();

/* Error Settings */
error_reporting(E_ALL);

/* Make sure Config File Exists */
if (is_readable(SYSTEMDIR.'Config.php')) {

  /* Load the Site Config */
  require(SYSTEMDIR.'Config.php');
  new Config();

  /* Load Site Settings From Database */
  new Core\LoadSiteSettings();

  /* Run the Core Loader */
  require(SYSTEMDIR.'core/coreloader.php');

  /* Load Top Extender for Index */
  Core\Extender::load_ext('index', 'top');

  /* Load the Page Router */
  new Core\Router();

  /* Load Bottom Extender for Index */
  Core\Extender::load_ext('index', 'bottom');

} else {
    /** No Config Setup, Start Install */
    if (file_exists(SYSTEMDIR.'Install/Install.php')) {
        require SYSTEMDIR.'Install/Install.php';
    } else {
        echo "<h1>Update and Rename Example-Config.php to Config.php</h1>";
        echo "<p>Make sure to rename <code>/app/Example-Config.php</code> to <code>/app/Config.php</code> for this application to start working.</p>";
        echo "<p>Read the README for details.</p>";
    }
}
