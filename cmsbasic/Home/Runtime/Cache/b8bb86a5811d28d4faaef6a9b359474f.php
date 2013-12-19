<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta http-equiv="X-UA-Compatible" content="IE=7" /><style type="text/css"><!--
*{ padding:0; margin:0; font-size:12px}
a:link,a:visited{text-decoration:none;color:#0068a6}
a:hover,a:active{color:#ff6600;text-decoration: underline}
.showMsg{border: 1px solid #1e64c8; zoom:1; width:450px; height:172px;position:absolute;top:44%;left:50%;margin:-87px 0 0 -225px}
.showMsg h5{background-image: url(__ROOT__/Public/images/msg.png);background-repeat: no-repeat; color:#fff; padding-left:35px; height:25px; line-height:26px;*line-height:28px; overflow:hidden; font-size:14px; text-align:left}
.showMsg .content{ padding:46px 12px 10px 45px; font-size:14px; height:64px; text-align:left}
.showMsg .bottom{ background:#e4ecf7; margin: 0 1px 1px 1px;line-height:26px; *line-height:30px; height:26px; text-align:center}
.showMsg .ok,.showMsg .guery{background: url(__ROOT__/Public/images/msg_bg.png) no-repeat 0px -560px ;}
.showMsg .guery{ background-position: left -460px;}
--></style><script language="javascript" type="text/javascript" src="__ROOT__/Public/js/jquery.js"></script><script type="text/javascript">	var totalsecond=parseInt("<?php echo ($waitSecond); ?>");
	var url="<?php echo ($jumpUrl); ?>";
	function redirect()
	{
		if(totalsecond==0)
		{
			window.location.href=url;
		}
		else
		{
			totalsecond=totalsecond-1;
			$("#waitsecond").html(totalsecond);
		}
	}
</script><title>信息提示页面</title></head><body><div class="showMsg" style="text-align:center"><h5><?php echo ($msgTitle); ?></h5><?php if(isset($message)): ?><div class="content guery" style="display:inline-block;display:-moz-inline-stack;zoom:1;*display:inline;max-width:330px;"><?php echo ($message); ?></div><?php endif; ?><div class="bottom"><?php if(isset($closeWin)): ?><input type="button" name="close" value="<?php echo ($lang["close"]); ?>" onClick="window.close();"><?php endif; if(isset($jumpUrl)): ?>系统将在 <span id="waitsecond" style="color:blue;font-weight:bold"><?php echo ($waitSecond); ?></span>秒后自动跳转，如果不想等待，直接点击<a href="<?php echo ($jumpUrl); ?>">这里</a><script language="javascript">		setInterval("redirect();",1*1000);
    </script><?php endif; if(isset($returnjs)): ?><script style="text/javascript"><?php echo ($returnjs); ?>;</script><?php endif; if(isset($dialog)): ?><script style="text/javascript">	var dialog = "<?php echo ($dialog); ?>";
	if (dialog!='') {
		window.top.right.location.reload();window.top.art.dialog({id:dialog}).close();
	}
    </script><?php endif; ?></div></div></body></html>