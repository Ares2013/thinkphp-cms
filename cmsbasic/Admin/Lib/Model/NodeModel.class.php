<?php
/**
 +------------------------------------------------------------------------------
 * 节点模型
 +------------------------------------------------------------------------------
 * @category   Model
 * @package  Lib
 * @subpackage  Model
 * @author   668FreeCloud <chinasoftstar@qq.com>
 * @version  $Id: NodeModel.class.php 2791 2013/7/28  668FreeCloud $
 +------------------------------------------------------------------------------
 */
class NodeModel extends CommonModel {
	protected $_validate	=	array(
		array('name','checkNode','节点已经存在',0,'callback'),
		);

	public function checkNode() {
		$map['name']     =  $_POST['name'];
		$map['pid']      =  isset($_POST['pid'])?$_POST['pid']:0;
                $map['status']   = 1;
                if(!empty($_POST['id'])) {
                    $map['id']	 =  array('neq',$_POST['id']);
                }
                $result	= $this->where($map)->field('id')->find();
                if($result) {
                        return false;
                }else{
                        return true;
                }
	}
}
?>