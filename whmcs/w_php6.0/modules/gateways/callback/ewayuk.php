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
$GATEWAY = getGatewayVariables('ewayuk');
if( !$GATEWAY['type'] )
{
    exit( "Module Not Activated" );
}
$postfields = array(  );
if( $GATEWAY['testmode'] )
{
    $postfields['CustomerID'] = '87654321';
    $postfields['UserName'] = 'TestAccount';
}
else
{
    $postfields['CustomerID'] = $GATEWAY['customerid'];
    $postfields['UserName'] = $GATEWAY['username'];
}
$postfields['AccessPaymentCode'] = $_REQUEST['AccessPaymentCode'];
$merchantposturl = "https://payment.ewaygateway.com/Result/?";
foreach( $postfields as $k => $v )
{
    $merchantposturl .= $k . "=" . urlencode($v) . "&";
}
$response = curlCall($merchantposturl, '');
$authecode = ewayuk_fetch_data($response, "<authCode>", "</authCode>");
$responsecode = ewayuk_fetch_data($response, "<responsecode>", "</responsecode>");
$returnamount = ewayuk_fetch_data($response, "<returnamount>", "</returnamount>");
$txn_id = ewayuk_fetch_data($response, "<trxnnumber>", "</trxnnumber>");
$trxnstatus = ewayuk_fetch_data($response, "<trxnstatus>", "</trxnstatus>");
$invoiceid = ewayuk_fetch_data($response, "<MerchantInvoice>", "</MerchantInvoice>");
$trxnresponsemessage = ewayuk_fetch_data($response, "<trxnresponsemessage>", "</trxnresponsemessage>");
$invoiceid = checkCbInvoiceID($invoiceid, "eWay UK Hosted Payments");
$response = array( 'response' => $response );
if( $trxnstatus == 'true' )
{
    logTransaction("eWay UK Hosted Payments", array_merge($_REQUEST, $postfields, $response), 'Successful');
    addInvoicePayment($invoiceid, $txn_id, $returnamount, '', 'ewayuk');
    redirSystemURL("id=" . $invoiceid . "&paymentsuccess=true", "viewinvoice.php");
}
else
{
    logTransaction("eWay UK Hosted Payments", array_merge($_REQUEST, $postfields, $response), 'Error');
    redirSystemURL("id=" . $invoiceid . "&paymentfailed=true", "viewinvoice.php");
}