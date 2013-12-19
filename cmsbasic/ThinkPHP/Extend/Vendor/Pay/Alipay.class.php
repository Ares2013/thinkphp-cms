<?php

/**
 * 
 * Alipay.php (支付宝支付模块)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2012-01-09 yourphp.cn $
 * @此注解信息不能修改或删除,请尊重我们的劳动成果,你的修改请注解在此注解下面。
 */
class Alipay extends Think {

    public $config = array();

    public function __construct($config = array()) {
        $this->config = $config;

        if ($this->config['alipay_pay_type'] == 1)
            $this->config['service'] = 'create_partner_trade_by_buyer'; //担保
        elseif ($this->config['alipay_pay_type'] == 3)
            $this->config['service'] = 'create_direct_pay_by_user'; //即时
        else
            $this->config['service'] = 'trade_create_by_buyer';

        $this->config['gateway_url'] = 'https://www.alipay.com/cooperate/gateway.do?';
        $this->config['gateway_method'] = 'POST';
        $this->config['notify_url'] = $this->config['notify_url'];
        $this->config['return_url'] = $this->config['return_url'];
    }

    public function setup() {

        $modules['pay_name'] = '支付宝';
        $modules['pay_code'] = 'Alipay';
        $modules['pay_desc'] = '支付宝是国内领先的独立第三方支付平台，由阿里巴巴集团创办。致力于为中国电子商务提供“简单、安全、快速”的在线支付解决方案。<a href="http://www.alipay.com" target="_blank">立即在线申请</a>';
        $modules['is_cod'] = '0';
        $modules['is_online'] = '1';
        $modules['author'] = '学子传媒';
        $modules['website'] = 'http://www.alipay.com';
        $modules['version'] = '1.0.0';
        $modules['config'] = array(
            array('name' => 'alipay_account', 'type' => 'text', 'value' => ''),
            array('name' => 'alipay_key', 'type' => 'text', 'value' => ''),
            array('name' => 'alipay_partner', 'type' => 'text', 'value' => ''),
            array('name' => 'alipay_pay_type', 'type' => 'select', 'value' => '', 'option' =>
                array('1' => L('alipay_pay_type_option1'), '2' => L('alipay_pay_type_option2'), '3' => L('alipay_pay_type_option3')))
        );

        return $modules;
    }

    public function get_code() {


        $parameter = array(
            'service' => $this->config['service'],
            'partner' => trim($this->config['alipay_partner']),
            '_input_charset' => 'utf-8',
            'notify_url' => trim($this->config['notify_url']),
            'return_url' => trim($this->config['return_url']),
            /* 商品信息 */
            'subject' => $this->config['order_sn'],
            'out_trade_no' => $this->config['order_sn'],
            'price' => $this->config['order_amount'],
            'body' => $this->config['body'],
            'quantity' => 1,
            'payment_type' => 1,
            /* 物流参数 */
            'logistics_type' => 'EXPRESS',
            'logistics_fee' => 0,
            'logistics_payment' => 'BUYER_PAY_AFTER_RECEIVE',
            //'agent'             => $this->config['agent'], 

            /* 买卖双方信息 */
            'seller_email' => trim($this->config['alipay_account'])
        );
        ksort($parameter);
        reset($parameter);
        $param = '';
        $sign = '';

        foreach ($parameter AS $key => $val) {
            $param .= "$key=" . urlencode($val) . "&";
            $sign .= "$key=$val&";
        }

        $param = substr($param, 0, -1);
        $sign = substr($sign, 0, -1) . $this->config['alipay_key'];
        //$sign  = substr($sign, 0, -1). ALIPAY_AUTH;

        $button = '<span><input type="button"  class="button" onclick="window.open(\'' . $this->config['gateway_url'] . $param . '&sign=' . MD5($sign) . '&sign_type=MD5\')" value="' . L('PAY_NOW') . '" /></span>';


        return $button;
    }
    
     public function get_pay_url() {
        $parameter = array(
            'service' => $this->config['service'],
            'partner' => trim($this->config['alipay_partner']),
            '_input_charset' => 'utf-8',
            'notify_url' => trim($this->config['notify_url']),
            'return_url' => trim($this->config['return_url']),
            /* 商品信息 */
            'subject' => $this->config['order_sn'],
            'out_trade_no' => $this->config['order_sn'],
            'price' => $this->config['order_amount'],
            'body' => $this->config['body'],
            'quantity' => 1,
            'payment_type' => 1,
            /* 物流参数 */
            'logistics_type' => 'EXPRESS',
            'logistics_fee' => 0,
            'logistics_payment' => 'BUYER_PAY_AFTER_RECEIVE',
            //'agent'             => $this->config['agent'], 

            /* 买卖双方信息 */
            'seller_email' => trim($this->config['alipay_account'])
        );
        ksort($parameter);
        //dump($this->config);
        reset($parameter);
        $param = '';
        $sign = '';

        foreach ($parameter AS $key => $val) {
            $param .= "$key=" . urlencode($val) . "&";
            $sign .= "$key=$val&";
        }

        $param = substr($param, 0, -1);
        $sign = substr($sign, 0, -1) . $this->config['alipay_key'];
        //$sign  = substr($sign, 0, -1). ALIPAY_AUTH;

        $url = $this->config['gateway_url'] . $param . '&sign=' . MD5($sign) . '&sign_type=MD5';


        return $url;
    }

    public function respond() {
        if (!empty($_POST)) {
            foreach ($_POST as $key => $data) {
                $_GET[$key] = $data;
            }
        }

        $seller_email = rawurldecode($_GET['seller_email']);
        //$order_sn = str_replace($_GET['subject'], '', $_GET['out_trade_no']);
        $order_sn = trim($_GET['out_trade_no']);




        /* 检查数字签名是否正确 */
        ksort($_GET);
        reset($_GET);

        $sign = '';
        foreach ($_GET AS $key => $val) {
            if ($key != 'sign' && $key != 'sign_type' && $key != 'code' && $key != 'g' && $key != 'm' && $key != 'a') {
                $sign .= "$key=$val&";
            }
        }

        $sign = substr($sign, 0, -1) . $this->config['alipay_key'];
        //$sign = substr($sign, 0, -1) . ALIPAY_AUTH;
        if (md5($sign) != $_GET['sign']) {
            return false;
        }

        if ($_GET['trade_status'] == 'WAIT_SELLER_SEND_GOODS' || $_GET['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS' || $_GET['trade_status'] == 'WAIT_BUYER_PAY') {
            /* 改变订单状态 进行中 */
            //order_pay_status($order_sn, '1');
            $resutl=array();
            $result['order_sn']=$order_sn;
            $result['total_fee']=$_GET['total_fee'];
            $result['status']=1;
            return $result;
            //return true;
        } elseif ($_GET['trade_status'] == 'TRADE_FINISHED') {
            /* 改变订单状态 */
            //order_pay_status($order_sn, '2');
            $resutl=array();
            $result['order_sn']=$order_sn;
            $result['total_fee']=$_GET['total_fee'];
            $result['status']=2;
            return $result;
            //return true;
        } elseif ($_GET['trade_status'] == 'TRADE_SUCCESS') {
            /* 改变订单状态 即时交易成功 */
            //order_pay_status($order_sn, '2');
            $resutl=array();
            $result['order_sn']=$order_sn;
            $result['total_fee']=$_GET['total_fee'];
            $result['status']=2;
            return $result;
            //return true;
        } else {
            return false;
        }
    }

}

?>