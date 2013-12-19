<?php
/**
 +------------------------------------------------------------------------------
 * 系统参数设置
 +------------------------------------------------------------------------------
 * @category   Action
 * @package  Lib
 * @subpackage  Action
 * @author   Ares Peng <Z13053003@wistron.local><1534157801@qq.com>
 * @version  $Id: SysconfAction.class.php 2013/10/1  Ares Peng
 +------------------------------------------------------------------------------
 */
class SysconfAction extends CommonAction {
	//调用index之前
	public function _before_index() {
		
	}
	//调用add之前
	public function _before_add() {
	}
	//调用eidt之前
	public function _before_edit() {
	}
	public function index()
	{
		$confM=D("Sysconf");
		$cfg=$confM->load_config();
		$this->assign('cfg',$cfg);
		$this->display();
	}
	//更新配置
	public function update() {
		$model = D ('Sysconf');
		
		$data=$_POST['info'];
		if(!empty($_FILES['img']['name'])){
			import("@.ORG.Util.UploadFile");
			$upload = new UploadFile();
			$upload->maxSize  = 3145728 ; 
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg'); 
			$upload->savePath = PUBLIC_PATH.'Upload/logo/';
			$upload->saveRule = 'uniqid';
			$upload->thumb = true;
			$upload->thumbMinWidth = 300;
			$upload->thumbMaxHeight = 160;
			$upload->uploadReplace = true;
			$upload->thumbPrefix = 'jpg';
			if(!$upload->upload()) { 
				$this->error($upload->getErrorMsg());
			}else{
				$imgs = $upload->getUploadFileInfo(); 
				$data['shop_logo']= $imgs['img']['savename'];
			}
		}
		foreach($data as $k=>$v)
		{
			$model->where(array('code'=>$k))->save(array('value'=>$v)); 
		}
		$error=$model->getDbError();

		if (empty($error)) {
			
			$this->success('编辑成功!');
		} else {
			//失败提示
			$this->error ('编辑失败!');
		}
	}
	
	//删除LOGO图片
	public function delimg(){
		$confM=D("Sysconf");
		$shop_logo=$confM->where(array('code'=>'shop_logo'))->find();
		$src = PUBLIC_PATH.'Upload/logo/'.$shop_logo['value'];
		$confM->where(array('code'=>'shop_logo'))->setField('value','');
		if(is_file($src))unlink($src);
		echo '{
				"statusCode":"1",
				"message":"\u64cd\u4f5c\u6210\u529f",
				"navTabId":"_blank",
				"forwardUrl":"",
				"callbackType":"forward"
			}';
	}
	public function sendEmail()
	{

		$sendto='tiger6681517@qq.com';//$this->_post('emailurl');
		$title='测试邮件';
		$response='邮件';
		try{
			$rs=s_mail($sendto, $title, $response);
		}
		catch(Exception $e)
		{
			$this->error("发送失败，请先保存参数，然后测试！");
		}
		if($rs)
		{
			$this->success("发送成功!");
		}
		else
		{
			$this->error("发送失败，请先保存参数，然后测试！");
		}
	}
}