<?php
/**
 +------------------------------------------------------------------------------
 * 后台用户模型
 +------------------------------------------------------------------------------
 * @category   Model
 * @package  Lib
 * @subpackage  Model
 * @author   Ares Peng <Z13053003@wistron.local><1534157801@qq.com>
 * @version  $Id: UserModel.class.php 2013/10/1  Ares Peng
 +------------------------------------------------------------------------------
 */
class UserModel extends CommonModel {
	public $_validate	=	array(
		//array('account','/^[a-z]\w{3,}$/i','帐号格式错误'),
		array('password','require','密码必须'),
		array('nickname','require','昵称必须'),
		array('repassword','require','确认密码必须'),
		array('repassword','password','确认密码不一致',self::EXISTS_VALIDATE,'confirm'),
		//array('account','','帐号已经存在',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT),
                array('job_number','','工号已经存在',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT),
		);

	public $_auto		=	array(
		array('password','pwdHash',self::MODEL_BOTH,'callback'),
		array('create_time','time',self::MODEL_INSERT,'function'),
		array('update_time','time',self::MODEL_UPDATE,'function'),
		);

	protected function pwdHash() {
		if(isset($_POST['password'])) {
			return pwdHash($_POST['password']);
		}else{
			return false;
		}
	}
       /*
        protected $_link = array(
            'UserGroup'=> array(  
                'mapping_type'=>BELONGS_TO,
                'class_name'=>'UserGroup',
                'foreign_key'=>'user_group_id',// 定义更多的关联属性
            ),
        );*/
}
?>