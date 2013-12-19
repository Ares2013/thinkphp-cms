<?php
/**
 +------------------------------------------------------------------------------
 * 前台文章显示处理类
 +------------------------------------------------------------------------------
 * @category   Action
 * @package  Lib
 * @subpackage  Action
 * @author   zhujiangtao <tiger6681517@qq.com>
 * @version  $Id: ArticleAction.class.php 2791 2012/10/16  zhujiangtao $
 +------------------------------------------------------------------------------
 */
class ArticleAction extends CommonAction{
    
           
    /**
	+----------------------------------------------------------
	* 文章列表页面
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
	public function index(){
		//获取文章类别ID
		$id=$this->_get("id");
		$artcM=D("ArticleCat");
		$artCat=$artcM->where(array('cat_id'=>$id))->find();
		$this->assign("artCat",$artCat);
        $path=$artCat['npath'];
		$catlist_id=$artcM->where(array('npath'=>array('like',$path.'%')))->getField('cat_id',true);
		//echo $artcM->getLastSql();
		//dump($catlist_id);
		$template='index';
		if(!empty($artCat['list_template']))
		{
			$template=$artCat['list_template'];
		}

		$model=D("Article");
		
		$sVars = array();
		// p: 页码 
        $this->sVar=array('p','id');
        $sVars=$this->_search();
        $this->assign('sVars',$sVars);
        //分页取数据
        import("ORG.Util.Page");
        $map = array();
		$map['status']=1;
		
		if(!empty($catlist_id))
		{
			$map['cat_id']=array('in',$catlist_id);
		}
		else
		{
			$map['cat_id']=$id;
		}
		$count = $model->where($map)->count(); 
		$Page = new Page($count,15);
		$show = $Page->show(MOUDULE_NAME.'/'.ACTION_NAME,$sVars); 
		$list = $model->where($map)->order('sort DESC,add_time DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		//赋值给模板
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display($template);
	}
	//查看文章详细信息
	public function view(){
		//获取中考资讯列表
		$artM=D("Article");


		//获取本片文章
		$id = $this->_get('id');
		$info = $artM->where(array('article_id'=>$id))->find();
		$this->assign('info',$info);

		$catM=D("ArticleCat");
		$cat=$catM->where(array('cat_id'=>$info['cat_id']))->find();
		$path=$cat['npath'];
		//dump($path);
		$catlist_id=$catM->where(array('npath'=>array('like',$path.'%')))->getField('cat_id',true);
		//dump($catlist_id);
		$this->assign('cat',$cat);
		$map=array(
			'article_id'=>array('lt',$id)
		);
		if(!empty($catlist_id))
		{
			$map['cat_id']=array('in',$catlist_id);
		}
		else
		{
			$map['cat_id']=$cat['cat_id'];
		}
		//dump($map);
		$art_pre = $artM->where($map)->order('article_id DESC')->field('article_id,title')->find();
		$this->assign('art_pre',$art_pre);//上一篇
		$map['article_id']=array('gt',$id);
		$art_next =$artM->where($map)->order('article_id asc')->field('article_id,title')->find();
		$this->assign('art_next',$art_next);//下一篇
		$this->display();
		
	}
	//单页展示
	public function Page()
	{
		$id = $this->_get("id");
		$artM=D("Article");
		$art=$artM->find($id);
                //dump($art);
		$this->assign('art',$art);
                $catM=D("ArticleCat");
                $cat=$catM->where(array('cat_id'=>$art['cat_id']))->find();
                $artList=$artM->where(array('cat_id'=>$art['cat_id']))->select();
                $this->assign('artList',$artList);
		if(!empty($cat['template']))
		{
			$src=TMPL_PATH.THEME_NAME.'/'.MODULE_NAME.'/page_'.$cat['template'].C('TMPL_TEMPLATE_SUFFIX');
			if(file_exists($src))
			{
				$this->display('page_'.$cat['template']);
			}
			else
			{
				$this->display();
			}
		}
		else
		{
			$this->display();
		}
	}
}