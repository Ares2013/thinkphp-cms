<?php
/**
 +------------------------------------------------------------------------------
 * 前台菜单分类模型
 +------------------------------------------------------------------------------
 * @category   Model
 * @package  Lib
 * @subpackage  Model
 * @author   668FreeCloud <chinasoftstar@qq.com>
 * @version  $Id: NavCategoryModel.class.php 2791 2013/7/28  668FreeCloud $
 +------------------------------------------------------------------------------
 */
class NavCategoryModel extends CommonModel
{
	protected $_validate = array(
		array('name','require','{%NAME_REQUIRE}'),
	);

	protected $_auto = array( 
		array('status','1'),  // 新增的时候把status字段设置为1	
	);
}
?>