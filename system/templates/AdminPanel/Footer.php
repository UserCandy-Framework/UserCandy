<?php
/**
* Admin Panel Footer
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

use Helpers\{Assets,Url};

?>
	</div>
</div>

<?php
/* Load Top Extender for Footer */
Core\Extender::load_ext('adminFooter', 'top');
?>
<?=Assets::js([
		Url::templatePath('AdminPanel').'js/jquery.min.js',
		Url::templatePath('AdminPanel').'js/bootstrap.bundle.min.js',
		Url::templatePath('AdminPanel').'js/jquery.easing.min.js',
		Url::templatePath('AdminPanel').'js/sb-admin.min.js',
		Url::templatePath('AdminPanel').'js/lumino.glyphs.js',
		Url::templatePath('AdminPanel').'js/chart.min.js',
		'https://use.fontawesome.com/releases/v5.12.0/js/all.js',
		Url::templatePath().'js/loader.js'
]);
?>
<?php
if(isset($data['ownjs'])){
	foreach($data['ownjs'] as $ownjs){
		echo $ownjs;
	}
}
?>
<script>
	$(document).ready(function(){
		$('[data-toggle="popover"]').popover();
	});
</script>

<script type='text/javascript'>
	$(document).ready(function(){
			$('#alertModal').modal('show');
	});
</script>

<?php
/* Load Bottom Extender for Footer */
Core\Extender::load_ext('adminFooter', 'bottom');
?>

</body>
</html>
