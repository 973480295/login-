<?php
namespace Home\Controller;
use Think\Controller;
class CommonController extends Controller
{

    public function _initialize()
    {
        if($_COOKIE['username']!='')
        {
            session('username',$_COOKIE['username']);
        }
        if(session('username')=='')
        {
            $this->display('Login/index');
        }
    }
}