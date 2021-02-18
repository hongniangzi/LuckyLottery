<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// Route::get('think', function () {
//     return 'hello,ThinkPHP5!';
// });

// Route::get('hello/:name', 'index/hello');

return [

    // 前台
    "index"                 =>  "index/Index/index", //首页
    "getExtractionPool"     =>  "index/Index/getExtractionPool", //获取用户抽取池
    "pump"                  =>  "index/Index/pump", // 抽取区
    "closeExtractionPool"   =>  "index/Index/closeExtractionPool", //清空抽取池以及抽中名单


    // 后台
    "setlottery"            =>  "admin/Lottery/setlottery", // 抽取概率设置
    "lottery/delProbability"=>  "admin/Lottery/delProbability", // 删除概率用户

    "lottery/identifyExcel" =>  "admin/Lottery/identifyExcel", // Excel导入识别
    "lottery/importExcel"   =>  "admin/Lottery/importExcel", // 记录数据库
    "lottery/addUserData"   =>  "admin/Lottery/addUserData", // 新增用户数据
    "lottery/delUserData"   =>  "admin/Lottery/delUserData", // 删除用户数据
    "lottery/closeUserData" =>  "admin/Lottery/closeUserData", //清空用户数据
];
