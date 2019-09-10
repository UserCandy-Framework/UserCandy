<?php
/**
* About View
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

$data['title'] = Language::show('uc_about', 'Welcome');

?>

<div class="col-lg-12 col-md-12 col-sm-12">
	<div class="card mb-3">
        <div class="card-header h4">
            <?=$data['title'];?>
        </div>
        <div class="card-body">
            <p><?php echo Language::show('aboutMessage', 'Welcome'); ?></p>
			<p><a href="../Home/" class="btn btn-primary btn-sm"><?php echo Language::show('openHome', 'Welcome'); ?></a>
            <a href="../Contact/" class="btn btn-primary btn-sm"><?php echo Language::show('openContact', 'Welcome'); ?></a></p>
        </div>
    </div>
</div>
