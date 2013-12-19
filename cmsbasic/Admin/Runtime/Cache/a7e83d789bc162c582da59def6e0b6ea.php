<?php if (!defined('THINK_PATH')) exit();?>	
<div class="accordion" fillSpace="sideBar">
		
		<?php if(is_array($grouplist)): $i = 0; $__LIST__ = $grouplist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="accordionHeader">
				<h2><span>Folder</span><?php echo ($vo["title"]); ?></h2>
			</div>
			<div class="accordionContent">
			
				<ul class="tree treeFolder">
					<?php if(is_array($vo['menu'])): $i = 0; $__LIST__ = $vo['menu'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i; if((strtolower($item['name'])) != "public"): if((strtolower($item['name'])) != "index"): if(($item['access']) == "1"): ?><li><a href="__APP__/<?php echo ($item['name']); ?>/<?php echo ($item['action']); ?>/" target="navTab" rel="<?php echo ($item['name']); ?>"><?php echo ($item['title']); ?></a></li><?php endif; endif; endif; endforeach; endif; else: echo "" ;endif; ?>
                                        <?php if($menugroupid == 2): if($vo['id'] == 9): if(is_array($teamlist)): $i = 0; $__LIST__ = $teamlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$team): $mod = ($i % 2 );++$i;?><li><a href="__APP__/AllRresult/summarize/teamid/<?php echo ($team['id']); ?>" target="navTab" rel="AllRresult<?php echo ($team['id']); ?>"><?php echo ($team['nickname']); ?></a></li><?php endforeach; endif; else: echo "" ;endif; endif; endif; ?>
				</ul>
                                
			</div>
                        
			<?php if($menugroupid == 2): if($vo['id'] == 3): ?><div class="accordionHeader">
						<h2><span>Folder</span>文章内容管理</h2>
					</div>
					<div class="accordionContent">
					
						<ul class="tree treeFolder">
							<?php if(is_array($artclist)): $i = 0; $__LIST__ = $artclist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$artc): $mod = ($i % 2 );++$i;?><li>
									<a href="__APP__/Article/index/cat_id/<?php echo ($artc['cat_id']); ?>" target="navTab" rel="Article">
										<?php echo ($artc['cat_name']); ?>
									</a>
								</li><?php endforeach; endif; else: echo "" ;endif; ?>
						</ul>

					</div><?php endif; endif; endforeach; endif; else: echo "" ;endif; ?>
	
</div>