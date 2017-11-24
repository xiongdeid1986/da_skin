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
$GATEWAY = getGatewayVariables('moneybookers');
if( !$GATEWAY['type'] )
{
    exit( "Module Not Activated" );
}
header("HTTP/1.1 200 OK");
header("Status: 200 OK");
$invoiceid = (int) $whmcs->get_req_var('invoice_id');
$transactionId = $whmcs->get_req_var('transaction_id');
$transid = $_POST['mb_transaction_id'];
$merchant_id = $_POST['merchant_id'];
$mb_amount = $_POST['mb_amount'];
$amount = $_POST['amount'];
$mb_currency = $_POST['mb_currency'];
$currency = $_POST['currency'];
$md5sig = $_POST['md5sig'];
$status = $_POST['status'];
checkCbTransID($_POST['mb_transaction_id']);
if( $GATEWAY['secretword'] )
{
    $md5Secret = strtoupper(md5($GATEWAY['secretword']));
    if( strtoupper(md5($merchant_id . $transactionId . $md5Secret . $mb_amount . $mb_currency . $status)) != $md5sig )
    {
        logTransaction('Skrill', $_REQUEST, "MD5 Signature Failure");
        exit();
    }
}
$result = select_query('tblcurrencies', 'id', array( 'code' => $currency ));
$data = mysql_fetch_array($result);
$currencyid = $data['id'];
if( !$currencyid )
{
    logTransaction('Skrill', $_REQUEST, "Unrecognised Currency");
    exit();
}
if( $GATEWAY['convertto'] )
{
    $result = select_query('tblinvoices', 'userid,total', array( 'id' => $invoiceid ));
    $data = mysql_fetch_array($result);
    $userid = $data['userid'];
    $total = $data['total'];
    $currency = getCurrency($userid);
    $amount = convertCurrency($amount, $currencyid, $currency['id']);
    if( $total < $amount + 1 && $amount - 1 < $total )
    {
        $amount = $total;
    }
}
if( $_POST['status'] == '2' )
{
    $invoiceid = checkCbInvoiceID($invoiceid, 'Skrill');
    addInvoicePayment($invoiceid, $transid, $amount, '', 'moneybookers');
    logTransaction('Skrill', $_REQUEST, 'Successful');
}
else
{
    logTransaction('Skrill', $_REQUEST, 'Unsuccessful');
}