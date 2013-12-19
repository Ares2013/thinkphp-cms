<?php
/**
 +------------------------------------------------------------------------------
 * 配置信息模型
 +------------------------------------------------------------------------------
 * @category   Model
 * @package  Lib
 * @subpackage  Model
 * @author   Ares Peng<Z13053003@wistron.com><1534157801@qq.com>
 * @version  $Id: CreateArticleModel.class.php 2791 2013/10/1  Ares Peng $
 +------------------------------------------------------------------------------
 */
class CreateArticleModel extends Model {
        public $_validate=array(		
		array('repassword','password','确认密码不一致',self::EXISTS_VALIDATE,'confirm'),
		//array('code','','projectcode已经存在',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT),
                array('job_number','','工号已经存在',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT),
		);

	public $_auto	= array(
		array('c_time','time',self::MODEL_INSERT,'function'),
                array('create_time','time',self::MODEL_UPDATE,'function'),
		array('update_time','time',self::MODEL_UPDATE,'function'),
		);
}
?>
