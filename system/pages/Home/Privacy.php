<?php
/**
* Site Privacy Page
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

use Core\Language;
use Helpers\{CurrentUserData,Csrf,SuccessMessages,ErrorMessages,Request,Form};
use Models\{AdminPanelModel,MembersModel};

/** Set the Basic Page Data **/
$AdminPanelModel = new AdminPanelModel();
$MembersModel = new MembersModel();
$data['title'] = Language::show('privacy_title', 'Welcome');
$data['bodyText'] = $AdminPanelModel->getSettings('site_privacy_content');
$privacy_agree_button_text = Language::show('privacy_agree_button', 'Auth');
$user_privacy_view = CurrentUserData::getUserPrivacyUpdate($u_id);
$site_privacy_date = $AdminPanelModel->getSettingsTimestamp('site_privacy_content');

/** Check for User Agreement **/
if($currentUserData[0]->userID > 0){
		if (isset($_POST['submit'])) {
				if(Csrf::isTokenValid('settings')) {
						$agree = Request::post('agree');
						if($agree == "true"){
							$MembersModel->updateUserPrivacy($currentUserData[0]->userID);
							/** Success Message Display **/
							SuccessMessages::push(Language::show('edit_user_privacy_success', 'Auth'), '');
						}else{
							/** Error Message Display **/
							ErrorMessages::push(Language::show('edit_user_privacy_error', 'Auth'), '');
						}
				}
		}
}

/** Setup Token for Form */
$data['csrfToken'] = Csrf::makeToken('settings');

?>

<div class="col-lg-12 col-md-12 col-sm-12">
	<div class="card mb-3">
        <div class="card-header h4">
            <?=$data['title'];?>
        </div>
        <div class="card-body forum"><?=$data['bodyText']?></div>
				<?php if($site_privacy_date > $user_privacy_view && $currentUserData[0]->userID > 0){ ?>
					<?php echo Form::open(array('method' => 'post')); ?>
					<div class="card-footer">
						<button class="btn btn-md btn-info" name="submit" type="submit"><?=$privacy_agree_button_text?></button>
					</div>
					<!-- CSRF Token and What is Being Updated -->
					<input type="hidden" name="token_settings" value="<?php echo $data['csrfToken']; ?>" />
					<input type="hidden" name="agree" value="true" />
					<?php echo Form::close(); ?>
				<?php } ?>
    </div>
</div>
<br><br>
