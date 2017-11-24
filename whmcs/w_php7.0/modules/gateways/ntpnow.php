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
$GATEWAYMODULE['ntpnowname'] = 'ntpnow';
$GATEWAYMODULE['ntpnowvisiblename'] = "Payment Leaf";
$GATEWAYMODULE['ntpnowtype'] = 'CC';
function ntpnow_activate()
{
    defineGatewayField('ntpnow', 'text', 'merchantid', '', "Merchant ID", '20', '');
}
function ntpnow_capture($params)
{
    $url = "https://ntpnow.com/NTPnow_V3_interface.asp";
    $fields['NTPNowID'] = $params['merchantid'];
    $fields['Amount'] = $params['amount'];
    $fields['NameOnCard'] = $params['clientdetails']['firstname'] . " " . $params['clientdetails']['lastname'];
    $fields['Street'] = $params['clientdetails']['address1'];
    $fields['City'] = $params['clientdetails']['city'];
    $fields['State'] = $params['clientdetails']['state'];
    $fields['Zip'] = $params['clientdetails']['postcode'];
    $fields['CreditCardNumber'] = $params['cardnum'];
    $fields['Month'] = substr($params['cardexp'], 0, 2);
    $fields['Year'] = substr($params['cardexp'], 2, 2);
    $fields['AVS'] = 'True';
    if( $params['cccvv'] )
    {
        $fields['CVV2'] = 'True';
        $fields['CVV2Number'] = $params['cccvv'];
    }
    $fields['OrderNumber'] = $params['invoiceid'];
    $poststring = '';
    foreach( $fields as $k => $v )
    {
        $poststring .= $k . "=" . urlencode($v) . "&";
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $poststring);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $result = curl_exec($ch);
    curl_close($ch);
    $responseText = explode("|", $result);
    foreach( $responseText as $k )
    {
        $result1 = explode(":", $k);
        $resultsarray[$result1[0]] = $result1[1];
    }
    $desc = "Action => Auth_Capture\nClient => " . $params['clientdetails']['firstname'] . " " . $params['clientdetails']['lastname'] . "\n";
    foreach( $resultsarray as $k => $v )
    {
        $desc .= $k . " => " . $v . "\n";
    }
    if( $resultsarray['STATUS'] == "TRANSACTION SUCCESSFUL" )
    {
        return array( 'status' => 'success', 'transid' => $resultsarray["Approval Code"], 'rawdata' => $desc );
    }
    return array( 'status' => 'declined', 'rawdata' => $desc );
}