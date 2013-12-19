<?php
/**
 +------------------------------------------------------------------------------
 * 前台菜单分类
 +------------------------------------------------------------------------------
 * @category   Action
 * @package  Lib
 * @subpackage  Action
 * @author   668FreeCloud <chinasoftstar@qq.com>
 * @version  $Id: NavAction.class.php 2791 2013/7/28 16:45  668FreeCloud $
 +------------------------------------------------------------------------------
 */
class NavAction extends CommonAction
{
	public function add()
	{
		$cate_list = D('NavCategory')->where('status = 1')->order('sort ASC,id ASC')->select();
		$cate_list = D('NavCategory')->toFormatTree($cate_list,'name','id');
		$this->assign("cate_list",$cate_list);
		parent::add();
	}
	
	public function edit()
	{
		$id = intval($_REQUEST['id']);
		$vo = D("Nav")->getById($id);
		$this->assign ( 'vo', $vo );
		$cate_list = D("NavCategory")->where('status = 1')->field('id,parent_id,name')->order('sort ASC,id ASC')->select();
		$cate_list = D("NavCategory")->toFormatTree($cate_list,array('name'),'id');
		
		$this->assign("cate_list",$cate_list);
		$this->display();
	}
	
	public function remove()
	{
		//删除指定记录
		$result = array('isErr'=>0,'content'=>'');
		$id = $_REQUEST['id'];
		if(!empty($id))
		{
			$name=$this->getActionName();
			$model = D($name);
			$pk = $model->getPk ();
			$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
			if($model->where(array("id"=>array('in',explode(',',$id)),'is_fix'=>1))->count()>0)
			{
				$result['isErr'] = 1;
				$result['content'] = L('FIX_DEL');
				die(json_encode($result));
			}
			
			if(false !== $model->where ( $condition )->delete ())
			{
				$this->saveLog(1,$id);
			}
			else
			{
				$this->saveLog(0,$id);
				$result['isErr'] = 1;
				$result['content'] = L('REMOVE_ERROR');
			}
		}
		else
		{
			$result['isErr'] = 1;
			$result['content'] = L('ACCESS_DENIED');
		}
		
		die(json_encode($result));
	}
}

function getCateName($cid)
{
	return D("NavCategory")->where('id = '.$cid)->getField('name');
}
?>