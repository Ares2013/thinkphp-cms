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
				<label>名称：</label>
				<input type="text" name="name" class="medium" >
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
			<li><a class="edit" href="__URL__/edit/id/{sid_node}" target="dialog" mask="true" warn="请选择节点"><span>修改</span></a></li>
			<li><a class="delete" href="__URL__/foreverdelete/id/{sid_node}/navTabId/__MODULE__" target="ajaxTodo" calback="navTabAjaxMenu" title="你确定要删除吗？" warn="请选择节点"><span>删除</span></a></li>
			<li class="line">line</li>
                        <li><a class="delete" href="__URL__/foreverdelete" posttype="string" rel="ids" target="selectedTodo" calback="navTabAjaxMenu" title="你确定要删除吗？" warn="请选择节点"><span>批量删除</span></a></li>
		</ul>
	</div>
	<table class="table" width="100%" layoutH="138">
		<thead>
		<tr>
		      <th width="20">
					<input type="checkbox" group="ids" class="checkboxCtrl" />
				</th>
			<th width="60">编号</th>
			<th width="100" orderField="title" <?php if($_REQUEST["_order"] == 'title'): ?>class="<?php echo ($_REQUEST["_sort"]); ?>"<?php endif; ?>>模块名</th>
			<th>显示名</th>
			<th width="100">分组</th>
			<th width="80" orderField="sequence" <?php if($_REQUEST["_order"] == 'sequence'): ?>class="<?php echo ($_REQUEST["_sort"]); ?>"<?php endif; ?>>序号</th>
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
				<td><a href="__URL__/index/pid/<?php echo ($vo['id']); ?>/" target="navTab" rel="__MODULE__"><?php echo ($vo['name']); ?></a></td>
				<td><?php echo ($vo['title']); ?></td>
				<td><?php echo (getnodegroupname($vo['group_id'])); ?></td>
				<td><?php echo ($vo['sequence']); ?></td>
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