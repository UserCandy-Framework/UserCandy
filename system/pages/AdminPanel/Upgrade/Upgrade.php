<?php
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

/** Kick user out if demo site **/
if(DEMO_SITE == "TRUE"){
  /** Error Message Display */
  ErrorMessages::push('Demo Limit - Dispenser Installs Disabled', 'AdminPanel-Dispenser/Framework/');
}

  /** Clear the session update_num **/
  $_SESSION['update_num'] = null;
  $_SESSION['cur_datetime'] = date("Y-m-d-H-i-s");
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

<div class="col">
  <div class="card">
    <div class="card-header">
      User Candy Framework Upgrades
    </div>
    <div class="card-body">
      <strong><font color=red>Warning!  Do not close browser or refresh the page.  Doing so can break the Framework.</font></strong><hr>
      <div class="progress">
        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
      <hr>
      <div class="col">
        <div id="message"></div>
      </div>
      <button id="btn-upgrade" class="btn btn-success">Run UserCandy Framework Upgrade</button>
    </div>
  </div>
</div>

<script>
  var timer;
  var bar = $(".progress-bar");
  var status;
  // The function to refresh the progress bar.
  function refreshProgress() {
    // We use Ajax again to check the progress by calling the checker script.
    // Also pass the session id to read the file because the file which storing the progress is placed in a file per session.
    // If the call was success, display the progress bar.
    var token_dispenser = "<?=$data['csrfToken']?>";
    var folder = "<?=$folder?>";
    var submit = "true";
    $.ajax({
      url     : '/AdminPanel-FrameworkProcess',
      type    : 'post',
      data    : {token_dispenser:token_dispenser,folder:folder,submit:submit},
      success : function(rec){
        bar.attr("aria-valuenow", rec.percent);
        bar.css("width", rec.percent + "%");
        if(rec.status == "success"){
          status = '<font color="green">Success</font>';
        }else{
          status = '<font color="red">Fail</font>';
        }
        $("#message").append("<li>" + rec.message + " - " + rec.details + " - " + status + "</li>");
        // If the process is completed, we should stop the checking process.
        if (rec.percent == 100) {
          window.clearInterval(timer);
          timer = window.setInterval(completed, 1000);
        }
        // Stop the the process if update num is null
        if (rec.udpate_num == "null") {
          window.clearInterval(timer);
          timer = window.setInterval(error, 1000);
        }
      }
    });
  }
  function completed() {
    $("#message").append("<hr><strong>UserCandy Framework Upgrade Completed</strong>");
    $("#message").append("<hr><a class='btn btn-success' href='<?=SITE_URL?>AdminPanel-Dispenser/Framework/'>Check for More Updates</button>");
    window.clearInterval(timer);
  }
  function error() {
    $("#message").append("<hr><strong><font color=red>UserCandy Framework Upgrade Encountered an Error and may not have installed correctly.</font></strong>");
    window.clearInterval(timer);
  }
  /** Run update when upgrade button is clicked **/
  $(document).ready(function() {
    $('#btn-upgrade').click(function(){
      $(this).hide();
      // Refresh the progress bar every 1 second.
      timer = window.setInterval(refreshProgress, 1000);
    });
  });
</script>
