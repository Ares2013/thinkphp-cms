<?php

/**
 +------------------------------------------------------------------------------
 * 操作权限节点
 +------------------------------------------------------------------------------
 * @category   Action
 * @package  Lib
 * @subpackage  Action
 * @author   Ares Peng<1534157801@qq.com>
 * @version  $Id: ItemtypeAction.class.php 2013/11/14 21:45  Ares Peng
 +------------------------------------------------------------------------------
 */
class ItemtypeAction extends CommonAction {
	public function _filter(&$map){
            
            if(!empty($_GET['group_id'])) {
                $map['group_id'] =  $_GET['group_id'];
                
                $this->assign('nodeName','分组');
            }elseif(empty($_POST['search']) && !isset($map['pid']) ) {
                            $map['pid']	=	0;
                    }
                    if($_GET['pid']!=''){
                            $map['pid']=$_GET['pid'];
                    }
                    $_SESSION['currentNodeId']	=	$map['pid'];
                    if($_SESSION['administrator'] == true){
                        
                    }else{
                        if($map['pid'] != '0'){
                            
                            $GroupId = $_SESSION['gid'];
                            
                            //根据用户登录后的类型 显示所属内部组名
                            if($_SESSION['administrator'] == true){
                            $GroupTeamModel = M("user_group");
                            $UserGroupTeam = $GroupTeamModel->select();
                            $allgroup = $UserGroupTeam;
                            }else{
                            $Groupid = $_SESSION['gid'];
                            //根据$_SESSION['gid']来找出所属分组
                            $UserGroupTeam = $this->AllGroupArray($Groupid);
                            $allgroup = $UserGroupTeam;
                            }
                            $typemodel = M("Itemtype");
                            foreach($UserGroupTeam as $k=>$v){
                                $UserGroupTeamId[$k] =  $UserGroupTeam[$k]['id'];
                            }
                            //测试所有Type的分组
                            $Teamplist = M("user_group")->where(array('id'=>$GroupId))->find();
                            $AllGroupArray = M("user_group")->select();//所有的小组
                            $TeampLevel = $Teamplist['level'];
                            $TeamPid = $Teamplist['pid'];

                            if($TeampLevel < 3){
                                //$TeamLevel < 3
                                $map['group_id']=array('in',$UserGroupTeamId);
                            }elseif($TeampLevel > 3){
                                //$TeamLevel > 3
                                $ascAllGroupArray = $this->AscAllGroupArray($AllGroupArray,$TeamPid);
                                $projectType_GroupId = $ascAllGroupArray['id'];

                                $map['group_id']=array('eq',$projectType_GroupId);
                            }elseif($TeampLevel == 3){
                                //$TeamLevel = 3 
                                $map['group_id']=array('eq',$Teamplist['id']);
                            }
                            
                            
                            //$map['group_id'] = $_SESSION['gid'];
                            //level 是Itemtype的等级1 project 2 task type
                            //$where['pid'] =array('neq','0');
                            //$where['level'] =array('eq','1');
                        }
                    }
                    //获取上级节点
                    $node  = M("Itemtype");
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
		$model	=	M("Group");
		$list	=	$model->where('status=1')->getField('id,title');
		$this->assign('groupList',$list);
	}

	// 获取配置类型
	public function _before_add() {
		$model	=	M("Group");
		$list	=	$model->where('status=1')->select();
		$this->assign('list',$list);
		$node	=	M("Itemtype");
                
		$nolist=$node->getById($_SESSION['currentNodeId']);
                
                $this->assign('pid',$node->id);
		$this->assign('level',$node->level+1);
                
                if($_SESSION['administrator'] == true){
                    $GroupTeamModel = M("user_group");
                    $UserGroupTeam = $GroupTeamModel->select();
                    $allgroup = $UserGroupTeam;
                }else{
                    $Groupid = $_SESSION['gid'];
                    //根据$_SESSION['gid']来找出所属分组
                    $UserGroupTeam = $this->AllGroupArray($Groupid);
                    $allgroup = $UserGroupTeam;
                }
               $this->assign('allgroup', $allgroup);
                 //dump($allgroup);
	}

    public function _before_patch() {
		$model	=	M("Group");
		$list	=	$model->where('status=1')->select();
		$this->assign('list',$list);
		$node	=	M("Itemtype");
		$node->getById($_SESSION['currentNodeId']);
                $this->assign('pid',$node->id);
		$this->assign('level',$node->level+1);
    }
    public function _before_edit() {
		$model	=	M("Itemtype");
		//$list	=	$model->where('status=1')->select();
		$this->assign('list',$list);
                if($_SESSION['administrator'] == true){
                    $GroupTeamModel = M("user_group");
                    $UserGroupTeam = $GroupTeamModel->select();
                    $allgroup = $UserGroupTeam;
                }else{
                    $Groupid = $_SESSION['gid'];
                    //根据$_SESSION['gid']来找出所属分组
                    $UserGroupTeam = $this->AllGroupArray($Groupid);
                    $allgroup = $UserGroupTeam;
                }
               $this->assign('allgroup', $allgroup);
    }
}
?>
