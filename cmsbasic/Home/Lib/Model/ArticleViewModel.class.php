<?php
class ArticleViewModel extends ViewModel
{
	public $viewFields = array(
		'Article'=>array('article_id','title','file_url','open_type','is_open','_type'=>'LEFT'),
		'ArticleCat'=>array('cat_id','cat_type','cat_name', 'sort_order','_on'=>'Article.cat_id=ArticleCat.cat_id')
	);
}