<?php


class CreateTaskModel extends RelationModel{
        public $_validate=array(		
		//array('repassword','password','确认密码不一致',self::EXISTS_VALIDATE,'confirm'),
		//array('task_title','','task已经存在您是不是填错了啊！',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT),
                //array('task_title','','工号已经存在',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT),
		);

	public $_auto	= array(
                array('create_time','time',self::MODEL_INSERT,'function'),
                array('create_time','time',self::MODEL_UPDATE,'function'),
		//array('update_time','time',self::MODEL_UPDATE,'function'),
		);
        protected $_link=array(
            'CreateArticle'=>array(
                'mapping_type'=>BELONGS_TO,
                'class_name'=>'CreateArticle',
                'foreign_key'=>'project_id',//相对于本身表的外键
                //'as_fields'=>'id:proid,total:ptotal,status:pstatus',
            ),
            

        );
}
?>
