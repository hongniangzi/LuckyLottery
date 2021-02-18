<?php
namespace app\index\validate;
use think\Validate;

class UserValidate extends Validate
{
    protected $rule = array(
        'pwd'       => 'max:15|min:8',
        'username'  => 'require|min:3',
    );
    
    protected $message = array(
        'pwd.require'   => '密码不能为空',
        'pwd.max'       => '密码最多不能超过15个字符',
        'pwd.min'       => '密码不能少于8个字符',
        'username.require'  => '用户名为必填项',
        'username.min'      => '用户名需在3个字符以上',
    );
    // login 登录时验证场景定义
    public function sceneLogin(){
        
        return $this->only(array('username','pwd'));
    }

    // install 新增时验证场景定义
    public function sceneInstall(){
        
        return $this->only(array('username','pwd'))->append('pwd',"require");
    }

    // install 修改时验证场景定义
    public function sceneUpdate(){
        
        return $this->only(array('username'));
    }

    // install 修改时验证场景定义
    public function sceneUpdatepwd(){
        
        return $this->only(array('pwd'));
    }
}