<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Task Loading Management System</title>
<script src="__PUBLIC__/js/jquery.js" type="text/javascript" ></script>
<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/admin/css/register.css"/>
<script language="JavaScript">
<!--
function fleshVerify(type){ 
	//重载验证码
	var timenow = new Date().getTime();
	if (type){
		$('#d').attr("src", '__URL__/verify/adv/1/'+timenow);
	}else{
		$('#d').attr("src", '__URL__/verify/'+timenow);         
	}
}
$(document).ready(function(){
	fleshVerify();
});
//-->
</script>
</head>
<body>
<div class='signup_container'>
 <form class="signup_form_form" id="signup_form" method="post" action="__URL__/checkLogin/" data-secure-action="" data-secure-ajax-action="">
    <h1 class='signup_title'>任务管理系统登录</h1>
    <!--<img src='__PUBLIC__/css/admin/images/people.png' id='admin'/>-->
    <div id="signup_forms" class="signup_forms clearfix">
           
                    <div class="form_row first_row">
                        <label for="signup_email">请输入用户名</label>
                        <input type="text" name="account" placeholder="请输入用户名" id="signup_name" data-required="required">
                    </div>
                    <div class="form_row">
                        <label for="signup_password">请输入密码</label>
                        <input type="password" name="password" placeholder="请输入密码" id="signup_password" data-required="required">
                    </div>
                    <div class="form_row">
                            <input id="signup_select" type="text" name="verify" placeholder="输入验证码" value='' data-required="required">
                            <img id="d" src='' onClick="fleshVerify()" border="0" alt="点击刷新验证码" style="cursor:pointer"/>
                    </div>
           
    </div>

    <div class="login-btn-set"><input type="submit" class='login-btn' value="" /></div>
    <p class='copyright'>版权所有 wistron QT</p>
	</form>
</div>

</body>
</html>