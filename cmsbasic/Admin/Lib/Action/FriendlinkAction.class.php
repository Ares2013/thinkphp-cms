<?php
/**
 +------------------------------------------------------------------------------
 * 友情连接
 +------------------------------------------------------------------------------
 * @category   Action
 * @package  Lib
 * @subpackage  Action
 * @author   668FreeCloud <chinasoftstar@qq.com>
 * @version  $Id: FriendlinkAction.class.php 2791 2013/7/28 16:45  668FreeCloud $
 +------------------------------------------------------------------------------
 */
class FriendlinkAction extends CommonAction {
	/**
	+----------------------------------------------------------
	* 列表页加载前，加载其他的必要配置
	* 
	+----------------------------------------------------------
	* @access public
	+----------------------------------------------------------
	* 
	* 
	+----------------------------------------------------------
	* @return html
	+----------------------------------------------------------
	*/
	public function _before_index() 
	{
		$friendM=D('Friendlink');
		$typelist=$friendM->Distinct(true)->field('type')->select();
		$this->assign('typelist',$typelist);
	}
	//
	public function _before_add() {
	}
	//
	public function _before_edit() {
	}
	//添加友情连接
	public function insert() {
		$model = D ('Friendlink');
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		if(!empty($_FILES['img']['name'])){
			import("@.ORG.Util.UploadFile");
			$upload = new UploadFile();
			$upload->maxSize  = 3145728 ; 
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg'); 
			$upload->savePath = PUBLIC_PATH.'Upload/friendlogo/';
			$upload->saveRule = 'uniqid';
			$upload->thumb = false;
			$upload->thumbMaxWidth = 48;
			$upload->thumbMaxHeight = 48;
			$upload->uploadReplace = true;
			$upload->thumbPrefix = '';
			if(!$upload->upload()) { 
				$this->error($upload->getErrorMsg());
			}else{
				$imgs = $upload->getUploadFileInfo(); 
				$model->link_logo = $imgs['img']['savename'];
			}
		}
		//保存当前数据对象
		$list=$model->add ();
		if ($list!==false) {
			$this->success("新增成功!");
		} else {
			//失败提示
			$this->error ('新增失败!');
		}
	}
	//更新友情连接
	public function update() {
		$model = D ('Friendlink');
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		if(!empty($_FILES['img']['name'])){
			import("@.ORG.Util.UploadFile");
			$upload = new UploadFile();
			$upload->maxSize  = 3145728 ; 
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg'); 
			$upload->savePath =  PUBLIC_PATH.'Upload/friendlogo/';
			$upload->saveRule = 'uniqid';
			$upload->thumb = false;
			$upload->thumbMaxWidth = 48;
			$upload->thumbMaxHeight = 48;
			$upload->uploadReplace = true;
			$upload->thumbPrefix = '';
			if(!$upload->upload()) { 
				$this->error($upload->getErrorMsg());
			}else{
				$imgs = $upload->getUploadFileInfo(); 
				$model->link_logo = $imgs['img']['savename'];
			}
		}
		//保存当前数据对象
		$list=$model->save();
		if ($list!==false) {
			$this->success('编辑成功!');
		} else {
			//失败提示
			$this->error ('编辑失败!');
		}
	}
	//删除文章时删除预览图片,删除路由规则
	public function _before_foreverdelete() {
		if($_GET['id']){
			$id = $_GET['id'];
			$src = PUBLIC_PATH.'Upload/friendlogo/'.D('Friendlink')->where('link_id='.$id)->getField('img');
			if(is_file($src)){
				unlink($src);
			}
		}
	}
	//删除图片
	public function delimg(){
		if($_GET['id']){
			$id = $_GET['id'];			
			$src = PUBLIC_PATH.'Upload/article/'.D('Friendlink')->where('link_id='.$id)->getField('link_logo');
			D('Friendlink')->where('link_id='.$id)->setField('link_logo','');
			if(is_file($src))unlink($src);
		}
		echo '{
				"statusCode":"1",
				"message":"\u64cd\u4f5c\u6210\u529f",
				"navTabId":"_blank",
				"forwardUrl":"",
				"callbackType":""
			}';
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
		$name=$this->getActionName();
		$model = D ($name);
		$pk = $model->getPk ();
		$id = $_GET ['id'];
		$condition = array ($pk => array ('in', $id ) );
		if (false !== $model->resume ( $condition )) {
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( '状态恢复成功！' );
		} else {
			$this->error ( '状态恢复失败！' );
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
	public function forbid() {
		$name=$this->getActionName();
		$model = D ($name);
		$pk = $model->getPk ();
		$id = $_REQUEST ['id'];
		$condition = array ($pk => array ('in', $id ) );
		$list=$model->forbid ( $condition );
		if ($list!==false) {
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( '状态禁用成功' );
		} else {
			$this->error  (  '状态禁用失败！' );
		}
	}

	public function foreverdelete() {
		$msg="";
		//删除指定记录
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST ['id'];
			if(empty($id))
			{
				$id=$_REQUEST ['ids'];
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