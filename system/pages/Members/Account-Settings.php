<?php
/**
* Account Settings Page
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.3
*/

use Core\Language;
use Helpers\ErrorMessages;

if (!$auth->isLogged())
  /** User Not logged in - kick them out **/
  ErrorMessages::push(Language::show('user_not_logged_in', 'Auth'), 'Login');

$data['title'] = Language::show('mem_act_settings_title', 'Members');
$data['welcomeMessage'] = Language::show('mem_act_settings_welcomemessage', 'Members');

/** Setup Breadcrumbs **/
$data['breadcrumbs'] = "<li class='breadcrumb-item active'>".$data['title']."</li>";

?>

<?php
/* Load Top Extender for Account-Settings */
Core\Extender::load_ext('Account-Settings', 'top');
?>

<div class="col-lg-12 col-md-12 col-sm-12">
	<div class="card mb-3">
		<div class="card-header h4">
			<?=$data['title'];?>
		</div>
		<div class="card-body">
			<p><?=$data['welcomeMessage'];?></p>
			<hr>
			<a href='<?=SITE_URL?>Edit-Profile' rel='nofollow'><?=Language::show('mem_act_edit_profile', 'Members'); ?></a><br>
			<?=Language::show('mem_act_edit_profile_description', 'Members'); ?>
			<hr>
			<a href='<?=SITE_URL?>Edit-Profile-Images' rel='nofollow'><?=Language::show('mem_act_edit_profile_images', 'Members'); ?></a><br>
			<?=Language::show('mem_act_edit_profile_images_description', 'Members'); ?>
			<hr>
			<a href='<?=SITE_URL?>Change-Email' rel='nofollow'><?=Language::show('mem_act_change_email', 'Members'); ?></a><br>
			<?=Language::show('mem_act_change_email_description', 'Members'); ?>
			<hr>
			<a href='<?=SITE_URL?>Change-Password' rel='nofollow'><?=Language::show('mem_act_change_pass', 'Members'); ?></a><br>
			<?=Language::show('mem_act_change_pass_description', 'Members'); ?>
			<hr>
			<a href='<?=SITE_URL?>Privacy-Settings' rel='nofollow'><?=Language::show('mem_act_privacy_settings', 'Members'); ?></a><br>
			<?=Language::show('mem_act_privacy_settings_description', 'Members'); ?>

    </div>
  </div>
</div>

<?php
/* Load Bottom Extender for Account-Settings */
Core\Extender::load_ext('Account-Settings', 'bottom');
?>
