<?php
/**
* Account Edit Profile View
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

if (!$auth->isLogged())
  /** User Not logged in - kick them out **/
  ErrorMessages::push(Language::show('user_not_logged_in', 'Auth'), 'Login');

$username = $membersModel->getUserName($u_id);

$main_image = $membersModel->getUserImageMain($u_id);

if(sizeof($username) > 0){
		if (isset($_POST['submit'])) {
				if(Csrf::isTokenValid('editprofile')) {
						$firstName = Request::post('firstName');
						$lastName = Request::post('lastName');
						$location = Request::post('location');
						$gender = Request::post('gender') == 'male' ? 'Male' : 'Female';
            $website = filter_var(Request::post('website'), FILTER_VALIDATE_URL);
						$aboutMe = nl2br(Request::post('aboutMe'));
						$signature = nl2br(Request::post('signature'));

						/* Check to make sure First Name does not have any html char in it */
						if($firstName != strip_tags($firstName)){
								/* Error Message Display */
								ErrorMessages::push(Language::show('edit_profile_firstname_error', 'Members'), 'Edit-Profile');
						}
						/* Check to make sure Last Name does not have any html char in it */
						if($lastName != strip_tags($lastName)){
								/* Error Message Display */
								ErrorMessages::push(Language::show('edit_profile_lastname_error', 'Members'), 'Edit-Profile');
						}
						/* Check to make sure Website url is valid */
						if (!empty($website)){
								if (filter_var('http://'.$website, FILTER_VALIDATE_URL) === FALSE) {
										/* Error Message Display */
										ErrorMessages::push(Language::show('edit_profile_website_error', 'Members'), 'Edit-Profile');
								}
						}
						/* Clean Up Aboutme and Signature from using HTML */
						$aboutMe = strip_tags($aboutMe, "<br>");
						$signature = strip_tags($signature, "<br>");

						$membersModel->updateProfile($u_id, $firstName, $lastName, $gender, $website, $aboutMe, $signature, $location);
						/** Success Message Display **/
						SuccessMessages::push(Language::show('edit_profile_success', 'Members'), 'Edit-Profile');
				}else{
						/** Error Message Display **/
						ErrorMessages::push(Language::show('edit_profile_error', 'Members'), 'Edit-Profile');
				}
			}
		}

		/** Get User data **/
		$username = $username[0]->username;
		$profile = $membersModel->getUserProfile($username);

		$data['title'] = $username . "&#146;s ".Language::show('edit_profile_title', 'Members');
		$data['profile'] = $profile[0];
		$data['csrfToken'] = Csrf::makeToken('editprofile');
		$data['main_image'] = $main_image;

		/** Setup Breadcrumbs **/
		$data['breadcrumbs'] = "<li class='breadcrumb-item'><a href='".SITE_URL."Account-Settings'>".Language::show('mem_act_settings_title', 'Members')."</a></li><li class='breadcrumb-item active'>".$data['title']."</li>";

?>

<div class="col-lg-12 col-md-12 col-sm-12">
	<div class="card mb-3">
		<div class="card-header h4">
			<?=$data['title'];?>
		</div>
		<div class="card-body">
        <div class="col-xs-12">
            <h4><?=Language::show('edit_profile', 'Members'); ?> <strong><?php echo $data['profile']->username; ?></strong></h4>
            <hr>

            <form role="form" method="post" enctype="multipart/form-data">

                <div class="input-group mb-3">
									<div class="input-group-prepend">
										<div class="input-group-text"><?=Language::show('members_profile_firstname', 'Members'); ?> </div>
									</div>
                  <input id="firstName" type="text" class="form-control" name="firstName" placeholder="<?=Language::show('members_profile_firstname', 'Members'); ?>" value="<?php echo $data['profile']->firstName; ?>">
									<div class="input-group-append">
										<div class="input-group-text"><span class="badge badge-danger"><?=Language::show('required', 'Members'); ?></span></div>
									</div>
                </div>

								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<div class="input-group-text"><?=Language::show('members_profile_lastname', 'Members'); ?> </div>
									</div>
									<input id="lastName" type="text" class="form-control" name="lastName" placeholder="<?=Language::show('members_profile_lastname', 'Members'); ?>" value="<?php echo $data['profile']->lastName; ?>">
									<div class="input-group-append">
										<div class="input-group-text"><span class="badge badge-danger"><?=Language::show('required', 'Members'); ?></span></div>
									</div>
								</div>

								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<div class="input-group-text"><?=Language::show('members_profile_location', 'Members'); ?> </div>
									</div>
									<input id="location" type="text" class="form-control" name="location" placeholder="<?=Language::show('members_profile_location', 'Members'); ?>" value="<?php echo $data['profile']->location; ?>">
								</div>

								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<div class="input-group-text"><?=Language::show('members_profile_gender', 'Members'); ?> </div>
									</div>
									<select class='custom-select' id='gender' name='gender'>
											<option value='male' <?php if($data['profile']->gender == "Male") echo "selected";?> >Male</option>
											<option value='female' <?php if($data['profile']->gender == "Female") echo "selected";?> >Female</option>
									</select>
									<div class="input-group-append">
										<div class="input-group-text"><span class="badge badge-danger"><?=Language::show('required', 'Members'); ?></span></div>
									</div>
								</div>

								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<div class="input-group-text"><?=Language::show('members_profile_website', 'Members'); ?> </div>
									</div>
									<input id="website" type="website" class="form-control" name="website" placeholder="<?=Language::show('members_profile_website', 'Members'); ?>" value="<?php echo $data['profile']->website; ?>">
								</div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
											<div class="input-group-text"><?=Language::show('edit_profile_aboutme', 'Members'); ?></div>
										</div>
                    <textarea id="aboutMe"  class="form-control" name="aboutMe" placeholder="<?=Language::show('edit_profile_aboutme', 'Members'); ?>" rows="5"><?php echo str_replace('<br />' , '', $data['profile']->aboutme); ?></textarea>
                </div>

								<?php
									/* Check to see if Private Message Module is installed, if it is show link */
									if(file_exists(ROOTDIR.'app/Plugins/Forum/Controllers/Forum.php')){
								?>
								<div class="input-group mb-3">
										<div class="input-group-prepend">
											<div class="input-group-text"><?=Language::show('edit_profile_forum_sign', 'Members'); ?> </div>
										</div>
	                  <textarea id="signature"  class="form-control" name="signature" placeholder="<?=Language::show('edit_profile_forum_sign', 'Members'); ?>" rows="5"><?php echo str_replace('<br />' , '', $data['profile']->signature); ?></textarea>
	                </div>
								<?php } ?>

                <input type="hidden" name="token_editprofile" value="<?=$data['csrfToken'];?>" />
                <input type="submit" name="submit" class="btn btn-primary" value="<?=Language::show('edit_profile_button', 'Members'); ?>">
            </form>
        </div>
    </div>
  </div>
</div>
