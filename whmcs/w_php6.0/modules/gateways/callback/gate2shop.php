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
$GATEWAY = getGatewayVariables('gate2shop');
if( !$GATEWAY['type'] )
{
    exit( "Module Not Activated" );
}
$cId = $_REQUEST['customField1'];
$invoiceid = checkCbInvoiceID($cId, 'Gate2Shop');
if( isset($_REQUEST['TransactionID']) )
{
    $trId = $_REQUEST['TransactionID'];
}
if( isset($_REQUEST['ErrCode']) )
{
    $errCode = $_REQUEST['ErrCode'];
}
if( isset($_REQUEST['ExErrCode']) )
{
    $exErrCode = $_REQUEST['ExErrCode'];
}
if( isset($_REQUEST['Status']) )
{
    $status = $_REQUEST['Status'];
}
if( isset($_REQUEST['responsechecksum']) )
{
    $responsechecksum = $_REQUEST['responsechecksum'];
}
if( isset($_REQUEST['AuthCode']) )
{
    $authCode = $_REQUEST['AuthCode'];
}
if( isset($_REQUEST['Token']) )
{
    $token = $_REQUEST['Token'];
}
if( isset($_REQUEST['Reason']) )
{
    $reason = $_REQUEST['Reason'];
}
if( isset($_REQUEST['ReasonCode']) )
{
    $ReasonCode = $_REQUEST['ReasonCode'];
}
if( isset($_REQUEST['responsechecksum']) )
{
    $responseChecksum = $_REQUEST['responsechecksum'];
}
if( isset($_REQUEST['totalAmount']) )
{
    $totalAmount = $_REQUEST['totalAmount'];
}
if( isset($_REQUEST['ClientUniqueID']) )
{
    $custId = $_REQUEST['ClientUniqueID'];
}
$sCheckString = $GATEWAY['SecretKey'];
$sCheckString .= $trId;
$sCheckString .= $errCode;
$sCheckString .= $exErrCode;
$sCheckString .= $status;
$checksum = md5($sCheckString);
if( $responseChecksum == $checksum )
{
    if( isset($_REQUEST['ErrCode']) && $_REQUEST['ErrCode'] == 0 && isset($_REQUEST['ExErrCode']) && $_REQUEST['ExErrCode'] == 0 && isset($_REQUEST['Status']) && $_REQUEST['Status'] == 'APPROVED' )
    {
        addInvoicePayment($invoiceid, $trId, '', '', 'gate2shop');
        logTransaction('Gate2Shop', $_REQUEST, 'Successful');
        redirSystemURL("id=" . $invoiceid . "&paymentsuccess=true", "viewinvoice.php");
    }
    else
    {
        logTransaction('Gate2Shop', $_REQUEST, 'Failed');
    }
}
else
{
    logTransaction('Gate2Shop', $_REQUEST, "Checksum Error");
}
redirSystemURL("id=" . $invoiceid . "&paymentfailed=true", "viewinvoice.php");