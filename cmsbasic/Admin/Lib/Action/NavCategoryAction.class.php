<?php
/**
 +------------------------------------------------------------------------------
 * 前台菜单分类
 +------------------------------------------------------------------------------
 * @category   Action
 * @package  Lib
 * @subpackage  Action
 * @author   668FreeCloud <chinasoftstar@qq.com>
 * @version  $Id: NavCategoryAction.class.php 2791 2013/7/28 16:45  668FreeCloud $
 +------------------------------------------------------------------------------
 */
class NavCategoryAction extends CommonAction
{
	public function index()
	{
		$list = D('NavCategory')->order('sort ASC,id ASC')->select();
		$list = D('NavCategory')->toFormatTree($list,'name','id','parent_id');
		$this->assign("list",$list);
		$this->display ();
	}
	
	public function add()
	{
		$cate_list = D('NavCategory')->where('parent_id = 0')->order('sort ASC,id ASC')->select();
		$cate_list = D('NavCategory')->toFormatTree($cate_list,'name','id');
		$this->assign("cate_list",$cate_list);
		parent::add();
	}
	
	public function edit()
	{
		$id = intval($_REQUEST['id']);
		$vo = D("NavCategory")->getById($id);
		$this->assign ( 'vo', $vo );
		$childs = D("NavCategory")->getChildIds($id);
		$childs[] = $id;
		$cate_list = D("NavCategory")->where('id not in ('.implode(',', $childs).')')->field('id,parent_id,name')->order('sort ASC,id ASC')->select();
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
			if($model->where(array ("parent_id" => array ('in', explode ( ',', $id ) ) ))->count()>0)
			{
				$result['isErr'] = 1;
				$result['content'] = L('CATE_EXIST_CHILD');
				die(json_encode($result));
			}
			
			if($model->where(array("id"=>array('in',explode(',',$id)),'is_fix'=>1))->count()>0)
			{
				$result['isErr'] = 1;
				$result['content'] = L('CATE_FIX_DEL');
				die(json_encode($result));
			}
			
			if(D("Nav")->where(array ("cid" => array ('in', explode ( ',', $id ) ) ))->count()>0)
			{
				$result['isErr'] = 1;
				$result['content'] = L('CATE_EXIST_NAV');
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
?>