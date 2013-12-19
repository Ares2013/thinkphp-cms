<?php 
	class NavBoxWidget extends Widget
	{
		/**
		 * 导航栏 盒子模型
		 * @access public
		 * @param array $data 
		 *      $data=array(
		 *            $cat_id, integer  导航分类ID
		 *            $number, integer  导航显示个数
		 *       )
		 * @return html
		 */
		public function render($data)
		{
			$navM=D("Nav");
			
			$cat_id=$data['cat_id'];
			$number=$data['number'];
			$rendData['navlist']=$navM->where(array('cid'=>$cat_id,'status'=>1))->order('sort asc,id asc')->limit($number)->select();
			$cururl="http://".getDomain().__SELF__;
			$rendData["cururl"]=$cururl;
			$rendData["selfurl"]=__SELF__;
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

