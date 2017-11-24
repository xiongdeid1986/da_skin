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
$result = select_query('tblinvoices', "id, userid", array( 'id' => $invoiceid ));
$data = mysql_fetch_array($result);
$invoiceid = $data['id'];
$userid = $data['userid'];
if( !$invoiceid )
{
    $apiresults = array( 'result' => 'error', 'message' => "Invoice ID Not Found" );
}
else
{
    if( $itemdescription )
    {
        foreach( $itemdescription as $lineid => $description )
        {
            $amount = $itemamount[$lineid];
            $taxed = $itemtaxed[$lineid];
            $update = array( 'userid' => $userid, 'description' => $description, 'amount' => $amount, 'taxed' => $taxed );
            update_query('tblinvoiceitems', $update, array( 'id' => $lineid ));
        }
    }
    if( $newitemdescription )
    {
        foreach( $newitemdescription as $k => $v )
        {
            $description = $v;
            $amount = $newitemamount[$k];
            $taxed = $newitemtaxed[$k];
            $insert = array( 'invoiceid' => $invoiceid, 'userid' => $userid, 'description' => $description, 'amount' => $amount, 'taxed' => $taxed );
            insert_query('tblinvoiceitems', $insert);
        }
    }
    if( $deletelineids )
    {
        foreach( $deletelineids as $lineid )
        {
            delete_query('tblinvoiceitems', array( 'id' => $lineid, 'invoiceid' => $invoiceid ));
        }
    }
    updateInvoiceTotal($invoiceid);
    $updateqry = array(  );
    if( $invoicenum )
    {
        $updateqry['invoicenum'] = $invoicenum;
    }
    if( $date )
    {
        $updateqry['date'] = $date;
    }
    if( $duedate )
    {
        $updateqry['duedate'] = $duedate;
    }
    if( $datepaid )
    {
        $updateqry['datepaid'] = $datepaid;
    }
    if( $subtotal )
    {
        $updateqry['subtotal'] = $subtotal;
    }
    if( $credit )
    {
        $updateqry['credit'] = $credit;
    }
    if( $tax )
    {
        $updateqry['tax'] = $tax;
    }
    if( $tax2 )
    {
        $updateqry['tax2'] = $tax2;
    }
    if( $total )
    {
        $updateqry['total'] = $total;
    }
    if( $taxrate )
    {
        $updateqry['taxrate'] = $taxrate;
    }
    if( $taxrate2 )
    {
        $updateqry['taxrate2'] = $taxrate2;
    }
    if( $status )
    {
        $updateqry['status'] = $status;
    }
    if( $paymentmethod )
    {
        $updateqry['paymentmethod'] = $paymentmethod;
    }
    if( $notes )
    {
        $updateqry['notes'] = $notes;
    }
    if( 0 < count($updateqry) )
    {
        update_query('tblinvoices', $updateqry, array( 'id' => $invoiceid ));
    }
    $apiresults = array( 'result' => 'success', 'invoiceid' => $invoiceid );
}