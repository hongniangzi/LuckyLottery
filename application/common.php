<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

// 读取Excel文件
function readExcel($file='',$extension='xlsx'){
	try {
		$res['status'] = 0;
		date_default_timezone_set('Asia/ShangHai');
		include_once dirname(ROOT_PATH).'/thinkphp/library/Vendor/PHPExcel/PHPExcel.php';
		include_once dirname(ROOT_PATH).'/thinkphp/library/Vendor/PHPExcel/PHPExcel/Worksheet/Drawing.php';
		// Vendor('PHPExcel.PHPExcel');
		// Vendor('PHPExcel.PHPExcel.Worksheet.Drawing');

		// $reader = PHPExcel_IOFactory::createReader('Excel2007'); 
		//设置以Excel5格式(Excel97-2003工作簿)
		if ($extension =='xlsx') {
			$reader = PHPExcel_IOFactory::createReader('Excel2007'); 
		} else if ($extension =='xls') {
			$reader = PHPExcel_IOFactory::createReader('Excel5');
		} else if ($extension=='csv') {
			$reader = PHPExcel_IOFactory::createReader('CSV'); 
			//默认输入字符集
			$reader->setInputEncoding('GBK');
			//默认的分隔符
			$reader->setDelimiter(',');
		}
		
		$PHPExcel = $reader->load($file); // 载入excel文件
		$sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
		$highestRow = $sheet->getHighestRow(); // 取得总行数
		$highestColumm = $sheet->getHighestColumn(); // 取得总列数
		
		/** 循环读取每个单元格的数据 */
		$arr = array();
		for ($row = 1; $row <= $highestRow; $row++){//行数是以第1行开始
			for ($column = 'A'; $column <= $highestColumm; $column++) {//列数是以A列开始
				$arr[$row][] =  $sheet->getCell($column.$row)->getValue();
			}
		}
		$res['status'] = 1;
		$res['Msg'] = $arr;
	} catch(\Exception $e) {
		$res['Msg'] = $e->getMessage();
	
	}
	return $res;
}