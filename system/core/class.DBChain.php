<?php
/**
* Database Core
*
* UserCandy
* @author David (DaVaR) Sargent <davar@usercandy.com>
* @version 1.0.0
*/

/**
* Goal here is to create a database interaction that is easier to read and
* understand.
* $db->select('*')
*    ->from('table')
*    ->where('something', $something)
*    ->run();
**/

namespace Core;

use Core\ErrorLogger;
use Helpers\Database;

class DBChain {

    /**
     * @var array Array of saved databases for reusing
     */
    protected static $instances = array();

    private $queryData;
    private $selection;
    private $table;
    private $where;
    private $whereAND;
    private $runType;
    private $params = [];
    private $query = [];
    private $bindnum = 1;
    private $run_query;
    private $col_val_array = [];
    private $where_col_val_array = [];
    protected $db;

    /**
    * DB Start Method
    **/
    public function start(){
      unset($this->run_query);
      unset($this->col_val_array);
      unset($this->where_col_val_array);
      $this->db = Database::get();
      return $this;
    }

    /**
    * DB Select Method
    **/
    public function select($selection = "*"){
      $selection = "SELECT ".$selection." FROM ";
      $this->run_query .= $selection;
      $this->runType = "select";
      return $this;
    }

    /**
    * DB Select Count Method
    **/
    public function selectCount($selection = "*"){
      $selection = "SELECT ".$selection." FROM ";
      $this->run_query .= $selection;
      $this->runType = "selectCount";
      return $this;
    }

    /**
    * DB Insert Method
    **/
    public function insert(){
      $this->runType = "insert";
      return $this;
    }

    /**
    * DB Update Method
    **/
    public function update(){
      $this->runType = "update";
      return $this;
    }

    /**
    * DB Table Method
    **/
    public function table($table = null){
      $table = PREFIX.$table." ";
      $this->run_query .= $table;
      return $this;
    }

    /**
    * DB Insert Columns List
    **/
    public function columns(){
      $this->get_cols = func_get_args();
      return $this;
    }

    /**
    * DB Insert Values List
    **/
    public function values(){
      $this->get_vals = func_get_args();
      return $this;
    }

    /**
    * DB Where Columns List
    **/
    public function whereColumns(){
      $this->where_get_cols = func_get_args();
      return $this;
    }

    /**
    * DB Where Values List
    **/
    public function whereValues(){
      $this->where_get_vals = func_get_args();
      return $this;
    }

    /**
    * DB Insert Combine Columns and Values List
    **/
    public function insertColVal(){
      if(isset($this->get_cols)){
        foreach ($this->get_cols as $key => $value) {
          $this->col_val_array[$value] = $this->get_vals[$key];
        }
      }
      return $this;
    }

    /**
    * DB Where Combine Columns and Values List
    **/
    public function whereColVal(){
      if(isset($this->where_get_cols)){
        foreach ($this->where_get_cols as $key => $value) {
          $this->where_col_val_array[$value] = $this->where_get_vals[$key];
        }
      }
      return $this;
    }

    /**
    * DB Where Method
    **/
    public function where($where = null, $equal = null, $operator = "="){
      $where = array($where => $equal);
      $this->params($where);
      $this->run_query .= "WHERE ";
      foreach ($where as $key => $value) {
        $key = str_replace(':', '', $key);
        $this->run_query .= $key." ".$operator." :".$this->bindnum." ";
        $this->bindnum++;
      }
      return $this;
    }

    /**
    * DB WhereAND Method
    **/
    public function whereAND($where = null, $equal = null, $operator = "="){
      $whereAND = array($where => $equal);
      $this->params($whereAND);
      foreach ($whereAND as $key => $value) {
        $key = str_replace(':', '', $key);
        $this->run_query .= "AND ".$key." ".$operator." :".$this->bindnum." ";
        $this->bindnum++;
      }
      return $this;
    }

    /**
    * DB WhereOR Method
    **/
    public function whereOR($where = null, $equal = null, $operator = "="){
      $whereOR = array($where => $equal);
      $this->params($whereOR);
      foreach ($whereOR as $key => $value) {
        $key = str_replace(':', '', $key);
        $this->run_query .= "OR ".$key." ".$operator." :".$this->bindnum." ";
        $this->bindnum++;
      }
      return $this;
    }

    /**
    * DB Having Method
    **/
    public function having($where = null, $equal = null, $operator = "="){
      $where = array($where => $equal);
      $this->params($where);
      $this->run_query .= "HAVING ";
      foreach ($where as $key => $value) {
        $key = str_replace(':', '', $key);
        $this->run_query .= $key." ".$operator." :".$this->bindnum." ";
        $this->bindnum++;
      }
      return $this;
    }

    /**
    * DB Join Method
    **/
    public function join($joinType,$table,$comparison){
      $this->run_query .= $joinType." ".PREFIX.$table." ON ".$comparison." ";
      return $this;
    }

    /**
    * DB Limit Method
    **/
    public function groupby($groupby = null){
      $groupby = "GROUP BY ".$groupby." ";
      $this->run_query .= $groupby;
      return $this;
    }

    /**
    * DB Limit Method
    **/
    public function orderby($orderby = null, $sort = "ASC"){
      $orderby = "ORDER BY ".$orderby." ".$sort." ";
      $this->run_query .= $orderby;
      return $this;
    }

    /**
    * DB Limit Method
    **/
    public function limit($limit = null){
      $limit = "LIMIT ".$limit." ";
      $this->run_query .= $limit;
      return $this;
    }

    /**
    * DB Params Method
    **/
    private function params($params = null){
      $this->params[] = $params;
      return $this;
    }

    /**
    * DB Get Params Method
    **/
    private function get_params($params = null){
      if(isset($params)){
        $bn = 1;
        foreach ($params as $new_array) {
          foreach ($new_array as $key => $value) {
            $set_new_array[$bn] = $value;
            $bn++;
          }
        }
      }
      if(isset($set_new_array)){
        return $set_new_array;
      }
    }

    /**
    * DB Run Method
    **/
    public function run(){
      /** Put all the params together **/
      $get_params = $this->get_params($this->params);
      /** Check to see what type of query **/
      if($this->runType == "select"){
        if(!empty($get_params)){
          return $this->db->select($this->run_query,$get_params);
        }else{
          return $this->db->select($this->run_query);
        }
      }else if($this->runType == "selectCount"){
        if(!empty($get_params)){
          return $this->db->selectCount($this->run_query,$get_params);
        }else{
          return $this->db->selectCount($this->run_query);
        }
      }else if($this->runType == "insert"){
        self::insertColVal();
        return $this->db->insert($this->run_query, $this->col_val_array);
      }else if($this->runType == "update"){
        self::insertColVal();
        self::whereColVal();
        return $this->db->update($this->run_query, $this->col_val_array, $this->where_col_val_array);
      }

    }


}
