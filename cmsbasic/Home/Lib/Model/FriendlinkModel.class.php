<?php
/**
 +------------------------------------------------------------------------------
 * 友情连接模型模型
 +------------------------------------------------------------------------------
 * @category   Model
 * @package  Lib
 * @subpackage  Model
 * @author   zhujiangtao <tiger6681517@qq.com>
 * @version  $Id: FriendlinkModel.class.php 2791 2012/11/5 15:38  zhujiangtao $
 +------------------------------------------------------------------------------
 */
class FriendlinkModel extends Model
{
	protected $tableName = 'friend_link';
	protected $_validate =array(
		array('link_name','require','网站名称不能为空必须！'), //默认情况下用正则进行验证
		array('link_url','require','网址不能为空必须！'), //默认情况下用正则进行验证
	);

	/**
	+----------------------------------------------------------
	* 调用友情连接列表
	* 
	+----------------------------------------------------------
	* @access public
	+----------------------------------------------------------
	* @param int $top 指定要需要调用的条数
	* 默认为10 
	* 
	* 
	+----------------------------------------------------------
	* @return array
	+----------------------------------------------------------
	*/
	public function getLinkList($top=10)
	{
		$arr=array();
		$where=array();
		$arr=$this->where($where)->order('show_order desc')->limit($top)->select();
		return $arr;
	}
}