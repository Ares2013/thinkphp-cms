<?php
/**
 +------------------------------------------------------------------------------
 * 广告
 +------------------------------------------------------------------------------
 * @category   Action
 * @package  Lib
 * @subpackage  Action
 * @author   668FreeCloud <chinasoftstar@qq.com>
 * @version  $Id: AdvAction.class.php 2791 2013/7/28 16:45  668FreeCloud $
 +------------------------------------------------------------------------------
 */
class AdvAction extends CommonAction
{
	//赋值可用模块
	public function _before_index() 
	{
	}
	//赋值可用模块
	public function _before_add() 
	{
		$model	=	D("AdvPosition");
		$this->assign('list',$model->select());
	}
	//赋值可用模块
	public function _before_edit() {
		$model	=	D("AdvPosition");
		$this->assign('list',$model->select());
	}
	//添加广告
	public function insert(){
		$model = D ('Adv');
		$advpM  = D("AdvPosition");
		$adv = $model->create ();
		if (false === $adv) {
			$this->error ( $model->getError () );
		}
		$advp=$advpM->where(array('position_id'=>$adv['position_id']))->find();

		if(!empty($_FILES['img']['name'])){
			import("ORG.Net.UploadFile");
			$upload = new UploadFile();
			$upload->maxSize  = 3145728 ; 
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg'); 
			$upload->savePath = PUBLIC_PATH.'Upload/adv/';
			$upload->saveRule = 'uniqid';
			$upload->thumb = false;
			$upload->thumbMaxWidth = $advp['ad_width'];
			$upload->thumbMaxHeight = $advp['ad_height'];
			$upload->uploadReplace = true;
			$upload->thumbPrefix = '';
			if(!$upload->upload()) { 
				$this->error($upload->getErrorMsg());
			}else{
				$imgs = $upload->getUploadFileInfo(); 
				$model->ad_code = $imgs[0]['savename'];
			}
		}

		//保存当前数据对象
		$list=$model->add ();
		if ($list!==false) { //保存成功
			$this->success ('新增成功!');
		} else {
			//失败提示
			$this->error ('新增失败!');
		}
	}
	//编辑广告位
	public function update() {
		$model = D ( 'Adv' );
		$advpM  = D("AdvPosition");
		$adv = $model->create ();
		if (false === $adv) {
			$this->error ( $model->getError () );
		}
		$advp=$advpM->where(array('position_id'=>$adv['position_id']))->find();

		if(!empty($_FILES['img']['name'])){
			import("ORG.Net.UploadFile");
			$upload = new UploadFile();
			$upload->maxSize  = 3145728 ; 
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg'); 
			$upload->savePath = PUBLIC_PATH.'Upload/adv/';
			$upload->saveRule = 'uniqid';
			$upload->thumb = false;
			$upload->thumbMaxWidth = $advp['ad_width'];
			$upload->thumbMaxHeight = $advp['ad_height'];
			$upload->uploadReplace = true;
			$upload->thumbPrefix = '';
			if(!$upload->upload()) { 
				$this->error($upload->getErrorMsg());
			}else{
				$imgs = $upload->getUploadFileInfo(); 
				$model->ad_code = $imgs[0]['savename'];
			}
		}
		// 更新数据
		$list=$model->save ();
		if (false !== $list) {
			$this->success ('编辑成功!');
		} else {
			//错误提示
			$this->error ('编辑失败!');
		}
	}
	
	//删除文章时删除预览图片,删除路由规则
	public function _before_foreverdelete() {
		if($_GET['id']){
			$id = $_GET['id'];
			$src = PUBLIC_PATH.'Upload/adv/'.D('Adv')->where('ad_id='.$id)->getField('ad_code');
			if(is_file($src)){
				unlink($src);
			}
		}
	}
	//删除图片
	public function delimg(){
		if($_GET['id']){
			$id = $_GET['id'];			
			$src = PUBLIC_PATH.'Upload/adv/'.D('Adv')->where('ad_id='.$id)->getField('ad_code');
			D('Adv')->where('ad_id='.$id)->setField('ad_code','');
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
}
?>