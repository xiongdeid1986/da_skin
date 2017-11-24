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
$GATEWAY = getGatewayVariables('paypoint');
if( !$GATEWAY['type'] )
{
    exit( "Module Not Activated" );
}
$transid = $_REQUEST['trans_id'];
$valid = $_REQUEST['valid'];
$authcode = $_REQUEST['auth_code'];
$amount = $_REQUEST['amount'];
$code = $_REQUEST['code'];
$teststatus = $_REQUEST['test_status'];
$hash = $_REQUEST['hash'];
$expiry = $_REQUEST['expiry'];
$card_no = $_REQUEST['card_no'];
$customer = $_REQUEST['customer'];
$invoiceid = explode('-', $transid);
$invoiceid = $invoiceid[0];
$invoiceid = checkCbInvoiceID($invoiceid, 'PayPoint');
if( $GATEWAY['secretword'] )
{
    $string_to_hash = "transid=" . $transid . "&amount=" . $amount . "&callback=" . $GATEWAY['systemurl'] . "/modules/gateways/callback/paypoint.php&" . $GATEWAY['digestkey'];
    $check_key = md5($string_to_hash);
    if( $check_key != $hash )
    {
        logTransaction('PayPoint', $_REQUEST, "MD5 Hash Failure");
        exit();
    }
}
if( $teststatus && !$GATEWAY['testmode'] )
{
    logTransaction('PayPoint', $_REQUEST, "Invalid Test Mode");
    exit();
}
if( $code == 'A' && $valid )
{
    addInvoicePayment($invoiceid, $_REQUEST['x_trans_id'], $amount, $fee, 'tco');
    logTransaction('PayPoint', $_REQUEST, 'Successful');
    echo "<meta http-equiv=\"refresh\" content=\"2;url=" . $CONFIG['SystemURL'] . "/viewinvoice.php?id=" . $invoiceid . "&paymentsuccess=true\">";
}
else
{
    logTransaction('PayPoint', $_REQUEST, 'Unsuccessful');
    echo "<meta http-equiv=\"refresh\" content=\"2;url=" . $CONFIG['SystemURL'] . "/clientarea.php?action=invoices\">";
}