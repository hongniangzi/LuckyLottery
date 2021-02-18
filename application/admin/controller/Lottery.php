<?php
namespace app\admin\controller;
use app\index\model\User;
use app\index\model\Probability;

class Lottery extends Admin{

    protected $url;

    protected function initialize(){
        $this->url = $this->request->url();
    }

    /**
     * 后台人员设置
     */
    public function setlottery(){

        if($this->isAjax()){
            $data = $this->param();
            $result['status'] = 0;

            if(empty($data['id_card']) || empty($data['name']) || empty($data['probability'])){
                $result['Msg'] = '部分数据丢失';
                return $result;
            }
            foreach($data['id_card'] as $key =>$value){
                if($value){
                    $arr[$key]['id_card'] = trim($value);
                }
                if(!empty($data['id'][$key])){
                    $arr[$key]['id'] = trim($data['id'][$key]);
                }
                if(!empty($data['probability'][$key])){
                    $arr[$key]['probability'] = trim($data['probability'][$key]);
                }
                if(!empty($data['name'][$key])){
                    $arr[$key]['name'] = trim($data['name'][$key]);
                }
                if(!empty($data['def_num'][$key])){
                    $arr[$key]['def_num'] = trim($data['def_num'][$key]);
                }
            }

            if(!empty($arr)){
                if((new Probability())->allAddProbability($arr)){
                    $result['status'] = 1;
                    $result['Msg'] = '已保存';
                }else{
                    $result['Msg'] = '保存失败 #2';
                }
            }else{
                $result['Msg'] = '保存失败 #1';
            }
            return $result;

        }else{
            $user = (new User())->getUser();
            $probability = (new Probability())->getProbability();

            $probability_arr = array('pro'=>'','def'=>'');
            $pro_sum = '';
            foreach($probability as $key =>$val){
                if(is_numeric($val['probability'])){
                    $probability_arr['pro'][] = $val;
                    $pro_sum += $val['probability'];

                }else{
                    $probability_arr['def'][] = $val;
                }
            }
            
            $this->assign('user',$user);
            $this->assign('probability',$probability_arr);
            $this->assign('pro_sum',$pro_sum);
            return $this->fetch(__FUNCTION__);
        }
    }

    /**
     * 删除概率用户
     */
    public function delProbability(){
        $data = $this->param();
        $resutl['status'] = 0;

        if(empty($data['id']) || !is_numeric($data['id'])){
            $resutl['Msg'] = 'ID编号丢失';
            return $resutl;
        }

        if((new Probability())->deleteProbability(array('id'=>$data['id']))){
            $resutl['status'] = 1;
            $resutl['Msg'] = '删除成功';
        }else{
            $resutl['Msg'] = '删除失败';
        }
        return $resutl;
    }


    /**
     * 新增用户数据
     */
    public function addUserData(){
        $data = $this->param();
        $resutl['status'] = 0;
        if(empty($data['username']) || empty($data['phone']) || empty($data['id_card']) || empty($data['profession']) || empty($data['unit'])){
            $resutl['Msg'] = '部分数据走失';
            return $resutl;
        }

        $id = (new User())->addUser($data);
        if($id){
            $data['id'] = $id;
            $resutl['status'] = 1;
            $resutl['Msg'] = $data;
        }else{
            $resutl['Msg'] = '新增失败';
        }
        return $resutl;
        
    }

    /**
     * 删除用户数据
     */
    public function delUserData(){
        $data = $this->param();
        $resutl['status'] = 0;

        if(empty($data['id']) || !is_numeric($data['id'])){
            $resutl['Msg'] = 'ID编号丢失';
            return $resutl;
        }

        if((new User())->deleteUser(array('id'=>$data['id']))){
            $resutl['status'] = 1;
            $resutl['Msg'] = '删除成功';
        }else{
            $resutl['Msg'] = '删除失败';
        }
        return $resutl;
    }

    /**
     * Excel导入识别
     */
    public function identifyExcel(){
        $data = $this->param();
        $resutl['status'] = 0;
        $file = @$_FILES['file'];

        if($file['error']!=0){
            $result['Msg'] = '文件上传错误';
            return json($result);
        }
        $suffix = mb_substr($file['name'],mb_strrpos($file['name'],'.')+1);
        $new_path = 'Uploads/Excel';
        if(!is_dir($new_path)){
            mkdir($new_path);
        }
        $new_path = $new_path.'/'.md5(time()).time().'.'.$suffix;
        move_uploaded_file($file['tmp_name'],$new_path);
        $peoples = readExcel($new_path,$suffix);
        @unlink($new_path);
        if($peoples['status']!=1){
            $result['Msg'] = $peoples['Msg'];
            return json($result);
        }
        if(empty($peoples['Msg'][1])){
            $result['Msg'] = '无识别内容';
            return json($result);
        }
        unset($peoples['Msg'][1]);
        $people = array_values($peoples['Msg']);
        foreach($people as $key =>$value){
            $arr[$key]['username'] = $value[0]; // 姓名
            $arr[$key]['phone'] = $value[1]=='null'?"":$value[1]; // 电话
            $arr[$key]['id_card'] = $value[2]; // 身份证
            $arr[$key]['profession'] = $value[3]=='null'?"":$value[3]; // 职称
            $arr[$key]['unit'] = $value[4]=='null'?"":$value[4]; // 单位
        }
        if(!empty($arr)){
            $result['status'] = 1;
            $result['Msg'] = $arr;
            session('ExcelPeople',$arr);
        }else{
            $result['status'] = 0;
            $result['Msg'] = '无识别内容 #2';
        }
        return json($result);
    }

    /**
     * 记录导入数据至数据库
     */
    public function importExcel(){
        $result['status'] = 0;
        $arr = session('ExcelPeople');

        if(!empty($arr)){
            if((new User())->allAddUser($arr)){
                $result['status'] = 1;
                $result['Msg'] = '导入成功';
                session('ExcelPeople',null);
            }
        }else{
            $result['Msg'] = '无可用内容';
        }
        return $result;
    }

    /**
     * 清空用户数据
     */
    public function closeUserData(){

        try{
            \think\Db::query("TRUNCATE TABLE pgy_user");
        }catch(\Exception $e){
            $result['status'] = 0;
            $result['Msg'] = '清空失败,'.$e->getMessage();
            return $result;
        }
        $result['status'] = 1;
        $result['Msg'] = '清空成功';
         return $result;
         
    }
}