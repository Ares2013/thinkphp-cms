<?php
class CommonModel extends Model {
	public function getPosition($id){
		$type = D('Category')->where('status=1')->find($id);
		if($type['pid']==0){
			$position = $id;
		}else{
			$position = $type['pid'];
		}
		return $position;
	}
	public function getCategoryMap($id){
		$type = D('Category')->where('status=1')->find($id);
		if($type['pid']==0){
			$types = D('Category')->where('status=1 AND pid='.$type['id'])->select();
			if(is_array($types)){
					foreach($types as $val) $ary[]=$val['id'];
			}
			$map['tid']	= array('in',$ary);
		}else{
			$map['tid'] = array('eq',$id);
		}
		return $map;		
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