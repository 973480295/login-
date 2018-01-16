<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller {
    public function index(){
//        print_r(cookie());
//        print_r($_COOKIE);
        // exit;
        if($_COOKIE['username']!='')
        {
            session('username',$_COOKIE['username']);
        }
        if(session('username')=='')
        {
            $this->display('index');
        }else{
            $this->redirect('Index/welcome');
        }
    }
    public function login(){
        $username=I('post.username');
        $password=I('post.password');
//        echo "<pre/>";
//        print_r($data);
        $info=M('user')->field('id,username,password')->where('username="'.$username.'"')->find();
//        echo "<pre/>";
//        print_r($info);
//        exit;
        if($info){
            if(md5($password)==$info['password']){
                session('username',$username);
                session('id',$info['id']);
                if(I('post.ischecks'))
                {
                    // C('COOKIE_EXPIRE',time()+864000);
                    cookie('username',$username,864000);
                    //setcookie('username',$username,864000);
                }
                $datas=[
                  'msg'=>'登录成功',
                  'status'=>1
                ];
                $this->ajaxReturn($datas,'JSON');
//                $this->success('登录成功','Index/index');
            }else{
                $datas=[
                    'msg'=>'登录失败,密码错误',
                    'status'=>0
                ];
                $this->ajaxReturn($datas,'JSON');
//                $this->error('登录失败,密码错误','Index/index');
            }
        }else{
            $datas=[
                'msg'=>'用户名不存在',
                'status'=>0
            ];
            $this->ajaxReturn($datas,'JSON');
//            $this->error('用户名不存在','Index/index');
        }
    }
//    public function welcome(){
//        $this->display('welcome');
//    }
    public function add(){
        $this->display('add');
    }
    public function do_add(){
        $data=I('post.');
//        echo "<pre/>";
//        print_r($data);
        $data['password']=md5($data['password']);
        $data['qq']=md5($data['qq']);
        if($data['password']==$data['qq']){
            $info=M('user')->add($data);
            if($info){
                $this->success('注册成功','Index/index');
            }else{
                $this->error('注册失败');
            }
        }else{
            $this->error('两次密码不一致');
        }
    }
}