<?php
namespace Home\Controller;
use Think\Controller;
use Home\Controller\CommonController;
class IndexController extends CommonController {
    public function index(){
        echo session('username');
    }
    public function welcome()
    {
        echo "<pre/>";
        print_r(cookie());
        print_r($_COOKIE);
        //print_r(session());exit;
        $this->assign('username',session('username'));
        $this->display('welcome');
    }
}