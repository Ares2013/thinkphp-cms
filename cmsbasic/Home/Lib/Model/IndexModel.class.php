<?php
class IndexModel extends CommonModel {
	//protected $tableName = 'article';
        //在模型里单独设置数据库连接信息
        protected $connection = array(
            'db_type'  => 'mysql',
            'db_user'  => 'root',
            'db_pwd'   => '',
            'db_host'  => 'localhost',
            'db_port'  => '3306',
            //'db_prefix'  => 'ams_',//ams_user_team
            'db_name'  => 'ms'
        );
}
?>
