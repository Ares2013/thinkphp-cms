<?php
/**
 +------------------------------------------------------------------------------
 * 员工所属组模型
 +------------------------------------------------------------------------------
 * @category   Model
 * @package  Lib
 * @subpackage  Model
 * @author   668FreeCloud <chinasoftstar@qq.com>
 * @version  $Id: NodeModel.class.php 2791 2013/7/28  668FreeCloud $
 +------------------------------------------------------------------------------
 */
class UserGroupModel extends CommonModel {
	public $_validate	=	array(
		array('title','/^[a-z]\w{3,}$/i','帐号格式错误'),
		//array('password','require','密码必须'),
		//array('title','require','昵称必须'),
		//array('repassword','require','确认密码必须'),
		//array('repassword','password','确认密码不一致',self::EXISTS_VALIDATE,'confirm'),
		//array('account','','帐号已经存在',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT),
                array('nickname','','内部小组名已经存在',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT),
		);

	public $_auto		=	array(
		array('create_time','time',self::MODEL_INSERT,'function'),
		array('update_time','time',self::MODEL_UPDATE,'function'),
		);
}
?>
