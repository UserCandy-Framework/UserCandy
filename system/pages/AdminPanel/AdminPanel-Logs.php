<?php
/**
* Admin Panel Auth Log Viewer
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

use Helpers\{ErrorMessages,SuccessMessages,Form,Csrf,Request};

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

/** Get data from URL **/
(empty($viewVars[0])) ? $log_type = null : $log_type = $viewVars[0];

/** Set The Page Title **/
$data['title'] = $log_type." Logs";

/** Display Logs if log type is selected **/
if(!empty($log_type)){
  if($log_type == "Error"){
    /** Get data from the php-error.log file **/
    $log_file = ROOTDIR."system/logs/php-error.log";
  }else if($log_type == "Upgrade"){
    /** Get data from the framework-upgrade.log file **/
    $log_file = ROOTDIR."system/logs/framework-upgrade.log";
  }else{
    /** No Log Type Selected - Kick user out */
    ErrorMessages::push('There was an error getting the type of logs requested.', 'AdminPanel');
  }
}else{
  /** No Log Type Selected - Kick user out */
  ErrorMessages::push('There was an error getting the type of logs requested.', 'AdminPanel');
}

/** Check if requested log file exist **/
if(file_exists($log_file)){
  $log_data = file_get_contents($log_file);
}

/** Check if Admin is clearing log file **/
/** Check to see if Admin is using POST */
if(isset($_POST['submit'])){
  /** Check to see if site is a demo site */
  if(DEMO_SITE == "TRUE"){
    /** Error Message Display */
    ErrorMessages::push('Demo Limit - Dispenser Installs Disabled', 'AdminPanel-Logs/'.$log_type);
  }
  /** Check to make sure the csrf token is good */
  if (Csrf::isTokenValid('logs')) {
    /** Get Data from POST **/
    $action = Request::post('action');
    $logstype = Request::post('logstype');
    /** Check to see if user is trying to clear a log file **/
    if($action == "clearlogs"){
      /** Make sure page matches request **/
      if($logstype == $log_type){
        /** Make sure log file exists **/
        if(file_exists($log_file)){
          /** Clear the contents of the log file **/
          $fh = fopen($log_file, 'w');
          fclose($fh);
          /** Success Message Display */
          SuccessMessages::push($log_type.' Log File has been cleared!', 'AdminPanel-Logs/'.$log_type);
        }
      }
    }
  }else{
    /** Error Message Display */
    ErrorMessages::push('There was an error with the token!', 'AdminPanel-Logs/'.$log_type);
  }
}

/** Setup Token for Form */
$data['csrfToken'] = Csrf::makeToken('logs');

/** Show error if no log data exists **/
if(empty($log_data)){
  $log_data = $log_type." Log File is empty or does not exists.";
}

/** Setup Breadcrumbs **/
$data['breadcrumbs'] = "<li class='breadcrumb-item'><a href='".SITE_URL."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li><li class='breadcrumb-item active'><i class='fa fa-fw fa-server'></i> ".$data['title']."</li>";

?>
<div class='col-lg-12 col-md-12 col-sm-12'>
	<div class='card mb-3'>
		<div class='card-header h4'>
			<?php echo $data['title'] ?>
		</div>
		<div class="card-body">
      <pre>
<?=$log_data?>
      </pre>
    </div>
    <div class="card-footer">
        <?php echo " <a href='#ClearLogsModal' class='btn btn-info btn-sm float-right trigger-btn m-2' data-toggle='modal'>Clear $log_type Log File</a> "; ?>
        <div class='modal fade' id='ClearLogsModal' tabindex='-1' role='dialog' aria-labelledby='DeleteLabel' aria-hidden='true'>
          <div class='modal-dialog' role='document'>
            <div class='modal-content'>
              <div class='modal-header'>
                <h5 class='modal-title' id='DeleteLabel'>Clear Logs?</h5>
                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                  <span aria-hidden='true'>&times;</span>
                </button>
              </div>
              <div class='modal-body'>
                Do you want to clear this log file?<br><br>
                <?=$log_type?> Log file will be cleared of all data.<Br><Br>
                Note: Once cleared, the data can not be recovered.
              </div>
              <div class='modal-footer'>
                <button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>
                <?php echo Form::buttonForm(null, [['name'=>'action','value'=>'clearlogs'],['name'=>'logstype','value'=>$log_type],['name'=>'token_logs','value'=>$data['csrfToken']]], ['class'=>'btn btn-danger','name'=>'submit','type'=>'submit','value'=>'Clear Log File']); ?>
              </div>
            </div>
          </div>
        </div>
    </div>
	</div>
</div>
