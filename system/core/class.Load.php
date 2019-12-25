<?php
/**
* System Load Class
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

namespace Core;

use Core\Dispenser;
use Models\{AdminPanelModel,UsersModel,DispenserModel,MembersModel};
use Helpers\{AuthHelper,PageFunctions};

/**
* Load Class Loads Views Based on settings in Controllers
*/
class Load {

    /*
    ** Load View
    ** Loads files needed to display a page.
    */
    static function View($viewFile, $viewVars = array(), $template = DEFAULT_TEMPLATE, $useHeadFoot = true){
        /** Get Common User Data For Site **/
        /** initialize the AdminPanelModel **/
        $AdminPanelModel = new AdminPanelModel();
        /** initialize the AuthHelper object */
        $auth = new AuthHelper();
        /** initialize the Users object */
        $usersModel = new UsersModel();
        /** initialize the Dispenser object */
        $Dispenser = new Dispenser();
        $DispenserModel = new DispenserModel();
        /** initialize the PageFunctions object **/
        $PageFunctions = new PageFunctions();
        /** initialize Forum Stats if Installed **/
        if($DispenserModel->checkDispenserEnabled('Forum')){
          require_once(CUSTOMDIR.'plugins/Forum/helper.ForumStats.php');
        }
        /** Check to see if user is logged in **/
        if($user_data['isLoggedIn'] = $auth->isLogged()){
          /** User is logged in - Get their data **/
          $u_id = $auth->user_info();
          $user_data['currentUserData'] = $usersModel->getCurrentUserData($u_id);
          $user_data['isAdmin'] = $usersModel->checkIsAdmin($u_id);
          $user_data['current_userID'] = $u_id;
          /** Check if Site Profile Notifications are enabled **/
          $site_profile_notifi_check = $AdminPanelModel->getSettings('site_profile_notifi_check');
          if($site_profile_notifi_check == "true"){
            /** Check to see if user is missing anything in their profile **/
            $firstNameCheck = $user_data['currentUserData'][0]->firstName;
            $aboutmeCheck = $user_data['currentUserData'][0]->aboutme;
            $defaultImageCheck = $usersModel->getUserImageMain($u_id);
            if(empty($firstNameCheck)){
              $info_alert = Language::show('edit_profile_first_name_not_set', 'Members')." <a href='".SITE_URL."Edit-Profile'>".Language::show('edit_profile', 'Members')."</a>";
            }else if(empty($aboutmeCheck)){
              $info_alert = Language::show('edit_profile_aboutme_not_set', 'Members')." <a href='".SITE_URL."Edit-Profile'>".Language::show('edit_profile', 'Members')."</a>";
            }else{
              if(strpos($defaultImageCheck, 'default-') !== false){
                $info_alert = Language::show('edit_profile_default_image_not_set', 'Members')." <a href='".SITE_URL."Edit-Profile-Images'>".Language::show('mem_act_edit_profile_images', 'Members')."</a>";
              }
            }
          }
          /** Run a check to see if user has seen latest terms and privacy **/
          $terms_privacy_check = $PageFunctions->checkUserTermsPrivacy($u_id);
          if(!empty($terms_privacy_check)){ $info_alert = $terms_privacy_check; }
        }
        /** Get Data For Member Totals Stats Sidebar **/
        $membersModel = new MembersModel();
        $user_data['activatedAccounts'] = count($membersModel->getActivatedAccounts());
        $user_data['onlineAccounts'] = count($membersModel->getOnlineAccounts());
        /** Check to if Terms and Privacy are enabled **/
        $user_data['terms_enabled'] = $usersModel->checkSiteSetting('site_terms_content');
        $user_data['privacy_enabled'] = $usersModel->checkSiteSetting('site_privacy_content');
        /** Check to see if the Template is set **/
        (empty($template)) ? $template = DEFAULT_TEMPLATE : "";
        $data = array_merge($user_data, $viewVars);
        /** Extract the $data array to vars **/
        extract($user_data);
        extract($viewVars);
        /* Setup Main View File */
        if(!preg_match('/(\.php)$/i', $viewFile)){
            $viewFile .= ".php";
        }
        $viewFile = str_replace("::", "", $viewFile);
        $viewFile = $viewFile;
        /** Check to see if $viewFile is Error Page **/
        if($viewFile == 'HomeError.php'){
          $viewFile = SYSTEMDIR."pages/Home/Error.php";
        }
        /** Check for sidebar widgets based on page_id **/
        $viewFileData = explode("/", $viewFile);
        $viewFileDataSlice = array_slice($viewFileData, -2, 2);
        /** Check for Custom Page **/
        if($viewFileData[5] == "custom"){
          $cur_pagefolder = "custompages";
        }else{
          $cur_pagefolder = $viewFileDataSlice[0];
        }
        /** Remove the .php extension from the page file if it exist **/
        $cur_pagefile = str_replace(".php", "", $viewFileDataSlice[1]);
        /** Get the current page ID from the pages db **/
        $cur_page_id = $DispenserModel->getCurrentPageID($cur_pagefolder, $cur_pagefile);
        /** Check to see if a Widget is enabled for the current page **/
        if($get_widget_data = $DispenserModel->getWidgetByPage($cur_page_id)){
          /** Check to see if the widget has data **/
          if(isset($get_widget_data)){
            foreach ($get_widget_data as $widget_data) {
              if($get_dispenser_data = $DispenserModel->getDispenserData($widget_data->widget_id)){
                /** Check if widget is a sidebar **/
                if($widget_data->display_type == 'sidebar'){
                  $sidebarFileDir = CUSTOMDIR."widgets/".$get_dispenser_data[0]->folder_location."/display.php";
                  if(file_exists($sidebarFileDir)){
                    $sidebarFile = $sidebarFileDir;
                    $sidebarLocation = $widget_data->display_location;
                    /* Add the widget to the sidebar if the file exist */
                    if(!empty($sidebarFile)){
                        ($sidebarLocation == "sidebar_right") ? $rightSidebar[] = $sidebarFile : "";
                        ($sidebarLocation == "sidebar_left") ? $leftSidebar[] = $sidebarFile : "";
                    }
                  }
                }
              }
            }
          }
        }
        /* Setup Template Files */
        if($_POST['hide_head_foot'] == "true"){
            $templateHeader = "";
            $templateFooter = "";
        }else if($useHeadFoot === true){
          /** Check to see if is Custom Template **/
          if($template == "Default" || $template == "AdminPanel"){
            $templateHeader = SYSTEMDIR."templates/".$template."/Header.php";
            $templateFooter = SYSTEMDIR."templates/".$template."/Footer.php";
          }else{
            /** Make sure Custom Template is enabled, if not then use default **/
            if($DispenserModel->checkDispenserEnabled($template)){
              $templateHeader = CUSTOMDIR."templates/".$template."/Header.php";
              $templateFooter = CUSTOMDIR."templates/".$template."/Footer.php";
            }else{
              $templateHeader = CUSTOMDIR."templates/".DEFAULT_TEMPLATE."/Header.php";
              $templateFooter = CUSTOMDIR."templates/".DEFAULT_TEMPLATE."/Footer.php";
            }
          }
        }else{
            $templateHeader = "";
            $templateFooter = "";
        }
        /* Load files needed to make the page work */
        /* Load Header File if exists */
        (file_exists($templateHeader)) ? require_once $templateHeader : "";
        /* Check for Left Sidebar and load files if needed */
        if(isset($leftSidebar)){
          echo "<div class='col-lg-3 col-md-3 col-sm-12'>";
          foreach ($leftSidebar as $lsb) {
            (file_exists($lsb)) ? require_once $lsb : "";
          }
          echo "</div>";
        }
        /* Add Col if Sidebars are set */
        if(isset($leftSidebar) && isset($rightSidebar)){
          echo "<div class='col-lg-6 col-md-6 col-sm-12 p-0 m-0'>";
        }else if(isset($leftSidebar) || isset($rightSidebar)){
          echo "<div class='col-lg-9 col-md-9 col-sm-12 p-0 m-0'>";
        }
        /* Load Display Page File */
        require_once $viewFile;
        /* Add Col if Sidebars are set */
        if(isset($leftSidebar) || isset($rightSidebar)){
          echo "</div>";
        }
        /* Check for Left Sidebar and load files if needed */
        if(isset($rightSidebar)){
          echo "<div class='col-lg-3 col-md-3 col-sm-12'>";
          foreach ($rightSidebar as $rsb) {
            (file_exists($rsb)) ? require_once $rsb : "";
          }
          echo "</div>";
        }
        /* Load Footer File */
        (file_exists($templateFooter)) ? require_once $templateFooter : "";
        /** Make sure the Template Header File exists before setting up Meta Data **/
        if(file_exists($templateHeader)){
          /** Get Meta Data From Page **/
          $current_page_url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          if(!empty($data['title'])){ $meta['title'] = $data['title']; }
          if(!empty($data['site_description'])){ $meta['description'] = $data['site_description']; }
          if(!empty($data['site_keywords'])){ $meta['keywords'] = $data['site_keywords']; }
          (empty($meta['title'])) ? $meta['title'] = SITE_TITLE : $meta['title'] = $meta['title'];
          (empty($meta['description'])) ? $meta['description'] = SITE_DESCRIPTION : $meta['description'] = $meta['description'];
          (empty($meta['keywords'])) ? $meta['keywords'] = SITE_KEYWORDS : $meta['keywords'] = $meta['keywords'];
          (empty($meta['image'])) ? $meta['image'] = "" : $meta['image'] = $meta['image'];
          (empty($data['breadcrumbs'])) ? $data['breadcrumbs'] = "" : $data['breadcrumbs'] = $data['breadcrumbs'];
          /** Send Meta Data to DB For Future Use **/
          $PageFunctions->checkUpdateMetaData($current_page_url, $meta['title'], $meta['description'], $meta['keywords'], $meta['image'], $data['breadcrumbs']);
        }
    }
}
