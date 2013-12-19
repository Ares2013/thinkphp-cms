<?php
/**
 +------------------------------------------------------------------------------
 * 配置类型模型
 +------------------------------------------------------------------------------
 * @category   Model
 * @package  Lib
 * @subpackage  Model
 * @author   668FreeCloud <chinasoftstar@qq.com>
 * @version  $Id: GroupModel.class.php 2791 2013/7/28  668FreeCloud $
 +------------------------------------------------------------------------------
 */
class GroupModel extends CommonModel {
	protected $_validate = array(
		array('name','require','名称必须'),
		);

	protected $_auto		=	array(
        array('status',1,self::MODEL_INSERT,'string'),
		array('create_time','time',self::MODEL_INSERT,'function'),
		);
	public function getNav(){
		$modules = array('0'=>'系统管理','1'=>'会员管理','2'=>'内容管理','3'=>'商务管理',);//'4'=>'其他拓展'
		return $modules;
	}
       public function getUserGroup(){
           $user_group = array('0'=>'other','1'=>'Annie','2'=>'Astro','3'=>'Lily','4'=>'Rosa','5'=>'Yama');
           return $user_group;
       }
       public function getProjectType(){
           $projecttype = array('0'=>'other','1'=>'New','2'=>'New 1',);
           return $projecttype;
       }
       public function getTaskType(){
           $tasktype = array('0'=>'other','1'=>'EVT1','2'=>'EVT2','3'=>'DVT','4'=>'Refresh','5'=>'Sustain','6'=>'BOIS','7'=>'Driver','8'=>'Linux','9'=>'Windows OS','10'=>'HDD','11'=>'ODD','12'=>'Card Reader',
                                '13'=>'Panle','14'=>'Memory','15'=>'CPU','16'=>'VGA','17'=>'Monitor','18'=>'PSU','19'=>'TPM',
                                '20'=>'Blutooth','21'=>'KB-Mouse','22'=>'Wlan','23'=>'Webcam','24'=>'Speaker','25'=>'TV Turner'
               );
           return $tasktype;
       }
}
?>