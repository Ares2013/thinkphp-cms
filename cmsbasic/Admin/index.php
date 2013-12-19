<?php
// +----------------------------------------------------------------------
// | Author: zhujiangtao
// +----------------------------------------------------------------------

//定义项目名称和路径
defined('ROOT_PATH') or define('ROOT_PATH', dirname(dirname(__FILE__)));
if (!is_file(ROOT_PATH . '/data/install.lock')) {
    header('Location: /Install/index.php');
    exit;
}
define('THINK_PATH','../ThinkPHP/');
define('PUBLIC_PATH','../Public/');
define('APP_NAME', 'Admin');
define('APP_PATH', './');
define('APP_DEBUG', true); //调试模式开关
// 加载框架入口文件
require THINK_PATH.'ThinkPHP.php';

?>