<?php
class SysconfModel extends Model
{
	protected $tableName = 'shop_config';
	/**
	 * 载入配置信息
	 *
	 * @access  public
	 * @return  array
	 */
	public function load_config()
	{
		$arr = array();
		//$sql = 'SELECT code, value FROM ' . $GLOBALS['ecs']->table('shop_config') . ' WHERE parent_id > 0';
		//$res = $GLOBALS['db']->getAll($sql);
		$res= $this->field("code,value")->where("parent_id>0")->select();
		foreach ($res AS $row)
		{
			$arr[$row['code']] = $row['value'];
		}

		/* 对数值型设置处理 */
		$arr['watermark_alpha']      = intval($arr['watermark_alpha']);
		$arr['market_price_rate']    = floatval($arr['market_price_rate']);
		$arr['integral_scale']       = floatval($arr['integral_scale']);
		$arr['integral_percent']     = floatval($arr['integral_percent']);
		$arr['cache_time']           = intval($arr['cache_time']);
		$arr['thumb_width']          = intval($arr['thumb_width']);
		$arr['thumb_height']         = intval($arr['thumb_height']);
		$arr['image_width']          = intval($arr['image_width']);
		$arr['image_height']         = intval($arr['image_height']);
		$arr['best_number']          = !empty($arr['best_number']) && intval($arr['best_number']) > 0 ? intval($arr['best_number'])     : 3;
		$arr['new_number']           = !empty($arr['new_number']) && intval($arr['new_number']) > 0 ? intval($arr['new_number'])      : 3;
		$arr['hot_number']           = !empty($arr['hot_number']) && intval($arr['hot_number']) > 0 ? intval($arr['hot_number'])      : 3;
		$arr['promote_number']       = !empty($arr['promote_number']) && intval($arr['promote_number']) > 0 ? intval($arr['promote_number'])  : 3;
		$arr['top_number']           = intval($arr['top_number'])      > 0 ? intval($arr['top_number'])      : 10;
		$arr['history_number']       = intval($arr['history_number'])  > 0 ? intval($arr['history_number'])  : 5;
		$arr['comments_number']      = intval($arr['comments_number']) > 0 ? intval($arr['comments_number']) : 5;
		$arr['article_number']       = intval($arr['article_number'])  > 0 ? intval($arr['article_number'])  : 5;
		$arr['page_size']            = intval($arr['page_size'])       > 0 ? intval($arr['page_size'])       : 10;
		$arr['bought_goods']         = intval($arr['bought_goods']);
		$arr['goods_name_length']    = intval($arr['goods_name_length']);
		$arr['top10_time']           = intval($arr['top10_time']);
		$arr['goods_gallery_number'] = intval($arr['goods_gallery_number']) ? intval($arr['goods_gallery_number']) : 5;
		$arr['no_picture']           = !empty($arr['no_picture']) ? str_replace('../', './', $arr['no_picture']) : 'images/no_picture.gif'; // 修改默认商品图片的路径
		$arr['qq']                   = !empty($arr['qq']) ? $arr['qq'] : '';
		$arr['ww']                   = !empty($arr['ww']) ? $arr['ww'] : '';
		$arr['default_storage']      = isset($arr['default_storage']) ? intval($arr['default_storage']) : 1;
		$arr['min_goods_amount']     = isset($arr['min_goods_amount']) ? floatval($arr['min_goods_amount']) : 0;
		$arr['one_step_buy']         = empty($arr['one_step_buy']) ? 0 : 1;
		$arr['invoice_type']         = empty($arr['invoice_type']) ? array('type' => array(), 'rate' => array()) : unserialize($arr['invoice_type']);
		$arr['show_order_type']      = isset($arr['show_order_type']) ? $arr['show_order_type'] : 0;    // 显示方式默认为列表方式
		$arr['help_open']            = isset($arr['help_open']) ? $arr['help_open'] : 1;    // 显示方式默认为列表方式
		if (empty($arr['integrate_code']))
		{
			$arr['integrate_code'] = 'ecshop'; // 默认的会员整合插件为 ecshop
		}
		

		return $arr;
	}
	/**
	 * 获得设置信息
	 *
	 * @param   array   $groups     需要获得的设置组
	 * @param   array   $excludes   不需要获得的设置组
	 *
	 * @return  array
	 */
	function get_settings($groups=null, $excludes=null)
	{
		global $db, $ecs, $_LANG;

		$config_groups = '';
		$excludes_groups = '';

		if (!empty($groups))
		{
			foreach ($groups AS $key=>$val)
			{
				$config_groups .= " AND (id='$val' OR parent_id='$val')";
			}
		}

		if (!empty($excludes))
		{
			foreach ($excludes AS $key=>$val)
			{
				$excludes_groups .= " AND (parent_id<>'$val' AND id<>'$val')";
			}
		}

		/* 取出全部数据：分组和变量 */
		$sql = "SELECT * FROM " . $this->getTableName() .
				" WHERE type<>'hidden' $config_groups $excludes_groups ORDER BY parent_id, sort_order, id";
		$item_list = $db->query($sql);

		/* 整理数据 */
		$group_list = array();
		foreach ($item_list AS $key => $item)
		{
			$pid = $item['parent_id'];
			$item['name'] = isset($_LANG['cfg_name'][$item['code']]) ? $_LANG['cfg_name'][$item['code']] : $item['code'];
			$item['desc'] = isset($_LANG['cfg_desc'][$item['code']]) ? $_LANG['cfg_desc'][$item['code']] : '';

			if ($item['code'] == 'sms_shop_mobile')
			{
				$item['url'] = 1;
			}
			if ($pid == 0)
			{
				/* 分组 */
				if ($item['type'] == 'group')
				{
					$group_list[$item['id']] = $item;
				}
			}
			else
			{
				/* 变量 */
				if (isset($group_list[$pid]))
				{
					if ($item['store_range'])
					{
						$item['store_options'] = explode(',', $item['store_range']);

						foreach ($item['store_options'] AS $k => $v)
						{
							$item['display_options'][$k] = isset($_LANG['cfg_range'][$item['code']][$v]) ?
									$_LANG['cfg_range'][$item['code']][$v] : $v;
						}
					}
					$group_list[$pid]['vars'][] = $item;
				}
			}

		}

		return $group_list;
	}
}