<?php
/**
 +------------------------------------------------------------------------------
 * xuezi 文章信息模型
 +------------------------------------------------------------------------------
 * @category   Model
 * @package  Lib
 * @subpackage  Model
 * @author   zhujiangtao <tiger6681517@qq.com>
 * @version  $Id: TeacherModel.class.php 2791 2012/8/21  zhujiangtao $
 +------------------------------------------------------------------------------
 */
class ArticleModel extends CommonModel {
	protected $tableName = 'article';
	
	/**
	+----------------------------------------------------------
	* 调用文章信息列表，前多少条
	* 
	+----------------------------------------------------------
	* @access public
	+----------------------------------------------------------
	* @param int $cat 指定文章类别
	* @param int $top 指定要需要调用的条数
	* 默认为10 
	* 
	* 
	+----------------------------------------------------------
	* @return array
	+----------------------------------------------------------
	*/
	public function getArtList($cat,$top=10)
	{
		$arr=array();
		$where=array(
			'is_open'=>1,
			'cat_id'=>$cat
		);
		$arr=$this->where($where)->order('article_id desc')->limit($top)->select();
		return $arr;
	}
}