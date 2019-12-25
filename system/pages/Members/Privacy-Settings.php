<?php
/**
* Account Privacy Settings View
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

use Core\Language;
use Helpers\{ErrorMessages,Csrf,Request,Form};

if (!$auth->isLogged())
  /** User Not logged in - kick them out **/
  ErrorMessages::push(Language::show('user_not_logged_in', 'Auth'), 'Login');

  /* Load Top Extender for Privacy-Settings */
  Core\Extender::load_ext('Privacy-Settings', 'top');

$data['title'] = Language::show('ps_title', 'Members');
$data['welcomeMessage'] = Language::show('ps_welcomemessage', 'Members');
$data['csrfToken'] = Csrf::makeToken('editprivacy');

if (isset($_POST['submit'])) {
    if(Csrf::isTokenValid('editprivacy')) {
        $privacy_massemail = Request::post('privacy_massemail');
        $privacy_pm = Request::post('privacy_pm');
        $data['privacy_profile'] = Request::post('privacy_profile');

        /* Load Form Submit Extender for Privacy-Settings */
        Core\Extender::load_ext('Privacy-Settings', 'formSubmit');

        if($privacy_massemail != "true"){$privacy_massemail = "false";}
        if($privacy_pm != "true"){$privacy_pm = "false";}

        if($membersModel->updateUPrivacy($u_id, $privacy_massemail, $privacy_pm, $data['privacy_profile'])){
          SuccessMessages::push(Language::show('ps_success', 'Members'), 'Privacy-Settings');
        }else{
          ErrorMessages::push(Language::show('ps_error', 'Members'), 'Privacy-Settings');
        }
    }
}
/** Check users settings to see if privacy mass email is enabled or not **/
if($data['currentUserData'][0]->privacy_massemail == "true"){
  $data['pme_checked'] = "checked";
}
/** Check users settings to see if privacy private message is enabled or not **/
if($data['currentUserData'][0]->privacy_pm == "true"){
  $data['ppm_checked'] = "checked";
}
/** Check for User Profile Privacy Setting **/
if(!empty($data['currentUserData'][0]->privacy_profile)){
  $data['privacy_profile'] = $data['currentUserData'][0]->privacy_profile;
}else{
  $data['privacy_profile'] = "Public";
}

/** Setup Breadcrumbs **/
$data['breadcrumbs'] = "<li class='breadcrumb-item'><a href='".SITE_URL."Account-Settings'>".Language::show('mem_act_settings_title', 'Members')."</a></li><li class='breadcrumb-item active'>".$data['title']."</li>";

?>

<div class="col-lg-12 col-md-12 col-sm-12">
	<div class="card mb-3">
		<div class="card-header h4">
			<?=$data['title'];?>
		</div>
		<div class="card-body">
			<p><?=$data['welcomeMessage'];?></p>
			<div class='card border-secondary mb-3'>
				<div class='card-header'>
					<?=Language::show('ps_email_settings', 'Members'); ?>
				</div>
					<?php echo Form::open(array('method' => 'post')); ?>
						<table class='table table-striped table-hover responsive'>
							<tr><th align='left'><?=Language::show('ps_setting', 'Members'); ?></th><th align='right'><?=Language::show('ps_enable', 'Members'); ?></th></tr>
							<tr>
								<td align='left'><?=Language::show('ps_admin_mail', 'Members'); ?></td>
								<td align='right'><input type='checkbox' id='pme' name='privacy_massemail' value='true' <?=$data['pme_checked']?>></td>
							</tr>
							<tr>
								<td align='left'><?=Language::show('ps_pm_mail', 'Members'); ?></td>
								<td align='right'><input type='checkbox' id='ppm' name='privacy_pm' value='true' <?=$data['ppm_checked']?>></td>
							</tr>
						</table>
			</div>
			<div class='card border-secondary mb-3'>
				<div class='card-header'>
					<?=Language::show('ps_profile_settings', 'Members'); ?>
				</div>
				<div class='card-body'>
					<?=Language::show('ps_profile_settings_description', 'Members'); ?><br><br>
					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<div class="input-group-text"><?=Language::show('ps_profile_settings', 'Members'); ?> </div>
						</div>
						<select class='custom-select' id='privacy_profile' name='privacy_profile'>
								<option value='Public' <?php if($data['privacy_profile'] == "Public") echo "selected";?> >Public (Default)</option>
								<option value='Members' <?php if($data['privacy_profile'] == "Members") echo "selected";?> >Members Only</option>
								<option value='Friends' <?php if($data['privacy_profile'] == "Friends") echo "selected";?> >Friends Only</option>
						</select>
						<div class="input-group-append">
							<div class="input-group-text"><span class="badge badge-danger"><?=Language::show('required', 'Members'); ?></span></div>
						</div>
					</div>
				</div>
			</div>

      <?php
              /* Load Form Extender for Privacy-Settings */
              Core\Extender::load_ext('Privacy-Settings', 'form');
      ?>

				<input type="hidden" name="token_editprivacy" value="<?=$data['csrfToken'];?>" />
				<input type="submit" name="submit" class="btn btn-success" value="<?=Language::show('ps_button', 'Members'); ?>">
			<?php echo Form::close(); ?>
    </div>
  </div>
</div>

<?php
/* Load Bottom Extender for Privacy-Settings */
Core\Extender::load_ext('Privacy-Settings', 'Bottom');
?>
