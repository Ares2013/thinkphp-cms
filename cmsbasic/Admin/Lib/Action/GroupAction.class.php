<?php
/**
 +------------------------------------------------------------------------------
 * 后台操作分组
 +------------------------------------------------------------------------------
 * @category   Action
 * @package  Lib
 * @subpackage  Action
 * @author   668FreeCloud <chinasoftstar@qq.com>
 * @version  $Id: GroupAction.class.php 2791 2013/7/28 16:45  668FreeCloud $
 +------------------------------------------------------------------------------
 */
class GroupAction extends CommonAction {

	
	public function _before_index() {
		$model	=	D("Group");
		$list	=	$model->getNav();
		$this->assign('navlist',$list);
	}
	public function _before_add() {
		$model	=	D("Group");
		$list	=	$model->getNav();
		$this->assign('navlist',$list);
	}
	public function _before_edit() {
		$model	=	D("Group");
		$list	=	$model->getNav();
		$this->assign('navlist',$list);
	}

    /**
     +----------------------------------------------------------
     * 默认排序操作
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    public function sort()
    {
		$node = M('Group');
        if(!empty($_GET['sortId'])) {
            $map = array();
            $map['status'] = 1;
            $map['id']   = array('in',$_GET['sortId']);
            $sortList   =   $node->where($map)->order('sort asc')->select();
        }else{
            $sortList   =   $node->where('status=1')->order('sort asc')->select();
        }
        $this->assign("sortList",$sortList);
        $this->display();
        return ;
    }
	
	public function foreverdelete() {
		$msg="";
		//删除指定记录
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if(empty($id))
			{
				$id=$_REQUEST [$pk.'s'];
			}
			if (isset ( $id )) {
				
				$ids=explode (',', $id );
				foreach($ids as $k=>$v)
				{
					$type=$model->where(array($pk=>$v))->getField('type');
					if($type==1)
					{
						$msg="包含系统必须部分，不能删除！";
						unset($ids[$k]);
					}
				}
				if(empty($ids))
				{
					$this->error ( '系统必须部分，不能删除！' );
				}
				else
				{
					$condition = array ($pk => array ('in', $ids ) );
					if (false !== $model->where ( $condition )->delete ()) {
						//echo $model->getlastsql();
						$this->success ('删除成功！'.$msg);
					} else {
						$this->error ('删除失败！'.$msg);
					}
				}
			} else {
				$this->error ( '非法操作'.$msg );
			}
		}
		$this->forward ();
	}

}
?>