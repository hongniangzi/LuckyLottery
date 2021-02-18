<?php
// 分页类库
// +----------------------------------------------------------------------
// | PHP version 5.4+
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.17php.cn, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhujinkui <developer@zhujinkui.com>
// +----------------------------------------------------------------------

namespace think;

class Page
{
    public  $page;          //当前页
    public  $total;         //总记录数
    public  $listRows;      //每页显示记录数
    private $uri;           //动态url
    public  $pageNum;       //总页数
    private $listNum = 10;  //显示页码按钮数量
    public  $render;        //分页后的html模板
    public  $data;          //分页后渲染到模板的数据
    public  $param;         // 参数 (解决翻页时丢失条件参数问题)

    /**
     * [__construct 初始化分页数据]
     * @param integer  $sdata    [待分页的数据]
     * @param integer $listRows [每页记录数]
     */
    public function __construct($sdata,$uri ,$total, $listRows = 15, $p, $param='')
    {
        $this->total    = $total;
        $this->listRows = $listRows;
        $this->uri      = $uri ? $uri : $this->getUri();
        $this->page     = $p ? $p:1;
        $this->param    = $param;
        $this->pageNum  = ceil($this->total/$this->listRows);
        $this->render   = $this->pageHtml();
        // $this->data     = array_slice($sdata, ($this->page-1)*$this->listRows,$listRows);
        $this->data     = $sdata; 
        return $this->data;
    }

    /**
     * [getUri 动态获取url]
     * @return string
     */
    private function getUri()
    {
        $url   = $_SERVER["REQUEST_URI"].(strpos($_SERVER["REQUEST_URI"], '?')?'':"?");
        $parse = parse_url($url);

        if(isset($parse["query"])){
            parse_str($parse['query'],$params);
            unset($params["p"]);
            $url = $parse['path'].'?'.http_build_query($params);
        }

        return $url;
    }

    /**
     * [first 首页]
     * @return string
     */
    private function first()
    {
        $html = "";
        if($this->page == 1)
            $html .= " <a style='magin=10px;' class='current disabled'>首 页</a>";
        else
            $html .= " <a class='btn-primary-outline' href='{$this->uri}/p/1.html".$this->param."'>首 页</a>";

        return $html;
    }

    /**
     * [prev 上一页]
     * @return string
     */
    private function prev()
    {
        $html = "";
        if($this->page == 1)
            $html.= " <a class='current disabled'>上一页</a>";
        else
            $html.= " <a class='btn-primary-outline' href='{$this->uri}/p/".($this->page-1).".html".$this->param."'>上一页</a>";

        return $html;
    }

    /**
     * [pageList 页码按钮]
     * @return string
     */
    private function pageList()
    {
        $linkPage = "";

        $inum     = floor($this->listNum/2);

        for($i = $this->page-$inum;$i<=$this->page+$inum;$i++){
            if($i <= 0) {
                continue;
            }

            if($i>$this->pageNum) {
                continue;
            }

            if($i == $this->page) {
                $linkPage.=" <a class='current btn-secondary' style='color:red;'>{$i}</a>";
            } else {
                $linkPage.=" <a class='btn-primary-outline' href='{$this->uri}/p/{$i}.html".$this->param."'>{$i}</a>";
            }
        }

        return $linkPage;
    }

    /**
     * [next 下一页]
     * @return string
     */
    private function next()
    {
        $html = "";
        if($this->page==$this->pageNum)
            $html.=" <a class='current disabled'>下一页</a>";
        else
            $html.=" <a class='btn-primary-outline' href='{$this->uri}/p/".($this->page+1).".html".$this->param."'>下一页</a>";
        return $html;
    }

    /**
     * [last 尾页]
     * @return string
     */
    private function last()
    {
        $html = "";
        if($this->page==$this->pageNum)
            $html.=" <a class='current disabled'>尾 页</a>";
        else
            $html.=" <a class='btn-primary-outline' href='{$this->uri}/p/".($this->pageNum).".html".$this->param."'>尾 页</a>";

        return $html;
    }

    /**
     * [goPage 输入指定页码]
     * @return string
     */
    private function goPage()
    {
        return '  <input class="input-text" type="text" onkeydown="javascript:if(event.keyCode==13){var page=(this.value>'.$this->pageNum.')?'.$this->pageNum.':this.value;location=\''.$this->uri.'/p/\'+page+\'\'.html'.$this->param.'}" value="'.$this->page.'" style="width:35px; text-align:center;margin-right:5px;"><input class="btn-secondary" type="button" value="GO" onclick="javascript:var page=(this.previousSibling.value>'.$this->pageNum.')?'.$this->pageNum.':this.previousSibling.value;location=\''.$this->uri.'/p/\'+page+\'\'".html'.$this->param.'>  ';
    }

    /**
     * [selectPage 选择指定页码]
     * @return string
     */
    function selectPage()
    {
        $inum       = 10;
        $location   = $this->uri.'/p/';
        $selectPage = "<span class='va-m'>到第 </span> <span class='select-box' style='width:initial'><select class='select' style='width:35px !important; height:23px; text-align:center; vertical-align:inherit !important;position: relative;top: 1px;' name='topage' size='1' onchange='window.location=\"$location\"+this.value+\".html$this->param\"'>";

        for($i=$this->page-$inum;$i<=$this->page+$inum;$i++){
            if($i<=0){
                continue;
            }
            if($i>$this->pageNum){
                continue;
            }
            if($i == $this->page){
                $selectPage .="<option value='$i' selected>$i</option>";
            }else{
                $selectPage .="<option value='$i'>$i</option>";
            }
        }

        $selectPage .="</select></span> <span class='va-m'>页</span>";

        return $selectPage;
    }

    /**
     * [pageHtml 组装分页的html模板]
     * @return string
     */
    function pageHtml()
    {
        $html  = "<div class='cl mt-20 text-c' style='margin: 5px 10px 5px 0px;'>&emsp;";
        $html .= "<span class='pr-20'>共有 <b>{$this->total}</b> 条记录</span>&emsp;";
        $html .= "<span class='pr-20'>每页显示 <b>{$this->listRows}</b> 条</span>&emsp;";
        $html .= "<span class='pr-20'><b>当前 {$this->page}/{$this->pageNum} </b>页</span>&emsp;";

        $html .= '<div class="page-list">'.$this->first()."&emsp;";
        $html .= $this->prev()."&emsp;";
        $html .= $this->pageList()."&emsp;";
        $html .= $this->next()."&emsp;";
        $html .= $this->last()."</div>&emsp;";
        // $html .= $this->goPage();
        $html .= $this->selectPage()."&emsp;";
        $html .= '</div>';

        return $html;
    }
}