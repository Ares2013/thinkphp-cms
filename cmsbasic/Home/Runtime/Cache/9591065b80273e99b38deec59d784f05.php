<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html xmlns:wb=“http://open.weibo.com/wb”>
<head>
<meta charset="utf-8" />
<meta name="keywords" content="<?php echo ($seo["keywords"]); ?>">
<meta name="description" content="<?php echo ($seo["desc"]); ?>">
    
<meta http-equiv="x-ua-compatible" content="ie=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1" />
<title><?php echo ($cfg["shop_name"]); ?></title>
<!--[if lt IE 9]>
  <script src="__PUBLIC__/css/unsemantic-master/assets/javascripts/html5.js"></script>
<![endif]-->
<link rel="stylesheet" href="__PUBLIC__/css/reset.css" />
<!--[if (gt IE 8) | (IEMobile)]><!-->
  <link rel="stylesheet" href="__PUBLIC__/css/unsemantic-master/assets/stylesheets/unsemantic-grid-responsive.css" />
<!--<![endif]-->
<!--[if (lt IE 9) & (!IEMobile)]>
  <link rel="stylesheet" href="__PUBLIC__/css/unsemantic-master/assets/stylesheets/ie.css" />
<![endif]-->
<script src="http://tjs.sjs.sinajs.cn/open/api/js/wb.js" type="text/javascript" charset="utf-8"></script>
<script src="__PUBLIC__/js/jquery.js"></script>
<script>
	jQuery.noConflict();
</script>
<link rel="shortcut icon" href="favicon.ico" />
<link href="__SKIN__/css/global.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="__PUBLIC__/css/reset.css" />
<link rel="stylesheet" href="__SKIN__/css/basic.css" />
<link rel="stylesheet" href="__SKIN__/css/layout.css" />
<link rel="stylesheet" href="__SKIN__/css/skin.css" />
<?php if(!empty($page)): ?><link rel="stylesheet" href="__SKIN__/css/pagination.css" /><?php endif; ?>

<link href="__SKIN__/css/online_qq.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
	(function($){
		$(document).ready(function(){

			$("#floatShow").bind("click",function(){
			
				$("#onlineService").animate({width:"show", opacity:"show"}, "normal" ,function(){
					$("#onlineService").show();
				});
				
				$("#floatShow").attr("style","display:none");
				$("#floatHide").attr("style","display:block");
				
				return false;
			});
			
			$("#floatHide").bind("click",function(){
			
				$("#onlineService").animate({width:"hide", opacity:"hide"}, "normal" ,function(){
					$("#onlineService").hide();
				});
				
				$("#floatShow").attr("style","display:block");
				$("#floatHide").attr("style","display:none");
				
				return false;
			});
		  
		});
	})(jQuery);
</script>



</head>
<body>
<div id="top_box">
	<div class="grid-container">
		<div class="grid-100 mobile-grid-100">
			<a href="<?php echo U('User/login');?>" title="登录">
				登录
			</a>
			|
			<a href="<?php echo U('User/register');?>" title="注册">
				注册
			</a>
		 </div>
	</div>
</div>
<div id="logo_box">
	<div class="grid-container">
		<div class="c">
			<a href="<?php echo U('Index/index');?>" title="<?php echo ($cfg["shop_name"]); ?>">
				<img src="__SKIN__/images/logo.gif" alt="<?php echo ($cfg["shop_name"]); ?>" />
			</a>
			<div style="float:right;padding:20px;">
				<iframe src="http://follow.v.t.qq.com/index.php?c=follow&a=quick&name=chinasoftstar&style=5&t=1373772836622&f=1" frameborder="0" scrolling="auto" width="178" height="24" marginwidth="0" marginheight="0" allowtransparency="true"></iframe>
				<wb:follow-button uid="1397050420" type="red_2" width="136" height="24" ></wb:follow-button>
			</div>
			<div style="float:right;padding:20px;">
				<a target="_blank" href="https://plus.google.com/111133576622046575806" rel="publisher">Google+</a>
			</div>
			
		</div>
	</div>
</div>
<div class="clear">
</div>
<div id="nav_box">
	<div class="grid-container">
		<div class="grid-100">
			<div class="container">
				<ul>
					<?php echo W('NavBox',array('cat_id'=>1,'number'=>8));?>
				</ul>
			</div>
		</div>
	</div>
</div>
<div id="notice_box">
	<div class="grid-container">
		<div class="grid-100">
			<div class="c container">
				668自由云，专业的互联网络解决方案、完善的售后服务体系－主要业务：<a href="http://www.6681517.com/" target="_blank"><strong>网站建设</strong></a>,<a href="http://www.6681517.com/" target="_blank"><strong>软件开发</strong></a>，手机客户端开发.
			</div>
		</div>
	</div>
</div>
<link href="__SKIN__/css/index.css" rel="stylesheet" type="text/css"/>
<link href="__SKIN__/css/box.css" rel="stylesheet" type="text/css"/>
<link href="__SKIN__/css/pagination.css" rel="stylesheet" type="text/css"/>
<div id="art_submain">
	<div class="grid-container">
		<div class="container">
			<div class="grid-25 mobile-grid-100">
				<div class="left_menu boxa">
					<div class="t">
						<?php echo ($cat["cat_name"]); ?> 
					</div>
					<div class="c">
						<ul>
							<?php echo W('ArtListBox',array('cat_id'=>$cat['cat_id'],'top'=>10,'length'=>10,'template'=>'nodate'));?>
						</ul>
					</div>
					<div class="f">
					</div>
				</div>
				<div class="left_menu boxa">
					<div class="t">
						新闻资讯
					</div>
					<div class="c">
						<ul>
							<?php echo W('ArtListBox',array('cat_id'=>'3','top'=>10,'length'=>26,'template'=>'nodate'));?>
						</ul>
					</div>
					<div class="f">
					</div>
				</div>
			</div>
			<div class="grid-75 mobile-grid-100">
				<div id="artpage_content">
					<div class="title">
						<?php echo ($cat["cat_name"]); ?> 
					</div>
					<div class="content">
						<ul>
						 <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>

								<a alt="<?php echo ($vo["title"]); ?>" href="<?php echo U('Article/view',array('id'=>$vo['article_id']));?>" >
									<?php echo (msubstr(str_replace('　', '',strip_tags($vo["title"])),0,100)); ?>
								</a>
							</li><?php endforeach; endif; else: echo "" ;endif; ?>
						</ul>
					</div>
					<div class="page">
						<?php echo ($page); ?>	
					 </div>
				</div>
			</div>
			<div class="clear">
			</div>
		</div>
	</div>
</div>
<div class="clear">
</div>
<div id="footer_box">
	<div class="grid-container">
		<!--<div id="help_box" class="grid-100">
			<ul class="grid-25 mobile-grid-50">
				<li class="item first">
					<h5>
						帮助中心
					</h5>
				</li>
				<?php $__FOR_START_13860408__=0;$__FOR_END_13860408__=10;for($i=$__FOR_START_13860408__;$i < $__FOR_END_13860408__;$i+=1){ ?><li class="item">
						<a href="#"  alt="">
							帮助中心<?php echo ($i); ?>
						</a>
					</li><?php } ?>
			</ul>
			<ul class="grid-25 mobile-grid-50">
				<li class="item first">
					<h5>
						帮助中心
					</h5>
				</li>
				<?php $__FOR_START_1469412961__=0;$__FOR_END_1469412961__=10;for($i=$__FOR_START_1469412961__;$i < $__FOR_END_1469412961__;$i+=1){ ?><li class="item">
						<a href="#"  alt="">
							帮助中心<?php echo ($i); ?>
						</a>
					</li><?php } ?>
			</ul>
			<ul class="grid-25 mobile-grid-50">
				<li class="item first">
					<h5>
						帮助中心
					</h5>
				</li>
				<?php $__FOR_START_906482711__=0;$__FOR_END_906482711__=10;for($i=$__FOR_START_906482711__;$i < $__FOR_END_906482711__;$i+=1){ ?><li class="item">
						<a href="#"  alt="">
							帮助中心<?php echo ($i); ?>
						</a>
					</li><?php } ?>
			</ul>
			<ul class="grid-25 mobile-grid-50">
				<li class="item first">
					<h5>
						帮助中心
					</h5>
				</li>
				<?php $__FOR_START_730990154__=0;$__FOR_END_730990154__=10;for($i=$__FOR_START_730990154__;$i < $__FOR_END_730990154__;$i+=1){ ?><li class="item">
						<a href="#"  alt="">
							帮助中心<?php echo ($i); ?>
						</a>
					</li><?php } ?>
			</ul>
			<div class="clear">
			</div>-->
		</div>
		<div class="clear">
		</div>
		<div id="friend_box" class="grid-100">
				<a href="#"  alt="">
					关于668
				</a>
				&nbsp;|&nbsp;
				<a href="#"  alt="">
					加入我们
				</a>
				&nbsp;|&nbsp;
				<a href="#"  alt="">
					运营合作
				</a>
				&nbsp;|&nbsp;
				<a href="#"  alt="">
					资本注入
				</a>
				&nbsp;|&nbsp;
				<a href="#"  alt="">
					网站地图
				</a>
				&nbsp;|&nbsp;
				<a href="#"  alt="">
					意见反馈
				</a>
				&nbsp;|&nbsp;
				<a href="#"  alt="">
					手机668
				</a>
		</div>
		<div class="clear" class="grid-100">
		</div>
		<div id="copyright_box" class="grid-100">
			<p>客服电话：<?php echo ($cfg["service_phone"]); ?>   客服QQ：<?php echo ($cfg["qq"]); ?>  E-mail：<?php echo ($cfg["service_email"]); ?></p>
			<p><?php echo ($cfg["ww"]); ?> Network © 2006-2013 6681517  <script type="text/javascript" src="http://tajs.qq.com/stats?sId=13446796" charset="UTF-8"></script></p>
			<p>备案号 :  <?php echo ($cfg["icp_number"]); ?></p>
		</div>
		<div class="clear">
		</div>
	</div>
</div>
<div id="online_qq_layer">
	<div id="online_qq_tab">
		<a class="qq_tab" id="floatShow" style="display:block;" href="javascript:void(0);">收缩</a> 
		<a class="qq_tab" id="floatHide" style="display:none;" href="javascript:void(0);">展开</a>
	</div>
	<div id="onlineService">
		<div class="onlineMenu">
			<h3 class="tQQ">QQ在线客服</h3>
			<ul>
				<li class="tli zixun">在线咨询</li>
				<li><a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=416121203&site=qq&menu=yes"><img src="__SKIN__/images/kefu/pa.gif" width="74" height="22" alt="客服001" /></a></li>
				<li><a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=469914070&site=qq&menu=yes"><img src="__SKIN__/images/kefu/pa.gif" width="74" height="22" alt="客服001" /></a></li>
				<!--<li class="tli fufei">付费学员</li>
				<li class="last"><a href="#"><img src="__SKIN__/images/kefu/pa.gif" width="74" height="22" alt="客服001" /></a></li>-->
			</ul>
		</div>
		<!--<div class="wyzx">
			<a href="#"><img src="__SKIN__/images/kefu/right_float_web.png" width="122" height="50" alt="网页咨询" /></a>
		</div>-->
		<div class="onlineMenu">
			<h3 class="tele">QQ在线客服</h3>
			<ul>
				<li class="tli phone">
					<script src="http://skype.tom.com/script/skypeCheck40.js" type="text/javascript"></script>
					<a onclick="return skypeCheck();" href="skype:liuliubaniwoyiqi?call" class="ldgt"><em></em>来电沟通</a>
				</li>
				<!--<li class="last"><a class="newpage" href="#">意见反馈</a></li>-->
			</ul>
		</div>
		<div class="btmbg"></div>
	</div>
</div>
<!-- Baidu Button BEGIN -->
<script type="text/javascript" id="bdshare_js" data="type=slide&amp;img=2&amp;pos=left&amp;uid=6758106" ></script>
<script type="text/javascript" id="bdshell_js"></script>
<script type="text/javascript">
var bds_config={"bdTop":189,"snsKey":"{'tsina':'3401072010','tqq':'801071718','t163':'','tsohu':''}"};
document.getElementById("bdshell_js").src = "http://bdimg.share.baidu.com/static/js/shell_v2.js?cdnversion=" + Math.ceil(new Date()/3600000);
</script>
<!-- Baidu Button END -->

</body>
</html>