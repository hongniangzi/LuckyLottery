<?php
namespace think\verify;

class Verify{
    private $width;//验证码的宽
    private $height;//验证码的高    
    private $codeNum;//验证码中的字符数
    private $type;//验证码中字符的类型，1=数字，2=字母，3=字母加数字(默认)
    private $fontStyle;//验证码字体样式，需引入字体文件    
    private $dot;//验证码雪花点数    
    private $line;//验证码干扰线条
    private $image;//验证码图片
    private $chars;//验证码字符串
    
    function __construct($width=100,$height=40,$codeNum=4,$type=3,$session='Verify',$fontStyle='ygyxsziti2.0.ttf',$dot=50,$line=4){
        session_start();
        $this->width=$width;
        $this->height=$height;
        $this->codeNum=$codeNum;
        $this->type=$type;
        $this->session=$session;
        $this->fontStyle=__DIR__.'/'.$fontStyle;;
        $this->dot=$dot;
        $this->line=$line;
        $this->image=$this->createImage();    
        $this->chars=$this->createChar();
        $_SESSION[$this->session] = $this->chars;//将验证码的值放入session中，以便验证登陆    
    }
    //展现图片
    public function showImage($red=232,$green=155,$blue=55){
        $color=imagecolorallocate($this->image, $red, $green, $blue);
        imagefilledrectangle($this->image, 0, 0, $this->width, $this->height, $color);
        for($i = 0; $i <$this->codeNum; $i++) {
            $size = mt_rand ( 16, 18 );//取值有待完善
            $angle = mt_rand ( - 15, 15 );
            $x = 10 + $i * $size;
            $y = mt_rand ( 20, 26 );
            $color=imagecolorallocate($this->image,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
            $text = substr ( $this->chars, $i, 1 );
            imagettftext ( $this->image, $size, $angle, $x, $y, $color, $this->fontStyle, $text );
        }
        
        $this->interferon();
        header ( "content-type:image/gif" );
        imagegif ( $this->image );//显示图片
        imagedestroy ( $this->image );//销毁图片
        
    }
    //生成图片
    private function createImage(){        
        $image=imagecreatetruecolor($this->width, $this->height);                
        return $image;    
    }
    //生成验证码字符
    private function createChar(){
        if($this->type==1){
            $chars=implode('', range(0, 9));//生成数字
        }else if($this->type==2){
            $chars=implode('', array_merge(range('A','Z'),range('a', 'z')));//生成字母
        }elseif ($this->type==3) {
            $chars=implode('', array_merge(range(0, 9),range('A','Z'),range('a', 'z')));//生成数字字母
        }
        $chars=str_shuffle($chars);//打乱字符串的排序
        if($this->codeNum>strlen($chars)){
            exit('数字过大');//判断截取的个数是不是超过字符串的长度
        }
        $chars=substr($chars, 0,$this->codeNum);//截取字符
        return $chars;
    }
    //生成干扰元素
    private function interferon(){
        for ($i=0; $i <$this->line ; $i++) { 
            $color = imagecolorallocate ($this->image, mt_rand ( 0, 255 ), mt_rand ( 0, 255 ), mt_rand ( 0, 255 ) );
            imageline($this->image,mt_rand(0, $this->width-1),mt_rand(0, $this->height-1), mt_rand(0, $this->width-1), mt_rand(0, $this->height-1), $color);
        }
        for ($i=0; $i <$this->dot ; $i++) { 
            $color = imagecolorallocate ($this->image, mt_rand ( 0, 255 ), mt_rand ( 0, 255 ), mt_rand ( 0, 255 ) );    
            imagesetpixel($this->image,mt_rand(0, $this->width-1) , mt_rand(0, $this->height-1), $color);        
        }
    }
    
}
// $a =new securityCode();
// $a->showImage();