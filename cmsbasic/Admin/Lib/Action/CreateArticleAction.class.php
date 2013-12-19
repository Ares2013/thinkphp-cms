<?php
/**
 +------------------------------------------------------------------------------
 * 发布project,testcas等
 +------------------------------------------------------------------------------
 * @category   Action
 * @package  Lib
 * @subpackage  Action
 * @author   Ares Peng <Z13053003@wistron.local><1534157801@qq.com>
 * @version  $Id: CreateArticleAction.class.php 2013/10/1  Ares Peng
 +------------------------------------------------------------------------------
 */

class CreateArticleAction extends CommonAction {
   public function ajaxRegion(){
       $gid = $_REQUEST["gid"];
       $usergroup = M("User_group");
       $result = $usergroup->where("id = $gid")->select();
    }
    public function _filter(&$map){
            
            if(!empty($_GET['group_id'])) {
                $map['group_id'] =  $_GET['group_id'];
                
                $this->assign('nodeName','分组');
            }elseif(empty($_POST['search']) && !isset($map['pid']) ) {
                            //$map['pid']	=	0;
                    }
                    if($_GET['pid']!=''){
                            $map['pid']=$_GET['pid'];
                    }
                    $_SESSION['currentNodeId']	=	$map['pid'];
                    if($_SESSION['administrator'] == true){
                        
                    }else{
                            $map['status'] = array('neq','0');
                            if(!empty($_REQUEST['teamid'])){
                                $Groupid = $_REQUEST['teamid'];
                                //根据$_SESSION['gid']来找出所属分组
                                $UserGroupTeam = $this->AllGroupArray($Groupid);
                                foreach($UserGroupTeam as $k=>$v){
                                    $AllGroupTeam[$k]=$UserGroupTeam[$k]['id'];
                                }
                                $map['teamid']=array('in',$AllGroupTeam);//当查询条件teamid不为空时
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
    //调用index之前
    public function _before_index() {
	$gid=$_SESSION['gid'];
        $uid = $_SESSION['authId'];
        
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
        
        //特殊要求，查询条件时去掉本身
        foreach($allgroup as $k=>$v){
            if($allgroup[$k]['id'] == $gid){
                unset($allgroup[$k]);
            }
        }
        $this->assign('allgroup', $allgroup);//所属内部分组
    }
    //调用add之前
    public function _before_add() {
        $gid=$_SESSION['gid'];
        $uid = $_SESSION['authId'];
        
        $user = M("User");
        $this->assign('jobnumber',$_SESSION['job_number']);
        $this->assign('gid',$gid);//登陆用户的组id
        $this->assign('uid',$uid);//登陆用户的用户id
        
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
        
        //TestLeader 部分
        foreach($UserGroupTeam as $k=>$v){
            $UserGroupTeamId[$k] =  $UserGroupTeam[$k]['id'];
        }
        if($_SESSION['administrator'] == true){
            $UserMessageWhere['id'] = array('neq',$_SESSION['authId']);
        }else{
            $UserMessageWhere['user_group_id'] = array('in',$UserGroupTeamId);
            $UserMessageWhere['status'] = array('neq',"0");
        }
        $userlist = $user->where($UserMessageWhere)->order('account asc')->select();//所有下属的成员
        
       //type分类部分 project leverl=1 task leverl=2
       $typemodel = M("Itemtype");
       if($_SESSION['administrator'] == true){
              $where=array(
                  'pid'=>array('neq','0'),
                  //'group_id'=>array('eq',$gid),
                  'level'=>'1'
              );          
        }else{
            
            //测试所有Type的分组
            $Teamplist = M("user_group")->where(array('id'=>$gid))->find();
            $AllGroupArray = M("user_group")->select();//所有的小组
            $TeampLevel = $Teamplist['level'];
            $TeamPid = $Teamplist['pid'];

            if($TeampLevel < 3){
                //$TeamLevel < 3
                $where['group_id']=array('in',$UserGroupTeamId);
            }elseif($TeampLevel > 3){
                //$TeamLevel > 3
                $ascAllGroupArray = $this->AscAllGroupArray($AllGroupArray,$TeamPid);
                $projectType_GroupId = $ascAllGroupArray['id'];
                
                $where['group_id']=array('eq',$projectType_GroupId);
            }elseif($TeampLevel == 3){
                //$TeamLevel = 3 
                $where['group_id']=array('eq',$Teamplist['id']);
            }

            //level 是Itemtype的等级1 project 2 task type
            $where['pid'] =array('neq','0');
            $where['level'] =array('eq','1');
            //dump($where);
       
        }
        $type_list = $typemodel->where($where)->select();
        //echo $typemodel->getLastSql();
        $this->assign('typelist',$type_list);//所属分类
        $this->assign('allgroup', $allgroup);//所属内部分组
        $this->assign('userlist',$userlist);//TestLeader
        
	}
    
    //调用eidt之前
    public function _before_edit() {
        $gid=$_SESSION['gid'];
        $uid = $_SESSION['authId'];
        
        $user = M("User");
        $this->assign('gid',$gid);//登陆用户的组id
        $this->assign('uid',$uid);//登陆用户的用户id
        $this->assign('jobnumber',$_SESSION['job_number']);
       
        
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
        
        //TestLeader 部分
        foreach($UserGroupTeam as $k=>$v){
            $UserGroupTeamId[$k] =  $UserGroupTeam[$k]['id'];
        }
        if($_SESSION['administrator'] == true){
            $UserMessageWhere['id'] = array('neq',$_SESSION['authId']);
        }else{
            $UserMessageWhere['user_group_id'] = array('in',$UserGroupTeamId);
            $UserMessageWhere['status'] = array('neq',"0");
        }
        $userlist = $user->where($UserMessageWhere)->order('account asc')->select();//所有下属的成员
        
        //type分类部分 project leverl=1 task leverl=2
       $typemodel = M("Itemtype");
       if($_SESSION['administrator'] == true){
              $where=array(
                  'pid'=>array('neq','0'),
                  //'group_id'=>array('eq',$gid),
                  'level'=>'1'
              );          
        }else{
            
            //测试所有Type的分组
            $Teamplist = M("user_group")->where(array('id'=>$gid))->find();
            $AllGroupArray = M("user_group")->select();//所有的小组
            $TeampLevel = $Teamplist['level'];
            $TeamPid = $Teamplist['pid'];
            if($TeampLevel < 3){
                //$TeamLevel < 3
                $where['group_id']=array('in',$UserGroupTeamId);
            }elseif($TeampLevel > 3){
                //$TeamLevel > 3
                $ascAllGroupArray = $this->AscAllGroupArray($AllGroupArray,$TeamPid);
                $projectType_GroupId = $ascAllGroupArray['id'];
                
                $where['group_id']=array('eq',$projectType_GroupId);
            }elseif($TeampLevel == 3){
                //$TeamLevel = 3 
                $where['group_id']=array('eq',$Teamplist['id']);
            }
            //level 是Itemtype的等级1 project 2 task type
            $where['pid'] =array('neq','0');
            $where['level'] =array('eq','1');
        }
        $type_list = $typemodel->where($where)->select();
        $this->assign('typelist',$type_list);//所属分类
        $this->assign('allgroup', $allgroup);//所属内部分组
        $this->assign('userlist',$userlist);//TestLeader
        
       
	}
    
    public function insert(){
        $name = $this->getActionName();
        if(strtolower($_REQUEST['navTabId']) != strtolower($name)){
            $model = D($_REQUEST['navTabId']);
            $proid=$_POST['project_id'];
            $_POST['task_menber']=  $_POST['orgLookup_id'];
            $_POST['schedule_start']=  strtotime($_POST['schedule_start']);
            $_POST['schedule_end']=  strtotime($_POST['schedule_end']);
            
            //将传递过来的值组合    把所有以前task的任务量也要加起来
            $stringMember = $_POST['orgLookup_id'];//用户添加时的Member Id
            $stringMemberList = explode(",", $stringMember);
            
            $st=$_POST['schedule_start'];
            $et=$_POST['schedule_end'];
            $totalTime = ($et-$st)/86400;//天数
            
            
            //处理具体日期的任务量问题
            $taskmodel = M("TasksMember");
            
            $workday=0;
            $n=0;
            //开始时间 和 结束时间的unix时间戳  workday 的数目 总天数
            for($i=0; $i<=$totalTime;$i++){
                $tem = ($st+(86400*$i));
                
                $Memberresult = $this->AddMemberData($tem, $stringMemberList);
                $days_task[$i]['userIdresult']=$Memberresult['userIdresult'];
                $days_task[$i]['userNumberresult']=$Memberresult['userNumberresult'];
                $days_task[$i]['days']=date('Y/m/d',($st+(86400*$i)));
                //$days_task[$i]['progres']=$task_lists[$i];
                //$days_task[$i]['dllists']=$dllists[$i];
                $days_task[$i]['unixtime']=$tem;
                $n = $n +1;
            }
            $NumberIndex = 0;
            //求出可分配的工作量  将每个Member 和对应unix时间戳对应上来求
           for($i=0;$i<count($days_task);$i++){
               $TempDataIdList = "data_".$i."id:".$days_task[$i]['userIdresult']."data_".$i."id:";
               $TempDataNumberList = "data_".$i."num:".$days_task[$i]['userNumberresult']."data_".$i."num:";
               $AdduserlistsData = $AdduserlistsData.$TempDataIdList.$TempDataNumberList;
               
               //根据任务量来创建tasks_member 的数据
               if(!empty($days_task[$i]['userIdresult'])){
                   //所有工作日的
                   $UserIdResultList = explode(',', $days_task[$i]['userIdresult']);
                   $UserNumberResultList = explode(',', $days_task[$i]['userNumberresult']);
                   foreach($UserIdResultList as $key=>$value){
                       $createTasksMemberData[$NumberIndex]['uid']=$UserIdResultList[$key];
                       $createTasksMemberData[$NumberIndex]['task_num']=$UserNumberResultList[$key];
                       $createTasksMemberData[$NumberIndex]['detail_time']=$days_task[$i]['unixtime'];
                       $createTasksMemberData[$NumberIndex]['schedule_start']=$st;
                       $createTasksMemberData[$NumberIndex]['schedule_end']=$et;
                       $createTasksMemberData[$NumberIndex]['taskstatus']=$_POST['status'];
                       
                       //索引自增
                       $NumberIndex = $NumberIndex +1;
                   }
               }
           }
           $_POST['userlists_data']=  $AdduserlistsData; 
            
        }else{
            $model = D($name);
        }
        
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        //保存当前数据对象
        $list = $model->add();
        if ($list !== false) { //保存成功
             if(strtolower($_REQUEST['navTabId']) != strtolower($name)){             
                    $m=M($name);
                    $m->where("id = $proid")->setInc('total',1);
                    //$s=$m->find($_POST['project_id']);
                    //$c['id']=$s['id'];
                    //$c['total']=array('exp','total+1');
                    //$m->save($c);
                    if(!empty($createTasksMemberData)){
                        //新增id 号
                        for($j=0;$j<count($createTasksMemberData);$j++){
                            $createTasksMemberData[$j]['taskid'] = $list;
                        }
                        $TaskMemberNote = $taskmodel->addAll($createTasksMemberData);//主要是Mysql才可以用addAll()方法
                    }
                    if($TaskMemberNote !== false){
                        $this->saveLog(1, $list);
                        $this->assign ( 'jumpUrl', cookie( '_currentUrl_' ) );
                        $this->success('恭喜您，新增成功!',cookie( '_currentUrl_' ),true);
                    }else{
                        $this->saveLog(0, $TaskMemberNote);
                        $this->assign ( 'jumpUrl', cookie( '_currentUrl_' ) );
                        $this->success('新增失败，Member的任务量保存失败请于管理员联系!',cookie( '_currentUrl_' ),true);
                    }
             }else{
                 $this->saveLog(1, $list);
                 $this->assign ( 'jumpUrl', cookie( '_currentUrl_' ) );
                 $this->success('新增成功!',cookie( '_currentUrl_' ),true);
             }
        } else {
            //失败提示
            $this->saveLog(0, $list);
            $this->error('新增失败!');
        }
    }
    /*
     * 特殊的方法
     * 仅仅childadd功能使用
     * $unix  为具体某一天的时间戳
     * $stringMemberList 可以为一维数组，可是具体某个人Id
     * $where 是需要排除TaskId
     */
    public function AddMemberData($unix,$stringMemberList,$where=""){
            $statusModel=M("CreateTask");
            $taskmodel = M("TasksMember");
            $ByDayUnix = $unix;
            
            $whereByDay = array(
                'detail_time'=>array('eq',$ByDayUnix),//具体天的日期
                //'uid'=>array('in',$stringMemberList),//添加的Member
                //'taskid'=>array('neq',$tid),//TaskId不等于当前
            );
            if(!empty($where)){
                $whereByDay['taskid'] = array('neq',$where);
            }
            if(is_array($stringMemberList)){
                $whereByDay['uid'] = array('in',$stringMemberList);
                $list=$taskmodel->where($whereByDay)->select();//查找出具体一天用户的工作量
                //去掉重复的 taskid
                foreach($list as $k=>$v){
                    $listTaskId[$k] = $list[$k]['taskid'];
                }
                $TaskId = array_unique($listTaskId);//没有重复的值
                //找出create_task表中，满足status = 1 finis=0条件的列
                $statusWhere =array(
                    'id'=>array('in',$TaskId),
                    'status'=>array('eq','1'),
                    //'finish'=>array('eq','0'),
                );
                $statusList = $statusModel->where($statusWhere)->getField("id",true);//从开始时间到结束时间范围内的  所有正在进行，没有完成的Task  一维数组

                foreach($list as $key=>$value){
                    if(in_array($list[$key]['taskid'],$statusList)){

                    }else{
                        unset($list[$key]);
                    }
                }
                //将Member的总量求出来
                foreach($stringMemberList as $k=>$v){
                    $userNumberTemp = $v;
                    $userNumber = 0;//每到下个Member时重新赋值0
                    foreach($list as $key=>$value){
                        if($userNumberTemp == $list[$key]['uid']){
                            $userNumber = $userNumber + $list[$key]['task_num'];
                        }
                    }
                    if((1-$userNumber)<0){
                        $userNumber = 0;
                    }else{
                        $userNumber = (1-$userNumber);
                    }
                    $userNumberList[$k]['uid'] = $userNumberTemp;
                    $userNumberList[$k]['task_num'] = $userNumber;
                    //$userNumberList[$k]['detail_time'] = $ByDayUnix;
                    //$userNumberList[$k]['schedule_start'] = $st;
                    //$userNumberList[$k]['schedule_end'] = $et;
                }
                for($i=0;$i<count($userNumberList);$i++){
                    if($i == (count($userNumberList)-1)){
                        $userIdresult = $userIdresult.$userNumberList[$i]['uid'];
                        $userNumberresult = $userNumberresult.$userNumberList[$i]['task_num'];
                    }else{
                        $userIdresult = $userIdresult.$userNumberList[$i]['uid'].",";
                        $userNumberresult = $userNumberresult.$userNumberList[$i]['task_num'].",";
                    }
                }
            }elseif(is_string($stringMemberList)){
                $stringMemberList = explode(',', $stringMemberList);
                $whereByDay['uid'] = array('in',$stringMemberList);
                $list=$taskmodel->where($whereByDay)->select();//查找出具体一天用户的工作量
                //去掉重复的 taskid
                foreach($list as $k=>$v){
                    $listTaskId[$k] = $list[$k]['taskid'];
                }
                $TaskId = array_unique($listTaskId);//没有重复的值
                //找出create_task表中，满足status = 1 finis=0条件的列
                $statusWhere =array(
                    'id'=>array('in',$TaskId),
                    'status'=>array('eq','1'),
                    //'finish'=>array('eq','0'),
                );
                $statusList = $statusModel->where($statusWhere)->getField("id",true);//从开始时间到结束时间范围内的  所有正在进行，没有完成的Task  一维数组
                foreach($list as $key=>$value){
                    if(in_array($list[$key]['taskid'],$statusList)){

                    }else{
                        unset($list[$key]);
                    }
                }
                //将Member的总量求出来
                foreach($stringMemberList as $k=>$v){
                    $userNumberTemp = $v;
                    $userNumber = 0;//每到下个Member时重新赋值0
                    foreach($list as $key=>$value){
                        if($userNumberTemp == $list[$key]['uid']){
                            $userNumber = $userNumber + $list[$key]['task_num'];
                        }
                    }
                    if((1-$userNumber)<0){
                        $userNumber = 0;
                    }else{
                        $userNumber = (1-$userNumber);
                    }
                    $userNumberList[$k]['uid'] = $userNumberTemp;
                    $userNumberList[$k]['task_num'] = $userNumber;
                    //$userNumberList[$k]['detail_time'] = $ByDayUnix;
                    //$userNumberList[$k]['schedule_start'] = $st;
                    //$userNumberList[$k]['schedule_end'] = $et;
                }
                for($i=0;$i<count($userNumberList);$i++){
                    if($i == (count($userNumberList)-1)){
                        $userIdresult = $userIdresult.$userNumberList[$i]['uid'];
                        $userNumberresult = $userNumberresult.$userNumberList[$i]['task_num'];
                    }else{
                        $userIdresult = $userIdresult.$userNumberList[$i]['uid'].",";
                        $userNumberresult = $userNumberresult.$userNumberList[$i]['task_num'].",";
                    }
                }
            }elseif(is_int($stringMemberList)){
                $whereByDay['uid'] = array('eq',$stringMemberList);
                $list=$taskmodel->where($whereByDay)->select();//查找出具体一天用户的工作量
                echo $taskmodel->getlastSql();
                if(!empty($list)){
                    //去掉重复的 taskid
                    foreach($list as $k=>$v){
                        $listTaskId[$k] = $list[$k]['taskid'];
                    }
                    $TaskId = array_unique($listTaskId);//没有重复的值
                    //找出create_task表中，满足status = 1 finis=0条件的列
                    $statusWhere =array(
                        'id'=>array('in',$TaskId),
                        'status'=>array('eq','1'),
                        //'finish'=>array('eq','0'),
                    );
                    $statusList = $statusModel->where($statusWhere)->getField("id",true);//从开始时间到结束时间范围内的  所有正在进行，没有完成的Task  一维数组

                    foreach($list as $key=>$value){
                        if(in_array($list[$key]['taskid'],$statusList)){

                        }else{
                            unset($list[$key]);
                        }
                    }
                    $userNumber = 0;//每到下个Member时重新赋值0
                    foreach($list as $key=>$value){
                        if($stringMemberList == $list[$key]['uid']){
                            $userNumber = $userNumber + $list[$key]['task_num'];
                        }
                    }
                    if((1-$userNumber)<0){
                        $userNumber = 0;
                    }else{
                        $userNumber = (1-$userNumber);
                    }
                    $userIdresult = $stringMemberList;
                    $userNumberresult = $userNumber;
                    
                }else{
                    $userIdresult = $stringMemberList;
                    $userNumberresult = 1;
                }
            }
            return array(
                'userIdresult'=>$userIdresult,
                'userNumberresult'=>$userNumberresult,
            );
    }
    public function child(){
        $id=$_REQUEST['pid'];        
        $project =array();
        $Model=M("CreateArticle");
        $task=D("CreateTask");
        $user=M('User');
        $map['id']=array('eq',$id);
        $where['project_id']=array('eq',$id);
        $result=$Model->where($map)->getField('teamid,title,c_name_id,status');
        $gmodel=D('createtask');
        $glist = $gmodel->relation(true)->where($where)->select();
        foreach ($result as $k=>$v){
            $project['teamid']=$result[$k]['teamid'];
            $project['projectname']=$result[$k]['title'];
            $project['createid']=$result[$k]['c_name_id'];
            $project['status']=$result[$k]['status'];
        }
        
        if(!empty($_REQUEST['task_title']) && !empty($_REQUEST['teamid'])){
            $map_where['task_title'] = array('like',"%".$_REQUEST['task_title']."%");
            $Groupid = $_REQUEST['teamid'];
            //根据$_SESSION['gid']来找出所属分组
            $UserGroupTeam = $this->AllGroupArray($Groupid);
            foreach($UserGroupTeam as $k=>$v){
                $AllGroupTeam[$k]=$UserGroupTeam[$k]['id'];
            }
            $listactive = $Model->where(array('teamid'=>array('in',$AllGroupTeam),'status'=>'1','finish'=>'0'))->getField('id',true);//teamid符合数组
            
            $map_where['project_id']=array('in', $listactive);
        }else{
            if(!empty($_REQUEST['task_title'])){
                $map_where['task_title'] = array('like',"%".$_REQUEST['task_title']."%");
                $map_where['project_id'] = $id;
            }
            if(!empty($_REQUEST['teamid'])){
                $Groupid = $_REQUEST['teamid'];
                //根据$_SESSION['gid']来找出所属分组
                $UserGroupTeam = $this->AllGroupArray($Groupid);
                foreach($UserGroupTeam as $k=>$v){
                    $AllGroupTeam[$k]=$UserGroupTeam[$k]['id'];
                }
                $listactive = $Model->where(array('teamid'=>array('in',$AllGroupTeam),'status'=>'1','finish'=>'0'))->getField('id',true);//teamid符合数组
                
                $map_where =array(
                    'project_id' => array('in', $listactive),
                );
            }
        }
        if(empty($_REQUEST['task_title']) && empty($_REQUEST['teamid'])){
            $map_where['project_id']=$id;
        }
       
        //取得满足条件的记录数
        $count = $task->where($map_where)->count($task->getPk());
        
        //echo $model->getLastSql();
        if ($count > 0) {
            import("ORG.Util.Page");
            //创建分页对象
            if (!empty($_REQUEST ['listRows'])) {
                $listRows = $_REQUEST ['listRows'];
            } else {
                $listRows = '';
            }
            $p = new Page($count, $listRows);
	    if(empty($_REQUEST['pageNum'])){
                $firstRow = $p->firstRow;
                $listRows = $p->listRows;
            }else{
                $firstRow = ($p->listRows)*($_REQUEST['pageNum'] - 1);
                $listRows = ($p->listRows)*($_REQUEST['pageNum']);
            }
            
            //分页查询数据
            $voList = $task->where($map_where)->order('id desc')->limit($firstRow . ',' . $listRows)->select();
            //echo $task->getLastSql();
            //将查询到的结果Member赋上英文名
            foreach ($voList as $key => $value) {
                $member="";
                $user_list=  explode(',', $voList[$key]['task_menber']);
                if(!empty($voList[$key]['task_menber'])){
                    
                    for($i=0;$i<count($user_list);$i++){
                        if($i == (count($user_list)-1)){
                            $tem=$user->field('account')->where(array('id'=>$user_list[$i]))->find();//"id = $v"
                            $member = $member.$tem['account'];
                        }else{
                            $tem=$user->field('account')->where(array('id'=>$user_list[$i]))->find();
                            $member = $member.$tem['account'].',';
                        }
                    }
                }
                $voList[$key]['task_menbers']=$member;                
            }
        }
        $this->assign('totalCount', $count);
        $this->assign('numPerPage', $p->listRows);
        $this->assign('currentPage', !empty($_REQUEST[C('VAR_PAGE')]) ? $_REQUEST[C('VAR_PAGE')] : 1);
        //dump("页面跳转数：".$_REQUEST['pageNum']."原数据开始：".$p->firstRow."结束数据:".$p->listRows);
        
        $this->assign('list',$voList);
        $this->assign('projectid',$id);
        $this->assign('teamid',$project['teamid']);
        $this->assign('createid',$project['createid']);
        $this->assign('pname',$project['projectname']);
        
        
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
        $this->assign('allgroup', $allgroup);
        $this->display();
    }
    public function childadd(){
        $pid=$_REQUEST['pid'];
        $pname=$_REQUEST['pname'];
        $gid=$_SESSION['gid'];
        
        $user = M("User");
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
        
        //TestLeader 部分
        foreach($UserGroupTeam as $k=>$v){
            $UserGroupTeamId[$k] =  $UserGroupTeam[$k]['id'];
        }
        if($_SESSION['administrator'] == true){
            $UserMessageWhere['id'] = array('neq',$_SESSION['authId']);
        }else{
            $UserMessageWhere['user_group_id'] = array('in',$UserGroupTeamId);
            $UserMessageWhere['status'] = array('neq',"0");
        }
        $userlist = $user->where($UserMessageWhere)->order('account asc')->select();//所有下属的成员
        
       //type分类部分 project leverl=1 task leverl=2
       $typemodel = M("Itemtype");
       if($_SESSION['administrator'] == true){
              $where=array(
                  'pid'=>array('neq','0'),
                  //'group_id'=>array('eq',$gid),
                  'level'=>'2'
              );          
        }else{
            
            //测试所有Type的分组
            $Teamplist = M("user_group")->where(array('id'=>$gid))->find();
            $AllGroupArray = M("user_group")->select();//所有的小组
            $TeampLevel = $Teamplist['level'];
            $TeamPid = $Teamplist['pid'];

            if($TeampLevel < 3){
                //$TeamLevel < 3
                $where['group_id']=array('in',$UserGroupTeamId);
            }elseif($TeampLevel > 3){
                //$TeamLevel > 3
                $ascAllGroupArray = $this->AscAllGroupArray($AllGroupArray,$TeamPid);
                $projectType_GroupId = $ascAllGroupArray['id'];
                
                $where['group_id']=array('eq',$projectType_GroupId);
            }elseif($TeampLevel == 3){
                //$TeamLevel = 3 
                $where['group_id']=array('eq',$Teamplist['id']);
            }

            //level 是Itemtype的等级1 project 2 task type
            $where['pid'] =array('neq','0');
            $where['level'] =array('eq','2');
            //dump($where);
       
        }
        $type_list = $typemodel->where($where)->select();
        $this->assign('typelist',$type_list);//所属分类
        $this->assign('userlist',$userlist);//TestLeader
        
        $this->assign('pname',$pname);
        $this->assign('pid',$pid);
        $this->assign('gid',$gid);
       
        $this->display();
    }
    public function childdelete(){
        $msg = "";
        //删除指定记录
        $name = $this->getActionName();
        $model = D('CreateTask');
        $pid=$_REQUEST['pid'];
        if (!empty($model)) {
            $pk = $model->getPk();
            $id = $_REQUEST ['id'];
            if (empty($id)) {
                $id = $_REQUEST ['ids'];
            }
            if (isset($id)) {
                $ids = explode(',', $id);
                foreach ($ids as $k => $v) {
                    $type = $model->where(array($pk => $v))->getField('type');
                    if ($type == 1) {
                        $this->saveLog(0, $list);
                        $msg = "包含系统必须部分，不能删除！";
                        unset($ids[$k]);
                    }
                }
                if (empty($ids)) {
                    $this->saveLog(0, $list);
                    $this->error('系统必须部分，不能删除！');
                } else {
                    $condition = array($pk => array('in', $ids));
                    if (false !== $model->where($condition)->delete()) {
                        $pmodel=M($name);
                        $count=$model->where("project_id = $pid")->count();
                        $data['id']=$pid;
                        $data['total']=$count;
                        $plist=$pmodel->save($data);
                        $this->saveLog(1, $list);
                        $this->success('删除成功！' . $msg);
                    } else {
                        $this->saveLog(0, $list);
                        $this->error('删除失败！' . $msg);
                    }
                }
            } else {
                $this->saveLog(0, $list);
                $this->error('非法操作' . $msg);
            }
        }
        $this->forward();
    }
    public function childedit(){
        $pid=$_REQUEST['pid'];
        $pname=$_REQUEST['pname'];
        $gid=$_SESSION['gid'];
        $this->assign('pname',$pname);
        $this->assign('pid',$pid);
        $this->assign('gid',$gid);
        $name = $this->getActionName();
        $model = D('CreateTask');
        $pk = $model->getPk();
        $id = $_REQUEST ['id'];
        $where_edit = array($pk => $id);
        $vo = $model->where($where_edit)->find();
        $vo['schedule_start']=  date('Y-m-d',$vo['schedule_start']);
        $vo['schedule_end']=  date('Y-m-d',$vo['schedule_end']);
        
        $TaskMember = $vo['task_menber'];
        if(empty($TaskMember)){
            $TaskMemberName = "未添加";
        }else{
            $TaskMemberList = explode(",", $TaskMember);
            for($i=0;$i<count($TaskMemberList);$i++){
                $Temp_TaskMemberName = getUserName_en($TaskMemberList[$i]);
                if($i == (count($TaskMemberList)-1)){
                    $TaskMemberName = $TaskMemberName.$Temp_TaskMemberName;
                }else{
                    $TaskMemberName = $TaskMemberName.$Temp_TaskMemberName.",";
                }
            }
        }
        $vo['task_menber_name']=$TaskMemberName;
        $this->assign('vo', $vo);
        $user = M("User");
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
        
        //TestLeader 部分
        foreach($UserGroupTeam as $k=>$v){
            $UserGroupTeamId[$k] =  $UserGroupTeam[$k]['id'];
        }
        if($_SESSION['administrator'] == true){
            $UserMessageWhere['id'] = array('neq',$_SESSION['authId']);
        }else{
            $UserMessageWhere['user_group_id'] = array('in',$UserGroupTeamId);
            $UserMessageWhere['status'] = array('neq',"0");
        }
        $userlist = $user->where($UserMessageWhere)->order('account asc')->select();//所有下属的成员
        
       //type分类部分 project leverl=1 task leverl=2
       $typemodel = M("Itemtype");
       if($_SESSION['administrator'] == true){
              $where=array(
                  'pid'=>array('neq','0'),
                  //'group_id'=>array('eq',$gid),
                  'level'=>'2'
              );          
        }else{
            //测试所有Type的分组
            $Teamplist = M("user_group")->where(array('id'=>$gid))->find();
            $AllGroupArray = M("user_group")->select();//所有的小组
            $TeampLevel = $Teamplist['level'];
            $TeamPid = $Teamplist['pid'];
            
            if($TeampLevel < 3){
                //$TeamLevel < 3
                $where['group_id']=array('in',$UserGroupTeamId);
            }elseif($TeampLevel > 3){
                //$TeamLevel > 3
                $ascAllGroupArray = $this->AscAllGroupArray($AllGroupArray,$TeamPid);
                $projectType_GroupId = $ascAllGroupArray['id'];
                
                $where['group_id']=array('eq',$projectType_GroupId);
            }elseif($TeampLevel == 3){
                //$TeamLevel = 3 
                $where['group_id']=array('eq',$Teamplist['id']);
            }

            //level 是Itemtype的等级1 project 2 task type
            $where['pid'] =array('neq','0');
            $where['level'] =array('eq','2');
                   
        }
        $type_list = $typemodel->where($where)->select();
        $this->assign('typelist',$type_list);//所属分类
        $this->assign('userlist',$userlist);//TestLeader
        
        $this->display();
    }
    public function childupdata(){
        $name = $this->getActionName();
        $model = M('CreateTask');
        $tasksModel = M("TasksMember");
        $_POST['task_menber']=  $_POST['orgLookup_id'];
        $_POST['schedule_start']=  strtotime($_POST['schedule_start']);
        $_POST['schedule_end']=  strtotime($_POST['schedule_end']);
        
        $TasksMemberId = $_POST['id'];
        $TasksList = $model->where(array('id'=>array('eq',$TasksMemberId)))->find();//存到数据库的原始数据
        
        $NewTasksMemberList = explode(",", $_POST["task_menber"]);
        $OldTasksMemberList = explode(",", $TasksList["task_menber"]);
        $createTasksMemberIndex = 0;
        $updateTasksMemberIndex = 0;
        $deleteTasksMemberIndex = 0;
        
        $changeDeleteTasksIndex = 0;
        $changeUpdateTasksIndex = 0;
        $changeCreateTasksIndex = 0;
        
        $changeDeleteTasksIndex_2 = 0;
        $changeUpdateTasksIndex_2 = 0;
        $changeCreateTasksIndex_2 = 0;
        
        if(($TasksList['schedule_start'] == $_POST["schedule_start"] && $TasksList['schedule_end'] == $_POST["schedule_end"]) && $TasksList['task_menber'] == $_POST["task_menber"]){
            if($TasksList['status'] != $_POST["status"]){
                $StatusData = $_POST["status"];
                 //更新数组
                $changeUpdateTasksUnixData = array(
                    'schedule_start'=>$_POST["schedule_start"],
                    //'schedule_start_Date'=>date('Y/m/d',$_POST["schedule_start"]),
                    'schedule_end'=>$_POST["schedule_end"],
                    //'schedule_end_Date'=>date('Y/m/d',$_POST["schedule_end"]),
                );
                $changeUpdateTasksUnixData['taskstatus'] = $StatusData;
            }else{
                //status属性没有改变
                $StatusData = $TasksList['status'];
            }
        }else{
            if($TasksList['task_menber'] == $_POST["task_menber"]){
                //Member没有变
                if($TasksList['schedule_start'] != $_POST["schedule_start"] && $TasksList['schedule_end'] != $_POST["schedule_end"]){//false==>1.两个有一个为假，另外一个为真；2.两个都为假
                        $changeSchedule_start = $_POST["schedule_start"];
                        $changeSchedule_end   = $_POST["schedule_end"];
                    if($_POST["schedule_start"] > $TasksList['schedule_start']){
                        $changeTotal = (($_POST["schedule_start"]-$TasksList['schedule_start'])/86400);
                        for($i=0;$i<$changeTotal;$i++){
                            $temp_unix = ($TasksList['schedule_start']+(86400*$i));
                            $changeDeleteTasksUnix[$changeDeleteTasksIndex]['unixtimes'] = $temp_unix;
                            $changeDeleteTasksUnix[$changeDeleteTasksIndex]['unixdays'] = date('Y/m/d',$temp_unix);
                            $changeDeleteTasksIndex = $changeDeleteTasksIndex +1;
                        }
                    }elseif($_POST["schedule_start"] < $TasksList['schedule_start']){
                        $changeTotal = (($TasksList['schedule_start']-$_POST["schedule_start"])/86400);
                        for($i=0;$i<$changeTotal;$i++){
                            $temp_unix = ($_POST["schedule_start"]+(86400*$i));
                            $changeCreateTasksUnix[$changeCreateTasksIndex]['unixtimes'] = $temp_unix;
                            $changeCreateTasksUnix[$changeCreateTasksIndex]['unixdays'] = date('Y/m/d',$temp_unix);
                            $changeCreateTasksIndex = $changeCreateTasksIndex +1;
                        }
                    }
                    if($_POST["schedule_end"] > $TasksList['schedule_end']){
                        $changeTotal = ((($_POST["schedule_end"])-($TasksList['schedule_end']+86400))/86400);
                        for($j=0;$j<=$changeTotal;$j++){
                            $temp_unix = (($TasksList["schedule_end"]+86400)+(86400*$j));
                            $changeCreateTasksUnix[$changeCreateTasksIndex]['unixtimes'] = $temp_unix;
                            $changeCreateTasksUnix[$changeCreateTasksIndex]['unixdays'] = date('Y/m/d',$temp_unix);
                            $changeCreateTasksIndex = $changeCreateTasksIndex +1;
                        }
                    }elseif($_POST["schedule_end"] < $TasksList['schedule_end']){
                        $changeTotal = (($TasksList['schedule_end']-($_POST["schedule_end"]+86400))/86400);
                        for($j=0;$j<=$changeTotal;$j++){
                            $temp_unix = (($_POST["schedule_end"]+86400)+(86400*$j));
                            $changeDeleteTasksUnix[$changeDeleteTasksIndex]['unixtimes'] = $temp_unix;
                            $changeDeleteTasksUnix[$changeDeleteTasksIndex]['unixdays'] = date('Y/m/d',$temp_unix);
                            $changeDeleteTasksIndex = $changeDeleteTasksIndex +1;
                        }
                    }
                }else{
                    if($TasksList['schedule_start'] != $_POST["schedule_start"]){
                        $changeSchedule_start = $_POST["schedule_start"];
                        $changeSchedule_end   = $TasksList['schedule_end'];
                        if($_POST["schedule_start"] > $TasksList['schedule_start']){
                            $changeTotal = (($_POST["schedule_start"]-$TasksList['schedule_start'])/86400);
                            for($i=0;$i<$changeTotal;$i++){
                                $temp_unix = ($TasksList['schedule_start']+(86400*$i));
                                $changeDeleteTasksUnix[$changeDeleteTasksIndex]['unixtimes'] = $temp_unix;
                                $changeDeleteTasksUnix[$changeDeleteTasksIndex]['unixdays'] = date('Y/m/d',$temp_unix);
                                $changeDeleteTasksIndex = $changeDeleteTasksIndex +1;
                            }
                        }elseif($_POST["schedule_start"] < $TasksList['schedule_start']){
                            $changeTotal = (($TasksList['schedule_start']-$_POST["schedule_start"])/86400);
                            for($i=0;$i<$changeTotal;$i++){
                                $temp_unix = ($_POST["schedule_start"]+(86400*$i));
                                $changeCreateTasksUnix[$changeCreateTasksIndex]['unixtimes'] = $temp_unix;
                                $changeCreateTasksUnix[$changeCreateTasksIndex]['unixdays'] = date('Y/m/d',$temp_unix);
                                $changeCreateTasksIndex = $changeCreateTasksIndex +1;
                            }
                        }
                    }else{
                        $changeSchedule_start = $TasksList["schedule_start"];
                        $changeSchedule_end   = $_POST["schedule_end"];
                        if($_POST["schedule_end"] > $TasksList['schedule_end']){
                            $changeTotal = ((($_POST["schedule_end"])-($TasksList['schedule_end']+86400))/86400);
                            for($j=0;$j<=$changeTotal;$j++){
                                $temp_unix = (($TasksList["schedule_end"]+86400)+(86400*$j));
                                $changeCreateTasksUnix[$changeCreateTasksIndex]['unixtimes'] = $temp_unix;
                                $changeCreateTasksUnix[$changeCreateTasksIndex]['unixdays'] = date('Y/m/d',$temp_unix);
                                $changeCreateTasksIndex = $changeCreateTasksIndex +1;
                            }
                        }elseif($_POST["schedule_end"] < $TasksList['schedule_end']){
                            $changeTotal = (($TasksList['schedule_end']-($_POST["schedule_end"]+86400))/86400);
                            for($j=0;$j<=$changeTotal;$j++){
                                $temp_unix = (($_POST["schedule_end"]+86400)+(86400*$j));
                                $changeDeleteTasksUnix[$changeDeleteTasksIndex]['unixtimes'] = $temp_unix;
                                $changeDeleteTasksUnix[$changeDeleteTasksIndex]['unixdays'] = date('Y/m/d',$temp_unix);
                                $changeDeleteTasksIndex = $changeDeleteTasksIndex +1;
                            }
                        }
                    }
                }
                //更新数组
                $changeUpdateTasksUnixData = array(
                    'schedule_start'=>$changeSchedule_start,
                    //'schedule_start_Date'=>date('Y/m/d',$changeSchedule_start),
                    'schedule_end'=>$changeSchedule_end,
                    //'schedule_end_Date'=>date('Y/m/d',$changeSchedule_end),
                );
                //再判断status属性有没有改变
                //1.改变了，$changeCreateTasksUnix 数组中的taskstatus=$_post['status']
                //  $changeUpdateTasksUnixData 就要更新原来taskstatus 和 schedule_start schedule_end
                //2.没变
                if($TasksList['status'] != $_POST["status"]){
                    $StatusData = $_POST["status"];
                    foreach($changeCreateTasksUnix as $k=>$v){
                        $changeCreateTasksUnix[$k]['taskstatus'] = $StatusData;
                    }
                    $changeUpdateTasksUnixData['taskstatus'] = $StatusData;
                }else{
                    //status属性没有改变
                    $StatusData = $TasksList['status'];
                    foreach($changeCreateTasksUnix as $k=>$v){
                        $changeCreateTasksUnix[$k]['taskstatus'] = $StatusData;
                    }
                    $changeUpdateTasksUnixData['taskstatus'] = $StatusData;
                }
                $c = 0;
                $d = 0;                
                if(!empty($changeCreateTasksUnix)){
                    foreach($changeCreateTasksUnix as $key=>$value){
                        foreach($NewTasksMemberList as $k=>$v){
                            $changeCreateTasksUnixData[$c]['taskid']= $TasksMemberId;
                            $changeCreateTasksUnixData[$c]['taskstatus']= $StatusData;
                            $changeCreateTasksUnixData[$c]['uid']= $v;
                            $changeCreateTasksUnixData[$c]['detail_time']= $changeCreateTasksUnix[$key]['unixtimes'];
                            $Memberresult = $this->AddMemberData($changeCreateTasksUnix[$key]['unixtimes'], $v, $TasksMemberId);//返回userIdresult userNumberresult
                            $changeCreateTasksUnixData[$c]['task_num']= $Memberresult['userNumberresult'];
                            $changeCreateTasksUnixData[$c]['schedule_start']= $changeSchedule_start;
                            $changeCreateTasksUnixData[$c]['schedule_end']= $changeSchedule_end;
                            $c=$c+1;
                        }
                    }
                }
                if(!empty($changeDeleteTasksUnix)){
                    foreach($changeDeleteTasksUnix as $key=>$value){
                        foreach($NewTasksMemberList as $k=>$v){
                           $changeDeleteTasksUnixData[$d]['taskid'] = $TasksMemberId;
                           $changeDeleteTasksUnixData[$d]['taskstatus'] = $TasksList['status'];
                           $changeDeleteTasksUnixData[$d]['uid'] = $v;
                           $changeDeleteTasksUnixData[$d]['detail_time'] = $changeDeleteTasksUnix[$key]['unixtimes'];
                           $changeDeleteTasksUnixData[$d]['schedule_start'] = $TasksList['schedule_start'];
                           $changeDeleteTasksUnixData[$d]['schedule_end'] = $TasksList['schedule_end'];
                           $d = $d+1;
                        }
                    }
                }
            }else{
                //新旧Member不相等，那么c、u、d就需要得到
                foreach($NewTasksMemberList as $k=>$v){
                    if(in_array($v, $OldTasksMemberList)){
                        $updateTasksMemberId[$updateTasksMemberIndex]=$v;
                        $updateTasksMemberIndex = $updateTasksMemberIndex +1;
                    }else{
                        $createTasksMemberId[$createTasksMemberIndex]=$v;
                        $createTasksMemberIndex = $createTasksMemberIndex + 1;
                    }
                }
                foreach($OldTasksMemberList as $key=>$value){
                    if(in_array($value, $NewTasksMemberList)){

                    }else{
                        $deleteTasksMemberId[$deleteTasksMemberIndex]=$value;
                        $deleteTasksMemberIndex = $deleteTasksMemberIndex + 1;
                    }
                }
                if($TasksList['schedule_start'] == $_POST["schedule_start"] && $TasksList['schedule_end'] == $_POST["schedule_end"]){
                    $changeSchedule_start = $TasksList["schedule_start"];
                    $changeSchedule_end   = $TasksList['schedule_end'];
                    $total = ($_POST["schedule_end"] - $_POST["schedule_start"])/86400;
                    $changeCreateTasksUnix = '';//$changeCreateTasksUnixData
                    $changeDeleteTasksUnix = '';//$changeDeleteTasksUnixData
                    $changeUpdateTasksUnix = '';//$changeUpdateTasksUnixData
                    
                    if(!empty($createTasksMemberId)){
                        if($TasksList['status'] != $_POST["status"]){
                            $StatusData = $_POST["status"];
                        }else{
                            //status属性没有改变
                            $StatusData = $TasksList['status'];
                        }
                        for($i=0;$i<=$total;$i++){
                            $tem = ($_POST["schedule_start"]+(86400*$i));
                            foreach ($createTasksMemberId as $k => $v) {
                                $changeCreateTasksUnixData[$changeCreateTasksIndex]['taskid']= $TasksMemberId;
                                $changeCreateTasksUnixData[$changeCreateTasksIndex]['taskstatus']= $StatusData;
                                $changeCreateTasksUnixData[$changeCreateTasksIndex]['uid']= $v;
                                $changeCreateTasksUnixData[$changeCreateTasksIndex]['detail_time']= $tem;
                                $Memberresult = $this->AddMemberData($tem, $v, $TasksMemberId);//返回userIdresult userNumberresult
                                $changeCreateTasksUnixData[$changeCreateTasksIndex]['task_num']= $Memberresult['userNumberresult'];
                                $changeCreateTasksUnixData[$changeCreateTasksIndex]['schedule_start']= $changeSchedule_start;
                                $changeCreateTasksUnixData[$changeCreateTasksIndex]['schedule_end']= $changeSchedule_end;
                                $changeCreateTasksIndex = $changeCreateTasksIndex + 1;
                            }
                            
                        }
                    }
                    if(!empty($deleteTasksMemberId)){
                        for($i=0;$i<=$total;$i++){
                            $tem = ($_POST["schedule_start"]+(86400*$i));
                            foreach($deleteTasksMemberId as $k=>$v){
                            $changeDeleteTasksUnixData[$changeDeleteTasksIndex]['taskid'] = $TasksMemberId;
                            $changeDeleteTasksUnixData[$changeDeleteTasksIndex]['taskstatus'] = $TasksList['status'];
                            $changeDeleteTasksUnixData[$changeDeleteTasksIndex]['uid'] = $v;
                            $changeDeleteTasksUnixData[$changeDeleteTasksIndex]['detail_time'] = $tem;
                            $changeDeleteTasksUnixData[$changeDeleteTasksIndex]['schedule_start'] = $TasksList['schedule_start'];
                            $changeDeleteTasksUnixData[$changeDeleteTasksIndex]['schedule_end'] = $TasksList['schedule_end'];
                            $changeDeleteTasksIndex = $changeDeleteTasksIndex + 1;
                            }
                        }
                    }
                        if($TasksList['status'] != $_POST["status"]){
                            $StatusData = $_POST["status"];
                            //更新数组
                            $changeUpdateTasksUnixData = array(
                                'schedule_start'=>$changeSchedule_start,
                                //'schedule_start_Date'=>date('Y/m/d',$changeSchedule_start),
                                'schedule_end'=>$changeSchedule_end,
                                //'schedule_end_Date'=>date('Y/m/d',$changeSchedule_end),
                            );
                            $changeUpdateTasksUnixData['taskstatus'] = $StatusData;
                        }else{
                            //status属性没有改变
                            $StatusData = $TasksList['status'];
                        }
                }else{
                    if($TasksList['schedule_start'] != $_POST["schedule_start"] && $TasksList['schedule_end'] != $_POST["schedule_end"]){
                        
                        $changeSchedule_start = $_POST["schedule_start"];
                        $changeSchedule_end   = $_POST["schedule_end"];
                        if($_POST["schedule_start"] > $TasksList['schedule_start']){
                            $changeTotal = (($_POST["schedule_start"]-$TasksList['schedule_start'])/86400);
                            for($i=0;$i<$changeTotal;$i++){
                                $temp_unix = ($TasksList['schedule_start']+(86400*$i));
                                $changeDeleteTasksUnix[$changeDeleteTasksIndex]['unixtimes'] = $temp_unix;
                                $changeDeleteTasksUnix[$changeDeleteTasksIndex]['unixdays'] = date('Y/m/d',$temp_unix);
                                $changeDeleteTasksIndex = $changeDeleteTasksIndex +1;
                            }
                        }elseif($_POST["schedule_start"] < $TasksList['schedule_start']){
                            $changeTotal = (($TasksList['schedule_start']-$_POST["schedule_start"])/86400);
                            for($i=0;$i<$changeTotal;$i++){
                                $temp_unix = ($_POST["schedule_start"]+(86400*$i));
                                $changeCreateTasksUnix[$changeCreateTasksIndex]['unixtimes'] = $temp_unix;
                                $changeCreateTasksUnix[$changeCreateTasksIndex]['unixdays'] = date('Y/m/d',$temp_unix);
                                $changeCreateTasksIndex = $changeCreateTasksIndex +1;
                            }
                        }
                        if($_POST["schedule_end"] > $TasksList['schedule_end']){
                            $changeTotal = ((($_POST["schedule_end"])-($TasksList['schedule_end']+86400))/86400);
                            for($j=0;$j<=$changeTotal;$j++){
                                $temp_unix = (($TasksList["schedule_end"]+86400)+(86400*$j));
                                $changeCreateTasksUnix[$changeCreateTasksIndex]['unixtimes'] = $temp_unix;
                                $changeCreateTasksUnix[$changeCreateTasksIndex]['unixdays'] = date('Y/m/d',$temp_unix);
                                $changeCreateTasksIndex = $changeCreateTasksIndex +1;
                            }
                        }elseif($_POST["schedule_end"] < $TasksList['schedule_end']){
                            $changeTotal = (($TasksList['schedule_end']-($_POST["schedule_end"]+86400))/86400);
                            for($j=0;$j<=$changeTotal;$j++){
                                $temp_unix = (($_POST["schedule_end"]+86400)+(86400*$j));
                                $changeDeleteTasksUnix[$changeDeleteTasksIndex]['unixtimes'] = $temp_unix;
                                $changeDeleteTasksUnix[$changeDeleteTasksIndex]['unixdays'] = date('Y/m/d',$temp_unix);
                                $changeDeleteTasksIndex = $changeDeleteTasksIndex +1;
                            }
                        }
                        //更新数组
                        $changeUpdateTasksUnixData = array(
                                'schedule_start'=>$changeSchedule_start,
                                //'schedule_start_Date'=>date('Y/m/d',$changeSchedule_start),
                                'schedule_end'=>$changeSchedule_end,
                                //'schedule_end_Date'=>date('Y/m/d',$changeSchedule_end),
                            );
                        if($TasksList['status'] != $_POST["status"]){
                            $StatusData = $_POST["status"];
                            $changeUpdateTasksUnixData['taskstatus'] = $StatusData;
                        }else{
                            //status属性没有改变
                            $StatusData = $TasksList['status'];
                        }
                        if(!empty($createTasksMemberId)){
                            $resultTotal = ($_POST["schedule_end"] - $_POST["schedule_start"])/86400;
                            for($i=0;$i<=$resultTotal;$i++){
                                $tem_create = ($_POST["schedule_start"]+(86400*$i));
                                foreach($createTasksMemberId as $k=>$v){
                                    $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['taskid']= $TasksMemberId;
                                    $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['taskstatus']= $StatusData;
                                    $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['uid']= $v;
                                    $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['detail_time']= $tem_create;
                                    $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['detail_time_days']= date('Y/m/d',$tem_create);
                                    $Memberresult = $this->AddMemberData($tem_create, $v, $TasksMemberId);//返回userIdresult userNumberresult
                                    $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['task_num']= $Memberresult['userNumberresult'];
                                    $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['schedule_start']= $changeSchedule_start;
                                    $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['schedule_end']= $changeSchedule_end;
                                    $changeCreateTasksIndex_2 = $changeCreateTasksIndex_2 + 1;
                                }
                            }
                        }
                        
                        if(!empty($changeCreateTasksUnix) && !empty($changeDeleteTasksUnix)){
                            if(!empty($updateTasksMemberId)){
                                foreach($changeCreateTasksUnix as $k=>$v){   
                                    $tem_create = $changeCreateTasksUnix[$k]['unixtimes'];
                                    foreach($updateTasksMemberId as $key=>$value){
                                            $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['taskid']= $TasksMemberId;
                                            $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['taskstatus']= $StatusData;
                                            $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['uid']= $value;
                                            $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['detail_time']= $tem_create;
                                            $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['detail_time_days']= date('Y/m/d',$tem_create);
                                            $Memberresult = $this->AddMemberData($tem_create, $value, $TasksMemberId);//返回userIdresult userNumberresult
                                            $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['task_num']= $Memberresult['userNumberresult'];
                                            $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['schedule_start']= $changeSchedule_start;
                                            $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['schedule_end']= $changeSchedule_end;
                                            $changeCreateTasksIndex_2 = $changeCreateTasksIndex_2 + 1;
                                    }
                                }
                                foreach($changeDeleteTasksUnix as $k=>$v){   
                                        $tem_delete = $changeDeleteTasksUnix[$k]['unixtimes'];
                                        foreach($updateTasksMemberId as $key=>$value){
                                                $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['taskid']= $TasksMemberId;
                                                $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['taskstatus']= $TasksList['status'];
                                                $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['uid']= $value;
                                                $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['detail_time']= $tem_delete;
                                                $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['detail_time_days']= date('Y/m/d',$tem_delete);
                                                $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['schedule_start']= $TasksList['schedule_start'];
                                                $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['schedule_end']= $TasksList['schedule_end'];
                                                $changeDeleteTasksIndex_2 = $changeDeleteTasksIndex_2 + 1;
                                        }
                                }
                                
                            }
                        }else{
                            //有一个为空，条件为：一个为空，另外一个肯定不为空
                            if(!empty($changeCreateTasksUnix)){
                                //$changeDeleteTasksUnix 肯定为空
                             if(!empty($updateTasksMemberId)){
                                foreach($changeCreateTasksUnix as $k=>$v){   
                                    $tem_create = $changeCreateTasksUnix[$k]['unixtimes'];
                                    foreach($updateTasksMemberId as $key=>$value){
                                            $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['taskid']= $TasksMemberId;
                                            $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['taskstatus']= $StatusData;
                                            $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['uid']= $value;
                                            $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['detail_time']= $tem_create;
                                            $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['detail_time_days']= date('Y/m/d',$tem_create);
                                            $Memberresult = $this->AddMemberData($tem_create, $value, $TasksMemberId);//返回userIdresult userNumberresult
                                            $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['task_num']= $Memberresult['userNumberresult'];
                                            $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['schedule_start']= $changeSchedule_start;
                                            $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['schedule_end']= $changeSchedule_end;
                                            $changeCreateTasksIndex_2 = $changeCreateTasksIndex_2 + 1;
                                    }
                                }
                             }
                            }else{
                                //$changeDeleteTasksUnix 肯定不为空
                               //只有删除的日期
                              if(!empty($updateTasksMemberId)){
                                foreach($changeDeleteTasksUnix as $k=>$v){   
                                        $tem_delete = $changeDeleteTasksUnix[$k]['unixtimes'];
                                        foreach($updateTasksMemberId as $key=>$value){
                                                $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['taskid']= $TasksMemberId;
                                                $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['taskstatus']= $TasksList['status'];
                                                $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['uid']= $value;
                                                $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['detail_time']= $tem_delete;
                                                $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['detail_time_days']= date('Y/m/d',$tem_delete);
                                                $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['schedule_start']= $TasksList['schedule_start'];
                                                $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['schedule_end']= $TasksList['schedule_end'];
                                                $changeDeleteTasksIndex_2 = $changeDeleteTasksIndex_2 + 1;
                                        }
                                    } 
                                } 
                            }
                            
                        }
                        if(!empty($deleteTasksMemberId)){
                            $resultTotal = ($TasksList['schedule_end'] - $TasksList['schedule_start'])/86400;
                            for($i=0;$i<=$resultTotal;$i++){
                                $tem_delete = ($TasksList['schedule_start']+(86400*$i));
                                foreach($deleteTasksMemberId as $key=>$value){
                                   $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['taskid']= $TasksMemberId;
                                   $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['taskstatus']= $TasksList['status'];
                                   $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['uid']= $value;
                                   $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['detail_time']= $tem_delete;
                                   $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['detail_time_days']= date('Y/m/d',$tem_delete)."dele";
                                   $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['schedule_start']= $TasksList['schedule_start'];
                                   $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['schedule_end']= $TasksList['schedule_end'];
                                   $changeDeleteTasksIndex_2 = $changeDeleteTasksIndex_2 + 1; 
                                }
                            }
                        }
                    }else{
                        if($TasksList['schedule_start'] != $_POST["schedule_start"]){
                            $changeSchedule_start = $_POST["schedule_start"];
                            $changeSchedule_end   = $TasksList['schedule_end'];
                            if($_POST["schedule_start"] > $TasksList['schedule_start']){
                                $changeTotal = (($_POST["schedule_start"]-$TasksList['schedule_start'])/86400);
                                for($i=0;$i<$changeTotal;$i++){
                                    $temp_unix = ($TasksList['schedule_start']+(86400*$i));
                                    $changeDeleteTasksUnix[$changeDeleteTasksIndex]['unixtimes'] = $temp_unix;
                                    $changeDeleteTasksUnix[$changeDeleteTasksIndex]['unixdays'] = date('Y/m/d',$temp_unix);
                                    $changeDeleteTasksIndex = $changeDeleteTasksIndex +1;
                                }
                            }elseif($_POST["schedule_start"] < $TasksList['schedule_start']){
                                $changeTotal = (($TasksList['schedule_start']-$_POST["schedule_start"])/86400);
                                for($i=0;$i<$changeTotal;$i++){
                                    $temp_unix = ($_POST["schedule_start"]+(86400*$i));
                                    $changeCreateTasksUnix[$changeCreateTasksIndex]['unixtimes'] = $temp_unix;
                                    $changeCreateTasksUnix[$changeCreateTasksIndex]['unixdays'] = date('Y/m/d',$temp_unix);
                                    $changeCreateTasksIndex = $changeCreateTasksIndex +1;
                                }
                            }
                        }else{
                            $changeSchedule_start = $TasksList["schedule_start"];
                            $changeSchedule_end   = $_POST["schedule_end"];
                            
                            if($_POST["schedule_end"] > $TasksList['schedule_end']){
                                $changeTotal = ((($_POST["schedule_end"])-($TasksList['schedule_end']+86400))/86400);
                                for($j=0;$j<=$changeTotal;$j++){
                                    $temp_unix = (($TasksList["schedule_end"]+86400)+(86400*$j));
                                    $changeCreateTasksUnix[$changeCreateTasksIndex]['unixtimes'] = $temp_unix;
                                    $changeCreateTasksUnix[$changeCreateTasksIndex]['unixdays'] = date('Y/m/d',$temp_unix);
                                    $changeCreateTasksIndex = $changeCreateTasksIndex +1;
                                }
                            }elseif($_POST["schedule_end"] < $TasksList['schedule_end']){
                                $changeTotal = (($TasksList['schedule_end']-($_POST["schedule_end"]+86400))/86400);
                                for($j=0;$j<=$changeTotal;$j++){
                                    $temp_unix = (($_POST["schedule_end"]+86400)+(86400*$j));
                                    $changeDeleteTasksUnix[$changeDeleteTasksIndex]['unixtimes'] = $temp_unix;
                                    $changeDeleteTasksUnix[$changeDeleteTasksIndex]['unixdays'] = date('Y/m/d',$temp_unix);
                                    $changeDeleteTasksIndex = $changeDeleteTasksIndex +1;
                                }
                            }
                        }
                        //更新数组
                            $changeUpdateTasksUnixData = array(
                                    'schedule_start'=>$changeSchedule_start,
                                    //'schedule_start_Date'=>date('Y/m/d',$changeSchedule_start),
                                    'schedule_end'=>$changeSchedule_end,
                                    //'schedule_end_Date'=>date('Y/m/d',$changeSchedule_end),
                                );
                            if($TasksList['status'] != $_POST["status"]){
                                $StatusData_2 = $_POST["status"];
                                $changeUpdateTasksUnixData['taskstatus'] = $StatusData_2;
                            }else{
                                //status属性没有改变
                                $StatusData_2 = $TasksList['status'];
                            }
                        if(!empty($createTasksMemberId)){
                                $resultTotal = ($_POST["schedule_end"] - $_POST["schedule_start"])/86400;
                                for($i=0;$i<=$resultTotal;$i++){
                                    $tem_create = ($_POST["schedule_start"]+(86400*$i));
                                    foreach($createTasksMemberId as $k=>$v){
                                        $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['taskid']= $TasksMemberId;
                                        $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['taskstatus']= $StatusData_2;
                                        $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['uid']= $v;
                                        $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['detail_time']= $tem_create;
                                        $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['detail_time_days']= date('Y/m/d',$tem_create);
                                        $Memberresult = $this->AddMemberData($tem_create, $v, $TasksMemberId);//返回userIdresult userNumberresult
                                        $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['task_num']= $Memberresult['userNumberresult'];
                                        $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['schedule_start']= $changeSchedule_start;
                                        $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['schedule_end']= $changeSchedule_end;
                                        $changeCreateTasksIndex_2 = $changeCreateTasksIndex_2 + 1;
                                    }
                                }
                        }
                        //有一个为空，条件为：一个为空，另外一个肯定不为空
                        if(!empty($changeCreateTasksUnix)){
                                //$changeDeleteTasksUnix 肯定为空
                             if(!empty($updateTasksMemberId)){
                                foreach($changeCreateTasksUnix as $k=>$v){   
                                    $tem_create = $changeCreateTasksUnix[$k]['unixtimes'];
                                    foreach($updateTasksMemberId as $key=>$value){
                                            $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['taskid']= $TasksMemberId;
                                            $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['taskstatus']= $_POST["status"];
                                            $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['uid']= $value;
                                            $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['detail_time']= $tem_create;
                                            $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['detail_time_days']= date('Y/m/d',$tem_create);
                                            $Memberresult = $this->AddMemberData($tem_create, $value, $TasksMemberId);//返回userIdresult userNumberresult
                                            $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['task_num']= $Memberresult['userNumberresult'];
                                            $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['schedule_start']= $changeSchedule_start;
                                            $changeCreateTasksUnixData[$changeCreateTasksIndex_2]['schedule_end']= $changeSchedule_end;
                                            $changeCreateTasksIndex_2 = $changeCreateTasksIndex_2 + 1;
                                    }
                                }
                             }
                        }else{
                                //$changeDeleteTasksUnix 肯定不为空
                               //只有删除的日期
                              if(!empty($updateTasksMemberId)){
                                foreach($changeDeleteTasksUnix as $k=>$v){   
                                        $tem_delete = $changeDeleteTasksUnix[$k]['unixtimes'];
                                        foreach($updateTasksMemberId as $key=>$value){
                                                $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['taskid']= $TasksMemberId;
                                                $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['taskstatus']= $TasksList['status'];
                                                $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['uid']= $value;
                                                $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['detail_time']= $tem_delete;
                                                $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['detail_time_days']= date('Y/m/d',$tem_delete);
                                                $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['schedule_start']= $TasksList['schedule_start'];
                                                $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['schedule_end']= $TasksList['schedule_end'];
                                                $changeDeleteTasksIndex_2 = $changeDeleteTasksIndex_2 + 1;
                                        }
                                    } 
                                } 
                            }
                        
                        if(!empty($deleteTasksMemberId)){
                            $resultTotal = ($TasksList['schedule_end'] - $TasksList['schedule_start'])/86400;
                            for($i=0;$i<=$resultTotal;$i++){
                                $tem_delete = ($TasksList['schedule_start']+(86400*$i));
                                foreach($deleteTasksMemberId as $key=>$value){
                                   $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['taskid']= $TasksMemberId;
                                   $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['taskstatus']= $TasksList['status'];
                                   $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['uid']= $value;
                                   $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['detail_time']= $tem_delete;
                                   $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['detail_time_days']= date('Y/m/d',$tem_delete)."dele";
                                   $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['schedule_start']= $TasksList['schedule_start'];
                                   $changeDeleteTasksUnixData[$changeDeleteTasksIndex_2]['schedule_end']= $TasksList['schedule_end'];
                                   $changeDeleteTasksIndex_2 = $changeDeleteTasksIndex_2 + 1; 
                                }
                            }
                        }
                    }
                }
                
            }
        }
        $separatorResultIndex = 0;
        $separatorResultsIndex = 0;
        $userlistScheduleTotal = ($TasksList['schedule_end'] - $TasksList['schedule_start'])/86400;//原数据的长度
       
        for($i=0;$i<=$userlistScheduleTotal;$i++){
            $tem = ($TasksList['schedule_start']+(86400*$i));
            $separatorId = "data_".$i."id:";//201,202,285,286data_0id:
            $separatorNum = "data_".$i."num:";//data_0num:0,0,0,0data_0num:data_0num:
            $separatorResultId = explode($separatorId, $TasksList['userlists_data']);
            $separatorResultNum = explode($separatorNum, $TasksList['userlists_data']);
            
            $separatorResult[$separatorResultIndex]['ids'] = $separatorResultId['1'];
            $separatorResult[$separatorResultIndex]['Nums'] = $separatorResultNum['1'];
            $separatorResult[$separatorResultIndex]['Unix'] = $tem;
            $separatorResult[$separatorResultIndex]['Days'] = date("Y/m/d", $tem);
            $separatorResultIndex = $separatorResultIndex + 1;
        }
        //如果Member改变了
        foreach($separatorResult as $k=>$v){
            if(!empty($separatorResult[$k]['ids'])){
                 $separatorResult_Id= explode(",", $separatorResult[$k]['ids']);
                 $separatorResult_Num= explode(",", $separatorResult[$k]['Nums']);
                 foreach($separatorResult_Id as $key=>$value){
                     $separatorResultGather[$separatorResultsIndex]['uid']=$value;
                     $separatorResultGather[$separatorResultsIndex]['num']=$separatorResult_Num[$key];
                     $separatorResultGather[$separatorResultsIndex]['unix']=$separatorResult[$k]['Unix'];
                     $separatorResultGather[$separatorResultsIndex]['days']=$separatorResult[$k]['Days'];
                     $separatorResultsIndex = $separatorResultsIndex + 1;
                 }
            }else{
                 $separatorResult_Id= explode(",", $TasksList['task_menber']);
                 foreach($separatorResult_Id as $key=>$value){
                     $separatorResultGather[$separatorResultsIndex]['uid']=$value;
                     $separatorResultGather[$separatorResultsIndex]['num']="";
                     $separatorResultGather[$separatorResultsIndex]['unix']=$separatorResult[$k]['Unix'];
                     $separatorResultGather[$separatorResultsIndex]['days']=$separatorResult[$k]['Days'];
                     $separatorResultsIndex = $separatorResultsIndex + 1;
                 }
            }
        }
        //以前schedule及num
        foreach($separatorResultGather as $k=>$v){
            if(in_array($separatorResultGather[$k]['uid'],$NewTasksMemberList)){
                
            }else{
                unset($separatorResultGather[$k]);
            }
        }
        
        $ResultGatherIndexs = 0;
        $userPostScheduleTotal = ($_POST['schedule_end'] - $_POST['schedule_start'])/86400;//原数据的长度
        for($i=0;$i<=$userPostScheduleTotal;$i++){
            $tem = ($_POST['schedule_start']+(86400*$i));
            $ResultGatherIndex = 0;
            foreach($separatorResultGather as $k=>$v){
              if($tem == $separatorResultGather[$k]['unix']){
                $ResultGatherList[$ResultGatherIndexs][$ResultGatherIndex]['detail_time'] = $separatorResultGather[$k]['unix'];
                $ResultGatherList[$ResultGatherIndexs][$ResultGatherIndex]['uid'] = $separatorResultGather[$k]['uid'];
                $ResultGatherList[$ResultGatherIndexs][$ResultGatherIndex]['task_num'] = $separatorResultGather[$k]['num'];
                $ResultGatherList[$ResultGatherIndexs][$ResultGatherIndex]['days'] = date("Y/m/d", $separatorResultGather[$k]['unix']);
                $ResultGatherIndex = $ResultGatherIndex + 1;
              }
            }
            if(!empty($changeCreateTasksUnixData)){
                foreach($changeCreateTasksUnixData as $k=>$v){
                    if($tem == $changeCreateTasksUnixData[$k]['detail_time']){
                        $ResultGatherList[$ResultGatherIndexs][$ResultGatherIndex]['detail_time'] = $changeCreateTasksUnixData[$k]['detail_time'];
                        $ResultGatherList[$ResultGatherIndexs][$ResultGatherIndex]['uid'] = $changeCreateTasksUnixData[$k]['uid'];
                        $ResultGatherList[$ResultGatherIndexs][$ResultGatherIndex]['task_num'] = $changeCreateTasksUnixData[$k]['task_num'];
                        $ResultGatherList[$ResultGatherIndexs][$ResultGatherIndex]['days'] = date("Y/m/d", $changeCreateTasksUnixData[$k]['detail_time']);
                        $ResultGatherIndex = $ResultGatherIndex + 1;
                    }
                }
            }
            $ResultGatherIndexs = $ResultGatherIndexs + 1;
        }
        foreach($ResultGatherList as $k=>$v){
            $ResultGatherArray = $ResultGatherList[$k];
            $ResultGatherseparatorId = "";
            $ResultGatherseparatorNum = "";
            for($i=0;$i<count($ResultGatherArray);$i++){
                if($i == (count($ResultGatherArray)-1)){
                    $ResultGatherseparatorId = $ResultGatherseparatorId.$ResultGatherArray[$i]["uid"];
                    $ResultGatherseparatorNum = $ResultGatherseparatorNum.$ResultGatherArray[$i]["task_num"];
                }else{
                    $ResultGatherseparatorId = $ResultGatherseparatorId.$ResultGatherArray[$i]["uid"].",";
                    $ResultGatherseparatorNum = $ResultGatherseparatorNum.$ResultGatherArray[$i]["task_num"].",";
                }
            }
            $TempDataIdList = "data_".$k."id:".$ResultGatherseparatorId."data_".$k."id:";
            $TempDataNumberList = "data_".$k."num:".$ResultGatherseparatorNum."data_".$k."num:";
            $AdduserlistsData = $AdduserlistsData.$TempDataIdList.$TempDataNumberList;
        }
        $_POST['userlists_data']=  $AdduserlistsData;
        
        if(!empty($TasksList['schedule_end'])){
            
        }
        if(!empty($changeCreateTasksUnixData)){
            foreach($changeCreateTasksUnixData as $k=>$v){
               $Alldetail[$k] = $changeCreateTasksUnixData[$k]['detail_time'];
            }
            $AlldetailIndex = 0;
            foreach(array_unique($Alldetail) as $key=>$value){
                $AlldetailList[$AlldetailIndex] = $value;//需要创建的不重复时间轴
                $AlldetailIndex = $AlldetailIndex + 1;
            }
        }
        if(!empty($changeDeleteTasksUnixData)){
           foreach($changeDeleteTasksUnixData as $k=>$v){
               $AlldetailDelete[$k] = $changeCreateTasksUnixData[$k]['detail_time'];
            }
            $AlldetailDeleteIndex = 0;
            foreach(array_unique($AlldetailDelete) as $key=>$value){
                $AlldetailDeleteList[$AlldetailDeleteIndex] = $value;//需要创建的不重复时间轴
                $AlldetailDeleteIndex = $AlldetailDeleteIndex + 1;
            } 
        }
        $task_lists_total= ($TasksList['schedule_end'] - $TasksList['schedule_start'])/86400;
        $task_lists_New_total= ($_POST['schedule_end'] - $_POST['schedule_start'])/86400;
        $separateTaskListsNum = explode(",", $TasksList['task_lists']);
        $separateDLNum = explode(",", $TasksList['dllists']);
        for($i=0;$i<=$task_lists_total;$i++){
            $tem = ($TasksList['schedule_start']+(86400*$i));
            $task_lists_Old[$i]['unixtime'] = $tem;
            $task_lists_Old[$i]['days'] = date("Y-m-d",$tem);
            $task_lists_Old[$i]['actuallyprogress'] = $separateTaskListsNum[$i];
            $task_lists_Old[$i]['dlgrap'] = $separateDLNum[$i];
        }
        for($i=0;$i<=$task_lists_New_total;$i++){
            $tem = ($_POST['schedule_start']+(86400*$i));
            $task_lists_New[$i]['unixtime'] = $tem;
            $task_lists_New[$i]['days'] = date("Y-m-d",$tem);
            if(in_array($tem, $AlldetailList)){
                $task_lists_New[$i]['actuallyprogress'] = "";
                $task_lists_New[$i]['dlgrap'] = "";
            }else{
                foreach($task_lists_Old as $key=>$value){
                    if($tem == $task_lists_Old[$key]['unixtime']){
                       $task_lists_New[$i]['actuallyprogress'] = $task_lists_Old[$key]['actuallyprogress'];
                       $task_lists_New[$i]['dlgrap'] = $task_lists_Old[$key]['dlgrap']; 
                    }
                }
            }
            
        }
        $task_lists_separate = "";
        $dlgrap_separate = "";
        for($i=0;$i<count($task_lists_New);$i++){
            if($i == (count($task_lists_New)-1)){
                if(empty($task_lists_New[$i]['actuallyprogress'])){
                  $task_lists_separate = $task_lists_separate."0";  
                }else{
                  $task_lists_separate = $task_lists_separate.$task_lists_New[$i]['actuallyprogress'];  
                }
                if(empty($task_lists_New[$i]['dlgrap'])){
                   $dlgrap_separate = $dlgrap_separate."0"; 
                }else{
                   $dlgrap_separate = $dlgrap_separate.$task_lists_New[$i]['dlgrap']; 
                }
                
            }else{
                if(empty($task_lists_New[$i]['actuallyprogress'])){
                  $task_lists_separate = $task_lists_separate."0".",";
                }else{
                  $task_lists_separate = $task_lists_separate.$task_lists_New[$i]['actuallyprogress'].",";
                }
                if(empty($task_lists_New[$i]['dlgrap'])){
                   $dlgrap_separate = $dlgrap_separate."0".",";
                }else{
                   $dlgrap_separate = $dlgrap_separate.$task_lists_New[$i]['dlgrap'].",";
                }
            }
        }
        $model->where(array('id'=>$_POST['id']))->data(array('task_lists'=>$task_lists_separate,'dllists'=>$dlgrap_separate))->save();
        
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        // 更新数据
        $list = $model->save();
        if (false !== $list) {
            if(!empty($changeDeleteTasksUnixData)){
                foreach ($changeDeleteTasksUnixData as $key => $value) {
                    $where["taskid"] = $changeDeleteTasksUnixData[$key]["taskid"];
                    $where["uid"] = $changeDeleteTasksUnixData[$key]["uid"];
                    $where["detail_time"] = $changeDeleteTasksUnixData[$key]["detail_time"];
                    $where["schedule_start"] = $changeDeleteTasksUnixData[$key]["schedule_start"];
                    $where["schedule_end"] = $changeDeleteTasksUnixData[$key]["schedule_end"];
                    $tasksModel->where($where)->delete();
                }
            }
            if(!empty($changeUpdateTasksUnixData)){
                $updatenote = $tasksModel->where(array('taskid'=>$_POST['id']))->data($changeUpdateTasksUnixData)->save();
            }
            if(!empty($changeCreateTasksUnixData)){
                $createnote = $tasksModel->addAll($changeCreateTasksUnixData);
            }
            if(false !== $updatenote && false !== $createnote){
                $this->saveLog(1, $list."||".$updatenote.$createnote);
                $this->success('编辑成功!');
            }else{
                if(false == $updatenote && false == $createnote){
                   $this->saveLog(0, $list."||".$updatenote.$createnote);
                    $this->success('编辑失败!部分写入、更新系统错误请尽快联系管理员'); 
                }else{
                    if(false !== $updatenote){
                        $this->saveLog(0, $list."||".$updatenote.$createnote);
                        $this->success('编辑失败!写入数据系统错误请尽快联系管理员');
                    }else{
                        $this->saveLog(0, $list."||".$updatenote.$createnote);
                        $this->success('编辑失败!更新数据系统错误请尽快联系管理员');    
                    }
                }
            }
        } else {
            $this->saveLog(0, $list);
            //错误提示
            $this->error('编辑失败!');
        }
    }
    public function childaddmenber(){
        $name = $this->getActionName();
        $taskdata=array();
        $tid=$_REQUEST['id'];
        
        $tm=D('CreateTask');
        $umodel=M('User');
        $tmlist = $tm->relation(true)->select($tid);
        $taskdata['id']=$_REQUEST['id'];
        $taskdata['project_id']=$tmlist['0']['project_id'];
        $taskdata['pteamid']=$tmlist[0]['CreateArticle']['teamid'];
        $taskdata['task_title']=$tmlist['0']['task_title'];
        $teamid=$tmlist[0]['CreateArticle']['teamid'];
        $taskdata['est_time']=$tmlist['0']['est_time'];
        $taskdata['schedule_start']=$tmlist['0']['schedule_start'];
        $taskdata['schedule_end']=$tmlist['0']['schedule_end'];
        
        //$query=$umodel->where(array(array('user_group_id',$teamid),'eq'))->select(false);
        //$userlists=$umodel->field(array('id'=>'uid','account'=>'uname','job_number'=>'unumber','email'=>'uemail','status'=>'ustatus'))->where(array('user_group_id'=>array('eq',$teamid),'id'=>array('neq','1')))->select();//"user_group_id = $teamid"
        
        
        $st=$taskdata['schedule_start'];
        $et=$taskdata['schedule_end'];
        $total=($et-$st)/86400;//$total=($et-$st)/86400;
        $days_task=array();
        $w=0;
        for($i=0; $i<=$total;$i++){
            $tem = ($st+(86400*$i));
            $tem_a=getdate($tem);
            if($tem_a['wday'] != '6' && $tem_a['wday'] != '0'){
                $days_task[$w]['days']=date('Y/m/d',($st+(86400*$i)));
                $days_task[$w]['unix']=($st+(86400*$i));
                //$days_task[$w]['progres']=$task_lists[$w];
                //$days_task[$w]['dllists']=$dllists[$w];
                $w=$w+1;
            }
        }
        $this->assign('days_list',$days_task);
        $this->assign('line',$w);//显示多少行
        $this->assign('taskid',$taskdata['id']);
        $this->assign('tname',$taskdata['task_title']);
        $this->assign('st',$taskdata['schedule_start']);
        $this->assign('et',$taskdata['schedule_end']);
        $this->assign('tname', $tmlist['0']['task_title']);
        
        $TaskMember = $tmlist['0']['task_menber'];
        
        if(empty($TaskMember)){
            $TaskMemberName = "未添加";
        }else{
            $TaskMemberList = explode(",", $TaskMember);
            for($i=0;$i<count($TaskMemberList)-1;$i++){
                $Temp_TaskMemberName = getUserName_en($TaskMemberList[$i]);
                if($i == (count($TaskMemberList)-2)){
                    $TaskMemberName = $TaskMemberName.$Temp_TaskMemberName;
                }else{
                    $TaskMemberName = $TaskMemberName.$Temp_TaskMemberName.",";
                }
            }
        }
        $this->assign('TaskMemberName',$TaskMemberName);
        $this->assign('membeName', $TaskMemberName);
        $this->assign('membe', $TaskMember);
        
        $gid=$_SESSION['gid'];
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
        foreach($UserGroupTeam as $k=>$v){
            $UserGroupTeamId[$k] =  $UserGroupTeam[$k]['id'];
        }
        
        //查询条件
        if(empty($_REQUEST['usesrname'])){
           $where['user_group_id'] = array('in',$UserGroupTeamId);
           $where['id']=array('neq','1');
        }else{
            //array('user_group_id'=>array('eq',$teamid),'id'=>array('neq','1'))
           $where['user_group_id'] = array('in',$UserGroupTeamId);
           $where['id']=array('neq','1');
           $where['account']=array('like',"%".$_REQUEST['usesrname']."%");
        }
        $where['status']=array('neq','0');
        //取得满足条件的记录数
        $count = $umodel->where($where)->count($umodel->getPk());
        
        //echo $model->getLastSql();
        if ($count > 0) {
            import("ORG.Util.Page");
            //创建分页对象
            if (!empty($_REQUEST ['listRows'])) {
                $listRows = $_REQUEST ['listRows'];
            } else {
                $listRows = '';
            }
            $p = new Page($count, $listRows);
	    if(empty($_REQUEST['pageNum'])){
                $firstRow = $p->firstRow;
                $listRows = $p->listRows;
            }else{
                $firstRow = ($p->listRows)*($_REQUEST['pageNum'] - 1);
                $listRows = ($p->listRows)*($_REQUEST['pageNum']);
            }
            
        }
        
        //分页查询数据
        $voList = $umodel->field(array('id'=>'uid','account'=>'uname','job_number'=>'unumber','email'=>'uemail','status'=>'ustatus'))->where($where)->order('id desc')->limit($firstRow . ',' . $listRows)->select();
        //echo $umodel->getLastSql();
        $this->assign('totalCount', $count);
        $this->assign('numPerPage', $p->listRows);
        $this->assign('currentPage', !empty($_REQUEST[C('VAR_PAGE')]) ? $_REQUEST[C('VAR_PAGE')] : 1);
        
        $this->assign('list',$voList);
        $this->display();
    }
    /*
     * 查看每个member详细的task信息
     */
    public function expatiation(){
        $uid = $_REQUEST['uid'];
        $uname = $_REQUEST['un'];
        $unix  = $_REQUEST['su'];
        $taskmodel=D('TasksMember');
        $taskmodel_c=D('CreateTask');
        
        //$tasklist = $taskmodel->where(array('uid'=>array('eq',$uid)))->select();//对应用户的所有task列表
        $list=$taskmodel->where(array('detail_time'=>array('eq',$unix),'uid'=>array('eq',$uid)))->select();//查找出具体一天用户的工作量
        //找出所有taskid
        foreach($list as $k => $v){
            if($list[$k]['taskid'] != $list[$k-1]['taskid']){
                $taskid = $taskid.$list[$k]['taskid'].',';
            }
        }
        $taskid_te = explode(',', $taskid);
        for($i=0; $i<count($taskid_te)-1; $i++){
            $taskid_arr[$i]=$taskid_te[$i];
        }
        $pk=$taskmodel_c->getPk();
        $condition = array($pk => array('in', $taskid_arr),'status'=>array('neq','0'),'_logic'=>'And');
        $createtask=$taskmodel_c->field('id,task_title')->where($condition)->select();//查询所有不为0的tasklist并且id在指定数组
        
        
        //将激活状态二维id数组转成一维
        foreach($createtask as $key => $value){
            $temp_date[$key] = $createtask[$key]['id'];
        }
        //将对应taskid的名称添加到数组里面
        foreach ($list as $key => $value) {
            //task_title
            $task_id_temp = $list[$key]['taskid'];
            foreach ($createtask as $k => $v) {
                if($task_id_temp == $createtask[$k]['id']){
                    $list[$key]['task_name']=$createtask[$k]['task_title'];
                }
            }
        }
        //销毁$list内已经完成的Taskid，条件是status=0
        foreach($list as $key=>$value){
            if(in_array($list[$key]['taskid'],$temp_date)){
                
            }else{
                unset($list[$key]);
            }
        }
        for($i=0;$i<=count($list);$i++){
            $a += $list[$i]['task_num'];
        }
        $a = 1-$a;//同一天内内容分步量
        
        $this->assign('tasklist', $list);
        $this->assign('tasklist_number',  count($list));
        $this->assign('task_total',$a);
        $this->display();
    }
    /*
     * 功能;对每一天的工做任务添加Member
     */
    public function expatiation_2(){
       $mb=$_REQUEST['mb'];//之前所有添加的成员
       $mn=$_REQUEST['mn'];//之前所有添加的成员
       $unix=$_REQUEST['su'];//具体日期
       $tid=$_REQUEST['taskid'];
       $user=M('User');
       $taskmodel = M('TasksMember');
       $taskmodel_c=D('CreateTask');
       $TasksTitle= $taskmodel_c->where(array('id'=>$tid))->getField("task_title");
       $this->assign("TasksTitle",$TasksTitle);
       //将成员分隔为数组
       $mb_t=explode(',',$mb);//临时接收mb的数组,并去掉空的数组
       $mn_t=  explode(",", $mn);
       for($i=0;$i<count($mb_t);$i++){
           if(!empty($mb_t[$i])){
            $mb_list[$i]=$mb_t[$i];   
           }
       }
       foreach($mb_t as $k=>$v){
           $processingTeamp[$k]['id'] = $v;
           $processingTeamp[$k]['task_num'] = $mn_t[$k];
       }
       foreach($processingTeamp as $key=>$value){
           if(!empty($processingTeamp[$key]['id'])){
               $processing[$key]['id'] = $processingTeamp[$key]['id'];
               $processing[$key]['task_num'] =$processingTeamp[$key]['task_num'];
           }
       }
       $pk=$user->getPk();
       
       if(!empty($_REQUEST['support'])){
           $supportTeam = $this->AllGroupArray($_REQUEST['support']);
            foreach($supportTeam as $k=>$v){
                $supportTeam[$k] = $supportTeam[$k]['id'];
            }
            for($i=0;$i<count($supportTeam);$i++){
                if($i == count($supportTeam)-1){
                    $supportTeamId = $supportTeamId.$supportTeam[$i]; 
                }else{
                    $supportTeamId = $supportTeamId.$supportTeam[$i].","; 
                }
            }
            for($i=0;$i<count($mb_list);$i++){
                if($i == count($mb_list)-1){
                    $tempAresId = $tempAresId.$mb_list[$i]; 
                }else{
                    $tempAresId = $tempAresId.$mb_list[$i].",";
                }
            }
            $where = "status <> 0 And ($pk IN ($tempAresId) OR user_group_id IN ($supportTeamId))";
       }else{
           $where=array(
                $pk => array('in', $mb_list),
                'status'=>array('neq','0'),
            );
       }
       
       $user_list = $user->field('id,account,nickname,job_number')->where($where)->order('account asc')->select();
       
       //处理具体日期的任务量问题
       $list=$taskmodel->where(array('detail_time'=>array('eq',$unix),'uid'=>array('in',$mb_list),'taskid'=>array('neq',$tid)))->select();//查找出具体一天用户的工作量
       
       //把姓名添加到task任务内
       foreach ($list as $key => $value) {
           foreach ($user_list as $k => $v) {
              if($list[$key]["uid"] == $user_list[$k]['id']){
                  $list[$key]["uname_e"] = $user_list[$k]['account'];
                  $list[$key]["uname_z"] = $user_list[$k]['nickname'];
                  $list[$key]["ujnumber"] = $user_list[$k]['job_number'];
              } 
           }
       }
       
        //找出所有taskid
        foreach($list as $k => $v){
            if($list[$k]['taskid'] != $list[$k-1]['taskid']){
                $taskid = $taskid.$list[$k]['taskid'].',';
            }
        }
        $taskid_te = explode(',', $taskid);
        for($i=0; $i<count($taskid_te)-1; $i++){
            $taskid_arr[$i]=$taskid_te[$i];
        }
        $pk=$taskmodel_c->getPk();
        $condition = array($pk => array('in', $taskid_arr),'status'=>array('neq','0'),'finish'=>array('neq','1'),'_logic'=>'And');
        $createtask=$taskmodel_c->field('id,task_title')->where($condition)->select();//查询所有不为0的tasklist并且id在指定数组
        
        //将create_task的信息添充到list数组中去
        foreach($list as $key =>$value){
            foreach ($createtask as $k => $v) {
                if($list[$key]['taskid'] == $createtask[$k]['id']){
                    $list[$key]['task_title']=$createtask[$k]['task_title'];
                }
            }
        }
        //将激活状态二维id数组转成一维,返回的都是现在未完成的taskid
        foreach($createtask as $key => $value){
            $temp_date[$key] = $createtask[$key]['id'];
        }
        //销毁$list内已经完成的Taskid，条件是status=0
        foreach($list as $key=>$value){
            if(in_array($list[$key]['taskid'],$temp_date)){
                
            }else{
                unset($list[$key]);
            }
        }
        
        //将计算出member的总数来
        foreach($list as $key=>$value){
            $data_id[$key]=$list[$key]['uid'];
            $data_num[$key]=$list[$key]['task_num'];
        }
        foreach($user_list as $k=>$v){
            $data_num_temp=0;
            $key_array=array_keys($data_id,$user_list[$k]['id']);
            
            foreach($key_array as $ks=>$vs){
                $data_num_temp =$data_num_temp + $data_num[$vs];
            }
            $user_list[$k]['total']=$data_num_temp;
            $user_list[$k]['residue']=(1-$data_num_temp);
        }
        $supportModel=M("UserGroup");        
        $support=$supportModel->where(array('level'=>array('egt',4)))->select();
        $this->assign("supportlist",$support);
        
        foreach($user_list as $k=>$v){
            $nowTemp = $user_list[$k]['id'];
            foreach($processing as $key=>$value){
                if($nowTemp == $processing[$key]['id']){
                    $user_list[$k]['now'] = $processing[$key]['task_num'];
                }
            }
        }
        $this->assign("mb",$mb);
        $this->assign('user_list', $user_list);
        $this->assign('task_list', $list);
        $this->display();
    }
    /*
     * 详细信息的更新数据功能
     * 占用于$_post的数据处理
     */
    public function expatiation_update(){
        
        $msg = "";
        $navTabId=$_REQUEST['navTabId'];//CreateTask
        $taskid=$_REQUEST['taskid'];
        $mb=$_REQUEST['mb'];//member数组
        $arr_progres=$_REQUEST['lists'];//进展的数组
        $arr_dllists=$_REQUEST['dllists'];//DL所需总数
        $mb_list=explode(',', $mb);
        foreach ($mb_list as $k=>$v){
            $userlists_data_t="userlists_".$v.":";
            foreach ($_REQUEST['userlists_'.$v] as $key=>$value){
                $userlists_data=$userlists_data.$value.',';                
            }
            $data=$data.$userlists_data_t.$userlists_data.$userlists_data_t;
            $userlists_data="";
        }
        
        foreach ($arr_progres as $k=>$v){
            $list_progres=$list_progres.$v.',';
        }
        foreach ($arr_dllists as $k=>$v){
            $list_dllists=$list_dllists.$v.',';
        }
        $data_progres['id']=$taskid;
        $data_progres['task_lists']=$list_progres;
        $data_progres['userlists_data']=$data;
        $data_progres['dllists']=$list_dllists;
        $tmodel=M($navTabId);
        if(!empty($tmodel)){
            $update=$tmodel->save($data_progres);
            if(false !== $update){
                $this->saveLog(1, $update);
                $this->success('task数据保存成功！' . $msg);
            }else{
                $this->saveLog(0, $update);
                $this->success('task数据保存失败！' . $msg);
            }
        }
    
    }
    /*
     * 详细信息的删除功能
     * option:tasks_member 的id ,
     */
    public function expatiation_delet(){
        $msg = "";
        //删除指定记录
        $name = $this->getActionName();
        $model = D('TasksMember');
        $del_time = $_REQUEST['del_time'];
        $st=$_REQUEST['st'];
        $et=$_REQUEST['et'];
        $pid=$_REQUEST['pid'];
        $id = $_REQUEST ['id'];
        $idlist=explode('_',$id);
        $tids = $idlist['0'];//对应TasksMember的主键
        $ids = $idlist['1'];//用户id
        $tid = $idlist['2'];//对应task_create的主键
        $del_num=$idlist['3'];//对应工作量
        $a=0;
        $total=($et-$st)/86400;//比实际天数少一天，索引0开始最为合适
        
        for($i=0; $i<=$total;$i++){
            $tem = ($st+(86400*$i));
            $tem_a=getdate($tem);
            if($tem_a['wday'] != '6' && $tem_a['wday'] != '0'){
                $days_task[$a]['days']=date('Y/m/d',($st+(86400*$i)));
                //$days_task[$w]['progres']=$task_lists[$w];
                //$days_task[$w]['dllists']=$dllists[$w];
                $days_task[$a]['unixtime']=$tem;
                if($del_time == $tem){
                    $del_index=$a;
                }
                $a=$a+1;
            }
        }
        
        if (!empty($model)) {
            $pk = $model->getPk();
            if (empty($tids)){
                //$id = $_REQUEST ['ids'];
            }
            if (isset($id)) {
                /*
                $ids = explode(',', $id);
                foreach ($ids as $k => $v) {
                    $type = $model->where(array($pk => $v))->getField('type');
                    if ($type == 1) {
                        $this->saveLog(0, $list);
                        $msg = "包含系统必须部分，不能删除！";
                        unset($ids[$k]);
                    }
                }*/
                
                if (empty($tids)) {
                    $this->saveLog(0, $list);
                    $this->error('系统错误，不能删除！');
                } else {
                    $condition = array($pk => array('in', $tids));
                    $list = $model->where($condition)->delete();
                    if (false !== $list) {
                        //还要讲$tid的数据修改
                        $task_model = M('CreateTask');
                        $task_list=$task_model->field('userlists_data')->where(array('id'=>array('eq',$tid)))->find();

                        $temp_cha_id="data_".$del_index."id:";
                        $temp_cha_num="data_".$del_index."num:";
                        $temp_cha_ids=explode($temp_cha_id, $task_list['userlists_data']);
                        $temp_cha_nums=explode($temp_cha_num, $task_list['userlists_data']);

                        $tem_uid = explode(',', $temp_cha_ids['1']);
                        $tem_unums = explode(',', $temp_cha_nums['1']);
                        //删除指定$ids的id nums的数据
                        for($i=0;$i<=count($tem_uid)-1;$i++){
                            if($tem_uid[$i] == $ids){
                                $temp_uid_index2 = $i;
                            }else{
                                if($i == count($tem_uid)-1){
                                    $temp_array_uid = $temp_array_uid.$tem_uid[$i];
                                    $temp_array_num = $temp_array_num.$tem_unums[$i];
                                }else{
                                    $temp_array_uid = $temp_array_uid.$tem_uid[$i].',';
                                    $temp_array_num = $temp_array_num.$tem_unums[$i].',';
                                }
                            }
                        }
                        $save_new['userlists_data']=$temp_cha_ids['0'].$temp_cha_id.$temp_array_uid.$temp_cha_id.$temp_cha_num.$temp_array_num.$temp_cha_num.$temp_cha_nums['2'];
                        $save_note=$task_model->where(array('id'=>array('eq',$tid)))->save($save_new);
                        if(false !== $save_note){
                            $this->saveLog(1, $list.$save_note);
                            $this->success('删除成功！' . $msg);
                        }else{
                            $this->saveLog(0, $list.$save_note);
                            $this->error('删除部分数据，但未更新！' . $msg);
                        }
                    }else {
                        $this->saveLog(0, $list);
                        $this->error('删除其他Task数据失败！' . $msg);
                    }
                }
            } else {
                $this->saveLog(0, $list);
                $this->error('非法操作' . $msg);
            }
        }
    }
    
    /*
     * function name:updatemenber
     * 用于添加member时保存数据到对应task中去
     * 时效：正常使用
     * 创建日期:2013.11.06
     */
    
    public function updatemenber(){
        $msg = "";
        $uid=$_REQUEST['ids'];//字符串并列
        $taskid=$_REQUEST['taskid'];
        $name=$_REQUEST['navTabId'];//CreateTask
        $st=$_REQUEST['st'];
        $et=$_REQUEST['et'];
        
        $tmodel=M($name);
        $tmenber=M('TaskMenber');
        $se=$tmodel->field('task_menber')->where(array('id'=>array('eq',$taskid)))->find();
        $selist = explode(",",$se['task_menber']);
        $ids = explode(',', $uid);
        if(empty($se)){
            $data['task_menber']=$uid;//.','.$se['task_menber']
        }else{
            for($i=0;$i<count($ids);$i++){
                if(in_array($ids[$i], $selist)){
                               
                }else{
                   $temp = $temp.$ids[$i].',';   
                }
            }
            $data['task_menber']=$se['task_menber'].$temp;//最后一个会多一个逗号，为兼容以前版本未除掉多余逗号
            
        }
        $sava_note = $tmodel->where(array('id'=>array('eq',$taskid)))->data($data)->save();
        if($sava_note !== false){
                    $this->saveLog(1, $sava_note);
                    $this->success('添加成功！' . $msg); 
                }else{
                    $this->saveLog(0, $sava_note);
                    $this->success('添加失败！' . $msg); 
                }
        /*
        $se=$tmodel->field('task_menber')->where("id = $taskid")->find();
        $selist = explode(",",$se['task_menber']);
        $ids = explode(',', $uid);
        $data['id']=$taskid;
        if(empty($se)){
            $data['task_menber']=$uid;//.','.$se['task_menber']
        }else{
            foreach ($ids as $k => $v) {
                if(in_array($ids[$k], $selist)){
                    
                }else{
                    $temp = $temp.$ids[$k].',';
                }
            }
            $data['task_menber']=$se['task_menber'].$temp;
        }
        $lt=$tmodel->save($data);
        $tmlist=$tmenber->where("taskid = $taskid AND (s_time >=$st AND e_time<=$et)")->select();
        
        $delet = array();
        $updata=array();
        $create=array();
        if (isset($uid)) {
            //$ids = explode(',', $uid);
            if(!empty($tmlist)){
                for ($i = 0; $i<=count($tmlist)-1; $i++){
                    $value=$tmlist[$i]['uid'];
                    $key=$tmlist[$i]['id'];
                    if(in_array($value, $ids)){
                        $updata[$i]['uid']=$value;
                        $updata[$i]['id']=$key;
                    }else{
                        $delet[$i]['uid']=$value;
                        $delet[$i]['id']=$key;
                    }
                }
                $temp=array();
                if(isset($updata)){
                    foreach ($updata as $k =>$v){
                        $temp[$k]=$updata[$k]['uid'];
                    }
                    for($j=0; $j<=count($ids)-1; $j++){
                        $vl=$ids[$j];
                        if(in_array($vl,$temp)){
                            
                        }else{
                            $create[$j]['uid']=$vl;
                        }
                    }
                }else{
                    for($i=0; $i<=count($ids)-1; $i++){
                        $create[$i]['uid']=$ids[$i];
                    }
                }
            }else{
                //直接创建一个数组
                for($i=0; $i<=count($ids)-1;$i++){
                    $create[$i]['uid']=$ids[$i];
                }
            }
            //进行数据插入
            if(!empty($tmenber)){
                $pk=$tmenber->getPk();
                //对应原来数据进行删除
                /*if(!empty($delet)){
                    $delid=array();
                    foreach ($delet as $k=>$v){
                        if(!empty($delet[$k]['id'])){
                            $delid[$k]=$delet[$k]['id'];
                        }
                    }
                   $condition = array($pk => array('in', $delid));
                   $ares=$tmenber->where($condition)->delete();
                }*/
        /*
                if(!empty($create)){
                    $create_data=array();
                    $i=0;
                    foreach ($create as $key => $value) {
                        $create_data[$i]['uid']=$create[$key]['uid'];
                        $create_data[$i]['taskid']=$taskid;
                        $create_data[$i]['s_time']=$st;
                        $create_data[$i]['e_time']=$et;
                        $i=$i+1;
                    }
                    $ares2=$tmenber->addAll($create_data);
                }
                if($lt !== false && $ares2 !== false){
                    $this->saveLog(1, $lt);
                    $this->success('添加成功！' . $msg); 
                }else{
                    $this->saveLog(0, $lt);
                    $this->success('添加失败！' . $msg); 
                }
            }else {
                $this->saveLog(0, $list);
                $this->error('非法无效操作' . $msg);
            }
        }else {
                $this->saveLog(0, $list);
                $this->error('非法无效操作' . $msg);
        }
        */
    }
    public function taskedit(){
        $pid=$_REQUEST['pid'];
        $st=$_REQUEST['st'];
        $et=$_REQUEST['et'];
        $est=$_REQUEST['est'];
        $ratios=$_REQUEST['ratios'];
        $navId=$_REQUEST['navId'];//CreateTask
        $mb=$_REQUEST['mb'];
        
        $tmodel=M($navId);
        $user=M('User');
        $pk=$user->getPk();
        $mbs =  explode(',', $mb);//将member传递值分隔到数组中
        foreach($mbs as $k=>$v){
           if(!empty($v)){
             $delid[$k]=$v;  
           }
        }
        $condition = array($pk => array('in', $delid));
        $userlist=$user->field('id,account')->where($condition)->select();//查找出member的信息
        
        $task_list=$tmodel->where("id = $pid")->getField('task_lists');//
        $data_list=$tmodel->where("id = $pid")->Field('userlists_data,dllists')->find();
        $task_lists=explode(',',$task_list);//进度数
        $dllists=  explode(',', $data_list['dllists']);//DL的数组
        $tasks_arr=$data_list['userlists_data'];//对应Member的任务量数据
        
        //将unix时间戳转换成时间段
        $total=($et-$st)/86400;//比实际天数少一天，索引0开始最为合适
        $days_task=array();
        $w=0;//工作天数的计数
        $n=0;//假日天数的计数
        $includesaturday = 0;//包含周六的总数
        $includesaturdaytotal = 0;
        for($i=0; $i<=$total;$i++){
            $tem = ($st+(86400*$i));
            $tem_a=getdate($tem);
            if($tem_a['wday'] != '6' && $tem_a['wday'] != '0'){
                $w=$w+1;//求出工作天数
                $days_task[$i]['td_bgcolor']="";
                $days_task[$i]['workdaybyday']=$w;
                $days_task[$i]['holidaybyday']='';
            }else{
                $n = $n +1;
                $days_task[$i]['td_bgcolor']="#DFDFDF";
                $days_task[$i]['workdaybyday']='';
                $days_task[$i]['holidaybyday']=$n;
            }
            if($tem_a['wday'] != '0'){
                $days_task[$i]['includesaturday']=$includesaturday;
                $includesaturday = $includesaturday + 1;
                $includesaturdaytotal = $includesaturdaytotal + 1;
            }else{
                $days_task[$i]['includesaturday']="";
            }
            $days_task[$i]['days']=date('m/d',($st+(86400*$i)));
            $days_task[$i]['progres']=$task_lists[$i];
            $days_task[$i]['dllists']=$dllists[$i];
            $days_task[$i]['unixtime']=$tem;
            
        }
        $Eprogres=(round((1/$w),2)*100);
        $recomment=  round(((round($est,1))/($ratios*0.01))/round($w*6.5,1));
        
        //2013.11.06  15:30:30重新写
        //讲用户数组中最后一个多余去掉
        for($i=0;$i<count($delid)-1;$i++){
            $delids[$i]=$delid[$i];
        }
        
        $tasks_member_model=M('TasksMember');
        //每个用户在开始时间到结束时间的数据处理
        
        //数据库的原数据，组合成数组用于比较
        //$w 是前面根据开始时间和结束时间得到的工作总数  $tasklist通过数据库查询出来的
        $task_createmodel=M('CreateTask');
        $tasklist=$task_createmodel->field('userlists_data')->where(array('id'=>array('eq',$pid)))->find();//先查询出源数据，用于后面对比好做创建tasks_member的值
       
        for($i=0;$i<=$total;$i++){
            $temp_cha_id="data_".$i."id:";
            $temp_cha_num="data_".$i."num:";
            $temp_cha_ids=explode($temp_cha_id, $tasklist['userlists_data']);
            $temp_cha_nums=explode($temp_cha_num, $tasklist['userlists_data']);
            $temp_total_data = explode(',', $temp_cha_nums['1']);
            $temp_total_data_id =  explode(',', $temp_cha_ids['1']);
            //Actually Manpower计算公式
            $temp_total_2 = 0;
            if(empty($temp_cha_ids['1'])){
                $temp_total_data_2 = 0;
                $temp_total_data_ids = 0;
            }else{
                foreach($temp_total_data as $key=>$value){
                    $temp_total_2 = $temp_total_2 + $temp_total_data[$key];
                }
                $temp_total_data_2 = $temp_total_2;
                $temp_total_data_ids = count($temp_total_data_id);
            }
            //Gap = a_manpower - task_num的计算公式
            $gap_total=$temp_total_data_2 - $recomment;
            if($gap_total < 0){
                if(empty($temp_cha_ids['1'])){
                    $gap_color="";
                    $gap_total=0;
                }else{
                    $gap_color="#FF0000";
                }
            }else{
                $gap_color="";
            }
            
            $compare[$i]['uid']=$temp_cha_ids['1'];
            $compare[$i]['username']=$tempUsername;
            $compare[$i]['task_num']=$temp_cha_nums['1'];
            $compare[$i]['a_manpower']= $temp_total_data_2;
            $compare[$i]['gap']= $gap_total;
            $compare[$i]['gap_color']= $gap_color;
            $compare[$i]['total_id']= $temp_total_data_ids;
        }
        
        //将$days_task  和 $compare 组合在一起
        foreach($days_task as $key=>$value){
            $days_task[$key]['uid']=$compare[$key]['uid'];
            $tempList = explode(",", $compare[$key]['uid']);
            $tempUsername = "";
            for($i=0;$i<count($tempList);$i++){
                if($i == (count($tempList)-1)){
                    $tempUsername = $tempUsername.getUserName_en($tempList[$i]);
                }else{
                    $tempUsername = $tempUsername.getUserName_en($tempList[$i]).",";
                }
            }
            $days_task[$key]['username']=$tempUsername;
            $days_task[$key]['task_num']=$compare[$key]['task_num'];
            $days_task[$key]['a_manpower']=$compare[$key]['a_manpower'];
            $days_task[$key]['gap']=$compare[$key]['gap'];
            $days_task[$key]['gap_color']=$compare[$key]['gap_color'];
            $days_task[$key]['total_id']=$compare[$key]['total_id'];
            
            if(!empty($days_task[$key]["workdaybyday"])){
                $EprogresDaysTask = ($days_task[$key]["workdaybyday"])/($w);
                $EprogresDaysTask = (round($EprogresDaysTask,2))*100;
                $days_task[$key]['eprogres']=  $EprogresDaysTask;
                if(!empty($days_task[$key]['dllists']) || !empty($compare[$key]['total_id'])){
                    $days_task[$key]['noattend_w'] =  (1 - round(($est/((round($days_task[$key]['dllists']*$days_task[$key]['workdaybyday']*9.2,2) + round($compare[$key]['total_id']*6.5, 2)))),2))*100;
                }else{
                    $days_task[$key]['noattend_w'] = "";
                }
                
            }else{
                $days_task[$key]['eprogres']="";
            }
            if(!empty($days_task[$key]['includesaturday'])){
                $days_task[$key]['noattend_a'] = (1 - round(($est/((round($days_task[$key]['dllists']*($includesaturdaytotal - $days_task[$key]['includesaturday'])*9.2,2) + round($compare[$key]['total_id']*($includesaturdaytotal - $days_task[$key]['includesaturday'])*6.5, 2)))),2))*100;
            }else{
                if($days_task[$key]['includesaturday'] == 0){
                 $days_task[$key]['noattend_a'] = (1 - round(($est/((round($days_task[$key]['dllists']*($includesaturdaytotal - $days_task[$key]['includesaturday'])*9.2,2) + round($compare[$key]['total_id']*($includesaturdaytotal - $days_task[$key]['includesaturday'])*6.5, 2)))),2))*100;   
                }else{
                  $days_task[$key]['noattend_a'] = "";
                }
            }
            
        }
        //dump($days_task);
        //dump($days_task[29]['includesaturday']);
        //dump($includesaturdaytotal);
        //dump((1 - round(($est/((round($days_task[29]['dllists']*($includesaturdaytotal - $days_task[29]['includesaturday'])*9.2,2) + round($days_task[29]['total_id']*($includesaturdaytotal - $days_task[29]['includesaturday'])*6.5, 2)))),2))*100);
        $this->assign('eprogres',$Eprogres);
        $this->assign('recomment',$recomment);
        $this->assign('days_task',$days_task);
        $this->assign('userlist',$userlist);
        
        $this->display();
    }
    public function updatetlist(){
        $msg = "";
        $navTabId=$_REQUEST['navTabId'];//CreateTask
        $taskid=$_REQUEST['taskid'];
        $mb=$_REQUEST['mb'];//member数组
        $arr_progres=$_REQUEST['lists'];//进展的数组
        $arr_dllists=$_REQUEST['dllists'];//DL所需总数
        $mb_list=explode(',', $mb);
        foreach ($mb_list as $k=>$v){
            $userlists_data_t="userlists_".$v.":";
            foreach ($_REQUEST['userlists_'.$v] as $key=>$value){
                $userlists_data=$userlists_data.$value.',';                
            }
            $data=$data.$userlists_data_t.$userlists_data.$userlists_data_t;
            $userlists_data="";
        }
        
        foreach ($arr_progres as $k=>$v){
            $list_progres=$list_progres.$v.',';
        }
        foreach ($arr_dllists as $k=>$v){
            $list_dllists=$list_dllists.$v.',';
        }
        $data_progres['id']=$taskid;
        $data_progres['task_lists']=$list_progres;
        $data_progres['userlists_data']=$data;
        $data_progres['dllists']=$list_dllists;
        $tmodel=M($navTabId);
        if(!empty($tmodel)){
            $update=$tmodel->save($data_progres);
            if(false !== $update){
                $this->saveLog(1, $update);
                $this->success('task数据保存成功！' . $msg);
            }else{
                $this->saveLog(0, $update);
                $this->success('task数据保存失败！' . $msg);
            }
        }
    }
    /*
     * updatetlist方法的第二个测试版本
     * @db lhl_tasks_member
     * @Author  Ares Peng
     * @function 测试记录每天task 的member  工作量
     */
    public function updatetlist_ares(){
        $msg = "";
        $createmodel=M('TasksMember');
        $task_createmodel=M('CreateTask');
        $navTabId=$_REQUEST['navTabId'];
        $st=$_REQUEST['st'];
        $et=$_REQUEST['et'];
        $taskid=$_REQUEST['taskid'];
        $mb=$_REQUEST['mb'];//member数组
        $arr_progres=$_REQUEST['lists'];//进展的数组
        $arr_dllists=$_REQUEST['dllists'];//DL所需总数
        $mbs=explode(',', $mb);
        foreach($mbs as $k=>$v){
            if(!empty($v)){
                $mb_list[$k]=$v;
            }
        }
        $data_create=array();
        $total=($et-$st)/86400;//比实际天数少一天，索引0开始最为合适
        $days_task=array();
        $data_create=array();
        $a=0;
        
        //根据st,et时间转换成时间轴，除星期六星期天以外
        for($i=0; $i<=$total;$i++){
            $tem = ($st+(86400*$i));
            $tem_a=getdate($tem);
            if($tem_a['wday'] != '6' && $tem_a['wday'] != '0'){
                $w=$w+1;//求出工作天数
                //$days_task[$i]['td_bgcolor']="";
            }else{
                //$days_task[$i]['td_bgcolor']="#DFDFDF";
            }
            $days_task[$i]['days']=date('Y/m/d',($st+(86400*$i)));
            $days_task[$i]['progres']=$task_lists[$w];
            $days_task[$i]['dllists']=$dllists[$w];
            $days_task[$i]['unixtime']=$tem;
            $a=$a+1;
            /*
            if($tem_a['wday'] != '6' && $tem_a['wday'] != '0'){
                $days_task[$a]['days']=date('Y/m/d',($st+(86400*$i)));
                //$days_task[$w]['progres']=$task_lists[$w];
                //$days_task[$w]['dllists']=$dllists[$w];
                $days_task[$a]['unixtime']=$tem;
                $a=$a+1;
            }*/
        }
        
        //根据$a的求出总量对应到提交上来的数组
        for($i=0;$i<=$total;$i++){
            $tem_id="org".$i."_orgid";
            $tem_num="org".$i."_orgNum";
            $org[$i]['id']=$_REQUEST[$tem_id];
            $org[$i]['num']=$_REQUEST[$tem_num];
        }
        
        $tasklist=$task_createmodel->field('userlists_data')->where(array('id'=>array('eq',$taskid)))->find();//先查询出源数据，用于后面对比好做创建tasks_member的值
        
        //数据库的原数据，组合成数组用于比较
        for($i=0;$i<=$total;$i++){
            $temp_cha_id="data_".$i."id:";
            $temp_cha_num="data_".$i."num:";
            $temp_cha_ids=explode($temp_cha_id, $tasklist['userlists_data']);
            $temp_cha_nums=explode($temp_cha_num, $tasklist['userlists_data']);
            $compare[$i]['uid']=$temp_cha_ids['1'];
            $compare[$i]['task_num']=$temp_cha_nums['1'];
        }
        //将$org数组和days_task数组合并为一个数组，但没有其他作用
        foreach ($days_task as $k=>$v) {
            $day_by_task[$k]['days']=$days_task[$k]['days'];
            $day_by_task[$k]['detail_time']=$days_task[$k]['unixtime'];
            $day_by_task[$k]['uid']=$org[$k]['id'];
            $day_by_task[$k]['task_num']=$org[$k]['num'];
            $day_by_task[$k]['taskid']=$taskid;
            $day_by_task[$k]['schedule_start']=$st;
            $day_by_task[$k]['schedule_end']=$et;
        }
        //将$compare和$day_by_task数组比较就能找出需要创建
        $b=0;
        $c=0;
        $d=0;
        $u=0;
        foreach($day_by_task as $k=>$v){
            $day_by_task_uidlist = explode(',', $day_by_task[$k]['uid']);
            $compare_uidlist = explode(',', $compare[$k]['uid']);
            $day_by_task_numlist = explode(',', $day_by_task[$k]['task_num']);
            $compare_numlist = explode(',', $compare[$k]['task_num']);
            if(count($day_by_task_uidlist) > count($compare_uidlist)){
                //有创建的，有更新的(通过值比较)
                foreach ($day_by_task_uidlist as $keys => $values) {
                        if(in_array($day_by_task_uidlist[$keys], $compare_uidlist)){
                            //需要更新的用户
                           //比较值，如果值相等就不需要更新
                            $index = array_search($day_by_task_uidlist[$keys], $compare_uidlist);//返回索引值
                            //$compare_numlist[$index];//真正的源值
                            if($day_by_task_numlist[$keys] == $compare_numlist[$index]){
                                
                            }else{
                                $update_old[$u]['uid']=$day_by_task_numlist[$keys];
                                $update_old[$u]['task_num']=$day_by_task_numlist[$keys];
                                $update_old[$u]['detail_time']=$day_by_task[$k]['detail_time'];
                                $update_old[$u]['taskid']=$day_by_task[$k]['taskid'];
                                $update_old[$u]['schedule_start']=$day_by_task[$k]['schedule_start'];
                                $update_old[$u]['schedule_end']=$day_by_task[$k]['schedule_end'];
                                $u=$u+1;
                            }
                        }else{
                            //需要新创建的用户
                            $create_new[$c]['uid']=$day_by_task_uidlist[$keys];
                            $create_new[$c]['task_num']=$day_by_task_numlist[$keys];
                            $create_new[$c]['detail_time']=$day_by_task[$k]['detail_time'];
                            $create_new[$c]['taskid']=$day_by_task[$k]['taskid'];
                            $create_new[$c]['schedule_start']=$day_by_task[$k]['schedule_start'];
                            $create_new[$c]['schedule_end']=$day_by_task[$k]['schedule_end'];
                            $c +=1;
                        }
                }
                //需要删除的用户
                foreach($compare_uidlist as $ks =>$vs){
                    if(in_array($compare_uidlist[$ks], $day_by_task_uidlist)){
                        
                    }else{
                        $delete_old[$d]['uid']=$compare_uidlist[$ks];
                        $delete_old[$d]['task_num']=$compare_numlist[$ks];
                        $delete_old[$d]['detail_time']=$day_by_task[$k]['detail_time'];
                        $delete_old[$d]['taskid']=$day_by_task[$k]['taskid'];
                        $delete_old[$d]['schedule_start']=$day_by_task[$k]['schedule_start'];
                        $delete_old[$d]['schedule_end']=$day_by_task[$k]['schedule_start'];
                        $d = $d + 1;
                    }
                }
            }elseif(count($day_by_task_uidlist) < count($compare_uidlist)){
                //有删除的，有更新的(通过值比较),有创建的(通过值比较)
                
                //需要删除的数组
                foreach($compare_uidlist as $keys => $values){
                    if(in_array($compare_uidlist[$keys], $day_by_task_uidlist)){
                        //这里是需要更新的数组，分为两种  1.新
                        $index = array_search($compare_uidlist[$keys], $day_by_task_uidlist);
                        if($compare_numlist[$keys] == $day_by_task_numlist[$index]){
                            
                        }else{
                            $update_old[$u]['uid']=$day_by_task_uidlist[$index];
                            $update_old[$u]['task_num']=$day_by_task_numlist[$index];
                            $update_old[$u]['detail_time']=$day_by_task[$k]['detail_time'];
                            $update_old[$u]['taskid']=$day_by_task[$k]['taskid'];
                            $update_old[$u]['schedule_start']=$day_by_task[$k]['schedule_start'];
                            $update_old[$u]['schedule_end']=$day_by_task[$k]['schedule_end'];
                            $u = $u + 1;
                        }
                    }else{
                        $delete_old[$d]['uid']=$compare_uidlist[$keys];
                        $delete_old[$d]['task_num']=$compare_numlist[$keys];
                        $delete_old[$d]['detail_time']=$day_by_task[$k]['detail_time'];
                        $delete_old[$d]['taskid']=$day_by_task[$k]['taskid'];
                        $delete_old[$d]['schedule_start']=$day_by_task[$k]['schedule_start'];
                        $delete_old[$d]['schedule_end']=$day_by_task[$k]['schedule_start'];
                        $d = $d + 1;
                    }
                }
                //需要创建的数组
                foreach ($day_by_task_uidlist as $ks => $vs) {
                    if(in_array($day_by_task_uidlist[$ks], $compare_uidlist)){
                        
                    }else{
                        $create_new[$c]['uid']=$day_by_task_uidlist[$ks];
                        $create_new[$c]['task_num']=$day_by_task_numlist[$ks];
                        $create_new[$c]['detail_time']=$day_by_task[$k]['detail_time'];
                        $create_new[$c]['taskid']=$day_by_task[$k]['taskid'];
                        $create_new[$c]['schedule_start']=$day_by_task[$k]['schedule_start'];
                        $create_new[$c]['schedule_end']=$day_by_task[$k]['schedule_end'];
                        $c = $c +1;
                    }
                }
                
            }else{
                //有创建的，有删除的，有更新的(通过值比较)
                //先由新数据，找出需要创建、更新
                foreach($day_by_task_uidlist as $key=>$value){
                    if(in_array($day_by_task_uidlist[$key], $compare_uidlist)){
                        $indexs=array_search($day_by_task_uidlist[$key], $compare_uidlist);
                        if($day_by_task_numlist[$key] != $compare_numlist[$indexs]){
                            $update_old[$u]['uid']=$day_by_task_uidlist[$key];
                            $update_old[$u]['task_num']=$day_by_task_numlist[$key];
                            $update_old[$u]['detail_time']=$day_by_task[$k]['detail_time'];
                            $update_old[$u]['taskid']=$day_by_task[$k]['taskid'];
                            $update_old[$u]['schedule_start']=$day_by_task[$k]['schedule_start'];
                            $update_old[$u]['schedule_end']=$day_by_task[$k]['schedule_end'];
                            $u = $u + 1;
                        }
                    }else{
                        $create_new[$c]['uid']=$day_by_task_uidlist[$key];
                        $create_new[$c]['task_num']=$day_by_task_numlist[$key];
                        $create_new[$c]['detail_time']=$day_by_task[$k]['detail_time'];
                        $create_new[$c]['taskid']=$day_by_task[$k]['taskid'];
                        $create_new[$c]['schedule_start']=$day_by_task[$k]['schedule_start'];
                        $create_new[$c]['schedule_end']=$day_by_task[$k]['schedule_end'];
                        $c = $c +1;
                    }
                }
                //再由原来数据，找出需要删除
                foreach($compare_uidlist as $keys => $values){
                    if(in_array($compare_uidlist[$keys], $day_by_task_uidlist)){
                        
                    }else{
                        $delete_old[$d]['uid']=$compare_uidlist[$keys];
                        $delete_old[$d]['task_num']=$compare_numlist[$keys];
                        $delete_old[$d]['detail_time']=$day_by_task[$k]['detail_time'];
                        $delete_old[$d]['taskid']=$day_by_task[$k]['taskid'];
                        $delete_old[$d]['schedule_start']=$day_by_task[$k]['schedule_start'];
                        $delete_old[$d]['schedule_end']=$day_by_task[$k]['schedule_start'];
                        $d = $d + 1;
                    }
                }
            }
        }
        //将uid和num对应并连接成字符串便于存放到数据库create_task中
        foreach ($day_by_task as $key => $value) {
            $lianjie_id = "data_".$key."id:";
            $lianjie_num= "data_".$key."num:";
            $lianjie = $lianjie.$lianjie_id.$day_by_task[$key]['uid'].$lianjie_id.$lianjie_num.$day_by_task[$key]['task_num'].$lianjie_num;
        }
        
        //将进度数据存储到数据库
        $progres = $_REQUEST['lists'];
        for($i=0;$i<=count($progres)-1;$i++){
            if($i == count($progres)-1){
                $temp_progres = $temp_progres.$progres[$i];
            }else{
                $temp_progres = $temp_progres.$progres[$i].',';
            }
        }
        
        //将DL的总量存储到数据库
        $dl_total = $_REQUEST['dllists'];
        for($i=0;$i<=count($dl_total)-1;$i++){
            if($i == count($progres)-1){
                $temp_dltotal = $temp_dltotal.$dl_total[$i];
            }else{
                $temp_dltotal = $temp_dltotal.$dl_total[$i].',';
            }
        }
        
        $separatorId = "";
        $separatorTempId = "";
        foreach($org as $k=>$v){
            $separatorList = explode(",", $org[$k]['id']);
            for($i=0;$i<count($separatorList);$i++){
                if(in_array($separatorList[$i], $mb_list)){

                }else{
                   if($i == count($separatorList)-1){
                       $separatorId = $separatorId.$separatorList[$i];
                   }else{
                       $separatorId = $separatorId.$separatorList[$i].",";
                   }
                }
            }
        }
        if(!empty($separatorId)){
            $separatorData = "";
            $gatherresult = "";
            $separatorTemp = explode(",",$separatorId);
            for($i=0;$i<count($separatorTempId);$i++){
                if($i == count($separatorTempId)-1){
                    $separatorTempId = $separatorTempId.$separatorTemp[$i];
                }else{
                    $separatorTempId = $separatorTempId.$separatorTemp[$i].",";
                }
            }
             //将uid和num对应并连接成字符串便于存放到数据库create_task中
            foreach ($org as $key => $value) {
                $separator_id = "data_".$key."id:";
                $separator_num= "data_".$key."num:";
                $separatorData = $separatorData.$separator_id.$org[$key]['id'].$separator_id.$separator_num.$org[$key]['num'].$separator_num;
            }
            for($i=0;$i<count($mb_list);$i++){
                if($i == count($mb_list)-1){
                    $gatherresult = $gatherresult.$mb_list[$i];
                }else{
                    $gatherresult = $gatherresult.$mb_list[$i].",";
                }
            }
            $task_createmodel->where(array('id'=>array('eq',$taskid)))->data(array('task_menber'=>$gatherresult.",".$separatorTempId))->save();
        }
        /*
        dump("需要保存是数据");
        dump($lianjie);
        dump("在TasKsMember的新建");
        dump($create_new);
        dump("在tasksMember中删除");
        dump($delete_old);
        dump("在Tasksmember中更新");
        dump($update_old);
        exit;*/
        //将创建数组，更新数组，删除数组对tasks_member数据表操作
        //将连接的字符存储到create_task的某字段里面
        if(!empty($task_createmodel) && !empty($createmodel)){
            $data['id']=$taskid;
            $data['userlists_data']=$lianjie;
            $data['task_lists']=$temp_progres;
            $data['dllists']=$temp_dltotal;
            $save_note = $task_createmodel->save($data);
            if($save_note !== false){
                if(!empty($create_new)){
                    $create_note=$createmodel->addAll($create_new);
                }
                if(!empty($update_old)){
                    foreach($update_old as $keyu=>$valueu){
                        $update_where=array(
                            'uid'=>array('eq',$update_old[$keyu]['uid']),
                            'taskid'=>array('eq',$update_old[$keyu]['taskid']),
                            'detail_time'=>array('eq',$update_old[$keyu]['detail_time']),
                            'schedule_start'=>array('eq',$update_old[$keyu]['schedule_start']),
                            'schedule_end'=>array('eq',$update_old[$keyu]['schedule_end']),
                        );
                        $update_date['task_num']=$update_old[$keyu]['task_num'];
                        $update_note=$createmodel->where($update_where)->data($update_date)->save();//根据指定条件修改数据
                    }
                }
                if(!empty($delete_old)){
                    foreach($delete_old as $keyd=>$valued){
                        $delete_where=array(
                            'uid'=>array('eq',$delete_old[$keyd]['uid']),
                            'task_num'=>array('eq',$delete_old[$keyd]['task_num']),
                            'taskid'=>array('eq',$delete_old[$keyd]['taskid']),
                            'detail_time'=>array('eq',$delete_old[$keyd]['detail_time']),
                            'schedule_start'=>array('eq',$delete_old[$keyd]['schedule_start']),
                            'schedule_end'=>array('eq',$delete_old[$keyd]['schedule_end']),
                        );
                        $delete_note=$createmodel->where($delete_where)->delete();
                    }
                }
            if(false !== $create_note && false !== $update_note  && false !== $delete_note){
                $this->saveLog(1, $create_note.$update_note.$delete_note);
                $this->success('添加,更新，删除指定的数据成功！' . $msg);
            }else{
                $this->saveLog(0, $create_note.$update_note.$delete_note);
                $this->success('添加,更新，删除指定的数据失败！' . $msg);
            }

            }
        }else{
            $this->saveLog(0, $create_note.$update_note.$delete_note);
            $this->success('数据库连接失败！' . $msg);
        }
        
        
    }
    /*
     * 用于及时修改的Ajax回调
     */
    public function aresUpdate(){
        $msg = "";
        $tid=$_REQUEST['tid'];
        $eidt_num=$_REQUEST['editv'];
        $task_edit_model=M('TasksMember');
        $data_edit['task_num']=$eidt_num;
        $edit_note = $task_edit_model->where(array('id'=>array('eq',$tid)))->data($data_edit)->save();
        if(!empty($task_edit_model)){
            if(false != $edit_note){
                $this->saveLog(1, $edit_note);
                $this->success('数据保存成功！' . $msg);
            }else{
                $this->saveLog(0, $edit_note);
                $this->success('数据保存失败！操作有问题' . $msg);
            }
        }else{
            $this->saveLog(0, $edit_note);
            $this->success('数据库连接失败！' . $msg);
        }
        /*
        $uid = $_REQUEST["uid"];
        $tid = $_REQUEST["tid"];
        $d_t = $_REQUEST["d_t"];
        $s_t = $_REQUEST['s_t'];
        $e_t = $_REQUEST['e_t'];
        $updat = $_REQUEST["updat"];
        $status = $_REQUEST['status'];
        $where = array(
                'taskid'=>array('eq',$tid),
                'uid'=>array('eq',$uid),
                'detail_time'=>array('eq',$d_t),
                'schedule_start'=>array('eq',$s_t),
                'schedule_end'=>array('eq',$e_t),
            );
        $save['task_num']=$updat;
        $data['taskid']=$tid;
        $data['uid']=$uid;
        $data['detail_time']=$d_t;
        $data['schedule_start']=$s_t;
        $data['schedule_end']=$e_t;
        $data['task_num']=$updat;
        $model = M('TasksMember');
        if($status !== "1"){
            $model->add($data);
            $state = true;
        }else{
            $model->where($where)->save($save);
            $state = true;
        }
        dump($status);*/
    }
    
    /*
     * OrgLookup 
     * 功能：在新增Task的功能中添加Member功能
     * 时间：2013-11-30 15:30:00
     */
    public function OrgLookup(){
        $TaskId = $_REQUEST['id'];
        $TaskMember =$_REQUEST['Member'];//选中的Task的Member信息
        
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
        foreach($UserGroupTeam as $k=>$v){
            $UserGroupTeamId[$k] =  $UserGroupTeam[$k]['id'];
        }
        
        
        if(!empty($_REQUEST['orgName'])){
            $ShowMemberWhere['account'] = array('like',"%".$_REQUEST['orgName']."%");
        }
        if(!empty($_REQUEST['support'])){
            $supportTeam = $this->AllGroupArray($_REQUEST['support']);
            foreach($supportTeam as $k=>$v){
                $supportTeam[$k] = $supportTeam[$k]['id'];
            }
            $UserGroupTeamId = array_merge($UserGroupTeamId,$supportTeam);
            
        }
        $ShowMemberWhere['user_group_id']=array('in',$UserGroupTeamId);
        $ShowMemberWhere['status']=array('gt','0');
        $usermodel = M("User");
        $supportModel=M("UserGroup");        
        $support=$supportModel->where(array('level'=>array('egt',4)))->select();
        $this->assign("supportlist",$support);
        //查询数据
        $voList = $usermodel->field(array('id'=>'uid','account'=>'uname','job_number'=>'unumber','email'=>'uemail','status'=>'status'))->where($ShowMemberWhere)->order('account asc')->select();
        //echo $usermodel->getLastSql();
                
        $this->assign('list',$voList);
        $this->assign('TaskMember',$TaskMember);
        if(empty($TaskMember)){
            $TaskMemberName = "未添加";
        }else{
            $TaskMemberList = explode(",", $TaskMember);
            for($i=0;$i<count($TaskMemberList);$i++){
                $Temp_TaskMemberName = getUserName_en($TaskMemberList[$i]);
                if($i == (count($TaskMemberList)-1)){
                    $TaskMemberName = $TaskMemberName.$Temp_TaskMemberName;
                }else{
                    $TaskMemberName = $TaskMemberName.$Temp_TaskMemberName.",";
                }
            }
        }
        $this->assign('TaskMemberName',$TaskMemberName);
        $this->display();
    }
    public function supportTeam(){
        $supportModel=M("UserGroup");        
        $support=$supportModel->field("id,nickname,create_time,status")->where(array('level'=>array('egt',4)))->select();
        $this->assign("supportlist",$support);
        $this->display();
    }
    public function DeleteMember(){
        $ids = $_REQUEST['ids'];
        $idslist = explode(",",$ids);
        dump($idslist);
        exit;
    }
}
?>
