<?php
/**
 +------------------------------------------------------------------------------
 * 广告位
 +------------------------------------------------------------------------------
 * @category   Action
 * @package  Lib
 * @subpackage  Action
 * @author   668FreeCloud <chinasoftstar@qq.com>
 * @version  $Id: AdvPositionAction.class.php 2791 2013/7/28 16:45  668FreeCloud $
 +------------------------------------------------------------------------------
 */
class AdvPositionAction extends CommonAction
{
	//赋值可用模块
	public function _before_index() {
		$model	=	D("AdvPosition");
		$this->assign('template',$model->getTemplate());
	}
	//赋值可用模块
	public function _before_add() 
	{
		$this->insert_data_list();
	}
	//赋值可用模块
	public function _before_edit() {
		$this->insert_data_list();
	}
	//添加广告位
	public function insert(){
		$model = D ('AdvPosition');
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
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
		$model = D ( 'AdvPosition' );
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
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
	private function insert_data_list()
	{
		$model	=	D("AdvPosition");
		$this->assign('template',$model->getTemplate());
		$datalist=$model->getTemplateDate();
		$this->assign('datalist',$datalist);
		$scriptstring="<script type='text/javascript'>";
		$scriptstring.="var data='".json_encode($datalist)."'";
		$scriptstring.="</script>";
		$this->assign('commonscript',$scriptstring);
	}
}

?>