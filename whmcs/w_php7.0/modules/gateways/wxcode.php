<?php
/**
 * 发卡君提供API免签约支付接口和云端发卡
 * www.fakajun.com    QQ466660801
 */
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * 支付配置
 * @return [type] [description]
 */
function wxcode_config() {
    return array(
        // 显示名称
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => '微信扫码支付 - 发卡君（www.fakajun.com）',
        ),
        // 发卡君AccessKey（https://www.fakajun.com/api/token）
        'access_key' => array(
            'FriendlyName' => 'AccessKey',
            'Type' => 'text',
            'Size' => '12',
            'Default' => 'rultdaotymqa',
            'Description' => '发卡君AccessKey（<a href="https://www.fakajun.com/api/token" target="_blank">查看获得密钥信息</a>）',
        ),
        // 发卡君AccessKey（https://www.fakajun.com/api/token）
        'secret_key' => array(
            'FriendlyName' => 'SecretKey',
            'Type' => 'text',
            'Size' => '18',
            'Default' => 'u7GWl8nqQHcfL5OFaL',
            'Description' => '发卡君SecreKey（<a href="https://www.fakajun.com/api/token" target="_blank">查看获得密钥信息</a>）',
        )
    );
}

/**
 * 前台显示
 * @param  array $params 系统参数
 * @return [type]         [description]
 */
function wxcode_link($params)
{
    if (!stristr($_SERVER['PHP_SELF'], 'viewinvoice')) {
        /** 帐单页面 */
        return '<img style="width:150px;" src="/modules/gateways/wxcode/wxcode.png" alt="微信扫码支付" />';
    }

    $http['access_key']    =   $params['access_key']; //AccessKey 查看地址：https://www.fakajun.com/api/token
    $http['secret_key']    =   $params['secret_key']; //SecreKey 查看地址：https://www.fakajun.com/api/token
    $http['method']        =   'wxpay.pay.unifiedorder'; //提交方法（操作指令）
    $http['nonce']         =   time(); //随即字符串（时间戳）
    /**
     * 业务参数 JSON格式提交
     */
    $http['biz_content']  =   json_encode([
        'out_trade_no'  =>  $params['invoiceid'],
        'total_amount'  =>  $params['amount'],
        'subject'       =>  $params['description']
    ]);
    /**
     * 签名
     * @var [type]
     */
    $http['sign']      =     signs($http);
    $result = json_decode(results($http), true);
    if ($result['code'] == 10000) {
        $qr_code = $result['data']['qr_code'];
    } else {
        $qr_code = '遇到问题，请联系管理员！';
    }

    if (isset($_GET['query'])) {
        $http['method']    =   'wxpay.pay.orderquery'; //提交方法（操作指令）
        $http['sign']      =     signs($http);
        $res = json_decode(results($http), true);
        /** 支付成功 */
        if ($res['code'] == 10000) {
            $invoiceid = checkCbInvoiceID($params['invoiceid'],$params['paymentmethod']); # Checks invoice ID is a valid invoice number or ends processing
            //checkCbTransID($transid); # Checks transaction number isn't already in the database and ends processing if it does
            $transid = $res['trade_no'];
            $amount = $res['total_amount'];
            $fee = 0;
            $gatewaymodule = $params['paymentmethod'];

            $table = "tblaccounts";
            $fields = "transid";
            $where = array("transid"=>$transid);
            $result = select_query($table,$fields,$where);
            $data = mysql_fetch_array($result);
            if(!$data){
                addInvoicePayment($invoiceid,$transid,$amount,$fee,$gatewaymodule);
                logTransaction($params['paymentmethod'],$_GET,"Successful");
            }
            exit('10000');
        }
        exit('20000');
    }
    // JS异步查询
    echo '
    <script src="//avatar-1253585425.cossh.myqcloud.com/jquery.min.js"></script>
    <script>
    function hello(){ 
    $.get(window.location.href+"&query=a", function(res) {
        if (res == 10000) {
            window.location.reload();
        }
    });
    } 
    setInterval("hello()","5000");
    </script>';
    //返回二维码
    return '<img src="//pan.baidu.com/share/qrcode?w=150&h=150&url='.$qr_code.'" />';
}

/**
 * MD5签名算法
 * @param  array $params HTTP请求参数
 * @return string           MD5签名值
 */
function signs($params)
{
    $para_filter = array();
    while (list ($key, $val) = each ($params)) {
        if($key == "sign" || $key == "sign_type" || $val == "")continue;
        else    $para_filter[$key] = $params[$key];
    }

    ksort($para_filter);
    reset($para_filter);

    $arg  = "";

    while (list ($key, $val) = each ($para_filter)) {
        // 不是数组的时候才会组合，否则传入数组会出错
        if (!is_array($val)) {
            $arg.=$key."=".$val."&";
        }
    }

    //去掉最后一个&字符
    $arg = substr($arg,0,count($arg)-2);
    //如果存在转义字符，那么去掉转义
    if(get_magic_quotes_gpc()){
        $arg = stripslashes($arg);
    }

    $string = $params['access_key'] . $arg . $params['secret_key'];

    // md5签名
    return strtoupper(md5($string));
}

/**
 * 模拟数据提交
 * @param array $params 请求数组
 * @return JSON 返回JSON格式数据结果
 */
function results($params)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.fakajun.com/gateway.do');
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    return curl_exec($ch);
}
