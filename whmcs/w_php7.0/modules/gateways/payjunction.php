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
$GATEWAYMODULE['payjunctionname'] = 'payjunction';
$GATEWAYMODULE['payjunctionvisiblename'] = "Pay Junction";
$GATEWAYMODULE['payjunctiontype'] = 'CC';
function payjunction_activate()
{
    defineGatewayField('payjunction', 'text', 'dc_logon', '', 'Logon', '20', "The username identifying your account");
    defineGatewayField('payjunction', 'text', 'dc_password', '', 'Password', '20', "The password for your account");
}
function payjunction_capture($params)
{
    $url = "https://payjunction.com/quick_link";
    $fields['dc_logon'] = $params['dc_logon'];
    $fields['dc_password'] = $params['dc_password'];
    $fields['dc_first_name'] = $params['clientdetails']['firstname'];
    $fields['dc_last_name'] = $params['clientdetails']['lastname'];
    $fields['dc_address'] = $params['clientdetails']['address1'];
    $fields['dc_city'] = $params['clientdetails']['city'];
    $fields['dc_state'] = $params['clientdetails']['state'];
    $fields['dc_zipcode'] = $params['clientdetails']['postcode'];
    $fields['dc_country'] = $params['clientdetails']['country'];
    $fields['dc_number'] = $params['cardnum'];
    $fields['dc_expiration_month'] = substr($params['cardexp'], 0, 2);
    $fields['dc_expiration_year'] = substr($params['cardexp'], 2, 2);
    $fields['dc_verification_number'] = $params['cccvv'];
    $fields['dc_transaction_amount'] = $params['amount'];
    $fields['dc_notes'] = $params['description'];
    $fields['dc_transaction_type'] = 'AUTHORIZATION_CAPTURE';
    $fields['dc_test'] = 'No';
    $fields['dc_version'] = "1.2";
    $query_string = '';
    foreach( $fields as $k => $v )
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
    $gatewayresult = curl_exec($ch);
    if( curl_errno($ch) )
    {
        $gatewayresult = "CurlError=" . curl_error($ch);
    }
    curl_close($ch);
    $content = explode(chr(28), $gatewayresult);
    foreach( $content as $key_value )
    {
        list($key, $value) = explode("=", $key_value);
        $response[$key] = $value;
    }
    if( strcmp($response['dc_response_code'], '00') == 0 || strcmp($response['dc_response_code'], '85') == 0 )
    {
        return array( 'status' => 'success', 'transid' => $transid, 'rawdata' => $response );
    }
    return array( 'status' => 'declined', 'rawdata' => $response );
}
function payjunction_refund($params)
{
    $url = "https://payjunction.com/quick_link";
    $fields['dc_logon'] = $params['dc_logon'];
    $fields['dc_password'] = $params['dc_password'];
    $fields['dc_first_name'] = $params['clientdetails']['firstname'];
    $fields['dc_last_name'] = $params['clientdetails']['lastname'];
    $fields['dc_address'] = $params['clientdetails']['address1'];
    $fields['dc_city'] = $params['clientdetails']['city'];
    $fields['dc_state'] = $params['clientdetails']['state'];
    $fields['dc_zipcode'] = $params['clientdetails']['postcode'];
    $fields['dc_country'] = $params['clientdetails']['country'];
    $fields['dc_number'] = $params['cardnum'];
    $fields['dc_expiration_month'] = substr($params['cardexp'], 0, 2);
    $fields['dc_expiration_year'] = substr($params['cardexp'], 2, 2);
    $fields['dc_transaction_amount'] = $params['amount'];
    $fields['dc_notes'] = $params['description'];
    $fields['dc_transaction_type'] = 'CREDIT';
    $fields['dc_version'] = "1.2";
    $query_string = '';
    foreach( $fields as $k => $v )
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
    $gatewayresult = curl_exec($ch);
    if( curl_errno($ch) )
    {
        $gatewayresult = "CurlError=" . curl_error($ch);
    }
    curl_close($ch);
    $content = explode(chr(28), $gatewayresult);
    foreach( $content as $key_value )
    {
        list($key, $value) = explode("=", $key_value);
        $response[$key] = $value;
    }
    $debugoutput = '';
    foreach( $response as $k => $v )
    {
        $debugoutput .= $k . " => " . $v . "\n";
    }
    if( strcmp($response['dc_response_code'], '00') == 0 || strcmp($response['dc_response_code'], '85') == 0 )
    {
        refundInvoicePayment($params['invoiceid'], $transid);
        logTransaction('PayJunction', $debugoutput, 'Successful');
        $result = 'success';
    }
    else
    {
        logTransaction('PayJunction', $debugoutput, 'Declined');
        $result = 'declined';
    }
    return $result;
}