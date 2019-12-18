<?php
/**
* Install Script Step 3
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.3
*/

use Helpers\Request;

/** Create Database **/
?>

<div class='card border-info mb-3'>
	<div class='card-header h4'>
		<h3>UserCandy Database Creation</h3>
	</div>
	<div class='card-body'>
		Now we are going to Import data to the Database.  <br>
		<br>
		<b>Database Name:</b> <?=DB_NAME?><Br>
		<b>Database Prefix:</b> <?=PREFIX?>
		<hr>

		<?php
			/** Check to see if user is importing data **/
			if(isset($_GET['import_database']) || isset($_GET['import_database'])){
				if(Request::get('import_database') == "true" || Request::post('import_database') == "true"){
					/** Include the Import DB File **/
					require SYSTEMDIR.'Install/database_import.php';
				}
			}else{
				echo "<form method='get' action=''>";
				echo "<input type='hidden' name='install_step' value='3'>";
				echo "<input type='hidden' name='import_database' value='true'>";
				echo "<button class='btn btn-primary btn-lg' id='submit'>Import Data to Database</button>";
				echo "</form>";
				echo "<Br>";
			}

			/** Database Import Success, Show button to move on **/
			if(isset($database_import)){
				echo "<hr>";
				echo "<a href='$set_site_url?install_step=4' class='btn btn-primary btn-lg'>Move on to Step 4</a>";
			}else{
				/** Check for database error **/
				if(isset($database_error)){
					echo "<hr><div class='alert alert-danger'>There was an error importing data to database.  Check your database and make sure ".DB_NAME." database exist and is empty.</div>";
					echo "<a href='$set_site_url?install_step=3&import_database=true' class='btn btn-warning btn-lg'>Retry Import Data to Database</a><br>";
				}
			}
		?>

	</div>
</div>
