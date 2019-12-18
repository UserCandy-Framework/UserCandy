<?php

/**
* Header for Install Script
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.3
*/

use Helpers\Request;

/** Check to see where the user is at within the install **/
if(isset($_GET['install_step'])){
  /** Check to see what step user is on **/
  if(Request::get('install_step') == "2"){
    /** User is on step 2 **/
    $step1_style = "btn-success";
    $step2_style = "btn-warning";
    $step3_style = "btn-info";
    $step4_style = "btn-info";
    $percentage = "25";
  }else if(Request::get('install_step') == "3"){
    /** User is on step 3 **/
    $step1_style = "btn-success";
    $step2_style = "btn-success";
    $step3_style = "btn-warning";
    $step4_style = "btn-info";
    $percentage = "50";
  }else if(Request::get('install_step') == "4"){
    /** User is on step 4 **/
    $step1_style = "btn-success";
    $step2_style = "btn-success";
    $step3_style = "btn-success";
    $step4_style = "btn-warning";
    $percentage = "75";
  }else if(Request::get('install_step') == "5"){
    /** User is on step 4 **/
    $step1_style = "btn-success";
    $step2_style = "btn-success";
    $step3_style = "btn-success";
    $step4_style = "btn-success";
    $percentage = "100";
  }
}else{
  /** User is on step 1 **/
  $step1_style = "btn-warning";
  $step2_style = "btn-info";
  $step3_style = "btn-info";
  $step4_style = "btn-info";
  $percentage = "0";
}

/** Function to display success message after redirect **/
function installSuccess($success_msg, $redirect_to_page = null){
    // Check to see if there is already a success message session
    if(isset($_SESSION['success_message'])){
      // Clean success message Session
      unset($_SESSION['success_message']);
    }
    // Send success message to session
    $_SESSION['success_message'] = $success_msg;
    // Check to see if a redirect to page is supplied
    if(isset($redirect_to_page)){
      // Redirect User to Given Page
      header('Location: '.$redirect_to_page);

      exit;
    }
}

$get_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
$set_site_url = substr($url, 0, strrpos( $get_url, '/'));

?>

<!DOCTYPE html>
<html lang="En">
<head>
  <meta charset="utf-8">
	<meta http-equiv='X-UA-Compatible' content='IE=edge'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
  <title>UC v4 Installation</title>
	<link rel='shortcut icon' href='http://uc3demo.usercandy.com/templates/default/assets/images/favicon.ico'>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <style type="text/css">
    .loader {
        position: fixed;
        z-index: 99;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #f5f5f5;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .loader > img {
        width: 100px;
    }
    .loader.hidden {
        animation: fadeOut 1s;
        animation-fill-mode: forwards;
    }
    @keyframes fadeOut {
        100% {
            opacity: 0;
            visibility: hidden;
        }
    }
    .thumb {
        height: 100px;
        border: 1px solid black;
        margin: 10px;
    }
  </style>
</head>
<body>

  <!-- Loading Display -->
	<div class="loader">
    <img src="UserCandyLogoLoading.gif" alt="Loading..." />
	</div>

<br>
	<div class='container'>
		<div class='row'>

			<div class='col-lg-12 col-md-12 col-sm-12'>

			<div class='card border-primary mb-3'>
				<div class='card-header h4'>
					<div class='row'>
						<div class='col-lg-12 col-md-12 col-sm-12' align='center'>
							<h3>UserCandy v1.0.0 Installation</h3>
						</div>
						<div class='col-lg-12 col-md-12 col-sm-12'>
							<hr>
						</div>
						<div class='col-lg-3 col-md-3 col-sm-3' align='center'>
							<div class='btn <?=$step1_style?> btn-lg'>Step 1</div><br>
							<small>System Check</small>
						</div>
						<div class='col-lg-3 col-md-3 col-sm-3' align='center'>
							<div href='/?install_step=2' class='btn <?=$step2_style?> btn-lg'>Step 2</div><br>
							<small>System Settings</small>
						</div>
						<div class='col-lg-3 col-md-3 col-sm-3' align='center'>
							<div href='/?install_step=3' class='btn <?=$step3_style?> btn-lg'>Step 3</div><br>
							<small>Create Database</small>
						</div>
						<div class='col-lg-3 col-md-3 col-sm-3' align='center'>
							<div href='/?install_step=4' class='btn <?=$step4_style?> btn-lg'>Step 4</div><br>
							<small>Finalize Install</small>
						</div>
						<div class='col-lg-12 col-md-12 col-sm-12'>
							<hr>
						</div>
						<div class='col-lg-12 col-md-12 col-sm-12'>
							<div class="progress">
								<div class="progress-bar bg-info" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
								  <div class="progress-bar bg-success" role="progressbar" aria-valuenow="<?=$percentage?>" aria-valuemin="0" aria-valuemax="100" style="width: <?=$percentage?>%;"> &nbsp;<?=$percentage?>% </div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
