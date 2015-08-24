<?php
define('BASE_DIR',__DIR__.'/');
require_once BASE_DIR.'Env.php';
require_once BASE_DIR.'BaseController.php';
require_once BASE_DIR.'DB.php';

set_error_handler(function($errno, $errstr, $errfile, $errline){
    throw new Exception();
});

class Dispatcher{

  private static $_env;

  private static $_conf;

  public static function env()
  {
    return self::$_env;
  }

  public static function conf($key)
  {
    return isset(self::$_conf[$key]) ? self::$_conf[$key] : null ;
  }

  public static function invoke($conf){
    self::$_env  = Env::get();
    self::$_conf = $conf;

    DB::connect(self::$_env);

    $ignore_path   = isset($conf['ignore_path']) ? [$conf['ignore_path']] :[];
    $ignore_path[] = "[^\/]+\.php";

    $path = $_SERVER['REQUEST_URI'];
    foreach($ignore_path as $pattern){
      $path = preg_replace("/$pattern/",'',$path);
    }
    $path = explode('/',$path);

    $action = [];
    switch(count($path)){
       case 1:
           $action = ['controller'=>'top','action' => 'index'];
           break;
       case 2:
           $path[1] = $path[1] == '' ? 'top' : $path[1];
           $action = ['controller'=>$path[1],'action' => 'index'];
           break;
       case 3:
           $action = ['controller'=>$path[1],'action' => $path[2]];
           break;
    }

    $controller_name = ucwords($action['controller']).'Controller';
    $action_name = ucwords($action['action']);

    try{
      if(!file_exists($conf['controller_dir'].$controller_name.".php")){
        throw new Exception('コントローラが見つかりません');
      }

      require_once $conf['controller_dir'].$controller_name.".php";
      $controller = new  $controller_name();
      $controller->controller = $action['controller'];
      $controller->action = $action_name;
      $controller->$action_name();
    }catch(Exception $e){
      header('HTTP/1.1 500 Internal Server Error');
      echo '500 Internal Server Error';
      error_log($e->getMessage());
    }


  }
}
