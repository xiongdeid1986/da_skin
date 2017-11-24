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
$GATEWAY = getGatewayVariables('cyberbit');
if( !$GATEWAY['type'] )
{
    exit( "Module Not Activated" );
}
$hash = $_REQUEST['Hash'];
$xml = $_REQUEST['xml'];
$invoiceid = $OrderId = $_REQUEST['OrderId'];
$StatusCode = $_REQUEST['StatusCode'];
$StatusText = $_REQUEST['StatusText'];
$Time = $_REQUEST['Time'];
$invoiceid = explode('-', $invoiceid);
$invoiceid = $invoiceid[1];
$invoiceid = checkCbInvoiceID($invoiceid, 'CyberBit');
$fingerprint = sha1($StatusCode . $StatusText . $OrderId . $Time . $GATEWAY['hashkey']);
if( $fingerprint != $hash )
{
    logTransaction('CyberBit', $_REQUEST, "Invalid Hash");
    redirSystemURL("id=" . $invoiceid . "&paymentfailed=true", "viewinvoice.php");
}
if( $StatusCode == '000' )
{
    logTransaction('CyberBit', $_REQUEST, 'Successful');
    addInvoicePayment($invoiceid, $OrderId, '', '', 'cyberbit');
    $result = select_query('tblinvoices', 'userid', array( 'id' => $invoiceid ));
    $data = mysql_fetch_array($result);
    $userid = $data['userid'];
    update_query('tblclients', array( 'gatewayid' => $OrderId ), array( 'id' => $userid ));
    redirSystemURL("id=" . $invoiceid . "&paymentsuccess=true", "viewinvoice.php");
}
else
{
    logTransaction('CyberBit', $_REQUEST, 'Unsuccessful');
    redirSystemURL("id=" . $invoiceid . "&paymentfailed=true", "viewinvoice.php");
}