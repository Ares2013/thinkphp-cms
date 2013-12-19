<?php
/**
 +------------------------------------------------------------------------------
 * 文章信息模型
 +------------------------------------------------------------------------------
 * @category   Model
 * @package  Lib
 * @subpackage  Model
 * @author   668FreeCloud <chinasoftstar@qq.com>
 * @version  $Id: ArticleModel.class.php 2791 2013/7/28  668FreeCloud $
 +------------------------------------------------------------------------------
 */
class ArticleModel extends CommonModel {
	protected $tableName = 'article';
	protected $pk  = 'article_id';
	
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