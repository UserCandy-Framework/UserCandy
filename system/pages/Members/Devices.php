<?php
/**
* Account Privacy Settings View
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

use Core\Language;
use Helpers\{SuccessMessages,ErrorMessages,Csrf,Request,Form};

if (!$auth->isLogged())
  /** User Not logged in - kick them out **/
  ErrorMessages::push(Language::show('user_not_logged_in', 'Auth'), 'Login');

  /* Load Top Extender for Privacy-Settings */
  Core\Extender::load_ext('Devices', 'top');

$data['title'] = Language::show('devices_title', 'Members');
$data['welcomeMessage'] = Language::show('devices_welcomemessage', 'Members');
$data['csrfToken'] = Csrf::makeToken('editdevices');

if (isset($_POST['submit'])) {
    if(Csrf::isTokenValid('editdevices')) {
        $action = Request::post('action');
        $id = Request::post('id');

        if($action == "Enable"){ $allow = "1"; }
        if($action == "Disable"){ $allow = "0"; }

        if($membersModel->updateUserDevice($u_id, $id, $allow)){
          SuccessMessages::push(Language::show('device_success', 'Members'), 'Devices');
        }else{
          ErrorMessages::push(Language::show('device_error', 'Members'), 'Devices');
        }
    }
}

/** Get list of User's devices **/
$users_devices = $membersModel->getUsersDevices($u_id);

/** Setup Breadcrumbs **/
$data['breadcrumbs'] = "<li class='breadcrumb-item'><a href='".SITE_URL."Account-Settings'>".Language::show('mem_act_settings_title', 'Members')."</a></li><li class='breadcrumb-item active'>".$data['title']."</li>";

?>

<div class="col-lg-12 col-md-12 col-sm-12">
	<div class="card mb-3">
		<div class="card-header h4">
			<?=$data['title'];?>
		</div>
    <div class="card-body">
      <?=$data['welcomeMessage'];?>
    </div>
			<table class='table table-striped table-hover responsive'>
				<tr>
          <th align='left' class='d-none d-md-table-cell'><?=Language::show('device_browser', 'Members'); ?></th>
          <th align='left'><?=Language::show('device_os', 'Members'); ?></th>
          <th align='left'><?=Language::show('device_location', 'Members'); ?></th>
          <th align='left' class='d-none d-md-table-cell'><?=Language::show('device_device', 'Members'); ?></th>
          <th align='left'></th>
        </tr>
        <?php
          if(!empty($users_devices)){
            foreach ($users_devices as $device) {
              echo "
        				<tr>
        					<td align='left' class='d-none d-md-table-cell'>".$device->browser."</td>
                  <td align='left'>".$device->os."</td>
                  <td align='left'>".$device->city.", ".$device->state.", ".$device->country."</td>
        					<td align='left' class='d-none d-md-table-cell'>".$device->device."</td>
                  <td align='left'>";
                  if($device->allow == "1"){
                    echo Form::buttonForm(null, [['name'=>'action','value'=>'Disable'],['name'=>'id','value'=>$device->id],['name'=>'token_editdevices','value'=>$data['csrfToken']]], ['class'=>'btn btn-danger btn-sm float-right m-2','name'=>'submit','type'=>'submit','value'=>Language::show('device_disable', 'Members')]);
                  }else{
                    echo Form::buttonForm(null, [['name'=>'action','value'=>'Enable'],['name'=>'id','value'=>$device->id],['name'=>'token_editdevices','value'=>$data['csrfToken']]], ['class'=>'btn btn-success btn-sm float-right m-2','name'=>'submit','type'=>'submit','value'=>Language::show('device_enable', 'Members')]);
                  }
                  echo "</td>
        				</tr>
              ";
            }
          }
        ?>
			</table>
  </div>
</div>

<?php
/* Load Bottom Extender for Privacy-Settings */
Core\Extender::load_ext('Devices', 'Bottom');
?>
