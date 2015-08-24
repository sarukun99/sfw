<?php
class Env
{
  private static $env = [
    'dev' => [
      'db.host' => 'localhost',
      'db.name' => 'mysommelier',
      'db.user' => 'root',
      'db.pass' => '',
    ]
  ];

  public static function get(){
    foreach(self::$env as $env)
    {
      if(isset($env['host']) && $env['host'] == $_SERVER['HTTP_HOST'])
      {
        return $env;
      }
      $keys = array_keys(self::$env);
      return self::$env[$keys[0]];
    }
  }
}
