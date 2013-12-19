<?php if (!defined('THINK_PATH')) exit();?><div class="pageContent">
	<form method="post" action="__URL__/insert/navTabId/__MODULE__" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxMenu)">
		<input type="hidden" name="user_id" value="<?php echo $_SESSION[C('USER_AUTH_KEY')] ?>"/> 
		<input type="hidden" name="level" value="<?php echo ($level); ?>">
		<input type="hidden" name="pid" value="<?php echo ($pid); ?>">
		<input type="hidden" name="callbackType" value="closeCurrent" />
		<div class="pageFormContent" layoutH="58">
			<div class="unit">
				<label>模块名：</label>
				<input type="text" class="required alphanumeric"  name="name">
			</div>
			<div class="unit">
				<label>操作名：</label>
				<input type="text" class="alphanumeric"   name="action_name">
			</div>
			<div class="unit">
				<label>显示名：</label>
				<input type="text" class="required"   name="title">
			</div>
			
			<div class="unit">
				<label>分 组：</label>
				<SELECT name="group_id">
					<option value="">未分组</option>
					<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["title"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
				</SELECT>
			</div>
			<div class="unit">
				<label>状态：</label>
				<SELECT name="status">
					<option value="1">启用</option>
					<option value="0">禁用</option>
				</SELECT>
			</div>
			<div class="unit">
				<label>描 述：</label>
				<TEXTAREA name="remark"  rows="3" cols="57"></textarea>
			</div>
		</div>
		
		<div class="formBar">
			<ul>
				<li><div class="buttonActive"><div class="buttonContent"><button type="submit">保存</button></div></div></li>
				<li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
			</ul>
		</div>
	</form>

</div>