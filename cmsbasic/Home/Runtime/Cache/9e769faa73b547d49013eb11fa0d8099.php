<?php if (!defined('THINK_PATH')) exit(); if(is_array($adlist)): $i = 0; $__LIST__ = $adlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ad): $mod = ($i % 2 );++$i;?><div class="pic">
		<a target="_blank" alt="<?php echo ($ad["ad_name"]); ?>" href="<?php echo ($ad["ad_link"]); ?>">
			<img thumb="" alt="<?php echo ($ad["ad_name"]); ?>" src="__PUBLIC__/Upload/adv/<?php echo ($ad["ad_code"]); ?>" style="width:<?php echo ($adp['ad_width']); ?>px;height:<?php echo ($adp['ad_height']); ?>px" />
		</a>
	</div><?php endforeach; endif; else: echo "" ;endif; ?>