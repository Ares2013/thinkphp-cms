<?php

class CommonAction extends Action {

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
        /*$result = array();
        $result['status'] = $status;
        $result['statusCode'] = $status;
        $result['navTabId'] = $_REQUEST['navTabId'];
        $result['rel'] = $_REQUEST['rel'];
        $result['callbackType'] = $_REQUEST['callbackType'];
        $result['forwardUrl'] = $_REQUEST['forwardUrl'];
        $result['confirmMsg'] = $_REQUEST['confirmMsg'];
        $result['message'] = $info;
        $result['info'] = $info;
        $result['data'] = $data;*/
      
        
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
        //dump($data);
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

    function _initialize() {
        //指定文档编码格式,默认是utf-8编码
        header('Content-type: text/html; charset=utf-8');
        Load('extend');
        $langSet = C('DEFAULT_LANG');
        // 定义当前语言

        // 读取项目公共语言包
        /*if (is_file(LANG_PATH . $langSet . '/common.php'))
            L(include LANG_PATH . $langSet . '/common.php');

        // 读取当前模块语言包
        if (is_file(LANG_PATH . $langSet . '/' . MODULE_NAME . '.php'))
            L(include LANG_PATH . $langSet . '/' . MODULE_NAME . '.php');*/
        import('ORG.Util.Cookie');
        // 用户权限检查
        if (C('USER_AUTH_ON') && !in_array(MODULE_NAME, explode(',', C('NOT_AUTH_MODULE')))) {
            import('ORG.Util.RBAC');
            if (!RBAC::AccessDecision()) {
                //检查认证识别号
                if (!$_SESSION [C('USER_AUTH_KEY')]) {
                    if ($this->isAjax()) {
                        $this->ajaxReturn(true, "", 301);
                    } else {
                        //跳转到认证网关-登录页面
                        redirect(PHP_FILE . C('USER_AUTH_GATEWAY'));
                    }
                }
                // 没有权限 抛出错误
                if (C('RBAC_ERROR_PAGE')) {
                    // 定义权限错误页面
                    redirect(C('RBAC_ERROR_PAGE'));
                } else {
                    if (C('GUEST_AUTH_ON')) {
                        $this->assign('jumpUrl', PHP_FILE . C('USER_AUTH_GATEWAY'));
                    }
                    // 提示错误信息
                    $this->error(L('_VALID_ACCESS_'),__APP__."/Public/logout");
                    
                }
            }
        }
        //$cur = strtolower(MODULE_NAME) . '_' . strtolower(ACTION_NAME);
        //$this->assign('cur', $cur);
    }

    public function index() {
        //列表过滤器，生成查询Map对象
        $map = $this->_search();
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
	$sortBy='';
	$asc=false;
        $name = $this->getActionName();
        if (method_exists($this, '_sortdefault')) {
            $this->_sortdefault($sortBy,$asc);
        }
        $model = D($name);
        if (!empty($model)) {
//            dump("模型名：");
//            dump($model);
//            dump("条件查询");
//            dump($map);
//            dump("排序");
//            dump($sortBy);
//            dump("升序降序");
//            dump($asc);
            $this->_list($model, $map,$sortBy,$asc);
        }
        $this->display();
        return;
    }

    /**
      +----------------------------------------------------------
     * 取得操作成功后要返回的URL地址
     * 默认返回当前模块的默认操作
     * 可以在action控制器中重载
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    function getReturnUrl() {
        return __URL__ . '?' . C('VAR_MODULE') . '=' . MODULE_NAME . '&' . C('VAR_ACTION') . '=' . C('DEFAULT_ACTION');
    }

    /**
      +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
      +----------------------------------------------------------
     * @access protected
      +----------------------------------------------------------
     * @param string $name 数据对象名称
      +----------------------------------------------------------
     * @return HashMap
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    protected function _search($name = '') {
        //生成查询条件
        if (empty($name)) {
            $name = $this->getActionName();
        }
        $model = D($name);
        $map = array();
        $fields = $model->getDbFieldsType();
        
        foreach ($fields as $key => $val) {
            if (isset($_REQUEST [$key]) && $_REQUEST [$key] != '' && $_REQUEST [$key] != 'all') {
                $rs = strpos($val, "varchar");
                if (is_int($rs)) {
                    $map [$key] = array('like', "%" . $_REQUEST[$key] . "%");
                } else {
                    $map [$key] = $_REQUEST [$key];
                }
            }
        }
        return $map;
    }

    /**
      +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
      +----------------------------------------------------------
     * @access protected
      +----------------------------------------------------------
     * @param Model $model 数据对象
     * @param HashMap $map 过滤条件
     * @param string $sortBy 排序
     * @param boolean $asc 是否正序
      +----------------------------------------------------------
     * @return void
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    protected function _list($model, $map, $sortBy = '', $asc = false) {
        //排序字段 默认为主键名
        if (!empty($_REQUEST ['_order'])) {
            $order = $_REQUEST ['_order'];
        } else {
            $order = !empty($sortBy) ? $sortBy : $model->getPk();//获取$model的主键
        }
        
        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        if (isset($_REQUEST ['_sort'])) {
//			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
            $sort = $_REQUEST ['_sort'] == 'asc' ? 'asc' : 'desc'; //
        } else {
            $sort = $asc ? 'asc' : 'desc';
        }
	$this->assign("sortorder",$order);
	$this->assign("sortasc",$sort);
        
        //取得满足条件的记录数
        $count = $model->where($map)->count($model->getPk());
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
            //dump("页面跳转数：".$_REQUEST['pageNum']."原数据开始：".$p->firstRow."结束数据:".$p->listRows);
            //分页查询数据
            $voList = $model->where($map)->order("`" . $order . "` " . $sort)->limit($firstRow . ',' . $listRows)->select();
            //echo $model->getLastSql();
            //分页跳转的时候保证查询条件
            foreach ($map as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter .= "$key=" . urlencode($val) . "&";
                }
            }
            //分页显示
            $page = $p->show();
            //列表排序显示
            $sortImg = $sort; //排序图标
            $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
            $sort = $sort == 'desc' ? 1 : 0; //排序方式

            if (method_exists($this, '_deallist')) {
                $this->_deallist($voList);
            }
            //模板赋值显示
            
            $this->assign('list', $voList);
            $this->assign('sort', $sort);
            $this->assign('order', $order);
            $this->assign('sortImg', $sortImg);
            $this->assign('sortType', $sortAlt);
            $this->assign("page", $page);
        }

        //dump("page:".$page."sort:".$sort."order:".$order."totalCount:".$count."numPerPage:".$p->listRows);
        
        $this->assign('totalCount', $count);
        $this->assign('numPerPage', $p->listRows);
        $this->assign('currentPage', !empty($_REQUEST[C('VAR_PAGE')]) ? $_REQUEST[C('VAR_PAGE')] : 1);

        cookie('_currentUrl_', __SELF__);
        return;
    }

    function insert() {
        //B('FilterString');
        $name = $this->getActionName();
        $model = D($name);
        
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        //保存当前数据对象
        $list = $model->add();
        if ($list !== false) { //保存成功
            $this->saveLog(1, $list);
            //$this->assign ( 'jumpUrl', cookie( '_currentUrl_' ) );
            $this->success('新增成功!');
        } else {
            //失败提示
            $this->saveLog(0, $list);
            $this->error('新增失败!');
        }
    }

    public function add() {
        $this->display();
    }

    function read() {
        $this->edit();
    }

    function edit() {
        $name = $this->getActionName();
        $model = D($name);
        $pk = $model->getPk();
        $id = $_REQUEST ['id'];
        $where = array($pk => $id);
        $vo = $model->where($where)->find();
        
        $this->assign("vo", $vo);
        
        $this->display();
    }

    function update() {
        //B('FilterString');
        $name = $this->getActionName();
        $model = D($name);
        //dump($model->create());
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        // 更新数据
        $list = $model->save();
        //dump($model->getLastSql());
        //exit;
        if (false !== $list) {
            $this->saveLog(1, $list);
            //成功提示
            $this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
            $this->success('编辑成功!',cookie( '_currentUrl_' ),true);
        } else {
            $this->saveLog(0, $list);
            //错误提示
            $this->error('编辑失败!');
        }
    }

    /**
      +----------------------------------------------------------
     * 默认删除操作
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    public function delete() {
        //删除指定记录
        $name = $this->getActionName();
        $model = M($name);
        if (!empty($model)) {
            $pk = $model->getPk();
            $id = $_REQUEST [$pk];
            if (isset($id)) {
                $condition = array($pk => array('in', explode(',', $id)));
                $list = $model->where($condition)->setField('status', - 1);
                if ($list !== false) {
                    $this->saveLog(1, $list);
                    $this->success('删除成功！');
                } else {
                    $this->saveLog(0, $list);
                    $this->error('删除失败！');
                }
            } else {
                $this->saveLog(0, $list);
                $this->error('非法操作');
            }
        }
    }

    public function foreverdelete() {
        $msg = "";
        //删除指定记录
        $name = $this->getActionName();
        $model = D($name);
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

    public function clear() {
        //删除指定记录
        $name = $this->getActionName();
        $model = D($name);
        if (!empty($model)) {
            if (false !== $model->where('status=-1')->delete()) { // zhanghuihua@msn.com change status=1 to status=-1
                $this->assign("jumpUrl", $this->getReturnUrl());
                $this->success(L('_DELETE_SUCCESS_'));
            } else {
                $this->error(L('_DELETE_FAIL_'));
            }
        }
        $this->forward();
    }

    /**
      +----------------------------------------------------------
     * 默认禁用操作
     *
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     * @throws FcsException
      +----------------------------------------------------------
     */
    public function forbid() {
        $name = $this->getActionName();
        $model = D($name);
        $pk = $model->getPk();
        $id = $_REQUEST ['id'];
        $f = isset($_GET['f']) ? $_GET['f'] : 'status';
        $condition = array($pk => array('in', $id));
        $list = $model->forbid($condition, $f);
        if ($list !== false) {
            $this->saveLog(1, $list);
            $this->assign("jumpUrl", $this->getReturnUrl());
            $this->success('状态禁用成功');
        } else {
            $this->saveLog(0, $list);
            $this->error('状态禁用失败！');
        }
    }

    public function checkPass() {
        $name = $this->getActionName();
        $model = D($name);
        $pk = $model->getPk();
        $id = $_GET [$pk];
        $condition = array($pk => array('in', $id));
        if (false !== $model->checkPass($condition)) {
            $this->assign("jumpUrl", $this->getReturnUrl());
            $this->success('状态批准成功！');
        } else {
            $this->error('状态批准失败！');
        }
    }

    public function recycle() {
        $name = $this->getActionName();
        $model = D($name);
        $pk = $model->getPk();
        $id = $_GET [$pk];
        $f = isset($_GET['f']) ? $_GET['f'] : 'status';
        $condition = array($pk => array('in', $id));
        if (false !== $model->recycle($condition, $f)) {

            $this->assign("jumpUrl", $this->getReturnUrl());
            $this->success('状态还原成功！');
        } else {
            $this->error('状态还原失败！');
        }
    }

    public function recycleBin() {
        $map = $this->_search();
        $map ['status'] = - 1;
        $name = $this->getActionName();
        $model = D($name);
        if (!empty($model)) {
            $this->_list($model, $map);
        }
        $this->display();
    }

    /**
      +----------------------------------------------------------
     * 默认恢复操作
     *
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     * @throws FcsException
      +----------------------------------------------------------
     */
    function resume() {
        //恢复指定记录
        $name = $this->getActionName();
        $model = D($name);

        $pk = $model->getPk();
        $id = $_GET ['id'];
        $f = isset($_GET['f']) ? $_GET['f'] : 'status';
        $condition = array($pk => array('in', $id));
        if (false !== $model->resume($condition, $f)) {
            $this->saveLog(1, $list);
            $this->assign("jumpUrl", $this->getReturnUrl());
            $this->success('状态恢复成功！');
        } else {
            $this->saveLog(0, $list);
            $this->error('状态恢复失败！');
        }
    }

    /**
      +----------------------------------------------------------
     * 默认禁用操作
     *
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     * @throws FcsException
      +----------------------------------------------------------
     */
    public function toggle() {
        $name = $this->getActionName();
        $model = D($name);
        $pk = $model->getPk();
        $id = $_REQUEST ['id'];
        $f = isset($_GET['f']) ? $_GET['f'] : 'status';
        $condition = array($pk => array('in', $id));
        $list = $model->toggle($condition, $f);
        if ($list !== false) {
            $this->saveLog(1, $list);
            $this->assign("jumpUrl", $this->getReturnUrl());
            echo '{
				"statusCode":"1",
				"message":"\u64cd\u4f5c\u6210\u529f",
				"navTabId":"_blank",
				"forwardUrl":"",
				"callbackType":"",
				"rel":"status_' . $f . '_' . $id . '",
				"src":"/Public/dwz/Images/status-' . $list . '.gif"
			}';
        } else {
            $this->saveLog(0, $list);
            $this->error('状态禁用失败！');
        }
    }

    function saveSort() {
        $seqNoList = $_POST ['seqNoList'];
        if (!empty($seqNoList)) {
            //更新数据对象
            $name = $this->getActionName();
            $model = D($name);
            $col = explode(',', $seqNoList);
            //启动事务
            $model->startTrans();
            foreach ($col as $val) {
                $val = explode(':', $val);
                $model->id = $val [0];
                $model->sort = $val [1];
                $result = $model->save();
                if (!$result) {
                    break;
                }
            }
            //提交事务
            $model->commit();
            if ($result !== false) {
                //采用普通方式跳转刷新页面
                $this->success('更新成功');
            } else {
                $this->error($model->getError());
            }
        }
    }

    /**
      +----------------------------------------------------------
     * 根据查询语句
     * 进行列表过滤
      +----------------------------------------------------------
     * @access protected
      +----------------------------------------------------------
     * @param Model $model 数据对象
     * @param string $sql 查询语句
     * @param int $count 数据总量,用于分页
     * @param array $parameter 分页跳转的时候保证查询条件
     * @param string $sortBy 排序
     * @param boolean $asc 是否正序
      +----------------------------------------------------------
     * @return void
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    protected function _sqlList($model, $sql, $count, $parameter = array(), $sortBy = '', $asc = false, $returnUrl = 'returnUrl') {
        //排序字段 默认为主键名
        if (isset($_REQUEST ['_order'])) {
            $order = $_REQUEST ['_order'];
        } else {
            $order = !empty($sortBy) ? $sortBy : $model->getPk();
        }
        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        if (isset($_REQUEST ['_sort'])) {
            $sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
        } else {
            $sort = $asc ? 'asc' : 'desc';
        }

        if ($count > 0) {
            import("ORG.Util.Page");
            //创建分页对象
            if (!empty($_REQUEST ['listRows'])) {
                $listRows = $_REQUEST ['listRows'];
            } else {
                $listRows = '';
            }
            $p = new Page($count, $listRows);

            //分页查询数据
            if (!empty($order))
                $sql .= ' ORDER BY ' . $order . ' ' . $sort;

            $sql .= ' LIMIT ' . $p->firstRow . ',' . $p->listRows;

            $voList = $model->query($sql, false);

            //echo $model->getlastsql();
            //分页跳转的时候保证查询条件
            foreach ($parameter as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter .= "&$key=" . urlencode($val);
                }
            }

            //分页显示
            $page = $p->show();
            //列表排序显示
            $sortImg = $sort; //排序图标
            $sortAlt = $sort == 'desc' ? L('ASC_TITLE') : L('DESC_TITLE'); //排序提示
            $sort = $sort == 'desc' ? 1 : 0; //排序方式
            //模板赋值显示
            $this->assign('list', $voList);
            $this->assign('sort', $sort);
            $this->assign('order', $order);
            $this->assign('sortImg', $sortImg);
            $this->assign('sortType', $sortAlt);
            $this->assign("page", $page);
            Cookie::set('_currentUrl_', $p->currentUrl);
            Cookie::set($returnUrl, $p->currentUrl);
        } else {
            Cookie::set('_currentUrl_', U($this->getActionName() . "/index"));
            Cookie::set($returnUrl, U($this->getActionName() . "/index"));
        }
        return;
    }

	/* 图片库管理*/
	public function photoGallery()
	{
		$tablename = $this->getActionName();
		$tablename = strtolower($tablename);
		$tablekey=$this->_get("id");
		$tab_id=$tablename."_"."photogallery";
		$this->assign('tab_id',$tab_id);
                $model = D("Picture");
		$map=array(
			'table_name'=>$tablename,
			'table_key'=>$tablekey
		);
		$list=$model->where($map)->order('sort asc,id asc')->select();
		$this->assign('list',$list);
		$this->assign('tablekey',$tablekey);
		$this->display("Public:photogallery");
	}
	/* 图片库修改*/
	public function photoGalleryAct()
	{
		$tablename = $this->getActionName();
		$tablename = strtolower($tablename);
		$tablekey=$this->_post("tablekey");
		if((!empty($_FILES['img_file']['name'][0]))){
			import("ORG.Net.UploadFile");
			$upload = new UploadFile();
			$upload->maxSize  = 3145728 ; 
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg'); 
			$upload->savePath =  PUBLIC_PATH.'Upload/photogallery/';
			$upload->saveRule = 'uniqid';
			$upload->thumb=true;
			$upload->thumbMaxWidth=$cfg['image_width']['value'].','.$cfg['thumb_width']['value'];
			$upload->thumbMaxHeight=$cfg['image_height']['value'].','.$cfg['thumb_height']['value'];
			$upload->thumbPrefix = 'goods_,thumb_';
			$upload->thumb_function="crop,crop";
			//$upload->autoSub=true;
			//$upload->subType='date';
			$gallery=array();
			if(!$upload->upload()) { 
				$this->error($upload->getErrorMsg());
			}else{
				$imgs = $upload->getUploadFileInfo();
				foreach($imgs as $k=>$v)
				{
					$gallery[]=$v;
				}
			}
		}


        $model = D("Picture");
		$img_urls=$_POST['img_url'];
		$tags=$_POST['tags'];
		foreach($gallery as $k=>$v)
		{
			$data=array(
				'table_name'=>$tablename,
				'table_key'=>$tablekey,
				'imgurl'=>$v['savename'],
				'tags'=>$tags[$k],
				'add_time'=>time()
			);
			$model->data($data)->add();
		}
		//echo $model->getLastSql();
		$this->success("保存成功！");
	}
	/* 图片库删除*/
	public function photoGalleryDel()
	{
		$tablename = $this->getActionName();
		$tablename = strtolower($tablename);
		$tablekey=$this->_get("tablekey");
		$id=$this->_get("id");
        $model = D("Picture");
		$map=array(
			'table_name'=>$tablename,
			'table_key'=>$tablekey,
			'id'=>$id
		);
		$imgurl=$model->where($map)->getField('imgurl');
		$src = PUBLIC_PATH.'Upload/photogallery/'.$imgurl;
		if(is_file($src))unlink($src);
		$rs=$model->where($map)->delete();
		if($rs!==false)
		{
			$this->success("删除成功！");
		}
		else
		{
			$this->success("删除失败！");
		}
	}

	/* 格式化 所有分类的路径名称 */
	public function FormateCate()
	{
		$name = $this->getActionName();
        $model = D($name);
		$pk = $model->getPk();
		$list=$model->order('parent_id asc,'.$pk.' asc')->select();
		foreach($list as $k=>$v)
		{
			if($v['parent_id']==0)
			{
				$path="0";
				$npath="0,".$v[$pk];
			}
			else
			{
				$parent=$model->where(array($pk=>$v['parent_id']))->find();
				if(empty($parent))
				{
					$path="0";
					$npath="0,".$v[$pk];
				}
				else
				{
					$path=$parent['npath'];
					$npath=$parent['npath'].','.$v[$pk];
				}
			}
			$model->where(array($pk=>$v[$pk]))->save(array('path'=>$path,'npath'=>$npath));
		}
		$this->success("格式化成功！");
	}


    //用于日志的记录
    protected function saveLog($result = '1', $data_id = 0, $msg = '') {
        $log_app = C("LOG_APP");
        $log_module = MODULE_NAME;
        $log_action = ACTION_NAME;

        if (true) {//in_array($log_action, $log_app[$log_module]))
            $log_data = array();
            $log_data['log_module'] = $log_module;
            $log_data['log_action'] = $log_action;
            if (!$data_id) {
                $pk = M(MODULE_NAME)->getPk();
                $data_id = intval($_REQUEST['id']);
            }
            $log_data['data_id'] = $data_id;
            $log_data['log_time'] = time();
            $log_data['admin_id'] = intval($_SESSION[C("USER_AUTH_KEY")]);
            $log_data['ip'] = get_client_ip();
            $log_data['log_result'] = $result;
            $log_data['log_msg'] = $msg;
            D("AdminLog")->add($log_data);
        }
    }
    
    /*
     * 无限分级的行政组别
     */
    /*
         * 封装 protected
         * AllGroupArray($groupid);需要groupArray($AllArray,$GroupFistArray)配合
         * 根据$groupid来找出下属的所有分组
         * 如果$groupid下无分组将返回自己
         * 如果$groupid下有分组将返回所在组和下属组
         * 核心代码
         */
        protected function AllGroupArray($groupid){
            //找出同组人
                $GroupTeamid = $groupid;//$_SESSION['gid']
                $GroupTeamModel = M("user_group");
                $where['id'] = $GroupTeamid;
                $GroupTeamListItself = $GroupTeamModel->where($where)->select();//本身的数组
                $MemberLevel = $GroupTeamListItself[0]['level'];
                //所有的组
                $AllArray = $GroupTeamModel->select();
                if($MemberLevel == "1"){
                    $GroupList = $AllArray;
                }else{
                    //递归找出所有分组
                    foreach($AllArray as $k=>$v){
                        if($AllArray[$k]['pid'] == $GroupTeamid && $AllArray[$k]['level'] > $MemberLevel){
                            $GroupFistArray[$k]['id']=$AllArray[$k]['id'];
                            $GroupFistArray[$k]['nickname']=$AllArray[$k]['nickname'];
                            $GroupFistArray[$k]['name']=$AllArray[$k]['name'];
                            $GroupFistArray[$k]['pccname']=$AllArray[$k]['pccname'];
                            $GroupFistArray[$k]['member']=$AllArray[$k]['member'];
                            $GroupFistArray[$k]['level']=$AllArray[$k]['level'];
                            $GroupFistArray[$k]['total']=$AllArray[$k]['total'];
                            $GroupFistArray[$k]['create_time']=$AllArray[$k]['create_time'];
                            $GroupFistArray[$k]['update_time']=$AllArray[$k]['update_time'];
                            $GroupFistArray[$k]['status']=$AllArray[$k]['status'];
                            $GroupFistArray[$k]['remark']=$AllArray[$k]['remark'];
                            $GroupFistArray[$k]['pid']=$AllArray[$k]['pid'];
                        }
                    }
                
                    if(!empty($GroupFistArray)){
                        $grouplist = $this->groupArray($AllArray,$GroupFistArray);
                        $GroupList = array_merge($grouplist,$GroupTeamListItself);
                    }else{
                        $GroupList = $GroupTeamListItself; 
                    }
                }
                return $GroupList;
        }
        protected function groupArray($AllArray,$GroupFistArray){
                //递归找出所有分组
                foreach($GroupFistArray as $key=>$value){
                    $GroupTeamid =$GroupFistArray[$key]['id'];
                    $MemberLevel =$GroupFistArray[$key]['level'];
                    foreach($AllArray as $k=>$v){
                        if($AllArray[$k]['pid'] == $GroupTeamid && $AllArray[$k]['level'] > $MemberLevel){
                            //$GroupSecendArray[$k]['id']=$AllArray[$k]['id'];
                            //$GroupSecendArray[$k]['pid']=$AllArray[$k]['pid'];
                            //$GroupSecendArray[$k]['nickname']=$AllArray[$k]['nickname'];
                            
                            $GroupSecendArray[$k]['id']=$AllArray[$k]['id'];
                            $GroupSecendArray[$k]['nickname']=$AllArray[$k]['nickname'];
                            $GroupSecendArray[$k]['name']=$AllArray[$k]['name'];
                            $GroupSecendArray[$k]['pccname']=$AllArray[$k]['pccname'];
                            $GroupSecendArray[$k]['member']=$AllArray[$k]['member'];
                            $GroupSecendArray[$k]['level']=$AllArray[$k]['level'];
                            $GroupSecendArray[$k]['total']=$AllArray[$k]['total'];
                            $GroupSecendArray[$k]['create_time']=$AllArray[$k]['create_time'];
                            $GroupSecendArray[$k]['update_time']=$AllArray[$k]['update_time'];
                            $GroupSecendArray[$k]['status']=$AllArray[$k]['status'];
                            $GroupSecendArray[$k]['remark']=$AllArray[$k]['remark'];
                            $GroupSecendArray[$k]['pid']=$AllArray[$k]['pid'];
                        }
                    }
                }
                if(!empty($GroupSecendArray)){
                   $GroupFistArray = array_merge($GroupFistArray,$this->groupArray($AllArray,$GroupSecendArray));
                }else {
                    
                }
                return $GroupFistArray;
        }
        //特殊方法  用于寻找Task project的Type的分类
        public function AscAllGroupArray($AllGroupArray,$TeamPid){
        foreach($AllGroupArray as $k=>$v){
                if($TeamPid == $AllGroupArray[$k]['id']){
                    $FistGroupArray['id']= $AllGroupArray[$k]['id'];
                    $FistGroupArray['nickname']= $AllGroupArray[$k]['nickname'];
                    $FistGroupArray['name']= $AllGroupArray[$k]['name'];
                    $FistGroupArray['pccname']= $AllGroupArray[$k]['pccname'];
                    $FistGroupArray['member']= $AllGroupArray[$k]['member'];
                    $FistGroupArray['level']= $AllGroupArray[$k]['level'];
                    $FistGroupArray['total']= $AllGroupArray[$k]['total'];
                    $FistGroupArray['create_time']= $AllGroupArray[$k]['create_time'];
                    $FistGroupArray['update_time']= $AllGroupArray[$k]['update_time'];
                    $FistGroupArray['status']= $AllGroupArray[$k]['status'];
                    $FistGroupArray['remark']= $AllGroupArray[$k]['remark'];
                    $FistGroupArray['pid']= $AllGroupArray[$k]['pid'];
                }
            }
      if($FistGroupArray['level'] == "3"){
          //找到level=3  就可以返回
          return $FistGroupArray;
      }else{
          //找到level不等于3  就递归再往上找一层
          $SecoundAllGroupArray = $this->AscAllGroupArray($AllGroupArray,$FistGroupArray['pid']);
          return $SecoundAllGroupArray;
      }      
    }
    
    //author:AresPeng 获取指定日期所在月的开始日期与结束日期
    protected function getMonthRange($date){
        $ret=array();
        if(is_string($date)){
            $timestamp=strtotime($date);
        }elseif(is_int($date)){
            $timestamp=$date;
        }
        //$timestamp=strtotime($date);
        $mdays=date('t',$timestamp);
        $ret['sdate']=date('Y-m-1 00:00:00',$timestamp);
        $ret['sdate_unix']=strtotime(date('Y-m-1 00:00:00',$timestamp));
        $ret['edate']=date('Y-m-'.$mdays.' 23:59:59',$timestamp);
        $ret['edate_unix']=  strtotime(date('Y-m-'.$mdays.' 23:59:59',$timestamp));
        return $ret;
    }
    /*
     * 根据指定年月返回每周的开始时间和结束时间
     * example:2013-12
     */
    protected function get_weekinfo($month){
        $weekinfo = array();
        $end_date = date('d',strtotime($month.' +1 month -1 day'));
        for ($i=1; $i <$end_date ; $i=$i+7) { 
            $w = date('N',strtotime($month.'-'.$i));
            $weekinfo[] = array(date('Y-m-d',strtotime($month.'-'.$i.' -'.($w-1).' days')),strtotime($month.'-'.$i.' -'.($w-1).' days'),date('Y-m-d',strtotime($month.'-'.$i.' +'.(7-$w).' days')),strtotime($month.'-'.$i.' +'.(7-$w).' days'));
        }
        return $weekinfo;
    }
    protected function weeks_in_month($year, $month){
        $fdTS   = mktime(0,0,0,$month,"1",$year);
        $days   = date('t',$fdTS);
        
        if($fw  = date('w',$fdTS)){
            $days-= 7-$fw;
            $wp   = 1;
        }else{
                $wp   = 0;
        }
        return $wp + ceil($days/7);
    }
}

?>