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
$GATEWAY = getGatewayVariables('quantumvault');
if( !$GATEWAY['type'] )
{
    exit( "Module Not Activated" );
}
$invoiceid = checkCbInvoiceID($_REQUEST['ID'], "Quantum Vault");
$transid = $_REQUEST['transID'];
$transresult = $_REQUEST['trans_result'];
$amount = $_REQUEST['amount'];
$md5_hash = $_REQUEST['md5_hash'];
$vaultid = $_REQUEST['cust_id'];
checkCbTransID($transid);
$ourhash = md5($GATEWAY['md5hash'] . $GATEWAY['loginid'] . $transid . $amount);
if( $ourhash != $md5_hash )
{
    logTransaction("Quantum Vault", $_REQUEST, "MD5 Hash Failure");
    echo "Hash Failure. Please Contact Support.";
    exit();
}
if( $GATEWAY['convertto'] )
{
    $result = select_query('tblinvoices', 'userid,total', array( 'id' => $invoiceid ));
    $data = mysql_fetch_array($result);
    $userid = $data['userid'];
    $total = $data['total'];
    $currency = getCurrency($userid);
    $amount = convertCurrency($amount, $GATEWAY['convertto'], $currency['id']);
    if( $total < $amount + 1 && $amount - 1 < $total )
    {
        $amount = $total;
    }
}
if( $transresult == 'APPROVED' )
{
    update_query('tblclients', array( 'gatewayid' => $vaultid ), array( 'id' => $vaultid ));
    addInvoicePayment($invoiceid, $transid, $amount, '', 'quantumvault', 'on');
    logTransaction("Quantum Vault", $_REQUEST, 'Approved');
    sendMessage("Credit Card Payment Confirmation", $invoiceid);
    callback3DSecureRedirect($invoiceid, true);
}
logTransaction("Quantum Vault", $_REQUEST, 'Declined');
sendMessage("Credit Card Payment Failed", $invoiceid);
callback3DSecureRedirect($invoiceid, false);