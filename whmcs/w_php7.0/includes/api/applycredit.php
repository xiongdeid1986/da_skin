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
if( !function_exists('applyCredit') )
{
    require(ROOTDIR . "/includes/invoicefunctions.php");
}
$data = get_query_vals('tblinvoices', 'id,userid,credit,total,status', array( 'id' => $invoiceid ));
$invoiceid = $data['id'];
if( !$invoiceid )
{
    $apiresults = array( 'result' => 'error', 'message' => "Invoice ID Not Found" );
}
else
{
    $userid = $data['userid'];
    $credit = $data['credit'];
    $total = $data['total'];
    $status = $data['status'];
    $amountpaid = get_query_val('tblaccounts', "SUM(amountin)-SUM(amountout)", array( 'invoiceid' => $invoiceid ));
    $balance = round($total - $amountpaid, 2);
    $amount = $amount == 'full' ? $balance : round($amount, 2);
    $totalcredit = get_query_val('tblclients', 'credit', array( 'id' => $userid ));
    if( $status != 'Unpaid' )
    {
        $apiresults = array( 'result' => 'error', 'message' => "Invoice Not in Unpaid Status" );
    }
    else
    {
        if( $totalcredit < $amount )
        {
            $apiresults = array( 'result' => 'error', 'message' => "Amount exceeds customer credit balance" );
        }
        else
        {
            if( $balance < $amount )
            {
                $apiresults = array( 'result' => 'error', 'message' => "Amount Exceeds Invoice Balance" );
            }
            else
            {
                if( $amount == "0.00" )
                {
                    $apiresults = array( 'result' => 'error', 'message' => "Credit Amount to apply must be greater than zero" );
                }
                else
                {
                    $appliedamount = min($amount, $totalcredit);
                    applyCredit($invoiceid, $userid, $appliedamount, $noemail);
                    $apiresults = array( 'result' => 'success', 'invoiceid' => $invoiceid, 'amount' => $appliedamount, 'invoicepaid' => get_query_val('tblinvoices', 'status', array( 'id' => $invoiceid )) == 'Paid' ? 'true' : 'false' );
                }
            }
        }
    }
}