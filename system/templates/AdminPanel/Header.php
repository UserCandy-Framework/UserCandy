<?php
/**
* Admin Panel Header
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

use Core\Language;
use Helpers\{PageFunctions,Url,Assets,ErrorMessages,SuccessMessages};

	// Check to see what page is being viewed
	// If not Home, Login, Register, etc..
	// Send url to Session
	PageFunctions::prevpage();

	/** Checks to see if current page is in Dispenser **/
	$current_page = $_SERVER['REQUEST_URI'];
	if($current_page == '/AdminPanel-Dispenser-Settings' || $current_page == '/AdminPanel-Dispenser/Widgets' || $current_page == '/AdminPanel-Dispenser/Plugins' || $current_page == '/AdminPanel-Dispenser/Themes' || $current_page == '/AdminPanel-Dispenser/Helpers'){
		$show = "show";
	}

	$meta_output = PageFunctions::getPageMetaData();

?>

<!DOCTYPE html>
<html lang="<?php echo LANGUAGE_CODE; ?>">
<head>
    <meta charset="utf-8">
		<meta http-equiv='X-UA-Compatible' content='IE=edge'>
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $meta_output[0]->title.' - '.SITE_TITLE.' Admin Panel';?></title>
		<link rel='shortcut icon' href='<?=Url::templatePath()?>images/favicon.ico'>
    <?=Assets::css([
		'https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css',
		Url::templatePath('AdminPanel').'css/sb-admin.css',
		'https://use.fontawesome.com/releases/v5.9.0/css/all.css'
    ]);
    ?>
</head>
<body class="fixed-nav sticky-footer bg-dark" id="page-top">

	<!-- Loading Display -->
	<div class="loader">
    <img src="<?=SITE_URL?>UserCandyLogoLoading.gif" alt="Loading..." />
	</div>

	<!-- Navigation-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
    <a class="navbar-brand" href="#">UC Admin Panel</a>
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Dashboard">
          <a class="nav-link" href="<?php echo SITE_URL; ?>AdminPanel">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span class="nav-link-text">Dashboard</span>
          </a>
        </li>
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Charts">
          <a class="nav-link" href="<?php echo SITE_URL; ?>AdminPanel-Settings">
            <i class="fa fa-fw fa-cog"></i>
            <span class="nav-link-text">Main Settings</span>
          </a>
        </li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="Dispenser">
					<a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseDispenser" data-parent="#exampleAccordion">
						<i class="fa fa-fw fa-wrench"></i>
						<span class="nav-link-text">Dispenser</span>
					</a>
					<ul class="sidenav-second-level collapse rounded ml-2 mr-2 <?=$show?>" id="collapseDispenser">
						<li>
							<a href="<?php echo SITE_URL; ?>AdminPanel-Dispenser-Settings"><i class="fa fa-fw fa-cog"></i> Settings</a>
						</li>
						<li>
							<a href="<?php echo SITE_URL; ?>AdminPanel-Dispenser/Widgets"><i class="fa fa-fw fa-puzzle-piece"></i> Widgets</a>
						</li>
						<li>
							<a href="<?php echo SITE_URL; ?>AdminPanel-Dispenser/Plugins"><i class="fa fa-fw fa-plug"></i> Plug-Ins</a>
						</li>
						<li>
							<a href="<?php echo SITE_URL; ?>AdminPanel-Dispenser/Themes"><i class="fas fa-folder-plus"></i> Themes</a>
						</li>
						<li>
							<a href="<?php echo SITE_URL; ?>AdminPanel-Dispenser/Helpers"><i class="fas fa-plus-square"></i> Helpers</a>
						</li>
					</ul>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="Tables">
					<a class="nav-link" href="<?php echo SITE_URL; ?>AdminPanel-Users">
						<i class="fa fa-fw fa-users"></i>
						<span class="nav-link-text">Users</span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="Tables">
					<a class="nav-link" href="<?php echo SITE_URL; ?>AdminPanel-Groups">
						<i class="fa fa-fw fa-users-cog"></i>
						<span class="nav-link-text">Groups</span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="Tables">
					<a class="nav-link" href="<?php echo SITE_URL; ?>AdminPanel-AdvancedSettings">
						<i class="fa fa-fw fa-cog"></i>
						<span class="nav-link-text">Advanced Settings</span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="Tables">
					<a class="nav-link" href="<?php echo SITE_URL; ?>AdminPanel-EmailSettings">
						<i class="fa fa-fw fa-envelope"></i>
						<span class="nav-link-text">E-Mail Settings</span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="Tables">
					<a class="nav-link" href="<?php echo SITE_URL; ?>AdminPanel-SiteLinks">
						<i class="fa fa-fw fa-globe"></i>
						<span class="nav-link-text">Site Links</span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="Tables">
					<a class="nav-link" href="<?php echo SITE_URL; ?>AdminPanel-PagesPermissions">
						<i class="fas fa-fw fa-unlock-alt"></i>
						<span class="nav-link-text">Pages Permissions</span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="Tables">
					<a class="nav-link" href="<?php echo SITE_URL; ?>AdminPanel-MassEmail">
						<i class="fas fa-fw fa-mail-bulk"></i>
						<span class="nav-link-text">Mass Email</span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="Tables">
					<a class="nav-link" href="<?php echo SITE_URL; ?>AdminPanel-TermsPrivacy">
						<i class="fas fa-fw fa-info-circle"></i>
						<span class="nav-link-text">Terms and Privacy</span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="Tables">
					<a class="nav-link" href="<?php echo SITE_URL; ?>AdminPanel-AuthLogs">
						<i class="fa fa-fw fa-server"></i>
						<span class="nav-link-text">Auth Logs</span>
					</a>
				</li>
				<?php echo PageFunctions::getLinks('nav_admin', $currentUserData[0]->userID); ?>
			</ul>
      <ul class="navbar-nav sidenav-toggler">
        <li class="nav-item">
          <a class="nav-link text-center" id="sidenavToggler">
            <i class="fa fa-fw fa-angle-left"></i>
          </a>
        </li>
      </ul>
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link" href="<?php echo SITE_URL ?>">
            <i class="fas fa-sign-out-alt"></i> Main Site
					</a>
        </li>
      </ul>
    </div>
  </nav>

	<div class="content-wrapper">
    <div class="container-fluid">
			<div class="row">
				<!-- BreadCrumbs -->
				<?php
				// Display Breadcrumbs if set
				if(!empty($meta_output[0]->breadcrumbs)){
					echo "<div class='col-lg-12 col-md-12 col-sm-12'>";
						echo "<ol class='breadcrumb'>";
							echo "<li class='breadcrumb-item'><a href='".SITE_URL."'>".Language::show('uc_home', 'Welcome')."</a></li>";
							echo $meta_output[0]->breadcrumbs;
						echo "</ol>";
					echo "</div>";
				}
				?>
			</div>

	<div class="row">
		<?php
		// Setup the Error and Success Messages Libs
		// Display Success and Error Messages if any
		echo ErrorMessages::display();
		echo SuccessMessages::display();
		if(isset($error)) { echo ErrorMessages::display_raw($error); }
		if(isset($success)) { echo SuccessMessages::display_raw($success); }
		?>
	</div>
	<div class="row">
