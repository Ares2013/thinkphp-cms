<?php
/**
 +------------------------------------------------------------------------------
 * 基础模型
 +------------------------------------------------------------------------------
 * @category   Model
 * @package  Lib
 * @subpackage  Model
 * @author   668FreeCloud <chinasoftstar@qq.com>
 * @version  $Id: CommonModel.class.php 2791 2013/7/28  668FreeCloud $
 +------------------------------------------------------------------------------
 */
class CommonModel extends Model {

	// 获取当前用户的ID
    public function getMemberId() {
        return isset($_SESSION[C('USER_AUTH_KEY')])?$_SESSION[C('USER_AUTH_KEY')]:0;
    }

   /**
     +----------------------------------------------------------
     * 根据条件禁用表数据
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $options 条件
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    public function forbid($options,$field='status'){
		
        if(FALSE === $this->where($options)->setField($field,0)){
            $this->error =  L('_OPERATION_WRONG_');
            return false;
        }else {
            return True;
        }
    }
	/**
     +----------------------------------------------------------
     * 根据toggle
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $options 条件
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    public function toggle($options,$field='status'){
		$cur=$this->where($options)->getField($field);
		$cur=intval($cur);
		if($cur==0)
		{
			$cur=1;
		}
		else
		{
			$cur=0;
		}
        if(FALSE === $this->where($options)->setField($field,$cur)){
            $this->error =  L('_OPERATION_WRONG_');
            return false;
        }else {
            return $cur;
        }
    }
	 /**
     +----------------------------------------------------------
     * 根据条件批准表数据
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $options 条件
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */

    public function checkPass($options,$field='status'){
        if(FALSE === $this->where($options)->setField($field,1)){
            $this->error =  L('_OPERATION_WRONG_');
            return false;
        }else {
            return True;
        }
    }


    /**
     +----------------------------------------------------------
     * 根据条件恢复表数据
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $options 条件
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    public function resume($options,$field='status'){
        if(FALSE === $this->where($options)->setField($field,1)){
			
            $this->error =  L('_OPERATION_WRONG_');
            return false;
        }else {
			/*echo $this->getLastSql();
			exit;*/
            return True;
        }
    }

    /**
     +----------------------------------------------------------
     * 根据条件恢复表数据
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $options 条件
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    public function recycle($options,$field='status'){
        if(FALSE === $this->where($options)->setField($field,0)){
            $this->error =  L('_OPERATION_WRONG_');
            return false;
        }else {
            return True;
        }
    }

    public function recommend($options,$field='is_recommend'){
        if(FALSE === $this->where($options)->setField($field,1)){
            $this->error =  L('_OPERATION_WRONG_');
            return false;
        }else {
            return True;
        }
    }

    public function unrecommend($options,$field='is_recommend'){
        if(FALSE === $this->where($options)->setField($field,0)){
            $this->error =  L('_OPERATION_WRONG_');
            return false;
        }else {
            return True;
        }
    }
	/**
     * 把返回的数据集转换成Tree
     * @access public
     * @param array $list 要转换的数据集
     * @param string $pid parent标记字段
     * @param string $level level标记字段
     * @return array
     */
    public function toTree($list=null, $pk='id',$pid = 'parent_id',$child = '_child')
    {
        if(null === $list)
			return;

        // 创建Tree
        $tree = array();
        if(is_array($list))
		{
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data)
			{
                $_key = is_object($data)?$data->$pk:$data[$pk];
                $refer[$_key] =& $list[$key];
            }            

            foreach ($list as $key => $data)
			{
                // 判断是否存在parent
                $parentId = is_object($data)?$data->$pid:$data[$pid];
                $is_exist_pid = false;
                foreach($refer as $k=>$v)
                {
                	if($parentId==$k)
                	{
                		$is_exist_pid = true;
                		break;
                	}
                }

                if ($is_exist_pid)
				{ 
                    if (isset($refer[$parentId]))
					{
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }
				else
				{
                    $tree[] =& $list[$key];
                }
            }
        }
        return $tree;
    }
	
	/**
	 * 将格式数组转换为树
	 * @param array $list
	 * @param integer $level 进行递归时传递用的参数
	 * @param string dispname 显示的名称的列的集合
	 */
	private $formatTree; //用于树型数组完成递归格式的全局变量
	private function _toFormatTree($list,$level=0,$dispname_arr=array('title')) 
	{

		  foreach($list as $key=>$val)
		  {
			$tmp_str=str_repeat("&nbsp;&nbsp;",$level*2);
			$tmp_str.="|--";
			foreach($dispname_arr as $dispname)
			{
				$val[$dispname]=$tmp_str."&nbsp;&nbsp;".$val[$dispname];
			}

			$val['level'] = $level;
			if(!array_key_exists('_child',$val))
			{
			   array_push($this->formatTree,$val);
			}
			else
			{
			   $tmp_ary = $val['_child'];
			   unset($val['_child']);
			   array_push($this->formatTree,$val);
			   $this->_toFormatTree($tmp_ary,$level+1,$dispname_arr); //进行下一层递归
			}
		  }
		  return;
	}

	public function toFormatTree($list,$dispname_arr=array('title'),$pk='id',$pid = 'parent_id')
	{
		if(!is_array($dispname_arr))
			$dispname_arr = array($dispname_arr);
			
		$list = $this->toTree($list,$pk,$pid);
		$this->formatTree = array();
		$this->_toFormatTree($list,0,$dispname_arr);
		return $this->formatTree;
	}
	
	//无限递归获取子数据ID集合

	private $childIds;
	private function _getChildIds($pid = '0', $pk_str='id' , $pid_str ='parent_id')
	{
		$childItem_arr = $this->field($pk_str)->where($pid_str."=".$pid)->select();
		if($childItem_arr)
		{
			foreach($childItem_arr as $childItem)
			{
				$this->childIds[] = $childItem[$pk_str];
				$this->_getChildIds($childItem[$pk_str],$pk_str,$pid_str);
			}
		}
	}

	public function getChildIds($pid = '0', $pk_str='id' , $pid_str ='parent_id')
	{
		$this->childIds = array();
		$this->_getChildIds($pid,$pk_str,$pid_str);
		return $this->childIds;
	}

	/*
	 * 获取其他表的真实表名
	 */
	public function getOtherTableName($classname)
	{
		if(!empty($classname))
		{
			$tableName  = !empty($this->tablePrefix) ? $this->tablePrefix : '';
			$tableName .= $classname;
			$talbeName=strtolower($tableName);
			return (!empty($this->dbName)?$this->dbName.'.':'').$tableName;
		}
		return $classname;
	}
	public function getOne($sql,$limited=false)
	{
		if ($limited == true)
        {
            $sql = trim($sql . ' LIMIT 1');
        }

        $res = $this->query($sql);
        if ($res !== false)
        {
            $row = $res[0];
            if ($row !== false)
            {
				if(is_array($row))
				{
					$row=array_values($row);
					return $row[0];
				}
				else
				{
					return $row;
				}
            }
            else
            {
                return '';
            }
        }
        else
        {
            return false;
        }
	}
	/*
	 *
	 */
	public function getRow($sql,$limited = false)
	{
		if ($limited == true)
        {
            $sql = trim($sql . ' LIMIT 1');
        }

        $res = $this->query($sql);
        if ($res !== false)
        {
            return $res[0];
        }
        else
        {
            return false;
        }
	}
	public function getCol($sql)
    {
        $res = $this->query($sql);
        if ($res !== false)
        {
            $arr = array();
            foreach ($res as $row)
            {
                $arr[] = $row[0];
            }

            return $arr;
        }
        else
        {
            return false;
        }
    }
}
?>