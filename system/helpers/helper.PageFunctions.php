<?php
/**
* Page Functions Plugin
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

namespace Helpers;

use Core\Language;
use Helpers\Database;
use Models\AdminPanelModel;

class PageFunctions {

	/* Ready the DB for usage */
	private static $db;

	/* Function that Checks to see what user's previous page was before login */
	public static function prevpage(){
		/* Make sure that user is not redirected back to Login, Register, ForgotPassword, etc. */
		/* List of Pages that user should never get redirected to when logged in */
		$no_redir_pages = array("login", "register", "logout", "forgot-password", "resend-activation-email", "reset-password", "activate",
								"Login", "Register", "Logout", "Forgot-Password", "Resend-Activation-Email", "Reset-Password", "Activate",
								"Templates", "assets", "favicon.ico", "changelang", "ChangeLang");
		/* Get Current Page so we have something to compair to */
		$cur_page = $_SERVER['REQUEST_URI'];
		/* Remove the extra forward slash from link */
		$cur_page_a = ltrim($cur_page, SITE_URL);
		/* Get first part of the url (page name) */
		$cur_page_b = explode('/', $cur_page_a);
		/* Check to see if we should log as a previous page */
		if(strpos ($cur_page,"." ) === FALSE){
			if(!in_array($cur_page_b[0], $no_redir_pages)){
				$_SESSION['login_prev_page'] = $cur_page_a;
			}
		}
	}

	/* Function that gets urls based on location */
	public static function getLinks($location, $userID = 0){
		self::$db = Database::get();
		$data = self::$db->select("
				SELECT
					*
				FROM
					".PREFIX."links u
				WHERE
					location = :location
				AND
					drop_down_for = '0'
				ORDER BY link_order ASC
				",
			array(':location' => $location));
			$links_output = "";
			if(isset($data)){
				foreach ($data as $link) {
					/* Check to see if is a plugin link and if that plugin exists */
					if(isset($link->require_plugin)){
						if(!file_exists(ROOTDIR.'app/Plugins/'.$link->require_plugin.'/Controllers/'.$link->require_plugin.'.php')){
							$link_enable = false;
						}
					}
					/** Check to see if user has permission to see the link **/
					if($userID == 0 || empty($userID)){
						/** User is not logged in - Only show Public Links **/
						if($link->permission == 0){
							/** Permission Match - Show Link **/
							$link_enable = true;
						}
					}else{
						/** User is logged in - Check for permissions **/
						$user_groups = CurrentUserData::getCUGroups($userID);
						/** Check if New Member **/
						foreach ($user_groups as $user_group) {
							if($user_group->groupID >= $link->permission){
								/** Permission Match - Show Link **/
								$link_enable = true;
							}
						}
					}
					/** Check if link is enabled **/
					if($link_enable != true){
						$link_enable = false;
					}
					/** Get output for links display **/
					if($link_enable == true){
						if($link->location == "header_main"){
							$set_class = "nav-link";
							if($link->drop_down == "1"){
								$links_output .= "<li class='nav-item dropdown'>";
								$links_output .= "<a href='#' title='".$link->alt_text."' class='nav-link dropdown-toggle' data-toggle='dropdown' id='links_".$link->id."'><i class='$link->icon'></i> ".$link->title." </a>";
								$links_output .= SELF::getDropDownLinks($link->id, $userID);
								$links_output .= "</li>";
							}else{
								$links_output .= "<li><a class='$set_class' href='".SITE_URL.$link->url."' title='".$link->alt_text."'><i class='$link->icon'></i> ".$link->title." </a></li>";
							}
						}
						if($link->location == "nav_admin"){
							$set_class = "nav-link";
							if($link->drop_down == "1"){
								$links_output .= "<li class='nav-item' data-toggle='tooltip' data-placement='right'>";
								$links_output .= "<a href='#".$link->id."' title='".$link->alt_text."' class='nav-link nav-link-collapse collapsed' data-toggle='collapse' id='links_".$link->id."'><i class='$link->icon'></i> ".$link->title." </a>";
								$links_output .= SELF::getDropDownLinks($link->id, $userID, $link->location);
								$links_output .= "</li>";
							}else{
								$links_output .= "<li class='nav-item' data-toggle='tooltip' data-placement='right' title='Tables'><a class='$set_class' href='".SITE_URL.$link->url."' title='".$link->alt_text."'><i class='$link->icon'></i> ".$link->title." </a></li>";
							}
						}
					}
				}
			}
			(isset($links_output)) ? $links_output = $links_output : $links_output = "";
		return $links_output;
	}

	/* Function that gets urls for dropdown menus */
	public static function getDropDownLinks($drop_down_for, $userID, $location = null){
		self::$db = Database::get();
		$data = self::$db->select("
				SELECT
					*
				FROM
					".PREFIX."links u
				WHERE
					drop_down_for = :drop_down_for
				ORDER BY link_order_drop_down ASC
				",
			array(':drop_down_for' => $drop_down_for));
			$links_output = "";
			if(isset($data)){
				if($location == "nav_admin"){
					$links_output .=  "<ul class='sidenav-second-level collapse rounded ml-2 mr-2' id='".$drop_down_for."'>";
					$links_output .=  "<li class='nav-item' data-toggle='tooltip' data-placement='right' title='Tables'>";
				}else{
					$links_output .= "<div class='dropdown-menu' aria-labelledby='links_".$drop_down_for."'>";
				}
				foreach ($data as $link) {
					/* Check to see if is a plugin link and if that plugin exists */
					if(isset($link->require_plugin)){
						if(!file_exists(ROOTDIR.'app/Plugins/'.$link->require_plugin.'/Controllers/'.$link->require_plugin.'.php')){
							$link_enable = false;
						}
					}
					/** Check to see if user has permission to see the link **/
					if($userID == 0 || empty($userID)){
						/** User is not logged in - Only show Public Links **/
						if($link->permission == 0){
							/** Permission Match - Show Link **/
							$link_enable = true;
						}
					}else{
						/** User is logged in - Check for permissions **/
						$user_groups = CurrentUserData::getCUGroups($userID);
						/** Check if New Member **/
						foreach ($user_groups as $user_group) {
							if($user_group->groupID >= $link->permission){
								/** Permission Match - Show Link **/
								$link_enable = true;
							}
						}
					}
					/** Check if link is enabled **/
					if($link_enable != true){
						$link_enable = false;
					}
					if($link_enable == true){
						if($location == "nav_admin"){
							$links_output .= "<a class='nav-link' href='".SITE_URL.$link->url."' title='".$link->alt_text."'><i class='$link->icon'></i> ".$link->title." </a>";
						}else{
							$links_output .= "<a class='dropdown-item' href='".SITE_URL.$link->url."' title='".$link->alt_text."'><i class='$link->icon'></i> ".$link->title." </a>";
						}
					}
				}
				if($location == "nav_admin"){
					$links_output .= "</li>";
					$links_output .= "</ul>";
				}else{
					$links_output .= "</div>";
				}
			}
			(isset($links_output)) ? $links_output = $links_output : $links_output = "";
		return $links_output;
	}

	/** Popover Function to display bootstrap popover **/
	public static function displayPopover($title, $content, $form = false, $class = 'btn-info'){
		if($form == true){ $display .= "<div class='input-group-append'>"; }
		$display .= "<button type='button' class='$class' data-toggle='popover' data-trigger='focus' title='$title' data-content='$content'><i class='far fa-question-circle'></i></button>";
		if($form == true){ $display .= "</div>"; }
		return $display;
	}

	/** Get current pages's groups **/
  public static function getPagesGroups($page_id){
    self::$db = Database::get();
    $pages_groups = self::$db->select("
        SELECT
          pp.page_id, pp.group_id, g.groupID, g.groupName, g.groupDescription, g.groupFontColor, g.groupFontWeight
        FROM
          ".PREFIX."pages_permissions pp
        LEFT JOIN
          ".PREFIX."groups g
          ON g.groupID = pp.group_id
        WHERE
          pp.page_id = :page_id
        GROUP BY
          g.groupName
        ",
      array(':page_id' => $page_id));
    return $pages_groups;
  }

	/** Get current pages's groups ID numbers **/
  public static function getPagesGroupsID($page_id){
    self::$db = Database::get();
    $pages_groups = self::$db->select("
        SELECT
          pp.group_id, g.groupID
        FROM
          ".PREFIX."pages_permissions pp
        LEFT JOIN
          ".PREFIX."groups g
          ON g.groupID = pp.group_id
        WHERE
          pp.page_id = :page_id
        GROUP BY
          g.groupName
        ",
      array(':page_id' => $page_id));
    return $pages_groups;
  }

	/**
	 * Get current user's Group
	 */
	public static function getPageGroupName($where_id){
		self::$db = Database::get();
		// Get user's group ID
		$data = self::$db->select("SELECT group_id FROM ".PREFIX."pages_permissions WHERE page_id = :page_id ORDER BY group_id ASC",
			array(':page_id' => $where_id));
		$groupOutput = "";
		foreach($data as $row){
			/** Check to see if Public **/
			if($row->group_id > 0){
				// Use group ID to get the group name
				$data2 = self::$db->select("SELECT groupName, groupFontColor, groupFontWeight FROM ".PREFIX."groups WHERE groupID = :groupID",
					array(':groupID' => $row->group_id));
				$groupName = $data2[0]->groupName;
				$groupColor = "color='".$data2[0]->groupFontColor."'";
				$groupWeight = "style='font-weight:".$data2[0]->groupFontWeight."'";
			}else{
				$groupName = "Public";
				$groupColor = "color=''";
				$groupWeight = "style='font-weight: normal'";
			}
			// Format the output with font style
			$groupOutput .= " <font $groupColor $groupWeight>$groupName</font> ";
		}
		return $groupOutput;
	}

	/** Get current pages's groups **/
  public static function checkPageGroup($page_id = null, $group_id = null){
    self::$db = Database::get();
    $data = self::$db->selectCount("
        SELECT
          *
        FROM
          ".PREFIX."pages_permissions
        WHERE
          page_id = :page_id
				AND
					group_id = :group_id
        GROUP BY
          page_id
        ",
      array(':page_id' => $page_id, ':group_id' => $group_id));
		if($data > 0){
			return true;
		}else{
			return false;
		}
  }

	/** Check if user can view given page **/
	public static function systemPagePermission($u_id = null){
		/** Setup DB **/
		self::$db = Database::get();
		/** Set to block page by default **/
		$allow_page = false;
		/** Get URL input from browser **/
		if(Request::get('url') !== null){
			$url = Request::get('url');
			$url = rtrim($url,'/');
			$parts = explode("/", $url);
			$page_url = (isset($parts[0])) ? $parts[0] : "";
		}else{
			$page_url = 'Home';
		}
		/** Get Page ID from Pages Permissions **/
		$get_page_id = self::$db->select("SELECT id FROM ".PREFIX."pages WHERE url = :url ORDER BY url ASC LIMIT 1",
			array(':url' => $page_url));
		/** Get Page Permissions **/
		$get_page_groups = self::getPagesGroupsID($get_page_id[0]->id);
		/** Check to see if page permission is public **/
		foreach ($get_page_groups as $key => $value) {
			/** Setup page perms array **/
			$page_perms[] = $value->group_id;
		}
		/** Allow page if page Permissions set to Public **/
		if(isset($page_perms)){
			if(in_array(0, $page_perms)){
				$allow_page = true;
			}else{
				/** Get User's Groups **/
	      $current_user_groups = self::$db->select("
	        SELECT
	          ug.userID,
	          ug.groupID
	        FROM
	          ".PREFIX."users_groups ug
	        WHERE
	          ug.userID = :userID
	        ORDER BY
	          ug.userID ASC
	      ",
	        array(':userID' => $u_id));
				/** Get User's Groups and put groupIDs in array **/
	      if(!empty($current_user_groups)){
	        foreach($current_user_groups as $user_group_data){
	          $cu_groupID[] = $user_group_data->groupID;
	        }
	        /** Check for matches User Group and Page Group Permissions **/
	        foreach ($page_perms as $key) {
	          if(in_array($key,$cu_groupID)){$allow_page=true;}
	        }
				}
			}
			/** Check if user is not allowed to view the page **/
			if($allow_page == false){
				/** Check to see if user is logged in **/
				if($u_id > 0){
					/** User is logged in - send them to home page **/
					ErrorMessages::push('You Do Not have permission to view that page!', '');
				}else{
					/** User Not logged in - send them to login page **/
					ErrorMessages::push('You Must be Logged In to view that page!', 'Login');
				}
			}
		}
	}

	/** Check if current user has viewed the latest Terms and Privacy updates **/
	public static function checkUserTermsPrivacy($u_id = null){
		if(!empty($u_id)){
			/** Get users terms view timestamp **/
			$AdminPanelModel = new AdminPanelModel();
			$user_terms_view = CurrentUserData::getUserTermsUpdate($u_id);
			$site_terms_date = $AdminPanelModel->getSettingsTimestamp('site_terms_content');
			$user_privacy_view = CurrentUserData::getUserPrivacyUpdate($u_id);
			$site_privacy_date = $AdminPanelModel->getSettingsTimestamp('site_privacy_content');
			if($site_terms_date > $user_terms_view){
				$display_data = self::displayTermsPrivacy('Terms');
			}else if($site_privacy_date > $user_privacy_view){
				$display_data = self::displayTermsPrivacy('Privacy');
			}

			return $display_data;
		}
	}

	public static function displayTermsPrivacy($type = null){
		if($type == "Terms"){
			$tp_data = Language::show('terms_updated', 'Auth');
		}else if($type == "Privacy"){
			$tp_data = Language::show('privacy_updated', 'Auth');
		}
		if(!empty($tp_data)){
			return $tp_data;
		}
	}

	/**
  * Check MetaData in DB and see if there are any changes before updating
  */
  public static function checkUpdateMetaData($url, $title = null, $description = null, $keywords = null, $image = null, $breadcrumbs = null){
		self::$db = Database::get();
    $check_url = self::$db->select("SELECT id FROM ".PREFIX."metadata WHERE url = :url LIMIT 1", array(':url' => $url));
    if(!empty($check_url)){
      $update_meta = self::$db->update(PREFIX."metadata", array('title' => $title, 'description' => $description, 'keywords' => $keywords, 'image' => $image, 'breadcrumbs' => $breadcrumbs), array('id' => $check_url[0]->id));
    }else{
      self::$db->insert(PREFIX."metadata", array('url' => $url, 'title' => $title, 'description' => $description, 'keywords' => $keywords, 'image' => $image, 'breadcrumbs' => $breadcrumbs));
    }
  }

	/**
	* Get Current Page MetaData from DB
	*/
	public static function getPageMetaData(){
		$url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		self::$db = Database::get();
		return self::$db->select("SELECT * FROM ".PREFIX."metadata WHERE url = :url LIMIT 1", array(':url' => $url));
	}

}
