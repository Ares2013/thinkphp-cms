<?php
class NavModel extends Model
{
	

	/**
	 * 取得自定义导航栏列表
	 * @param   string      $type    位置，如top、bottom、middle
	 * @return  array         列表
	 */
	public function get_navigator($ctype = '', $catlist = array())
	{
		$res = $this->where("ifshow=1")->order("type asc,vieworder asc")->select();

		$cur_url = substr(strrchr($_SERVER['REQUEST_URI'],'/'),1);

		if (intval($GLOBALS['_CFG']['rewrite']))
		{
			if(strpos($cur_url, '-'))
			{
				preg_match('/([a-z]*)-([0-9]*)/',$cur_url,$matches);
				$cur_url = $matches[1].'.php?id='.$matches[2];
			}
		}
		else
		{
			$cur_url = substr(strrchr($_SERVER['REQUEST_URI'],'/'),1);
		}

		$noindex = false;
		$active = 0;
		$navlist = array(
			'top' => array(),
			'middle' => array(),
			'bottom' => array()
		);
		foreach($res as $k=>$v)
		{
			$navlist[$v['type']][]=array(
					'name'      =>  $v['name'],
					'opennew'   =>  $v['opennew'],
					'url'       =>  $v['url'],
					'ctype'     =>  $v['ctype'],
					'cid'       =>  $v['cid'],
				);
		}
		/*遍历自定义是否存在currentPage*/
		foreach($navlist['middle'] as $k=>$v)
		{
			$condition = empty($ctype) ? (strpos($cur_url, $v['url']) === 0) : (strpos($cur_url, $v['url']) === 0 && strlen($cur_url) == strlen($v['url']));
			if ($condition)
			{
				$navlist['middle'][$k]['active'] = 1;
				$noindex = true;
				$active += 1;
			}
		}

		if(!empty($ctype) && $active < 1)
		{
			foreach($catlist as $key => $val)
			{
				foreach($navlist['middle'] as $k=>$v)
				{
					if(!empty($v['ctype']) && $v['ctype'] == $ctype && $v['cid'] == $val && $active < 1)
					{
						$navlist['middle'][$k]['active'] = 1;
						$noindex = true;
						$active += 1;
					}
				}
			}
		}

		if ($noindex == false) {
			$navlist['config']['index'] = 1;
		}

		return $navlist;
	}
}