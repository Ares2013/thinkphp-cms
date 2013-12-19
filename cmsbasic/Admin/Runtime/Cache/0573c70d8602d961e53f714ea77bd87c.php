<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<title><?php echo (C("sitename")); ?></title>

<link href="__PUBLIC__/dwz/themes/azure/style.css" rel="stylesheet" type="text/css" />
<link href="__PUBLIC__/dwz/themes/css/core.css" rel="stylesheet" type="text/css" />
<!--<link href="__PUBLIC__/dwz/themes/css/table.css" rel="stylesheet" />-->
<!--[if IE]>
<link href="themes/css/ieHack.css" rel="stylesheet" type="text/css" media="screen"/>
<![endif]-->
<style type="text/css">
	#header{height:85px}
	#leftside, #container, #splitBar, #splitBarProxy{top:90px}
	.main_top {
		background: none repeat scroll 0 0 #FFFCED;
		border: 1px solid #FFBE7A;
		line-height: 20px;
		padding: 10px;
	}
        h1{
            font-size:12px;
        }
</style>

<script src="__PUBLIC__/dwz/js/speedup.js" type="text/javascript"></script>
<script src="__PUBLIC__/js/jquery.js" type="text/javascript"></script>
<script src="__PUBLIC__/dwz/js/jquery.cookie.js" type="text/javascript"></script>
<script src="__PUBLIC__/dwz/js/jquery.validate.js" type="text/javascript"></script>
<script src="__PUBLIC__/dwz/js/jquery.bgiframe.js" type="text/javascript"></script>
<script src="__PUBLIC__/xheditor/xheditor.min.js" type="text/javascript"></script>
<script src="__PUBLIC__/xheditor/xheditor_lang/zh-cn.js" type="text/javascript"></script>
<script src="__PUBLIC__/dwz/js/dwz.min.js" type="text/javascript"></script>
<script src="__PUBLIC__/dwz/js/dwz.regional.zh.js" type="text/javascript"></script>

<!--<script type="text/javascript" src="__PUBLIC__/dwz/js/json.js"></script>-->
<script type="text/javascript" src="__PUBLIC__/dwz/js/jquery.pngFix.js"></script>

<!-- svg图表  supports Firefox 3.0+, Safari 3.0+, Chrome 5.0+, Opera 9.5+ and Internet Explorer 6.0+ -->
<script type="text/javascript" src="__CHARTT__/rapheal/raphael.js"></script>
<script type="text/javascript" src="__CHARTT__/rapheal/g.raphael.js"></script>
<script type="text/javascript" src="__CHARTT__/rapheal/g.bar.js"></script>
<script type="text/javascript" src="__CHARTT__/rapheal/g.line.js"></script>
<script type="text/javascript" src="__CHARTT__/rapheal/g.pie.js"></script>
<script type="text/javascript" src="__CHARTT__/rapheal/g.dot.js"></script>


<script type="text/javascript">
<!--
//指定当前组模块URL地址 
var URL = '__URL__';
var ROOT_PATH = '__ROOT__';
var APP	 =	 '__APP__';
var STATIC = '__TMPL__Static';
var VAR_MODULE = '<?php echo c('VAR_MODULE');?>';
var VAR_ACTION = '<?php echo c('VAR_ACTION');?>';
var CURR_MODULE = '<?php echo ($module_name); ?>';
var CURR_ACTION = '<?php echo ($action_name); ?>';

//定义JS中使用的语言变量
var CONFIRM_DELETE = '{%CONFIRM_DELETE}';
var AJAX_LOADING = '{%AJAX_LOADING}';
var AJAX_ERROR = '{%AJAX_ERROR}';
var ALREADY_REMOVE = '{%ALREADY_REMOVE}';
var SEARCH_LOADING = '{%SEARCH_LOADING}';
var CLICK_EDIT_CONTENT = '{%CLICK_EDIT_CONTENT}';
//-->
</script>

<script type="text/javascript">
function fleshVerify(){
	//重载验证码
	$('#verifyImg').attr("src", '__APP__/Public/verify/'+new Date().getTime());
}
function dialogAjaxMenu(json){
	dialogAjaxDone(json);
	if (json.statusCode == DWZ.statusCode.ok){
		//$("#sidebar").loadUrl("__APP__/Public/menu");
	}
}
function navTabAjaxMenu(json){
	//navTabAjaxDone(json);
	if (json.status == 301){
		window.local.href="__APP__/Public/login";
	}
	else
	{
		$("#sidebar").loadUrl("__APP__/Public/menu");
	}
}
$(function(){
	DWZ.init("__PUBLIC__/dwz/dwz.frag.xml", {
		loginUrl:"__APP__/Public/login_dialog", loginTitle:"登录",	// 弹出登录对话框
//		loginUrl:"__APP__/Public/login",	//跳到登录页面
		statusCode:{ok:1,error:0},
		pageInfo:{pageNum:"pageNum", numPerPage:"numPerPage", orderField:"_order", orderDirection:"_sort"}, //【可选】
		debug:false,	// 调试模式 【true|false】
		callback:function(){
			initEnv();
			$("#themeList").theme({themeBase:"__PUBLIC__/dwz/themes"});
		}
	});
});
</script>

</head>

<body scroll="no">
	<div id="layout">
		<div id="header">
			<div class="headerNav">
				<a class="logo" href="__APP__"></a>
				<ul class="nav">
					<!--<li><a href="__ROOT__/" target="_blank" width="580" height="360" rel="sysInfo">前台首页</a></li>>-->
                                        <li><a href="__APP__/Public/main" target="dialog" width="580" height="360" rel="sysInfo" title="用户消息">用户信息</a></li>
					<li><a href="__APP__/Public/password/" target="dialog" mask="true">修改密码</a></li>
					<li><a href="__APP__/Public/profile/" target="dialog" mask="true">修改资料</a></li>
					<li><a href="__APP__/Public/logout/">退出</a></li>
				</ul>
				<ul class="themeList" id="themeList">
					<!--<li theme="default"><div>蓝色</div></li>
					<li theme="green"><div>绿色</div></li>
					<li theme="purple"><div>紫色</div></li>
					<li theme="azure"><div class="selected">天蓝</div></li>-->
				</ul>
			</div>
			<div id="navMenu">
				<ul>
				    <?php if(is_array($navlist)): $i = 0; $__LIST__ = $navlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vl): $mod = ($i % 2 );++$i;?><li class=""><a href="<?php echo U('Public/menu',array('id'=>$key));?>"><span><?php echo ($vl); ?></span></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
					<!--<li class=""><a href="sidebar_1.html"><span>资讯管理</span></a></li>
					<li class=""><a href="sidebar_2.html"><span>订单管理</span></a></li>
					<li class=""><a href="sidebar_1.html"><span>产品管理</span></a></li>-->
					<!--<li class="selected"><a href="sidebar_2.html"><span>会员管理</span></a></li>
					<li class=""><a href="sidebar_1.html"><span>服务管理</span></a></li>
					<li class=""><a href="sidebar_2.html"><span>系统设置</span></a></li>-->
				</ul>
			</div>
		</div>
		
		<div id="leftside">
			<div id="sidebar_s">
				<div class="collapse">
					<div class="toggleCollapse"><div></div></div>
				</div>
			</div>
			
			<div id="sidebar">
					
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

			</div>
		</div>

		<div id="container">
			<div id="navTab" class="tabsPage">
				<div class="tabsPageHeader">
					<div class="tabsPageHeaderContent"><!-- 显示左右控制时添加 class="tabsPageHeaderMargin" -->
						<ul class="navTab-tab">
							<li tabid="main" class="main"><a href="javascript:void(0)"><span><span class="home_icon">我的主页</span></span></a></li>
						</ul>
					</div>
					<div class="tabsLeft">left</div><!-- 禁用只需要添加一个样式 class="tabsLeft tabsLeftDisabled" -->
					<div class="tabsRight">right</div><!-- 禁用只需要添加一个样式 class="tabsRight tabsRightDisabled" -->
					<div class="tabsMore">more</div>
				</div>
				<ul class="tabsMoreList">
					<li><a href="javascript:void(0)">我的主页</a></li>
				</ul>
				<div class="navTab-panel tabsPageContent layoutBox">
					<div class="page unitBox">
						<div class="accountInfo">
							<div class="alertInfo">
							</div>
							<div class="right">
								<p><?php echo (date('Y-m-d g:i a',time())); ?></p>
							</div>
                                                    <p>Welcome, 【<a href="__APP__/Public/main" target="dialog" rel="sysInfo" title="用户消息" style="color:red;font-weight: bold;"><?php echo ($loginUserName); ?></a>】登陆本系统！</p>
                                                        
						</div>
						<div class="pageFormContent" layoutH="80">
							<div style="clear:both;margin-bottom: 10px;" class="main_top">
		                        <?php if($no_security_info == 1): ?><h4>安全提示</h4>
									<?php if(is_array($security_info)): $i = 0; $__LIST__ = $security_info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><p style="font-size:14px;clear:both;padding:10px;color:#ff0000;float:none;" class="red" style="font-size:14px;">※　<?php echo ($v); ?></p><?php endforeach; endif; else: echo "" ;endif; endif; ?>
								<h4>网站无法显示</h4>
								<p class="green" style="clear:both;padding:10px;float:none;color:#0E774A;">
									网站以前正常显示，现在无法显示请清除缓存试试，【<a target="ajaxTodo" href="/Admin/index.php/System/clear/navTabId/System" style="color:#367ABB; text-decoration: none;">更新缓存</a>】
								</p>
							</div>
							<div class="panel [close collapse]" [defH="200"|minH=”100”] style="">
								  <h1>配置信息</h1>
								  <div>
										<?php if(is_array($server_info)): $i = 0; $__LIST__ = $server_info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><p style="clear:both;padding:10px;float:none;"><?php echo ($key); ?> : <?php echo ($v); ?></p><?php endforeach; endif; else: echo "" ;endif; ?>
								  </div>
							</div>
							<div class="divider"></div>
						</div>

					</div>
				</div>
			</div>
		</div>

	</div>
	
	<div id="footer">Copyright &copy; wistron QT—<?php echo ($Year); ?> <!--<a href="mailto:Ares Peng@Wistron.com" target="_blank">Ares Peng</a>--></div>


</body>
</html>