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
if( !function_exists('addInvoicePayment') )
{
    require(ROOTDIR . "/includes/invoicefunctions.php");
}
$whmcs = WHMCS_Application::getinstance();
$id = (int) $whmcs->get_req_var('invoiceid');
$where = array( 'id' => $id );
$result = select_query('tblinvoices', 'id', $where);
$data = mysql_fetch_array($result);
$invoiceid = $data['id'];
if( !$invoiceid )
{
    $apiresults = array( 'result' => 'error', 'message' => "Invoice ID Not Found" );
}
else
{
    $date = $whmcs->get_req_var('date');
    $date = $date ? fromMySQLDate($date) : '';
    $date2 = $whmcs->get_req_var('date2');
    if( $date2 )
    {
        $date = fromMySQLDate($date2);
    }
    $transid = $whmcs->get_req_var('transid');
    $amount = $whmcs->get_req_var('amount');
    $fees = $whmcs->get_req_var('fees');
    $gateway = $whmcs->get_req_var('gateway');
    $noemail = $whmcs->get_req_var('noemail');
    addInvoicePayment($invoiceid, $transid, $amount, $fees, $gateway, $noemail, $date);
    $apiresults = array( 'result' => 'success' );
}