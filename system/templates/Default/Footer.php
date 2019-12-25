<?php
/**
* Default Footer
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

use Core\Language;
use Helpers\Assets;

?>
</div>
</div><!-- /.container -->

<?php
/* Load Top Extender for Footer */
Core\Extender::load_ext('defaultFooter', 'top');
?>
                    <!-- Footer (sticky) -->
                    <footer class='footer'>
                        <div class='container'>
                            <span class='text-muted text-center'>

                                <!-- Footer links / text -->
                    						<?=Language::show('uc_poweredby', 'Welcome');?> <a href='https://www.usercandy.com' title='View UserCandy Website' ALT='UserCandy' target='_blank'>UserCandy</a>
                                 |
                    						<!-- Display Copywrite stuff with auto year -->
                    						 &copy; <?php echo date("Y") ?> <?php echo SITE_TITLE;?> <?=Language::show('uc_all_rights', 'Welcome');?>.
                                 |
                                <!-- Terms / Privacy Links -->
                                <?php if($data['terms_enabled'] == true){ ?>
                     						<a href='<?=SITE_URL?>Terms'><?=Language::show('terms_title', 'Welcome');?></a>
                                 |
                                <?php } if($data['privacy_enabled'] == true){ ?>
                                <a href='<?=SITE_URL?>Privacy'><?=Language::show('privacy_title', 'Welcome');?></a>
                                 |
                                <?php } ?>
                    							<?php
                    								/** Get List of all enabled Languages **/
                    								$languages = Language::getlangs();
                    								$cur_lang_code = Language::setLang();
                    								foreach ($languages as $code => $info) {
                    									/** List Lang Links **/
                    									if($cur_lang_code == $code){
                    										echo "<strong>".$info['name']."</strong> ";
                    									}else{
                    										echo "<a href='".SITE_URL."ChangeLang/".$code."' title='".$info['info']."'>".$info['name']."</a> ";
                    									}
                    								}
                    							?>

                            </div>
                        </div>
                    </footer>

        <?=Assets::js([
            'https://code.jquery.com/jquery-3.2.1.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js',
            'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js',
            'https://use.fontawesome.com/releases/v5.9.0/js/all.js',
            'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.8.2/js/lightbox.min.js'
        ])?>
        <?=(isset($js)) ? $js : ""?>
        <?php if(isset($ownjs)){ foreach ($ownjs as $eachownjs) { echo "$eachownjs"; } } ?>
        <?=(isset($footer)) ? $footer : ""?>

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
        Core\Extender::load_ext('defaultFooter', 'bottom');
        ?>

    </body>
</html>
