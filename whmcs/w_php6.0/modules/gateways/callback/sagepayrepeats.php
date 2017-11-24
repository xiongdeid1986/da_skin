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
$GATEWAY = getGatewayVariables('sagepayrepeats');
if( !$GATEWAY['type'] )
{
    exit( "Module Not Activated" );
}
if( $protxsimmode )
{
    $url = "https://test.sagepay.com/simulator/VSPDirectCallback.asp";
}
else
{
    if( $GATEWAY['testmode'] )
    {
        $url = "https://test.sagepay.com/gateway/service/direct3dcallback.vsp";
    }
    else
    {
        $url = "https://live.sagepay.com/gateway/service/direct3dcallback.vsp";
    }
}
$data = "PaRes=" . urlencode($_POST['PaRes']) . "&MD=" . $_POST['MD'];
$data = sagepayrepeats_formatData($_POST);
$response = sagepayrepeats_requestPost($url, $data);
$baseStatus = $response['Status'];
$transdump = '';
foreach( $response as $key => $value )
{
    $transdump .= $key . " => " . $value . "\n";
}
$invoiceid = $_REQUEST['invoiceid'];
if( !$invoiceid && isset($_SESSION['sagepayrepeatsinvoiceid']) )
{
    $invoiceid = $_SESSION['sagepayrepeatsinvoiceid'];
}
$invoiceid = checkCbInvoiceID($invoiceid, "SagePay Repeats 3DAuth");
$userid = get_query_val('tblinvoices', 'userid', array( 'id' => $invoiceid ));
$gatewayid = get_query_val('tblclients', 'gatewayid', array( 'id' => $userid ));
$callbacksuccess = false;
switch( $response['Status'] )
{
    case 'OK':
        checkCbTransID($response['VPSTxId']);
        addInvoicePayment($invoiceid, $response['VPSTxId'], '', '', 'sagepayrepeats', 'on');
        $gatewayid .= $response['VPSTxId'] . ',' . $response['SecurityKey'] . ',' . $response['TxAuthNo'];
        update_query('tblclients', array( 'gatewayid' => $gatewayid, 'cardnum' => '' ), array( 'id' => $userid ));
        logTransaction("SagePay Repeats 3DAuth", $transdump, 'Successful');
        sendMessage("Credit Card Payment Confirmation", $invoiceid);
        $callbacksuccess = true;
        break;
    case 'NOTAUTHED':
        logTransaction("SagePay Repeats 3DAuth", $transdump, "Not Authed");
        sendMessage("Credit Card Payment Failed", $invoiceid);
        update_query('tblclients', array( 'cardtype' => '', 'cardlastfour' => '', 'cardnum' => '', 'expdate' => '', 'issuenumber' => '' ), array( 'id' => $userid ));
        break;
    case 'REJECTED':
        logTransaction("SagePay Repeats 3DAuth", $transdump, 'Rejected');
        sendMessage("Credit Card Payment Failed", $invoiceid);
        update_query('tblclients', array( 'cardtype' => '', 'cardlastfour' => '', 'cardnum' => '', 'expdate' => '', 'issuenumber' => '' ), array( 'id' => $userid ));
        break;
    case 'FAIL':
        logTransaction("SagePay Repeats 3DAuth", $transdump, 'Failed');
        sendMessage("Credit Card Payment Failed", $invoiceid);
        update_query('tblclients', array( 'cardtype' => '', 'cardlastfour' => '', 'cardnum' => '', 'expdate' => '', 'issuenumber' => '' ), array( 'id' => $userid ));
        break;
    default:
        logTransaction("SagePay Repeats 3DAuth", $transdump, 'Error');
        sendMessage("Credit Card Payment Failed", $invoiceid);
        update_query('tblclients', array( 'cardtype' => '', 'cardlastfour' => '', 'cardnum' => '', 'expdate' => '', 'issuenumber' => '' ), array( 'id' => $userid ));
        break;
}
callback3DSecureRedirect($invoiceid, $callbacksuccess);