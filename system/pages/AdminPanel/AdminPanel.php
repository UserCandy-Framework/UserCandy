<?php
/**
* Admin Panel Home View
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.3
*/

use Helpers\{ErrorMessages,Paginator,SiteStats};
use Models\AdminPanelModel;

/** Check to see if user is logged in */
if($data['isLoggedIn'] = $auth->isLogged()){
	/** User is logged in - Get their data */
	$u_id = $auth->user_info();
	$data['currentUserData'] = $usersModel->getCurrentUserData($u_id);
	if($data['isAdmin'] = $usersModel->checkIsAdmin($u_id) == 'false'){
		/** User Not Admin - kick them out */
		ErrorMessages::push('You are Not Admin', '');
	}
}else{
	/** User Not logged in - kick them out */
	ErrorMessages::push('You are Not Logged In', 'Login');
}

/** Load Models **/
$AdminPanelModel = new AdminPanelModel();
$pages = new Paginator(USERS_PAGEINATOR_LIMIT);  // How many rows per page

// Get data for dashboard
$data['current_page'] = $_SERVER['REQUEST_URI'];
$data['title'] = "Dashboard";
$data['welcomeMessage'] = "Welcom to the Admin Panel Dashboard!";

/** Get Data For Member Totals Stats Sidebar */
$data['activatedAccounts'] = count($membersModel->getActivatedAccounts());
$data['onlineAccounts'] = count($membersModel->getOnlineAccounts());

/** Get Count Data For Groups */
$usergroups = count($AdminPanelModel->getAllGroups());

/** Get Count of Members that Have Logged In Past Days */
$mem_login_past_1 = count($AdminPanelModel->getPastUsersData('LastLogin', '1'));
$mem_login_past_7 = count($AdminPanelModel->getPastUsersData('LastLogin', '7'));
$mem_login_past_30 = count($AdminPanelModel->getPastUsersData('LastLogin', '30'));
$mem_login_past_90 = count($AdminPanelModel->getPastUsersData('LastLogin', '90'));
$mem_login_past_365 = count($AdminPanelModel->getPastUsersData('LastLogin', '365'));

/** Get Count of Members that Have Signed Up In Past Days */
$mem_signup_past_1 = count($AdminPanelModel->getPastUsersData('SignUp', '1'));
$mem_signup_past_7 = count($AdminPanelModel->getPastUsersData('SignUp', '7'));
$mem_signup_past_30 = count($AdminPanelModel->getPastUsersData('SignUp', '30'));
$mem_signup_past_90 = count($AdminPanelModel->getPastUsersData('SignUp', '90'));
$mem_signup_past_365 = count($AdminPanelModel->getPastUsersData('SignUp', '365'));

/** Get total page views count */
$totalPageViews = SiteStats::getTotalViews();

/** Get Top Referers */
$topRefer = $AdminPanelModel->getTopRefer('30');
$topReferYear = $AdminPanelModel->getTopRefer('365');

/** Function to check if the files exist (prevent errors when mother server is down) */
function UR_exists($url){
	$headers=get_headers($url);
	return stripos($headers[0],"200 OK")?true:false;
}

/** Get Current UC Version Data From UserCandy.com */
$check_url = 'https://www.usercandy.com/ucversion.php?getversion=UC';
if(UR_exists($check_url)){
	$html = file_get_contents($check_url);
	preg_match("/UC v(.*) UC/i", $html, $match);
	$cur_uc_version = UCVersion;
	if($cur_uc_version != $match[1]){
		if($cur_uc_version < $match[1]){ $data['cur_uc_version'] = $match[1]; }
	}
}

/** Check to see if UC Files are Newer than Database Version */
$uc_files_version = UCVersion;
$uc_database_version = $AdminPanelModel->getDatabaseVersion();
if(empty($uc_database_version)){ $uc_database_version = "1.0.0"; }

// Setup Breadcrumbs
$data['breadcrumbs'] = "<li class='breadcrumb-item'><a href='".SITE_URL."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li><li class='breadcrumb-item active'><i class='fas fa-fw fa-tachometer-alt'></i> ".$data['title']."</li>";

if(isset($data['cur_uc_version'])){
	echo "<div class='col-lg-12 col-md-12 col-sm-12'>";
	echo "<div class='alert alert-danger'>";
		if(isset($data['cur_uc_version'])){
			echo "<b>New Update Released for UC! <br>";
			echo "New Version:</b> {$data['cur_uc_version']} <br>";
			echo "<b>Current Version:</b> ".UCVersion."<br>";
		}
		echo "<hr>Visit <a href='http://www.usercandy.com' target='_blank'>www.UserCandy.com</a> For Updates";
	echo "</div>";
	echo "</div>";
}
if($uc_files_version > $uc_database_version){
	echo "<div class='col-lg-12 col-md-12 col-sm-12'>";
	echo "<div class='alert alert-danger'>";
		echo "<b>UC Database is out of Date. <br>";
		echo "New Version:</b> $uc_files_version <br>";
		echo "<b>Current Version:</b> $uc_database_version <br>";
		echo "<a href='".SITE_URL."AdminPanel-Upgrade'>Click Here to Upgrade</a>";
	echo "</div>";
	echo "</div>";
}

?>
<div class='col-lg-12 col-md-12 col-sm-12'>
	<div class='row'>
		<div class="col-xs-12 col-md-6 col-lg-3 mb-3">
			<div class="card text-white bg-primary o-hidden h-100">
            <div class="card-body">
              <div class="card-body-icon">
                <i class="fa fa-fw fa-user"></i>
              </div>
              <div class="mr-5"><?=$activatedAccounts?></div>
            </div>
            <a class="card-footer text-white clearfix small z-1" href="<?=SITE_URL?>AdminPanel-Users">
              <span class="float-left">Site Members</span>
              <span class="float-rightt">
                <i class="fa fa-angle-right"></i>
              </span>
            </a>
			</div>
		</div>

		<div class="col-xs-12 col-md-6 col-lg-3 mb-3">
			<div class="card text-white bg-warning o-hidden h-100">
            <div class="card-body">
              <div class="card-body-icon">
                <i class="fa fa-fw fa-users"></i>
              </div>
              <div class="mr-5"><?=$usergroups?></div>
            </div>
            <a class="card-footer text-white clearfix small z-1" href="<?=SITE_URL?>AdminPanel-Groups">
              <span class="float-left">User Groups</span>
              <span class="float-rightt">
                <i class="fa fa-angle-right"></i>
              </span>
            </a>
			</div>
		</div>

		<div class="col-xs-12 col-md-6 col-lg-3 mb-3">
			<div class="card text-white bg-success o-hidden h-100">
            <div class="card-body">
              <div class="card-body-icon">
                <i class="fa fa-fw fa-user"></i>
              </div>
              <div class="mr-5"><?=$onlineAccounts?></div>
            </div>
            <a class="card-footer text-white clearfix small z-1" href="<?=SITE_URL?>AdminPanel-Users">
              <span class="float-left">Online Members</span>
              <span class="float-rightt">
                <i class="fa fa-angle-right"></i>
              </span>
            </a>
			</div>
		</div>

		<div class="col-xs-12 col-md-6 col-lg-3 mb-3">
			<div class="card text-white bg-danger o-hidden h-100">
            <div class="card-body">
              <div class="card-body-icon">
                <i class="fa fa-fw fa-road"></i>
              </div>
              <div class="mr-5"><?=$totalPageViews?></div>
            </div>
            <a class="card-footer text-white clearfix small z-1" href="#">
              <span class="float-left">Page Views</span>
              <span class="float-rightt">
                <i class="fa fa-angle-right"></i>
              </span>
            </a>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<div class="card mb-3">
				<div class="card-header h4">Site Traffic Overview<span class='float-right'><small><font color='#30a4ff'>Current Year</font> <font color='#dcdcdc'>Previous Year</font></small></span></div>
				<div class="card-body">
					<div class="canvas-wrapper">
						<canvas class="main-chart" id="line-chart" height="200" width="600"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div><!--/.row-->

	<div class="row">
		<div class='col-lg-6 col-md-6'>
			<div class='card mb-3'>
				<div class='card-header h4'>
					Users Signed Up Stats
				</div>
				<ul class='list-group list-group-flush'>
						<li class='list-group-item'><span class='pull-left'>Past Day:</span><span class='float-right'><?=$mem_signup_past_1?></span><div class='clearfix'></div></li>
						<li class='list-group-item'><span class='pull-left'>Past Week:</span><span class='float-right'><?=$mem_signup_past_7?></span><div class='clearfix'></div></li>
						<li class='list-group-item'><span class='pull-left'>Past 30 Days:</span><span class='float-right'><?=$mem_signup_past_30?></span><div class='clearfix'></div></li>
						<li class='list-group-item'><span class='pull-left'>Past 90 Days:</span><span class='float-right'><?=$mem_signup_past_90?></span><div class='clearfix'></div></li>
						<li class='list-group-item'><span class='pull-left'>Past Year:</span><span class='float-right'><?=$mem_signup_past_365?></span><div class='clearfix'></div></li>
				</ul>
			</div>
		</div>

		<div class='col-lg-6 col-md-6'>
			<div class='card mb-3'>
				<div class='card-header h4'>
					Users Logged In Stats
				</div>
				<ul class='list-group list-group-flush'>
						<li class='list-group-item'><span class='pull-left'>Past Day:</span><span class='float-right'><?=$mem_login_past_1?></span><div class='clearfix'></div></li>
						<li class='list-group-item'><span class='pull-left'>Past Week:</span><span class='float-right'><?=$mem_login_past_7?></span><div class='clearfix'></div></li>
						<li class='list-group-item'><span class='pull-left'>Past 30 Days:</span><span class='float-right'><?=$mem_login_past_30?></span><div class='clearfix'></div></li>
						<li class='list-group-item'><span class='pull-left'>Past 90 Days:</span><span class='float-right'><?=$mem_login_past_90?></span><div class='clearfix'></div></li>
						<li class='list-group-item'><span class='pull-left'>Past Year:</span><span class='float-right'><?=$mem_login_past_365?></span><div class='clearfix'></div></li>
				</ul>
			</div>
		</div>

		<div class='col-lg-6 col-md-6'>
			<div class='card mb-3'>
				<div class='card-header h4'>
					Top Referers Past Month
				</div>
				<ul class='list-group list-group-flush'>
					<?php
						if(isset($topRefer)){
							foreach ($topRefer as $refer) {
								echo "<li class='list-group-item'><span class='pull-left'>$refer->refer</span><span class='float-right'>$refer->refer_count</span><div class='clearfix'></div></li>";
							}
						}
					?>
				</ul>
			</div>
		</div>

		<div class='col-lg-6 col-md-6'>
			<div class='card mb-3'>
				<div class='card-header h4'>
					Top Referers Past Year
				</div>
				<ul class='list-group list-group-flush'>
					<?php
						if(isset($topReferYear)){
							foreach ($topReferYear as $refer) {
								echo "<li class='list-group-item'><span class='pull-left'>$refer->refer</span><span class='float-right'>$refer->refer_count</span><div class='clearfix'></div></li>";
							}
						}
					?>
				</ul>
			</div>
		</div>

		<div class='col-lg-12 col-md-12'>
			<div class='card mb-3'>
				<div class='card-header h4'>
					Site Map File
				</div>
				<ul class='list-group list-group-flush'>
						<li class='list-group-item'><a href='<?=SITE_URL?>sitemap.xml' target='_blank' class='pull-left'><?=SITE_URL?>sitemap.xml</a><span class='float-right'>Copy URL to <a href='https://search.google.com/search-console/' target='_blank'>Google Search Console</a></span></li>
				</ul>
			</div>
		</div>

		<div class='col-lg-12 col-md-12'>
			<div class='card mb-3'>
				<div class='card-header h4'>
					UserCandy Support and Community
				</div>
				<ul class='list-group list-group-flush'>
						<li class='list-group-item'>
							<i class='fas fa-lg fa-home'></i> <a href='https://www.usercandy.com/Forum' target='_blank' class='pull-left'>UserCandy Forums</a>
							<span class='float-right'><a href='https://discordapp.com/invite/XATkVce' target='_blank' class='pull-left'>Discord</a> <i class='fab fa-lg fa-discord'></i></span>
						</li>
						<li class='list-group-item'>
							<i class='fab fa-lg fa-github'></i> <a href='https://github.com/UserCandy-Framework/UserCandy' target='_blank' class='pull-left'>GitHub Repo</a>
							<span class='float-right'><a href='https://www.facebook.com/UserCandy/' target='_blank' class='pull-left'>Facebook</a> <i class='fab fa-lg fa-facebook'></i></span>
						</li>
						<li class='list-group-item'>
							<i class='fab fa-lg fa-gitter'></i> <a href='https://gitter.im/UserCandyFramework/community' target='_blank' class='pull-left'>Gitter</a>
							<span class='float-right'><a href='https://twitter.com/UserCandy1' target='_blank' class='pull-left'>Twitter</a> <i class='fab fa-lg fa-twitter'></i></span>
						</li>
				</ul>
			</div>
		</div>

	</div>
</div>

<?php
$first  = strtotime('first day of next month');
$months = array();

for ($i = 7; $i >= 1; $i--) {
  array_push($months, date('F Y', strtotime("-$i month", $first)));
}
$month_display = '';
$month_cur_year = '';
$month_prev_year = '';
foreach($months as $row){
	$month_display .= '"'.date('F', strtotime($row)).'",';
	$month_cur_year .= '"'.SiteStats::getCurrentMonth($row, 'thisYear').'",';
	$month_prev_year .= '"'.SiteStats::getCurrentMonth($row, 'lastYear').'",';
}
$month_display1 = rtrim($month_display,'",');
$month_display2 = substr($month_display1, 1);
$month_cur_year = rtrim($month_cur_year,'",');
$month_cur_year = substr($month_cur_year, 1);
$month_prev_year = rtrim($month_prev_year,'",');
$month_prev_year = substr($month_prev_year, 1);
?>

<script type="text/javascript">
var lineChartData = {
		labels : ["<?php echo $month_display2;?>"],
		datasets : [
			{
				label: "Page Views Current Year",
				fillColor : "rgba(48, 164, 255, 0.2)",
				strokeColor : "rgba(48, 164, 255, 1)",
				pointColor : "rgba(48, 164, 255, 1)",
				pointStrokeColor : "#fff",
				pointHighlightFill : "#fff",
				pointHighlightStroke : "rgba(48, 164, 255, 1)",
				data : ["<?php echo $month_cur_year;?>"]
			},
			{
				label: "Page Views Previous Year",
				fillColor : "rgba(220,220,220,0.2)",
				strokeColor : "rgba(220,220,220,1)",
				pointColor : "rgba(220,220,220,1)",
				pointStrokeColor : "#fff",
				pointHighlightFill : "#fff",
				pointHighlightStroke : "rgba(220,220,220,1)",
				data : ["<?php echo $month_prev_year;?>"]
			}
		]

	}

	window.onload = function(){
		var chart1 = document.getElementById("line-chart").getContext("2d");
		window.myLine = new Chart(chart1).Line(lineChartData, {
			responsive: true
		});
	};
</script>
