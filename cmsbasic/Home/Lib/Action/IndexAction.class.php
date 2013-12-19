<?php
/**
 +------------------------------------------------------------------------------
 * 前台动作默认动作类
 +------------------------------------------------------------------------------
 * @category   Action
 * @package  Lib
 * @subpackage  Action
 * @author   Ares Peng<1534157801@qq.com>
 * @version  $Id: IndexAction.class.php Ares Peng<1534157801@qq.com>
 +------------------------------------------------------------------------------
 */
class IndexAction extends CommonAction{
    /**
	+----------------------------------------------------------
	* 首页处理
	* 
	+----------------------------------------------------------
	* @access public
	+----------------------------------------------------------
	* 
	* 
	+----------------------------------------------------------
	* @return max
	+----------------------------------------------------------
	*/
	public function index(){
		
           
                $artlist=array();
		$artM=D("Article");
		$arth=$artM->where(array('cat_id'=>2))->find();
		$artw=$artM->where(array('cat_id'=>3))->find();
		$arto=$artM->where(array('cat_id'=>array('not in',"2,3")))->find();
		$artlist[]=$arth;
                $artlist[]=$artw;
		$artlist[]=$arto;
		$this->assign("artlist",$artlist);
		
                //$this->success('we will jump into management page.',__ROOT__.'/Admin/index.php/Public/login');
                $demomodel = M('user_employee','ams_','mysql://root:@localhost/ms');
                $demolist = $demomodel->field('Index,Status')->select();
                $demolist2 = $demomodel->select();//->field("Em_Name,Em_Name_En,Entryday,Leave_Day")
                
                dump($demolist2);//hiredate受雇时间 departure 辞职时间
                //dump(date("Y-m-d", strtotime($demolist2['0']['Entryday'])));
                //dump($this->operationArray("WZS/QT"));
                foreach($demolist2 as $k=>$v){
                    $createArray[$k]['job_number'] = $demolist2[$k]['Em_ID'];
                    $createArray[$k]['account'] = $demolist2[$k]['Em_Name_En'];
                    $createArray[$k]['nickname'] = $demolist2[$k]['Em_Name'];
                    $createArray[$k]['email'] = $demolist2[$k]['Email'];
                    $createArray[$k]['password'] = md5($demolist2[$k]['Password']);
                    $createArray[$k]['create_time'] = time();
                    $createArray[$k]['update_time'] = time();
                    if($demolist2[$k]['Sex'] == "M"){
                        $createArray[$k]['sex'] = 1;
                    }elseif($demolist2[$k]['Sex'] == "F"){
                        $createArray[$k]['sex'] = 0;
                    }
                    if($demolist2[$k]['Status'] == "On"){
                        $createArray[$k]['status'] = 1;
                    }elseif($demolist2[$k]['Status'] == "Off"){
                        $createArray[$k]['status'] = 0;
                    }else{
                        $createArray[$k]['status'] = 0;
                    }
                    $createArray[$k]['user_group_id'] = $this->operationArray($demolist2[$k]['Team_Name']);
            }
//        dump($createArray);
//         dump($createArray['262']);
//         dump($createArray['285']);
//         dump($createArray['286']);
//         exit;
//            $UserGroupModel = M('user','lhl_','mysql://root:@localhost/cmsbasic');
//            $note = $UserGroupModel->data($createArray['262'])->add();
//                if($note !== false){
//                    dump('数据全部导入成功'.$j."条数据");
//                }else{
//                    dump('数据全部导入失败,在'.$j.'没有成功');
//                }
            /*
            for($j=0;$j<=count($createArray)-1;$j++){
                $note = $UserGroupModel->data($createArray[$j])->add();
                if($note !== false){
                    dump('数据全部导入成功'.$j."条数据");
                }else{
                    dump('数据全部导入失败,在'.$j.'没有成功');
                }
            }*/
            //$note = $UserGroupModel->addAll($SecondArray);
            
            
            //临时用于处理数据库
           //2013.11.28 负责处理员工属性
            $userModel = M("User");
            $userWhere =array('27','30','32','34','35');
            $userDataList = $userModel->where(array('user_group_id'=>array('in',$userWhere)))->select();
            dump($userDataList);
            
                 
    }
    public function operationArray($groupName){
        switch ($groupName) {
            //Annie Group
            case "Annie BIOS":
                return $groupName=23;
                break;
            case "Annie A/V":
                //return $groupName ="AV";
                return $groupName =24;
                break;
            case "Annie Tech":
                //return $groupName ="Annie_Tech";
                return $groupName =35;
                break;
            case "Annie I/O":
                //return $groupName ="IO";
                return $groupName =25;
                break;
            case "Annie TB":
                //return $groupName ="Bundle";
                return $groupName =37;
                break;
            case "Preload":
                //return $groupName ="Preload";
                return $groupName =33;
                break;
            case "Preload Te":
                //return $groupName ="Preload_Tech";
                return $groupName =34;
                break;
            //Astro Group
            case "Astro":
                //return $groupName ="Astro";
                return $groupName =26;
                break;
            case "Astro Tech":
                //return $groupName ="Astro_Tech";
                return $groupName =27;
                break;
           //Yama Group
           case "Yama":
                //return $groupName ="Yama";
               return $groupName =28;
                break;
           case "Yama Tech":
                //return $groupName ="Yama_Tech";
               return $groupName =28;
                break;
           //Lily Group
            case "Lily Tech":
                //return $groupName ="Lily_Tech";
                return $groupName =30;
                break;
           case "Lily":
                //return $groupName ="Lily";
               return $groupName =29;
                break;
           //Rosa Group
            case "Rosa":
                //return $groupName ="Rosa";
                return $groupName =31;
                break;
            case "Rosa Tech":
                //return $groupName ="Rosa_Tech";
                return $groupName =32;
                break;
            default:
                //return $groupName ="1STZ00";
                return $groupName =15;
                break;
        }
    }
}