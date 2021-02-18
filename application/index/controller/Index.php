<?php
namespace app\index\controller;
use app\index\model\User;
use app\index\model\Probability;

class Index extends Home{

    protected $url;
    protected static $user_pro; //总抽取池
    protected static $default_user; //默认全部内定人员
    protected static $default_this; //本次抽取的内定人员
    protected static $default_staff; //非本次抽取的所有内定人员

    protected function initialize(){
        self::$default_this = array();// 本次内定人员
        self::$default_user = array();// 内定人员

        $this->url = $this->request->url();
    }

    /**
     * 抽取首页
     */
    public function index(){
        return  $this->fetch(__FUNCTION__);
    }

    /**
     * 获取池中用户
     * 
     */
    public function getExtractionPool(){
        $result['status'] = 0;
        $extractionPool = session('extractionPool');
        if(!$extractionPool){
            if(session('inPump')){
                $result['Msg'] = '抽取池内已无可用人员';
                return json($result);
                die;
            }
            $user = (new User())->getUser();
            $probability = (new Probability())->getProbability();
            if($probability){
                foreach($user as $k =>$val){
                    foreach($probability as $key =>$value){
                        if($val['id_card']==$value['id_card']){
                            $val['probability'] = $value['probability'];
                        }
                        
                        $user_pro[$val['id']] = $val;
                        $username_arr[$k] = $val['username'];
                    }
                }
            }else{
                foreach($user as $k =>$val){
                    $user_pro[$val['id']] = $val;
                    $username_arr[$k] = $val['username'];
                }
            }
            // 缓存总抽取池
            @session('extractionPool',$user_pro);
            
        }else{
            foreach($extractionPool as $k =>$val){
                $username_arr[] = $val['username'];
            }
        }
        if(!empty($username_arr)){
            $result['status'] = 1;
            $result['Msg'] = ($username_arr);
        }else{            
            $result['Msg'] = '请先导入用户数据';
        }
        return json($result);
    }

    /**
     * 抽区区
     */
    public function pump(){
        if($this->isAjax()){
            $extractionPool = session('extractionPool');
            $data = $this->param();
            $result['status'] = 0;
            
            if(empty($data['luckyDrawNum']) || $data['luckyDrawNum']<1){
                $result['Msg'] = '请输入正确的抽取人数 #1';
                return $result;
            }
            $get_inpump_num = self::getInPumpNum($data['luckyDrawNum']);
            if($get_inpump_num['status']==0){
                return json($get_inpump_num);
            }else{
                $inPumpPople = $get_inpump_num['inPumpPople'];
            }
            if(!$extractionPool){
                if(session('inPump')){
                    $result['Msg'] = '抽取池内已无可用人员 #2';
                    return json($result);
                    die;
                }
                $user = (new User())->getUser();
                $probability = (new Probability())->getProbability();
                if($probability){
                    foreach($user as $k =>$val){
                        foreach($probability as $key =>$value){
                            if($val['id_card']==$value['id_card']){
                                $val['probability'] = $value['probability'];
                            }
                            $user_pro[$val['id']] = $val;
                        }
                    }
                }else{
                    foreach($user as $k =>$val){
                        $user_pro[$val['id']] = $val;
                    }
                }
                // 缓存总抽取池
                session('extractionPool',$user_pro);
            }else{
                $user_pro = $extractionPool;

                $probability = (new Probability())->getProbability();
                if($probability){
                    foreach($probability as $key =>$value){
                        if(!empty($value['probability']) && $value['probability']=='内定'){
                            $default_user[$value['id']]['def_num'] = $value['def_num'];
                            $default_user[$value['id']]['id_card'] = $value['id_card'];
                        }
                    }
                    if(!empty($default_user)){
                        self::$default_user = $default_user;
                    }
                }
            }
            self::$user_pro = $user_pro; // 本次抽取池
            // var_dump($user_pro);
            $ret = $this->get_gift($data['luckyDrawNum']); //开始抽取
            if($ret || self::$default_this){
                // 抽中名单session缓存
                $inPump = session('inPump')?session('inPump'):array();
                $ret = array_merge($ret,self::$default_this);
                $inPump = array_merge($inPump,array($ret));
                session('inPump',$inPump);
                $result['status'] = 1;
                $result['Msg'] = $ret;
                $result['inPumpPople'] = $inPumpPople;
            }else{
                $result['Msg'] = '抽取失败 #3';
            }
            return json($result);
            // var_dump('<pre>',$ret,session('inPump'),session('extractionPool'));
        }else{
            $inPump = session('inPump');
            $extractionPool = session('extractionPool');
            
            $this->assign('inPump',$inPump);
            $this->assign('extractionPool',$extractionPool);
            return  $this->fetch(__FUNCTION__);
        }
          
    }

    /**
     * 递归抽取
     * @param   int     $num        抽取次数
     * @param   array   $ret        每次在池中抽中的人
     */
    protected function get_gift($num=1,$ret=array()){ 
        //拼装奖项数组 
        // 奖项id，奖品，概率
        // $prize_arr = array( 
        // '0' => array('id'=>1,'prize'=>'平板电脑','v'=>1), 
        // '1' => array('id'=>2,'prize'=>'数码相机','v'=>1), 
        // '2' => array('id'=>3,'prize'=>'音箱设备','v'=>6), 
        // '3' => array('id'=>4,'prize'=>'4G优盘','v'=>1), 
        // '4' => array('id'=>5,'prize'=>'10Q币','v'=>8), 
        // '5' => array('id'=>6,'prize'=>'空奖','v'=>1), 
        // ); 
        
        $prize_arr = self::$user_pro;
        $default_user = self::$default_user;
        $inPump = session('inPump');
      
        if(!$prize_arr){
            return $ret;
        }
        // var_dump(count($inPump)+1);
        foreach ($prize_arr as $key => $val) { 
            foreach($default_user as $k=> $v){
            // for($i=0; $i<1; $i++){
                if($val['id_card']==$v['id_card'] && $num>0){
                    if($v['def_num']==count($inPump)+1){
                        self::$default_this[] = $val;
                        unset($default_user[$k]);
                        self::$default_user = $default_user;
                        // 移出抽取池内本次抽中人员
                        self::setExtractionPool(array($val));
                        $num--;
                    }else{
                        // 记录非本次抽取的内定人员
                        self::$default_staff[$key] = $val;
                    }
                    // 移出全部内定人员
                    unset($prize_arr[$key]);
                    self::$user_pro = $prize_arr;
                   
                }
            }
        }
        
        if($num>0){
            $arr = array();
            foreach ($prize_arr as $key => $val) { 
                $arr[$val['id']] = !empty($val['probability'])?$val['probability']:1;//概率数组 
                $user_arr[$val['id']] = $val;
            }
            
            $rid = $this->get_rand($arr); //根据概率获取奖项id 
            $ret[] = $user_arr[$rid]; //中奖项 
            if(!empty($ret)){
                // 移出抽取池内本次抽中人员
                self::setExtractionPool($ret);
            }
            // 放入非本次抽取的内定人员
            if(!empty(self::$default_staff)){
                $prize_arr = $prize_arr+self::$default_staff;
            }
            
            unset($prize_arr[$rid]); //将中奖项从数组中剔除，剩下未中奖项
            self::$user_pro = $prize_arr;
            
            return $this->get_gift($num-1,$ret);
        } else{
            return $ret;
        }
    } 
    
    //计算中奖概率
    protected function get_rand($proArr) { 
        $result = ''; 
        //概率数组的总概率精度 
        $proSum = array_sum($proArr); 
        // var_dump($proSum);
        //概率数组循环 
        foreach ($proArr as $key => $proCur) { 
            $randNum = mt_rand(1, $proSum); //返回随机整数 
        
            if ($randNum <= $proCur) { 
                $result = $key; 
                break; 
            } else { 
                $proSum -= $proCur; 
            } 
        } 
        unset ($proArr); 
        return $result; 
    } 
    
    
    /**
     * 检测抽取人数是否满足前端抽取量
     * @return      array       结果以及下次抽取的数量
     */
    protected static function getInPumpNum($luckyDrawNum=0){
        $result['status'] = 0;
        
        $inPumpNum = count(session('inPump')); // 抽的次数
        $extractionPool = session('extractionPool');
        $extractionPoolNum = count($extractionPool); // 池内可抽人数总和
        $default_user = self::$default_user; // 内定人员
        $pople_num = 0; // 本次内定人数
        foreach($extractionPool as $key =>$value){
            foreach($default_user as $k =>$val){
                if($value['id_card']==$val){
                    $pople_num++;
                    if(($inPumpNum+1)==$k){
                        $pople_num--;
                    }
                }
            }
        }

        //本次非内定
        if(($extractionPoolNum-$pople_num)<$luckyDrawNum){
            $result['Msg'] = '池内抽取人数不足，请减少抽取人数';
        }else{
            $result['status'] = 1;
            $result['Msg'] = '正常抽取';
            $result['inPumpPople'] = $extractionPoolNum-$luckyDrawNum;
        }
        // var_dump($result,session('extractionPool'),$pople_num,$luckyDrawNum);
        return $result;
    }

    /**
     * 将已抽中的人移出抽取池内
     * @param   array   $ret        本次抽中名单
     */
    protected static function setExtractionPool($ret){
        $extractionPool = session('extractionPool');
        foreach($ret as $key =>$value){
            foreach ($extractionPool as $k => $val) {
                # code...
                if($val['id']==$value['id']){
                    unset($extractionPool[$k]);
                }
            }
        }
        session('extractionPool',$extractionPool);
    }

    /**
     * 清空抽中名单
     * 清空总池
     */
    public function closeExtractionPool(){
        session('extractionPool',null);
        session('inPump',null);
        return $this->redirect('/pump');
    }

}
