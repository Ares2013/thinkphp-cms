<?php
/**
 +------------------------------------------------------------------------------
 * 广告位模型
 +------------------------------------------------------------------------------
 * @category   Model
 * @package  Lib
 * @subpackage  Model
 * @author   zhujiangtao <tiger6681517@qq.com>
 * @version  $Id: AdvPositionModel.class.php 2791 2012/11/7 14:45  zhujiangtao $
 +------------------------------------------------------------------------------
 */
class AdvPositionModel extends CommonModel
{
	protected $tableName = 'ad_position';
	protected $pk="position_id";
	public $_validate = array(
		array('position_name','require','广告位名称必须填写！'),
	);

	public function getTemplate(){
		$modules = array('adv'=>'默认模版','adv_myfocue'=>'首页数字轮换','adv_lunhuan'=>'其他');
		return $modules;
	}
}
?>