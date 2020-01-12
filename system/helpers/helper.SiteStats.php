<?php
/**
* Site Stats Plugin
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

namespace Helpers;

class SiteStats
{
  private static $db;

  /**
  *  Log Current Activity
  */
  public static function log($username = null){
    /** Check if username is empty **/
    if(empty($username)){$username = "Guest";}
    /** Get the Refering Page if There is one **/
    if(isset($_SERVER['HTTP_REFERER'])){ $refer = $_SERVER['HTTP_REFERER']; }else{ $refer = ""; }
    /** Will return the type of web browser or user agent that is being used to access the current script. **/
    $useragent = $_SERVER['HTTP_USER_AGENT'];
    /** The filename of the currently executing script, relative to the document root. **/
    $cfile = $_SERVER['PHP_SELF'];
    /** Prints the exact path and filename relative to the DOCUMENT_ROOT of your site. **/
	  $uri = $_SERVER['REQUEST_URI'];
    /** Contains the IP address of the user requesting the PHP script from the server. **/
	  $ipaddy = $_SERVER['REMOTE_ADDR'];
    /** Get Server Name Site is Accessed On. **/
    $server = $_SERVER['SERVER_NAME'];

    // List of Pages that user should never get logged
    $no_log_pages = array("Templates", "assets");
    //Remove the extra forward slash from link
    $cur_page_a = ltrim($uri, SITE_URL);
    // Get first part of the url (page name)
    $cur_page_b = explode('/', $cur_page_a);

    // Check to see if we should log as a previous page
    if(strpos ($uri,"." ) === FALSE){
        if(!in_array($cur_page_b[0], $no_log_pages)){
            self::$db = Database::get();
            self::$db->insert(
              PREFIX.'sitelogs',
                array('membername' => $username, 'refer' => $refer,
                      'useragent' => $useragent, 'cfile' => $cfile,
                      'uri' => $uri, 'ipaddy' => $ipaddy,
                      'server' => $server));
        }
    }
  }

  /**
	 * Get total number of site logs
	 */
	public static function getTotalViews(){
    self::$db = Database::get();
		$data = self::$db->select("SELECT count(id) as num_rows FROM ".PREFIX."sitelogs");
		$number = $data[0]->num_rows;
    $abbrevs = array(12 => "T", 9 => "B", 6 => "M", 3 => "K", 0 => "");
    foreach($abbrevs as $exponent => $abbrev) {
        if($number >= pow(10, $exponent)) {
        	$display_num = $number / pow(10, $exponent);
        	$decimals = ($exponent >= 3 && round($display_num) < 100) ? 1 : 0;
            return number_format($display_num,$decimals) . $abbrev;
        }
    }
	}


  /**
	 * Get total number of site logs for given month
	 */
	public static function getCurrentMonth($date = null, $when = null){
    if($when == "lastYear"){$date = date('F Y', strtotime($date.' -1 year'));}
    $month = date('n', strtotime($date));
    $year = date('Y', strtotime($date));
    self::$db = Database::get();
		$data = self::$db->select("SELECT count(id) as num_rows FROM ".PREFIX."sitelogs WHERE YEAR(timestamp) = :year AND MONTH(timestamp) = :month", array(':year' => $year, ':month' => $month));
    return $data[0]->num_rows;
	}


  /**
  * Get Current User's Browser Type
  */
  public static function getDeviceInformation(){
    /** Get User's IP **/
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    /** Get User's OS **/
    $os_platform    = "Unknown OS Platform";
    $os_array       = array(
                          '/windows nt 10/i'      =>  'Windows 10',
                          '/windows phone 8/i'    =>  'Windows Phone 8',
                          '/windows phone os 7/i' =>  'Windows Phone 7',
                          '/windows nt 6.3/i'     =>  'Windows 8.1',
                          '/windows nt 6.2/i'     =>  'Windows 8',
                          '/windows nt 6.1/i'     =>  'Windows 7',
                          '/windows nt 6.0/i'     =>  'Windows Vista',
                          '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                          '/windows nt 5.1/i'     =>  'Windows XP',
                          '/windows xp/i'         =>  'Windows XP',
                          '/windows nt 5.0/i'     =>  'Windows 2000',
                          '/windows me/i'         =>  'Windows ME',
                          '/win98/i'              =>  'Windows 98',
                          '/win95/i'              =>  'Windows 95',
                          '/win16/i'              =>  'Windows 3.11',
                          '/macintosh|mac os x/i' =>  'Mac OS X',
                          '/mac_powerpc/i'        =>  'Mac OS 9',
                          '/linux/i'              =>  'Linux',
                          '/ubuntu/i'             =>  'Ubuntu',
                          '/iphone/i'             =>  'iPhone',
                          '/ipod/i'               =>  'iPod',
                          '/ipad/i'               =>  'iPad',
                          '/android/i'            =>  'Android',
                          '/blackberry/i'         =>  'BlackBerry',
                          '/webos/i'              =>  'Mobile');
    $found = false;
    $device = '';
    foreach ($os_array as $regex => $value)
    {
        if($found)
         break;
        else if (preg_match($regex, $user_agent))
        {
            $os_platform    =   $value;
            $device = !preg_match('/(windows|mac|linux|ubuntu)/i',$os_platform)
                      ?'MOBILE':(preg_match('/phone/i', $os_platform)?'MOBILE':'SYSTEM');
        }
    }
    $device = !$device? 'SYSTEM':$device;
    /** Get Browser Type **/
    $browser        =   "Unknown Browser";
    $browser_array  = array('/msie/i'       =>  'Internet Explorer',
                            '/firefox/i'    =>  'Firefox',
                            '/safari/i'     =>  'Safari',
                            '/chrome/i'     =>  'Chrome',
                            '/edge/i'       =>  'Edge',
                            '/opera/i'      =>  'Opera',
                            '/netscape/i'   =>  'Netscape',
                            '/maxthon/i'    =>  'Maxthon',
                            '/konqueror/i'  =>  'Konqueror',
                            '/mobile/i'     =>  'Handheld Browser');
    foreach ($browser_array as $regex => $value)
    {
        if($found)
         break;
        else if (preg_match($regex, $user_agent,$result))
        {
            $browser    =   $value;
        }
    }
    return array('os'=>$os_platform,'device'=>$device,'browser'=>$browser);
  }

  /**
  * Use API to get User's Device Location
  */
  public static function getDeviceLocation(){
    $deviceIP = '';
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $deviceIP = $_SERVER['HTTP_CLIENT_IP'];
    } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $deviceIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
        $deviceIP = $_SERVER['HTTP_X_FORWARDED'];
    } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
        $deviceIP = $_SERVER['HTTP_FORWARDED_FOR'];
    } else if (isset($_SERVER['HTTP_FORWARDED'])) {
        $deviceIP = $_SERVER['HTTP_FORWARDED'];
    } else if (isset($_SERVER['REMOTE_ADDR'])) {
        $deviceIP = $_SERVER['REMOTE_ADDR'];
    } else {
        $deviceIP = 'UNKNOWN';
    }
    $device_location_data = file_get_contents("http://ipinfo.io/$deviceIP/geo");
    $device_location_data = json_decode($device_location_data, true);
    return $device_location_data;
  }

  /**
  * Update or Add User's device to the database.
  * If device data match then change new to 0.
  */
  public static function updateUserDeviceInfo($userId){
    /** Ready the Auth Model **/
    $authModel = new \Models\AuthModel();
    /** Get Current Device Information **/
    $user_devise = Self::getDeviceInformation();
    $user_location = Self::getDeviceLocation();
    $os = $user_devise['os'];
    $device = $user_devise['device'];
    $browser = $user_devise['browser'];
    $city = $user_location['city'];
    $state = $user_location['region'];
    $country = $user_location['country'];
    $useragent = $_SERVER['HTTP_USER_AGENT'];
    $ip = (isset($user_location['ip'])) ? $user_location['ip'] : $_SERVER['REMOTE_ADDR'];
    /** Update device from new in database if new is 1 **/
    $authModel->updateInDB('users_devices',array('new'=>'0'),array('userID'=>$userId,'os'=>$os,'device'=>$device,'browser'=>$browser,'city'=>$city,'state'=>$state,'country'=>$country,'useragent'=>$useragent));
    /** Check to see if device information exists **/
    if(!$authModel->getDeviceExists($userId,$os,$device,$browser,$city,$state,$country,$useragent)){
      /** If not exists then add to database **/
      if($authModel->addIntoDB('users_devices',array('userID'=>$userId,'os'=>$os,'device'=>$device,'browser'=>$browser,'city'=>$city,'state'=>$state,'country'=>$country,'useragent'=>$useragent,'ip'=>$ip))){
        /** Check if Email Settings are set **/
        $site_mail_setting = SITEEMAIL;
        if(!empty($site_mail_setting)){
          /** User has new device or location information - Send Email **/
          $email = \Helpers\CurrentUserData::getUserEmail($userId);
          $username = \Helpers\CurrentUserData::getUserName($userId);
          $mail = new \Helpers\Mail();
          $mail->addAddress($email);
          $mail->setFrom(SITEEMAIL, EMAIL_FROM_NAME);
          $mail->subject(SITE_TITLE. " - ".\Core\Language::show('login_device_email_sub', 'Auth'));
          $body = \Helpers\PageFunctions::displayEmailHeader();
          $body .= sprintf(\Core\Language::show('login_new_device_email', 'Auth'), $username);
          $body .= "<hr><b>".\Core\Language::show('device_device', 'Members')."</b>";
          $body .= "<br>".$browser." - ".$os;
          $body .= "<hr><b>".\Core\Language::show('device_location', 'Members')."</b>";
          $body .= "<br>".$city.", ".$state.", ".$country;
          $body .= \Core\Language::show('login_device_footer_email', 'Auth');
          $body .= \Helpers\PageFunctions::displayEmailFooter();
          $mail->body($body);
          $mail->send();
        }
      }
    }
    /** Check if device is enabled and return the status **/
    return $authModel->getDeviceStatus($userId,$os,$device,$browser,$city,$state,$country,$useragent);

  }

}
