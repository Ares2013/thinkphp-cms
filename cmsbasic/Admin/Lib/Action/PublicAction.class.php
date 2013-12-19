<?php
/**
 +------------------------------------------------------------------------------
 * 后台动作默认公共类
 +------------------------------------------------------------------------------
 * @category   Action
 * @package  Lib
 * @subpackage  Action
 * @author   Ares Peng <Z13053003@wistron.com><1534157801@qq.com>
 * @version  $Id: PublicAction.class.php 2013/10/1  Ares Peng
 +------------------------------------------------------------------------------
 */
class PublicAction extends Action {
     /**
      +----------------------------------------------------------
     * Ajax方式返回数据到客户端
      +----------------------------------------------------------
     * @access protected
      +----------------------------------------------------------
     * @param mixed $data 要返回的数据
     * @param String $info 提示信息
     * @param boolean $status 返回状态
     * @param String $status ajax返回类型 JSON XML
      +----------------------------------------------------------
     * @return void
      +----------------------------------------------------------
     */
    protected function ajaxReturn($data,$type = '') {
      
        
        if(func_num_args()>2) {// 兼容3.0之前用法
            $args           =   func_get_args();
            array_shift($args);
            $info           =   array();
            
            $info['data']   =   $data;
            $info['info']   =   array_shift($args);
            $info['status'] =   array_shift($args);
            $data           =   $info;
            $type           =   $args?array_shift($args):'';
        }
        $data['statusCode'] = $data['status'];
        $data['navTabId'] = $_REQUEST['navTabId'];
        $data['rel'] = $_REQUEST['rel'];
        $data['callbackType'] = $_REQUEST['callbackType'];
        $data['forwardUrl'] = $_REQUEST['forwardUrl'];
        $data['confirmMsg'] = $_REQUEST['confirmMsg'];
        $data['message'] = $data['info'];
        if(empty($type)) $type  =   C('DEFAULT_AJAX_RETURN');
        switch (strtoupper($type)){
            case 'JSON' :
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode($data));
            case 'XML'  :
                // 返回xml格式数据
                header('Content-Type:text/xml; charset=utf-8');
                exit(xml_encode($data));
            case 'JSONP':
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                $handler  =   isset($_GET[C('VAR_JSONP_HANDLER')]) ? $_GET[C('VAR_JSONP_HANDLER')] : C('DEFAULT_JSONP_HANDLER');
                exit($handler.'('.json_encode($data).');');  
            case 'EVAL' :
                // 返回可执行的js脚本
                header('Content-Type:text/html; charset=utf-8');
                exit($data);            
            default     :
                // 用于扩展其他返回格式数据
                tag('ajax_return',$data);
        }
    }

	// 检查用户是否登录

	protected function checkUser() {
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			$this->assign('jumpUrl','Public/login');
			$this->ajaxReturn(true, "", 301);
		}
	}

	// 顶部页面
	public function top() {
		C('SHOW_RUN_TIME',false);			// 运行时间显示
		C('SHOW_PAGE_TRACE',false);
		$model	=	M("Group");
		$list	=	$model->where('status=1')->getField('id,title');
		$this->assign('nodeGroupList',$list);
		$this->display();
	}
	// 尾部页面
	public function footer() {
		C('SHOW_RUN_TIME',false);			// 运行时间显示
		C('SHOW_PAGE_TRACE',false);
		$this->display();
	}
	// 菜单页面
	public function menu() {
                $this->checkUser();
		$id=$this->_get('id');
		$this->assign('menugroupid',$id);
		if(empty($id))
		{
			$id=0;
		}
                if(isset($_SESSION[C('USER_AUTH_KEY')])) {
			//显示分组
			$grouplist=array();
			$groupM=D("Group");
			$map=array(
				'status'=>1,
				'nav'=>$id
			);
			$order='type desc,sort asc';
			$grouplist=$groupM->where($map)->order($order)->select();
			/*
                        $artcM=D("ArticleCat");
			$artclist=$artcM->select();
			$this->assign('artclist',$artclist);
                         * 
                         */
                        $TeamModel = D("UserGroup");
                        $TeamList = $TeamModel->field("id,nickname,level")->where(array('level'=>3))->order(array('id'=>'asc'))->select();
                        $this->assign("teamlist",$TeamList);
            //显示菜单项
            $menu  = array();
            if(false){
                //如果已经缓存，直接读取缓存
                $menu   =   $_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]];
                    foreach($grouplist as $k=>$v){
                        foreach($menu as $i=>$j){
                            if($j['group_id']==$v['id']){
                                $grouplist[$k]['menu'][]=$menu[$i];
                                }
                        }
                    }
		$this->assign('grouplist',$grouplist);
            }else {
                
                //读取数据库模块列表生成菜单项
                $node =M("Node");
		$id=$node->getField("id");
		$where['level']=2;
		$where['status']=1;
		//$where['pid']=$id;
                $list=$node->where($where)->field('id,name,group_id,title,action_name')->order('sort asc')->select();
                $accessList = $_SESSION['_ACCESS_LIST'];
                foreach($list as $key=>$module) {
                     if(true){//isset($accessList[strtoupper(APP_NAME)][strtoupper($module['name'])]) || $_SESSION['administrator']) {
                        //设置模块访问权限
                        $module['access'] =   1;
                        $menu[$key]  = $module;
                    }
                }
		foreach($grouplist as $k=>$v){
                    foreach($menu as $i=>$j){
			if($j['group_id']==$v['id']){
                            $grouplist[$k]['menu'][]=$menu[$i];
			}
                    }
		}
		$this->assign('grouplist',$grouplist);
                //缓存菜单访问
                $_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]]	=$menu;
            }
                if(!empty($_GET['tag'])){
                    $this->assign('menuTag',$_GET['tag']);
                }
                $this->assign('menu',$menu);
	}
		C('SHOW_RUN_TIME',false);			// 运行时间显示
		C('SHOW_PAGE_TRACE',false);
		$this->display();
}
    // 后台首页 查看系统信息
    public function main() {
       /* $info = array(
            '操作系统'=>PHP_OS,
            '运行环境'=>$_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式'=>php_sapi_name(),
            'ThinkPHP版本'=>THINK_VERSION.' [ <a href="http://thinkphp.cn" target="_blank">查看最新版本</a> ]',
            '上传附件限制'=>ini_get('upload_max_filesize'),
            '执行时间限制'=>ini_get('max_execution_time').'秒',
            '服务器时间'=>date("Y年n月j日 H:i:s"),
            '北京时间'=>gmdate("Y年n月j日 H:i:s",time()+8*3600),
            '服务器域名/IP'=>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
            '剩余空间'=>round((@disk_free_space(".")/(1024*1024)),2).'M',
            'register_globals'=>get_cfg_var("register_globals")=="1" ? "ON" : "OFF",
            'magic_quotes_gpc'=>(1===get_magic_quotes_gpc())?'YES':'NO',
            'magic_quotes_runtime'=>(1===get_magic_quotes_runtime())?'YES':'NO',
            );*/
        $info = array(
            '用户ID:'=>$_SESSION['authId'],
            '中文名:'=>($_SESSION['loginUserName']!='')?$_SESSION["loginUserName"]:'未填写',
            '工号:'=>$_SESSION['job_number'],
            'E-mail'=>  (empty($_SESSION['email'])?'未填写':$_SESSION["email"]),
            '上次登录时间'=>date('Y-m-d H:i:s',$_SESSION["lastLoginTime"]),
        );
        $this->assign('info',$info);
        $this->display();
    }

	// 用户登录页面
	public function login() {
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
		
			$this->display();
			
		}else{
			$this->redirect('Index/index');
		}
	}

	public function index()
	{
		//如果通过认证跳转到首页
		redirect(__APP__);
	}

	// 用户登出
    public function logout()
    {
        if(isset($_SESSION[C('USER_AUTH_KEY')])) {
			unset($_SESSION[C('USER_AUTH_KEY')]);
			unset($_SESSION);
			session_destroy();
            $this->assign("jumpUrl",__URL__.'/login/');
            $this->success('登出成功！');
        }else {
			
            $this->assign("jumpUrl",__URL__.'/login/');
            $this->error('已经登出！');
        }
    }

	// 登录检测
	public function checkLogin() {
		if(empty($_POST['account'])) {
			$this->error('帐号错误！');
		}elseif (empty($_POST['password'])){
			$this->error('密码必须！');
		}elseif (empty($_POST['verify'])){
                    $this->error('验证码不能为空！');
		}
                //生成认证条件
                $map = array();
		// 支持使用绑定帐号登录
                if(is_numeric(substr($_POST['account'],1))){
                    $map['job_number'] = $_POST['account'];
                }else{
                    $map['account'] = $_POST['account'];
                }
		//$map['account']	= is_numeric(substr($_POST['account'],1))?$_POST['account']:$_POST['account'];
                $map["status"]	= array('gt',0);
		$verify=session('verify');
                
		if($verify != md5($_POST['verify'])){
			$this->error('验证码输入错误！');
		}
                
		//将用户信息取出
                //import ( '@.ORG.Util.RBAC' );
                import ( 'ORG.Util.RBAC' );
                $authInfo = RBAC::authenticate($map);
                
            //使用用户名、密码和状态的方式进行认证
            if(false === $authInfo) {
                $this->error('帐号不存在或已禁用！');
            }else {
            if($authInfo['password'] != md5($_POST['password'])) {
                $this->error('密码错误！');
            }
            //设置session值
            $_SESSION[C('USER_AUTH_KEY')]	=	$authInfo['id'];
            $_SESSION['email']	=	$authInfo['email'];
            $_SESSION['loginUserName']		=	(empty($authInfo['nickname'])?$authInfo['account']:$authInfo['nickname']);
            $_SESSION['lastLoginTime']		=	$authInfo['last_login_time'];
            $_SESSION['job_number']             =       $authInfo['job_number'];
            $_SESSION['login_count']	=	$authInfo['login_count'];
            $_SESSION['gid']            =       $authInfo['user_group_id'];
            //用最高权限登陆，即时access表中没有赋予权限也可以登陆
            if($authInfo['account']=='wistron_QT') {
            	$_SESSION['administrator']		=	true;
            }
            //保存登录信息
			$User = M('User');
			$ip = get_client_ip();
			$time =	time();
                        $data = array();
			$data['id']	=	$authInfo['id'];
			$data['last_login_time']	=	$time;
			$data['login_count']	=	array('exp','login_count+1');
			$data['last_login_ip']	=	$ip;
			$User->save($data);//更新对应id的用户信息

			// 缓存访问权限
                        RBAC::saveAccessList();
                        
			$this->success('登录成功！');
		}
	}
    // 更换密码
    public function changePwd()
    {
		$this->checkUser();
		$verify=session("verify");
        //对表单提交处理进行处理或者增加非表单数据
		if(md5($_POST['verify'])	!= $verify) {
			$this->error('验证码错误！');
		}
		$map	=	array();
        $map['password']= pwdHash($_POST['oldpassword']);
        if(isset($_POST['account'])) {
            $map['account']	 =	 $_POST['account'];
        }elseif(isset($_SESSION[C('USER_AUTH_KEY')])) {
            $map['id']		=	$_SESSION[C('USER_AUTH_KEY')];
        }
        //检查用户
        $User    =   M("User");
        if(!$User->where($map)->field('id')->find()) {
            $this->error('旧密码不符或者用户名错误！');
        }else {
			$User->password	=	pwdHash($_POST['password']);
			$User->save();
                        $otherSystemDataSaveModel=M('user_employee','ams_','mysql://root:@localhost/ms');
                        $otherSystemDataSaveData['Password'] = $_POST['password'];
                        $otherSystemDataSavejobnumber = $otherSystemDataSaveModel->where(array('Em_ID'=>array('eq',$_SESSION['job_number'])))->data($otherSystemDataSaveData)->save();
			$this->success('密码修改成功！');
         }
         
    }
	public function profile() {
		$this->checkUser();
		$User	 =	 M("User");
		$vo	=	$User->getById($_SESSION[C('USER_AUTH_KEY')]);
		$this->assign('vo',$vo);
		$this->display();
	}
	public function verify()
    {

		$type=isset($_GET['type'])?$_GET['type']:'gif';
                import("ORG.Util.Image");
                Image::buildImageVerify(4,1,$type);
    }
	// 修改资料
	public function change() {
		$this->checkUser();
		$User	 =	 D("User");
		if(!$User->create()) {
			$this->error($User->getError());
		}
		$result	=	$User->save();
		if(false !== $result) {
			$this->success('资料修改成功！');
		}else{
			$this->error('资料修改失败!');
		}
	}
	//xheditor上传
	public function xupload()
	{
		$inputName='filedata';//表单文件域name
		$attachDir=C("UPLOAD_URL");//上传文件保存路径，结尾不要带/
		$dirType=1;//1:按天存入目录 2:按月存入目录 3:按扩展名存目录  建议使用按天存
		$maxAttachSize=2097152;//最大上传大小，默认是2M
		$upExt='txt,rar,zip,jpg,jpeg,gif,png,swf,wmv,avi,wma,mp3,mid,doc,xls';//上传扩展名
		$msgType=2;//返回上传参数的格式：1，只返回url，2，返回参数数组
		$immediate=isset($_GET['immediate'])?$_GET['immediate']:0;//立即上传模式，仅为演示用
		ini_set('date.timezone','Asia/Shanghai');//时区

		$err = "";
		$msg = "''";
		$tempPath=$attachDir.'/'.date("YmdHis").mt_rand(10000,99999).'.tmp';
		$localName='';

		if(isset($_SERVER['HTTP_CONTENT_DISPOSITION'])&&preg_match('/attachment;\s+name="(.+?)";\s+filename="(.+?)"/i',$_SERVER['HTTP_CONTENT_DISPOSITION'],$info)){//HTML5上传
			file_put_contents($tempPath,file_get_contents("php://input"));
			$localName=urldecode($info[2]);
		}
		else{//标准表单式上传
			$upfile=@$_FILES[$inputName];
			if(!isset($upfile))$err='文件域的name错误';
			elseif(!empty($upfile['error'])){
				switch($upfile['error'])
				{
					case '1':
						$err = '文件大小超过了php.ini定义的upload_max_filesize值';
						break;
					case '2':
						$err = '文件大小超过了HTML定义的MAX_FILE_SIZE值';
						break;
					case '3':
						$err = '文件上传不完全';
						break;
					case '4':
						$err = '无文件上传';
						break;
					case '6':
						$err = '缺少临时文件夹';
						break;
					case '7':
						$err = '写文件失败';
						break;
					case '8':
						$err = '上传被其它扩展中断';
						break;
					case '999':
					default:
						$err = '无有效错误代码';
				}
			}
			elseif(empty($upfile['tmp_name']) || $upfile['tmp_name'] == 'none')$err = '无文件上传';
			else{
				move_uploaded_file($upfile['tmp_name'],$tempPath);
				$localName=$upfile['name'];
			}
		}
		if($err==''){
			$fileInfo=pathinfo($localName);
			$extension=$fileInfo['extension'];
			if(preg_match('/^('.str_replace(',','|',$upExt).')$/i',$extension))
			{
				$bytes=filesize($tempPath);
				if($bytes > $maxAttachSize)$err='请不要上传大小超过'.$this->formatBytes($maxAttachSize).'的文件';
				else
				{
					switch($dirType)
					{
						case 1: $attachSubDir = 'day_'.date('ymd'); break;
						case 2: $attachSubDir = 'month_'.date('ym'); break;
						case 3: $attachSubDir = 'ext_'.$extension; break;
					}
					$attachDir = $attachDir.'/'.$attachSubDir;
					$url=C('__UPLOAD__').$attachSubDir;
					if(!is_dir($attachDir))
					{
						@mkdir($attachDir, 0777);
						@fclose(fopen($attachDir.'/index.htm', 'w'));
					}
					PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
					$newFilename=date("YmdHis").mt_rand(1000,9999).'.'.$extension;
					$targetPath = $attachDir.'/'.$newFilename;
					$url=$url.'/'.$newFilename;
					rename($tempPath,$targetPath);
					@chmod($targetPath,0755);

					$targetPath=$this->jsonString($targetPath);
					if($immediate=='1')$targetPath='!'.$targetPath;
					if($msgType==1)$msg="'$targetPath'";
					else $msg="{'url':'".$url."','localname':'".$this->jsonString($localName)."','id':'1'}";//id参数固定不变，仅供演示，实际项目中可以是数据库ID
				}
			}
			else $err='上传文件扩展名必需为：'.$upExt;

			@unlink($tempPath);
		}

		echo "{'err':'".$this->jsonString($err)."','msg':".$msg."}";
	}
	private function jsonString($str)
	{
		return preg_replace("/([\\\\\/'])/",'\\\$1',$str);
	}
	private function formatBytes($bytes) {
		if($bytes >= 1073741824) {
			$bytes = round($bytes / 1073741824 * 100) / 100 . 'GB';
		} elseif($bytes >= 1048576) {
			$bytes = round($bytes / 1048576 * 100) / 100 . 'MB';
		} elseif($bytes >= 1024) {
			$bytes = round($bytes / 1024 * 100) / 100 . 'KB';
		} else {
			$bytes = $bytes . 'Bytes';
		}
		return $bytes;
	}
}
?>