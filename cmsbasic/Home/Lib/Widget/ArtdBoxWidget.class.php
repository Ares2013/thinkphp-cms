<?php 
	/**
	 * 文章具体内容显示模型
	 * @access public
	 * @param array $data 
	 *      $data=array(
	 *            $id, integer  文章ID
	 *            $subnum, integer  最大显示数量
	 *       )
	 * @return html
	 */
	class ArtdBoxWidget extends Widget
	{
		
		public function render($data)
		{
			//加载文章
			$artM=D('Article');
			$map=array(
				'article_id'=>$data['id']	
			);
			$art=$artM->where($map)->find();
			//echo $artM->getLastSql();
			$data['art']=$art;
			$rendData=$data;
			
			if(empty($data['template']))
			{
				$template='index';
			}
			else
			{
				$template=$data['template'];
			}
			$countent=$this->renderFile($template,$rendData);
			return $countent;
		}
	}

