<?php
/**
 +------------------------------------------------------------------------------
 * 广告位模型
 +------------------------------------------------------------------------------
 * @category   Model
 * @package  Lib
 * @subpackage  Model
 * @author   668FreeCloud <chinasoftstar@qq.com>
 * @version  $Id: AdvPositionModel.class.php 2791 2013/7/28  668FreeCloud $
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
		$modules = array('adv'=>'默认模版','adv_pic_lunhuan'=>'图片轮换');
		return $modules;
	}
	public function getTemplateDate(){
		$modules = array(
			'adv'=>array(),
			'adv_pic_lunhuan'=>array(
								'mF_YSlider'=>'经典黄色数字轮换',
								'mF_luluJQ'=>'风琴折叠',
								'mF_pithy_tb'=>'缩略图轮换'
							)
		);
		return $modules;
	}
}
?>