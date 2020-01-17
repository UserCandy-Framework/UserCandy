<?php
/**
* Install Script Step 5
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

/** Install Success **/

/** Last thing we need to do is Copy Example-Config.php to Config.php **/
if (file_exists(SYSTEMDIR.'Example-Config.php') && is_writable(SYSTEMDIR)) {
	if(copy(SYSTEMDIR.'Example-Config.php', SYSTEMDIR.'Config.php')){
		$copy_file = true;
		//delete example-config file and database
		unlink(SYSTEMDIR.'Example-Config.php');
		unlink(ROOTDIR.'database.sql');
	}else{
		$copy_file = false;
	}
}else{
	$copy_file = false;
}

if(!$copy_file){
	echo "<div class='alert alert-danger'>There was an error creating Config.php.  You must manually rename Example-Config.php to Config.php in the /system/ folder.</div>";
}else{
?>

<div class='card border-info mb-3'>
	<div class='card-header h4'>
		<h3>UserCandy Installation Step 4</h3>
	</div>
	<div class='card-body'>
		UserCandy Has Successfully Installed on your Server.  <br>
		Make sure to Register for your site first, as the first user to Register is Administrator by default. <br>
		<br>
		Thank You for choosing UserCandy Framework to run your website.  Make sure to visit
		<a href='http://www.usercandy.com/' target='_blank'>www.usercandy.com</a>
		for updates, plugins, and much more!<br>
		<hr>
		You may change site settings in the future by editing /system/Config.php file.<br><br>
		Also if you like you can delete the Install Folder that is located in /system/Intall/.
		<hr>
		<a href='<?=$set_site_url?>' class='btn btn-primary btn-lg'>Click Here To Enjoy Your New UserCandy Installation</a>

	</div>
</div>
<?php } ?>
