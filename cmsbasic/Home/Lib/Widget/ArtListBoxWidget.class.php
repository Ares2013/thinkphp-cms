<?php 
	/**
	 * 文章列表显示模型
	 * @access public
	 * @param array $data 
	 *      $data=array(
	 *            $cat_id, integer  文章类别
	 *            $top, integer  显示数量
	 *            $length,integer  文章长度
	 *			  $template, string  模版
	 *       )
	 * @return html
	 */
	class ArtListBoxWidget extends Widget
	{
		
		public function render($data)
		{
			//加载文章
			$artM=D('Article');
			$artcM=D("ArticleCat");
			$cat_id=$data['cat_id'];
			$top=$data['top'];
			if(empty($top))
			{
				$top=10;
			}
			$length=$data['length'];
			if(empty($length))
			{
				$length=80;
			}
			$artc=$artcM->where(array('cat_id'=>$cat_id))->find();
			$artlist=$artM->where(array('cat_id'=>$cat_id))->limit($top)->select();
			foreach($artlist as $k=>$v)
			{
				$artlist[$k]['title']=cutstr($v['title'],$length);
			}
			$rendData['artlist']=$artlist;
			$rendData['artc']=$artc;
			
			if(!empty($data['template']))
			{
				$template=$data['template'];
			}
			else
			{
				$template='default';
			}
			$countent=$this->renderFile($template,$rendData);
			return $countent;
		}
	}

