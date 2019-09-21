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
$MembersModel = new MembersModel();
$data['title'] = Language::show('terms_title', 'Welcome');
$data['bodyText'] = $AdminPanelModel->getSettings('site_terms_content');
$terms_agree_button_text = Language::show('terms_agree_button', 'Auth');
$user_terms_view = CurrentUserData::getUserTermsUpdate($u_id);
$site_terms_date = $AdminPanelModel->getSettingsTimestamp('site_terms_content');

/** Check for User Agreement **/
if($currentUserData[0]->userID > 0){
		if (isset($_POST['submit'])) {
				if(Csrf::isTokenValid('settings')) {
						$agree = Request::post('agree');
						if($agree == "true"){
							$MembersModel->updateUserTerms($currentUserData[0]->userID);
							/** Success Message Display **/
							SuccessMessages::push(Language::show('edit_user_terms_success', 'Auth'), '');
						}else{
							/** Error Message Display **/
							ErrorMessages::push(Language::show('edit_user_terms_error', 'Auth'), '');
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
				<?php if($site_terms_date > $user_terms_view && $currentUserData[0]->userID > 0){ ?>
					<?php echo Form::open(array('method' => 'post')); ?>
					<div class="card-footer">
						<button class="btn btn-md btn-info" name="submit" type="submit"><?=$terms_agree_button_text?></button>
					</div>
					<!-- CSRF Token and What is Being Updated -->
					<input type="hidden" name="token_settings" value="<?php echo $data['csrfToken']; ?>" />
					<input type="hidden" name="agree" value="true" />
					<?php echo Form::close(); ?>
				<?php } ?>
    </div>
</div>
<br><br>
