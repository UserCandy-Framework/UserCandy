<?php
/**
* Members Sidebar Widget Display
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

use Core\Language;

?>

<script>
function process()
  {
  var url="<?php echo SITE_URL; ?>Members/UN-ASC/1/" + document.getElementById("forumSearch").value;
  location.href=url;
  return false;
  }
</script>




    <div class='card mb-3'>
        <div class='card-header h4'>
            <?=Language::show('members_user_stats', 'Members'); ?>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><a href="<?php echo SITE_URL; ?>Members"><?=Language::show('members_title', 'Members'); ?>: <?php echo $data['activatedAccounts']; ?></a></li>
            <li class="list-group-item"><a href="<?php echo SITE_URL; ?>Online-Members"><?=Language::show('members_online_title', 'Members'); ?>: <?php echo $data['onlineAccounts']; ?></a></li>
        </ul>
    </div>

<?php
    // Display Search Members Form
    if($data['members_page']){
?>
        <div class='card mb-3'>
          <form onSubmit="return process();" class="form" method="post">
          <div class='card-header h4'>
            <?=Language::show('search_members', 'Members'); ?>
          </div>
          <div class='card-body'>
            <div class="form-group">
              <input type="forumSearch" class="form-control" id="forumSearch" placeholder="<?=Language::show('search_members', 'Members'); ?>" value="<?php if(isset($data['search'])){ echo $data['search']; } ?>">
            </div>
            <button type="submit" class="btn btn-secondary"><?=Language::show('search', 'Members'); ?></button>
          </div>
          </form>
        </div>
<?php } ?>
