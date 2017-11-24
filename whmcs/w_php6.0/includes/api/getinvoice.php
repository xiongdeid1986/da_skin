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
$result = select_query('tblinvoices', '', array( 'id' => $invoiceid ));
$data = mysql_fetch_array($result);
$invoiceid = $data['id'];
if( !$invoiceid )
{
    $apiresults = array( 'status' => 'error', 'message' => "Invoice ID Not Found" );
}
else
{
    $userid = $data['userid'];
    $invoicenum = $data['invoicenum'];
    $date = $data['date'];
    $duedate = $data['duedate'];
    $datepaid = $data['datepaid'];
    $subtotal = $data['subtotal'];
    $credit = $data['credit'];
    $tax = $data['tax'];
    $tax2 = $data['tax2'];
    $total = $data['total'];
    $taxrate = $data['taxrate'];
    $taxrate2 = $data['taxrate2'];
    $status = $data['status'];
    $paymentmethod = $data['paymentmethod'];
    $notes = $data['notes'];
    $result = select_query('tblaccounts', "SUM(amountin)-SUM(amountout)", array( 'invoiceid' => $invoiceid ));
    $data = mysql_fetch_array($result);
    $amountpaid = $data[0];
    $balance = $total - $amountpaid;
    $balance = format_as_currency($balance);
    $gatewaytype = get_query_val('tblpaymentgateways', 'value', array( 'gateway' => $paymentmethod, 'setting' => 'type' ));
    $ccgateway = $gatewaytype == 'CC' || $gatewaytype == 'OfflineCC' ? true : false;
    $apiresults = array( 'result' => 'success', 'invoiceid' => $invoiceid, 'invoicenum' => $invoicenum, 'userid' => $userid, 'date' => $date, 'duedate' => $duedate, 'datepaid' => $datepaid, 'subtotal' => $subtotal, 'credit' => $credit, 'tax' => $tax, 'tax2' => $tax2, 'total' => $total, 'balance' => $balance, 'taxrate' => $taxrate, 'taxrate2' => $taxrate2, 'status' => $status, 'paymentmethod' => $paymentmethod, 'notes' => $notes, 'ccgateway' => $ccgateway );
    $result = select_query('tblinvoiceitems', '', array( 'invoiceid' => $invoiceid ));
    while( $data = mysql_fetch_array($result) )
    {
        $apiresults['items']['item'][] = array( 'id' => $data['id'], 'type' => $data['type'], 'relid' => $data['relid'], 'description' => $data['description'], 'amount' => $data['amount'], 'taxed' => $data['taxed'] );
    }
    $apiresults['transactions'] = '';
    $result = select_query('tblaccounts', '', array( 'invoiceid' => $invoiceid ));
    while( $data = mysql_fetch_assoc($result) )
    {
        $apiresults['transactions']['transaction'][] = $data;
    }
    $responsetype = 'xml';
}