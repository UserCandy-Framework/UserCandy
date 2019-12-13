<?php
/**
* Success Messages Plguin
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

namespace Helpers;

use Core\Language;

/**
 * collection of methods for working with success messages.
 */
class SuccessMessages
{
  /**
   * Get and display recent success message from success session
   * @return string
   */
  public static function display(){
    // Check to see if session success_message exists
    if(isset($_SESSION['success_message'])){
      // Get data from session then display it
  		$success_msg = $_SESSION['success_message'];
  		$display_msg = "
        <div class='modal hide fade' id='alertModal' role='dialog'>
          <div class='modal-dialog modal-lg'>
            <div class='modal-content'>
              <div class='modal-header alert-success'>
                <h5 class='modal-title' id='DeleteLabel'><strong>".Language::show('uc_success', 'Welcome')."!</strong></h5>
                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                  <span aria-hidden='true'>&times;</span>
                </button>
              </div>
              <div class='modal-body'>
                <p>$success_msg</p>
              </div>
            </div>
          </div>
        </div>
      ";
  		unset($_SESSION['success_message']);
      return $display_msg;
  	}
  }

  /**
  * Push Success Message to Session for display on page user is redirected to
  * @param $error_msg  string  Message Text
  * @param $redirect_to_page  string  URL Page Name for Redirect
  */
  public static function push($success_msg, $redirect_to_page = null){
    // Check to see if there is already a success message session
    if(isset($_SESSION['success_message'])){
      // Clean success message Session
      unset($_SESSION['success_message']);
    }
    // Send success message to session
    $_SESSION['success_message'] = $success_msg;
    // Check to see if a redirect to page is supplied
    if(isset($redirect_to_page)){
      // Redirect User to Given Page
      Url::redirect($redirect_to_page);
    }
  }

  /**
  * Displays Message without sessions to keep form data for retry
  * @param $e_msg  string  Message Text
  * @return string
  */
  public static function display_raw($s_msg = null){
    // Make sure an Error Message should be displayed
    if(isset($s_msg)){
      // Check to see if we are displaying an array of messages
      if(is_array($s_msg)){
        // Setup Array for display
        $success_msg = "";
        foreach($s_msg as $sm){
          $success_msg .= "<br>$sm";
        }
      }else{
        $success_msg = $s_msg;
      }
        // Not an array, display single error
      $display_msg = "
        <div class='modal hide fade' id='alertModal' role='dialog'>
          <div class='modal-dialog modal-lg'>
            <div class='modal-content'>
              <div class='modal-header alert-success'>
                <h5 class='modal-title' id='DeleteLabel'><strong>".Language::show('uc_success', 'Welcome')."!</strong></h5>
                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                  <span aria-hidden='true'>&times;</span>
                </button>
              </div>
              <div class='modal-body'>
                <p>$success_msg</p>
              </div>
            </div>
          </div>
        </div>
      ";
      return $display_msg;
    }
  }

  /**
  * Displays Message without sessions to keep form data for retry
  * @param $e_msg  string  Message Text
  * @return string
  */
  public static function display_raw_info($s_msg = null){
    // Make sure an Error Message should be displayed
    if(isset($s_msg)){
      // Check to see if we are displaying an array of messages
      if(is_array($s_msg)){
        // Setup Array for display
        $success_msg = "";
        foreach($s_msg as $sm){
          $success_msg .= "<br>$sm";
        }
      }else{
        $success_msg = $s_msg;
      }
        // Not an array, display single error
      $display_msg = "
        <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
          <div class='alert alert-info alert-dismissible' role='alert'>
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
              <span aria-hidden='true'>&times;</span>
            </button>
            $success_msg
          </div>
        </div>";
      return $display_msg;
    }
  }

  /**
   * Get and display recent success message from success session
   * Temp usage without lang file
   * @return string
   */
  public static function display_nolang(){
    // Check to see if session success_message exists
    if(isset($_SESSION['success_message'])){
      // Get data from session then display it
  		$success_msg = $_SESSION['success_message'];
  		$display_msg = "
        <div class='modal hide fade' id='alertModal' role='dialog'>
          <div class='modal-dialog modal-lg'>
            <div class='modal-content'>
              <div class='modal-header alert-success'>
                <h5 class='modal-title' id='DeleteLabel'><strong>Success!</strong></h5>
                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                  <span aria-hidden='true'>&times;</span>
                </button>
              </div>
              <div class='modal-body'>
                <p>$success_msg</p>
              </div>
            </div>
          </div>
        </div>
      ";
  		unset($_SESSION['success_message']);
      return $display_msg;
  	}
  }

}
