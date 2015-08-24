<?php
class TopController extends BaseController
{
   function index()
   {
     var_dump(DB::table('max_invoice_no')->fetchAll());
     $this->render(['sample' => 'test2']);
   }
}
