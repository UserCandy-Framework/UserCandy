<?php
/**
* Site Terms Page
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

/** Set the Basic Page Data **/
$AdminPanelModel = new AdminPanelModel();
$data['title'] = Language::show('terms_title', 'Welcome');
$data['bodyText'] = $AdminPanelModel->getSettings('site_terms_content');

?>

<div class="col-lg-12 col-md-12 col-sm-12">
	<div class="card mb-3">
        <div class="card-header h4">
            <?=$data['title'];?>
        </div>
        <div class="card-body">
						<?=$data['bodyText']?>
        </div>
    </div>
</div>
<br><br>
