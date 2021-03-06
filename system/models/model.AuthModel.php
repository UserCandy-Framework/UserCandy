<?php
/**
 * Auth Database Models
 *
 * UserCandy
 * @author David (DaVaR) Sargent <davar@usercandy.com>
 * @version uc 1.0.4
 */

namespace Models;

use Core\Models;

class AuthModel extends Models
{
    /**
     * Gets user account info by username
     * @param $username
     * @return array dataset
     */
    public function getAccountInfo($username)
    {
        return $this->db->select("SELECT * FROM ".PREFIX."users WHERE username=:username", array(":username" => $username));
    }

    /**
     * Gets user account info by email
     * @param $email
     * @return array dataset
     */
    public function getAccountInfoEmail($email)
    {
        return $this->db->select("SELECT * FROM ".PREFIX."users WHERE email=:email", array(":email" => $email));
    }

    /**
     * Delete user by username
     * @param $username
     * @return int : rows deleted
     */
    public function deleteUser($username)
    {
        return $this->db->delete(PREFIX."users", array("username" => $username));
    }

    /**
     * Gets session info by the hash
     * @param $hash
     * @return array dataset
     */
    public function sessionInfo($hash)
    {
        return $this->db->select("SELECT uid, username, expiredate, ip FROM ".PREFIX."sessions WHERE hash=:hash", array(':hash' => $hash));
    }

    /**
     * Delete session by username
     * @param $username
     * @return int : rows deleted
     */
    public function deleteSession($username)
    {
        return $this->db->delete(PREFIX."sessions", array('username' => $username));
    }

    /**
     * Gets all attempts to login all accounts
     * @return array dataset
     */
    public function getAttempts()
    {
        return $this->db->select("SELECT ip, expiredate FROM ".PREFIX."attempts");
    }

    /**
     * Gets login attempt by ip address
     * @param $ip
     * @return array dataset
     */
    public function getAttempt($ip)
    {
        return $this->db->select("SELECT count FROM ".PREFIX."attempts WHERE ip=:ip", array(":ip" => $ip));
    }

    /**
     * Delete attempts of logging in
     * @param $where
     * @return int : deleted rows
     */
    public function deleteAttempt($where)
    {
        return $this->db->delete(PREFIX."attempts", $where);
    }

    /**
     * Add into DB
     * @param $table
     * @param $info
     * @return int : row id
     */
    public function addIntoDB($table,$info)
    {
        return $this->db->insert(PREFIX.$table,$info);
    }

    /**
     * Update in DB
     * @param $table
     * @param $info
     * @param $where
     * @return int
     */
    public function updateInDB($table,$info,$where)
    {
        return $this->db->update(PREFIX.$table,$info,$where);
    }

    /**
     * Get the user id by username
     * @param $username
     * @return array dataset
     */
    public function getUserID($username)
    {
        return $this->db->select("SELECT userID FROM ".PREFIX."users WHERE username=:username", array(":username" => $username));
    }

    /**
     * Check is user is a New Member (groupID = 1)
     * @param $userID
     * @return array dataset
     */
    public function getUserGroups($userID)
    {
        return $this->db->select("SELECT groupID FROM ".PREFIX."users_groups WHERE userID = :userID",array(':userID' => $userID));
    }

    /**
     * Get device status
     * @return int data
     */
    public function getDeviceStatus($userId,$os,$device,$browser,$city,$state,$country,$useragent)
    {
      return $this->db->select("SELECT * FROM ".PREFIX."users_devices WHERE userID = :userID AND os = :os AND device = :device AND browser = :browser AND city = :city AND state = :state AND country = :country AND useragent = :useragent",
                          array('userID'=>$userId,'os'=>$os,'device'=>$device,'browser'=>$browser,'city'=>$city,'state'=>$state,'country'=>$country,'useragent'=>$useragent));
    }

    /**
     * Get device status
     * @return int data
     */
    public function getDeviceExists($userId,$os,$device,$browser,$city,$state,$country,$useragent)
    {
      $data = $this->db->selectCount("SELECT * FROM ".PREFIX."users_devices WHERE userID = :userID AND os = :os AND device = :device AND browser = :browser AND city = :city AND state = :state AND country = :country AND useragent = :useragent",
                          array('userID'=>$userId,'os'=>$os,'device'=>$device,'browser'=>$browser,'city'=>$city,'state'=>$state,'country'=>$country,'useragent'=>$useragent));
      if($data > 0){
        return true;
      }else{
        return false;
      }
    }

}
