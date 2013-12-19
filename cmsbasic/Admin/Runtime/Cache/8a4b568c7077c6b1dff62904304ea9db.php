<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
	ul.rightTools {float:right; display:block;}
	ul.rightTools li{float:left; display:block; margin-left:5px}
        .ares{height:10px;}
</style>
<!--style="height:230px;"-->
<div class="panel" style="height:220px;overflow: auto;">
		<h1>分析报告</h1>
                <!--<div style="width:100%">
			<ul class="rightTools">
				<li><a class="button" target="navTab" href="__URL__/analyze/tid/<?php echo ($teamid); ?>" mask="true"><span>曲线图</span></a></li>
                                <li><a class="button" target="navTab" href="__URL__/analyze/tid/<?php echo ($teamid); ?>" mask="true"><span>柱状图</span></a></li>
			</ul>
                </div>-->
                <!--layoutH="505"-->
             <table class="list ares" width="100%" style="height:105px;">
		<thead>
			<tr>
				<th width="20" align="left" ></th>
                                <?php if(is_array($monthlyRecord)): $i = 0; $__LIST__ = $monthlyRecord;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$mrd): $mod = ($i % 2 );++$i;?><th width="10" align="left"><?php echo ($mrd['date']); ?></th><?php endforeach; endif; else: echo "" ;endif; ?>
				
                <th width="" align="center"><a class="button" target="navTab" href="__URL__/analyze/tid/<?php echo ($teamid); ?>" mask="true"><span>饼形图</span></a><a class="button" target="navTab" href="__URL__/analyze/tid/<?php echo ($teamid); ?>" mask="true"><span>曲线图</span></a><a class="button" target="navTab" href="__URL__/analyze/tid/<?php echo ($teamid); ?>" mask="true"><span>柱状图</span></a></th>
			</tr>
		</thead>
		<tbody>
			<tr target="sid_user" rel="1">
				
				<td>最大需求</td>
				<?php if(is_array($monthlyRecord)): $i = 0; $__LIST__ = $monthlyRecord;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$mrd): $mod = ($i % 2 );++$i;?><td><?php echo ($mrd['MaxIDLManpower']); ?></td><?php endforeach; endif; else: echo "" ;endif; ?>
                                <td width="32%" rowspan="5">
                                    <img alt="饼图" src="__APP__/Images/pie?w=480&h=130&p=60&c=<?php echo rand(1000,88888)?>" width="480" height="130" />
                                </td>
				<!--<td rowspan="6">
					<a title="删除" target="ajaxTodo" href="demo/common/ajaxDone.html?id=xxx" class="btnDel">删除</a>
					<a title="编辑" target="navTab" href="demo_page4.html?id=xxx" class="btnEdit">编辑</a>
				</td>-->
			</tr>
			<!--<tr target="sid_user" rel="2">
				
				<td>DL(推荐)</td>
				<?php if(is_array($monthlyRecord)): $i = 0; $__LIST__ = $monthlyRecord;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$mrd): $mod = ($i % 2 );++$i;?><td><?php echo ($mrd['DLrecommend']); ?></td><?php endforeach; endif; else: echo "" ;endif; ?>
                                
			</tr>-->
                        <tr target="sid_user" rel="2">
				
				<td>IDL(实际)</td>
				<?php if(is_array($monthlyRecord)): $i = 0; $__LIST__ = $monthlyRecord;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$mrd): $mod = ($i % 2 );++$i;?><td><?php echo ($mrd['IDLTaskByday']); ?></td><?php endforeach; endif; else: echo "" ;endif; ?>
				
			</tr>
                        <tr target="sid_user" rel="2">
				
				<td>DL(实际)</td>
				<?php if(is_array($monthlyRecord)): $i = 0; $__LIST__ = $monthlyRecord;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$mrd): $mod = ($i % 2 );++$i;?><td><?php echo ($mrd['DLTaskByday']); ?></td><?php endforeach; endif; else: echo "" ;endif; ?>
				
			</tr>
                        <tr target="sid_user" rel="2">
				
				<td>Manpower</td>
				<?php if(is_array($monthlyRecord)): $i = 0; $__LIST__ = $monthlyRecord;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$mrd): $mod = ($i % 2 );++$i;?><td><?php echo ($mrd['Manpower']); ?></td><?php endforeach; endif; else: echo "" ;endif; ?>
				
			</tr>
                        <tr target="sid_user" rel="2">
				
				<td>Gap</td>
				<?php if(is_array($monthlyRecord)): $i = 0; $__LIST__ = $monthlyRecord;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$mrd): $mod = ($i % 2 );++$i;?><td><?php echo ($mrd['Gap']); ?></td><?php endforeach; endif; else: echo "" ;endif; ?>
				<!--<td rowspan="6">
					<a title="删除" target="ajaxTodo" href="demo/common/ajaxDone.html?id=xxx" class="btnDel">删除</a>
					<a title="编辑" target="navTab" href="demo_page4.html?id=xxx" class="btnEdit">编辑</a>
				</td>-->
                                
			</tr>
                        
		</tbody>
	</table>
	</div>
<div class="pageHeader" style="border:1px #B8D0D6 solid">
	<form id="pagerForm" onsubmit="return navTabSearch(this)" action="__URL__/summarize/" method="post">
	<input type="hidden" name="pageNum" value="1" />
        <input type="hidden" name="teamid" value="<?php echo ($teamid); ?>"
	<input type="hidden" name="numPerPage" value="${model.numPerPage}" />
	<input type="hidden" name="orderField" value="${param.orderField}" />
	<input type="hidden" name="orderDirection" value="<?php echo ($teamid); ?>" />
	<div class="searchBar">
		<table class="searchContent">
			<tr>
				<td class="dateRange">
					日期:
					<input type="text" value="<?php echo ($_REQUEST['dateStart']); ?>" readonly="readonly" class="date" name="dateStart">
					<span class="limit">-</span>
					<input type="text" value="<?php echo ($_REQUEST['dateEnd']); ?>" readonly="readonly" class="date" name="dateEnd">
				</td>
				<td>
					Task状态：
					<input type="radio" name="status" value="" <?php if(empty($_REQUEST['status'])): ?>checked="checked"<?php endif; ?> />全部
					<input type="radio" name="status" value="1" <?php if(($_REQUEST['status']) == "1"): ?>checked="checked"<?php endif; ?> />正在进行
					<input type="radio" name="status" value="2" <?php if(($_REQUEST['status']) == "2"): ?>checked="checked"<?php endif; ?> />已完成
				</td>
				<!--<td>
					project名：<input type="text" name="keyword" />
				</td>-->
				<td><div class="buttonActive"><div class="buttonContent"><button type="submit">检索</button></div></div></td>
			</tr>
		</table>
	</div>
	</form>
</div>

<div class="pageContent" style="border-left:1px #B8D0D6 solid;border-right:1px #B8D0D6 solid">
<div class="panelBar">
		<ul class="toolBar">
			<!--<li><a class="add" href="demo/pagination/dialog2.html" target="dialog" mask="true"><span>添加尿检检测</span></a></li>
			<li><a class="delete" href="demo/pagination/ajaxDone3.html?uid={sid_obj}" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a></li>
			<li><a class="edit" href="demo/pagination/dialog2.html?uid={sid_obj}" target="dialog" mask="true"><span>修改</span></a></li>-->
			<li class="line">line</li>
			<li><a class="icon" href="javascript:void(0);" target="dwzExport" title="实要导出这些记录吗?"><span>导出EXCEL</span></a></li>
		</ul>
	</div>
    <table class="table" width="99%" layoutH="335" rel="jbsxBox">
		<thead>
			<tr>
				<!--<th width="80">序号</th>-->
				<th width="60">project</th><!--orderField="number" class="asc"-->
				<th width="80">Task</th><!--orderField="name"-->
                                <th width="120">PIC(project)</th>
                                <th width="120">PIC(Task)</th>
				<th width="120">schedule</th>
				<th width="100">W.D</th>
				<th width="100">W.D(s)</th>
				<th width="100">Progress</th>
				<th width="80">Attend Rate</th>
                                <th width="80">No-AttendRate</th>
			</tr>
		</thead>
		<tbody>
                <?php if(is_array($projectlist)): $i = 0; $__LIST__ = $projectlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$plist): $mod = ($i % 2 );++$i; if(is_array($plist['Tasks'])): $i = 0; $__LIST__ = $plist['Tasks'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tasklist): $mod = ($i % 2 );++$i;?><tr target="sid_obj" rel="<?php echo ($key); ?>">
				<!--<td><?php echo ($key); ?></td>-->
                                
                                    <?php if($tasklist['sort'] == 0): ?><td rowspan="<?php echo ($plist['TasksTotal']); ?>"><a href="__URL__/summarize/teamid/<?php echo ($teamid); ?>/type/1/id/<?php echo ($plist['id']); ?>"><?php echo ($plist['title']); ?></a></td>
                                    <?php else: endif; ?>
                                <td><a href="__URL__/summarize/teamid/<?php echo ($teamid); ?>/type/2/id/<?php echo ($tasklist['taskid']); ?>"><?php echo ($tasklist['task_title']); ?></a></td>
                                <td><?php echo (getusername_en($plist['p_cw'])); ?></td>
                                <td><?php echo (getusername_en($tasklist['task_leader'])); ?></td>
				<td><?php echo (date("Y-m-d",$tasklist['schedule_start'])); ?>~<?php echo (date("Y-m-d",$tasklist['schedule_end'])); ?></td>
				<td><?php echo ($tasklist['workday']); ?></td>
				<td><?php echo ($tasklist['workday_saturday']); ?></td>
				<td><?php echo ($tasklist['Last_Progress']); ?></td>
				<td><?php echo ($tasklist['Attend']); ?></td>
                                <td><?php echo ($tasklist['NoAttend']); ?></td>
                    </tr><?php endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>
		</tbody>
	</table>
	<div class="panelBar">
		<div class="pages">
			<span>显示</span>
			<select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage:this.value}, 'jbsxBox')">
				<option value="20">20</option>
				<option value="50">50</option>
				<option value="100">100</option>
				<option value="200">200</option>
			</select>
			<span>条，共50条</span>
		</div>
		
		<div class="pagination" rel="jbsxBox" totalCount="200" numPerPage="20" pageNumShown="5" currentPage="1"></div>

	</div>
</div>