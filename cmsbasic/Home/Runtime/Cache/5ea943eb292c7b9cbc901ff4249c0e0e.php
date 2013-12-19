<?php if (!defined('THINK_PATH')) exit(); if(is_array($artlist)): $i = 0; $__LIST__ = $artlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
		<a href="<?php echo U('Article/view',array('id'=>$vo['article_id']));?>"><?php echo ($vo["title"]); ?></a>
	</li><?php endforeach; endif; else: echo "" ;endif; ?>