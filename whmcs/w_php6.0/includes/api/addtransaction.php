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
if( !function_exists('addTransaction') )
{
    require(ROOTDIR . "/includes/invoicefunctions.php");
}
if( $userid )
{
    $result = select_query('tblclients', 'id', array( 'id' => $userid ));
    $data = mysql_fetch_array($result);
    if( !$data['id'] )
    {
        $apiresults = array( 'result' => 'error', 'message' => "Client ID Not Found" );
        return NULL;
    }
}
if( $invoiceid )
{
    $result = select_query('tblinvoices', 'id', array( 'id' => (int) $_POST['invoiceid'] ));
    $data = mysql_fetch_array($result);
    $invoiceid = $data['id'];
    if( !$invoiceid )
    {
        $apiresults = array( 'result' => 'error', 'message' => "Invoice ID Not Found" );
        return NULL;
    }
}
if( !$paymentmethod )
{
    $apiresults = array( 'result' => 'error', 'message' => "Payment Method is required" );
}
else
{
    if( $transid && !isUniqueTransactionID($transid, $paymentmethod) )
    {
        $apiresults = array( 'result' => 'error', 'message' => "Transaction ID must be Unique" );
    }
    else
    {
        addTransaction($userid, $currencyid, $description, $amountin, $fees, $amountout, $paymentmethod, $transid, $invoiceid, $date, '', $rate);
        if( $userid && $credit && (!$invoiceid || $invoiceid == 0) )
        {
            if( $transid )
            {
                $description .= " (Trans ID: " . $transid . ")";
            }
            insert_query('tblcredit', array( 'clientid' => $userid, 'date' => toMySQLDate($date), 'description' => $description, 'amount' => $amountin ));
            update_query('tblclients', array( 'credit' => "+=" . $amountin ), array( 'id' => (int) $userid ));
        }
        if( 0 < $invoiceid )
        {
            $totalPaid = get_query_val('tblaccounts', "SUM(amountin)-SUM(amountout)", array( 'invoiceid' => $invoiceid ));
            $invoiceData = get_query_vals('tblinvoices', "status, total", array( 'id' => $invoiceid ));
            $balance = $invoiceData['total'] - $totalPaid;
            if( $balance <= 0 && $invoiceData['status'] == 'Unpaid' )
            {
                processPaidInvoice($invoiceid, '', $date);
            }
        }
        $apiresults = array( 'result' => 'success' );
    }
}