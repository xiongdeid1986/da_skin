<?php //00e57
// *************************************************************************
// *                                                                       *
// * WHMCS - The Complete Client Management, Billing & Support Solution    *
// * Copyright (c) WHMCS Ltd. All Rights Reserved,                         *
// * Version: 5.3.14 (5.3.14-release.1)                                    *
// * BuildId: 0866bd1.62                                                   *
// * Build Date: 28 May 2015                                               *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * Email: info@whmcs.com                                                 *
// * Website: http://www.whmcs.com                                         *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * This software is furnished under a license and may be used and copied *
// * only  in  accordance  with  the  terms  of such  license and with the *
// * inclusion of the above copyright notice.  This software  or any other *
// * copies thereof may not be provided or otherwise made available to any *
// * other person.  No title to and  ownership of the  software is  hereby *
// * transferred.                                                          *
// *                                                                       *
// * You may not reverse  engineer, decompile, defeat  license  encryption *
// * mechanisms, or  disassemble this software product or software product *
// * license.  WHMCompleteSolution may terminate this license if you don't *
// * comply with any of the terms and conditions set forth in our end user *
// * license agreement (EULA).  In such event,  licensee  agrees to return *
// * licensor  or destroy  all copies of software  upon termination of the *
// * license.                                                              *
// *                                                                       *
// * Please see the EULA file for the full End User License Agreement.     *
// *                                                                       *
// *************************************************************************
if( !defined('WHMCS') )
{
    exit( "This file cannot be accessed directly" );
}
$GATEWAYMODULE['netbillingname'] = 'netbilling';
$GATEWAYMODULE['netbillingvisiblename'] = 'NETbilling';
$GATEWAYMODULE['netbillingtype'] = 'CC';
function netbilling_activate()
{
    defineGatewayField('netbilling', 'text', 'accountid', '', "Account ID", '20', '');
    defineGatewayField('netbilling', 'text', 'sitetag', '', "Site Tag", '20', '');
}
function netbilling_capture($params)
{
    $payment['account_id'] = $params['accountid'];
    $payment['site_tag'] = $params['sitetag'];
    $payment['tran_type'] = 'S';
    $payment['amount'] = $params['amount'];
    $payment['description'] = "Invoice ID " . $params['invoiceid'];
    $payment['bill_name1'] = $params['clientdetails']['firstname'];
    $payment['bill_name2'] = $params['clientdetails']['lastname'];
    $payment['bill_street'] = $params['clientdetails']['address1'];
    $payment['bill_city'] = $params['clientdetails']['city'];
    $payment['bill_state'] = $params['clientdetails']['state'];
    $payment['bill_zip'] = $params['clientdetails']['postcode'];
    $payment['bill_country'] = $params['clientdetails']['country'];
    $payment['cust_email'] = $params['email'];
    $payment['pay_type'] = 'C';
    $payment['card_number'] = $params['cardnum'];
    $payment['card_expire'] = $params['cardexp'];
    if( $params['cccvv'] )
    {
        $payment['card_cvv2'] = $params['cccvv'];
    }
    else
    {
        $payment['disable_cvv2'] = 1;
    }
    $post_str = '';
    foreach( $payment as $k => $v )
    {
        if( !empty($post_str) )
        {
            $post_str .= "&";
        }
        $post_str .= $k . "=" . urlencode($v);
    }
    $gateway_url = "https://secure.netbilling.com:1402/gw/sas/direct3.1";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $gateway_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 90);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_str);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $res = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if( curl_errno($ch) )
    {
        $curlerror = curl_errno($ch) . " - " . curl_error($ch);
    }
    curl_close($ch);
    $resp = explode("\n\r\n", $res);
    $header = explode("\n", $resp[0]);
    parse_str($resp[1], $result);
    $approved = 0;
    $retry = 0;
    $failed = 0;
    $response_msg = '';
    $desc = "Action => Auth_Capture\nClient => " . $params['clientdetails']['firstname'] . " " . $params['clientdetails']['lastname'] . "\n" . $res;
    if( $curlerror )
    {
        $desc .= "\nCURL Error => " . $curlerror;
    }
    if( $http_code == '200' )
    {
        $status_code = $result['status_code'];
        if( $status_code == '0' || $status_code == 'F' )
        {
            return array( 'status' => 'error', 'rawdata' => $desc );
        }
        if( $status_code == 'D' )
        {
            return array( 'status' => 'declined', 'rawdata' => $desc );
        }
        return array( 'status' => 'success', 'transid' => $result['auth_code'], 'rawdata' => $desc );
    }
    logTransaction('NetBilling', $desc, "Connection Failed");
}