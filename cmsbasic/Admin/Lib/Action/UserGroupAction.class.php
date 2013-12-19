<?php
/**
 +------------------------------------------------------------------------------
 * 后台用户组别的管理模块
 +------------------------------------------------------------------------------
 * @category   Action
 * @package  Lib
 * @subpackage  Action
 * @author   Ares Peng<Z13053003@wistron.local><1534157801@qq.com>
 * @version  $Id: UsergroupAction.class.php 2013/10/1  Ares Peng
 +------------------------------------------------------------------------------
 */
    class UserGroupAction extends CommonAction {
        public function _filter(&$map)
	{
        if(!empty($_GET['group_id'])) {
            $map['group_id'] =  $_GET['group_id'];
            $this->assign('nodeName','分组');
        }elseif(empty($_POST['search']) && !isset($map['pid']) ) {
			$map['pid']	=	0;
		}
		if($_GET['pid']!=''){
			$map['pid']=$_GET['pid'];
		}
		//dump($map['pid']);
		$_SESSION['currentNodeId']	=	$map['pid'];
		//获取上级节点
                $name=$this->getActionName();
		$node  = M($name);
		$pnode=$node->where(array('id'=>$map['pid']))->find();
		$pid=0;
		if(empty($pnode))
		{
			$pid=$pnode['id'];
		}
		$this->assign('pid',$pid);
        if(isset($map['pid'])) {
            if($node->getById($map['pid'])) {
                $this->assign('level',$node->level+1);
                $this->assign('nodeName',$node->name);
            }else {
                $this->assign('level',1);
            }
        }
	}
        public function _before_index() {
		$model	=	M("Team");
		$list	=	$model->where('status=1')->getField('id,title');
		$this->assign('groupList',$list);
	}
        
        // 获取配置类型
	public function _before_add() {
                $name=$this->getActionName();
                $model	=	M($name);
                if($_SESSION['administrator'] == true){
                    $where['status'] = array('neq','0');
                }else{
                    $where['status'] = array('neq','0');
                }
		$list	=	$model->where($where)->select();
                
                $nolist=$model->getById($_SESSION['currentNodeId']);
                $this->assign('pid',$model->id);
                $this->assign('level',$model->level+1);
		$this->assign('list',$list);
                
                //根据$_SESSION['gid']来找出所属分组
                $UserGroupTeam = $this->AllGroupArray($_SESSION['gid']);
                $this->assign("UserGroupTeam",$UserGroupTeam);
                //dump($UserGroupTeam);
	}
        public function _before_edit() {
		$name=$this->getActionName();
                $model	=	M($name);
		$list	=	$model->where(array('status'=>array('eq','1')))->select();
		$this->assign('list',$list);
                
                $nolist=$model->getById($_SESSION['currentNodeId']);
                $this->assign('pid',$model->id);
                $this->assign('level',$model->level+1);
        }
        
        
        
        public function addleader(){
            $name=$this->getActionName();
            $UserGroup = $_REQUEST['gid'];
            $Search = $_REQUEST['username'];
            $user=D('User');
            $UserGroupModel = M("user_group");
            if($_SESSION['administrator'] == true){
                $where['id'] = array('neq',$_SESSION['authId']);
            }else{
                $Groupid = $_SESSION['gid'];
                $UserGroupTeam = $this->AllGroupArray($Groupid);
                foreach($UserGroupTeam as $k=>$v){
                    $UserGroupTeamId[$k] = $UserGroupTeam[$k]['id'];
                }
                $where['user_group_id'] = array('in',$UserGroupTeamId);
                $where['status'] = array('neq','0');
            }
            if(!empty($Search)){
                $where['account'] = array('like',"%".$Search."%");
            }
            //符合条件的查询
            if(!empty($user)){
                $list=$user->where($where)->order(array('account'=>'asc'))->select();
            }
            $ItselfGroup=$UserGroupModel->where(array('id'=>$UserGroup))->find();
            $leaderMessage = getUserMessage($ItselfGroup['member']);
            
            $this->assign('gid',$UserGroup);
            $this->assign('list',$list);
            //dump($list);
            //选中的小组信息
            $this->assign('ItselfGroup_name',$ItselfGroup['nickname']);
            $this->assign('ItselfGroup_leader_en',$leaderMessage['account']);
            $this->assign('ItselfGroup_leader_cn',$leaderMessage['nickname']);
            $this->display();
        }
        public function upmenber(){
            $msg="";
            $uid=$_REQUEST['uid'];
            $gid=$_REQUEST['gid'];
            dump($gid);
            dump($uid);
            exit;
            $umodel=$_REQUEST['navTabId'];
            $name=  $this->getActionName();
            $ulist=explode(',', $uid);
            $usergroup=D($name);
            if(isset($user)){
                $GroupSaveData['id']=$gid;
                $GroupSaveData['member']=$uid;
                $lis=$usergroup->save($GroupSaveData);
            }
            if(false !==$lis){
                 $this->saveLog(1, $lt.$lis);
                 $this->success('指定Leader成功！' . $msg);
            }else{
                 $this->saveLog(0, $lt.$lis);
                 $this->success('指定Leader失败！' . $msg);
            }
        }
    }
?>
