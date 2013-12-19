<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" action="__URL__" method="post">
	<input type="hidden" name="pageNum" value="1"/>
	<input type="hidden" name="_order" value="<?php echo ($_REQUEST["_order"]); ?>"/>
	<input type="hidden" name="_sort" value="<?php echo ($_REQUEST["_sort"]); ?>"/>
	<input type="hidden" name="pid" value="<?php echo ($_REQUEST["pid"]); ?>"/>
</form>
<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="__URL__" method="post">
	<div class="searchBar">
		<ul class="searchContent">
			<li>
				<label>project名称：</label>
				<input type="text" name="title" class="medium" value="<?php echo ($_POST['title']); ?>">
			</li>
                        <li>
				<label>Team名称：</label>
				<!--<input type="text" name="teamid" class="medium" value="<?php echo ($_POST['title']); ?>">-->
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
			<li><a class="add" href="__URL__/add" target="dialog" mask="true"><span>新增</span></a></li>
			<li><a class="edit" href="__URL__/edit/id/{sid_node}" rel="childedit" target="dialog" mask="true" warn="请选择节点"><span>修改</span></a></li>
			<?php if(($_SESSION['loginUserName'] == '超级管理员') OR ($_SESSION['loginUserName'] == 'wistron_QT') ): ?><li><a class="delete" href="__URL__/foreverdelete/id/{sid_node}/navTabId/__MODULE__" target="ajaxTodo" calback="navTabAjaxMenu" title="你确定要删除吗？" warn="请选择节点"><span>删除</span></a></li>
			<li class="line">line</li>
                        <li><a class="delete" href="__URL__/foreverdelete" posttype="string" rel="ids" target="selectedTodo" calback="navTabAjaxMenu" title="你确定要删除吗？" warn="请选择节点"><span>批量删除</span></a></li>
                        <?php else: endif; ?>
		</ul>
	</div>
	<table class="table" width="100%" layoutH="138">
		<thead>
		<tr>
		      <th width="20">
					<input type="checkbox" group="ids" class="checkboxCtrl" />
				</th>
			<th width="60">编号</th>
			<th width="100" orderField="title" <?php if($_REQUEST["_order"] == 'title'): ?>class="<?php echo ($_REQUEST["_sort"]); ?>"<?php endif; ?>>project_name</th>
			<th width="100">project_code</th>
                        <th width="80">Test Leader</th>
			<th width="100">创建时间</th>
			<th width="80" orderField="c_name" <?php if($_REQUEST["_order"] == 'c_name'): ?>class="<?php echo ($_REQUEST["_sort"]); ?>"<?php endif; ?>>创建者</th>
                        <th width="100">Project Type</th>
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
                                <td><a href="__URL__/child/pid/<?php echo ($vo['id']); ?>/" target="navTab" rel="CreateTask" title="Task列表页"><?php echo ($vo['title']); ?></a></td>
				<td><?php echo ($vo['code']); ?></td>
                                <td><?php echo (getusername_en($vo['p_cw'])); ?></td>
				<td><?php echo (date("Y-m-d",$vo['c_time'])); ?></td>
                                <td><?php echo ($vo['c_name']); ?></td>
				<td><?php echo (gettypename($vo['type'])); ?></td>
				<td><?php echo (showstatusimg($vo['status'],$vo['id'])); ?></td>
				<td>
				<a href="__URL__/edit/id/<?php echo ($vo['id']); ?>" target="dialog">编辑</a>
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