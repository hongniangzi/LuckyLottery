<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;

class Admin extends Controller{


    public function is_login(){
        // 检测用户是否登录状态
        if(!session('Nickname')){
            return $this->redirect('/login/login');
        }
    }

    protected function method(){
        return $this->request->method();
    }
    protected function param(){
        return $this->request->param();
    }
    protected function get(){
        return $this->request->get();
    }
    protected function post(){
        return $this->request->post();
    }
    protected function file($file){
        return $this->request->file($file);
    }
    protected function isGet(){
        return $this->request->isGet();
    }
    protected function isPost(){
        return $this->request->isPost();
    }
    protected function isAjax(){
        return $this->request->isAjax();
    }

}