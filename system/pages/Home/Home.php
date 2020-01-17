<?php
/**
* Home View
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

use Core\Language;

$data['title'] = Language::show('uc_home', 'Welcome');

?>

<div class="col-lg-12 col-md-12 col-sm-12">
	<div class="card mb-3">
        <div class="card-header h4">
            <?=$data['title'];?>
        </div>
        <div class="card-body">
						<center><img src='<?=SITE_URL?>/Templates/<?=DEFAULT_TEMPLATE?>/Assets/images/UserCandyLogoLGBlack.png' class='img-fluid' /></center>
            <?php echo Language::show('homeMessage', 'Welcome'); ?><br>
            <a href="../About/" class="btn btn-primary btn-sm"><?php echo Language::show('openAbout', 'Welcome'); ?></a>
            <a href="../Contact/" class="btn btn-primary btn-sm"><?php echo Language::show('openContact', 'Welcome'); ?></a>
        </div>
    </div>
</div>
<br><br>
