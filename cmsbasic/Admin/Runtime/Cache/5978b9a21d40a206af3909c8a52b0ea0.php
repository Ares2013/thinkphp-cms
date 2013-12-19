<?php if (!defined('THINK_PATH')) exit();?><div class="pageContent">
    <form method="post" action="__URL__/updatetlist_ares/navTabId/CreateTask"  class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone)" novalidate="novalidate">
	<input type="hidden" name="taskid" value="<?php echo ($_REQUEST["pid"]); ?>">
        <input type="hidden" name="mb" value="<?php echo ($_REQUEST["mb"]); ?>">
        <input type="hidden" name="st" value="<?php echo ($_REQUEST["st"]); ?>">
        <input type="hidden" name="et" value="<?php echo ($_REQUEST["et"]); ?>">
        <input type="hidden" name="tast_title" value="<?php echo ($_REQUEST["task_title"]); ?>">
        <input type="hidden" value="closeCurrent" name="callbackType">
       <div class="panelBar">
		<ul class="toolBar">                    
                    <li><div class="buttonActive"><div class="buttonContent"><button type="submit">保存</button></div></div></li>
                    <!--<li><a class="add" href="__URL__/updatetlist_ares/taskid/<?php echo ($taskid); ?>/navTabId/CreateTask/st/<?php echo ($st); ?>/et/<?php echo ($et); ?>" posttype="string" rel="uid" target="selectedTodo" calback="navTabAjaxMenu" title="你确定要对<?php echo ($tname); ?>添加了吗？" warn="请选择对应的人"><span>添加</span></a></li>-->
                    <li class="line">line</li>
                    <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
		</ul>
	</div>
	<table class="table" width="100%" layoutH="50">
		<thead>
			<tr>
                            <th width="60"></th>
                                <?php if(is_array($days_task)): $i = 0; $__LIST__ = $days_task;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><th width="55" align="center" bgcolor="<?php echo ($vo["td_bgcolor"]); ?>"><?php echo ($vo["days"]); ?></th><?php endforeach; endif; else: echo "" ;endif; ?>
			</tr>
		</thead>
                <tbody id="ares">
                        <tr target="sid_user" rel="<?php echo ($vo["uid"]); ?>">
                                <td>Recommended Manpower</td>
                                <?php if(is_array($days_task)): $i = 0; $__LIST__ = $days_task;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><td bgcolor="<?php echo ($vo["td_bgcolor"]); ?>"><?php if(empty($vo["td_bgcolor"])): echo ($recomment); else: ?>无<?php endif; ?></td><?php endforeach; endif; else: echo "" ;endif; ?>
			</tr>
                        <tr>
                            <td>Expected Progress</td>
                            <?php if(is_array($days_task)): $i = 0; $__LIST__ = $days_task;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><td bgcolor="<?php echo ($vo["td_bgcolor"]); ?>"><?php if(empty($vo["td_bgcolor"])): echo ($vo["eprogres"]); ?>%<?php else: ?> 无<?php endif; ?></td><?php endforeach; endif; else: echo "" ;endif; ?>
                        </tr>
                        <tr>
                            <td>Actual Manpower</td>
                                <?php if(is_array($days_task)): $i = 0; $__LIST__ = $days_task;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><td bgcolor="<?php echo ($vo["td_bgcolor"]); ?>"><?php if(empty($vo["td_bgcolor"])): echo ($vo['a_manpower']); else: ?>0<?php endif; ?></td><?php endforeach; endif; else: echo "" ;endif; ?>
                        </tr>
                        <tr>
                            <td>Actually Progress</td>
                            <?php if(is_array($days_task)): $i = 0; $__LIST__ = $days_task;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><td bgcolor="<?php echo ($vo["td_bgcolor"]); ?>"><input type="text" class="number textInput valid" name="lists[<?php echo ($key); ?>]" value="<?php echo ($vo["progres"]); ?>" size="2">%</td><?php endforeach; endif; else: echo "" ;endif; ?>
                        </tr>
                        <tr>
                            <td>Gap</td>
                            <?php if(is_array($days_task)): $i = 0; $__LIST__ = $days_task;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><td <?php if(empty($vo["gap_color"])): ?>bgcolor="<?php echo ($vo["td_bgcolor"]); ?>"<?php else: ?>bgcolor="<?php echo ($vo["gap_color"]); ?>"<?php endif; ?>><?php echo ($vo['gap']); ?></td><?php endforeach; endif; else: echo "" ;endif; ?>
                        </tr>
                        <tr>
                            <td>DL(总数)</td>
                            <?php if(is_array($days_task)): $i = 0; $__LIST__ = $days_task;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$dvo): $mod = ($i % 2 );++$i;?><td bgcolor="<?php echo ($dvo["td_bgcolor"]); ?>"><input type="text" class="number textInput valid" name="dllists[<?php echo ($key); ?>]" value="<?php echo ($dvo["dllists"]); ?>" size="2"></td><?php endforeach; endif; else: echo "" ;endif; ?>
                        </tr>
                        <tr>
                            <td>IDL(总数)</td>
                            <?php if(is_array($days_task)): $i = 0; $__LIST__ = $days_task;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$dvo): $mod = ($i % 2 );++$i;?><td bgcolor="<?php echo ($dvo["td_bgcolor"]); ?>"><?php echo ($dvo["total_id"]); ?>人</td><?php endforeach; endif; else: echo "" ;endif; ?>
                        </tr>
                        <tr>
                            <td>IDL(人名)</td>
                            <?php if(is_array($days_task)): $i = 0; $__LIST__ = $days_task;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$dvo): $mod = ($i % 2 );++$i;?><td bgcolor="<?php echo ($dvo["td_bgcolor"]); ?>"><?php echo ($dvo["username"]); ?></td><?php endforeach; endif; else: echo "" ;endif; ?>
                        </tr>
                        <tr>
                            <td>IDL(任务量)</td>
                            <?php if(is_array($days_task)): $i = 0; $__LIST__ = $days_task;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$dvo): $mod = ($i % 2 );++$i;?><td bgcolor="<?php echo ($dvo["td_bgcolor"]); ?>"><?php echo ($dvo["task_num"]); ?></td><?php endforeach; endif; else: echo "" ;endif; ?>
                        </tr>
                        <!--<tr>
                            <td>IDL(详细)</td>
                            <?php if(is_array($days_task)): $i = 0; $__LIST__ = $days_task;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$dvo): $mod = ($i % 2 );++$i;?><td bgcolor="<?php echo ($dvo["td_bgcolor"]); ?>">
                                    
                                    <input type="hidden" class="textInput valid readonly" readonly="true" name="org<?php echo ($key); ?>.orgid" value="<?php echo ($dvo["uid"]); ?>" size="10">
                                </td><?php endforeach; endif; else: echo "" ;endif; ?>
                        </tr>
                        <tr>
                            <td>Task(详细)任务量</td>
                            <?php if(is_array($days_task)): $i = 0; $__LIST__ = $days_task;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$dvo): $mod = ($i % 2 );++$i;?><td bgcolor="<?php echo ($dvo["td_bgcolor"]); ?>">
                                    <input name="org<?php echo ($key); ?>.id" value="" type="hidden">
                                    <input type="hidden" class="textInput valid readonly" readonly="true" name="org<?php echo ($key); ?>.orgNum" value="<?php echo ($dvo["task_num"]); ?>" size="10">
                                    <a class="btnLook" href="__URL__/expatiation_2/uid/<?php echo ($uvo['id']); ?>/un/<?php echo ($uvo["account"]); ?>/su/<?php echo ($dvo["unixtime"]); ?>/mb/<?php echo ($_REQUEST["mb"]); ?>" mask="true" width="800" height="400" lookupGroup="org<?php echo ($key); ?>">查找带回</a>
                                </td><?php endforeach; endif; else: echo "" ;endif; ?>
                        </tr>-->
                        <tr>
                            <td>详情查看</td>
                            <?php if(is_array($days_task)): $i = 0; $__LIST__ = $days_task;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$dvo): $mod = ($i % 2 );++$i;?><td bgcolor="<?php echo ($dvo["td_bgcolor"]); ?>">
                                    <input name="org<?php echo ($key); ?>.id" value="" type="hidden">
                                    <input type="hidden" class="textInput valid readonly" readonly="true" name="org<?php echo ($key); ?>.orgid" value="<?php echo ($dvo["uid"]); ?>" size="10">
                                    <input type="hidden" class="textInput valid readonly" readonly="true" name="org<?php echo ($key); ?>.orgNum" value="<?php echo ($dvo["task_num"]); ?>" size="10">
                                    <a class="btnLook" href="__URL__/expatiation_2/uid/<?php echo ($uvo['id']); ?>/un/<?php echo ($uvo["account"]); ?>/su/<?php echo ($dvo["unixtime"]); ?>/mb/<?php echo (($dvo["uid"])?($dvo["uid"]):0); ?>/mn/<?php echo (($dvo["task_num"])?($dvo["task_num"]):0); ?>/taskid/<?php echo ($_REQUEST["pid"]); ?>/st/<?php echo ($_REQUEST["st"]); ?>/et/<?php echo ($_REQUEST["et"]); ?>" mask="true" width="800" height="500" lookupGroup="org<?php echo ($key); ?>" title="添加Member的任务量">查找带回</a>
                                    <!--<a class="add" href="javascript:void(0);" onclick="copy_data()">Copy</a>-->
                                </td><?php endforeach; endif; else: echo "" ;endif; ?>
                        </tr>
                        <tr>
                            <td>No-Attend Rate</td>
                            <?php if(is_array($days_task)): $i = 0; $__LIST__ = $days_task;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$dvo): $mod = ($i % 2 );++$i;?><td bgcolor="<?php echo ($dvo["td_bgcolor"]); ?>"><?php echo ($dvo['noattend_w']); ?></td><?php endforeach; endif; else: echo "" ;endif; ?>
                        </tr>
                        <tr>
                            <td>No-AttendRate(weekend)</td>
                            <?php if(is_array($days_task)): $i = 0; $__LIST__ = $days_task;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$dvo): $mod = ($i % 2 );++$i;?><td bgcolor="<?php echo ($dvo["td_bgcolor"]); ?>"><?php echo ($dvo['noattend_a']); ?></td><?php endforeach; endif; else: echo "" ;endif; ?>
                        </tr>
		</tbody>
	</table>
	<!--
        <div class="panelBar">
		<div class="pages">
			<span>显示</span>
			<select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage:this.value})">
				<option value="20">20</option>
				<option value="50">50</option>
				<option value="100">100</option>
				<option value="200">200</option>
			</select>
			<span>条，共${totalCount}条</span>
		</div>
		<div class="pagination" targetType="navTab" totalCount="200" numPerPage="20" pageNumShown="10" currentPage="1"></div>
	</div>
        -->
    </form>
</div>