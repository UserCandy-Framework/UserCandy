<?php
/**
* Default Header
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

use Core\Language;
use Helpers\{Assets,PageFunctions,Url,SuccessMessages,ErrorMessages,CurrentUserData};

    /* Load Top Extender for Header */
    Core\Extender::load_ext('defaultHeader', 'top');

    // Check to see what page is being viewed
  	// If not Home, Login, Register, etc..
  	// Send url to Session
  	PageFunctions::prevpage();

    /** Check to see if Side Wide Message is set **/
    $site_wide_message = SITE_WIDE_MESSAGE;
    if(!empty($site_wide_message)){ $info_alert = $site_wide_message; }

    $meta_output = PageFunctions::getPageMetaData();

?>

<!DOCTYPE html>
<html lang="<?php echo LANGUAGE_CODE; ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv='X-UA-Compatible' content='IE=edge'>
        <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
        <?php echo "
          <title>".SITE_TITLE." - ".$meta_output[0]->title."</title>
          <meta name=\"keywords\" content=\"{$meta_output[0]->keywords}\">
          <meta name=\"description\" content=\"{$meta_output[0]->description}\">
          <link rel=\"canonical\" href=\"".SITE_URL."\" />
          <meta property=\"og:locale\" content=\"en_US\" />
          <meta property=\"og:type\" content=\"website\" />
          <meta property=\"og:title\" content=\"{$meta_output[0]->title}\" />
          <meta property=\"og:description\" content=\"{$meta_output[0]->description}\" />
          <meta property=\"og:url\" content=\"".SITE_URL."\" />
          <meta property=\"og:site_name\" content=\"".SITE_TITLE."\" />
          <meta property=\"og:image\" content=\"{$meta_output[0]->image}\"/>
          <meta name=\"twitter:card\" content=\"summary\" />
          <meta name=\"twitter:description\" content=\"{$meta_output[0]->description}\" />
          <meta name=\"twitter:title\" content=\"{$meta_output[0]->title}\" />
        ";?>
        <link rel='shortcut icon' href='<?=Url::templatePath()?>images/favicon.ico'>
        <?php
          /** Check if SITE_THEME is not default **/
          $current_site_theme = SITE_THEME;
          if($current_site_theme != 'default'){
            echo Assets::css([
              SITE_URL.'themes/'.$current_site_theme.'/bootstrap.min.css',
              SITE_URL.'themes/'.$current_site_theme.'/uc-custom.css'
            ]);
          }else{
            echo Assets::css([
              'https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css',
              SITE_URL.'themes/'.$current_site_theme.'/uc-custom.css'
            ]);
          }
        ?>
        <?=Assets::css([
            'https://cdn.rawgit.com/google/code-prettify/master/src/prettify.css',
            'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.8.2/css/lightbox.min.css',
            Url::templatePath().'css/style.css'
        ])?>
        <?=(isset($css)) ? $css : ""?>
        <?=(isset($header)) ? $header : ""?>
    </head>
    <body class="fixed-nav sticky-footer bg-light" id="page-top">
      <div class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark">
          <a href="<?=SITE_URL?>" class="navbar-brand">
          <img class='navbar-brand' src='<?php echo Url::templatePath(); ?>images/logo.png'>
          </a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav">
              <?php echo PageFunctions::getLinks('header_main', $currentUserData[0]->userID); ?>
            </ul>

            <ul class="nav navbar-nav ml-auto">
              <?php
              /* Load Top Extender for Header */
              Core\Extender::load_ext('defaultHeader', 'navRight');
              ?>
              <?php if(!$isLoggedIn){ ?>
                <li><a class='nav-link' href="<?=SITE_URL?>Login"><i class="fas fa-fw fa-sign-in-alt"></i> <?=Language::show('login_button', 'Auth');?></a></li>
                <li><a class='nav-link' href="<?=SITE_URL?>Register"><i class="fas fa-fw fa-user-plus"></i> <?=Language::show('register_button', 'Auth');?></a></li>
              <?php }else{ ?>
                  <li class='nav-item dropdown'>
                    <a href='#' title='<?php echo $currentUserData[0]->username; ?>' class='nav-link dropdown-toggle' data-toggle='dropdown' id='themes'>
                    <i class='fas fa-fw fa-user'></i> <?php echo $currentUserData[0]->username; ?>
                    <?php
                      /** Check to see if Friends Plugin is installed, if it is show link **/
                      if($DispenserModel->checkDispenserEnabled('Friends')){
                          /** Check to see if there are any pending friend request for current user **/
                          $notifi_count_fr = CurrentUserData::getFriendRequests($currentUserData[0]->userID);
                      }
                      /** Check to see if Private Message Plugin is installed, if it is show link **/
                      if($DispenserModel->checkDispenserEnabled('Messages')){
                        /** Check to see if there are any unread messages in inbox **/
                        $notifi_count = CurrentUserData::getUnreadMessages($currentUserData[0]->userID);
                      }
                      if($notifi_count_fr >= "1" || $notifi_count >= "1"){
                          $notifi_total = $notifi_count_fr + $notifi_count;
                          if($notifi_total >= "1"){
                          echo "<span class='badge badge-light'>".$notifi_total."</span>";
                          }
                      }
                    ?>
                    <span class='caret'></span> </a>
                      <ul class='dropdown-menu dropdown-menu-right'>
                        <li>
                          <div class="navbar-login">
                            <div class="row">
                              <div class="col-4" align="center">
                                <div class="col-centered" align="center">
                                  <?php // Check to see if user has a profile image
                                    $user_image_display = CurrentUserData::getUserImage($currentUserData[0]->userID);
                                    if(!empty($user_image_display)){
                                      echo "<img src='".SITE_URL.IMG_DIR_PROFILE.$user_image_display."' class='rounded img-fluid'>";
                                    }else{
                                      echo "<span class='fas fa-user icon-size'></span>";
                                    }
                                  ?>
                                </div>
                              </div>
                              <div class="col-8">
                                <p class="h5"><?php echo $currentUserData[0]->username; if(isset($currentUserData[0]->firstName)){echo " <small class='navbar-login-text'>".$currentUserData[0]->firstName."</small>";}; if(isset($currentUserData[0]->lastName)){echo "  <small class='navbar-login-text'>".$currentUserData[0]->lastName."</small>";} ?></p>
                                <p class="h6"><?php echo $currentUserData[0]->email; ?></p>
                                <p>
                                  <a href='<?php echo SITE_URL."Profile/".$currentUserData[0]->username; ?>' title='View Your Profile' class='btn btn-primary btn-block btn-sm'> <span class='fas fa-fw fa-user' aria-hidden='true'></span> <?=Language::show('uc_view_profile', 'Welcome');?></a>
                                </p>
                              </div>
                            </div>
                          </div>
                          <li class="divider"></li>
                          <li>
                          <div class="navbar-login navbar-login-session">
                              <div class="row">
                                  <div class="col-lg-12">
                                      <p>
                        <a href='<?=SITE_URL?>Account-Settings' title='Change Your Account Settings' class='btn btn-info btn-block btn-sm'> <span class='fas fa-fw fa-briefcase' aria-hidden='true'></span> <?=Language::show('uc_account_settings', 'Welcome');?></a>
                        <?php
                          /** Check to see if Friends Plugin is installed, if it is show link **/
                          if($DispenserModel->checkDispenserEnabled('Friends')){
                              echo "<a href='".SITE_URL."Friends' title='Friends' class='btn btn-danger btn-block btn-sm'> <span class='fas fa-fw fa-user' aria-hidden='true'></span> ".Language::show('uc_friends', 'Welcome');
                                  /** Check to see if there are any pending friend requests **/
                                  $new_friend_count = CurrentUserData::getFriendRequests($currentUserData[0]->userID);
                                  if($new_friend_count >= "1"){
                                      echo " <span class='badge badge-light'>".$new_friend_count."</span>";
                                  }
                              echo " </a>";
                          }
                          /** Check to see if Private Message Plugin is installed, if it is show link **/
                          if($DispenserModel->checkDispenserEnabled('Messages')){
                            echo "<a href='".SITE_URL."Messages' title='Private Messages' class='btn btn-danger btn-block btn-sm'> <span class='fas fa-fw fa-envelope' aria-hidden='true'></span> ".Language::show('uc_private_messages', 'Welcome');
                              /** Check to see if there are any unread messages in inbox **/
                              $new_msg_count = CurrentUserData::getUnreadMessages($currentUserData[0]->userID);
                              if($new_msg_count >= "1"){
                                echo " <span class='badge badge-light'>".$new_msg_count."</span>";
                              }
                            echo " </a>";
                          }
                        ?>
                        <?php if($isAdmin == 'true'){ // Display Admin Panel Links if User Is Admin ?>
                          <a href='<?php echo SITE_URL; ?>AdminPanel' title='Open Admin Panel' class='btn btn-warning btn-block btn-sm'> <span class='fa fa-fw fa-cog' aria-hidden='true'></span> <?=Language::show('uc_admin_panel', 'Welcome');?></a>
                        <?php } ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                      </li>
                    </ul>
                  <li><a class='nav-link' href='<?php echo SITE_URL; ?>Logout'><i class="fas fa-fw fa-sign-out-alt"></i> <?=Language::show('uc_logout', 'Welcome');?></a></li>
              <?php }?>
            </ul>

          </div>
      </div>

<?php
/* Load Bottom Extender for Header */
Core\Extender::load_ext('defaultHeader', 'bottom');
?>

        <div class="container-fluid">
            <div class="row">

              <!-- Under Header Content -->
              <?php if(!empty($data['underHeader'])){ echo $data['underHeader']; } ?>

              <!-- BreadCrumbs -->
              <?php
              // Display Breadcrumbs if set
              if(!empty($meta_output[0]->breadcrumbs)){
                echo "<div class='col-lg-12 col-md-12 col-sm-12'>";
                  echo "<ol class='breadcrumb'>";
                    echo "<li class='breadcrumb-item'><a href='".SITE_URL."'>".Language::show('uc_home', 'Welcome')."</a></li>";
                    echo $meta_output[0]->breadcrumbs;
                  echo "</ol>";
                echo "</div>";
              }
              ?>

              <?php
              // Setup the Error and Success Messages Libs
              // Display Success and Error Messages if any
              echo ErrorMessages::display();
              echo SuccessMessages::display();
              if(isset($error)) { echo ErrorMessages::display_raw($error); }
              if(isset($success)) { echo SuccessMessages::display_raw($success); }
              if(isset($info_alert)) { echo SuccessMessages::display_raw_info($info_alert); }

              ?>
