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
$GATEWAYMODULE['usaepayname'] = 'usaepay';
$GATEWAYMODULE['usaepayvisiblename'] = "USA ePay";
$GATEWAYMODULE['usaepaytype'] = 'CC';
function usaepay_activate()
{
    defineGatewayField('usaepay', 'text', 'key', '', 'Key', '40', '');
    defineGatewayField('usaepay', 'yesno', 'testmode', '', "Test Mode", '', '');
}
function usaepay_capture($params)
{
    global $remote_ip;
    $url = "https://www.usaepay.com/gate";
    $postfields = array(  );
    $postfields['UMcommand'] = "cc:sale";
    $postfields['UMkey'] = $params['key'];
    $postfields['UMignoreDuplicate'] = 'yes';
    $postfields['UMcard'] = $params['cardnum'];
    $postfields['UMexpir'] = $params['cardexp'];
    $postfields['UMamount'] = $params['amount'];
    $postfields['UMinvoice'] = $params['invoiceid'];
    $postfields['UMname'] = $params['clientdetails']['firstname'] . " " . $params['clientdetails']['lastname'];
    $postfields['UMstreet'] = $params['clientdetails']['address1'];
    $postfields['UMzip'] = $params['clientdetails']['postcode'];
    $postfields['UMcvv2'] = $params['cccvv'];
    $postfields['UMip'] = $remote_ip;
    $query_string = '';
    foreach( $postfields as $k => $v )
    {
        $query_string .= $k . "=" . urlencode($v) . "&";
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    if( curl_error($ch) )
    {
        $result = "CURL Error: " . curl_error($ch);
    }
    curl_close($ch);
    $tmp = split("\n", $result);
    $result = $tmp[count($tmp) - 1];
    parse_str($result, $tmp);
    if( $tmp['UMresult'] == 'A' )
    {
        return array( 'status' => 'success', 'transid' => $tmp['UMrefNum'], 'rawdata' => $tmp );
    }
    return array( 'status' => 'declined', 'rawdata' => $tmp );
}