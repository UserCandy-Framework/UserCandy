<?php
/**
* System Error View
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

?>
<div class="col-lg-12 col-md-12 col-sm-12">
	<div class="card card-danger">
		<div class="card-header h4">

			<h1>404</h1>

			<?php echo $data['error'];?>

		</div>
		<div class="card-body">

			<?php echo Language::show('404content', '404'); ?>

			<?php if($isAdmin == 'true'){ // Display Admin Panel Links if User Is Admin ?>
				<a href='<?php echo SITE_URL; ?>AdminPanel-PagesPermissions' title='Open Admin Panel Pages Permissions' class='btn btn-warning btn-block btn-sm'> <span class='fa fa-fw fa-cog' aria-hidden='true'></span> You are Admin. Check Pages in Admin Panel for new Pages.</a>
			<?php } ?>

		</div>
	</div>
</div>
