<?php

class AuthBaseController extends BaseController{

  public function __construct()
  {
     if(!$this->isLogin()){
       $this->redirect(Dispatcher::conf('login_url'));
     }
  }
}
