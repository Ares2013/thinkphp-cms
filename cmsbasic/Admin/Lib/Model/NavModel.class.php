<?php
/**
 +------------------------------------------------------------------------------
 * 前台菜单模型
 +------------------------------------------------------------------------------
 * @category   Model
 * @package  Lib
 * @subpackage  Model
 * @author   668FreeCloud <chinasoftstar@qq.com>
 * @version  $Id: NavModel.class.php 2791 2013/7/28  668FreeCloud $
 +------------------------------------------------------------------------------
 */
class NavModel extends CommonModel
{
	protected $_validate = array(
		array('name','require','{%NAME_REQUIRE}'),
		array('url','require','{%URL_REQUIRE}'),
	);

	protected $_auto = array( 
		array('status','1'),  // 新增的时候把status字段设置为1	
	);
}
?>