<?php
$db_config	=	require '../config.inc.php';
$config	= array(
	/*
	 * 0:普通模式 (采用传统癿URL参数模式 )
	 * 1:PATHINFO模式(http://<serverName>/appName/module/action/id/1/)
	 * 2:REWRITE模式(PATHINFO模式基础上隐藏index.php)
	 * 3:兼容模式(普通模式和PATHINFO模式, 可以支持任何的运行环境, 如果你的环境不支持PATHINFO 请设置为3)
	 */
    'URL_MODEL'=>1,
	'DB_TYPE'=>'mysql',

	'APP_AUTOLOAD_PATH'=>'@.TagLib',
	'LANG_SWITCH_ON' => true,
	'LANG_AUTO_DETECT' => false, // 自动侦测语言 开启多语言功能后有效
	'LANG_LIST'        => 'zh-cn', // 允许切换的语言列表 用逗号分隔
	'DEFAULT_LANG'     =>'zh-cn',
	'VAR_LANGUAGE'     => 'l', // 默认语言切换变量
	'DEFAULT_THEME'=>'default',
        'sitename'=>'Task Loading Management System',
	'VAR_PAGE'=>'pageNum',

	/* SESSION设置 */
	'SESSION_NAME' => '' ,     // 默认Session_name
	'SESSION_PATH' => '' ,                     // 采用默认的Session save path 
	'SESSION_TYPE' => 'DB' ,                // 默认Session类型 支持 DB 和 File
	'SESSION_EXPIRE' => '300000' ,     // 默认Session有效期
	'SESSION_TABLE' => '' ,   // 数据库Session方式表名
	'SESSION_CALLBACK' => '' , 
	'SESSION_PREFIX'=>'ADMIN', //session 前缀

	'USER_AUTH_ON'=>true,           //是否开启用户认证
	'USER_AUTH_TYPE'=>1,		// 默认认证类型 1 登录认证 2 实时认证
	'USER_AUTH_KEY'=>'authId',	// 用户认证SESSION标记
        'ADMIN_AUTH_KEY'=>'administrator',
	'USER_AUTH_MODEL'=>'User',	// 默认验证数据表模型
	'AUTH_PWD_ENCODER'=>'md5',	// 用户认证密码加密方式
	'USER_AUTH_GATEWAY'=>'/Public/login',	// 默认认证网关
	'NOT_AUTH_MODULE'=>'Public',		// 默认无需认证模块
	'REQUIRE_AUTH_MODULE'=>'',		// 默认需要认证模块
	'NOT_AUTH_ACTION'=>'',		// 默认无需认证操作
	'REQUIRE_AUTH_ACTION'=>'',		// 默认需要认证操作
        'GUEST_AUTH_ON'=>false,    // 是否开启游客授权访问
        'GUEST_AUTH_ID'=>0,     // 游客的用户ID

        'DB_LIKE_FIELDS'=>'title|remark',
	'RBAC_ROLE_TABLE'=>'lhl_role',
	'RBAC_USER_TABLE'=>'lhl_role_user',
	'RBAC_ACCESS_TABLE'=>'lhl_access',
	'RBAC_NODE_TABLE'=>'lhl_node',

	//xheditor文本编辑器上传配置
	'UPLOAD_URL'=>'../Public/Upload/article',
	'__UPLOAD__'=>'/Public/Upload/article/',
	'__GOODSIMG__'=>'/Public/Upload/goods/',
	'__TMPL__'=>'/Admin/Tpl/default/',
	'WEB_PUBLIC_PATH'=>'/Public/Images/',

	//记录日志操作的模块与操作
	'LOG_APP' => array(
		'RoleNode' => array('insert' , 'update' , 'remove' , 'editfield' , 'togglestatus') , //权限节点的增删改日志记录
		'SysConf' => array('update') , //修改系统设置
	),
        'TMPL_PARSE_STRING'=>array(
            '__CHARTT__'=>__ROOT__.'/JS/charts',
        ),

);

return array_merge($db_config,$config);
?>