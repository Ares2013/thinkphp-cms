<?php 
	class AdvBoxWidget extends Widget
	{
		/**
		 * 广告盒子模型
		 * @access public
		 * @param array $data 
		 *      $data=array(
		 *            $cat_id, integer  商品分类ID
		 *            $number, integer  商品显示个数
		 *       )
		 * @return html
		 */
		public function render($data)
		{
			//加载广告位
			$adpM=D('AdvPosition');
			$map=array(
				'position_id'=>$data['id']	
			);
			$adp=$adpM->where($map)->find();
			$adM=D('Ad');
			$map=array(
				'position_id'=>$data['id'],
				'status'=>1
			);
			$adlist=$adM->where($map)->order('sort desc')->select();
			$data['adlist']=$adlist;
			$data['adp']=$adp;
			$rendData=$data;
			$template='adv';
			
			if(!empty($adp['template']))
			{
				$template=$adp['template'];
			}
			$countent=$this->renderFile($template,$rendData);
			return $countent;
		}
	}

