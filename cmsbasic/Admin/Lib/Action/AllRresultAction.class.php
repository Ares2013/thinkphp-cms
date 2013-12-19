<?php
/**
 +------------------------------------------------------------------------------
 * check project all the team results
 +------------------------------------------------------------------------------
 * @category   Action
 * @package  Lib
 * @subpackage  Action
 * @author   Ares Peng <Z13053003@wistron.local><1534157801@qq.com>
 * @version  $Id: AllRresultAction.class.php 2013/10/1  Ares Peng
 +------------------------------------------------------------------------------
 */

class AllRresultAction extends CommonAction{
    public function ajaxRegion(){
      
    }
    //调用index之前
    public function _before_index() {
	$group=M('UserGroup');
        $grouplist=$group->field(array('id'=>'gid','nickname'=>'name','member'=>'mlist'))->where(array('status'=>array('neq','0')))->select();
        $this->assign('glist',$grouplist);
        $this->assign('public',__PUBLIC__);        
    }
    //调用add之前
    public function _before_add() {
       
    }
    //调用eidt之前
    public function _before_edit() {
       
    }
    public function _filter(&$map){
	$map['status']=array('neq','0');	 
	//$map['account'] = array('like',"%".$_POST['account']."%");
    }
    public function index(){
        
        $this->display();
    }
    public function summarize(){
        $teamid = $_REQUEST['teamid'];
        $SeachdateStart = $_REQUEST['dateStart'];
        $SeachdateEnd = $_REQUEST['dateEnd'];
        $Seachstatus = $_REQUEST['status'];
        
        $teamids = $this->AllGroupArray($teamid);
        foreach($teamids as $k=>$v){
            $teamidlist[$k] = $teamids[$k]['id'];
        }
        
        $getMonthRange = $this->getMonthRange(time());//本月开始和结束
        if(empty($SeachdateStart)){
            $MonthStartDate = $getMonthRange['sdate_unix'];
        }else{
            $MonthStartDate = strtotime($SeachdateStart);
        }
        if(empty($SeachdateEnd)){
            $MonthEndDate = $getMonthRange['edate_unix'];
        }else{
            $MonthEndDate = strtotime($SeachdateEnd)+86399;
        }
        if(!empty($Seachstatus)){
            //task状态标示1 Ongoing 0 Finished 2 Death
            $seletcreatetaskwhere2['status']=$Seachstatus;
        }
        $MonthTotal = round(($MonthEndDate - $MonthStartDate)/86400);
        for($i=0;$i<$MonthTotal;$i++){
            $tem = ($MonthStartDate+(86400*$i));
            $monthlyRecord[$i]['unix'] = $tem;
            $monthlyRecord[$i]['date'] = date("m/d",$tem);
        }
        
        $weekly = $this->get_weekinfo(date("Y-m", time()));
        
        
        $CreateTaskModel = M("CreateTask");
        $CreateProjectModel = M("CreateArticle");
        $UserTeamModel = M("user");
        $UserTotal = $UserTeamModel->where(array("user_group_id"=>array('in',$teamidlist),"status"=>'1'))->count("id");
        
        $project_id = $CreateProjectModel->field("id,title,p_cw")->where(array('teamid'=>array('in',$teamidlist)))->select();
        foreach($project_id as $k=>$v){
            $project_ids[$k]= $project_id[$k]['id'];
        }
        $seletcreatetaskwhere1 =array(
            'schedule_start'=>array('between',array($MonthStartDate,$MonthEndDate)),
            'schedule_end'=>array('between',array($MonthStartDate,$MonthEndDate)),
            '_logic'=>'OR'
            );
        $seletcreatetaskwhere2 = array(
            'project_id'=>array('in',$project_ids),
        );
        $seletcreatetaskwhere2['_complex'] =$seletcreatetaskwhere1;
        $CreateTask = $CreateTaskModel->field("id,project_id,task_type,task_title,task_menber,task_leader,task_lists,userlists_data,dllists,est_time,ratios,schedule_start,schedule_end,status")->where($seletcreatetaskwhere2)->select();
        //echo $CreateTaskModel->getLastSql();
        
        foreach($CreateTask as $k=>$v){
            $projectId[$k] = $CreateTask[$k]['project_id'];
            $TasksId[$k] = $CreateTask[$k]['id'];
            $AtteandTime = $CreateTask[$k]['est_time'];
            $TaskDetails[$k]['sort']=$k;
            $TaskDetails[$k]['taskid']=$CreateTask[$k]['id'];
            $TaskDetails[$k]['project_id']=$CreateTask[$k]['project_id'];
            $TaskDetails[$k]['task_title']=$CreateTask[$k]['task_title'];
            $TaskDetails[$k]['schedule_start']=$CreateTask[$k]['schedule_start'];
            $TaskDetails[$k]['schedule_end']=$CreateTask[$k]['schedule_end'];
            $TaskDetails[$k]['task_leader']=$CreateTask[$k]['task_leader'];
            $TaskDetails[$k]['est_time']=$AtteandTime;
            $TaskDetails[$k]['ratios']=$CreateTask[$k]['ratios'];
            //$TaskDetails[$k]['member']=$CreateTask[$k]['task_menber'];
            //$TaskDetails[$k]['DLmember']=$CreateTask[$k]['dllists'];
            //$TaskDetails[$k]['task_lists']=$CreateTask[$k]['task_lists'];
            $workday = $this->getWorkingDay($CreateTask[$k]['schedule_start'], $CreateTask[$k]['schedule_end'], true);
            $workday_saturday = $this->getWorkingDay($CreateTask[$k]['schedule_start'], $CreateTask[$k]['schedule_end'], false);
            $TaskDetails[$k]['workday']=  $workday;
            $TaskDetails[$k]['workday_saturday']= $workday_saturday;
            $task_menber_list = explode(",", $CreateTask[$k]['task_menber']);
            $DLmember_list = explode(",", $CreateTask[$k]['dllists']);
            $task_menber_listindex = 0;
            $DLmember_listindex = 0;
            $DLmember_listTotal = 0;
            foreach($task_menber_list as $key=>$value){
                if(!empty($value)){
                    $task_menber_lists[$task_menber_listindex] = $value;
                    $task_menber_listindex = $task_menber_listindex + 1;
                }
            }
            $task_menber_total = count($task_menber_lists);
            foreach($DLmember_list as $key=>$value){
                if(!empty($value)){
                    $DLmember_lists[$DLmember_listindex] = $value;
                    $DLmember_listTotal = $DLmember_listTotal + $value;
                    $DLmember_listindex = $DLmember_listindex + 1;
                }
            }
            
            $Attend = (1 - round(($AtteandTime/($DLmember_listTotal*$workday*9.2+$task_menber_total*6.5*$workday)),2));
            $NoAttend = (1 - round(($AtteandTime/($DLmember_listTotal*$workday_saturday*9.2+$task_menber_total*6.5*$workday_saturday)),2));
            $TaskDetails[$k]['Attend']=$Attend;
            $TaskDetails[$k]['NoAttend']=$NoAttend;
            
            if(empty($CreateTask[$k]['task_lists'])){
                $progress = 0;
            }else{
                $task_list= explode(",", $CreateTask[$k]['task_lists']);
                $progressindex = 0;
                foreach ($task_list as $key => $value) {
                    if(!empty($value)){
                        $task_lists[$progressindex] = $value;
                        $progressindex = $progressindex + 1;
                    }
                }
                $count = count($task_lists)-1;
                //$progress = $task_lists[$count];
                if(empty($task_lists[$count])){
                    $progress = 0;
                }else{
                    $progress = $task_lists[$count];
                }
            }
            $TaskDetails[$k]['Last_Progress']=$progress;
            $TaskDetails[$k]['details']=$this->getTaskDetails($CreateTask[$k],$MonthStartDate,$MonthEndDate);
        }
        $projectIds = array_unique($projectId);//去掉重复值,本月所有project
        $TasksIds = array_unique($TasksId);//去掉重复值，本月所有的Task
        
        foreach($monthlyRecord as $k=>$v){
            $IDLTaskTotal = 0;
            $DLTaskTotal = 0;
            $MaxIDLManpower = 0;
            $dayunix = $monthlyRecord[$k]['unix'];
            foreach($TaskDetails as $key=>$value){
                $TaskMemberArray = $TaskDetails[$key]['details'];
                foreach($TaskMemberArray as $keys=>$values){
                    if($dayunix == $TaskMemberArray[$keys]['unix']){
                        if(empty($TaskMemberArray[$keys]['DLTaskByday'])){
                            $TaskMemberArray[$keys]['DLTaskByday'] = 0;
                        }
                        if(empty($TaskMemberArray[$keys]['uidarray'])){
                            $IDLTaskTotal = $IDLTaskTotal + 0;
                        }else{
                           $IDLTaskTotal = $IDLTaskTotal +count($TaskMemberArray[$keys]['uidarray']);
                        }
                        $DLTaskTotal = $DLTaskTotal +$TaskMemberArray[$keys]['DLTaskByday'];
                        $MaxIDLManpower = $MaxIDLManpower + $TaskMemberArray[$keys]['IDLrecommend'];
                    }
                }
            }
            $monthlyRecord[$k]['MaxIDLManpower'] = $MaxIDLManpower;
            $monthlyRecord[$k]['IDLTaskByday'] = $IDLTaskTotal;
            $monthlyRecord[$k]['DLTaskByday'] = $DLTaskTotal;//
            $monthlyRecord[$k]['IDLrecommend'] = $UserTotal;
            $monthlyRecord[$k]['Manpower'] = $UserTotal;
            $monthlyRecord[$k]['Gap'] = $UserTotal-($IDLTaskTotal +$DLTaskTotal);
        }
        $projectlistIndex = 0 ;
        foreach($project_id as $k=>$v){
            if(in_array($project_id[$k]['id'], $projectIds)){
                $projectlist[$projectlistIndex]['id'] = $project_id[$k]['id'];
                $projectlist[$projectlistIndex]['title'] = $project_id[$k]['title'];
                $projectlist[$projectlistIndex]['p_cw'] = $project_id[$k]['p_cw'];
                
                
                $projectlistTeamindex = 0 ;
                foreach($TaskDetails as $key=>$value){
                    if($project_id[$k]['id'] == $TaskDetails[$key]['project_id']){
                        $projectlist[$projectlistIndex]['Tasks'][$projectlistTeamindex] = $TaskDetails[$key];
                        $projectlist[$projectlistIndex]['Tasks'][$projectlistTeamindex]['sort'] = $projectlistTeamindex;
                        $projectlistTeamindex = $projectlistTeamindex + 1;
                    }
                }
                $projectlistIndex = $projectlistIndex + 1;
            }
        }
        foreach($projectlist as $k=>$v){
            $rowspanTotal = count($projectlist[$k]['Tasks']);
            $projectlist[$k]['TasksTotal'] = $rowspanTotal;
        }
        //dump($projectlist);
        $this->assign("monthlyRecord",$monthlyRecord);
        $this->assign("teamid",$teamid);
        $this->assign("projectlist",$projectlist);
        $this->assign("TaskDetails",$TaskDetails);
        $this->display();
    }
    private function ProcedureOperators(){
        
    }
    public function getTaskDetails($Taskarray,$removetime = "",$removetime2=""){
        $TaskTotal = ($Taskarray['schedule_end'] - $Taskarray['schedule_start'])/86400;//从0开始  总数是$TaskTotal+1
        $est = $Taskarray['est_time'];
        $ratios = $Taskarray['ratios'];
        $TaskDLByday = explode(',',$Taskarray["dllists"]);
        $getMonthRange = $this->getMonthRange(time());
        if(empty($removetime2)){
            $removetime2 = $getMonthRange['edate_unix'];
        }
        if(empty($removetime)){
            $removetime2 = $getMonthRange['sdate_unix'];
        }
        $removetimeIndex = 0;
        $workdayindex = 0;   
        $noworkdayindex = 0;
                for($i=0;$i<=$TaskTotal;$i++){
                    $tem_unix = ($Taskarray['schedule_start']+(86400*$i));
                    $TaskDLBydays = $TaskDLByday[$i];
                    $tem_a=getdate($tem_unix);
                    if($tem_a['wday'] != '6' && $tem_a['wday'] != '0'){
                        $workdayindex=$workdayindex+1;//求出工作天数
                    }else{
                        $noworkdayindex = $noworkdayindex +1;//放假的天数
                    }
                if($tem_unix >= $removetime && $tem_unix<=$removetime2){
                    $temp_cha_id="data_".$i."id:";
                    $temp_cha_num="data_".$i."num:";
                    $temp_cha_ids=explode($temp_cha_id, $Taskarray['userlists_data']);
                    $temp_cha_nums=explode($temp_cha_num, $Taskarray['userlists_data']);

                    $TaskBydays[$removetimeIndex]['uid'] = $temp_cha_ids['1'];
                    $TaskBydays[$removetimeIndex]['task_num'] = $temp_cha_nums['1'];
                    $TaskBydays[$removetimeIndex]['unix'] = $tem_unix;
                    if(empty($TaskDLBydays)){
                       $TaskBydays[$removetimeIndex]['DLTaskByday'] = 0; 
                    }else{
                       $TaskBydays[$removetimeIndex]['DLTaskByday'] = $TaskDLBydays; 
                    }
                    
                    $TaskBydays[$removetimeIndex]['date'] = date("Y/m/d",$tem_unix);
                    $removetimeIndex = $removetimeIndex + 1;
                }
              }
            $recomment=  round(((round($est,1))/($ratios*0.01))/round($workdayindex*6.5,1));
        foreach($TaskBydays as $key=>$value){
            $memberbyid = explode(",", $TaskBydays[$key]['uid']);
            $memberbynum = explode(",", $TaskBydays[$key]['task_num']);
            foreach($memberbyid as $k=>$v){
                if(!empty($v)){
                    $memberbyids[$k] = $v;
                    $memberbynums[$k] = $memberbynum[$k];
                    $members[$k]['uid'] = $v;
                    $members[$k]['num'] = $memberbynum[$k];
                }
            }
            $TaskBydays[$key]['uidarray'] = $members;
            $TaskBydays[$key]['IDLrecommend'] = $recomment;
        }
        return $TaskBydays;
    }
    public function getWorkingDay($Start,$End,$include=true){
        $workdayTotal = 0;
        $holiday = 0;
        if($include){
            $total = ($End - $Start)/86400;
            if(is_float($total)){
                $total = round($total);
            }
            for($i=0; $i<=$total;$i++){
              $tem = ($Start+(86400*$i)); 
              $tem_a=getdate($tem);
              if($tem_a['wday'] != '6' && $tem_a['wday'] != '0'){
                $workdayTotal = $workdayTotal + 1;//求出工作天数
              }else{
                    $holiday = $holiday +1;
              }
            }
        }else{
            $total = ($End - $Start)/86400;
            if(is_float($total)){
                $total = round($total);
            }
            for($i=0; $i<=$total;$i++){
              $tem = ($Start+(86400*$i)); 
              $tem_a=getdate($tem);
              if($tem_a['wday'] != '0'){
                $workdayTotal = $workdayTotal + 1;
              }else{
                $holiday = $holiday + 0;
              }
            }
        }
        return $workdayTotal;
    }
    /*
     * 对task的结果分析
     * 2013.12.12 备份
     */
    public function summarize_ares(){
        $getMonthRange = $this->getMonthRange(time());//$getMonthRange['sdate_unix'] $getMonthRange['edate_unix']
        $weekly = $this->get_weekinfo(date("Y-m", time()));
        $CreateTaskModel = M("CreateTask");
        $CreateTask = $CreateTaskModel->where(array('schedule_start'=>array('between',array($getMonthRange['sdate_unix'],$getMonthRange['edate_unix'])),'schedule_end'=>array('between',array($getMonthRange['sdate_unix'],$getMonthRange['edate_unix'])),'_logic'=>'OR'))->select();
        //echo $CreateTaskModel->getLastSql();
        foreach($CreateTask as $k=>$v){
            $projectId[$k] = $CreateTask[$k]['project_id'];
            $TasksId[$k] = $CreateTask[$k]['id'];
        }
        $projectIds = array_unique($projectId);//去掉重复值,本月所有project
        $TasksIds = array_unique($TasksId);//去掉重复值，本月所有的Task
        
        $this->display();
    }
    public function analyze(){
        //dump($_REQUEST);
        $this->display();
    }
    //人力分析
    public function Manpower(){
        dump($_REQUEST);
    }
    
  }
?>
