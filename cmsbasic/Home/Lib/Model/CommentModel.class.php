<?php
/**
 +------------------------------------------------------------------------------
 * 评论模型
 +------------------------------------------------------------------------------
 * @category   Model
 * @package  Lib
 * @subpackage  Model
 * @author   zhujiangtao <tiger6681517@qq.com>
 * @version  $Id: CommentModel.class.php 2791 2012/8/24  zhujiangtao $
 +------------------------------------------------------------------------------
 */
class CommentModel extends Model
{
	protected $tableName = 'comment';

	protected $_auto = array ( 
		array('status','1'),  // 新增的时候把status字段设置为1
		array('ip_address','get_client_ip',1,'function'), // 对name字段在新增的时候回调getName方法
		array('add_time','time',1,'function') // 对create_time字段在更新的时候写入当前时间戳
	);


}