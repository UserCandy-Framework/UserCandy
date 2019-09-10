<?php
/**
* Main Install Script
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

/** Get Data from URL and POST **/
$install_step = Request::get('install_step');
$install_step_post = Request::post('install_step');

/** Include the Example-Config.php file to get current settings **/
if (file_exists(SYSTEMDIR.'Example-Config.php')) {
	require SYSTEMDIR.'Example-Config.php';
	new Config();
}

/** Include Used Class Files **/
require SYSTEMDIR.'helpers/helper.SuccessMessages.php';
require SYSTEMDIR.'helpers/helper.Url.php';

/** Include the Install Header **/
require SYSTEMDIR.'Install/Header.php';

/** Check to make sure system is not already installed **/
if (!file_exists(SYSTEMDIR.'Config.php')) {
	/** Make Sure All The Steps Needed for Install are on server **/
	if (file_exists(SYSTEMDIR.'Install/Step1.php') && file_exists(SYSTEMDIR.'Install/Step2.php') && file_exists(SYSTEMDIR.'Install/Step3.php') && file_exists(SYSTEMDIR.'Install/Step4.php') && file_exists(SYSTEMDIR.'Install/Step5.php')) {
		/** Check to see if an install step is set **/
		if (empty($install_step)){
			/** No Install Step set, display step1 **/
			require SYSTEMDIR.'Install/Step1.php';
		}else{
			/** Check to see what step user is on **/
		  if($install_step == "2"){
				/** Display step2 **/
				require SYSTEMDIR.'Install/Step2.php';
			}else if($install_step == "3" || $install_step_post == "3"){
				/** Display step3 **/
				require SYSTEMDIR.'Install/Step3.php';
			}else if($install_step == "4"){
				/** Display step4 **/
				require SYSTEMDIR.'Install/Step4.php';
			}else if($install_step == "5"){
				/** Display step5 **/
				require SYSTEMDIR.'Install/Step5.php';
			}else{
				echo "<font color='red'>Error: Unable to load Installer!</font>";
			}
		}
	}else{
		echo "<font color='red'>Error: Install Files Missing!</font>";
	}
}else{
	echo "UC 3 Is Installed.  Remove Install Directory!";
}

/** Include the Install Footer **/
require SYSTEMDIR.'Install/Footer.php';
