<?php
/**
 +------------------------------------------------------------------------------
 * 后台用户模块
 +------------------------------------------------------------------------------
 * @category   Action
 * @package  Lib
 * @subpackage  Action
 * @author   Ares Peng<Z13053003@wistron.local><1534157801@qq.com>
 * @version  $Id: UserAction.class.php 2013/10/1  Ares Peng
 +------------------------------------------------------------------------------
 */
class UserAction extends CommonAction {
    public function ajaxRegion(){
      
    }
    //调用index之前
    public function _before_index() {
		
    }
    //调用add之前
    public function _before_add() {
        /*//所属组别
        $usergroumodel = M("UserGroup");
        $toplevel = $usergroumodel->where(array('pid'=>'0'))->getField('id',true);
        $secondlevel = $usergroumodel->where(array('pid'=>array('in',$toplevel)))->select();//s z 的四个组
        foreach($secondlevel as $k=>$v){
            $secondlevel_id[$k]=$v['id'];
            $secondlevel_content[$k]['id']=$v['id'];
            $secondlevel_content[$k]['nickname']=$v['nickname'];
        }
        $thirdlevrl = $usergroumodel->where(array('pid'=>array('in',$secondlevel_id)))->select();//下级分组
        $allgroup = $usergroumodel->where(array('status'=>array('neq','0')))->select();
        foreach($allgroup as $k=>$v){
            if(in_array($allgroup[$k]['id'], $toplevel) || in_array($allgroup[$k]['id'], $secondlevel_id)){
                unset($allgroup[$k]);
            }
        }*/
        
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
    //调用eidt之前
    public function _before_edit() {
        /*//所属组别
        $usergroumodel = M("UserGroup");
        $toplevel = $usergroumodel->where(array('pid'=>'0'))->getField('id',true);
        $secondlevel = $usergroumodel->where(array('pid'=>array('in',$toplevel)))->select();//s z 的四个组
        foreach($secondlevel as $k=>$v){
            $secondlevel_id[$k]=$v['id'];
            $secondlevel_content[$k]['id']=$v['id'];
            $secondlevel_content[$k]['nickname']=$v['nickname'];
        }
        $thirdlevrl = $usergroumodel->where(array('pid'=>array('in',$secondlevel_id)))->select();//下级分组
        $allgroup = $usergroumodel->where(array('status'=>array('neq','0')))->select();
        foreach($allgroup as $k=>$v){
            if(in_array($allgroup[$k]['id'], $toplevel) || in_array($allgroup[$k]['id'], $secondlevel_id)){
                unset($allgroup[$k]);
            }
        }*/
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
    function _filter(&$map){
		  //$map['id'] = array('egt',2);
                  if($_SESSION['administrator'] == true){
                      $map['id'] = array('neq',$_SESSION['authId']);
                  }else{
                    $Groupid = $_SESSION['gid'];
                    //根据$_SESSION['gid']来找出所属分组
                    $UserGroupTeam = $this->AllGroupArray($Groupid);
                    //$this->assign("UserGroupTeam",$UserGroupTeam);
                    foreach($UserGroupTeam as $k=>$v){
                        $GroupTeamid[$k] =  $UserGroupTeam[$k]['id'];
                    }
                    $map['user_group_id'] = array('in',$GroupTeamid);
                  }
                  
		//$map['account'] = array('like',"%".$_POST['account']."%");
	}
	// 检查帐号
	public function checkAccount() {
        if(!preg_match('/^[a-z]\w{4,}$/i',$_POST['account'])) {
            $this->error( '用户名必须是字母，且5位以上！');
        }
	$User = M("User");
        // 检测用户名是否冲突
        $name  =  $_REQUEST['job_number'];
        $result  =  $User->getByAccount($name);
        if($result) {
        	$this->error('该用户名已经存在！');
        }else {
           	$this->success('该用户名可以使用！');
        }
    }
    
	// 插入数据
	public function insert() {
		// 创建数据对象
		$User	 =	 D("User");
		if(!$User->create()) {
			$this->error($User->getError());
		}else{
			// 写入帐号数据
			if($result	 =	 $User->add()) {
				$this->addRole($result,$_REQUEST['user_group_id']);
                                //$this->addGroup($result,$_REQUEST['user_group_id']);
				$this->success('用户添加成功！');
			}else{
				$this->error('用户添加失败！');
			}
		}
	}

	protected function addRole($userId,$groupId) {
		//新增用户自动加入相应权限组
		$RoleUser = M("RoleUser");
		$RoleUser->user_id=$userId;
                // 默认加入网站编辑组
                $RoleUser->role_id=3;
		$RoleUser->add();
	}
        protected function addGroup($userId,$groupId){
            $group = M("UserGroup");
            $group->user_group_id=$groupId;
            $group->user_id=$userId;
            $group->add();
        }

    //重置密码
    public function resetPwd()
    {
    	$id  =  $_POST['id'];
        $password = $_POST['password'];
        $otherSystemDataSaveData = M("User")->field('job_number')->where(array('id'=>$id))->find();
        
        if(''== trim($password)) {
        	$this->error('密码不能为空！');
        }
        $User = M('User');
		$User->password	=	md5($password);
		$User->id			=	$id;
		$result	=	$User->save();
        if(false !== $result) {
            $otherSystemDataSaveModel=M('user_employee','ams_','mysql://root:@localhost/ms');
            $otherSystemDataSaveData['Password'] = $_POST['password'];
            $otherSystemDataSavejobnumber = $otherSystemDataSaveModel->where(array('Em_ID'=>array('eq',$otherSystemDataSaveData['job_number'])))->data($otherSystemDataSaveData)->save();
            $this->success("密码修改为$password");
        }else {
        	$this->error('重置密码失败！');
        }
    }
    
 
}
?>