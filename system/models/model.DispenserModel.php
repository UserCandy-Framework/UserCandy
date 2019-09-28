<?php
/**
* Dispenser Model Class
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

class DispenserModel extends Models {

  /**
  * Get Widget Data From Database
  * @param $page_id
  * @return array dataset
  */
  public function getWidgetByPage($page_id = null){
      $widget_data = $this->db->select("
          SELECT
              *
          FROM
              ".PREFIX."dispenser_widgets
          WHERE
              page_id = :page_id
          ORDER BY
              id DESC
      ",
      array(':page_id' => $page_id));
      return $widget_data;
  }

  /**
  * Get Widget Data From Database based on folder
  * @param $folder
  * @return array dataset
  */
  public function getDispenserByName($folder = null, $type = null){
    $folder = (string) $folder;
      $widget_data = $this->db->select("
          SELECT
              *
          FROM
              ".PREFIX."dispenser
          WHERE
              folder_location = :folder
          AND
              type = :type
          ORDER BY
              id DESC
      ",
      array(':folder' => $folder, ':type' => $type));
      return $widget_data;
  }

  /**
  * Get Dispenser Data From Database based on type and enabled
  * @param $type
  * @return string data
  */
  public function getDispenserByType($type = null){
      $data = $this->db->select("
          SELECT
              folder_location
          FROM
              ".PREFIX."dispenser
          WHERE
              type = :type
          AND
              enable = 'true'
          ORDER BY
              id DESC
      ",
      array(':type' => $type));
      return $data;
  }


  /**
  * Get Dispenser Data From Database
  * @param $id
  * @return array dataset
  */
  public function getDispenserData($id = null){
      $data = $this->db->select("
          SELECT
              *
          FROM
              ".PREFIX."dispenser
          WHERE
              id = :id
          AND
              enable = 'true'
          ORDER BY
              id DESC
      ",
      array(':id' => $id));
      return $data;
  }

  /**
  * Get Dispenser Data From Database
  * @param $id
  * @return array dataset
  */
  public function getDispenserDataAll($id = null){
      $data = $this->db->select("
          SELECT
              *
          FROM
              ".PREFIX."dispenser
          WHERE
              id = :id
          ORDER BY
              id DESC
      ",
      array(':id' => $id));
      return $data;
  }

  /**
  * Get Current page_id from database
  * @param string $pagefolder
  * @param string $pagefile
  * @return int data
  */
  public function getCurrentPageID($pagefolder = null, $pagefile = null){
      $data = $this->db->select("
          SELECT
              id
          FROM
              ".PREFIX."pages
          WHERE
              pagefolder = :pagefolder
          AND
              pagefile = :pagefile
      ", array(':pagefolder' => $pagefolder, ':pagefile' => $pagefile));
      return $data[0]->id;
  }

  /**
  * Update Dispenser Settings
  * @param $name
  * @return boolean true/false
  */
	public function updateDispenserSettings($id, $name = null, $type = null, $folder_location = null, $version = null, $enable = null){
		// Update users table
		$query = $this->db->update(PREFIX.'dispenser', array('name' => $name, 'type' => $type, 'folder_location' => $folder_location, 'version' => $version, 'enable' => $enable), array('id' => $id));
		if($query > 0){
			return true;
		}else{
			return false;
		}
	}

  /**
  * Update Dispenser Settings
  * @param $id
  * @param $version
  * @return boolean true/false
  */
	public function updateDispenserVersion($id = null, $version = null){
		/** Update users table **/
		$query = $this->db->update(PREFIX.'dispenser', array('version' => $version), array('id' => $id));
		if($query > 0){
		 	return true;
		}else{
			return false;
		}
	}

  /**
  * Update Dispenser Enable Setting
  * @param $id
  * @param $enable
  * @return boolean true/false
  */
	public function updateDispenserEnable($id = null, $enable = 'true'){
		// Update users table
		$query = $this->db->update(PREFIX.'dispenser', array('enable' => $enable), array('id' => $id));
		if($query > 0){
			return true;
		}else{
			return false;
		}
	}

  /**
  * Get Dispenser Data From Database
  * @param $widget_id
  * @return array dataset
  */
  public function getWidgetData($widget_id = null){
      $data = $this->db->select("
          SELECT
              *
          FROM
              ".PREFIX."dispenser_widgets
          WHERE
              widget_id = :widget_id
          ORDER BY
              id DESC
      ",
      array(':widget_id' => $widget_id));
      return $data;
  }

  /**
  * Get Dispenser Data From Database
  * @param $widget_id
  * @return array dataset
  */
  public function getWidgetEditData($widget_id = null){
      $data = $this->db->select("
          SELECT
              *
          FROM
              ".PREFIX."dispenser_widgets
          WHERE
              id = :widget_id
          ORDER BY
              id DESC
      ",
      array(':widget_id' => $widget_id));
      return $data;
  }

  /**
  * Get Current page name from database
  * @param string $page_name
  * @return string data
  */
  public function getPageName($page_id = null){
      $data = $this->db->select("
          SELECT
              url
          FROM
              ".PREFIX."pages
          WHERE
              id = :page_id
      ", array(':page_id' => $page_id));
      return $data[0]->url;
  }

  /**
  * Get all Pages from Database
  * @return array data
  */
  public function getPages(){
      $data = $this->db->select("
          SELECT
              *
          FROM
              ".PREFIX."pages
      ");
      return $data;
  }

  /**
  * Update Widget Setting to Database
  * @param $id
  * @param $display_type
  * @param $display_location
  * @param $page_id
  * @return boolean true/false
  */
	public function updateWidgetSetting($id, $display_type = null, $display_location = null, $page_id = null){
		// Update users table
		$query = $this->db->update(PREFIX.'dispenser_widgets', array('display_type' => $display_type, 'display_location' => $display_location, 'page_id' => $page_id), array('id' => $id));
		if($query > 0){
			return true;
		}else{
			return false;
		}
	}

  /**
  * Insert Widget Setting to Database
  * @param $display_type
  * @param $display_location
  * @param $page_id
  * @return boolean true/false
  */
	public function insertWidgetSetting($id = null, $display_type = null, $display_location = null, $page_id = null){
		// Update users table
		$query = $this->db->insert(PREFIX.'dispenser_widgets', array('widget_id' => $id, 'display_type' => $display_type, 'display_location' => $display_location, 'page_id' => $page_id));
		if($query > 0){
			return true;
		}else{
			return false;
		}
	}

  /**
  * Delete Dispenser Widget by ID
  * @param $id
  * @param $widget_id
  * @return boolean true/false
  */
  public function deleteWidgetSetting($id, $widget_id){
    $data = $this->db->delete(PREFIX.'dispenser_widgets', array('id' => $id, 'widget_id' => $widget_id));
    if($data > 0){
      return true;
    }else{
      return false;
    }
  }

  /**
  * Insert Widget to Database
  * @param $name
  * @param $type
  * @param $folder_location
  * @param $version
  * @return boolean true/false
  */
  public function insertDispenser($name = null, $type = null, $folder_location = null, $version = null){
    // Update users table
    $query = $this->db->insert(PREFIX.'dispenser', array('name' => $name, 'type' => $type, 'folder_location' => $folder_location, 'version' => $version));
    if($query > 0){
      return true;
    }else{
      return false;
    }
  }

  /**
  * Update the Database - Used to insert any data to database
  * @param $db_data
  * @return string data
  */
  public function updateDatabase($db_data, $current_version = null, $new_version = null){
    if(isset($current_version) && isset($new_version)){
      /** Inserting Raw Data to Database - $db_data must be an array **/
      $new_version = current($new_version);
      foreach ($db_data as $key => $value) {
        if(is_array($value)){
          $query_version = key($value);
        }else{
          $query_version = $value;
        }
        if($query_version > $current_version && $query_version <= $new_version){
          $db_data_sort[] = current($value);
        }
      }
      if(!empty($db_data_sort)){
        /** Go through the Array and update the database **/
        foreach ($db_data_sort as $key => $data) {
          /** Send the data to the database **/
          if($query = $this->db->raw($data)){
            $output[] = "Data Updated in Database.<br><i><small>$data</small></i><br>";
          }else{
            $output[] = "<font color='red'>Error With the following Query<Br><i><small>$data</small></i></font><br>";
          }
        }
      }else{
        $output[] = "No Data Found to Insert to Database.";
      }
    }else{
      if(!empty($db_data)){
        /** Go through the Array and update the database **/
        foreach ($db_data as $data) {
          /** Send the data to the database **/
          if($query = $this->db->raw($data)){
            $output[] = "Data Updated in Database.<br><i><small>$data</small></i><br>";
          }else{
            $output[] = "<font color='red'>Error With the following Query<Br><i><small>$data</small></i></font><br>";
          }
        }
      }else{
        $output[] = "No Data Found to Insert to Database.";
      }
    }
    return $output;
  }

  /**
  * Get Widget Data From Database based on folder
  * @param $folder
  * @return array dataset
  */
  public function checkDispenserEnabled($folder){
    $folder = (string) $folder;
      $query = $this->db->selectCount("
          SELECT
              id
          FROM
              ".PREFIX."dispenser
          WHERE
              folder_location = :folder
          AND
              enable = 'true'
          ORDER BY
              id DESC
      ",
      array(':folder' => $folder));
      if($query > 0){
        return true;
      }else{
        return false;
      }
  }

}
