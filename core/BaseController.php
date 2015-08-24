<?php
class BaseController{

  protected function uid()
  {
    $sid = null;
    if(isset($_COOKIE['shibuya.session.id'])){
      $sid = $_COOKIE['shibuya.session.id'];
      return DB::table('session')->where('sid=?',$sid)->fetch()->uid;
    }
    return null;
  }

  protected function isLogin()
  {
    $uid = $this->uid();
    return isset($uid);
  }

  protected function auth($uid, $pass)
  {
     $pass = sha1($pass);
     $auth = DB::table('Auth')->where('uid=?',$uid)->where('pass=?',$pass)->fetch();

     if(!isset($auth)) return false;

     $sid = sha1($_SERVER['REMOTE_ADDR'].time().mt_rand());
     setcookie('shibuya.session.id',$sid,86400*365,'/');
     try{
       DB::table('Session')->insert([  'sid' => $sid
                                      ,'uid' => $uid
                                   ]);
      return true;
    }catch(Exception $ex){
      return false;
    }
  }

  protected function register($uid, $pass)
  {
    try{
      DB::table('Session')->insert([  'uid'  =>  $uid
                                     ,'pass' =>  sha1($pass)
                                  ]);
     return true;
   }catch(Exception $ex){
     return false;
   }
  }

  protected function param($key,$default=null,$method='REQUEST'){
    switch($method){
      case 'GET':
          return isset($_GET[$key]) ? $_GET[$key] : $default;
      case 'POST':
          return isset($_POST[$key]) ? $_POST[$key] : $default;
      case 'REQUEST':
          return isset($_POST[$key]) ? $_POST[$key] : $default;
    }
  }

  protected function render($params=[])
  {
     header('Content-Type:text/html;charset=utf8');
     extract($params);
     include Dispatcher::conf('view_dir').$this->controller."/".$this->action.".php";
  }

  protected function renderJSON($params=[]){
    header('Content-Type:application/json;charset=utf8');
    echo json_encode($params);
  }

}
