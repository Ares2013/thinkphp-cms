<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" action="__URL__/child/" method="post">
	<input type="hidden" name="pageNum" value="1"/>
	<input type="hidden" name="_order" value="<?php echo ($_REQUEST["_order"]); ?>"/>
	<input type="hidden" name="_sort" value="<?php echo ($_REQUEST["_sort"]); ?>"/>
	<input type="hidden" name="pid" value="<?php echo ($_REQUEST["pid"]); ?>"/>
        <input type="hidden" value="closeCurrent" name="callbackType">
</form>
<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="__URL__/child/" method="post">
            <input type="hidden" name="pid" value="<?php echo ($_REQUEST["pid"]); ?>"/>
            <div class="searchBar">
		<ul class="searchContent">
			<li>
				<label>Task名称：</label>
				<input type="text" name="task_title" class="medium" value="<?php echo ($_POST['task_title']); ?>">
			</li>
                        <li>
				<label>team名称：</label>
				<!--<input type="text" name="task_title" class="medium" value="<?php echo ($_POST['task_title']); ?>">-->
                                <select name="teamid">
                                    <option value=""></option>
                                    <?php if(is_array($allgroup)): $i = 0; $__LIST__ = $allgroup;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$allgroup): $mod = ($i % 2 );++$i;?><option value="<?php echo ($allgroup["id"]); ?>" <?php if(($allgroup['id']) == $_POST['teamid']): ?>selected<?php endif; ?>><?php echo ($allgroup["nickname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
			</li>
		</ul>
		<div class="subBar">
			<ul>
				<li><div class="buttonActive"><div class="buttonContent"><button type="submit">查询</button></div></div></li>
			</ul>
		</div>
	</div>
	</form>
</div>
<div class="pageContent">
	<div class="panelBar">
		<ul class="toolBar">
			<!--在新增页面内新增查找带回的功能-->
                        <!--<li><a class="add" href="__URL__/childadd/pid/<?php echo ($projectid); ?>/tid/<?php echo ($teamid); ?>/cid/<?php echo ($createid); ?>/pname/<?php echo ($pname); ?>" target="dialog" mask="true"><span>新增</span></a></li>-->
                        <li><a class="add" href="__URL__/childadd/pid/<?php echo ($projectid); ?>/tid/<?php echo ($teamid); ?>/cid/<?php echo ($createid); ?>/pname/<?php echo ($pname); ?>" rel="childadd" target="dialog" title="新增任务页面" mask="true"><span>新增</span></a></li>
                        <li><a class="edit" href="__URL__/childedit/id/{sid_node}" target="dialog" rel="childedit" mask="true" warn="请选择节点"><span>修改</span></a></li>
                        <?php if(($_SESSION['loginUserName'] == '超级管理员') OR ($_SESSION['loginUserName'] == 'wistron_QT') ): ?><li><a class="delete" href="__URL__/childdelete/id/{sid_node}/pid/<?php echo ($_REQUEST["pid"]); ?>/navTabId/__MODULE__" target="ajaxTodo" calback="navTabAjaxMenu" title="你确定要删除吗？" warn="请选择节点"><span>删除</span></a></li>
                            <li class="line">line</li>
                            <li><a class="delete" href="__URL__/childdelete/pid/<?php echo ($_REQUEST["pid"]); ?>" posttype="string" rel="ids" target="selectedTodo" calback="navTabAjaxMenu" title="你确定要删除吗？" warn="请选择节点"><span>批量删除</span></a></li>
                        <?php else: endif; ?>
                        <!--<li><a class="icon" href="__URL__/childaddmenber/id/{sid_node}" target="navTab" mask="true" warn="请选择单个task"><span>添加Member</span></a></li>-->
		</ul>
	</div>
	<table class="table" width="100%" layoutH="138">
		<thead>
		<tr>
		      <th width="20">
					<input type="checkbox" group="ids" class="checkboxCtrl" />
				</th>
			<th width="60">编号</th>
			<th width="100" orderField="task_title" <?php if($_REQUEST["_order"] == 'task_title'): ?>class="<?php echo ($_REQUEST["_sort"]); ?>"<?php endif; ?>>Task_name</th>
			<th>task_member</th>
			<th width="100">实际时间</th>
			<th width="80" orderField="sequence" <?php if($_REQUEST["_order"] == 'sequence'): ?>class="<?php echo ($_REQUEST["_sort"]); ?>"<?php endif; ?>>schedule开始时间</th>
                        <th width="100">schedule结束时间</th>
			<th width="100" orderField="status" <?php if($_REQUEST["_order"] == 'status'): ?>class="<?php echo ($_REQUEST["_sort"]); ?>"<?php endif; ?>>状态</th>
			<th width="100">操作</th>
		</tr>
		</thead>
		<tbody>
		<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="sid_node" rel="<?php echo ($vo['id']); ?>">
			     <td>
					<input name="ids"  type="checkbox" value="<?php echo ($vo['id']); ?>" />
				 </td>
				<td><?php echo ($vo['id']); ?></td>
                                <td><a title="Task详细信息" href="__URL__/taskedit/pid/<?php echo ($vo['id']); ?>/st/<?php echo ($vo['schedule_start']); ?>/et/<?php echo ($vo['schedule_end']); ?>/est/<?php echo ($vo['est_time']); ?>/navId/CreateTask/mb/<?php echo (($vo['task_menber'])?($vo['task_menber']):0); ?>/ratios/<?php echo (($vo['ratios'])?($vo['ratios']):0); ?>/" target="navTab" rel="Taskedit" mask="true" width="800" height="400" calback="navTabAjaxMenu"><?php echo ($vo['task_title']); ?></a></td>
				<td><?php echo ($vo['task_menbers']); ?></td>
				<td><?php echo ($vo['est_time']); ?>/H</td>
                                <td><?php echo (date("Y-m-d",$vo['schedule_start'])); ?></td>
				<td><?php echo (date("Y-m-d",$vo['schedule_end'])); ?></td>
				<td><?php echo (showstatusimg($vo['status'],$vo['id'])); ?></td>
				<td>
				<a href="__URL__/childedit/id/<?php echo ($vo['id']); ?>" target="dialog">编辑</a>
				</td>
			</tr><?php endforeach; endif; else: echo "" ;endif; ?>
		</tbody>
	</table>

	<div class="panelBar">
		<div class="pages">
			<span>共<?php echo ($totalCount); ?>条</span>
		</div>
		<div class="pagination" targetType="navTab" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="10" currentPage="<?php echo ($currentPage); ?>"></div>
	</div>
</div>