<?php
/**
 * Sends Test E-Mail to test mail settings
 * Currently not in use with installer
 * Moved E-Mail settings to Admin Panel.
 *
 * UserCandy
 * @author David (DaVaR) Sargent <davar@usercandy.com>
 * @version 1.0.0
 */

//EMAIL MESSAGE USING PHPMAILER
$mail = new \Helpers\PhpMailer\Mail();
$mail->addAddress(EMAIL_USERNAME);
$mail->subject("UC - EMAIL TEST");
$body = "Hello<br/><br/>";
$body .= "This is a Test Email from your new UserCandy Installation.<br/>";
$body .= "If you can read this, well your email settings are good!<br/><br/>";
$body .= "Thank You for choosing UserCandy!  Enjoy!";
$mail->body($body);
try{
   $mail->Send();
   echo "<font color=green>Email Sent!</font>";

 } catch(Exception $e){
//Something went bad
    echo "<font color=red>Email Test Fail</font> - " . $mail->ErrorInfo;
 }
