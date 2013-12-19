<?php if (!defined('THINK_PATH')) exit();?><div class="pageContent">
	<form method="post" action="__URL__/insert/navTabId/__MODULE__" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxMenu)">
		<input type="hidden" name="c_name_id" value="<?php echo $_SESSION['authId'] ?>"/> 
		<input type="hidden" name="level" value="<?php echo ($level); ?>">
		<input type="hidden" name="pid" value="<?php echo ($pid); ?>">
		<input type="hidden" name="callbackType" value="closeCurrent" />
		<div class="pageFormContent" layoutH="58">
			<div class="unit">
				<label>project_name：</label>
                                <!--alphanumeric -->
				<input type="text" class="required"  name="title">
			</div>
			<div class="unit">
				<label>project_code：</label>
				<input type="text" class="required"   name="code">
			</div>
			<div class="unit">
				<label>create_name：</label>
                                <input type="text" class="textInput readonly" readonly="true" name="c_name" value="<?php echo ($jobnumber); ?>">
			</div>
			<div class="unit">
				<label>Test Leader：</label>
				<!--<input type="text" class=""   name="p_cw">-->
                                <select name="p_cw">
                                    <?php if(is_array($userlist)): $i = 0; $__LIST__ = $userlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$u_list): $mod = ($i % 2 );++$i;?><option value="<?php echo ($u_list["id"]); ?>" <?php if(($u_list['id']) == $uid): ?>selected<?php endif; ?>><?php echo ($u_list['account']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
			</div>
                        <div class="unit">
				<label>Type：</label>
				<SELECT name="type">
                                    <?php if(is_array($typelist)): $i = 0; $__LIST__ = $typelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["title"]); ?>===><?php echo (getusergroupname($vo["group_id"])); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
				</SELECT>
			</div>
			<div class="unit">
				<label>所属内部组名：</label>
				<SELECT name="teamid">
                                    <option value=""></option>
                                    <?php if(is_array($allgroup)): $i = 0; $__LIST__ = $allgroup;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$allgroup): $mod = ($i % 2 );++$i;?><option value="<?php echo ($allgroup["id"]); ?>"><?php echo ($allgroup["nickname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
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