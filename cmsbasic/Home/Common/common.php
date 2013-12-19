<?php

//公共函数 时间格式化
function toDate($time, $format = 'Y-m-d H:i:s') {
	if (empty ( $time )) {
		return '';
	}
	$format = str_replace ( '#', ':', $format );
	return date ($format, $time );
}

//公共函数 格式化 为 几天前
function mdate($time = NULL) {
   $text = '';
   $time = $time === NULL || $time > time() ? time() : intval($time);
   $t = time() - $time; //时间差 （秒）
   if ($t == 0)
       $text = '刚刚';
    elseif ($t < 60)
       $text = $t . '秒前'; // 一分钟内
    elseif ($t < 60 * 60)
       $text = floor($t / 60) . '分钟前'; //一小时内
    elseif ($t < 60 * 60 * 24)
       $text = floor($t / (60 * 60)) . '小时前'; // 一天内
    elseif ($t < 60 * 60 * 24 * 2)
       $text = '昨天 ' . date('H:i', $time); //两天内
    elseif ($t < 60 * 60 * 24 * 3)
       $text = '前天 ' . date('H:i', $time); // 三天内
    elseif ($t < 60 * 60 * 24 * 30)
       $text = date('m月d日 H:i', $time); //一个月内
    elseif ($t < 60 * 60 * 24 * 365)
       $text = date('m月d日', $time); //一年内
    else
       $text = date('Y年m月d日', $time); //一年以前
       return $text;
}

// 公共函数 把秒数格式化为 n天n小时n分n秒

function time2string($second){
	$day = floor($second/(3600*24));
	$second = $second%(3600*24);//除去整天之后剩余的时间
	$hour = floor($second/3600);
	$second = $second%3600;//除去整小时之后剩余的时间 
	$minute = floor($second/60);
	$second = $second%60;//除去整分钟之后剩余的时间 
	//返回字符串
	return $day.'天'.$hour.'小时'.$minute.'分'.$second.'秒';
}
//获取域名
function getDomain()
{
    return $_SERVER['HTTP_HOST'];
}


