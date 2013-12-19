<?php
/**
 +------------------------------------------------------------------------------
 * 后台日志
 +------------------------------------------------------------------------------
 * @category   Action
 * @package  Lib
 * @subpackage  Action
 * @author   668FreeCloud <chinasoftstar@qq.com>
 * @version  $Id: AdminLogAction.class.php 2791 2013/7/28 16:45  668FreeCloud $
 +------------------------------------------------------------------------------
 */
class AdminLogAction extends CommonAction
{	
	public function clear()
	{
		$where = '';
		$begin_time = trim($_REQUEST['begin_time']);
		$end_time = trim($_REQUEST['end_time']);
		
		if(!empty($begin_time))
		{
			$begin_time = strZTime($begin_time);
			$where .= " AND log_time >= '$begin_time'";
		}
		else
			$begin_time = 0;
		
		if(!empty($end_time) && strZTime($end_time) > $begin_time)
		{
			$end_time = strZTime($end_time);
			$where .= " AND log_time <= '$end_time'";
		}
		
		$model = M();
		
		if(!empty($where))
			$where = 'WHERE 1' . $where;
		
		$sql = 'DELETE FROM '.C("DB_PREFIX").'admin_log '.$where;
		
		M()->query($sql);
		$this->redirect('AdminLog/index');
	}
}

function getResult($result)
{
	return L('LOG_RESULT_'.$result);
}

function getAdminName($id)
{
	return D("Admin")->where('id = '.$id)->getField('admin_name');
}
?>