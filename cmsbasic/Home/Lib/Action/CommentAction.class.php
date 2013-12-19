<?php
//前台评论管理
class CommentAction extends CommonAction{
	public function add()
	{
		$model = D ('Comment');
		$m=$model->create ();
		if (false === $m) {
			$this->error ( $model->getError () );
		}
		$user_id=session('user_id');
		if(empty($user_id))
		{
			$user_id=0;
		}
		$user_name=session('user_name');
		if(empty($user_name))
		{
			$user_name="";
		}
		$model->user_id=$user_id;
		$model->user_name=$user_name;
		//保存当前数据对象
		$list=$model->add ();
		//echo $model->getLastSql();
		//exit;
		if ($list!==false) {
			$this->success("评论成功!");
		} else {
			//失败提示
			$this->error ('评论失败!');
		}
	}
}