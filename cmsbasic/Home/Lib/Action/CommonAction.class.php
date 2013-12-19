<?php
/**
 +------------------------------------------------------------------------------
 * 前台动作基类
 +------------------------------------------------------------------------------
 * @category   Action
 * @package  Lib
 * @subpackage  Action
 * @author   Ares Peng <Z13053003@wistron.local><1534157801@qq.com>
 * @version  $Id: CommonAction.class.php 2013/10/1  Ares Peng
 +------------------------------------------------------------------------------
 */
class CommonAction extends Action {
	//组合查询需要的变量
	protected $sVar=array();
	/**
	+----------------------------------------------------------
	* 初始化,调用页面公共信息部分
	* 
	+----------------------------------------------------------
	* @access public
	+----------------------------------------------------------
	* 
	* 
	+----------------------------------------------------------
	* @return max
	+----------------------------------------------------------
	*/
	function _initialize(){
		//指定文档编码格式,默认是utf-8编码
		header('Content-type: text/html; charset='.C("charset"));
		$skin=C('TMPL_PARSE_STRING');
		$skin['__SKIN__']=$skin['__SKIN__'].C('DEFAULT_THEME');
		C('TMPL_PARSE_STRING',$skin);
		Load('extend');
		
		//把编码赋值到页面
		$this->assign('charset',C("charset"));
		
		//加载配置信息
		$this->load_config();
		
		//当前操作字符串赋值给模版
		//$cur=strtolower(MODULE_NAME).'_'.strtolower(ACTION_NAME);
		$this->assign('cur',$cur);

		
	}
	/**
	+----------------------------------------------------------
	* 加载网站配置信息,并赋值给页面视图
	* 
	+----------------------------------------------------------
	* @access protected
	+----------------------------------------------------------
	* 
	* 
	+----------------------------------------------------------
	* @return max
	+----------------------------------------------------------
	*/
	protected function load_config()
	{
		$confM=D("Sysconf");
		$cfg=$confM->load_config();
		C('CFG',$cfg);
		$this->assign('cfg',$cfg);
	}
	/**
	+----------------------------------------------------------
	* 获取列表页面的查询参数
	* 
	+----------------------------------------------------------
	* @access protected
	+----------------------------------------------------------
	* 
	* 
	+----------------------------------------------------------
	* @return HashMap
	+----------------------------------------------------------
	*/
	protected function _search()
	{
		$map=array();
		foreach ( $this->sVar as $key => $val ) {
			if (isset ( $_REQUEST [$val] ) && $_REQUEST [$val] != '') {
				$map [$val] = remove_xss($_REQUEST [$val]);
			}
		}
		return $map;
	}
}