<?php
/**
 +------------------------------------------------------------------------------
 * 模版切换
 +------------------------------------------------------------------------------
 * @category   Action
 * @package  Lib
 * @subpackage  Action
 * @author   668FreeCloud <chinasoftstar@qq.com>
 * @version  $Id: TemplateAction.class.php 2791 2013/7/24  668FreeCloud $
 +------------------------------------------------------------------------------
 */
class TemplateAction extends CommonAction
{
    function index()
    {
		$mod = D('Sysconf');
		
    	if (isset($_GET['dirname']) && trim($_GET['dirname'])!='') {                     
             
            $mod->where("code='template'")->save(array('data'=>trim($_GET['dirname']))); 
            $config_file =ROOT_PATH.'/Home/Conf/theme.php';
            $content="<?php \r\n"
            ."return array('DEFAULT_THEME'=>'".trim($_GET['dirname'])."');";
            file_put_contents($config_file,$content);            
            @unlink(ROOT_PATH.'/Home/Runtime/~Runtime.php');
            $this->success(L('operation_success'));
        }
		$setting_template=$mod->where("code='template'")->getField('value');
        $this->assign('setting_template', $setting_template);
        // 获得模板文件下的模板
        $tpl_dir = ROOT_PATH . '/Home/Tpl/';
        $opdir = dir($tpl_dir);
        $template_list = array();
        while (false !== ($entry = $opdir->read())) {
            if ($entry{0} == '.') {
                continue;
            }
            if (!is_file($tpl_dir . $entry . '/info/info.php')) {
                continue;
            }
            $info = include_once($tpl_dir . $entry . '/info/info.php');
            if (!is_file($tpl_dir . $entry . '/info/preview.gif')) {
                $info['preview'] = __ROOT__ . '/Public/images/template_no_preview.gif';
            } else {
                $info['preview'] = __ROOT__ . '/Home/Tpl/' . $entry . '/info/preview.gif';
            }
            $info['dirname'] = $entry;
            $template_list[$entry] = $info;
        }
    	$this->assign('template_list',$template_list);
    	$this->display();
    }
	function update()
    {
		$mod = D('Sysconf');
		$dirname=$this->_post('dirname');

    	if (!empty($dirname)) {                     
             
            $mod->where("code='template'")->save(array('value'=>trim($dirname))); 
            $config_file =ROOT_PATH.'/Home/Conf/theme.php';
            $content="<?php \r\n"
            ."return array('DEFAULT_THEME'=>'".trim($dirname)."');";
            file_put_contents($config_file,$content);            
            @unlink(ROOT_PATH.'/Home/Runtime/~Runtime.php');
            $this->success("模版更新成功！");
        }
		else
		{
			$this->error("请选择正确的模版！");
		}
    }
}
?>