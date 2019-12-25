<?php
/**
 * Checks mySQL database settings.
 *
 * UserCandy
 * @author David (DaVaR) Sargent <davar@usercandy.com>
 * @version uc 1.0.4
 */

try {
  $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  echo "<font color=green>Connected Successfully!</font>";
  $updated_config = true;
}
catch(PDOException $e){
  echo "<font color=red>Connection failed:</font> " . $e->getMessage();
}
