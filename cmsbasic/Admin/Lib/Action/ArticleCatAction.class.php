<?php
/**
 +------------------------------------------------------------------------------
 * 文章分类
 +------------------------------------------------------------------------------
 * @category   Action
 * @package  Lib
 * @subpackage  Action
 * @author   668FreeCloud <chinasoftstar@qq.com>
 * @version  $Id: ArticleCatAction.class.php 2791 2013/7/28 16:45  668FreeCloud $
 +------------------------------------------------------------------------------
 */
class ArticleCatAction extends CommonAction {
	//处理默认分类
	public function _sortdefault(&$sortBy,&$asc){
		$sortBy="npath";
		$asc=true;
	}
	//赋值可用模块
	public function _before_index() {
		$model	=	D("ArticleCat");
		$this->assign('template',$model->getTemplate());
	}
	//赋值可用模块
	public function _before_add() {
		$model	=	D("ArticleCat");
		$list	=	$model->where('parent_id=0')->select();
		$this->assign('list',$list);
		$this->assign('template',$model->getTemplate());
	}
	//赋值可用模块
	public function _before_edit() {
		$model	=	D("ArticleCat");
		$list	=	$model->where('parent_id=0')->select();
		$this->assign('list',$list);
		$this->assign('template',$model->getTemplate());
	}
	//添加分类
	public function insert(){
		$model = D ('ArticleCat');
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list=$model->add ();
		if ($list!==false) { //保存成功
			if($_POST['rewrite']){
				$data['rewrite']=$_POST['rewrite'];
				$data['url']=strtolower($_POST['module']).'/index/id/'.$model->getLastInsID();
				D('Router')->add($data);
			}
			$this->success ('新增成功!');
		} else {
			//失败提示
			$this->error ('新增失败!');
		}
	}
	//编辑分类
	public function update() {
		$model = D ( 'ArticleCat' );
		$category = $model->find($_POST['cat_id']);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save ();
		if (false !== $list) {
			//成功提示
			/*D('Router')->where("url='".strtolower($category['module'])."/index/id/".$_POST['id']."'")->delete();
			if($_POST['rewrite']){
				$data['url']=strtolower($_POST['module'])."/index/id/".$_POST['id'];
				$data['rewrite']=$_POST['rewrite'];
				D('Router')->add($data);
			}*/
			$this->success ('编辑成功!');
		} else {
			//错误提示
			$this->error ('编辑失败!');
		}
	}	
	//树形结构数据组装
	public function tree(){
		$model = D("ArticleCat");
		$list = $model->where('parent_id=0')->select();
		if($list){
			foreach ($list as $key=>$val){
				$list[$key]['sub_category'] = $model->where('parent_id='.$val['cat_id'])->select(); 
			}
		}
		$this->assign('list',$list);
		$this->display();
	}
	//删除分类的同时，删除路由规则
	public function _before_foreverdelete() {
		if($_GET['cat_id']){
			//$id = $_GET['id'];
			//$rewrite = D('ArticleCat')->where('cat_id='.$id)->getField('rewrite');
			//D('Router')->where('rewrite="'.$rewrite.'"')->delete();
		}
	}
}