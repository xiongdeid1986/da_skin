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
$GATEWAY = $params = getGatewayVariables('protx');
if( !$GATEWAY['type'] )
{
    exit( "Module Not Activated" );
}
$url = "https://live.sagepay.com/gateway/service/direct3dcallback.vsp";
if( $params['testmode'] == 'on' )
{
    $url = "https://test.sagepay.com/gateway/service/direct3dcallback.vsp";
}
$data = protx_formatData($_POST);
$response = protx_requestPost($url, $data);
$baseStatus = $response['Status'];
$invoiceId = (int) $whmcs->get_req_var('invoiceid');
if( !$invoiceId && WHMCS_Session::get('protxinvoiceid') )
{
    $invoiceId = (int) WHMCS_Session::getanddelete('protxinvoiceid');
}
$response["Invoice ID"] = $invoiceId;
if( $params['cardtype'] == 'Maestro' )
{
    $userId = get_query_val('tblinvoices', 'userid', array( 'id' => $invoiceId ));
    if( !empty($userId) )
    {
        update_query('tblclients', array( 'cardtype' => '', 'cardnum' => '', 'expdate' => '', 'issuenumber' => '', 'startdate' => '' ), array( 'id' => $userId ));
    }
}
$callbackSuccess = false;
$email = "Credit Card Payment Failed";
switch( $response['Status'] )
{
    case 'OK':
        addInvoicePayment($invoiceId, $response['VPSTxId'], '', '', 'protx', 'on');
        $resultText = 'Successful';
        $email = "Credit Card Payment Confirmation";
        $callbackSuccess = true;
        break;
    case 'NOTAUTHED':
        $resultText = "Not Authorised";
        break;
    case 'REJECTED':
        $resultText = 'Rejected';
        break;
    case 'FAIL':
        $resultText = 'Failed';
        break;
    default:
        $resultText = 'Error';
        break;
}
logTransaction($GATEWAY['name'], $response, $resultText);
sendMessage($email, $invoiceId);
callback3DSecureRedirect($invoiceId, $callbackSuccess);