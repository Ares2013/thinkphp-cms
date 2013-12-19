<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="心澜设计师个人网站">
<meta name="description" content="心澜设计师个人网站">
<title>心澜设计师个人网站</title>
<link href="__PUBLIC__/css/reset.css" rel="stylesheet" type="text/css" />
<link href="__PUBLIC__/css/960_24.css" rel="stylesheet" type="text/css" />
<link href="__SKIN__/css/basic.css" rel="stylesheet" type="text/css" />
<link href="__SKIN__/css/globe.css" rel="stylesheet" type="text/css" />
</head>
<body>
<!--页面头部模块-->
<div class="header">
  <div class="container_24">
    <div class="grid_5"> <img src="__SKIN__/images/top-logo.gif" alt="logo" width="183" height="67" class="logo"/> </div>
    <div class="grid_12">
      <ul>
        <li><a href="/index.html">首页</a></li>
        <li><a href="<?php echo U('Article/page');?>">简介</a></li>
        <li><a href="<?php echo U('Article/index',array('id'=>1));?>">作品</a></li>
        <li><a href="">文章</a></li>
      </ul>
    </div>
    <div class="grid_7">
      <div id="search_box">
        <form name="" action="" method="post">
          <input type="text" name="" class="txt" value="" placeholder=" 搜索站内内容"/><input type="submit" class="btn" value="" />
          
        </form>
      </div>
    </div>
    <div class="clear"> </div>
  </div>
</div>
<!--内容-->
<div class="container_24">
  <div class="grid_24">
	<?php echo W('AdvBox',array('id'=>1));?>
  </div>
  <div class="clear"></div>
  <div class="line">
    <div class="title"> </div>
  </div>
  <div class="clear"></div>
  <div class="works">
    <?php if(is_array($artlist)): $i = 0; $__LIST__ = $artlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div <?php if($i == 0): ?>class="grid_7 alpha" style="width:314px;"<?php elseif($i == 1): ?> class="grid_7" style="width:314px;margin:0px 4px;" <?php else: ?> class="grid_7 omega" style="width:314px;"<?php endif; ?>> 
		<a class="" href="<?php echo U('Article/view',array('id'=>$vo['article_id']));?>" title="<?php echo ($vo["title"]); ?>">
			<img src="__PUBLIC__/Upload/article/<?php echo ($vo["file_url"]); ?>" alt="<?php echo ($vo["title"]); ?>" style="314px;height:235px;"/>
		</a>
	 </div><?php endforeach; endif; else: echo "" ;endif; ?>
    <div class="clear"></div>
  </div>
  <div class="desc">
    <div class="clear"></div>
    <div class="grid_7 alpha" style="width:314px;">
      <div class="desc_text">
      <h3 class="desc_title">绘画作品</h3>
      <div style="padding:10px;line-height:1.2em;">
      <p> 07年步入大学校门，第一次拿起艺术的画笔，踏上了我的设计生涯。虽然现如今，绘图软件比比皆是，但是我相信一个好的设计师必须拥有一定的绘画功底。这幅作品是大学时期临摹的一幅室内设计作品，主要是用彩铅绘画而成，不过遗憾的是现在只能看到照片了，原图被老师留下了！
      </p>
      </div>
      
      </div>
    </div>
    <div class="grid_7" style="width:314px;margin:0px 4px;" >
      <div class="desc_text">
      	<h3 class="desc_title">网站作品</h3>
      <div style="padding:10px;line-height:1.2em;">
      <p> 这个是为668自由云做的网站设计，668自由云主要是专门承接网站建设，软件开发，手机客户端开发
等项目！主色主要采用了红色，整个网站以简介大方为主，以确保顾客再第一时间能看到有效信息！
      </p>
      </div>
      </div>
    </div>
    <div class="grid_7 omega" style="width:314px;">
      <div class="desc_text">
      		<h3 class="desc_title">其它作品</h3>
      <div style="padding:10px;line-height:1.2em;">
      <p>
      	除了手绘，网站作品之外，还整理上传了其它的一些作品，包括手机APP等！还有根据百度Mac输入法皮肤大赛，比赛规则设计的百度输入法皮肤。以西瓜为主题，采用红绿对比色，视觉上更有冲击力！
      </p>
      </div>
      </div>
    </div>
  </div>
</div>
<div class="clear"></div>
<div class="footer">
  <div class="container_24">
    <div class="grid_9">
      <div class="footer_logo"><img src="__SKIN__/images/bottom-logo.gif" alt="logo" width="232" height="63"/ > </div>
    </div>
    <div class="grid_5">
      <div>
        <dl>
          <dt>页面</dt>
          <dd>简介</dd>
          <dd>作品</dd>
          <dd>文章</dd>
        </dl>
      </div>
    </div>
    <div class="grid_5">
      <div>
        <dl>
          <dt>链接表</dt>
          <dd>668自由云</dd>
        </dl>
      </div>
    </div>
    <div class="grid_5">
      <div>
        <dl>
          <dt>文章</dt>
          <dd>如何做好banner</dd>
          <dd>网络转载</dd>
        </dl>
      </div>
    </div>
  </div>
</div>
</body>
</html>