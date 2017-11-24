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
if( $action == 'add' )
{
    $reqperm = "Add Transaction";
}
else
{
    if( $action == 'edit' )
    {
        $reqperm = "Edit Transaction";
    }
    else
    {
        $reqperm = "List Transactions";
    }
}
$aInt = new WHMCS_Admin($reqperm);
$aInt->inClientsProfile = true;
$aInt->requiredFiles(array( 'gatewayfunctions', 'invoicefunctions' ));
$aInt->valUserID($userid);
$aInt->assertClientBoundary($userid);
if( $sub == 'add' )
{
    check_token("WHMCS.admin.default");
    if( $transid && !isUniqueTransactionID($transid, $paymentmethod) )
    {
        WHMCS_Cookie::set('DuplicateTransaction', array( 'invoiceid' => $invoiceid, 'transid' => $transid, 'amountin' => $amountin, 'fees' => $fees, 'paymentmethod' => $paymentmethod, 'date' => $date, 'amountout' => $amountout, 'description' => $description, 'addcredit' => $addcredit ));
        redir(array( 'userid' => $userid, 'error' => 'duplicate', 'action' => 'add' ));
    }
    if( $invoiceid )
    {
        $transuserid = get_query_val('tblinvoices', 'userid', array( 'id' => $invoiceid ));
        if( !$transuserid )
        {
            redir("error=invalidinvid");
        }
        else
        {
            if( $transuserid != $userid )
            {
                redir("error=wronguser");
            }
        }
        addInvoicePayment($invoiceid, $transid, $amountin, $fees, $paymentmethod, '', $date);
    }
    else
    {
        addTransaction($userid, 0, $description, $amountin, $fees, $amountout, $paymentmethod, $transid, $invoiceid, $date);
    }
    if( $addcredit )
    {
        if( $transid )
        {
            $description .= " (Trans ID: " . $transid . ")";
        }
        insert_query('tblcredit', array( 'clientid' => $userid, 'date' => toMySQLDate($date), 'description' => $description, 'amount' => $amountin ));
        update_query('tblclients', array( 'credit' => "+=" . $amountin ), array( 'id' => (int) $userid ));
    }
    redir("userid=" . $userid);
}
if( $sub == 'save' )
{
    check_token("WHMCS.admin.default");
    update_query('tblaccounts', array( 'gateway' => $paymentmethod, 'date' => toMySQLDate($date), 'description' => $description, 'amountin' => $amountin, 'fees' => $fees, 'amountout' => $amountout, 'transid' => $transid, 'invoiceid' => $invoiceid ), array( 'id' => $id ));
    logActivity("Modified Transaction (User ID: " . $userid . " - Transaction ID: " . $id . ")");
    redir("userid=" . $userid);
}
if( $sub == 'delete' )
{
    check_token("WHMCS.admin.default");
    checkPermission("Delete Transaction");
    delete_query('tblaccounts', array( 'id' => $ide ));
    logActivity("Deleted Transaction (ID: " . $ide . " - User ID: " . $userid . ")");
    redir("userid=" . $userid);
}
ob_start();
if( $action == '' )
{
    $aInt->deleteJSConfirm('doDelete', 'transactions', 'deletesure', "clientstransactions.php?userid=" . $userid . "&sub=delete&ide=");
    $currency = getCurrency($userid);
    if( $error == 'invalidinvid' )
    {
        infoBox($aInt->lang('invoices', 'checkInvoiceID'), $aInt->lang('invoices', 'invalidInvoiceID'), 'error');
    }
    else
    {
        if( $error == 'wronguser' )
        {
            infoBox($aInt->lang('invoices', 'checkInvoiceID'), $aInt->lang('invoices', 'wrongUser'), 'error');
        }
    }
    echo $infobox;
    $result = select_query('tblaccounts', "SUM(amountin),SUM(fees),SUM(amountout),SUM(amountin-fees-amountout)", array( 'userid' => $userid ));
    $data = mysql_fetch_array($result);
    echo "\n<table width=90% cellspacing=1 cellpadding=5 bgcolor=\"#CCCCCC\" align=\"center\"><tr bgcolor=\"#f4f4f4\" style=\"text-align:center\"><td><a href=\"";
    echo $whmcs->getPhpSelf();
    echo "?userid=";
    echo $userid;
    echo "&action=add\">";
    echo $aInt->lang('transactions', 'addnew');
    echo "</a></td><td>";
    echo $aInt->lang('transactions', 'totalin');
    echo ": ";
    echo formatCurrency($data[0]);
    echo "</td><td>";
    echo $aInt->lang('transactions', 'totalfees');
    echo ": ";
    echo formatCurrency($data[1]);
    echo "</td><td>";
    echo $aInt->lang('transactions', 'totalout');
    echo ": ";
    echo formatCurrency($data[2]);
    echo "</td><td><B>";
    echo $aInt->lang('fields', 'balance');
    echo ": ";
    echo formatCurrency($data[3]);
    echo "</B><br></td></tr></table>\n\n<br>\n\n";
    $aInt->sortableTableInit('date', 'DESC');
    $result = select_query('tblaccounts', "COUNT(*)", array( 'userid' => $userid ));
    $data = mysql_fetch_array($result);
    $numrows = $data[0];
    $result = select_query('tblaccounts', '', array( 'userid' => $userid ), $orderby, $order, $page * $limit . ',' . $limit);
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
        $totalin = $totalin + $amountin;
        $totalout = $totalout + $amountout;
        $totalfees = $totalfees + $fees;
        $amountin = formatCurrency($amountin);
        $fees = formatCurrency($fees);
        $amountout = formatCurrency($amountout);
        if( $invoiceid != '0' )
        {
            $description .= " (<a href=\"invoices.php?action=edit&id=" . $invoiceid . "\">#" . $invoiceid . "</a>)";
        }
        if( $transid != '' )
        {
            $description .= " - Trans ID: " . $transid;
        }
        $result2 = select_query('tblpaymentgateways', '', array( 'gateway' => $gateway, 'setting' => 'name' ));
        $data = mysql_fetch_array($result2);
        $gateway = $data['value'];
        $tabledata[] = array( $date, $gateway, $description, $amountin, $fees, $amountout, "<a href=\"?userid=" . $userid . "&action=edit&id=" . $ide . "\"><img src=\"images/edit.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"Edit\"></a>", "<a href=\"#\" onClick=\"doDelete('" . $ide . "');return false\"><img src=\"images/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"Delete\"></a>" );
    }
    echo $aInt->sortableTable(array( array( 'date', $aInt->lang('fields', 'date') ), array( 'gateway', $aInt->lang('fields', 'paymentmethod') ), array( 'description', $aInt->lang('fields', 'description') ), array( 'amountin', $aInt->lang('transactions', 'amountin') ), array( 'fees', $aInt->lang('transactions', 'fees') ), array( 'amountout', $aInt->lang('transactions', 'amountout') ), '', '' ), $tabledata);
}
else
{
    if( $action == 'add' )
    {
        $date2 = getTodaysDate();
        if( $error == 'duplicate' )
        {
            infobox($aInt->lang('transactions', 'duplicate'), $aInt->lang('transactions', 'requireUniqueTransaction'), 'error');
            $repopulateData = WHMCS_Cookie::get('DuplicateTransaction', true);
            $invoiceid = $repopulateData['invoiceid'] ? WHMCS_Input_Sanitize::makesafeforoutput($repopulateData['invoiceid']) : '';
            $transid = WHMCS_Input_Sanitize::makesafeforoutput($repopulateData['transid']);
            $amountin = $repopulateData['amountin'] ? WHMCS_Input_Sanitize::makesafeforoutput($repopulateData['amountin']) : "0.00";
            $fees = $repopulateData['fees'] ? WHMCS_Input_Sanitize::makesafeforoutput($repopulateData['fees']) : "0.00";
            $paymentmethod = WHMCS_Input_Sanitize::makesafeforoutput($repopulateData['paymentmethod']);
            $date2 = WHMCS_Input_Sanitize::makesafeforoutput($repopulateData['date']);
            $amountout = $repopulateData['amountout'] ? WHMCS_Input_Sanitize::makesafeforoutput($repopulateData['amountout']) : "0.00";
            $description = WHMCS_Input_Sanitize::makesafeforoutput($repopulateData['description']);
            $addcredit = $repopulateData['addcredit'] ? " CHECKED" : '';
            WHMCS_Cookie::delete('DuplicateTransaction');
        }
        echo $infobox;
        echo "\n<p><b>";
        echo $aInt->lang('transactions', 'addnew');
        echo "</b></p>\n\n<form method=\"post\" action=\"";
        echo $whmcs->getPhpSelf();
        echo "?userid=";
        echo $userid;
        echo "&sub=add\" name=\"calendarfrm\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr>\n    <td width=\"15%\" class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'date');
        echo "</td>\n    <td class=\"fieldarea\"><input type=\"text\" name=\"date\" value=\"";
        echo $date2;
        echo "\" class=\"datepick\"></td>\n    <td class=\"fieldlabel\" width=\"15%\">";
        echo $aInt->lang('transactions', 'amountin');
        echo "</td>\n    <td class=\"fieldarea\"><input type=\"text\" name=\"amountin\" size=10 value=\"";
        echo $amountin;
        echo "\"></td>\n</tr>\n<tr>\n    <td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'description');
        echo "</td>\n    <td class=\"fieldarea\"><input type=\"text\" name=\"description\" size=50></td>\n    <td class=\"fieldlabel\">";
        echo $aInt->lang('transactions', 'fees');
        echo "</td>\n    <td class=\"fieldarea\"><input type=\"text\" name=\"fees\" size=10 value=\"";
        echo $fees;
        echo "\"></td>\n</tr>\n<tr>\n    <td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'transid');
        echo "</td>\n    <td class=\"fieldarea\"><input type=\"text\" name=\"transid\" size=30 value=\"";
        echo $transid;
        echo "\"></td>\n    <td class=\"fieldlabel\">";
        echo $aInt->lang('transactions', 'amountout');
        echo "</td>\n    <td class=\"fieldarea\"><input type=\"text\" name=\"amountout\" size=10 value=\"";
        echo $amountout;
        echo "\"></td>\n</tr>\n<tr>\n    <td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'invoiceid');
        echo "</td>\n    <td class=\"fieldarea\"><input type=\"text\" name=\"invoiceid\" size=10 value=\"";
        echo $invoiceid;
        echo "\"></td>\n    <td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'credit');
        echo "</td>\n    <td class=\"fieldarea\">\n        <input type=\"checkbox\" name=\"addcredit\"";
        echo $addcredit;
        echo ">\n        ";
        echo $aInt->lang('invoices', 'refundtypecredit');
        echo "    </td>\n</tr>\n<tr>\n    <td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'paymentmethod');
        echo "</td>\n    <td class=\"fieldarea\">";
        echo paymentMethodsSelection($aInt->lang('global', 'none'));
        echo "</td>\n    <td class=\"fieldlabel\"></td><td class=\"fieldarea\"></td></tr>\n</table>\n\n<p align=\"center\"><input type=\"submit\" value=\"";
        echo $aInt->lang('transactions', 'add');
        echo "\" class=\"button\"></p>\n\n</form>\n\n";
    }
    else
    {
        if( $action == 'edit' )
        {
            $result = select_query('tblaccounts', '', array( 'id' => $id ));
            $data = mysql_fetch_array($result);
            $id = $data['id'];
            $date = $data['date'];
            $date = fromMySQLDate($date);
            $description = $data['description'];
            $amountin = $data['amountin'];
            $fees = $data['fees'];
            $amountout = $data['amountout'];
            $paymentmethod = $data['gateway'];
            $transid = $data['transid'];
            $invoiceid = $data['invoiceid'];
            echo "\n<p><b>";
            echo $aInt->lang('transactions', 'edit');
            echo "</b></p>\n\n<form method=\"post\" action=\"";
            echo $whmcs->getPhpSelf();
            echo "?userid=";
            echo $userid;
            echo "&sub=save&id=";
            echo $id;
            echo "\" name=\"calendarfrm\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">";
            echo $aInt->lang('fields', 'date');
            echo "</td><td class=\"fieldarea\"><input type=\"text\" size=\"12\" name=\"date\" value=\"";
            echo $date;
            echo "\" class=\"datepick\"></td><td width=\"15%\" class=\"fieldlabel\" width=110>";
            echo $aInt->lang('fields', 'transid');
            echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"transid\" size=20 value=\"";
            echo $transid;
            echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
            echo $aInt->lang('fields', 'paymentmethod');
            echo "</td><td class=\"fieldarea\">";
            echo paymentMethodsSelection($aInt->lang('global', 'none'));
            echo "</td><td class=\"fieldlabel\">";
            echo $aInt->lang('transactions', 'amountin');
            echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"amountin\" size=10 value=\"";
            echo $amountin;
            echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
            echo $aInt->lang('fields', 'description');
            echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"description\" size=50 value=\"";
            echo $description;
            echo "\"></td><td class=\"fieldlabel\">";
            echo $aInt->lang('transactions', 'fees');
            echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"fees\" size=10 value=\"";
            echo $fees;
            echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
            echo $aInt->lang('fields', 'invoiceid');
            echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"invoiceid\" size=8 value=\"";
            echo $invoiceid;
            echo "\"></td><td class=\"fieldlabel\">";
            echo $aInt->lang('transactions', 'amountout');
            echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"amountout\" size=10 value=\"";
            echo $amountout;
            echo "\"></td></tr>\n</table>\n\n<p align=\"center\"><input type=\"submit\" value=\"";
            echo $aInt->lang('global', 'savechanges');
            echo "\" class=\"button\"></p>\n\n</form>\n\n";
        }
    }
}
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jquerycode = $jquerycode;
$aInt->jscode = $jscode;
$aInt->display();