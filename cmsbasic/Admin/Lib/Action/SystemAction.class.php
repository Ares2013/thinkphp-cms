<?php
/**
 +------------------------------------------------------------------------------
 * 系统功能
 +------------------------------------------------------------------------------
 * @category   Action
 * @package  Lib
 * @subpackage  Action
 * @author   Ares Peng <Z13053003@wistron.local><1534157801@qq.com>
 * @version  $Id: SystemAction.class.php 2791 2013/10/1  Ares Peng
 +------------------------------------------------------------------------------
 */
class SystemAction extends CommonAction {
	//清除缓存
	public function clear(){
		import("ORG.Io.Dir");
		$dir = '../Admin/Runtime/';
		if(is_dir($dir)){
			Dir::delDir($dir);
		}
		$dir = '../Home/Runtime/';
		if(is_dir($dir)){
			Dir::delDir($dir);
		}
		$this->success('清除成功！');
	}
	//清除缓存
	public function removeInstall(){
		import("ORG.Io.Dir");
		$dir = new Dir;
		$dirname = ROOT_PATH.'/Install';
		if(is_dir($dirname)){
			$dir->delDir($dirname);
		}
		$this->success('删除成功！');
	}
	//备份数据库
	public function backdb(){
		$db =   Db::getInstance();  
		$tables = $db->getTables();
		foreach ($tables as $tbname){  
			//表结构
			$tempsql.="DROP TABLE IF EXISTS `$tbname`;\n";
			$struct=$db->query("show create table `$tbname`");
			$tempsql.= $struct[0]['Create Table'].";\n\n";  
			$sql='';
			//数据
			$coumt=$db->getFields($tbname);  
			$modelname=str_replace(C('DB_PREFIX'),'',$tbname);  
			$row=D($modelname);  
			$row=$row->select(); 
	   
			$values = array();  
			foreach ($row as $value) {  
				$sql = "INSERT INTO `{$tbname}` VALUES (";  
				foreach($value as $v) {  
					$sql .="'".mysql_real_escape_string($v)."',";  
				}  
			$sql=substr($sql,0,-1);  
			$sql .= ");\n\n";  
			$tempsql.= $sql;  
			$sql='';  
			}   
		}		
		$filename= 'db-'.date('Y').'-'.date('m').'-'.date('d').'.sql';	
		$filepath = '../Public/Upload/download/'.$filename;
		$fp = fopen($filepath,'w');         
		if(fputs($fp,$tempsql) === false){
			$this->error('备份数据失败！');
		}else{
			$filepath = $_SERVER[DOCUMENT_ROOT].__ROOT__.'/Public/Upload/download/'.$filename;
			header("Content-type: application/octet-stream");  
			header("Content-Length: ".filesize($filepath));  
			header("Content-Disposition: attachment; filename=$filename");	
			$fp = fopen($filepath, 'rb');  
			fpassthru($fp);  
			fclose($fp); 
			unlink($filepath);
		}
	}
	//执行SQL语句
	public function querysql(){
		if($_POST){
			$db = Db::getInstance();  
			$sql = $_POST['sql'];
			if(!$sql) $this->error('没有需要执行的SQL语句！');
			$sql = str_replace("\r", "\n",$sql);
			foreach(explode(";\n", trim($sql)) as $query){
				$queries = explode("\n", trim($query));
				$sq = "";
				foreach($queries as $query){
					$sq .= $query;
				}
				if(false===$db->query($sq)){					
					$this->error('执行出错，中止执行！');
					break;					
				} 
			}
			unset($sql);
			$this->success('SQL语句全部执行成功！');
		}else{
			$this->display();
		}
	}
	//RSS生成
	public function rss(){
		$fp = fopen('rss.xml', w);
		$sys = D('System')->find(1);
		$str='<?xml version="1.0" encoding="utf-8"?>
                      <rss version="2.0">
                      <channel>
                      <title>'.C('SITENAME').'</title>
                      <link>'.C('SITEURL').'</link>
                      <keywords>'.C('SITE_KEYWORDS').'</keywords>
                      <description>'.C('SITE_DESCRIPTION').'</description>';		
		$arts = D('Article')->where('status=1')->select();
		foreach ($arts as $val){
			$str.='
	<item>
		<title>'.$val['title'].'</title>
		<link>'.C('SITEURL').'/article/view/id/'.$val['id'].'</link>
		<description>'.$val['description'].'</description>
		<pubDate>'.date('Y-m-d H:i:s',$val['add_time']).'</pubDate>
	</item>';
		}
		$music = D('Music')->where('status=1')->select();
		foreach ($music as $val){
			$str.='
	<item>
		<title>'.$val['title'].'</title>
		<link>'.C('SITEURL').'/music/view/id/'.$val['id'].'</link>
		<description>'.$val['description'].'</description>
		<pubDate>'.date('Y-m-d H:i:s',$val['add_time']).'</pubDate>
	</item>';
		}
		$video = D('Video')->where('status=1')->select();
		foreach ($video as $val){
			$str.='
	<item>
		<title>'.$val['title'].'</title>
		<link>'.C('SITEURL').'/video/view/id/'.$val['id'].'</link>
		<description>'.$val['description'].'</description>
		<pubDate>'.date('Y-m-d H:i:s',$val['add_time']).'</pubDate>
	</item>';
		}
		$diary = D('Diary')->where('status=1')->select();
		foreach ($diary as $val){
			$str.='
	<item>
		<title>'.$val['content'].'</title>
		<link>'.C('SITEURL').'/diary/view/id/'.$val['id'].'</link>
		<weather>'.$val['weather'].'</weather>
		<pubDate>'.date('Y-m-d H:i:s',$val['add_time']).'</pubDate>
	</item>';
		}
		$photo = D('Photo')->where('status=1')->select();
		foreach ($photo as $val){
			$str.='
	<item>
		<title>'.$val['title'].'</title>
		<link>'.C('SITEURL').'/photo/view/id/'.$val['id'].'</link>
		<description>'.$val['intro'].'</description>
		<pubDate>'.date('Y-m-d H:i:s',$val['add_time']).'</pubDate>
	</item>';
		}
		
		$str.='
</channel>
</rss>';
		if(fwrite($fp, $str) === false){
			$this->error('RSS文件写入失败！');
		}else {
			$this->success('RSS文件生成成功！');
		}
		fclose($fp);
	}
}