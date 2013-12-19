<?php
$db_config = require("config.inc.php");
$home_config =  array(
	'DB_TYPE'		=>	'mysql',// 数据库类型	
	'DB_CHARSET'	=>	'utf8',// 网站编码
	'DB_PORT'		=>	'3306',// 数据库端口
	'APP_DEBUG'     =>  true,// 开启调试模式

	'TMPL_CACHE_ON'			=> false, 		//开启模板缓存
	'URL_CASE_INSENSITIVE'  => true, 		//URL不区分大小写
	'URL_PATHINFO_DEPR'     =>'-',
	'URL_MODEL'             => 2,           //服务器开启Rewrite模块时，可去除URL中的index.php
	'URL_HTML_SUFFIX'       => '.html',
	'CHARSET'=>'utf-8',

	//配置路径
	'TMPL_PARSE_STRING'=>array(
		'__SKIN__'=>'/Public/themes/home/',
	),
);
return array_merge($db_config, $home_config);
