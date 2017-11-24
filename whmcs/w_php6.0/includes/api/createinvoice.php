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
if( !function_exists('getClientsDetails') )
{
    require(ROOTDIR . "/includes/clientfunctions.php");
}
if( !function_exists('updateInvoiceTotal') )
{
    require(ROOTDIR . "/includes/invoicefunctions.php");
}
$result = select_query('tblclients', 'id', array( 'id' => $_POST['userid'] ));
$data = mysql_fetch_array($result);
if( !$data['id'] )
{
    $apiresults = array( 'result' => 'error', 'message' => "Client ID Not Found" );
}
else
{
    $taxrate = $_POST['taxrate'];
    $taxrate2 = $_POST['taxrate2'];
    if( $CONFIG['TaxEnabled'] == 'on' && !$taxrate && !$taxrate2 )
    {
        $clientsdetails = getClientsDetails($_POST['userid']);
        if( !$clientsdetails['taxexempt'] )
        {
            $state = $clientsdetails['state'];
            $country = $clientsdetails['country'];
            $taxdata = getTaxRate(1, $state, $country);
            $taxdata2 = getTaxRate(2, $state, $country);
            $taxrate = $taxdata['rate'];
            $taxrate2 = $taxdata2['rate'];
        }
    }
    $invoiceid = insert_query('tblinvoices', array( 'date' => $_POST['date'], 'duedate' => $_POST['duedate'], 'userid' => $_POST['userid'], 'status' => 'Unpaid', 'taxrate' => $taxrate, 'taxrate2' => $taxrate2, 'paymentmethod' => $_POST['paymentmethod'], 'notes' => $_POST['notes'] ));
    WHMCS_Invoices::adjustincrementfornextinvoice($invoiceid);
    foreach( $_POST as $k => $v )
    {
        if( substr($k, 0, 10) == 'itemamount' )
        {
            $counter = substr($k, 10);
            $description = $_POST['itemdescription' . $counter];
            $amount = $_POST['itemamount' . $counter];
            $taxed = $_POST['itemtaxed' . $counter];
            if( $description )
            {
                insert_query('tblinvoiceitems', array( 'invoiceid' => $invoiceid, 'userid' => $userid, 'description' => $description, 'amount' => $amount, 'taxed' => $taxed ));
            }
        }
    }
    updateInvoiceTotal($invoiceid);
    $invoiceArr = array( 'source' => 'api', 'user' => WHMCS_Session::get('adminid'), 'invoiceid' => $invoiceid );
    run_hook('InvoiceCreation', $invoiceArr);
    if( $_POST['sendinvoice'] )
    {
        run_hook('InvoiceCreationPreEmail', $invoiceArr);
        sendMessage("Invoice Created", $invoiceid);
    }
    if( $autoapplycredit )
    {
        $result = select_query('tblclients', 'credit', array( 'id' => $userid ));
        $data = mysql_fetch_array($result);
        $credit = $data['credit'];
        $result = select_query('tblinvoices', 'total', array( 'id' => $invoiceid ));
        $data = mysql_fetch_array($result);
        $total = $data['total'];
        if( 0 < $credit )
        {
            $doprocesspaid = '';
            if( $total <= $credit )
            {
                $creditleft = $credit - $total;
                $credit = $total;
                $doprocesspaid = true;
            }
            else
            {
                $creditleft = 0;
            }
            logActivity("Credit Automatically Applied at Invoice Creation - Invoice ID: " . $invoiceid . " - Amount: " . $credit, $userid);
            update_query('tblclients', array( 'credit' => $creditleft ), array( 'id' => $userid ));
            update_query('tblinvoices', array( 'credit' => $credit ), array( 'id' => $invoiceid ));
            insert_query('tblcredit', array( 'clientid' => $userid, 'date' => "now()", 'description' => "Credit Applied to Invoice #" . $invoiceid, 'amount' => $credit * (0 - 1) ));
            updateInvoiceTotal($invoiceid);
            if( $doprocesspaid )
            {
                processPaidInvoice($invoiceid);
            }
        }
    }
    run_hook('InvoiceCreated', $invoiceArr);
    $apiresults = array( 'result' => 'success', 'invoiceid' => $invoiceid );
}