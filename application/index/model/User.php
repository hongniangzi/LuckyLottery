<?php
namespace app\index\model;
use think\Model;
use think\Validate;

class User extends Model{
    protected $auto = array();

    // 自动完成, 只在新增的时候有效
    protected $insert = array();


    //自动完成 格式set字段Attr
    // protected function setCreateTimeAttr(){
    //     return time();
    // }

    // 新增
    public function addUser($data){
        if(!empty($data['id'])){
            // 重新修改
            $res = $this->isUpdate(true)->allowField(true)->save($data);
            return $res ? $this->id : false;
        }else{
            // 新增
            $res = $this->data($data)->allowField(true)->save();
            return $res ? $this->id : false;
        }
    }

    /**
     * 批量新增修改
     * @param   array   $data   id+需更新的值
     * @return  array           所影响的所有数据   
     */
    public function allAddUser($data){
        if(!empty($data)){
            return  $this->saveAll($data)->toArray();
        }
    }

    // 获取用户信息
    public function getUser($map=array(),$field=true,$order='',$limit=''){
        
        $order = 'id asc';
        return $this->field($field)->where($map)->order($order)->limit($limit)->select()->toArray();
    }
    
    // 删除用户
    public function deleteUser($data){
        return $this->where($data)->delete();
    }

}