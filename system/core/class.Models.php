<?php
/**
* System Models Class
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version uc 1.0.4
*/

namespace Core;

use Helpers\Database;

class Models {

    protected $db;

    function __construct(){
        /** Connect to PDO for all models. */
        $this->db = Database::get();
    }
}
