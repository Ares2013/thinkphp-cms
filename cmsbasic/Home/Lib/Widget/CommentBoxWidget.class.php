<?php 
	class CommentBoxWidget extends Widget
	{
		/**
		 * 用户评论盒子模型
		 * @access public
		 * @param array $data 
		 *      $data=array(
		 *            $type, integer  评论分类ID
		 *            $id, integer  评论id
		 *       )
		 * @return html
		 */
		public function render($data)
		{
			$cfg=C("CFG");
			$rendData=array();
			/* 验证码相关设置 */
			if ((intval($cfg['captcha'])))
			{
				$rendData['enabled_captcha']=1;
				$rendData['rand']=mt_rand();
			}
			$rendData['username']=stripslashes(session('user_name'));
			$rendData['email']=session('email');
			$rendData['comment_type']= $data['type'];
			$rendData['id']=           $data['id'];
			$cmt = assign_comment($data['id'],          $data['type']);
			$rendData['comments']=$cmt['comments'];
			$rendData['pager']=$cmt['pager'];
			$rendData['avg']=sprintf("%01.1f", $cmt['avg']);
			$rendData['user_name']=session('user_name');
			
			$countent=$this->renderFile('commentbox',$rendData);
			return $countent;
		}

	}

