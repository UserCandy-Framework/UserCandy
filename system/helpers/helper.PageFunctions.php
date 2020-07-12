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
		/** Check for page_id **/
		if(isset($page_id)){
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
	          g.groupName, pp.group_id
	        ",
	      array(':page_id' => $page_id));
	    return $pages_groups;
		}
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
		if(isset($get_page_groups)){
			foreach ($get_page_groups as $key => $value) {
				/** Setup page perms array **/
				$page_perms[] = $value->group_id;
			}
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
			$site_terms_data = $AdminPanelModel->getSettings('site_terms_content');
			$user_privacy_view = CurrentUserData::getUserPrivacyUpdate($u_id);
			$site_privacy_date = $AdminPanelModel->getSettingsTimestamp('site_privacy_content');
			$site_privacy_data = $AdminPanelModel->getSettings('site_privacy_content');
			if($site_terms_date > $user_terms_view){
				if(!empty($site_terms_data)){
					$display_data = self::displayTermsPrivacy('Terms');
				}
			}else if($site_privacy_date > $user_privacy_view){
				if(!empty($site_privacy_data)){
					$display_data = self::displayTermsPrivacy('Privacy');
				}
			}
			(isset($display_data)) ? $display_data = $display_data : $display_data = "";
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

	/**
	* Display Email Header
	*/
	public static function displayEmailHeader($title = ""){
		$AdminPanelModel = new \Models\AdminPanelModel();
		$html_data = "
		<!doctype html>
		<html>
		  <head>
		    <meta name=\"viewport\" content=\"width=device-width\" />
		    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
				<title>".$title."</title>
		    <style>
		      img {
		        border: none;
		        -ms-interpolation-mode: bicubic;
		        max-width: 100%;
		      }
		      body {
		        background-color: #f6f6f6;
		        font-family: sans-serif;
		        -webkit-font-smoothing: antialiased;
		        font-size: 14px;
		        line-height: 1.4;
		        margin: 0;
		        padding: 0;
		        -ms-text-size-adjust: 100%;
		        -webkit-text-size-adjust: 100%;
		      }

		      table {
		        border-collapse: separate;
		        mso-table-lspace: 0pt;
		        mso-table-rspace: 0pt;
		        width: 100%; }
		        table td {
		          font-family: sans-serif;
		          font-size: 14px;
		          vertical-align: top;
		      }
		      .body {
		        background-color: #f6f6f6;
		        width: 100%;
		      }
		      .container {
		        display: block;
		        margin: 0 auto !important;
		        max-width: 580px;
		        padding: 10px;
		        width: 580px;
		      }
		      .content {
		        box-sizing: border-box;
		        display: block;
		        margin: 0 auto;
		        max-width: 580px;
		        padding: 10px;
		      }
		      .main {
		        background: #ffffff;
		        border-radius: 3px;
		        width: 100%;
						box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
		      }
		      .wrapper {
		        box-sizing: border-box;
		        padding: 20px;
		      }
					.td_header {
						padding: 20px;
						background-color: #212529;
						text-align: center;
						margin: 0px;
						font-size: 35px;
						font-weight: 300;
						text-align: center;
						text-transform: capitalize;
						color: #FFFFFF;
					}
		      .content-block {
		        padding-bottom: 10px;
		        padding-top: 10px;
		      }
		      .footer {
		        clear: both;
		        margin-top: 10px;
		        text-align: center;
		        width: 100%;
		      }
		        .footer td,
		        .footer p,
		        .footer span,
		        .footer a {
		          color: #999999;
		          font-size: 12px;
		          text-align: center;
		      }
		      h1,
		      h2,
		      h3,
		      h4 {
		        color: #000000;
		        font-family: sans-serif;
		        font-weight: 400;
		        line-height: 1.4;
		        margin: 0;
		        margin-bottom: 30px;
		      }

		      h1 {
		        font-size: 35px;
		        font-weight: 300;
		        text-align: center;
		        text-transform: capitalize;
		      }

		      p,
		      ul,
		      ol {
		        font-family: sans-serif;
		        font-size: 14px;
		        font-weight: normal;
		        margin: 0;
		        margin-bottom: 15px;
		      }
		        p li,
		        ul li,
		        ol li {
		          list-style-position: inside;
		          margin-left: 5px;
		      }

		      a {
		        color: #3498db;
		        text-decoration: underline;
		      }
		      .btn {
		        box-sizing: border-box;
		        width: 100%; }
		        .btn > tbody > tr > td {
		          padding-bottom: 15px; }
		        .btn table {
		          width: auto;
		      }
		        .btn table td {
		          background-color: #ffffff;
		          border-radius: 5px;
		          text-align: center;
		      }
		        .btn {
		          background-color: #ffffff;
		          border: solid 1px #3498db;
		          border-radius: 5px;
		          box-sizing: border-box;
		          color: #3498db;
		          cursor: pointer;
		          display: inline-block;
		          font-size: 14px;
		          font-weight: bold;
		          margin: 0;
		          padding: 12px 25px;
		          text-decoration: none;
		          text-transform: capitalize;
							text-align: center;
		      }

		      .btn-primary table td {
		        background-color: #3498db;
		      }

		      .btn-primary {
		        background-color: #3498db;
		        border-color: #3498db;
		        color: #ffffff;
		      }

		      .last {
		        margin-bottom: 0;
		      }

		      .first {
		        margin-top: 0;
		      }

		      .align-center {
		        text-align: center;
		      }

		      .align-right {
		        text-align: right;
		      }

		      .align-left {
		        text-align: left;
		      }

		      .clear {
		        clear: both;
		      }

		      .mt0 {
		        margin-top: 0;
		      }

		      .mb0 {
		        margin-bottom: 0;
		      }

		      .preheader {
		        color: transparent;
		        display: none;
		        height: 0;
		        max-height: 0;
		        max-width: 0;
		        opacity: 0;
		        overflow: hidden;
		        mso-hide: all;
		        visibility: hidden;
		        width: 0;
		      }

		      .powered-by a {
		        text-decoration: none;
		      }

		      hr {
		        border: 0;
		        border-bottom: 1px solid #f6f6f6;
		        margin: 20px 0;
		      }

		      @media only screen and (max-width: 620px) {
		        table[class=body] h1 {
		          font-size: 28px !important;
		          margin-bottom: 10px !important;
		        }
		        table[class=body] p,
		        table[class=body] ul,
		        table[class=body] ol,
		        table[class=body] td,
		        table[class=body] span,
		        table[class=body] a {
		          font-size: 16px !important;
		        }
		        table[class=body] .wrapper,
		        table[class=body] .article {
		          padding: 10px !important;
		        }
		        table[class=body] .content {
		          padding: 0 !important;
		        }
		        table[class=body] .container {
		          padding: 0 !important;
		          width: 100% !important;
		        }
		        table[class=body] .main {
		          border-left-width: 0 !important;
		          border-radius: 0 !important;
		          border-right-width: 0 !important;
		        }
		        table[class=body] .btn table {
		          width: 100% !important;
		        }
		        table[class=body] .btn a {
		          width: 100% !important;
		        }
		        table[class=body] .img-responsive {
		          height: auto !important;
		          max-width: 100% !important;
		          width: auto !important;
		        }
		      }

		      @media all {
		        .ExternalClass {
		          width: 100%;
		        }
		        .ExternalClass,
		        .ExternalClass p,
		        .ExternalClass span,
		        .ExternalClass font,
		        .ExternalClass td,
		        .ExternalClass div {
		          line-height: 100%;
		        }
		        .apple-link a {
		          color: inherit !important;
		          font-family: inherit !important;
		          font-size: inherit !important;
		          font-weight: inherit !important;
		          line-height: inherit !important;
		          text-decoration: none !important;
		        }
		        #MessageViewBody a {
		          color: inherit;
		          text-decoration: none;
		          font-size: inherit;
		          font-family: inherit;
		          font-weight: inherit;
		          line-height: inherit;
		        }
		        .btn-primary table td:hover {
		          background-color: #34495e !important;
		        }
		        .btn-primary a:hover {
		          background-color: #34495e !important;
		          border-color: #34495e !important;
		        }
		      }

		    </style>
		  </head>
		  <body class=\"\">
					<table role=\"presentation\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"body\">
						<tr>
							<td>&nbsp;</td>
							<td class=\"container\">
								<div class=\"content\">

									<!-- START CENTERED WHITE CONTAINER -->
									<table role=\"presentation\" class=\"main\">
										<tr>
											<td class=\"td_header\">
												<img src=\"".$AdminPanelModel->getSettings('site_email_logo_url')."\" alt=\"".SITE_TITLE."\">
											</td>
										</tr>
										<!-- START MAIN CONTENT AREA -->
										<tr>
											<td class=\"wrapper\">
												<table role=\"presentation\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
													<tr>
														<td>
		";
		return $html_data;
	}

	/**
	* Display Email Footer
	*/
	public static function displayEmailFooter(){
		/** Check to if Terms and Privacy are enabled **/
		$usersModel = new \Models\UsersModel();
		$data['terms_enabled'] = $usersModel->checkSiteSetting('site_terms_content');
		$data['privacy_enabled'] = $usersModel->checkSiteSetting('site_privacy_content');
		/** Send HTML data **/
		$html_data = "
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</div>
							</td>
							<td>&nbsp;</td>
						</tr>
					</table>
					<center>";
					$html_data .= "&copy; ".date("Y")." ".SITE_TITLE." ".Language::show('uc_all_rights', 'Welcome').".";
					if($data['terms_enabled'] == true){
						$html_data .= " <br> ";
						$html_data .= "<a href='".SITE_URL."Terms'>".Language::show('terms_title', 'Welcome')."</a>";
					}
					if($data['privacy_enabled'] == true){
						$html_data .= " <br> ";
						$html_data .= "<a href='".SITE_URL."Privacy'>".Language::show('privacy_title', 'Welcome')."</a>";
					}
					$html_data .= "</center>
					</body>
			</html>
		";
		return $html_data;
	}


}
