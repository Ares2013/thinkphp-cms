<?php if (!defined('THINK_PATH')) exit();?><link rel="stylesheet" type="text/css" href="__PUBLIC__/js/mf-pattern/<?php echo ($adp["data"]); ?>.css">
<script type="text/javascript" src="__PUBLIC__/js/myfocus-2.0.1.min.js"></script>
<script type="text/javascript" src="__PUBLIC__/js/mf-pattern/<?php echo ($adp["data"]); ?>.js"></script>
<script type="text/javascript">
	//设置
	myFocus.set({
		id:'slideFocus',//ID
		pattern:'<?php echo ($adp["data"]); ?>',//风格
		width:parseInt('<?php echo ($adp["ad_width"]); ?>'),
		height:parseInt('<?php echo ($adp["ad_height"]); ?>')
	});
</script>
<div id="slideFocus">
	<div class="pic">
		<ul>
			<?php if(is_array($adlist)): $i = 0; $__LIST__ = $adlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ad): $mod = ($i % 2 );++$i;?><li>
					<a alt="<?php echo ($ad["ad_name"]); ?>" href="<?php echo ($ad["ad_link"]); ?>">
						<img thumb="" alt="<?php echo ($ad["ad_name"]); ?>" src="__PUBLIC__/Upload/adv/<?php echo ($ad["ad_code"]); ?>" style="width:<?php echo ($adp['ad_width']); ?>px;height:<?php echo ($adp['ad_height']); ?>px" />
					</a>
				</li><?php endforeach; endif; else: echo "" ;endif; ?>
			<?php if(empty($adlist)): ?><li>
					<a>
						<img thumb="" alt="吃货来了" src="__SKIN__/images/banner2.jpg" />
					</a>
				</li><?php endif; ?>
	  </ul>
	</div>
</div>