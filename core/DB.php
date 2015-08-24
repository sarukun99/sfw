<?php
class DB{

  private static $_con = [];


  public function __construct($context="MASTER"){
    $this->context  = $context;
    $this->_where   = [];
    $this->_where_params = [];
  }

  public static function connect($env=[],$context = 'MASTER'){

    if(!isset(self::$_con[$context])){

      if( !isset($env['db.host'])){
        throw new Exception ('Not Found Host Conf');
      }

      if( !isset($env['db.name'])){
        throw new Exception ('Not Found DB Name Conf');
      }

      if( !isset($env['db.user'])){
        throw new Exception ('Not Found DB User Conf');
      }

      if( !isset($env['db.pass'])){
        throw new Exception ('Not Found Pass Conf');
      }

      self::$_con[$context] =  new PDO( "mysql:host=".$env['db.host'].";dbname=".$env['db.name']
                            ,$env['db.user']
                            ,$env['db.pass']
                            ,array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
                           );
    }
  }

  public function query($sql,$params=[])
  {
    $stmt = self::$_con[$this->context]->prepare($sql);
    $stmt->execute($params);
    return $stmt;
  }

  public static function table($table){
    $db = new DB();
    $db->_table = $table;
    return $db;
  }

  public function where($where,$params){
    $this->_where[] = $where;
    $this->_where_params = $params;
    return $this;
  }

  public function order($order){
    $this->_order = $order;
    return $this;
  }

  public function limit($order){
    $this->_limit = $limit;
    return $this;
  }

  public function buildQuery()
  {


    $sql = "SELECT * FROM $this->_table";

    if(count($this->_where)>0){
      $sql_where = '';
      foreach($this->_where as $where){
        if( $sql_where != '') $sql_where .= ' AND ';
        $sql_where .=  $where;
      }
      $sql .=" WHERE $sql_where ";
    }

    if(isset($this->_order)){
      $sql .= " ORDER BY ".$this->_order;
    }

    if(isset($this->_limit)){
      $sql .= " LIMIT ".$this->_limit;
    }

    return $sql;

  }

  public function fetchAll(){
    $sql = $this->buildQuery();
    return $this->query($sql,$this->_where_params)->fetchAll();
  }

}
