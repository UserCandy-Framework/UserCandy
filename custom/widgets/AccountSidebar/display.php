<?php
/**
* Account Sidebar Widget Display
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

use Core\Language;

?>

  <div class='card border-primary mb-3'>
    <div class='card-header h4'>
      <?=Language::show('mem_act_settings_title', 'Members'); ?>
    </div>
    <ul class='list-group list-group-flush'>
      <li class='list-group-item'><a href='<?=SITE_URL?>Edit-Profile' rel='nofollow'><?=Language::show('mem_act_edit_profile', 'Members'); ?></a></li>
      <li class='list-group-item'><a href='<?=SITE_URL?>Edit-Profile-Images' rel='nofollow'><?=Language::show('mem_act_edit_profile_images', 'Members'); ?></a></li>
      <li class='list-group-item'><a href='<?=SITE_URL?>Change-Email' rel='nofollow'><?=Language::show('mem_act_change_email', 'Members'); ?></a></li>
      <li class='list-group-item'><a href='<?=SITE_URL?>Change-Password' rel='nofollow'><?=Language::show('mem_act_change_pass', 'Members'); ?></a></li>
      <li class='list-group-item'><a href='<?=SITE_URL?>Privacy-Settings' rel='nofollow'><?=Language::show('mem_act_privacy_settings', 'Members'); ?></a></li>
      <li class='list-group-item'><a href='<?=SITE_URL?>Devices' rel='nofollow'><?=Language::show('mem_act_devices', 'Members'); ?></a></li>
    </ul>
  </div>
