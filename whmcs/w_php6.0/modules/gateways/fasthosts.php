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
$GATEWAYMODULE['fasthostsname'] = 'fasthosts';
$GATEWAYMODULE['fasthostsvisiblename'] = 'FastHosts';
$GATEWAYMODULE['fasthoststype'] = 'CC';
function fasthosts_activate()
{
    defineGatewayField('fasthosts', 'text', 'merchantid', '', "Merchant ID", '20', '');
    defineGatewayField('fasthosts', 'text', 'paymentkey', '', "Payment Key", '20', '');
}
function fasthosts_capture($params)
{
    if( $params['cardtype'] == 'Visa' )
    {
        $cardtype = 'VI';
    }
    else
    {
        if( $params['cardtype'] == 'MasterCard' )
        {
            $cardtype = 'MC';
        }
        else
        {
            if( $params['cardtype'] == "American Express" )
            {
                $cardtype = 'AM';
            }
            else
            {
                if( $params['cardtype'] == "Diners Club" )
                {
                    $cardtype = 'DC';
                }
                else
                {
                    if( $params['cardtype'] == 'Discover' )
                    {
                        $cardtype = 'DI';
                    }
                    else
                    {
                        if( $params['cardtype'] == 'JCB' )
                        {
                            $cardtype = 'JC';
                        }
                        else
                        {
                            if( $params['cardtype'] == 'Delta' )
                            {
                                $cardtype = 'VD';
                            }
                            else
                            {
                                if( $params['cardtype'] == 'Solo' )
                                {
                                    $cardtype = 'MD';
                                }
                                else
                                {
                                    if( $params['cardtype'] == 'Maestro' )
                                    {
                                        $cardtype = 'MD';
                                    }
                                    else
                                    {
                                        if( $params['cardtype'] == 'Switch' )
                                        {
                                            $cardtype = 'MD';
                                        }
                                        else
                                        {
                                            if( $params['cardtype'] == 'Electron' )
                                            {
                                                $cardtype = 'VE';
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    $fasthosts[cardType] = $cardtype;
    $fasthosts[cardNumber] = $params['cardnum'];
    $fasthosts[cardExp] = substr($params['cardexp'], 0, 2) . '/' . substr($params['cardexp'], 2, 2);
    $fasthosts[issueNumber] = $params['cardissuenum'];
    $fasthosts[amount] = $params['amount'] * 100;
    $fasthosts[operation] = 'P';
    $fasthosts[merchantTxn] = $params['invoiceid'];
    if( $params['cccvv'] )
    {
        $fasthosts[cvdIndicator] = '1';
        $fasthosts[cvdValue] = $params['cccvv'];
    }
    $fasthosts[custName1] = $params['clientdetails']['firstname'] . " " . $params['clientdetails']['lastname'];
    $fasthosts[streetAddr] = $params['clientdetails']['address1'];
    $fasthosts[streetAddr2] = $params['clientdetails']['address2'];
    $fasthosts[city] = $params['clientdetails']['city'];
    $fasthosts[province] = $params['clientdetails']['state'];
    $fasthosts[zip] = $params['clientdetails']['postcode'];
    $fasthosts[country] = $params['clientdetails']['country'];
    $fasthosts[phone] = $params['clientdetails']['phonenumber'];
    $fasthosts[email] = $params['clientdetails']['email'];
    $fasthosts['merchantID'] = $params['merchantid'];
    $fasthosts['paymentKey'] = $params['paymentkey'];
    $fasthosts['clientVersion'] = "1.1";
    $fasthosts['operation'] = 'P';
    $data_stream = '';
    $url = "https://www.e-merchant.co.uk/api/";
    foreach( $fasthosts as $k => $v )
    {
        if( 0 < strlen($data_stream) )
        {
            $data_stream .= "&";
        }
        $data_stream .= $k . "=" . urlencode($v);
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_stream);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    $result_tmp = curl_exec($ch);
    curl_close($ch);
    $result_tmp = explode("&", urldecode($result_tmp));
    foreach( $result_tmp as $v )
    {
        list($key, $val) = explode("=", $v);
        $result[$key] = $val;
    }
    $desc = "Action => Auth_Capture\nClient => " . $params['clientdetails']['firstname'] . " " . $params['clientdetails']['lastname'] . "\n";
    foreach( $result as $key => $value )
    {
        $desc .= $key . " => " . $value . "\n";
    }
    if( $result['status'] == 'SP' )
    {
        return array( 'status' => 'success', 'transid' => $result['txnNumber'], 'rawdata' => $desc );
    }
    if( $result['status'] == 'E' )
    {
        return array( 'status' => 'declined', 'rawdata' => $desc );
    }
    return array( 'status' => 'error', 'rawdata' => $desc );
}