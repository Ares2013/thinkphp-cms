<?php
/**
 +------------------------------------------------------------------------------
 * 文章
 +------------------------------------------------------------------------------
 * @category   Action
 * @package  Lib
 * @subpackage  Action
 * @author   Ares Peng <Z13053003@wistron.local><153415781@qq.com>
 * @version  $Id: ArticleAction.class.php 2791 2013/7/28 16:45  668FreeCloud $
 +------------------------------------------------------------------------------
 */
class ArticleAction extends CommonAction {
	//赋值文章可用分类
	public function _before_index() {
		$model	=	M("ArticleCat");
		$list	=	$model->where()->select();
		$this->assign('types',$list);
	}
	//赋值文章可用分类
	public function _before_add() {
		$model	=	M("ArticleCat");
		$list	=	$model->where()->select();
		$this->assign('list',$list);
                
		$regionM=D("Region");
		$regionlist=$regionM->where(array('parent_id'=>1))->select();
		$this->assign('regionlist',$regionlist);

		$cat_id=$this->_get("cat_id");
		$this->assign("cat_id",$cat_id);
	}
	//赋值文章可用分类
	public function _before_edit() {
		$model	=	M("ArticleCat");
		$list	=	$model->where()->select();
		$this->assign('list',$list);
                
		$regionM=D("Region");
		$regionlist=$regionM->where(array('parent_id'=>1))->select();
		$this->assign('regionlist',$regionlist);
		$cat_id=$this->_get("cat_id");
		$this->assign("cat_id",$cat_id);
	}
        function edit() {
                $regionM=D("Region");
				$name=$this->getActionName();
				$model = D ( $name );
				$pk=$model->getPk();
				$id = $_REQUEST ['id'];
				$where=array($pk=>$id);
				$vo = $model->where($where)->find();
						
				if(!empty($vo['province']))
				{
					$citylist=$regionM->where(array('parent_id'=>$vo['province']))->select();
					$this->assign("citylist",$citylist);
				}
				if(!empty($vo['city']))
				{
					$arealist=$regionM->where(array('parent_id'=>$vo['city']))->select();
					$this->assign("arealist",$arealist);
				}
                
		$this->assign ( 'vo', $vo );
		$this->display ();
	}
	//添加文章
	public function insert() {
		$model = D ('Article');
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		if(!empty($_FILES['img']['name'])){
			import("ORG.Net.UploadFile");
			$upload = new UploadFile();
			$upload->maxSize  = 3145728 ; 
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg'); 
			$upload->savePath = PUBLIC_PATH.'Upload/article/';
			$upload->saveRule = 'uniqid';
			$upload->thumb = false;
			$upload->thumbMaxWidth = 300;
			$upload->thumbMaxHeight = 300;
			$upload->uploadReplace = true;
			$upload->thumbPrefix = '';
			if(!$upload->upload()) { 
				$this->error($upload->getErrorMsg());
			}else{
				$imgs = $upload->getUploadFileInfo(); 
				$model->file_url = $imgs[0]['savename'];
			}
		}
		//保存当前数据对象
		$list=$model->add ();
		if ($list!==false) {
			$this->saveLog(1,$list);
			$this->success("新增成功!");
		} else {
			//失败提示
			$this->saveLog(0,$list);
			$this->error ('新增失败!');
		}
	}
	//更新文章
	public function update() {
		$model = D ('Article');
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		if(!empty($_FILES['img']['name'])){
			import("ORG.Net.UploadFile");
			$upload = new UploadFile();
			$upload->maxSize  = 3145728 ; 
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg'); 
			$upload->savePath =  PUBLIC_PATH.'Upload/article/';
			$upload->saveRule = 'uniqid';
			$upload->thumb = false;
			$upload->thumbMaxWidth = 300;
			$upload->thumbMaxHeight = 300;
			$upload->uploadReplace = true;
			$upload->thumbPrefix = '';
			if(!$upload->upload()) { 
				$this->error($upload->getErrorMsg());
			}else{
				$imgs = $upload->getUploadFileInfo(); 
				$model->file_url = $imgs[0]['savename'];
			}
		}
                $province=$this->_post('province');
                $city=$this->_post('city');
                $area=$this->_post('area');
                if($area=='all')
                {
                    $model->area=0;
                }
                if($city=="all")
                {
                    $model->city=0;
                }
                if($province=='all')
                {
                    $model->province=0;
                }
		//保存当前数据对象
		$list=$model->save();
		if ($list!==false) {
			$this->saveLog(1,$list);
			$this->success('编辑成功!');
		} else {
			//失败提示
			$this->saveLog(0,$list);
			$this->error ('编辑失败!');
		}
	}
	//删除文章时删除预览图片,删除路由规则
	public function _before_foreverdelete() {
		if($_GET['id']){
			$id = $_GET['id'];
			$src =  PUBLIC_PATH.'Upload/article/'.D('Article')->where('article_id='.$id)->getField('file_url');
			if(is_file($src)){
				unlink($src);
			}
		}
	}
	//删除图片
	public function delimg(){
		if($_GET['id']){
			$id = $_GET['id'];			
			$src =  PUBLIC_PATH.'Upload/article/'.D('Article')->where('article_id='.$id)->getField('file_url');
			D('Article')->where('article_id='.$id)->setField('file_url','');
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