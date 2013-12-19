<?php
defined('ROOT_PATH') or define('ROOT_PATH', dirname(__FILE__));
if (!is_file(ROOT_PATH . '/data/install.lock')) {
    header('Location: ./Install/index.php');
    exit;
}

define('THINK_PATH','./ThinkPHP/');
define('APP_NAME','Home');
define('PUBLIC_PATH','./Public/');
define('APP_PATH','./Home/');
define('APP_DEBUG', true); //调试模式开关
define('SESS_ID',session_id());
require THINK_PATH.'ThinkPHP.php';
