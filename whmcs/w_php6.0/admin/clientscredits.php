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
$aInt = new WHMCS_Admin("View Credit Log");
$aInt->title = $aInt->lang('credit', 'creditmanagement');
ob_start();
$currency = getCurrency($userid);
$result = select_query('tblclients', 'firstname,lastname,credit', array( 'id' => $userid ));
$data = mysql_fetch_array($result);
$name = stripslashes($data['firstname'] . " " . $data['lastname']);
$creditbalance = $data['credit'];
if( $action == '' )
{
    if( $sub == 'add' )
    {
        check_token("WHMCS.admin.default");
        checkPermission("Manage Credits");
        insert_query('tblcredit', array( 'clientid' => $userid, 'date' => toMySQLDate($date), 'description' => $description, 'amount' => $amount ));
        update_query('tblclients', array( 'credit' => "+=" . $amount ), array( 'id' => (int) $userid ));
        logActivity("Added Credit - User ID: " . $userid . " - Amount: " . formatCurrency($amount), $userid);
        redir("userid=" . $userid);
    }
    if( $sub == 'remove' )
    {
        check_token("WHMCS.admin.default");
        checkPermission("Manage Credits");
        insert_query('tblcredit', array( 'clientid' => $userid, 'date' => toMySQLDate($date), 'description' => $description, 'amount' => 0 - $amount ));
        update_query('tblclients', array( 'credit' => "-=" . $amount ), array( 'id' => (int) $userid ));
        logActivity("Removed Credit - User ID: " . $userid . " - Amount: " . formatCurrency($amount), $userid);
        redir("userid=" . $userid);
    }
    if( $sub == 'save' )
    {
        check_token("WHMCS.admin.default");
        checkPermission("Manage Credits");
        update_query('tblcredit', array( 'date' => toMySQLDate($date), 'description' => $description, 'amount' => $amount ), array( 'id' => $id ));
        logActivity("Edited Credit - Credit ID: " . $id . " - User ID: " . $userid, $userid);
        redir("userid=" . $userid);
    }
    if( $sub == 'delete' )
    {
        check_token("WHMCS.admin.default");
        checkPermission("Manage Credits");
        $result = select_query('tblcredit', '', array( 'id' => $ide ));
        $data = mysql_fetch_array($result);
        $amount = $data['amount'];
        $creditbalance = $creditbalance - $amount;
        if( $creditbalance < 0 )
        {
            $creditbalance = 0;
        }
        update_query('tblclients', array( 'credit' => $creditbalance ), array( 'id' => (int) $userid ));
        delete_query('tblcredit', array( 'id' => $ide ));
        logActivity("Deleted Credit - Credit ID: " . $ide . " - User ID: " . $userid, $userid);
        redir("userid=" . $userid);
    }
    $creditbalance = formatCurrency($creditbalance);
    echo "\n<p>";
    echo $aInt->lang('credit', 'info');
    echo "</p>\n<div style=\"float:right\"><input type=\"button\" class=\"button btn-success\" value=\"";
    echo $aInt->lang('credit', 'addcredit');
    echo "\" onClick=\"window.location='";
    echo $whmcs->getPhpSelf();
    echo "?userid=";
    echo $userid;
    echo "&action=add'\"> <input type=\"button\" value=\"";
    echo $aInt->lang('credit', 'removecredit');
    echo "\" onClick=\"window.location='";
    echo $whmcs->getPhpSelf();
    echo "?userid=";
    echo $userid;
    echo "&action=remove'\"  class=\"button btn-inverse\"></div>\n<p>";
    echo $aInt->lang('fields', 'client');
    echo ": <B>";
    echo $name;
    echo "</B> (";
    echo $aInt->lang('fields', 'balance');
    echo ": ";
    echo $creditbalance;
    echo ")</p>\n<br />\n\n<script language=\"JavaScript\">\nfunction doDelete(id) {\nif (confirm(\"";
    echo addslashes($aInt->lang('credit', 'deleteq'));
    echo "\")) {\nwindow.location='";
    echo $whmcs->getPhpSelf();
    echo "?userid=";
    echo $userid;
    echo "&sub=delete&ide='+id+'";
    echo generate_token('link');
    echo "';\n}}\n</script>\n\n";
    $aInt->sortableTableInit('nopagination');
    $patterns = $replacements = array(  );
    $patterns[] = "/ Invoice #(.*?) /";
    $replacements[] = " <a href=\"invoices.php?action=edit&id=\$1\" target=\"_blank\">Invoice #\$1</a>";
    $result = select_query('tblcredit', '', array( 'clientid' => $userid ), 'date', 'DESC');
    while( $data = mysql_fetch_array($result) )
    {
        $id = $data['id'];
        $date = $data['date'];
        $date = fromMySQLDate($date);
        $description = $data['description'];
        $amount = $data['amount'];
        $description = preg_replace($patterns, $replacements, $description . " ");
        $tabledata[] = array( $date, nl2br(trim($description)), formatCurrency($amount), "<a href=\"?userid=" . $userid . "&action=edit&id=" . $id . "\"><img src=\"images/edit.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'edit') . "\"></a>", "<a href=\"#\" onClick=\"doDelete('" . $id . "');return false\"><img src=\"images/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'delete') . "\"></a>" );
    }
    echo $aInt->sortableTable(array( $aInt->lang('fields', 'date'), $aInt->lang('fields', 'description'), $aInt->lang('fields', 'amount'), '', '' ), $tabledata);
    echo "\n<p align=\"center\"><input type=\"button\" value=\"";
    echo $aInt->lang('addons', 'closewindow');
    echo "\" onClick=\"window.close()\" class=\"button\" /></p>\n\n";
}
else
{
    if( $action == 'add' || $action == 'remove' )
    {
        checkPermission("Manage Credits");
        $date = getTodaysDate();
        $amount = "0.00";
        if( $action == 'add' )
        {
            $title = $aInt->lang('credit', 'addcredit');
        }
        else
        {
            $title = $aInt->lang('credit', 'removecredit');
        }
        $result = select_query('tblclients', '', array( 'id' => $userid ));
        $data = mysql_fetch_array($result);
        $creditbalance = formatCurrency($data['credit']);
        echo "\n<form method=\"post\" action=\"";
        echo $whmcs->getPhpSelf();
        echo "?userid=";
        echo $userid;
        echo "&sub=";
        echo $action;
        echo "\">\n\n<p>";
        echo $aInt->lang('fields', 'client');
        echo ": <B>";
        echo $name;
        echo "</B> (";
        echo $aInt->lang('fields', 'balance');
        echo ": ";
        echo $creditbalance;
        echo ")</p>\n\n<p><b>";
        echo $title;
        echo "</b></p>\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'date');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"date\" size=\"12\" value=\"";
        echo $date;
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'description');
        echo "</td><td class=\"fieldarea\"><textarea name=\"description\" cols=\"75\" rows=\"4\">";
        echo $description;
        echo "</textarea></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'amount');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"amount\" size=\"15\" value=\"";
        echo $amount;
        echo "\"></td></tr>\n</table>\n\n<p align=center><input type=\"submit\" value=\"";
        echo $aInt->lang('global', 'savechanges');
        echo "\" class=\"button\"></p>\n\n</form>\n\n";
    }
    else
    {
        if( $action == 'edit' )
        {
            checkPermission("Manage Credits");
            $result = select_query('tblcredit', '', array( 'id' => $id ));
            $data = mysql_fetch_array($result);
            $id = $data['id'];
            $date = $data['date'];
            $date = fromMySQLDate($date);
            $description = $data['description'];
            $amount = $data['amount'];
            echo "\n<form method=\"post\" action=\"";
            echo $whmcs->getPhpSelf();
            echo "?userid=";
            echo $userid;
            echo "&sub=save&id=";
            echo $id;
            echo "\">\n\n<p><b>";
            echo $aInt->lang('credit', 'addcredit');
            echo "</b></p>\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">";
            echo $aInt->lang('fields', 'date');
            echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"date\" size=\"12\" value=\"";
            echo $date;
            echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
            echo $aInt->lang('fields', 'description');
            echo "</td><td class=\"fieldarea\"><textarea name=\"description\" cols=\"75\" rows=\"4\">";
            echo $description;
            echo "</textarea></td></tr>\n<tr><td class=\"fieldlabel\">";
            echo $aInt->lang('fields', 'amount');
            echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"amount\" size=\"15\" value=\"";
            echo $amount;
            echo "\"></td></tr>\n</table>\n\n<p align=center><input type=\"submit\" value=\"";
            echo $aInt->lang('global', 'savechanges');
            echo "\" class=\"button\"></p>\n\n</form>\n\n";
        }
    }
}
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->displayPopUp();