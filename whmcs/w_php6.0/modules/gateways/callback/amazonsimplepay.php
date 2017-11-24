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
require("../../../init.php");
$whmcs->load_function('gateway');
$whmcs->load_function('invoice');
$gatewaymodule = 'amazonsimplepay';
$GATEWAY = getGatewayVariables($gatewaymodule);
if( !$GATEWAY['type'] )
{
    exit( "Module Not Activated" );
}
$status = $_POST['status'];
$invoiceid = $_POST['referenceId'];
$transid = $_POST['transactionId'];
$amount = number_format(substr($_POST['transactionAmount'], strpos($_POST['transactionAmount'], " ")), 2);
$fee = "0.00";
$invoiceid = checkCbInvoiceID($invoiceid, $GATEWAY['name']);
checkCbTransID($transid);
$parameters = $_POST;
if( $GATEWAY['testmode'] )
{
    $url = "https://fps.sandbox.amazonaws.com";
}
else
{
    $url = "https://fps.amazonaws.com";
}
$url .= "?Action=VerifySignature";
$url .= "&UrlEndPoint=" . $CONFIG['SystemURL'] . "/modules/gateways/callback/amazonsimplepay.php";
$url .= "&HttpParameters=" . rawurlencode(http_build_query($parameters));
$url .= "&Version=2008-09-17";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_FILETIME, false);
curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_MAXREDIRS, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
$xmlobject = simplexml_load_string(trim($response));
$results['status'] = (bool) $xmlobject->VerifySignatureResult->VerificationStatus;
if( $status == 'PS' && $results['status'] == 'Success' )
{
    addInvoicePayment($invoiceid, $transid, '', $fee, $gatewaymodule);
    logTransaction($GATEWAY['name'], $_POST, 'Successful');
}
else
{
    logTransaction($GATEWAY['name'], $_POST, 'Unsuccessful');
}