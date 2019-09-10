<?php
/**
* Install Script Step 2
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/


// TODO
// Firgure out a better way to run the tests/or have a refreash of some sort.

/** System Settings **/

/** Function to update Example-Config.php file **/
function update_config($default, $new){
	$fname = SYSTEMDIR."Example-Config.php";
	$fhandle = fopen($fname,"r");
	$content = fread($fhandle,filesize($fname));

	$content = str_replace($default, $new, $content);

	$fhandle = fopen($fname,"w");
	fwrite($fhandle,$content);
	fclose($fhandle);
	return true;
}

/** Check to see if user has updated config file **/
if(isset($_GET['update_config_file']) && Request::get('update_config_file') == "true"){ $updated_config = true; }else{ $updated_config = false; }

/** Check to see if user is submitting data **/
if(isset($_POST['submit'])){
	/** Update Site URL in Config **/
	if(!empty($_REQUEST['SITE_URL'])){
		// Add check to make sure http:// or https:// is in SITE_URL before saving
		$site_url = rtrim($_REQUEST['SITE_URL'], '/') . '/';
		update_config(SITE_URL, $site_url);
	}
	/** Update DB Host in Config **/
	if(!empty($_REQUEST['DB_HOST'])){
		update_config(DB_HOST, $_REQUEST['DB_HOST']);
	}
	/** Update DB Name in Config **/
	if(!empty($_REQUEST['DB_NAME'])){
		update_config(DB_NAME, $_REQUEST['DB_NAME']);
	}
	/** Update DB Username in Config **/
	if(!empty($_REQUEST['DB_USER'])){
		update_config(DB_USER, $_REQUEST['DB_USER']);
	}
	/** Update DB Password in Config **/
	if(!empty($_REQUEST['DB_PASS'])){
		update_config(DB_PASS, $_REQUEST['DB_PASS']);
	}
	/** Update Site Prefix in Config **/
	if(!empty($_REQUEST['PREFIX'])){
		update_config(PREFIX, $_REQUEST['PREFIX']);
	}

	/** Config File Has been Updated. Refresh Page with success message **/
	installSuccess('You Have Successfully Updated The Config File! Please Double Check The Data!', $set_site_url.'?install_step=2&update_config_file_refresh=true');
}

	/** Check to see if user has updated config
	* If they have just updated the config file
	* Show a please wait animation
	* Then refresh the page back to step
	**/
	if(isset($_GET['update_config_file_refresh'])){
		if(Request::get('update_config_file_refresh') == "true"){
			echo "<div class='card border-primary bg-light mb-3 text-center'><card class='card-body'>";
			echo "<img src='UserCandyLogoLoading.gif'>";
			echo "<h3>Please Wait While The Config File Is Updated!</h3></card></div>";
			echo "<meta http-equiv='refresh' content='5; url=$set_site_url?install_step=2&update_config_file=true'>";
		}
	}else{
		echo "<div class='row'>";
			echo SuccessMessages::display_nolang();
		echo "</div>";
?>

<div class='card border-info mb-3'>
	<div class='card-header h4'>
		<h3>UserCandy System Settings</h3>
	</div>
	<div class='card-body'>
		Now we are going to setup all the settings needed to create your Config.php file.  <br>
		Make sure to fill in all the required fields. <br>
		<hr>
		<form class="form" method="post" action="<?=$set_site_url?>">
			<!-- Site Settings -->
			<div class='card mb-3'>
				<div class='card-header h4'>
					<h3>Site Settings</h3>
				</div>
				<div class='card-body'>
					<div class="form-group">
						<label for="SITE_URL">Website URL</label><span class="label label-danger float-right">Required</span>
						<input type="text" class="form-control" name="SITE_URL" id="SITE_URL" placeholder="<?=SITE_URL?>" value="<?php if(!empty($_REQUEST['SITE_URL'])){echo $_REQUEST['SITE_URL'];}else{echo $set_site_url;} ?>">
					</div>
				</div>
			</div>
			<!-- Database Settings -->
			<div class='card mb-3'>
				<div class='card-header h4'>
					<a name='database'></a>
					<h3>Database Settings</h3>
				</div>
				<div class='card-body'>
					<div class="form-group">
						<label for="DB_HOST">Host</label><span class="label label-danger float-right">Required</span>
						<input type="text" class="form-control" name="DB_HOST" id="DB_HOST" placeholder="<?=DB_HOST?>" value="<?php if(!empty($_REQUEST['DB_HOST'])){echo $_REQUEST['DB_HOST'];} ?>">
					</div>
					<div class="form-group">
						<label for="DB_NAME">Datebase Name</label><span class="label label-danger float-right">Required</span>
						<input type="text" class="form-control" name="DB_NAME" id="DB_NAME" placeholder="<?=DB_NAME?>" value="<?php if(!empty($_REQUEST['DB_NAME'])){echo $_REQUEST['DB_NAME'];} ?>">
					</div>
					<div class="form-group">
						<label for="DB_USER">Username</label><span class="label label-danger float-right">Required</span>
						<input type="text" class="form-control" name="DB_USER" id="DB_USER" placeholder="<?=DB_USER?>" value="<?php if(!empty($_REQUEST['DB_USER'])){echo $_REQUEST['DB_USER'];} ?>">
					</div>
					<div class="form-group">
						<label for="DB_PASS">Password</label><span class="label label-danger float-right">Required</span>
						<input type="password" class="form-control" name="DB_PASS" id="DB_PASS" placeholder="<?=DB_PASS?>" value="<?php if(!empty($_REQUEST['DB_PASS'])){echo $_REQUEST['DB_PASS'];} ?>">
					</div>
					<div class="form-group">
						<label for="PREFIX">Prefix</label><span class="label label-danger float-right">Required</span>
						<input type="text" class="form-control" name="PREFIX" id="PREFIX" placeholder="<?=PREFIX?>" value="<?php if(!empty($_REQUEST['PREFIX'])){echo $_REQUEST['PREFIX'];} ?>">
					</div>
				</div>
					<?php
						(Request::get('test_db') !== null) ? $test_db = Request::get('test_db') : $test_db = "" ;
						if($updated_config || $test_db == "true"){
						/** Test Database Settings **/
							echo "<div class='card-footer text-muted'>";
							echo "<b>Database Status:</b> ";
							if($test_db == "true"){
								require SYSTEMDIR.'Install/database_check.php';
								$updated_config = true;
							}else{
								echo "<a href='$set_site_url?install_step=2&test_db=true#database'>Test Database Settings</a>";
							}
							echo "</div>";
						}
					?>
			</div>
			<!-- Save to Config Button -->
			<button class="btn btn-primary btn-lg" name="submit" type="submit">Update Config File</button>
		</form>

		<hr>
		<?php
			/** Check to see if config has been updated **/
			if($updated_config){
				echo "<B>Double check the above site settings.  If everything looks good Move on to Step 3.</b><hr>";
				echo "<a href='$set_site_url?install_step=3' class='btn btn-success btn-lg'>Move on to Step 3</a>";
			}
		?>


	</div>
</div>
<?php } ?>
