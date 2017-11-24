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
$aInt = new WHMCS_Admin("To-Do List");
$aInt->title = $aInt->lang('todolist', 'todolisttitle');
$aInt->sidebar = 'utilities';
$aInt->icon = 'todolist';
if( $action == 'delete' )
{
    check_token("WHMCS.admin.default");
    delete_query('tbltodolist', array( 'id' => $id ));
    redir();
}
if( $action == 'add' )
{
    check_token("WHMCS.admin.default");
    $table = 'tbltodolist';
    $array = array( 'date' => toMySQLDate($date), 'title' => $title, 'description' => $description, 'admin' => $admin, 'status' => $status, 'duedate' => toMySQLDate($duedate) );
    insert_query($table, $array);
    redir();
}
if( $action == 'save' )
{
    check_token("WHMCS.admin.default");
    $table = 'tbltodolist';
    $array = array( 'date' => toMySQLDate($date), 'title' => $title, 'description' => $description, 'admin' => $admin, 'status' => $status, 'duedate' => toMySQLDate($duedate) );
    $where = array( 'id' => $id );
    update_query($table, $array, $where);
    redir();
}
if( $mass_assign )
{
    check_token("WHMCS.admin.default");
    foreach( $selids as $id )
    {
        update_query('tbltodolist', array( 'admin' => $_SESSION['adminid'] ), array( 'id' => $id ));
    }
    redir();
}
if( $mass_inprogress )
{
    check_token("WHMCS.admin.default");
    foreach( $selids as $id )
    {
        update_query('tbltodolist', array( 'status' => "In Progress" ), array( 'id' => $id ));
    }
    redir();
}
if( $mass_completed )
{
    check_token("WHMCS.admin.default");
    foreach( $selids as $id )
    {
        update_query('tbltodolist', array( 'status' => 'Completed' ), array( 'id' => $id ));
    }
    redir();
}
if( $mass_postponed )
{
    check_token("WHMCS.admin.default");
    foreach( $selids as $id )
    {
        update_query('tbltodolist', array( 'status' => 'Postponed' ), array( 'id' => $id ));
    }
    redir();
}
if( $mass_delete )
{
    check_token("WHMCS.admin.default");
    foreach( $selids as $id )
    {
        delete_query('tbltodolist', array( 'id' => $id ));
    }
    redir();
}
ob_start();
if( $action == '' )
{
    $aInt->deleteJSConfirm('doDelete', 'todolist', 'delsuretodoitem', "?action=delete&id=");
    echo $aInt->Tabs(array( $aInt->lang('global', 'searchfilter'), $aInt->lang('todolist', 'additem') ), true);
    echo "\n<div id=\"tab0box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n<form method=\"post\" action=\"todolist.php\"><input type=\"hidden\" name=\"filter\" value=\"true\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">Date</td><td class=\"fieldarea\"><input type=\"text\" name=\"date\" value=\"";
    echo $date;
    echo "\" class=\"datepick\"></td><td width=\"15%\" class=\"fieldlabel\">";
    echo $aInt->lang('fields', 'duedate');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"duedate\" value=\"";
    echo $duedate;
    echo "\" class=\"datepick\"></td></tr>\n<tr><td class=\"fieldlabel\">Title</td><td class=\"fieldarea\"><input type=\"text\" name=\"title\" size=50 value=\"";
    echo $title;
    echo "\"></td><td class=\"fieldlabel\">";
    echo $aInt->lang('fields', 'admin');
    echo "</td><td class=\"fieldarea\"><select name=\"admin\"><option value=\"\">Any";
    $result2 = select_query('tbladmins', 'id,username', '', 'username', 'ASC');
    while( $data2 = mysql_fetch_array($result2) )
    {
        $admin_id = $data2['id'];
        $admin_username = $data2['username'];
        echo "<option value=\"" . $admin_id . "\"";
        if( $admin_id == $admin )
        {
            echo " selected";
        }
        echo ">" . $admin_username;
    }
    echo "</select></td></tr>\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang('fields', 'description');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"description\" size=55 value=\"";
    echo $description;
    echo "\"></td><td class=\"fieldlabel\">";
    echo $aInt->lang('fields', 'status');
    echo "</td><td class=\"fieldarea\"><select name=\"status\"><option";
    if( $status == 'Incomplete' )
    {
        echo " selected";
    }
    echo ">";
    echo $aInt->lang('todolist', 'incomplete');
    echo "<option";
    if( $status == 'New' )
    {
        echo " selected";
    }
    echo ">New<option";
    if( $status == 'Pending' )
    {
        echo " selected";
    }
    echo ">Pending<option";
    if( $status == "In Progress" )
    {
        echo " selected";
    }
    echo ">In Progress<option";
    if( $status == 'Completed' )
    {
        echo " selected";
    }
    echo ">Completed<option";
    if( $status == 'Postponed' )
    {
        echo " selected";
    }
    echo ">Postponed</select></td></tr>\n</table>\n\n<img src=\"images/spacer.gif\" height=\"10\" width=\"1\"><br>\n<div align=\"center\"><input type=\"submit\" value=\"";
    echo $aInt->lang('global', 'searchfilter');
    echo "\" class=\"button\"></div>\n</form>\n\n  </div>\n</div>\n<div id=\"tab1box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n<form method=\"post\" action=\"";
    echo $whmcs->getPhpSelf();
    echo "?action=add\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">";
    echo $aInt->lang('fields', 'date');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"date\" value=\"";
    echo getTodaysDate();
    echo "\" class=\"datepick\"></td><td width=\"15%\" class=\"fieldlabel\">";
    echo $aInt->lang('fields', 'duedate');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"duedate\" class=\"datepick\"></td></tr>\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang('fields', 'title');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"title\" size=50></td><td class=\"fieldlabel\">";
    echo $aInt->lang('fields', 'admin');
    echo "</td><td class=\"fieldarea\"><select name=\"admin\"><option value=\"\">None";
    $result2 = select_query('tbladmins', 'id,firstname,lastname', array( 'disabled' => '0' ), 'username', 'ASC');
    while( $data2 = mysql_fetch_array($result2) )
    {
        $admin_id = $data2['id'];
        $admin_name = $data2['firstname'] . " " . $data2['lastname'];
        echo "<option value=\"" . $admin_id . "\">" . $admin_name . "</option>";
    }
    echo "</select></td></tr>\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang('fields', 'description');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"description\" size=55></td><td class=\"fieldlabel\">";
    echo $aInt->lang('fields', 'status');
    echo "</td><td class=\"fieldarea\"><select name=\"status\"><option>New<option>Pending<option>In Progress<option>Completed<option>Postponed</select></td></tr>\n</table>\n\n<img src=\"images/spacer.gif\" height=\"10\" width=\"1\"><br>\n<DIV ALIGN=\"center\"><input type=\"submit\" value=\"";
    echo $aInt->lang('todolist', 'addtodoitem');
    echo "\" class=\"button\"></DIV>\n</form>\n\n  </div>\n</div>\n\n<br />\n\n";
    $aInt->sortableTableInit('duedate', 'ASC');
    unset($where);
    if( $status == 'Incomplete' || $status == '' )
    {
        $where['status'] = array( 'sqltype' => 'NEQ', 'value' => 'Completed' );
    }
    else
    {
        $where['status'] = $status;
    }
    if( $date )
    {
        $where['date'] = toMySQLDate($date);
    }
    if( $duedate )
    {
        $where['duedate'] = toMySQLDate($duedate);
    }
    if( $title )
    {
        $where['title'] = array( 'sqltype' => 'LIKE', 'value' => $title );
    }
    if( $description )
    {
        $where['description'] = array( 'sqltype' => 'LIKE', 'value' => $description );
    }
    if( $admin )
    {
        $where['admin'] = $admin;
    }
    $table = 'tbltodolist';
    $result = select_query($table, "COUNT(*)", $where, $orderby, $order);
    $data = mysql_fetch_array($result);
    $numrows = $data[0];
    $AdminsArray = array(  );
    $result = select_query($table, '', $where, $orderby, $order, $page * $limit . ',' . $limit);
    while( $data = mysql_fetch_array($result) )
    {
        $i++;
        $id = $data['id'];
        $date = $data['date'];
        $title = $data['title'];
        $description = $data['description'];
        $adminid = $data['admin'];
        $status = $data['status'];
        $duedate = $data['duedate'];
        $date = fromMySQLDate($date);
        if( $duedate == '0000-00-00' )
        {
            $duedate = '-';
        }
        else
        {
            $duedate = fromMySQLDate($duedate);
        }
        if( 80 < strlen($description) )
        {
            $description = substr($description, 0, 80) . "...";
        }
        if( $adminid )
        {
            if( isset($AdminsArray[$adminid]) )
            {
                $admin = $AdminsArray[$adminid];
            }
            else
            {
                $result2 = select_query('tbladmins', 'firstname,lastname', array( 'id' => $adminid ));
                $data = mysql_fetch_array($result2);
                $admin = $data['firstname'] . " " . $data['lastname'];
                $AdminsArray[$adminid] = $admin;
            }
        }
        else
        {
            $admin = '';
        }
        $tabledata[] = array( "<input type=\"checkbox\" name=\"selids[]\" value=\"" . $id . "\" class=\"checkall\">", $date, $title, $description, $admin, $status, $duedate, "<a href=\"?action=edit&id=" . $id . "\"><img src=\"images/edit.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'edit') . "\"></a>", "<a href=\"#\" onClick=\"doDelete('" . $id . "');return false\"><img src=\"images/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'delete') . "\"></a>" );
    }
    $tableformurl = $_SERVER['PHP_SELF'];
    $tableformbuttons = "<input type=\"submit\" value=\"Assign to Me\" class=\"button\" name=\"mass_assign\"> <input type=\"submit\" value=\"Set In Progress\" class=\"button\" name=\"mass_inprogress\"> <input type=\"submit\" value=\"Set as Completed\" class=\"button\" name=\"mass_completed\"> <input type=\"submit\" value=\"Set to Postponed\" class=\"button\" name=\"mass_postponed\"> <input type=\"submit\" value=\"Delete\" class=\"button\" name=\"mass_delete\">";
    echo $aInt->sortableTable(array( 'checkall', array( 'date', 'Date' ), array( 'title', 'Title' ), array( 'description', 'Description' ), array( 'admin', 'Admin' ), array( 'status', 'Status' ), array( 'duedate', "Due Date" ), '', '' ), $tabledata, $tableformurl, $tableformbuttons);
}
else
{
    if( $action == 'edit' )
    {
        $table = 'tbltodolist';
        $fields = '';
        $where = array( 'id' => $id );
        $result = select_query($table, $fields, $where);
        $data = mysql_fetch_array($result);
        $date = $data['date'];
        $title = $data['title'];
        $description = $data['description'];
        $admin = $data['admin'];
        $status = $data['status'];
        $duedate = $data['duedate'];
        $date = fromMySQLDate($date);
        $duedate = fromMySQLDate($duedate);
        echo "\n<p><b>";
        echo $aInt->lang('todolist', 'edittodoitem');
        echo "</b></p>\n\n<form method=\"post\" action=\"";
        echo $whmcs->getPhpSelf();
        echo "?action=save&id=";
        echo $id;
        echo "\" name=\"calendarfrm\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'date');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"date\" value=\"";
        echo $date;
        echo "\" class=\"datepick\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'title');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"title\" size=50 value=\"";
        echo $title;
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'description');
        echo "</td><td class=\"fieldarea\"><textarea name=\"description\" cols=100 rows=8>";
        echo $description;
        echo "</textarea></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'admin');
        echo "</td><td class=\"fieldarea\"><select name=\"admin\"><option value=\"\">None";
        $result2 = select_query('tbladmins', 'id,firstname,lastname,disabled', '', 'username', 'ASC');
        while( $data2 = mysql_fetch_array($result2) )
        {
            $admin_id = $data2['id'];
            $admin_name = $data2['firstname'] . " " . $data2['lastname'];
            $admin_disabled = $data2['disabled'];
            echo "<option value=\"" . $admin_id . "\"";
            if( $admin_id == $admin )
            {
                echo " selected";
            }
            echo ">" . $admin_name . ($admin_disabled ? " (" . $aInt->lang('global', 'disabled') . ")" : '') . "</option>";
        }
        echo "</select></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'duedate');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"duedate\" value=\"";
        echo $duedate;
        echo "\" class=\"datepick\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'status');
        echo "</td><td class=\"fieldarea\"><select name=\"status\"><option";
        if( $status == 'Incomplete' )
        {
            echo " selected";
        }
        echo ">Incomplete<option";
        if( $status == 'New' )
        {
            echo " selected";
        }
        echo ">New<option";
        if( $status == 'Pending' )
        {
            echo " selected";
        }
        echo ">Pending<option";
        if( $status == "In Progress" )
        {
            echo " selected";
        }
        echo ">In Progress<option";
        if( $status == 'Completed' )
        {
            echo " selected";
        }
        echo ">Completed<option";
        if( $status == 'Postponed' )
        {
            echo " selected";
        }
        echo ">Postponed</select></td></tr>\n</table>\n\n<p align=\"center\"><input type=\"submit\" value=\"";
        echo $aInt->lang('global', 'savechanges');
        echo "\" class=\"button\"></p>\n\n</form>\n\n";
    }
}
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jquerycode = $jquerycode;
$aInt->jscode = $jscode;
$aInt->display();