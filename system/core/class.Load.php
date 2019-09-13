<?php
/**
* System Load Class
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

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
        /** initialize the AuthHelper object */
        $auth = new AuthHelper();
        /** initialize the Users object */
        $usersModel = new UsersModel();
        /** initialize the Dispenser object */
        $Dispenser = new Dispenser();
        $DispenserModel = new DispenserModel();
        /** Check to see if user is logged in **/
        if($user_data['isLoggedIn'] = $auth->isLogged()){
          /** User is logged in - Get their data **/
          $u_id = $auth->user_info();
          $user_data['currentUserData'] = $usersModel->getCurrentUserData($u_id);
          $user_data['isAdmin'] = $usersModel->checkIsAdmin($u_id);
          $user_data['current_userID'] = $u_id;

          //Todo - Add setting for admin to enable or disable
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
        /** Get Data For Member Totals Stats Sidebar **/
        $membersModel = new MembersModel();
        $user_data['activatedAccounts'] = count($membersModel->getActivatedAccounts());
        $user_data['onlineAccounts'] = count($membersModel->getOnlineAccounts());
        /** Check to if Terms and Privacy are enabled **/
        $user_data['terms_enabled'] = $usersModel->checkSiteSetting('site_terms_content');
        $user_data['privacy_enabled'] = $usersModel->checkSiteSetting('site_privacy_content');

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
        $cur_pagefolder = $viewFileDataSlice[0];
        $cur_pagefile = str_replace(".php", "", $viewFileDataSlice[1]);
        $cur_page_id = $DispenserModel->getCurrentPageID($cur_pagefolder, $cur_pagefile);
//        var_dump($cur_page_id);
        if($get_widget_data = $DispenserModel->getWidgetByPage($cur_page_id)){
//          var_dump($get_widget_data);
          if(isset($get_widget_data)){
            foreach ($get_widget_data as $widget_data) {
              if($get_dispenser_data = $DispenserModel->getDispenserData($widget_data->widget_id)){
//                var_dump($get_dispenser_data);
                /** Check if widget is a sidebar **/
                if($widget_data->display_type == 'sidebar'){
                  $sidebarFileDir = CUSTOMDIR."widgets/".$get_dispenser_data[0]->folder_location."/display.php";
                  if(file_exists($sidebarFileDir)){
                    $sidebarFile = $sidebarFileDir;
                    $sidebarLocation = $widget_data->display_location;
//                  var_dump($sidebarFile, $sidebarLocation);

                    /** Put the Files into an Array **/
                    /* Setup Sidebar File */
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
        if($useHeadFoot == true){
            $templateHeader = SYSTEMDIR."templates/".$template."/Header.php";
            $templateFooter = SYSTEMDIR."templates/".$template."/Footer.php";
        }

        /* todo - setup a file checker that sends error to log file or something if something is missing */


        /* Load files needed to make the page work */
        /* Load Header File */
        (isset($templateHeader)) ? require_once $templateHeader : "";

        /* Check for Left Sidebar and load files if needed */
        if(isset($leftSidebar)){
          echo "<div class='col-lg-3 col-md-3 col-sm-12 pr-0'>";
          foreach ($leftSidebar as $lsb) {
            (isset($lsb)) ? require_once $lsb : "";
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
          echo "<div class='col-lg-3 col-md-3 col-sm-12 pl-0'>";
          foreach ($rightSidebar as $rsb) {
            (isset($rsb)) ? require_once $rsb : "";
          }
          echo "</div>";
        }

        /* Load Footer File */
        (isset($templateFooter)) ? require_once $templateFooter : "";

        /** Check to see if Meta Tags **/
        if(empty($data['site_keywords'])){ $data['site_keywords'] = SITE_KEYWORDS; }
        if(empty($data['site_description'])){ $data['site_description'] = SITE_DESCRIPTION; }

        /** Set Header Content **/
        echo "<script>
          $(document).ready(function() {
            $('head').prepend(
              '<title>".SITE_TITLE." - ".$data['title']."</title>',
              '<meta name=\"keywords\" content=\"{$data['site_keywords']}\">',
              '<meta name=\"description\" content=\"{$data['site_description']}\">',
              '<link rel=\"canonical\" href=\"".SITE_URL."\" />',
            );
          });
        </script>";
        echo "
        <meta property=\"og:locale\" content=\"en_US\" />
        <meta property=\"og:type\" content=\"website\" />
        <meta property=\"og:title\" content=\"{$data['title']}\" />
        <meta property=\"og:description\" content=\"{$data['site_description']}\" />
        <meta property=\"og:url\" content=\"".SITE_URL."\" />',
        <meta property=\"og:site_name\" content=\"".SITE_TITLE."\" />
        <meta property=\"og:image\" content=\"{$data['site_image']}\"/>
        <meta name=\"twitter:card\" content=\"summary\" />
        <meta name=\"twitter:description\" content=\"{$data['site_description']}\" />
        <meta name=\"twitter:title\" content=\"{$data['title']}\" />
        ";
        /** Load the Breadcrumbs if enabled **/
        if(isset($data['breadcrumbs'])){
          $breadcrumbs_display = "<ol class='breadcrumb'><li class='breadcrumb-item'><a href='".SITE_URL."'>".Language::show('uc_home', 'Welcome')."</a></li>{$data['breadcrumbs']}</ol>";
          echo "
            <script>
              $(document).ready(function() {
                $('#breadcrumbs').prepend(\"{$breadcrumbs_display}\");
              });
            </script>
          ";
        }

    }

    /*
    ** Load Plugin View
    ** Loads files needed to display a plugin page.
    */
    static function ViewPlugin($viewFile, $viewVars = array(), $sidebarFile = "", $pluginFolder = "", $template = DEFAULT_TEMPLATE, $useHeadFoot = true){

        /** Get Common User Data For Site **/
        /** initialise the AuthHelper object */
        $auth = new AuthHelper();
        /** initialise the Users object */
        $usersModel = new Users();
        /** Check to see if user is logged in **/
        if($user_data['isLoggedIn'] = $auth->isLogged()){
          /** User is logged in - Get their data **/
          $u_id = $auth->user_info();
          $user_data['currentUserData'] = $usersModel->getCurrentUserData($u_id);
          $user_data['isAdmin'] = $usersModel->checkIsAdmin($u_id);
          $user_data['current_userID'] = $u_id;
        }
        /** Get Data For Member Totals Stats Sidebar **/
        $membersModel = new MembersModel();
        $user_data['activatedAccounts'] = count($membersModel->getActivatedAccounts());
        $user_data['onlineAccounts'] = count($membersModel->getOnlineAccounts());

        (empty($template)) ? $template = DEFAULT_TEMPLATE : "";
        $data = array_merge($user_data, $viewVars);
        /** Extract the $data array to vars **/
        extract($user_data);
        extract($viewVars);

        /* Setup Main View File */
        $viewFileCheck = explode(".", $viewFile);
        if(!isset($viewFileCheck[1])){
            $viewFile .= ".php";
        }
        $viewFile = str_replace("::", "/", $viewFile);
        $viewFile = SYSTEMDIR."Plugins/".$pluginFolder."/Views/".$viewFile;

        /* Setup Sidebar File */
        if(!empty($sidebarFile)){
            $sidebarFileCheck = explode(".", $sidebarFile);
            $esbfc = explode("/", str_replace("::", "/", $sidebarFile));
            $sidebarLocation = $esbfc[1];
            $sidebarFile = str_replace($sidebarLocation, "", $sidebarFile);
            $sidebarFile = rtrim(rtrim($sidebarFile,'/'),'::');
            if(!isset($sidebarFileCheck[1])){
                $sidebarFile .= ".php";
            }
            $sidebarFile = str_replace("::", "/", $sidebarFile);
            if($esbfc[0] == 'AdminPanel'){
                $sidebarLocation = $esbfc[2];
                $sidebarFile = SYSTEMDIR."pages/AdminPanel/".$esbfc[1].".php";
            }else{
                $sidebarFile = SYSTEMDIR."Plugins/".$pluginFolder."/Views/".$sidebarFile;
            }
            ($sidebarLocation == "Right" || $sidebarLocation == "right") ? $rightSidebar = $sidebarFile : "";
            ($sidebarLocation == "Left" || $sidebarLocation == "left") ? $leftSidebar = $sidebarFile : "";
        }

        /* Setup Template Files */
        if($useHeadFoot == true){
            $templateHeader = SYSTEMDIR."templates/".$template."/Header.php";
            $templateFooter = SYSTEMDIR."templates/".$template."/Footer.php";
        }

        /* todo - setup a file checker that sends error to log file or something if something is missing */

        /* Check to see if Adds are enabled for current page */
        if(preg_match('/(Members)/', $data['current_page']) || preg_match('/(AdminPanel)/', $data['current_page']) || preg_match('/(Friend)/', $data['title']) || preg_match('/(Message)/', $data['title'])){
          $addsEnable = false;
        }else{
          $addsEnable = true;
        }

        /* Setup Adds if Demo is FALSE */
        $mainAddsTop = SYSTEMDIR."pages/Adds/AddsTop.php";
        $mainAddsBottom = SYSTEMDIR."pages/Adds/AddsBottom.php";
        $sidebarAddsTop = SYSTEMDIR."pages/Adds/AddsSidebarTop.php";
        $sidebarAddsBottom = SYSTEMDIR."pages/Adds/AddsSidebarBottom.php";

        /* Load files needed to make the page work */
        (isset($templateHeader)) ? require_once $templateHeader : "";
        ($addsEnable) ? require_once $mainAddsTop : "";
        if(isset($leftSidebar)){ echo "<div class='col-lg-3 col-md-4 col-sm-12'>"; }
        (isset($leftSidebar) && $addsEnable) ? require_once $sidebarAddsTop : "";
        (isset($leftSidebar)) ? require_once $leftSidebar : "";
        (isset($leftSidebar) && $addsEnable) ? require_once $sidebarAddsBottom : "";
        if(isset($leftSidebar)){ echo "</div>"; }
        require_once $viewFile;
        if(isset($rightSidebar)){ echo "<div class='col-lg-3 col-md-4 col-sm-12'>"; }
        (isset($rightSidebar) && $addsEnable) ? require_once $sidebarAddsTop : "";
        (isset($rightSidebar)) ? require_once $rightSidebar : "";
        (isset($rightSidebar) && $addsEnable) ? require_once $sidebarAddsBottom : "";
        if(isset($rightSidebar)){ echo "</div>"; }
        ($addsEnable) ? require_once $mainAddsBottom : "";
        (isset($templateFooter)) ? require_once $templateFooter : "";
    }


}
