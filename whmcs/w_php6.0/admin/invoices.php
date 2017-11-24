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
define('ADMINAREA', true);
require("../init.php");
$action = $whmcs->get_req_var('action');
if( $action == 'edit' || $action == 'invtooltip' )
{
    $reqperm = "Manage Invoice";
}
else
{
    if( $action == 'createinvoice' )
    {
        $reqperm = "Create Invoice";
    }
    else
    {
        $reqperm = "List Invoices";
    }
}
$aInt = new WHMCS_Admin($reqperm);
if( $action == 'edit' )
{
    $pageicon = 'invoicesedit';
    $pagetitle = $aInt->lang('fields', 'invoicenum') . $id;
}
else
{
    $pageicon = 'invoices';
    $pagetitle = $aInt->lang('invoices', 'title');
}
$aInt->title = $pagetitle;
$aInt->sidebar = 'billing';
$aInt->icon = $pageicon;
$aInt->requiredFiles(array( 'clientfunctions', 'invoicefunctions', 'gatewayfunctions', 'processinvoices', 'ccfunctions' ));
$invoiceid = (int) $whmcs->get_req_var('invoiceid');
$status = $whmcs->get_req_var('status');
if( !in_array($status, array( 'Unpaid', 'Overdue', 'Paid', 'Cancelled', 'Refunded', 'Collections' )) )
{
    $status = '';
}
if( $action == 'invtooltip' )
{
    check_token("WHMCS.admin.default");
    echo "<table bgcolor=\"#cccccc\" cellspacing=\"1\" cellpadding=\"3\"><tr bgcolor=\"#efefef\" style=\"text-align:center;font-weight:bold;\"><td>" . $aInt->lang('fields', 'description') . "</td><td>" . $aInt->lang('fields', 'amount') . "</td></tr>";
    $currency = getCurrency($userid);
    $result = select_query('tblinvoiceitems', '', array( 'invoiceid' => $id ), 'id', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $lineid = $data['id'];
        echo "<tr bgcolor=\"#ffffff\"><td width=\"275\">" . nl2br($data['description']) . "</td><td width=\"100\" style=\"text-align:right;\">" . formatCurrency($data['amount']) . "</td></tr>";
    }
    $data = get_query_vals('tblinvoices', 'subtotal,credit,tax,tax2,taxrate,taxrate2,total', array( 'id' => $id ), 'id', 'ASC');
    echo "<tr bgcolor=\"#efefef\" style=\"text-align:right;font-weight:bold;\"><td>" . $aInt->lang('fields', 'subtotal') . "&nbsp;</td><td>" . formatCurrency($data['subtotal']) . "</td></tr>";
    if( $CONFIG['TaxEnabled'] )
    {
        if( 0 < $data['tax'] )
        {
            echo "<tr bgcolor=\"#efefef\" style=\"text-align:right;font-weight:bold;\"><td>" . $data['taxrate'] . "% " . $aInt->lang('fields', 'tax') . "&nbsp;</td><td>" . formatCurrency($data['tax']) . "</td></tr>";
        }
        if( 0 < $data['tax2'] )
        {
            echo "<tr bgcolor=\"#efefef\" style=\"text-align:right;font-weight:bold;\"><td>" . $data['taxrate2'] . "% " . $aInt->lang('fields', 'tax') . "&nbsp;</td><td>" . formatCurrency($data['tax2']) . "</td></tr>";
        }
    }
    echo "<tr bgcolor=\"#efefef\" style=\"text-align:right;font-weight:bold;\"><td>" . $aInt->lang('fields', 'credit') . "&nbsp;</td><td>" . formatCurrency($data['credit']) . "</td></tr>";
    echo "<tr bgcolor=\"#efefef\" style=\"text-align:right;font-weight:bold;\"><td>" . $aInt->lang('fields', 'totaldue') . "&nbsp;</td><td>" . formatCurrency($data['total']) . "</td></tr>";
    echo "</table>";
    exit();
}
if( $action == 'createinvoice' )
{
    check_token("WHMCS.admin.default");
    if( !checkActiveGateway() )
    {
        $aInt->gracefulExit($aInt->lang('gateways', 'nonesetup'));
    }
    $gateway = getClientsPaymentMethod($userid);
    if( $CONFIG['TaxEnabled'] == 'on' )
    {
        $clientsdetails = getClientsDetails($userid);
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
    $duedate = date('Ymd', mktime(0, 0, 0, date('m'), date('d') + $CONFIG['CreateInvoiceDaysBefore'], date('Y')));
    $invoiceid = insert_query('tblinvoices', array( 'date' => "now()", 'duedate' => $duedate, 'userid' => $userid, 'status' => 'Unpaid', 'paymentmethod' => $gateway, 'taxrate' => $taxrate, 'taxrate2' => $taxrate2 ));
    logActivity("Created Manual Invoice - Invoice ID: " . $invoiceid, $userid);
    WHMCS_Invoices::adjustincrementfornextinvoice($invoiceid);
    $invoiceArr = array( 'source' => 'adminarea', 'user' => WHMCS_Session::get('adminid'), 'invoiceid' => $invoiceid );
    run_hook('InvoiceCreation', $invoiceArr);
    run_hook('InvoiceCreationAdminArea', $invoiceArr);
    redir("action=edit&id=" . $invoiceid);
}
$filters = new WHMCS_Filter();
if( $whmcs->get_req_var('markpaid') )
{
    check_token("WHMCS.admin.default");
    checkPermission("Manage Invoice");
    foreach( $selectedinvoices as $invid )
    {
        $result2 = select_query('tblinvoices', 'paymentmethod', array( 'id' => $invid ));
        $data = mysql_fetch_array($result2);
        $paymentmethod = $data['paymentmethod'];
        addInvoicePayment($invid, '', '', '', $paymentmethod);
    }
    $filters->redir();
}
if( $whmcs->get_req_var('markunpaid') )
{
    check_token("WHMCS.admin.default");
    checkPermission("Manage Invoice");
    foreach( $selectedinvoices as $invid )
    {
        update_query('tblinvoices', array( 'status' => 'Unpaid', 'datepaid' => "0000-00-00 00:00:00" ), array( 'id' => $invid ));
        logActivity("Reactivated Invoice - Invoice ID: " . $invid);
        run_hook('InvoiceUnpaid', array( 'invoiceid' => $invid ));
    }
    $filters->redir();
}
if( $whmcs->get_req_var('markcancelled') )
{
    check_token("WHMCS.admin.default");
    checkPermission("Manage Invoice");
    foreach( $selectedinvoices as $invid )
    {
        update_query('tblinvoices', array( 'status' => 'Cancelled' ), array( 'id' => $invid ));
        logActivity("Cancelled Invoice - Invoice ID: " . $invid);
        run_hook('InvoiceCancelled', array( 'invoiceid' => $invid ));
    }
    $filters->redir();
}
if( $whmcs->get_req_var('duplicateinvoice') )
{
    check_token("WHMCS.admin.default");
    foreach( $selectedinvoices as $invid )
    {
        $invoices = new WHMCS_Invoices();
        $invoices->duplicate($invid);
    }
    $filters->redir();
}
if( $whmcs->get_req_var('massdelete') )
{
    check_token("WHMCS.admin.default");
    checkPermission("Delete Invoice");
    foreach( $selectedinvoices as $invid )
    {
        delete_query('tblinvoices', array( 'id' => $invid ));
        logActivity("Deleted Invoice - Invoice ID: " . $invid);
    }
    $filters->redir();
}
if( $whmcs->get_req_var('paymentreminder') )
{
    check_token("WHMCS.admin.default");
    foreach( $selectedinvoices as $invid )
    {
        sendMessage("Invoice Payment Reminder", $invid);
        logActivity("Invoice Payment Reminder Sent - Invoice ID: " . $invid);
    }
    $filters->redir();
}
if( $whmcs->get_req_var('delete') )
{
    check_token("WHMCS.admin.default");
    checkPermission("Delete Invoice");
    delete_query('tblinvoices', array( 'id' => $invoiceid ));
    logActivity("Deleted Invoice - Invoice ID: " . $invoiceid);
    $filters->redir();
}
ob_start();
if( $action == '' )
{
    $aInt->deleteJSConfirm('doDelete', 'invoices', 'delete', $_SERVER['PHP_SELF'] . "?status=" . $status . "&delete=true&invoiceid=");
    $name = 'invoices';
    $orderby = 'duedate';
    $sort = 'DESC';
    $pageObj = new WHMCS_Pagination($name, $orderby, $sort);
    $pageObj->digestCookieData();
    $tbl = new WHMCS_ListTable($pageObj);
    $tbl->setColumns(array( 'checkall', array( 'id', $aInt->lang('fields', 'invoicenum') ), array( 'clientname', $aInt->lang('fields', 'clientname') ), array( 'date', $aInt->lang('fields', 'invoicedate') ), array( 'duedate', $aInt->lang('fields', 'duedate') ), array( 'total', $aInt->lang('fields', 'total') ), array( 'paymentmethod', $aInt->lang('fields', 'paymentmethod') ), array( 'status', $aInt->lang('fields', 'status') ), '', '' ));
    $invoicesModel = new WHMCS_Invoices($pageObj);
    if( checkPermission("View Income Totals", true) )
    {
        $invoicetotals = $invoicesModel->getInvoiceTotals();
        if( count($invoicetotals) )
        {
            echo "<div class=\"contentbox\" style=\"font-size:18px;\">";
            foreach( $invoicetotals as $vals )
            {
                echo "<b>" . $vals['currencycode'] . "</b> " . $aInt->lang('status', 'paid') . ": <span class=\"textgreen\"><b>" . $vals['paid'] . "</b></span> " . $aInt->lang('status', 'unpaid') . ": <span class=\"textred\"><b>" . $vals['unpaid'] . "</b></span> " . $aInt->lang('status', 'overdue') . ": <span class=\"textblack\"><b>" . $vals['overdue'] . "</b></span><br />";
            }
            echo "</div><br />";
        }
    }
    echo $aInt->Tabs(array( $aInt->lang('global', 'searchfilter') ), true);
    $clientid = $filters->get('clientid');
    $invoicenum = $filters->get('invoicenum');
    echo "\n<div id=\"tab0box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n<!-- Filter -->\n<form action=\"";
    echo $whmcs->getPhpSelf();
    echo "\" method=\"post\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">";
    echo $aInt->lang('fields', 'clientname');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"clientname\" size=\"25\" value=\"";
    echo $clientname = $filters->get('clientname');
    echo "\"></td><td width=\"15%\" class=\"fieldlabel\">";
    echo $aInt->lang('fields', 'invoicedate');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"invoicedate\" size=\"15\" value=\"";
    echo $invoicedate = $filters->get('invoicedate');
    echo "\" class=\"datepick\"></td></tr>\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang('fields', 'lineitem');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"lineitem\" size=\"40\" value=\"";
    echo $lineitem = $filters->get('lineitem');
    echo "\"></td><td width=\"15%\" class=\"fieldlabel\">";
    echo $aInt->lang('fields', 'duedate');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"duedate\" size=\"15\" value=\"";
    echo $duedate = $filters->get('duedate');
    echo "\" class=\"datepick\"></td></tr>\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang('fields', 'paymentmethod');
    echo "</td><td class=\"fieldarea\">";
    $paymentmethod = $filters->get('paymentmethod');
    echo paymentMethodsSelection($aInt->lang('global', 'any'));
    echo "</td><td width=\"15%\" class=\"fieldlabel\">";
    echo $aInt->lang('fields', 'datepaid');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"datepaid\" size=\"15\" value=\"";
    echo $datepaid = $filters->get('datepaid');
    echo "\" class=\"datepick\"></td></tr>\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang('fields', 'status');
    echo "</td><td class=\"fieldarea\"><select name=\"status\">\n<option value=\"\">";
    echo $aInt->lang('global', 'any');
    echo "</option>\n<option value=\"Unpaid\"";
    $status = $filters->get('status');
    if( $status == 'Unpaid' )
    {
        echo " selected";
    }
    echo ">";
    echo $aInt->lang('status', 'unpaid');
    echo "</option>\n<option value=\"Overdue\"";
    if( $status == 'Overdue' )
    {
        echo " selected";
    }
    echo ">";
    echo $aInt->lang('status', 'overdue');
    echo "</option>\n<option value=\"Paid\"";
    if( $status == 'Paid' )
    {
        echo " selected";
    }
    echo ">";
    echo $aInt->lang('status', 'paid');
    echo "</option>\n<option value=\"Cancelled\"";
    if( $status == 'Cancelled' )
    {
        echo " selected";
    }
    echo ">";
    echo $aInt->lang('status', 'cancelled');
    echo "</option>\n<option value=\"Refunded\"";
    if( $status == 'Refunded' )
    {
        echo " selected";
    }
    echo ">";
    echo $aInt->lang('status', 'refunded');
    echo "</option>\n<option value=\"Collections\"";
    if( $status == 'Collections' )
    {
        echo " selected";
    }
    echo ">";
    echo $aInt->lang('status', 'collections');
    echo "</option>\n</select></td><td class=\"fieldlabel\">";
    echo $aInt->lang('fields', 'totaldue');
    echo "</td><td class=\"fieldarea\">";
    echo $aInt->lang('filters', 'from');
    echo " <input type=\"text\" name=\"totalfrom\" size=\"10\" value=\"";
    echo $totalfrom = $filters->get('totalfrom');
    echo "\"> ";
    echo $aInt->lang('filters', 'to');
    echo " <input type=\"text\" name=\"totalto\" size=\"10\" value=\"";
    echo $totalto = $filters->get('totalto');
    echo "\"></td></tr>\n<tr></tr>\n</table>\n\n<img src=\"images/spacer.gif\" height=\"5\" width=\"1\" /><br />\n<div align=\"center\"><input type=\"submit\" value=\"";
    echo $aInt->lang('global', 'search');
    echo "\" class=\"button\" /></div>\n\n</form>\n\n  </div>\n</div>\n\n<br />\n\n<script src=\"../includes/jscript/jquerytt.js\"></script>\n\n";
    $jquerycode = "\$(\".invtooltip\").tooltip({cssClass:\"invoicetooltip\"});";
    $aInt->jquerycode = $jquerycode;
    $filters->store();
    $criteria = array( 'clientid' => $clientid, 'clientname' => $clientname, 'invoicenum' => $invoicenum, 'lineitem' => $lineitem, 'paymentmethod' => $paymentmethod, 'invoicedate' => $invoicedate, 'duedate' => $duedate, 'datepaid' => $datepaid, 'totalfrom' => $totalfrom, 'totalto' => $totalto, 'status' => $status );
    $invoicesModel->execute($criteria);
    $numresults = $pageObj->getNumResults();
    if( $filters->isActive() && $numresults == 1 )
    {
        $invoice = $pageObj->getOne();
        redir("action=edit&id=" . $invoice['id'], "invoices.php");
    }
    else
    {
        $invoicelist = $pageObj->getData();
        foreach( $invoicelist as $invoice )
        {
            $linkopen = "<a href=\"invoices.php?action=edit&id=" . $invoice['id'] . "\">";
            $linkclose = "</a>";
            $tbl->addRow(array( "<input type=\"checkbox\" name=\"selectedinvoices[]\" value=\"" . $invoice['id'] . "\" class=\"checkall\">", $linkopen . $invoice['invoicenum'] . $linkclose, $invoice['clientname'], $invoice['date'], $invoice['duedate'], "<a href=\"invoices.php?action=invtooltip&id=" . $invoice['id'] . "&userid=" . $invoice['userid'] . generate_token('link') . "\" class=\"invtooltip\" lang=\"\">" . $invoice['totalformatted'] . "</a>", $invoice['paymentmethod'], $invoice['statusformatted'], $linkopen . "<img src=\"images/edit.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"Edit\">" . $linkclose, "<a href=\"#\" onClick=\"doDelete('" . $invoice['id'] . "');return false\"><img src=\"images/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"Delete\"></a>" ));
        }
        $tbl->setMassActionBtns("<input type=\"submit\" value=\"" . $aInt->lang('invoices', 'markpaid') . "\" class=\"btn-success\" name=\"markpaid\" onclick=\"return confirm('" . $aInt->lang('invoices', 'markpaidconfirm', '1') . "')\" /> <input type=\"submit\" value=\"" . $aInt->lang('invoices', 'markunpaid') . "\" name=\"markunpaid\" onclick=\"return confirm('" . $aInt->lang('invoices', 'markunpaidconfirm', '1') . "')\" /> <input type=\"submit\" value=\"" . $aInt->lang('invoices', 'markcancelled') . "\" name=\"markcancelled\" onclick=\"return confirm('" . $aInt->lang('invoices', 'markcancelledconfirm', '1') . "')\" /> <input type=\"submit\" value=\"" . $aInt->lang('invoices', 'duplicateinvoice') . "\" name=\"duplicateinvoice\" onclick=\"return confirm('" . $aInt->lang('invoices', 'duplicateinvoiceconfirm', '1') . "')\" /> <input type=\"submit\" value=\"" . $aInt->lang('invoices', 'sendreminder') . "\" name=\"paymentreminder\" onclick=\"return confirm('" . $aInt->lang('invoices', 'sendreminderconfirm', '1') . "')\" /> <input type=\"submit\" value=\"" . $aInt->lang('global', 'delete') . "\" class=\"btn-danger\" name=\"massdelete\"  onclick=\"return confirm('" . $aInt->lang('invoices', 'massdeleteconfirm', '1') . "')\" />");
        echo $tbl->output();
        unset($clientlist);
        unset($invoicesModel);
    }
}
else
{
    if( $action == 'edit' )
    {
        $result = select_query('tblinvoices', 'userid,paymentmethod', array( 'id' => $id ));
        $data = mysql_fetch_array($result);
        $userid = $data[0];
        $oldpaymentmethod = $data[1];
        $aInt->assertClientBoundary($userid);
        if( $saveoptions )
        {
            check_token("WHMCS.admin.default");
            update_query('tblinvoices', array( 'date' => toMySQLDate($invoicedate), 'duedate' => toMySQLDate($datedue), 'paymentmethod' => $paymentmethod, 'invoicenum' => $invoicenum, 'taxrate' => $taxrate, 'taxrate2' => $taxrate2, 'status' => $status ), array( 'id' => $id ));
            updateInvoiceTotal($id);
            if( $oldpaymentmethod != $paymentmethod )
            {
                run_hook('InvoiceChangeGateway', array( 'invoiceid' => $id, 'paymentmethod' => $paymentmethod ));
            }
            logActivity("Modified Invoice Options - Invoice ID: " . $id, $userid);
            redir("action=edit&id=" . $id);
        }
        if( $save == 'notes' )
        {
            check_token("WHMCS.admin.default");
            update_query('tblinvoices', array( 'notes' => $notes ), array( 'id' => $id ));
            logActivity("Modified Invoice Notes - Invoice ID: " . $id, $userid);
            redir("action=edit&id=" . $id);
        }
        if( $sub == 'statuscancelled' )
        {
            check_token("WHMCS.admin.default");
            $set = array( 'status' => 'Cancelled', 'datepaid' => "0000-00-00 00:00:00" );
            $where = array( 'id' => $id );
            update_query('tblinvoices', $set, $where);
            logActivity("Cancelled Invoice - Invoice ID: " . $id, $userid);
            run_hook('InvoiceCancelled', array( 'invoiceid' => $id ));
            redir("action=edit&id=" . $id);
        }
        if( $sub == 'statusunpaid' )
        {
            check_token("WHMCS.admin.default");
            $set = array( 'status' => 'Unpaid', 'datepaid' => "0000-00-00 00:00:00" );
            $where = array( 'id' => $id );
            update_query('tblinvoices', $set, $where);
            logActivity("Reactivated Invoice - Invoice ID: " . $id, $userid);
            run_hook('InvoiceUnpaid', array( 'invoiceid' => $id ));
            redir("action=edit&id=" . $id);
        }
        if( $sub == 'markpaid' )
        {
            check_token("WHMCS.admin.default");
            checkPermission("Add Transaction");
            if( $sendconfirmation == 'on' )
            {
                $sendconfirmation = '';
            }
            else
            {
                $sendconfirmation = 'on';
            }
            addInvoicePayment($id, $transid, $amount, $fees, $paymentmethod, $sendconfirmation, $date);
            redir("action=edit&id=" . $id);
        }
        if( $sub == 'save' )
        {
            check_token("WHMCS.admin.default");
            if( $description )
            {
                foreach( $description as $lineid => $desc )
                {
                    update_query('tblinvoiceitems', array( 'description' => $desc, 'amount' => $amount[$lineid], 'taxed' => $taxed[$lineid] ), array( 'id' => $lineid ));
                }
            }
            if( $adddescription )
            {
                insert_query('tblinvoiceitems', array( 'invoiceid' => $id, 'userid' => $userid, 'description' => $adddescription, 'amount' => $addamount, 'taxed' => $addtaxed ));
            }
            if( $selaction == 'delete' && is_array($itemids) )
            {
                foreach( $itemids as $itemid )
                {
                    delete_query('tblinvoiceitems', array( 'id' => $itemid ));
                }
            }
            if( $selaction == 'split' && is_array($itemids) )
            {
                $result = select_query('tblinvoices', 'userid,date,duedate,taxrate,taxrate2,paymentmethod', array( 'id' => $id ));
                $data = mysql_fetch_array($result);
                $userid = $data[0];
                $date = $data[1];
                $duedate = $data[2];
                $taxrate = $data[3];
                $taxrate2 = $data[4];
                $paymentmethod = $data[5];
                $result = select_query('tblinvoiceitems', "COUNT(*)", array( 'invoiceid' => $id ));
                $data = mysql_fetch_array($result);
                $totalitemscount = $data[0];
                if( count($itemids) < $totalitemscount )
                {
                    $invoiceid = insert_query('tblinvoices', array( 'date' => $date, 'duedate' => $duedate, 'userid' => $userid, 'status' => 'Unpaid', 'paymentmethod' => $paymentmethod, 'taxrate' => $taxrate, 'taxrate2' => $taxrate2 ));
                    WHMCS_Invoices::adjustincrementfornextinvoice($invoiceid);
                    foreach( $itemids as $itemid )
                    {
                        update_query('tblinvoiceitems', array( 'invoiceid' => $invoiceid ), array( 'id' => $itemid ));
                    }
                    updateInvoiceTotal($invoiceid);
                    updateInvoiceTotal($id);
                    logActivity("Split Invoice - Invoice ID: " . $id . " to Invoice ID: " . $invoiceid, $userid);
                    $invoiceArr = array( 'source' => 'adminarea', 'user' => WHMCS_Session::get('adminid'), 'invoiceid' => $invoiceid );
                    run_hook('InvoiceCreation', $invoiceArr);
                    run_hook('InvoiceCreationAdminArea', $invoiceArr);
                    run_hook('InvoiceSplit', array( 'originalinvoiceid' => $id, 'newinvoiceid' => $invoiceid ));
                    redir("action=edit&id=" . $invoiceid);
                }
            }
            updateInvoiceTotal($id);
            $result = select_query('tblinvoices', 'userid', array( 'id' => $id ));
            $data = mysql_fetch_array($result);
            $userid = $data[0];
            logActivity("Modified Invoice - Invoice ID: " . $id, $userid);
            redir("action=edit&id=" . $id);
        }
        if( $addcredit != "0.00" && $addcredit )
        {
            check_token("WHMCS.admin.default");
            $result2 = select_query('tblinvoices', 'userid,subtotal,credit,total', array( 'id' => $id ));
            $data = mysql_fetch_array($result2);
            $userid = $data['userid'];
            $subtotal = $data['subtotal'];
            $credit = $data['credit'];
            $total = $data['total'];
            $result2 = select_query('tblaccounts', "SUM(amountin)-SUM(amountout)", array( 'invoiceid' => $id ));
            $data = mysql_fetch_array($result2);
            $amountpaid = $data[0];
            $balance = $total - $amountpaid;
            if( $CONFIG['TaxType'] == 'Inclusive' )
            {
                $subtotal = $total;
            }
            $addcredit = round($addcredit, 2);
            $balance = round($balance, 2);
            $result2 = select_query('tblclients', 'credit', array( 'id' => $userid ));
            $data = mysql_fetch_array($result2);
            $totalcredit = $data['credit'];
            if( $totalcredit < $addcredit )
            {
                redir("action=edit&id=" . $id . "&creditapply=exceedbalance");
            }
            else
            {
                if( $balance < $addcredit )
                {
                    redir("action=edit&id=" . $id . "&creditapply=exceedtotal");
                }
                else
                {
                    applyCredit($id, $userid, $addcredit);
                    $currency = getCurrency($userid);
                    redir("action=edit&id=" . $id . "&creditapply=success&amt=" . $addcredit);
                }
            }
        }
        if( $removecredit != "0.00" && $removecredit != '' )
        {
            check_token("WHMCS.admin.default");
            $result2 = select_query('tblinvoices', 'userid,subtotal,credit,total,status', array( 'id' => $id ));
            $data = mysql_fetch_array($result2);
            $userid = $data['userid'];
            $subtotal = $data['subtotal'];
            $credit = $data['credit'];
            $total = $data['total'];
            $status = $data['status'];
            if( $credit < $removecredit )
            {
                redir("action=edit&id=" . $id . "&creditremove=exceedtotal");
            }
            else
            {
                update_query('tblinvoices', array( 'credit' => "-=" . $removecredit ), array( 'id' => (int) $id ));
                updateInvoiceTotal($id);
                update_query('tblclients', array( 'credit' => "+=" . $removecredit ), array( 'id' => (int) $userid ));
                insert_query('tblcredit', array( 'clientid' => $userid, 'date' => "now()", 'description' => "Credit Removed from Invoice #" . $id, 'amount' => $removecredit ));
                logActivity("Credit Removed - Amount: " . $removecredit . " - Invoice ID: " . $id, $userid);
                if( $status == 'Paid' )
                {
                    update_query('tblinvoices', array( 'status' => 'Refunded' ), array( 'id' => $id ));
                }
                redir("action=edit&id=" . $id . "&creditremove=success&amt=" . $removecredit);
            }
        }
        if( $sub == 'delete' )
        {
            check_token("WHMCS.admin.default");
            delete_query('tblinvoiceitems', array( 'id' => $iid ));
            updateInvoiceTotal($id);
            redir("action=edit&id=" . $id);
        }
        $result = select_query('tblinvoices', "tblpaymentgateways.value", array( "tblpaymentgateways.setting" => 'type', "tblinvoices.id" => $id ), '', '', '', "tblclients ON tblclients.id=tblinvoices.userid INNER JOIN tblpaymentgateways ON tblpaymentgateways.gateway=tblinvoices.paymentmethod");
        $data = mysql_fetch_array($result);
        $type = $data['value'];
        if( $tplname )
        {
            check_token("WHMCS.admin.default");
            sendMessage($tplname, $id, '', true);
        }
        if( $type == 'CC' )
        {
            if( $sub == 'attemptpayment' )
            {
                check_token("WHMCS.admin.default");
                $data = get_query_vals('tblclients', 'cardtype,gatewayid', array( 'id' => $userid ));
                if( $data[0] || $data[1] )
                {
                    logActivity("Admin Initiated Payment Capture - Invoice ID: " . $id, $userid);
                    if( captureCCPayment($id) )
                    {
                        infoBox($aInt->lang('invoices', 'capturesuccessful'), $aInt->lang('invoices', 'capturesuccessfulmsg'), 'success');
                    }
                    else
                    {
                        infoBox($aInt->lang('invoices', 'captureerror'), $aInt->lang('invoices', 'captureerrormsg'), 'error');
                    }
                }
                else
                {
                    infoBox($aInt->lang('invoices', 'captureerror'), "No Credit Card Details are stored for this client so the capture could not be attempted", 'info');
                }
            }
            if( $sub == 'initiatepayment' )
            {
                check_token("WHMCS.admin.default");
                $data = get_query_vals('tblclients', 'gatewayid', array( 'id' => $userid ));
                logActivity("Admin Initiated Payment Attempt - Invoice ID: " . $id, $userid);
                if( captureCCPayment($id) )
                {
                    infoBox($aInt->lang('invoices', 'initiatepaymentsuccessful'), $aInt->lang('invoices', 'initiatepaymentsuccessfulmsg'), 'success');
                }
                else
                {
                    infoBox($aInt->lang('invoices', 'initiatepaymenterror'), $aInt->lang('invoices', 'initiatepaymenterrormsg'), 'error');
                }
            }
        }
        if( $sub == 'refund' && $transid )
        {
            check_token("WHMCS.admin.default");
            checkPermission("Refund Invoice Payments");
            logActivity("Admin Initiated Refund - Invoice ID: " . $id . " - Transaction ID: " . $transid);
            if( $refundtype == 'sendtogateway' )
            {
                $sendtogateway = true;
            }
            else
            {
                if( $refundtype == 'addascredit' )
                {
                    $addascredit = true;
                }
            }
            $result = refundInvoicePayment($transid, $amount, $sendtogateway, $addascredit, $sendemail, $refundtransid);
            redir("action=edit&id=" . $id . "&refundattempted=1&response=" . $result);
        }
        if( $sub == 'deletetrans' )
        {
            check_token("WHMCS.admin.default");
            checkPermission("Delete Transaction");
            delete_query('tblaccounts', array( 'id' => $ide ));
            logActivity("Deleted Transaction - Transaction ID: " . $ide);
            redir("action=edit&id=" . $id);
        }
        $jscode = "function showrefundtransid() {\n    var refundtype = \$(\"#refundtype\").val();\n    if (refundtype != \"\") {\n        \$(\"#refundtransid\").slideUp();\n    } else {\n        \$(\"#refundtransid\").slideDown();\n    }\n}";
        $aInt->jscode = $jscode;
        if( $refundattempted )
        {
            if( $response == 'manual' )
            {
                infoBox($aInt->lang('invoices', 'refundsuccess'), $aInt->lang('invoices', 'refundmanualsuccessmsg'));
            }
            else
            {
                if( $response == 'amounterror' )
                {
                    infoBox($aInt->lang('invoices', 'refundfailed'), $aInt->lang('invoices', 'refundamounterrormsg'));
                }
                else
                {
                    if( $response == 'success' )
                    {
                        infoBox($aInt->lang('invoices', 'refundsuccess'), $aInt->lang('invoices', 'refundsuccessmsg'));
                    }
                    else
                    {
                        if( $response == 'creditsuccess' )
                        {
                            infoBox($aInt->lang('invoices', 'refundsuccess'), $aInt->lang('invoices', 'refundcreditmsg'));
                        }
                        else
                        {
                            infoBox($aInt->lang('invoices', 'refundfailed'), $aInt->lang('invoices', 'refundfailedmsg'));
                        }
                    }
                }
            }
        }
        if( $creditapply == 'exceedbalance' )
        {
            infoBox($aInt->lang('global', 'erroroccurred'), $aInt->lang('invoices', 'exceedBalance'), 'error');
        }
        if( $creditapply == 'exceedtotal' )
        {
            infoBox($aInt->lang('global', 'erroroccurred'), $aInt->lang('invoices', 'exceedTotal'), 'error');
        }
        if( $creditapply == 'success' )
        {
            $clientCurrency = getCurrency($userid);
            infoBox($aInt->lang('global', 'success'), sprintf($aInt->lang('invoices', 'creditApplySuccess'), formatCurrency($amt, $clientCurrency['id'])), 'success');
        }
        if( $creditremove == 'exceedtotal' )
        {
            infoBox($aInt->lang('global', 'erroroccurred'), $aInt->lang('invoices', 'exceedTotalRemove'), 'error');
        }
        if( $creditremove == 'success' )
        {
            $clientCurrency = getCurrency($userid);
            infoBox($aInt->lang('global', 'success'), sprintf($aInt->lang('invoices', 'creditRemoveSuccess'), formatCurrency($amt, $clientCurrency['id'])), 'success');
        }
        echo $infobox;
        $gatewaysarray = getGatewaysArray();
        $result = select_query('tblinvoices', "tblinvoices.*,tblclients.firstname,tblclients.lastname,tblclients.companyname,tblclients.groupid,tblclients.state,tblclients.country", array( "tblinvoices.id" => $id ), '', '', '', "tblclients ON tblclients.id=tblinvoices.userid");
        $data = mysql_fetch_array($result);
        $id = $data['id'];
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
        $userid = $data['userid'];
        $firstname = $data['firstname'];
        $lastname = $data['lastname'];
        $companyname = $data['companyname'];
        $groupid = $data['groupid'];
        $clientstate = $data['state'];
        $clientcountry = $data['country'];
        $date = fromMySQLDate($date);
        $duedate = fromMySQLDate($duedate);
        $datepaid = fromMySQLDate($datepaid, 'time');
        if( !$id )
        {
            $aInt->gracefulExit("Invoice ID Not Found");
        }
        $currency = getCurrency($userid);
        $result = select_query('tblaccounts', "COUNT(id),SUM(amountin)-SUM(amountout)", array( 'invoiceid' => $id ));
        $data = mysql_fetch_array($result);
        $transcount = $data[0];
        $amountpaid = $data[1];
        $balance = $total - $amountpaid;
        $balance = $rawbalance = sprintf("%01.2f", $balance);
        loadGatewayModule($paymentmethod);
        if( $status == 'Unpaid' )
        {
            $paymentmethodfriendly = $gatewaysarray[$paymentmethod];
        }
        else
        {
            if( $transcount == 0 )
            {
                $paymentmethodfriendly = $aInt->lang('invoices', 'notransapplied');
            }
            else
            {
                $paymentmethodfriendly = $gatewaysarray[$paymentmethod];
            }
        }
        if( 0 < $credit )
        {
            if( $total == 0 )
            {
                $paymentmethodfriendly = $aInt->lang('invoices', 'fullypaidcredit');
            }
            else
            {
                $paymentmethodfriendly .= " + " . $aInt->lang('invoices', 'partialcredit');
            }
        }
        $initiatevscapture = function_exists($paymentmethod . '_initiatepayment') ? true : false;
        if( function_exists($paymentmethod . '_adminstatusmsg') )
        {
            $gatewaymsg = call_user_func($paymentmethod . '_adminstatusmsg', array( 'invoiceid' => $id, 'userid' => $userid, 'date' => $date, 'duedate' => $duedate, 'datepaid' => $datepaid, 'subtotal' => $subtotal, 'tax' => $tax, 'tax2' => $tax2, 'total' => $total, 'status' => $status ));
            if( is_array($gatewaymsg) )
            {
                infoBox($gatewaymsg['title'], $gatewaymsg['msg'], $gatewaymsg['type']);
                echo $infobox;
            }
        }
        $aInt->deleteJSConfirm('doDelete', 'invoices', 'deletelineitem', "?action=edit&id=" . $id . "&sub=delete&iid=");
        $aInt->deleteJSConfirm('doDeleteTransaction', 'invoices', 'deletetransaction', "?action=edit&id=" . $id . "&sub=deletetrans&ide=");
        run_hook('ViewInvoiceDetailsPage', array( 'invoiceid' => $id ));
        echo $aInt->Tabs(array( $aInt->lang('invoices', 'summary'), $aInt->lang('invoices', 'addpayment'), $aInt->lang('invoices', 'options'), $aInt->lang('fields', 'credit'), $aInt->lang('invoices', 'refund'), $aInt->lang('fields', 'notes') ));
        echo "\n<div id=\"tab0box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n<table width=100%><tr><td width=50%>\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"35%\" class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'clientname');
        echo "</td><td class=\"fieldarea\">";
        echo $aInt->outputClientLink($userid, $firstname, $lastname, $companyname, $groupid);
        echo " (<a href=\"clientsinvoices.php?userid=";
        echo $userid;
        echo "\">";
        echo $aInt->lang('invoices', 'viewinvoices');
        echo "</a>)</td></tr>\n";
        if( $invoicenum )
        {
            echo "<tr><td class=\"fieldlabel\">";
            echo $aInt->lang('fields', 'invoicenum');
            echo "</td><td class=\"fieldarea\">";
            echo $invoicenum;
            echo "</td></tr>";
        }
        echo "<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'invoicedate');
        echo "</td><td class=\"fieldarea\">";
        echo $date;
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'duedate');
        echo "</td><td class=\"fieldarea\">";
        echo $duedate;
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'totaldue');
        echo "</td><td class=\"fieldarea\">";
        echo formatCurrency($credit + $total);
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'balance');
        echo "</td><td class=\"fieldarea\"><b>";
        if( 0 < $rawbalance )
        {
            echo "<font color=#cc0000>" . formatCurrency($balance) . "</font>";
        }
        else
        {
            echo "<font color=#99cc00>" . formatCurrency($balance) . "</font>";
        }
        echo "</b></td></tr>\n</table>\n\n</td><td align=center width=50%>\n";
        if( $status == 'Unpaid' )
        {
            echo "<span class=\"textred\" style=\"font-family:Arial;font-size:20px;font-weight:bold;text-transform:uppercase\">";
            echo $aInt->lang('status', 'unpaid');
            echo "</span>";
        }
        else
        {
            if( $status == 'Paid' )
            {
                echo "<span class=\"textgreen\" style=\"font-family:Arial;font-size:20px;font-weight:bold;text-transform:uppercase\">";
                echo $aInt->lang('status', 'paid');
                echo "</span><br><b>";
                echo $datepaid;
                echo "</b>";
            }
            else
            {
                if( $status == 'Cancelled' )
                {
                    echo "<span class=\"textgrey\" style=\"font-family:Arial;font-size:20px;font-weight:bold;text-transform:uppercase\">";
                    echo $aInt->lang('status', 'cancelled');
                    echo "</span>";
                }
                else
                {
                    if( $status == 'Refunded' )
                    {
                        echo "<span class=\"textblue\" style=\"font-family:Arial;font-size:20px;font-weight:bold;text-transform:uppercase\">";
                        echo $aInt->lang('status', 'refunded');
                        echo "</span>";
                    }
                    else
                    {
                        if( $status == 'Collections' )
                        {
                            echo "<span class=\"textgold\" style=\"font-family:Arial;font-size:20px;font-weight:bold;text-transform:uppercase\">";
                            echo $aInt->lang('status', 'collections');
                            echo "</span>";
                        }
                    }
                }
            }
        }
        echo "<br>";
        echo $aInt->lang('fields', 'paymentmethod');
        echo ": <B>";
        echo $paymentmethodfriendly;
        echo "</B>\n<br /><img src=\"images/spacer.gif\" width=\"1\" height=\"10\" /><br />\n<form method=\"post\" action=\"invoices.php?action=edit&id=";
        echo $id;
        echo "\">\n<select name=\"tplname\">";
        $emailtplsarray = array(  );
        $result = select_query('tblemailtemplates', 'id,name', array( 'type' => 'invoice', 'language' => '' ), 'name', 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $emailtplsarray[$data['name']] = $data['id'];
        }
        $emailtplsoutput = array( "Invoice Created", "Credit Card Invoice Created", "Invoice Payment Reminder", "First Invoice Overdue Notice", "Second Invoice Overdue Notice", "Third Invoice Overdue Notice", "Credit Card Payment Due", "Credit Card Payment Failed", "Invoice Payment Confirmation", "Credit Card Payment Confirmation", "Invoice Refund Confirmation" );
        if( $status == 'Paid' )
        {
            $emailtplsoutput = array_merge(array( "Invoice Payment Confirmation", "Credit Card Payment Confirmation" ), $emailtplsoutput);
        }
        if( $status == 'Refunded' )
        {
            $emailtplsoutput = array_merge(array( "Invoice Refund Confirmation" ), $emailtplsoutput);
        }
        foreach( $emailtplsoutput as $tplname )
        {
            if( array_key_exists($tplname, $emailtplsarray) )
            {
                echo "<option>" . $tplname . "</option>";
                unset($emailtplsarray[$tplname]);
            }
        }
        foreach( $emailtplsarray as $tplname => $k )
        {
            echo "<option>" . $tplname . "</option>";
        }
        echo "</select> <input type=\"submit\" value=\"";
        echo $aInt->lang('global', 'sendemail');
        echo "\" />\n</form>\n<img src=\"images/spacer.gif\" width=\"1\" height=\"5\" /><br />\n<input type=\"button\" value=\"";
        echo $initiatevscapture ? $aInt->lang('invoices', 'initiatepayment') : $aInt->lang('invoices', 'attemptcapture');
        echo "\" onClick=\"attemptpayment()\" class=\"button\"";
        if( $status == 'Paid' || $status == 'Cancelled' || !function_exists($paymentmethod . '_capture') )
        {
            echo " disabled";
        }
        echo " /> <input type=\"button\" value=\"";
        echo $aInt->lang('invoices', 'markcancelled');
        echo "\" class=\"button\" onClick=\"window.location='";
        echo $whmcs->getPhpSelf();
        echo "?action=edit&id=";
        echo $id;
        echo "&sub=statuscancelled";
        echo generate_token('link');
        echo "';\"";
        if( $status == 'Cancelled' )
        {
            echo " disabled";
        }
        echo "> <input type=\"button\" value=\"";
        echo $aInt->lang('invoices', 'markunpaid');
        echo "\" onClick=\"window.location='";
        echo $whmcs->getPhpSelf();
        echo "?action=edit&id=";
        echo $id;
        echo "&sub=statusunpaid";
        echo generate_token('link');
        echo "';\" class=\"button\"";
        if( $status == 'Unpaid' )
        {
            echo " disabled";
        }
        echo "><br /><img src=\"images/spacer.gif\" width=\"1\" height=\"5\" /><br />\n<input type=\"button\" value=\"";
        echo $aInt->lang('invoices', 'printableversion');
        echo "\" class=\"button\" onclick=\"window.open('../viewinvoice.php?id=";
        echo $id;
        echo "','windowfrm','menubar=yes,toolbar=yes,scrollbars=yes,resizable=yes,width=750,height=600')\" /> <input type=\"button\" value=\"";
        echo $aInt->lang('invoices', 'viewpdf');
        echo "\" class=\"button\" onclick=\"window.open('../dl.php?type=i&id=";
        echo $id;
        echo "&viewpdf=1','pdfinv','')\" /> <input type=\"button\" value=\"";
        echo $aInt->lang('invoices', 'downloadpdf');
        echo "\" class=\"button\" onclick=\"window.location='../dl.php?type=i&id=";
        echo $id;
        echo "'\" />\n\n";
        $addons_html = run_hook('AdminInvoicesControlsOutput', array( 'invoiceid' => $id, 'userid' => $userid, 'subtotal' => $subtotal, 'tax' => $tax, 'tax2' => $tax2, 'credit' => $credit, 'total' => $total, 'balance' => $balance, 'taxrate' => $taxrate, 'taxrate2' => $taxrate2, 'paymentmethod' => $paymentmethod ));
        foreach( $addons_html as $output )
        {
            echo $output;
        }
        echo "\n</td></tr></table>\n\n  </div>\n</div>\n<div id=\"tab1box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n<form method=\"post\" action=\"";
        echo $whmcs->getPhpSelf();
        echo "\">\n<input type=\"hidden\" name=\"action\" value=\"edit\">\n<input type=\"hidden\" name=\"id\" value=\"";
        echo $id;
        echo "\" id=\"invoiceId\">\n<input type=\"hidden\" name=\"sub\" value=\"markpaid\">\n\n";
        if( $rawbalance <= 0 )
        {
            infoBox($aInt->lang('invoices', 'paidstatuscredit'), $aInt->lang('invoices', 'paidstatuscreditdesc'));
            echo $infobox;
        }
        echo "\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"20%\" class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'date');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"date\" value=\"";
        echo getTodaysDate();
        echo "\" class=\"datepick\"></td><td width=\"20%\" class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'amount');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"amount\" value=\"";
        echo $rawbalance;
        echo "\" size=\"10\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'paymentmethod');
        echo "</td><td class=\"fieldarea\">";
        echo paymentMethodsSelection('None');
        echo "</td><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'fees');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"fees\" value=\"0.00\" size=\"10\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'transid');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"transid\" size=\"25\"></td><td class=\"fieldlabel\">";
        echo $aInt->lang('global', 'sendemail');
        echo "</td><td class=\"fieldarea\"><input type=\"checkbox\" name=\"sendconfirmation\" checked> ";
        echo $aInt->lang('invoices', 'ticksendconfirmation');
        echo "</td></tr>\n</table>\n<img src=\"images/spacer.gif\" width=\"1\" height=\"10\"><br>\n<div align=\"center\"><input type=\"submit\" value=\"";
        echo $aInt->lang('invoices', 'addpayment');
        echo "\" class=\"btn\" /></div>\n</form>\n\n  </div>\n</div>\n<div id=\"tab2box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n<form method=\"post\" action=\"";
        echo $whmcs->getPhpSelf();
        echo "\">\n<input type=\"hidden\" name=\"action\" value=\"edit\">\n<input type=\"hidden\" name=\"saveoptions\" value=\"true\">\n<input type=\"hidden\" name=\"id\" value=\"";
        echo $id;
        echo "\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"20%\" class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'invoicedate');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"invoicedate\" value=\"";
        echo $date;
        echo "\" class=\"datepick\"></td><td width=\"20%\" class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'duedate');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"datedue\" value=\"";
        echo $duedate;
        echo "\" class=\"datepick\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'paymentmethod');
        echo "</td><td class=\"fieldarea\">";
        echo paymentMethodsSelection();
        echo "</td><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'taxrate');
        echo "</td><td class=\"fieldarea\">1 <input type=\"text\" name=\"taxrate\" value=\"";
        echo $taxrate;
        echo "\" size=\"6\">% &nbsp;-&nbsp; 2 <input type=\"text\" name=\"taxrate2\" value=\"";
        echo $taxrate2;
        echo "\" size=\"6\">%</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'invoicenum');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"invoicenum\" value=\"";
        echo $invoicenum;
        echo "\" size=\"12\"></td><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'status');
        echo "</td><td class=\"fieldarea\"><select name=\"status\">\n<option value=\"Unpaid\"";
        if( $status == 'Unpaid' )
        {
            echo " selected";
        }
        echo ">";
        echo $aInt->lang('status', 'unpaid');
        echo "</option>\n<option value=\"Paid\"";
        if( $status == 'Paid' )
        {
            echo " selected";
        }
        echo ">";
        echo $aInt->lang('status', 'paid');
        echo "</option>\n<option value=\"Cancelled\"";
        if( $status == 'Cancelled' )
        {
            echo " selected";
        }
        echo ">";
        echo $aInt->lang('status', 'cancelled');
        echo "</option>\n<option value=\"Refunded\"";
        if( $status == 'Refunded' )
        {
            echo " selected";
        }
        echo ">";
        echo $aInt->lang('status', 'refunded');
        echo "</option>\n<option value=\"Collections\"";
        if( $status == 'Collections' )
        {
            echo " selected";
        }
        echo ">";
        echo $aInt->lang('status', 'collections');
        echo "</option>\n</select></td></tr>\n</table>\n<img src=\"images/spacer.gif\" width=\"1\" height=\"10\"><br>\n<div align=\"center\"><input type=\"submit\" value=\"";
        echo $aInt->lang('global', 'savechanges');
        echo "\" class=\"button\"></div>\n</form>\n\n  </div>\n</div>\n<div id=\"tab3box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n";
        $totalcredit = get_query_val('tblclients', 'credit', array( 'id' => $userid ));
        echo "<table width=75% align=\"center\">\n<tr><td width=50% align=\"center\"><b>";
        echo $aInt->lang('invoices', 'addcredit');
        echo "</b></td><td align=center><b>";
        echo $aInt->lang('invoices', 'removecredit');
        echo "</b></td></tr>\n<tr><td align=center><font color=#377D0D>";
        echo formatCurrency($totalcredit);
        echo " ";
        echo $aInt->lang('invoices', 'creditavailable');
        echo "</font></td><td align=center><font color=#cc0000>";
        echo formatCurrency($credit);
        echo " ";
        echo $aInt->lang('invoices', 'creditavailable');
        echo "</font></td></tr>\n<tr><td align=center><form method=\"post\" action=\"";
        echo $whmcs->getPhpSelf();
        echo "\"><input type=\"hidden\" name=\"action\" value=\"edit\"><input type=\"hidden\" name=\"id\" value=\"";
        echo $id;
        echo "\"><input type=\"text\" name=\"addcredit\" value=\"";
        echo $balance <= $totalcredit ? $balance : $totalcredit;
        echo "\" size=\"8\"";
        if( $totalcredit == "0.00" )
        {
            echo " disabled";
        }
        echo "> <input type=\"submit\" value=\"";
        echo $aInt->lang('global', 'go');
        echo "\" class=\"btn";
        if( $totalcredit == "0.00" )
        {
            echo " disabled";
        }
        echo "\"";
        if( $totalcredit == "0.00" )
        {
            echo " disabled";
        }
        echo "></form></td><td align=center><form method=\"post\" action=\"";
        echo $whmcs->getPhpSelf();
        echo "\"><input type=\"hidden\" name=\"action\" value=\"edit\"><input type=\"hidden\" name=\"id\" value=\"";
        echo $id;
        echo "\"><input type=\"text\" name=\"removecredit\" value=\"0.00\" size=\"8\"";
        if( $credit == "0.00" )
        {
            echo " disabled";
        }
        echo "> <input type=\"submit\" value=\"";
        echo $aInt->lang('global', 'go');
        echo "\" class=\"btn";
        if( $credit == "0.00" )
        {
            echo " disabled";
        }
        echo "\"";
        if( $credit == "0.00" )
        {
            echo " disabled";
        }
        echo "></form></td></tr>\n</table>\n</form>\n\n  </div>\n</div>\n<div id=\"tab4box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n";
        $numtrans = get_query_vals('tblaccounts', "COUNT(id)", array( 'invoiceid' => $id, 'amountin' => array( 'sqltype' => ">", 'value' => '0' ) ), "date` ASC,`id", 'ASC');
        $notransactions = $numtrans[0] == '0' ? true : false;
        echo "<form method=\"post\" action=\"";
        echo $whmcs->getPhpSelf();
        echo "\">\n<input type=\"hidden\" name=\"action\" value=\"edit\">\n<input type=\"hidden\" name=\"id\" value=\"";
        echo $id;
        echo "\">\n<input type=\"hidden\" name=\"sub\" value=\"refund\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"20%\" class=\"fieldlabel\">";
        echo $aInt->lang('invoices', 'transactions');
        echo "</td><td class=\"fieldarea\"><select name=\"transid\">";
        $result = select_query('tblaccounts', '', array( 'invoiceid' => $id, 'amountin' => array( 'sqltype' => ">", 'value' => '0' ) ), "date` ASC,`id", 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $trans_id = $data['id'];
            $trans_date = $data['date'];
            $trans_amountin = $data['amountin'];
            $trans_transid = $data['transid'];
            $trans_date = fromMySQLDate($trans_date);
            $trans_amountin = formatCurrency($trans_amountin);
            echo "<option value=\"" . $trans_id . "\">" . $trans_date . " | " . $trans_transid . " | " . $trans_amountin . "</option>";
        }
        if( $notransactions )
        {
            echo "<option value=\"\">" . $aInt->lang('invoices', 'notransactions') . "</option>";
        }
        echo "</select></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'amount');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"amount\" size=\"15\" /> Leave blank for full refund</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('invoices', 'refundtype');
        echo "</td><td class=\"fieldarea\"><select name=\"refundtype\" id=\"refundtype\" onchange=\"showrefundtransid();return false\"><option value=\"sendtogateway\">";
        echo $aInt->lang('invoices', 'refundtypegateway');
        echo "</option><option value=\"\" type=\"\">";
        echo $aInt->lang('invoices', 'refundtypemanual');
        echo "</option><option value=\"addascredit\">";
        echo $aInt->lang('invoices', 'refundtypecredit');
        echo "</option></select></td></tr>\n<tr id=\"refundtransid\" style=\"display:none;\" ><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'transid');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"refundtransid\" size=\"25\" /></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('global', 'sendemail');
        echo "</td><td class=\"fieldarea\"><input type=\"checkbox\" name=\"sendemail\" checked> ";
        echo $aInt->lang('invoices', 'ticksendconfirmation');
        echo "</td></tr>\n</table>\n<img src=\"images/spacer.gif\" width=\"1\" height=\"10\"><br>\n<div align=\"center\"><input type=\"submit\" value=\"";
        echo $aInt->lang('invoices', 'refund');
        echo "\" class=\"btn\"";
        if( $notransactions )
        {
            echo " disabled";
        }
        echo "></div>\n</form>\n\n  </div>\n</div>\n<div id=\"tab5box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n<form method=\"post\" action=\"";
        echo $whmcs->getPhpSelf();
        echo "?save=notes\">\n<input type=\"hidden\" name=\"action\" value=\"edit\">\n<input type=\"hidden\" name=\"id\" value=\"";
        echo $id;
        echo "\">\n<textarea rows=4 style=\"width:100%\" name=\"notes\">";
        echo $notes;
        echo "</textarea><br>\n<img src=\"images/spacer.gif\" width=\"1\" height=\"5\"><br>\n<div align=\"center\"><input type=\"submit\" value=\"";
        echo $aInt->lang('global', 'savechanges');
        echo "\" class=\"button\"></div>\n</form>\n\n  </div>\n</div>\n\n<script language=\"JavaScript\">\nfunction attemptpayment() {\nif (confirm(\"Are you sure you want to attempt payment for this invoice?\")) {\nwindow.location='";
        echo $whmcs->getPhpSelf();
        echo "?action=edit&id=";
        echo $id . generate_token('link');
        echo "&sub=";
        echo $initiatevscapture ? 'initiate' : 'attempt';
        echo "payment';\n}}\n</script>\n\n<h2>";
        echo $aInt->lang('invoices', 'items');
        echo "</h2>\n<form method=\"post\" action=\"";
        echo $whmcs->getPhpSelf();
        echo "\">\n<input type=\"hidden\" name=\"action\" value=\"edit\">\n<input type=\"hidden\" name=\"id\" value=\"";
        echo $id;
        echo "\">\n<input type=\"hidden\" name=\"userid\" value=\"";
        echo $userid;
        echo "\">\n<input type=\"hidden\" name=\"sub\" value=\"save\">\n\n<div class=\"tablebg\">\n<table class=\"datatable\" width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"3\">\n<tr><th width=\"20\"></th><th>";
        echo $aInt->lang('fields', 'description');
        echo "</th><th width=\"90\">";
        echo $aInt->lang('fields', 'amount');
        echo "</th><th width=\"50\">";
        echo $aInt->lang('fields', 'taxed');
        echo "</th><th width=\"20\"></th></tr>\n";
        $result = select_query('tblinvoiceitems', '', array( 'invoiceid' => $id ), 'id', 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $lineid = $data['id'];
            $description = $data['description'];
            $linecount = explode("\n", $description);
            $linecount = count($linecount);
            echo "<tr><td width=\"20\" align=\"center\"><input type=\"checkbox\" name=\"itemids[]\" value=\"" . $lineid . "\" /></td><td><textarea name=\"description[" . $lineid . "]" . "\" style=\"width:98%\" rows=\"" . $linecount . "\">" . $description . "</textarea></td><td align=center nowrap><input type=\"text\" name=\"amount[" . $lineid . "]" . "\" value=\"" . $data['amount'] . "\" size=\"10\" style=\"text-align:center\"></td><td align=center><input type=\"checkbox\" name=\"taxed[" . $lineid . "]" . "\" value=\"1\"";
            if( $data['taxed'] == '1' )
            {
                echo " checked";
            }
            echo "><td width=\"20\" align=\"center\"><a href=\"#\" onClick=\"doDelete('" . $lineid . "');return false\"><img src=\"images/delete.gif\" border=\"0\"></a></td></tr>";
        }
        echo "<tr><td width=\"20\"></td><td><textarea name=\"adddescription\" style=\"width:98%\" rows=\"1\"></textarea></td><td align=center><input type=\"text\" name=\"addamount\" size=\"10\" style=\"text-align:center\"></td><td align=center><input type=\"checkbox\" name=\"addtaxed\" value=\"1\"" . ($CONFIG['TaxEnabled'] && $CONFIG['TaxCustomInvoices'] ? " checked" : '') . "></td><td>&nbsp;</td></tr>";
        echo "<tr><td colspan=\"2\" style=\"text-align:right;background-color:#efefef;\"><div align=\"left\" style=\"width:60%;float:left;\"><select name=\"selaction\" onchange=\"this.form.submit()\"><option>- ";
        echo $aInt->lang('global', 'withselected');
        echo " -</option><option value=\"split\">";
        echo $aInt->lang('invoices', 'split');
        echo "</option><option value=\"delete\">";
        echo $aInt->lang('global', 'delete');
        echo "</option></select></div><div style=\"width:25%;float:right;line-height:22px;\"><strong>";
        echo $aInt->lang('fields', 'subtotal');
        echo ":</strong>&nbsp;</div></td><td width=\"90\" style=\"background-color:#efefef;\"><strong>";
        echo formatCurrency($subtotal);
        echo "</strong></td><td style=\"background-color:#efefef;\">&nbsp;</td><td style=\"background-color:#efefef;\">&nbsp;</td></tr>\n";
        if( $CONFIG['TaxEnabled'] == 'on' )
        {
            if( $taxrate != "0.00" )
            {
                echo "<tr><td colspan=\"2\" style=\"text-align:right;background-color:#efefef;\">";
                echo $taxrate;
                echo "% ";
                $taxdata = getTaxRate(1, $clientstate, $clientcountry);
                echo $taxdata['name'] ? $taxdata['name'] : $aInt->lang('invoices', 'taxdue');
                echo ":&nbsp;</td><td width=\"90\" style=\"background-color:#efefef;\">";
                echo formatCurrency($tax);
                echo "</td><td style=\"background-color:#efefef;\">&nbsp;</td><td style=\"background-color:#efefef;\">&nbsp;</td></tr>";
            }
            if( $taxrate2 != "0.00" )
            {
                echo "<tr><td colspan=\"2\" style=\"text-align:right;background-color:#efefef;\">";
                echo $taxrate2;
                echo "% ";
                $taxdata = getTaxRate(2, $clientstate, $clientcountry);
                echo $taxdata['name'] ? $taxdata['name'] : $aInt->lang('invoices', 'taxdue');
                echo ":&nbsp;</td><td width=\"90\" style=\"background-color:#efefef;\">";
                echo formatCurrency($tax2);
                echo "</td><td style=\"background-color:#efefef;\">&nbsp;</td><td style=\"background-color:#efefef;\">&nbsp;</td></tr>";
            }
        }
        echo "<tr><td colspan=\"2\" style=\"text-align:right;background-color:#efefef;\">";
        echo $aInt->lang('fields', 'credit');
        echo ":&nbsp;</td><td width=\"90\" style=\"background-color:#efefef;\">";
        echo formatCurrency($credit);
        echo "</td><td style=\"background-color:#efefef;\">&nbsp;</td><td style=\"background-color:#efefef;\">&nbsp;</td></tr>\n<tr><th colspan=\"2\" style=\"text-align:right;\">";
        echo $aInt->lang('fields', 'totaldue');
        echo ":&nbsp;</th><th width=\"90\">";
        echo formatCurrency($total);
        echo "</th><th></th><th></th></tr>\n</table>\n</div>\n<p align=center><input type=\"submit\" value=\"";
        echo $aInt->lang('global', 'savechanges');
        echo "\" class=\"button\" /> <input type=\"reset\" value=\"";
        echo $aInt->lang('global', 'cancelchanges');
        echo "\" class=\"button\" /></p>\n</form>\n\n<h2>";
        echo $aInt->lang('invoices', 'transactions');
        echo "</h2>\n\n";
        $aInt->sortableTableInit('nopagination');
        $result = select_query('tblaccounts', '', array( 'invoiceid' => $id ), "date` ASC,`id", 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $ide = $data['id'];
            $date = $data['date'];
            $date = fromMySQLDate($date);
            $gateway = $data['gateway'];
            $description = $data['description'];
            $amountin = $data['amountin'];
            $fees = $data['fees'];
            $amountout = $data['amountout'];
            $transid = $data['transid'];
            $invoiceid = $data['invoiceid'];
            $amount = formatCurrency($amountin - $amountout);
            $fees = formatCurrency($fees);
            if( $gateway )
            {
                $result2 = select_query('tblpaymentgateways', '', array( 'gateway' => $gateway, 'setting' => 'name' ));
                $data = mysql_fetch_array($result2);
                $gateway = $data['value'];
            }
            else
            {
                $gateway = "No Gateway";
            }
            $tabledata[] = array( $date, $gateway, $transid, $amount, $fees, "<a href=\"#\" onClick=\"doDeleteTransaction('" . $ide . "');return false\"><img src=\"images/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"Delete\"></a>" );
        }
        echo $aInt->sortableTable(array( $aInt->lang('fields', 'date'), $aInt->lang('fields', 'paymentmethod'), $aInt->lang('fields', 'transid'), $aInt->lang('fields', 'amount'), $aInt->lang('fields', 'fees'), '' ), $tabledata);
    }
}
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->display();