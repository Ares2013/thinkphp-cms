<?php

//公共函数 格式化时间
function toDate($time, $format = 'Y-m-d H:i:s') {
	if (empty ( $time )) {
		return '';
	}
	$format = str_replace ( '#', ':', $format );
	return date ($format, $time );
}
//根据 记录的某个字段显示状态图片
function showStatusImg($status, $id, $callback="ajaxTodoCallback",$field='status') {
	$callback="ajaxTodoCallback";
	$status = intval($status);
	switch ($status) {
		case 0 :
			$info = '<a class="status" id="status_'.$field.'_'.$id.'"  href="__URL__/toggle/id/' . $id .'/f/'.$field. '"  target="ajaxTodo" callback="'.$callback.'"><img status="'.$status.'" src="__PUBLIC__/dwz/images/status-'.$status.'.gif" /></a>';
			break;
		case 2 :
			$info = '<a class="status" id="status_'.$field.'_'.$id.'" href="__URL__/toggle/id/' . $id .'/f/'.$field. '" target="ajaxTodo" callback="'.$callback.'"><img status="'.$status.'" src="__PUBLIC__/dwz/images/status-'.$status.'.gif" /></a>';
			break;
		case 1 :
			$info = '<a class="status" id="status_'.$field.'_'.$id.'" href="__URL__/toggle/id/' . $id .'/f/'.$field. '" target="ajaxTodo" callback="'.$callback.'"><img status="'.$status.'" src="__PUBLIC__/dwz/images/status-'.$status.'.gif" /></a>';
			break;
		case - 1 :
			$info = '<a class="status" id="status_'.$field.'_'.$id.'" href="__URL__/toggle/id/' . $id .'/f/'.$field. '" target="ajaxTodo" callback="'.$callback.'"><img status="'.$status.'" src="__PUBLIC__/dwz/images/status-'.$status.'.gif" /></a>';
			break;
	}
	return $info;
}

//获取上级分组的信息
function getGroupName($id) {
	if ($id == 0) {
		return '无上级组';
	}
	if ($list = F ( 'groupName' )) {
		return $list [$id];
	}
	$dao = D ( "Role" );
	$list = $dao->select ( array ('field' => 'id,name' ) );
	foreach ( $list as $vo ) {
		$nameList [$vo ['id']] = $vo ['name'];
	}
	$name = $nameList [$id];
	F ( 'groupName', $nameList );
	return $name;
}
function getNodeGroupName($id) {
	if (empty ( $id )) {
		return '未分组';
	}
	/*if (isset ( $_SESSION ['nodeGroupList'] )) {
		return $_SESSION ['nodeGroupList'] [$id];
	}*/
	$Group = D ( "Group" );
	$list = $Group->getField ( 'id,title' );
	//$_SESSION ['nodeGroupList'] = $list;
	$name = $list [$id];
	return $name;
}
function getUserName_en($userid){
    if(empty($userid)){
        return '未指定';
    }
    $Group = D("User");
    $list = $Group->getField('id,account');
    $name = $list[$userid];
    return $name;
}
/*
 * 获取用户的组别
 * 2013.11.15创建
 */
function getUserGroupName($id){
    if(empty($id)){
        return '未指定';
    }
    $Group = D("UserGroup");
    $list = $Group->getField('id,nickname');
    $name = $list[$id];
    return $name;
}
/*
 * 获取用户的信息
 */
function getUserMessage($Userid){
    if(empty($Userid)){
        return $UserMessage;
    }
    $UserModel = M("User");
    $UserMessage = $UserModel->where(array('id'=>$Userid))->find();
    return $UserMessage;
}
/*
 * 获取project task type
 */
function getTypeName($id,$leverl){
    if(empty($id)){
        return "未指定";
    }
    $typemodel = M('itemtype');
    
    $list = $typemodel->where("")->getField('id,title');
    $name = $list[$id];
    return $name;
}
//根据ID获得分类名
function getArticleCatName($id){
	if (empty ( $id )) {
		return '顶级分类';
	}
	$Category = D ( "ArticleCat" );
	$list = $Category->getField ( 'cat_id,cat_name' );
	$name = $list [$id];
	return $name;
}

function pwdHash($password, $type = 'md5') {
	return hash ( $type, $password );
}
/*
function search($array, $v) {
                                $data = array();
                           foreach ($array as $key => $value) {
                                if (is_array($value)) {
                                    $result = test($value, $v);
                                    if (!empty($result)) {
                                        $data[$key] = $result;
                                    }
                                } else {
                                    if ($value == $v) {
                                        $data[$key] = $v;
                                    }
                                }
                            }
                return $data;
              }
 * 
 */
?>