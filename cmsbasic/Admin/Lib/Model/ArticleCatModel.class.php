<?php
/**
 +------------------------------------------------------------------------------
 * 文章分类信息模型
 +------------------------------------------------------------------------------
 * @category   Model
 * @package  Lib
 * @subpackage  Model
 * @author   668FreeCloud <chinasoftstar@qq.com>
 * @version  $Id: ArticleCatModel.class.php 2791 2013/7/28  668FreeCloud $
 +------------------------------------------------------------------------------
 */
class ArticleCatModel extends CommonModel {
	protected $tableName = 'article_cat';
	public function getModule(){
		$modules = array('Article'=>'文章模块','Music'=>'音乐模块','Video'=>'视频模块','Photo'=>'图片模块');
		return $modules;
	}
	public function getTemplate(){
		$modules = array('View'=>'默认模版','Help'=>'帮助中心','Page'=>'单页介绍');
		return $modules;
	}

}