<?php
/**
 +------------------------------------------------------------------------------
 * 后台首页操作类
 +------------------------------------------------------------------------------
 * @category   Action
 * @package  Lib
 * @subpackage  Action
 * @author   668FreeCloud <chinasoftstar@qq.com>
 * @version  $Id: IndexAction.class.php 2791 2013/7/28 16:45  AresPeng $
 +------------------------------------------------------------------------------
 */
class IndexAction extends CommonAction {
	
	// 框架首页
	public function index() {
		if (isset ( $_SESSION [C ( 'USER_AUTH_KEY' )] )) {
			
			//显示分组
			$grouplist=array();
			$groupM=D("Group");//实例化模型
			$navlist=$groupM->getNav();//array('0'=>'系统管理','1'=>'会员管理','2'=>'内容管理','3'=>'商务管理','4'=>'其他扩展');
			foreach($navlist as $k=>$v)
			{
				
                                $count=$groupM->where(array('nav'=>$k))->count();
                                if($count==0)
				{
					unset($navlist[$k]);
				}
			}
			$this->assign('navlist',$navlist);
			$map=array(
				'status'=>1,
				'nav'=>0
			);
			$order='type desc,sort asc';
			$grouplist=$groupM->where($map)->order($order)->select(); 
			//显示菜单项
			$menu = array ();			
			//读取数据库模块列表生成菜单项
			$node = M ( "Node" );
			$id = $node->getField ( "id" );
			$where ['level'] = 2;
			$where ['status'] = 1;
			//$where ['pid'] = $id;
			$list = $node->where ( $where )->field ( 'id,name,group_id,title' )->order ( 'sort asc' )->select ();
                        
			$accessList = $_SESSION ['_ACCESS_LIST'];
			foreach ( $list as $key => $module ) {
				if (true){//isset ( $accessList [strtoupper ( APP_NAME )] [strtoupper ( $module ['name'] )] ) || $_SESSION ['administrator']) {
					//设置模块访问权限
					$module ['access'] = 1;
					$menu [$key] = $module;
				}
			}
			foreach($grouplist as $k=>$v)
			{
				foreach($menu as $i=>$j)
				{
					if($j['group_id']==$v['id'])
					{
						$grouplist[$k]['menu'][]=$menu[$i];
					}
				}
			}
			$this->assign('grouplist',$grouplist);
			if (! empty ( $_GET ['tag'] )) {
				$this->assign ( 'menuTag', $_GET ['tag'] );
			}
			$this->assign ( 'menu', $menu );
		}
		C ( 'SHOW_RUN_TIME', false ); // 运行时间显示
		C ( 'SHOW_PAGE_TRACE', false );

		//系统信息
		$security_info=array();
		if(is_dir(ROOT_PATH."/Install")){
			$security_info[]="强烈建议删除安装文件夹,点击<a target='ajaxTodo' href='".U('System/removeInstall',array('navTabId'=>'System'))."'>【删除】</a>";
		}
		if(APP_DEBUG==true){
			$security_info[]="强烈建议您网站上线后，建议关闭 DEBUG （前台错误提示）";
		}	
		if(count($security_info)<=0){
			$this->assign('no_security_info',0);
		}
		else{
			$this->assign('no_security_info',1);
		}	
		$this->assign('security_info',$security_info);
                $disk_space = @disk_free_space(".")/pow(1024,2);
		$server_info = array(
		    '程序版本'=>'1.0 [<a href="mailto:Ares Peng@Wistron.com" target="_blank">查看最新版本</a>]',		
            '操作系统'=>PHP_OS,
            '运行环境'=>$_SERVER["SERVER_SOFTWARE"],	
            '上传附件限制'=>ini_get('upload_max_filesize'),
            '执行时间限制'=>ini_get('max_execution_time').'秒',		
            '服务器域名/IP'=>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
            '剩余空间'=>round($disk_space < 1024 ? $disk_space:$disk_space/1024 ,2).($disk_space<1024?'M':'G'),
		);
                $this->assign('set',$this->setting);
		$this->assign('server_info',$server_info);	
                $this->assign('Year',date('Y'));
                $this->assign('loginUserName',$_SESSION['loginUserName']);
                $this->display ();
	}

}
?>