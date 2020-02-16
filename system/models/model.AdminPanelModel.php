<?php
/**
* Admin Panel Class
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

namespace Models;

use Core\Models;

class AdminPanelModel extends Models {

  /**
  * Get Site Settings From Database
  * @param $setting
  * @return string data
  */
  public function getSettings($setting){
      $settings_data = $this->db->select("
          SELECT
              setting_data
          FROM
              ".PREFIX."settings
          WHERE
              setting_title = :setting
          ORDER BY
              setting_id DESC
      ",
      array(':setting' => $setting));
      return $settings_data[0]->setting_data;
  }

  /**
  * Get Site Settings Timestamp From Database
  * @param $setting
  * @return string data
  */
  public function getSettingsTimestamp($setting){
      $settings_data = $this->db->select("
          SELECT
              timestamp
          FROM
              ".PREFIX."settings
          WHERE
              setting_title = :setting
          ORDER BY
              setting_id DESC
      ",
      array(':setting' => $setting));
      return $settings_data[0]->timestamp;
  }

  /**
  * Check Site Setting in Database
  * @param $setting
  * @return boolean true/false
  */
  public function checkSetting($setting){
      $settings_data = $this->db->selectCount("
          SELECT
              setting_title
          FROM
              ".PREFIX."settings
          WHERE
              setting_title = :setting
      ",
      array(':setting' => $setting));

      if($settings_data > 0){
        return true;
      }else{
        return false;
      }
  }

  /**
  * Update Site Setting Data
  * @param $setting_title
  * @param $setting_data
  * @return boolean true/false
  */
	public function updateSetting($setting_title, $setting_data){
    /** Check to see if data is the same */
    $check_title = SELF::checkSetting($setting_title);
    $cur_setting = SELF::getSettings($setting_title);

    if($check_title){
      if($cur_setting == $setting_data){
        return true;
      }else{
    		/** Update Setting Data */
    		$query = $this->db->update(PREFIX.'settings', array('setting_data' => $setting_data), array('setting_title' => $setting_title));
    		if(isset($query) && $query > 0){
    			return true;
    		}else{
    			return false;
    		}
      }
    }else{
      /** Insert New Setting Data */
      $query = $this->db->insert(PREFIX.'settings', array('setting_title' => $setting_title, 'setting_data' => $setting_data));
      if(isset($query) && $query > 0){
        return true;
      }else{
        return false;
      }
    }
	}


  /**
  * Get list of all users
  * @param $orderby
  * @param $limit
  * @param $search_users
  * @return array dataset
  */
  public function getUsers($orderby, $limit = null, $search_users = ""){

    /** Set default orderby if one is not set */
    if($orderby == "ID-DESC"){
      $run_order = "userID DESC";
    }else if($orderby == "ID-ASC"){
      $run_order = "userID ASC";
    }else if($orderby == "UN-DESC"){
      $run_order = "username DESC";
    }else if($orderby == "UN-ASC"){
      $run_order = "username ASC";
    }else{
      // Default order
      $run_order = "userID ASC";
    }

    /** Check to see if admin is searching */
    if(!empty($search_users)){
      $user_data = $this->db->select("
          SELECT
            userID,
            username,
            firstName,
            lastName,
            isactive,
            LastLogin,
            SignUp
          FROM
            ".PREFIX."users
          WHERE
            username LIKE :users
          OR
            (firstName LIKE :users OR lastName LIKE :users)
          ORDER BY
            $run_order
          $limit
          ", array(':users' => '%'.$search_users.'%'));
      }else{
        $user_data = $this->db->select("
            SELECT
              userID,
              username,
              firstName,
              lastName,
              isactive,
              LastLogin,
              SignUp
            FROM
              ".PREFIX."users
            ORDER BY
              $run_order
            $limit
            ");
      }
    return $user_data;
  }

  /**
  * Get selected user's data
  * @return array dataset
  */
  public function getUser($id){
    $user_data = $this->db->select("
        SELECT
          u.*
        FROM
          ".PREFIX."users u
        WHERE
          u.userID = :userID
        ",
        array(':userID' => $id));
    return $user_data;
  }

  /**
  * Update User's Profile Data
  * @param $au_id
  * @param $au_username
  * @param $au_firstName
  * @param $au_lastName
  * @param $au_email
  * @param $au_gender
  * @param $au_website
  * @param $au_aboutme
  * @param $au_signature
  * @return boolean true/false
  */
	public function updateProfile($au_id, $au_username, $au_firstName, $au_lastName, $au_email, $au_gender, $au_website, $au_aboutme, $au_signature){
		// Format the About Me for database
		$au_aboutme = nl2br($au_aboutme);
    $au_signature = nl2br($au_signature);
		// Update users table
		$query = $this->db->update(PREFIX.'users', array('username' => $au_username, 'firstName' => $au_firstName, 'lastName' => $au_lastName, 'email' => $au_email, 'gender' => $au_gender, 'website' => $au_website, 'aboutme' => $au_aboutme, 'signature' => $au_signature), array('userID' => $au_id));
		if($query > 0){
			return true;
		}else{
			return false;
		}
	}

  /**
  * Update users isactive status
  * @param $au_id
  * @return boolean true/false
  */
  public function activateUser($au_id){
    // Update users table isactive status
		$query = $this->db->update(PREFIX.'users', array('isactive' => '1'), array('userID' => $au_id));
		if($query > 0){
			return true;
		}else{
			return false;
		}
  }

  /**
  * Update users isactive status
  * @param $au_id
  * @return boolean true/false
  */
  public function deactivateUser($au_id){
    // Update users table isactive status
		$query = $this->db->update(PREFIX.'users', array('isactive' => '0'), array('userID' => $au_id));
		if($query > 0){
			return true;
		}else{
			return false;
		}
  }

  /**
  * Gets total count of users
  * @return int
  * @return array dataset
  */
  public function getTotalUsers($search_users){
    /** Check to see if admin is searching */
    if(!empty($search_users)){
      $data = $this->db->selectCount("
          SELECT
            userID,
            username,
            firstName,
            lastName,
            isactive,
            LastLogin,
            SignUp
          FROM
            ".PREFIX."users
          WHERE
            username LIKE :users
          OR
            (firstName LIKE :users OR lastName LIKE :users)
          ", array(':users' => '%'.$search_users.'%'));
      }else{
        $data = $this->db->selectCount("
            SELECT
              *
            FROM
              ".PREFIX."users
            ");
      }
    return $data;
  }

  /**
  * Get list of all groups
  * @return array dataset
  */
  public function getAllGroups(){
    $data = $this->db->select("
        SELECT
          groupID
        FROM
          ".PREFIX."groups
        ORDER BY
          groupID
    ");
    return $data;
  }

  /**
  * Check to see if user is member of group
  * @param $userID
  * @param $groupID
  * @return boolean true/false
  */
  public function checkUserGroup($userID, $groupID){
    $data = $this->db->selectCount("
        SELECT
          userID,
          groupID
        FROM
          ".PREFIX."users_groups
        WHERE
          userID = :userID
          AND
          groupID = :groupID
        ORDER BY
          groupID DESC
        ",
        array(':userID' => $userID, ':groupID' => $groupID));
      if($data > 0){
        return true;
      }else{
        return false;
      }
  }

  /**
  * Get group data for requested group
  * @param $id
  * @return array dataset
  */
  public function getGroupData($id){
    $group_data = $this->db->select("
        SELECT
          groupID,
          groupName,
          groupFontColor,
          groupFontWeight,
          groupDescription
        FROM
          ".PREFIX."groups
        WHERE
          groupID = :groupID
        ORDER BY
          groupID DESC
        ",
        array(':groupID' => $id));
    return $group_data;
  }

  /**
  * Check to see how many groups user is a member of
  * @param $userID
  * @return boolean true/false
  */
  public function checkUserGroupsCount($userID){
    $data = $this->db->selectCount("
        SELECT
          userID
        FROM
          ".PREFIX."users_groups
        WHERE
          userID = :userID
        ",
        array(':userID' => $userID));
      if($data <= 1){
        return false;
      }else{
        return true;
      }
  }

  /**
  * Remove given user from group
  * @param $userID
  * @param $groupID
  * @return boolean true/false
  */
  public function removeFromGroup($userID, $groupID){
    $data = $this->db->delete(PREFIX.'users_groups', array('userID' => $userID, 'groupID' => $groupID));
    if($data > 0){
      return true;
    }else{
      return false;
    }
  }

  /**
  * Add given user to group
  * @param $userID
  * @param $groupID
  * @return boolean true/false
  */
  public function addToGroup($userID, $groupID){
    $data = $this->db->insert(PREFIX.'users_groups', array('userID' => $userID, 'groupID' => $groupID));
    if($data > 0){
      return true;
    }else{
      return false;
    }
  }

  /**
  * Get all groups data
  * @param $orderby
  * @return array dataset
  */
  public function getGroups($orderby){

    // Set default orderby if one is not set
    if($orderby == "ID-DESC"){
      $run_order = "groupID DESC";
    }else if($orderby == "ID-ASC"){
      $run_order = "groupID ASC";
    }else if($orderby == "UN-DESC"){
      $run_order = "groupName DESC";
    }else if($orderby == "UN-ASC"){
      $run_order = "groupName ASC";
    }else{
      // Default order
      $run_order = "groupID ASC";
    }

    $user_data = $this->db->select("
        SELECT
          groupID,
          groupName,
          groupFontColor,
          groupFontWeight,
          groupDescription
        FROM
          ".PREFIX."groups
        ORDER BY
          $run_order
        ");
    return $user_data;
  }

  /**
  * Get selected group's data
  * @param $id
  * @return array dataset
  */
  public function getGroup($id){
    $group_data = $this->db->select("
        SELECT
          groupID,
          groupName,
          groupDescription,
          groupFontColor,
          groupFontWeight
        FROM
          ".PREFIX."groups
        WHERE
          groupID = :groupID
        ORDER BY
          groupID ASC
        ",
        array(':groupID' => $id));
    return $group_data;
  }

  /**
  * Update Group's Data
  * @param $ag_groupID
  * @param $ag_groupName
  * @param $ag_groupDescription
  * @param $ag_groupFontColor
  * @param $ag_groupFontWeight
  * @return boolean true/false
  */
	public function updateGroup($ag_groupID, $ag_groupName, $ag_groupDescription, $ag_groupFontColor, $ag_groupFontWeight){
		// Update groups table
		$query = $this->db->update(PREFIX.'groups', array('groupName' => $ag_groupName, 'groupDescription' => $ag_groupDescription, 'groupFontColor' => $ag_groupFontColor, 'groupFontWeight' => $ag_groupFontWeight), array('groupID' => $ag_groupID));
		// Check to make sure something was updated
		if($query > 0){
			return true;
		}else{
			return false;
		}
	}

  /**
  * delete group
  * @param $groupID
  * @return boolean true/false
  */
  public function deleteGroup($groupID){
    $data = $this->db->delete(PREFIX.'groups', array('groupID' => $groupID));
    if($data > 0){
      return true;
    }else{
      return false;
    }
  }

  /**
   * insert new user group to database.
   * @param string $groupName Name of New User Group
   * @return int last insert ID
   */
  public function createGroup($groupName){
    $data = $this->db->insert(PREFIX.'groups', array('groupName' => $groupName));
    $new_group_id = $this->db->lastInsertId('groupID');
    if($data > 0){
      return $new_group_id;
    }else{
      return false;
    }
  }

  /**
  * Get list of data for past days
  * @param $getData
  * @param $length
  * @return array dataset
  */
  public function getPastUsersData($getData = null, $length = null){
    if($getData == 'LastLogin'){
      $data = $this->db->select("
        SELECT
          *,
          DATE_FORMAT(LastLogin, '%m/%d/%Y')
        FROM
          ".PREFIX."users
        WHERE
          LastLogin BETWEEN NOW() - INTERVAL :length DAY AND NOW()
        ORDER BY
          userID
        ", array(':length' => $length));
    }else if($getData == 'SignUp'){
      $data = $this->db->select("
        SELECT
          *,
          DATE_FORMAT(SignUp, '%m/%d/%Y')
        FROM
          ".PREFIX."users
        WHERE
          SignUp BETWEEN NOW() - INTERVAL :length DAY AND NOW()
        ORDER BY
          userID
        ", array(':length' => $length));
    }
    return $data;
  }

    /**
    * Checks database for pagefolder and pagefile
    * @param string $pagefolder
    * @param string $pagefile
    * @return boolean true/false
    */
    public function checkForRoute($pagefolder, $pagefile){
        $data = $this->db->selectCount("
            SELECT
                *
            FROM
                ".PREFIX."pages
            WHERE
                pagefolder = :pagefolder
            AND
                pagefile = :pagefile
        ", array(':pagefolder' => $pagefolder, ':pagefile' => $pagefile));
      if($data > 0){
          return true;
      }else{
          return false;
      }
    }


    /**
     * adds new Route To Database
     * @param string $pagefolder
     * @param string $pagefile
     * @return boolean true/false
     */
    public function addRoute($pagefolder, $pagefile, $url){
      $data = $this->db->insert(PREFIX.'routes', array('pagefolder' => $pagefolder, 'pagefile' => $pagefile, 'url' => $url));
      if($data > 0){
        return true;
      }else{
        return false;
      }
    }

    /**
    * checks systems routes for URL
    * @return boolean true/false
    */
    public function checkPagesURL($url){
      $data = $this->db->selectCount("
        SELECT
          *
        FROM
          ".PREFIX."pages
        WHERE url = :url
        ", array(':url' => $url));
      if($data > 0){
        return true;
      }else{
        return false;
      }
    }

    /**
    * gets all system routes from database
    * @return array dataset
    */
    public function getAllRoutes(){
        $system_routes = $this->db->select("
          SELECT
            *
          FROM
            ".PREFIX."pages
          ");
        return $system_routes;
    }


    /**
    * Checks database for pagefolder and pagefile
    * @param int $id
    * @return array dataset
    */
    public function getRoute($id){
        $data = $this->db->select("
            SELECT
                *
            FROM
                ".PREFIX."pages
            WHERE
                id = :id
            LIMIT 1
        ", array(':id' => $id));
      if(isset($data)){
          return $data;
      }else{
          return false;
      }
    }

    /**
    * adds new Route To Database
    * @param int $id
    * @param string $pagefolder
    * @param string $pagefile
    * @param string $url
    * @param string $arguments
    * @param string $enable
    * @return boolean true/false
    */
  	public function updateRoute($id, $pagefolder, $pagefile, $url, $arguments, $enable){
  		$query = $this->db->update(PREFIX.'routes', array('pagefolder' => $pagefolder, 'pagefile' => $pagefile, 'url' => $url, 'arguments' => $arguments, 'enable' => $enable), array('id' => $id));
  		if($query > 0){
  			return true;
  		}else{
  			return false;
  		}
  	}

    /**
    * Remove Route from Database
    * @param int $id
    * @return boolean true/false
    */
    public function deleteRoute($id){
      $data = $this->db->delete(PREFIX.'routes', array('id' => $id));
      if($data > 0){
        return true;
      }else{
        return false;
      }
    }

    /**
    * Gets auth logs
    * @param string $limit
    * @return array dataset
    */
    public function getAuthLogs($limit = null){
      return $this->db->select("SELECT * FROM ".PREFIX."activitylog WHERE NOT action='AUTH_CHECKSESSION' ORDER BY date DESC $limit");
    }

    /**
    * Gets total count of entries in auth logs
    * @return int count
    */
    public function getTotalAuthLogs(){
      return $this->db->selectCount("SELECT * FROM ".PREFIX."activitylog WHERE NOT action='AUTH_CHECKSESSION' ");
    }

    /**
    * Gets all Main Links that are not dropdown links
    * @param string $location
    * @return array dataset
    */
    public function getSiteLinks($location){
      return $this->db->select("SELECT * FROM ".PREFIX."links WHERE location = :location AND drop_down_for='0' ORDER BY link_order ASC", array(':location'=>$location));
    }

    /**
    * Gets id of last link order link
    * @param string $location
    * @return int data
    */
    public function getSiteLinksLastID($location){
      $last_link = $this->db->select("SELECT link_order FROM ".PREFIX."links WHERE location = :location AND drop_down_for='0' ORDER BY link_order DESC LIMIT 1", array(':location'=>$location));
      return $last_link[0]->link_order;
    }

    /**
    * update position of given object.
    * @param string $location
    * @param int $link_id
    * @return boolean true/false
    */
    public function moveUpLink($location,$link_id){
      $current_link_order = $this->db->select("SELECT link_order FROM ".PREFIX."links WHERE location = :location AND id = :id LIMIT 1", array(':location'=>$location,':id'=>$link_id));
      $old = $current_link_order[0]->link_order;
      // Moving up one spot
      $new = $old - 1;
      // Make sure this object is not already at top
      if($new > 0){
        // Update groups table
        $query = $this->db->raw("
          UPDATE
            ".PREFIX."links
          SET
            `link_order` = CASE
            WHEN (`link_order` = $old) THEN
              $new
            WHEN (`link_order` > $old and `link_order` <= $new) THEN
              `link_order`- 1
            WHEN (`link_order` < $old and `link_order` >= $new) THEN
              `link_order`+ 1
            ELSE
              `link_order`
          END
          WHERE `location` = '$location'
          ");
        // Check to make sure something was updated
        if(isset($query)){
          return true;
        }else{
          return false;
        }
      }else{
        return false;
      }
    }

    /**
    * update position of given object.
    * @param string $location
    * @param int $link_id
    * @return boolean true/false
    */
    public function moveDownLink($location,$link_id){
      $current_link_order = $this->db->select("SELECT link_order FROM ".PREFIX."links WHERE location = :location AND id = :id LIMIT 1", array(':location'=>$location,':id'=>$link_id));
      $old = $current_link_order[0]->link_order;
      // Moving down one spot
      $new = $old + 1;
      // Update groups table
      $query = $this->db->raw("
        UPDATE
          ".PREFIX."links
        SET
          `link_order` = CASE
          WHEN (`link_order` = $old) THEN
            $new
          WHEN (`link_order` < $old and `link_order` >= $new) THEN
            `link_order`+ 1
          WHEN (`link_order` > $old and `link_order` <= $new) THEN
            `link_order`- 1
          ELSE
            `link_order`
        END
        WHERE `location` = '$location'
        ");
      // Check to make sure something was updated
      if(isset($query)){
        return true;
      }else{
        return false;
      }
    }

    /**
    * Gets all Main Links that are not dropdown links
    * @param int $id
    * @return array dataset
    */
    public function getSiteLinkData($id){
      return $this->db->select("SELECT * FROM ".PREFIX."links WHERE id = :id LIMIT 1", array(':id'=>$id));
    }

    /**
    * adds new Site Link To Database
    * @param string $title
    * @param string $url
    * @param string $alt_text
    * @param string $location
    * @param string $drop_down
    * @param string $require_plugin
    * @param int $permission
    * @param string $icon
    * @return boolean true/false
    */
    public function addSiteLink($title, $url, $alt_text, $location, $drop_down, $require_plugin = null, $permission = 0, $icon = null){
      $link_order_last = SELF::getSiteLinksLastID($location);
      if(isset($link_order_last)){
        $link_order = $link_order_last + 1;
      }else{
        $link_order = "1";
      }
      $data = $this->db->insert(PREFIX.'links', array('title' => $title, 'url' => $url, 'alt_text' => $alt_text, 'location' => $location, 'drop_down' => $drop_down, 'require_plugin' => $require_plugin, 'link_order' => $link_order, 'permission' => $permission, 'icon' => $icon));
      if($data > 0){
        return $data;
      }else{
        return false;
      }
    }

    public function addSiteLinkDispenser($title, $location, $permission = 0, $icon = null, $drop_down = '0'){
      $link_order_last = SELF::getSiteLinksLastID($location);
      if(isset($link_order_last)){
        $link_order = $link_order_last + 1;
      }else{
        $link_order = "1";
      }
      $data = $this->db->insert(PREFIX.'links', array('title' => $title, 'url' => $title, 'alt_text' => $title, 'location' => $location, 'drop_down' => $drop_down, 'require_plugin' => $title, 'link_order' => $link_order, 'permission' => $permission, 'icon' => $icon));
      if($data > 0){
        return $data;
      }else{
        return false;
      }
    }

    public function deleteSiteLinkDispenser($title, $location){
      $data = $this->db->delete(PREFIX.'links', array('title' => $title, 'url' => $title, 'location' => $location));
      if($data > 0){
        return $data;
      }else{
        return false;
      }
    }

    /**
    * updates Site Link in Database
    * @param int $id
    * @param string $title
    * @param string $url
    * @param string $alt_text
    * @param string $location
    * @param int $drop_down
    * @param string $require_plugin
    * @param int $permission
    * @param string $icon
    * @return boolean true/false
    */
  	public function updateSiteLink($id, $title, $url, $alt_text, $location, $drop_down, $permission = 0, $icon = null){
  		$query = $this->db->update(PREFIX.'links', array('title' => $title, 'url' => $url, 'alt_text' => $alt_text, 'location' => $location, 'drop_down' => $drop_down, 'permission' => $permission, 'icon' => $icon), array('id' => $id));
  		if($query > 0){
  			return true;
  		}else{
  			return false;
  		}
  	}

    /**
    * Remove Site Link from Database
    * @param int $id
    * @return boolean true/false
    */
    public function deleteSiteLink($id){
      $data = $this->db->delete(PREFIX.'links', array('id' => $id));
      if($data > 0){
        $this->db->delete(PREFIX.'links', array('drop_down_for' => $id), 1000);
        return true;
      }else{
        return false;
      }
    }

    /**
    * Gets all links for Drop Down link
    * @param int $id
    * @return array dataset
    */
    public function getSiteDropDownLinks($id){
      return $this->db->select("SELECT * FROM ".PREFIX."links WHERE drop_down_for = :id ORDER BY link_order_drop_down ASC", array(':id'=>$id));
    }

    /**
    * Gets id of last link order link
    * @param int $id
    * @return string data
    */
    public function getSiteDropDownLinksLastID($id){
      $last_link = $this->db->select("SELECT link_order_drop_down FROM ".PREFIX."links WHERE drop_down_for = :id ORDER BY link_order_drop_down DESC LIMIT 1", array(':id'=>$id));
      return $last_link[0]->link_order_drop_down;
    }

    /**
    * update position of given object.
    * @param int $main_link_id
    * @param int $dd_link_id
    * @return boolean true/false
    */
    public function moveUpDDLink($main_link_id,$dd_link_id){
      $current_link_order = $this->db->select("SELECT link_order_drop_down FROM ".PREFIX."links WHERE drop_down_for = :drop_down_for AND id = :id LIMIT 1", array(':drop_down_for'=>$main_link_id,':id'=>$dd_link_id));
      $old = $current_link_order[0]->link_order_drop_down;
      // Moving up one spot
      $new = $old - 1;
      // Make sure this object is not already at top
      if($new > 0){
        // Update groups table
        $query = $this->db->raw("
          UPDATE
            ".PREFIX."links
          SET
            `link_order_drop_down` = CASE
            WHEN (`link_order_drop_down` = $old) THEN
              $new
            WHEN (`link_order_drop_down` > $old and `link_order_drop_down` <= $new) THEN
              `link_order_drop_down`- 1
            WHEN (`link_order_drop_down` < $old and `link_order_drop_down` >= $new) THEN
              `link_order_drop_down`+ 1
            ELSE
              `link_order_drop_down`
          END
          WHERE `drop_down_for` = '$main_link_id'
          ");
        // Check to make sure something was updated
        if(isset($query)){
          return true;
        }else{
          return false;
        }
      }else{
        return false;
      }
    }

    /**
    * update position of given object.
    * @param int $main_link_id
    * @param int $dd_link_id
    * @return boolean true/false
    */
    public function moveDownDDLink($main_link_id,$dd_link_id){
      $current_link_order = $this->db->select("SELECT link_order_drop_down FROM ".PREFIX."links WHERE drop_down_for = :drop_down_for AND id = :id LIMIT 1", array(':drop_down_for'=>$main_link_id,':id'=>$dd_link_id));
      $old = $current_link_order[0]->link_order_drop_down;
      // Moving down one spot
      $new = $old + 1;
      // Update groups table
      $query = $this->db->raw("
        UPDATE
          ".PREFIX."links
        SET
          `link_order_drop_down` = CASE
          WHEN (`link_order_drop_down` = $old) THEN
            $new
          WHEN (`link_order_drop_down` < $old and `link_order_drop_down` >= $new) THEN
            `link_order_drop_down`+ 1
          WHEN (`link_order_drop_down` > $old and `link_order_drop_down` <= $new) THEN
            `link_order_drop_down`- 1
          ELSE
            `link_order_drop_down`
        END
        WHERE `drop_down_for` = '$main_link_id'
        ");
      // Check to make sure something was updated
      if(isset($query)){
        return true;
      }else{
        return false;
      }
    }

    /**
    * adds new Site Drop Down Link To Database
    * @param string $title
    * @param string $url
    * @param string $alt_text
    * @param string $location
    * @param int $drop_down
    * @param string $require_plugin
    * @param int $drop_down_for
    * @param int $permission
    * @param string $icon
    * @return boolean true/false
    */
    public function addSiteDDLink($title, $url, $alt_text, $location, $drop_down, $require_plugin = null, $drop_down_for, $permission = 0, $icon = null){
      $link_order_last = SELF::getSiteDropDownLinksLastID($drop_down_for);
      if(isset($link_order_last)){
        $link_order = $link_order_last + 1;
      }else{
        $link_order = "1";
      }
      $current_link_order = $this->db->select("SELECT link_order FROM ".PREFIX."links WHERE id = :id LIMIT 1", array(':id'=>$drop_down_for));
      $get_link_order = $current_link_order[0]->link_order;
      $data = $this->db->insert(PREFIX.'links', array('title' => $title, 'url' => $url, 'alt_text' => $alt_text, 'location' => $location, 'drop_down' => $drop_down, 'require_plugin' => $require_plugin,
                                                          'link_order_drop_down' => $link_order, 'drop_down_for' => $drop_down_for, 'link_order' => $get_link_order, 'permission' => $permission, 'icon' => $icon));
      if($data > 0){
        return true;
      }else{
        return false;
      }
    }

    /**
    * Gets Link Title based on ID
    * @param int $id
    * @return string data
    */
    public function getMainLinkTitle($id){
      $data = $this->db->select("SELECT title FROM ".PREFIX."links WHERE id = :id LIMIT 1", array(':id'=>$id));
      return $data[0]->title;
    }

    /**
    * updates Site Drop Down Link in Database
    * @param int $id
    * @param string $title
    * @param string $url
    * @param string $alt_text
    * @param string $location
    * @param int $drop_down
    * @param string $require_plugin
    * @param int $permission
    * @param string $icon
    * @return boolean true/false
    */
  	public function updateSiteDDLink($id, $title, $url, $alt_text, $location, $drop_down, $require_plugin = null, $permission = 0, $icon = null){
  		$query = $this->db->update(PREFIX.'links', array('title' => $title, 'url' => $url, 'alt_text' => $alt_text, 'location' => $location, 'drop_down' => $drop_down, 'require_plugin' => $require_plugin, 'permission' => $permission, 'icon' => $icon), array('id' => $id));
  		if($query > 0){
  			return true;
  		}else{
  			return false;
  		}
  	}

    /**
    * Get the top referer URLs from site logs
    * @param int $days
    * @return array dataset
    */
    public function getTopRefer($days = '10'){
        $thissite = SITE_URL;
        $thissite = trim($thissite, '/');
        $thissite = parse_url($thissite);
        $data = $this->db->select("
          SELECT refer, COUNT(refer) as refer_count FROM `".PREFIX."sitelogs`
            WHERE server = '".$_SERVER['SERVER_NAME']."'
            AND refer != ''
            AND refer NOT LIKE :thissite
            AND refer NOT LIKE '%localhost%'
            AND timestamp >= CURDATE() - INTERVAL :days DAY
            GROUP BY refer
            ORDER BY refer_count DESC
            LIMIT 5
        ", array(':days' => $days, ':thissite' => '%'.$thissite['host'].'%'));
      if(isset($data)){
          return $data;
      }else{
          return false;
      }
    }

    /**
    * Check to see if page exist in pages database
    * @param string $pagefolder
    * @param string $pagefile
    * @return boolean true/false
    */
    public function checkForPage($pagefolder, $pagefile){
        $data = $this->db->selectCount("
            SELECT
                *
            FROM
                ".PREFIX."pages
            WHERE
                pagefolder = :pagefolder
            AND
                pagefile = :pagefile
        ", array(':pagefolder' => $pagefolder, ':pagefile' => $pagefile));
      if($data > 0){
          return true;
      }else{
          return false;
      }
    }


    /**
    * adds new Page To Database
    * @param string $pagefolder
    * @param string $pagefile
    * @param string $url
    * @return int inserted ID
    */
    public function addPage($pagefolder, $pagefile, $url){
      $data = $this->db->insert(PREFIX.'pages', array('pagefolder' => $pagefolder, 'pagefile' => $pagefile, 'url' => $url));
      if($data > 0){
        return $data;
      }else{
        return false;
      }
    }

    /**
    * adds new Page To Database
    * @param string $pagefolder
    * @param string $pagefile
    * @param string $url
    * @return int inserted ID
    */
    public function addPluginPage($pagefolder, $pagefile, $url, $arguments = '(:any)/(:any)/(:any)/(:any)', $sitemap = 'true', $template = 'Default', $headfoot = "1"){
      $data = $this->db->insert(PREFIX.'pages', array('pagefolder' => $pagefolder, 'pagefile' => $pagefile, 'url' => $url, 'arguments' => $arguments, 'sitemap' => $sitemap, 'template' => $template, 'headfoot' => $headfoot));
      if($data > 0){
        return $data;
      }else{
        return false;
      }
    }

    /**
    * adds new Page To Database
    * @param string $pagefolder
    * @param string $pagefile
    * @param string $url
    * @return int inserted ID
    */
    public function getPluginPage($pagefolder, $pagefile, $url){
      $data = $this->db->select("SELECT id FROM ".PREFIX."pages WHERE pagefolder = :pagefolder AND pagefile = :pagefile AND url = :url LIMIT 1", array('pagefolder' => $pagefolder, 'pagefile' => $pagefile, 'url' => $url));
      if($data > 0){
        return $data[0]->id;
      }else{
        return false;
      }
    }

    /**
    * gets all system pages from database
    * @param string $orderby
    * @return array dataset
    */
    public function getAllPages($orderby = null){
      // Set default orderby if one is not set
      if($orderby == "URL-DESC"){
        $run_order = "url DESC";
      }else if($orderby == "URL-ASC"){
        $run_order = "url ASC";
      }else if($orderby == "CON-DESC"){
        $run_order = "pagefolder DESC";
      }else if($orderby == "CON-ASC"){
        $run_order = "pagefolder ASC";
      }else if($orderby == "MET-DESC"){
        $run_order = "pagefile DESC";
      }else if($orderby == "MET-ASC"){
        $run_order = "pagefile ASC";
      }else{
        // Default order
        $run_order = "url ASC";
      }
      $system_pages = $this->db->select("
        SELECT
          *
        FROM
          ".PREFIX."pages
        ORDER BY
          $run_order
        ");
      return $system_pages;
    }


    /**
    * Checks database for page
    * @param int $id
    * @return array dataset
    */
    public function getPage($id){
        $data = $this->db->select("
            SELECT
                *
            FROM
                ".PREFIX."pages
            WHERE
                id = :id
            LIMIT 1
        ", array(':id' => $id));
      if(isset($data)){
          return $data;
      }else{
          return false;
      }
    }

    /**
    * updates Page in Database
    * @param int $id
    * @param string $pagefolder
    * @param string $pagefile
    * @param string $url
    * @return boolean true/false
    */
    public function updatePage($id, $pagefolder, $pagefile, $url){
      $query = $this->db->update(PREFIX.'pages', array('pagefolder' => $pagefolder, 'pagefile' => $pagefile, 'url' => $url), array('id' => $id));
      if($query > 0){
        return true;
      }else{
        return false;
      }
    }

    /**
    * Remove Page from Database
    * @param int $id
    * @return boolean true/false
    */
    public function deletePage($id){
      $data = $this->db->delete(PREFIX.'pages', array('id' => $id));
      if($data > 0){
        return true;
      }else{
        return false;
      }
    }

    /**
    * Remove Page Permissions from Database
    * @param int $id
    * @return boolean true/false
    */
    public function deletePagePermissions($id){
      $data = $this->db->delete(PREFIX.'pages_permissions', array('page_id' => $id));
      if($data > 0){
        return true;
      }else{
        return false;
      }
    }

    /**
    * adds new Page Permission To Database
    * @param int $page_id
    * @param int $group_id
    * @return boolean true/false
    */
    public function addPagePermission($page_id = null, $group_id = null){
      $data = $this->db->insert(PREFIX.'pages_permissions', array('page_id' => $page_id, 'group_id' => $group_id));
      if($data > 0){
        return true;
      }else{
        return false;
      }
    }

    /**
    * deletes Page Permission To Database
    * @param string $page_id
    * @param string $group_id
    * @return boolean true/false
    */
    public function removePagePermission($page_id = null, $group_id = null){
      $data = $this->db->delete(PREFIX.'pages_permissions', array('page_id' => $page_id, 'group_id' => $group_id));
      if($data > 0){
        return true;
      }else{
        return false;
      }
    }

    /**
    * Checks database for page
    * @param int $page_id
    * @return array dataset
    */
    public function getPageGroups($page_id){
        $data = $this->db->select("
            SELECT
              group_id
            FROM
              ".PREFIX."pages_permissions
            WHERE
              page_id = :page_id
            ORDER BY
              group_id
            ASC
        ", array(':page_id' => $page_id));
      if(isset($data)){
          return $data;
      }else{
          return false;
      }
    }

    /**
    * Checks database for page
    * @param int $page_id
    * @param int $group_id
    * @return boolean true/false
    */
    public function checkForPagePermission($page_id, $group_id){
        $data = $this->db->selectCount("
            SELECT
              group_id
            FROM
              ".PREFIX."pages_permissions
            WHERE
              page_id = :page_id
            AND
              group_id = :group_id
            ORDER BY
              group_id
            ASC
        ", array(':page_id' => $page_id, ':group_id' => $group_id));
      if($data > 0){
          return true;
      }else{
          return false;
      }
    }

    /**
    * updates system route URL
    * @param string $old_url
    * @param string $new_url
    * @return boolean true/false
    */
  	public function updateLinkURL($old_url = null, $new_url = null){
      $get_link_id = $this->db->select('SELECT id FROM '.PREFIX.'links WHERE url = :url', array(':url' => $old_url));
      foreach ($get_link_id as $key => $value) {
        $query = $this->db->update(PREFIX.'links', array('url' => $new_url), array('id' => $value->id));
      }
  		if($query > 0){
  			return true;
  		}else{
  			return false;
  		}
  	}

    /**
    * updates Page Permissions URL
    * @param string $old_url
    * @param string $new_url
    * @return boolean true/false
    */
    public function updatePagePermURL($old_url = null, $new_url = null){
      $get_page_id = $this->db->select('SELECT id FROM '.PREFIX.'pages WHERE url = :url LIMIT 1', array(':url' => $old_url));
      $query = $this->db->update(PREFIX.'pages', array('url' => $new_url), array('id' => $get_page_id[0]->id));
      if($query > 0){
        return true;
      }else{
        return false;
      }
    }

    /**
    * updates system route URL
    * @param string $old_url
    * @param string $new_url
    * @return boolean true/false
    */
    public function updateRouteURL($old_url = null, $new_url = null){
      $get_page_id = $this->db->select('SELECT id FROM '.PREFIX.'routes WHERE url = :url LIMIT 1', array(':url' => $old_url));
      $query = $this->db->update(PREFIX.'routes', array('url' => $new_url), array('id' => $get_page_id[0]->id));
      if($query > 0){
        return true;
      }else{
        return false;
      }
    }

    /**
    * updates Page Permissions sitemap
    * @param int $page_id
    * @param boolean $sitemap
    * @return boolean true/false
    */
    public function updatePageSiteMap($page_id = null, $sitemap = null, $pagefolder = null, $pagefile = null, $url = null, $arguments = null, $enable = 'true', $headfoot = 1, $template = 'Default'){
      $query = $this->db->update(PREFIX.'pages', array('sitemap' => $sitemap, 'pagefolder' => $pagefolder, 'pagefile' => $pagefile, 'url' => $url, 'arguments' => $arguments, 'enable' => $enable, 'headfoot' => $headfoot, 'template' => $template), array('id' => $page_id));
      if($query > 0){
        return true;
      }else{
        return false;
      }
    }

}
