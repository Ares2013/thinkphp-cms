<?php
/**
 +------------------------------------------------------------------------------
 * 图表功能的数据处理合成类
 +------------------------------------------------------------------------------
 * @category   Action
 * @package  Lib
 * @subpackage  chart.class.php
 * @author   Ares Peng <Z13053003@wistron.local><1534157801@qq.com>
 * @version  $Id: ImagesAction.class.php 2013/10/1  Ares Peng
 +------------------------------------------------------------------------------
 */
class ImagesAction extends Action {
    public function __construct() {
        parent::__construct();
    }
    //饼状图数据合成
    public  function pie(){
        import('ORG.Util.Chart');
        $chart = new Chart();
        $title = ""; //标题 3D 饼状图
        $data = array(20,27,45,75,90,10,20,40); //数据
        $size = $_REQUEST['p']; //尺寸70
        $width = $_REQUEST['w']; //宽度
        $height = $_REQUEST['h']; //高度
        $legend = array("aaaa ","bbbb","cccc","dddd ","eeee ","ffff ","gggg ","hhhh ");//说明
        $chart->create3dpie($title,$data,$size,$height,$width,$legend);
    }
    //柱状图数据合成
    public function hbar(){
        import('ORG.Util.Chart');
        $chart = new Chart();
        $title = "3D 柱状图"; //标题
        $data = array(20,27,45,75,150,10,20,40); //数据
        $size = 140; //尺寸
        $width = 750; //宽度
        $height = 350; //高度
        $legend = array("aaaa ","bbbb","cccc","dddd ","eeee ","ffff ","gggg ","hhhh ");//说明
        $chart->createcolumnar($title,$data,$size,$height,$width,$legend);
    }
    //线性图数据合成
    public function line(){
        import('ORG.Util.Chart');
        $chart = new Chart();
        $title = "3D 单线条图形"; //标题
        $data = array(20,27,45,75,90,10,20,40); //数据
        $size = 140; //尺寸
        $width = 750; //宽度
        $height = 350; //高度
        $legend = array("aaaa ","bbbb","cccc","dddd ","eeee ","ffff ","gggg ","hhhh ");//说明标题
        $chart->createmonthline($title,$data,$size,$height,$width,$legend);
    }
    //环形图
    public function hxing(){
        import('ORG.Util.Chart');
        $chart = new Chart();
        $title = "3D 环形图"; //标题
        $data = array(20,27,45,75,90,10,20,40); //数据
        $size = 140; //尺寸
        $width = 750; //宽度
        $height = 350; //高度
        $legend = array("aaaa ","bbbb","cccc","dddd ","eeee ","ffff ","gggg ","hhhh ");//说明标题
        $chart->createring($title,$data,$size,$height,$width,$legend);
    }
    public function zidingyi(){
        import('ORG.Util.Chart');
        $chart = new Chart();
        $title = "3D 环形图"; //标题
        $data = array(20,27,45,75,90,10,20,40); //数据
        $size = 140; //尺寸
        $width = 750; //宽度
        $height = 350; //高度
        $legend = array("aaaa ","bbbb","cccc","dddd ","eeee ","ffff ","gggg ","hhhh ");//说明标题
        $chart->zhuzhuangxing($title,$data,$size,$height,$width,$legend);
    }
    public function zidingyiA(){
        import('ORG.Util.Chart');
        $chart = new Chart();
        $title = "3D 环形图"; //标题
        $data = array(20,27,45,75,90,10,20,40); //数据
        $size = 140; //尺寸
        $width = 750; //宽度
        $height = 350; //高度
        $legend = array("aaaa ","bbbb","cccc","dddd ","eeee ","ffff ","gggg ","hhhh ");//说明标题
        $chart->sanzhongzhuzhuangtu($title,$data,$size,$height,$width,$legend);
    }
}
?>
