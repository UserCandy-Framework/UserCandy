<?php
/**
* Admin Panel Footer
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

use Helpers\{Assets,Url};

?>
	</div>
</div>

<?php
if(isset($data['ownjs'])){
	foreach($data['ownjs'] as $ownjs){
		echo $ownjs;
	}
}
?>
<?=Assets::js([
		Url::templatePath('AdminPanel').'js/jquery.min.js',
		Url::templatePath('AdminPanel').'js/bootstrap.bundle.min.js',
		Url::templatePath('AdminPanel').'js/jquery.easing.min.js',
		Url::templatePath('AdminPanel').'js/sb-admin.min.js',
		Url::templatePath('AdminPanel').'js/lumino.glyphs.js',
		Url::templatePath('AdminPanel').'js/chart.min.js',
		'https://use.fontawesome.com/releases/v5.9.0/js/all.js',
		Url::templatePath().'js/loader.js'
]);
?>
<script>
	$(document).ready(function(){
		$('[data-toggle="popover"]').popover();
	});
</script>
</body>
</html>
