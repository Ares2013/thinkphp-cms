<?php if (!defined('THINK_PATH')) exit();?><div class="pageContent">
    
    <form method="post" action="__URL__/insert/navTabId/CreateTask"  class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxMenu)">
		<!--<input type="hidden" name="c_name_id" value="<?php echo $_SESSION['authId'] ?>"/> 
		<input type="hidden" name="level" value="<?php echo ($level); ?>">-->
		<input type="hidden" name="project_id" value="<?php echo ($pid); ?>">
                <input type="hidden" value="closeCurrent" name="callbackType">
		<div class="pageFormContent" layoutH="58">
			<div class="unit">
				<label>project_name：</label>
                                <!--alphanumeric -->
                                <input type="text" class="required"  name="title" value="<?php echo ($pname); ?>" disabled="disabled">
			</div>
			<div class="unit">
				<label>Task name：</label>
				<input type="text" class="required"   name="task_title">
			</div>
			<div class="unit">
				<label>Attend Time：</label>
				<input type="text" class="digits textInput required" name="est_time">
                                <span class="info">(单位：Hour)</span>
			</div>
                        <div class="unit">
				<label>Testing Ratios：</label>
				<input type="text" class="digits textInput valid required" name="ratios">
                                <span class="info">(单位：%,Testing Ratios)</span>
			</div>
                        <div class="unit">
				<label>schedule-start：</label>
                                <input type="text" class="date textInput valid" name="schedule_start" />
                                <a href="javascript:;" class="inputDateButton">选择</a>
                                <span class="info">yyyy-MM-dd</span>
			</div>
                        <div class="unit">
				<label>schedule-end：</label>
                                <input type="text" class="date textInput valid" name="schedule_end" />
                                <a href="javascript:;" class="inputDateButton">选择</a>
                                <span class="info">yyyy-MM-dd</span>
			</div>
                        <div class="unit">
				<label>Type：</label>
				<SELECT name="task_type">
                           <?php if(is_array($typelist)): $i = 0; $__LIST__ = $typelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$volist): $mod = ($i % 2 );++$i;?><option value="<?php echo ($volist["id"]); ?>"><?php echo ($volist["title"]); ?>===><?php echo (getusergroupname($volist["group_id"])); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
				</SELECT>
			</div>
                        <div class="unit">
				<label>Task Leader：</label>
				<!--<input type="text" class=""   name="p_cw">-->
                                <select name="task_leader">
                                    <option value="0"></option>
                                    <?php if(is_array($userlist)): $i = 0; $__LIST__ = $userlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$u_list): $mod = ($i % 2 );++$i;?><option value="<?php echo ($u_list["id"]); ?>" <?php if(($u_list['id']) == $uid): ?>selected<?php endif; ?>><?php echo ($u_list['account']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                                <span class="info">有需要的小组请填写</span>
			</div>
			<div class="unit">
				<label>状态：</label>
				<SELECT name="status">
					<option value="1">Ongoing</option>
					<option value="0">Finished</option>
                                        <option value="2">Death</option>
				</SELECT>
			</div>
                        <div class="unit">
				<label>是否完成：</label>
				<SELECT name="finish">
					<option value="0">未完成</option>
					<option value="1">完成</option>
				</SELECT>
			</div>
                        <div class="unit">
                            <label>Member：</label>
				<input type="hidden" name="orgLookup.id" value="${orgLookup.id}"/>
				<input type="text" class="required" name="orgLookup.orgName" value="" suggestFields="orgNum,orgName" suggestUrl="demo/database/db_lookupSuggest.html" lookupGroup="orgLookup" />
				<a class="btnLook" href="__URL__/OrgLookup/" lookupGroup="orgLookup" title="添加Member的列表页" mask="true">查找带回</a>
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