<?php
/**
* Account Edit Profile Images View
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

use Core\Language;
use Helpers\{ErrorMessages,Paginator,Csrf,SuccessMessages,SimpleImage,Request,Form};

if (!$auth->isLogged())
  /** User Not logged in - kick them out **/
  ErrorMessages::push(Language::show('user_not_logged_in', 'Auth'), 'Login');

  /* Load Top Extender for Edit-Profile-Images */
  Core\Extender::load_ext('Edit-Profile-Images', 'top');

/** Get data from URL **/
(empty($viewVars[0])) ? $imageID = "" : $imageID = $viewVars[0];
(empty($viewVars[1])) ? $current_page = "1" : $current_page = $viewVars[1];

	$pages = new Paginator(USERS_PAGEINATOR_LIMIT);

        $username = $membersModel->getUserName($u_id);

        $main_image = $membersModel->getUserImageMain($u_id);

        /** Get Users Images **/
        $data['user_images'] = $membersModel->getUserImages($u_id, $pages->getLimit($current_page, USERS_PAGEINATOR_LIMIT));

        // Set total number of rows for paginator
        $total_num_images = $membersModel->getTotalImages($u_id);
        $pages->setTotal($total_num_images);

        // Send page links to view
        $pageFormat = SITE_URL."Edit-Profile-Images/View/"; // URL page where pages are
        $data['pageLinks'] = $pages->pageLinks($pageFormat, '', $current_page);
        $data['current_page_num'] = $current_page;
        if(empty($imageID) || $imageID == 'View'){
            if (isset($_POST['submit'])) {
                if(Csrf::isTokenValid('editprofile')) {
                    $userImage = Request::post('oldImg');
                    /** Ready site to upload Files **/
                    $countfiles = count($_FILES['profilePic']['name']);
                    if(!empty($_FILES['profilePic']['name'][0])){
                      for($i=0;$i<$countfiles;$i++){
                        // Check to see if an image is being uploaded
                        if(!empty($_FILES['profilePic']['tmp_name'][$i])){
                            $picture = file_exists($_FILES['profilePic']['tmp_name'][$i]) || is_uploaded_file($_FILES['profilePic']['tmp_name'][$i]) ? $_FILES ['profilePic']['tmp_name'][$i] : array ();
                            if($picture != ""){
                                // Set the User's Profile Image Directory
                                $img_dir_profile = IMG_DIR_PROFILE.$username[0]->username.'/';
        				                $check = getimagesize ( $picture );
                                // Check to make sure image is good
        						            if($check['size'] < 5000000 && $check && ($check['mime'] == "image/jpeg" || $check['mime'] == "image/png" || $check['mime'] == "image/gif")){
                                    // Check to see if Img Upload Directory Exists, if not create it
        							              if(!file_exists(ROOTDIR.$img_dir_profile))
        								                mkdir(ROOTDIR.$img_dir_profile,0777,true);
                                    // Format new image and upload it to server
        							              $image = new SimpleImage($picture);
                                    $rand_string = substr(str_shuffle(md5(time())), 0, 10);
                                    $img_name = $username[0]->username.'_PROFILE_'.$rand_string.'.jpg';
        							              $dir = $img_dir_profile.$img_name;
                                    $img_max_size = explode(',', IMG_MAX_SIZE);
        							              $image->best_fit($img_max_size[0],$img_max_size[1])->save(ROOTDIR.$dir);
        						            }else{
                                    /** Error Message Display **/
                                    ErrorMessages::push(Language::show('edit_profile_photo_error', 'Members'), 'Edit-Profile');
                                }
                                /** Check to see if Image name is set **/
                                if(!empty($img_name)){
                                    $db_image = $username[0]->username.'/'.$img_name;
                                }else{
                                    $db_image = $userImage;
                                }
                                if(!$membersModel->addUserImage($u_id, $db_image)){
                                  /* Load Form Submit Extender for Edit-Profile-Images */
                                  Core\Extender::load_ext('Edit-Profile-Images', 'formSubmit');
                                  $image_error[] = true;
                                }
                            }else{$image_error[] = true;}
                        }else{$image_error[] = true;}
                      }
                    }else{
                      $image_error[] = true;
                    }
                    /* Check for Image Errors */
                    if($image_error != true){
                        /** Success Message Display **/
                        SuccessMessages::push(Language::show('edit_profile_images_success', 'Members'), 'Edit-Profile-Images');
                    }else{
                        /* Error Message Display */
                        ErrorMessages::push(Language::show('edit_profile_photo_error', 'Members'), 'Edit-Profile-Images');
                    }
                }else{
                    /** Error Message Display **/
                    ErrorMessages::push(Language::show('edit_profile_error', 'Members'), 'Edit-Profile-Images');
                }

            }

            /** Get user data **/
            $username = $username[0]->username;
            $profile = $membersModel->getUserProfile($username);

            $data['title'] = Language::show('mem_act_edit_profile_images', 'Members');
            $data['profile'] = $profile[0];
            $data['csrfToken'] = Csrf::makeToken('editprofile');
            $data['main_image'] = $main_image;

            /** Setup Breadcrumbs **/
        		$data['breadcrumbs'] = "<li class='breadcrumb-item'><a href='".SITE_URL."Account-Settings'>".Language::show('mem_act_settings_title', 'Members')."</a></li><li class='breadcrumb-item active'>".$data['title']."</li>";

						/** Selected which section to display **/
	          $section_display = "Images";

        }else{
          /** User is editing an image **/
          $data['title'] = Language::show('mem_act_edit_profile_image', 'Members');
          $data['profile'] = $profile[0];
          $data['csrfToken'] = Csrf::makeToken('editprofile');
          $data['edit_image'] = $membersModel->getUserImage($u_id, $imageID);
          $data['main_image'] = $main_image;

          /** Check if Image requested exists and belongs to member **/
          if(empty($data['edit_image'])){
            /** Error Message Display **/
            ErrorMessages::push(Language::show('edit_profile_image_error', 'Members'), 'Edit-Profile-Images');
          }else{
            /** Check to see if user is editing a photo **/
            $data['imageID'] = $imageID;
            if (isset($_POST['submit'])) {
                if(Csrf::isTokenValid('editprofile')) {
                    /** Get Data from the POST **/
                    $image_action = Request::post('image_action');
                    $imageID = Request::post('imageID');
                    /** Check to see if user is setting an image as default or deleting **/
                    if($image_action == "default"){
                      /** Change image to default and change old default to regular **/
                      $main_image_id = $membersModel->getUserImageMainID($u_id);
                      if($membersModel->updateUserImage($u_id, $main_image_id, '0')){
                        if($membersModel->updateUserImage($u_id, $imageID, '1')){
                          /** Error Message Display **/
                          SuccessMessages::push(Language::show('edit_profile_image_success', 'Members'), 'Edit-Profile-Images');
                        }else{
                          /** Error Message Display **/
                          ErrorMessages::push(Language::show('edit_profile_image_error', 'Members'), 'Edit-Profile-Images');
                        }
                      }else{
                        /** Error Message Display **/
                        ErrorMessages::push(Language::show('edit_profile_image_error', 'Members'), 'Edit-Profile-Images');
                      }

                    }else if($image_action == "delete"){
                      /** Remove the Photo from the server and delete the image **/
                      if($data['edit_image'] == 'default-1.jpg' || $data['edit_image'] == 'default-2.jpg' || $data['edit_image'] == 'default-3.jpg' || $data['edit_image'] == 'default-4.jpg' || $data['edit_image'] == 'default-5.jpg'){
                        if($membersModel->deleteUserImage($u_id, $imageID)){
                          /** Error Message Display **/
                          SuccessMessages::push(Language::show('edit_profile_image_success', 'Members'), 'Edit-Profile-Images');
                        }
                      }else{
                        if(file_exists(ROOTDIR.IMG_DIR_PROFILE.$data['edit_image'])) {
                            unlink(ROOTDIR.IMG_DIR_PROFILE.$data['edit_image']);
                            if($membersModel->deleteUserImage($u_id, $imageID)){
                              /** Error Message Display **/
                              SuccessMessages::push(Language::show('edit_profile_image_success', 'Members'), 'Edit-Profile-Images');
                            }
                        }
                      }
                    }
                }else{
                  /** Error Message Display **/
                  ErrorMessages::push(Language::show('edit_profile_image_error', 'Members'), 'Edit-Profile-Images');
                }
            }
          }

          /** Setup Breadcrumbs **/
          $data['breadcrumbs'] = "<li class='breadcrumb-item'><a href='".SITE_URL."Account-Settings'>".Language::show('mem_act_settings_title', 'Members')."</a></li><li class='breadcrumb-item active'>".$data['title']."</li>";

          /** Selected which section to display **/
          $section_display = "Image";
				}

if($section_display == "Images"){
?>

<div class="col-lg-12 col-md-12 col-sm-12">
	<div class="card mb-3">
		<div class="card-header h4">
			<?=$data['title'];?>
		</div>
		<div class="card-body">
        <div class="col-xs-12">
            <form role="form" method="post" enctype="multipart/form-data">
								<div class="input-group mb-3">
								  <div class="input-group-prepend">
								    <span class="input-group-text" id="inputGroupFileAddon01"><?=Language::show('members_profile_new_photo', 'Members'); ?></span>
								  </div>
								  <div class="custom-file">
										<input type="file" class="custom-file-input" accept="image/jpeg, image/gif, image/x-png" id="profilePic" name="profilePic[]" aria-describedby="inputGroupFileAddon01" multiple="multiple">
								    <label class="custom-file-label" for="inputGroupFile01">Select Image Files</label>
								  </div>
								</div>
                <?php
                        /* Load Form Extender for Edit-Profile-Images */
                        Core\Extender::load_ext('Edit-Profile-Images', 'form');
                ?>
                <input type="hidden" name="token_editprofile" value="<?=$data['csrfToken'];?>" />
                <input type="submit" name="submit" class="btn btn-primary" value="<?=Language::show('edit_profile_images_button', 'Members'); ?>">
            </form>
        </div>
    </div>
  </div>

	<?php if($data['main_image'] != ""){ ?>
		<input id="oldImg" name="oldImg" type="hidden" value="<?php echo $data['main_image']; ?>"">
		<div class="card mb-3">
			<div class="card-header">
				<?=Language::show('members_profile_cur_photo', 'Members'); ?>
			</div>
			<div class="card-body text-center">
				<img alt="User Pic" src="<?php echo SITE_URL.IMG_DIR_PROFILE.$data['main_image']; ?>" class="rounded img-fluid">
			</div>
		</div>
	<?php } ?>

	<div class="card mb-3">
		<div class="card-header h4">
			<?php echo $data['profile']->username; ?>'s Images
		</div>
		<div class="card-body">
				<div class='row'>
					<?php
						if(isset($data['user_images'])){
							foreach ($data['user_images'] as $row) {
								echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-6' style='padding-bottom: 6px'>";
									echo "<a href='".SITE_URL."Edit-Profile-Images/$row->id'><img src='".SITE_URL.IMG_DIR_PROFILE."$row->userImage' class='img-thumbnail'></a>";
								echo "</div>";
							}
						}
					?>
				</div>
		</div>
		<?php
			// Check to see if there is more than one page
			if($data['pageLinks'] > "1"){
				echo "<div class='card-footer text-muted' style='text-align: center'>";
				echo $data['pageLinks'];
				echo "</div>";
			}
		?>
	</div>
</div>

<?php
}else if($section_display == "Image"){
?>

<div class="col-lg-12 col-md-12 col-sm-12">

	<?php if($data['edit_image'] != ""){ ?>
		<div class="card mb-3">
			<div class="card-header">
				<?=Language::show('members_profile_cur_photo', 'Members'); ?>
			</div>
			<div class="card-body text-center">
				<img alt="User Pic" src="<?php echo SITE_URL.IMG_DIR_PROFILE.$data['edit_image']; ?>" class="rounded img-fluid">
				<hr>
				<?php

						if($data['edit_image'] != $data['main_image']){
							/** Setup Delete Button Form **/
							$button_display = Form::open(array('method' => 'post', 'style' => 'display:inline'));
								$button_display .= " <input type='hidden' name='image_action' value='default' /> ";
								$button_display .= " <input type='hidden' name='imageID' value='$imageID' /> ";
								$button_display .= " <input type='hidden' name='token_editprofile' value='{$data['csrfToken']}' /> ";
								$button_display .= " <button type='submit' class='btn btn-primary btn-sm' value='submit' name='submit'>".Language::show('edit_profile_images_button_default', 'Members')." </button> ";
							$button_display .= Form::close();;
							echo $button_display;

							/** Setup Delete Button Form **/
							$button_display_delete = Form::open(array('method' => 'post', 'style' => 'display:inline'));
								$button_display_delete .= " <input type='hidden' name='image_action' value='delete' /> ";
								$button_display_delete .= " <input type='hidden' name='imageID' value='$imageID' /> ";
								$button_display_delete .= " <input type='hidden' name='token_editprofile' value='{$data['csrfToken']}' /> ";
								$button_display_delete .= " <button type='submit' class='btn btn-danger' value='submit' name='submit'>".Language::show('edit_profile_images_button_delete', 'Members')." </button> ";
							$button_display_delete .= Form::close();;
							echo "<a href='#DeleteModal' class='btn btn-sm btn-danger trigger-btn' data-toggle='modal'>".Language::show('edit_profile_images_button_delete', 'Members')."</a>";

							echo "
								<div class='modal fade' id='DeleteModal' tabindex='-1' role='dialog' aria-labelledby='DeleteLabel' aria-hidden='true'>
									<div class='modal-dialog' role='document'>
										<div class='modal-content'>
											<div class='modal-header'>
												<h5 class='modal-title' id='DeleteLabel'>".Language::show('ep_delete_profile_photo', 'Members')."</h5>
												<button type='button' class='close' data-dismiss='modal' aria-label='Close'>
													<span aria-hidden='true'>&times;</span>
												</button>
											</div>
											<div class='modal-body'>
												".Language::show('ep_delete_profile_photo_question', 'Members')."
											</div>
											<div class='modal-footer'>
												<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>
												$button_display_delete
											</div>
										</div>
									</div>
								</div>
							";
						}
					}

				?>
			</div>
		</div>
	</div>


<?php } ?>

<?php
/* Load Bottom Extender for Edit-Profile-Images */
Core\Extender::load_ext('Edit-Profile-Images', 'Bottom');
?>
