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
$aInt = new WHMCS_Admin("Configure Support Departments");
$aInt->title = $aInt->lang('supportticketescalations', 'supportticketescalationstitle');
$aInt->sidebar = 'config';
$aInt->icon = 'todolist';
$aInt->helplink = "Support Ticket Escalations";
if( $action == 'save' )
{
    check_token("WHMCS.admin.default");
    if( is_array($departments) )
    {
        $departments = implode(',', $departments);
    }
    if( is_array($statuses) )
    {
        $statuses = implode(',', $statuses);
    }
    if( is_array($priorities) )
    {
        $priorities = implode(',', $priorities);
    }
    if( is_array($notify) )
    {
        $notify = implode(',', $notify);
    }
    if( $id )
    {
        update_query('tblticketescalations', array( 'name' => $name, 'departments' => $departments, 'statuses' => $statuses, 'priorities' => $priorities, 'timeelapsed' => $timeelapsed, 'newdepartment' => $newdepartment, 'newstatus' => $newstatus, 'newpriority' => $newpriority, 'flagto' => $flagto, 'notify' => $notify, 'addreply' => $addreply ), array( 'id' => $id ));
        redir("saved=true");
    }
    else
    {
        insert_query('tblticketescalations', array( 'name' => $name, 'departments' => $departments, 'statuses' => $statuses, 'priorities' => $priorities, 'timeelapsed' => $timeelapsed, 'newdepartment' => $newdepartment, 'newstatus' => $newstatus, 'newpriority' => $newpriority, 'flagto' => $flagto, 'notify' => $notify, 'addreply' => $addreply ));
        redir("added=true");
    }
}
if( $action == 'delete' )
{
    check_token("WHMCS.admin.default");
    delete_query('tblticketescalations', array( 'id' => $id ));
    redir("deleted=true");
}
ob_start();
if( $action == '' )
{
    if( $added )
    {
        infoBox($aInt->lang('supportticketescalations', 'ruleaddsuccess'), $aInt->lang('supportticketescalations', 'ruleaddsuccessdesc'));
    }
    if( $saved )
    {
        infoBox($aInt->lang('supportticketescalations', 'ruleeditsuccess'), $aInt->lang('supportticketescalations', 'ruleeditsuccessdesc'));
    }
    if( $deleted )
    {
        infoBox($aInt->lang('supportticketescalations', 'ruledelsuccess'), $aInt->lang('supportticketescalations', 'ruledelsuccessdesc'));
    }
    echo $infobox;
    $aInt->deleteJSConfirm('doDelete', 'supportticketescalations', 'delsureescalationrule', "?action=delete&id=");
    echo "\n<p>";
    echo $aInt->lang('supportticketescalations', 'escalationrulesinfo');
    echo "</p>\n\n<div class=\"contentbox\">\n";
    echo $aInt->lang('supportticketescalations', 'croncommandreq');
    echo "<br /><input type=\"text\" size=\"100\" value=\"php -q ";
    echo ROOTDIR . '/' . $whmcs->get_admin_folder_name();
    echo "/cron.php escalations\" />\n</div>\n\n<p><B>";
    echo $aInt->lang('fields', 'options');
    echo ":</B> <a href=\"";
    echo $_SERVER['PHP_SELF'];
    echo "?action=manage\">";
    echo $aInt->lang('supportticketescalations', 'addnewrule');
    echo "</a></p>\n\n";
    $aInt->sortableTableInit('nopagination');
    $result = select_query('tblticketescalations', '', '', 'name', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $id = $data['id'];
        $name = $data['name'];
        $tabledata[] = array( $name, "<a href=\"" . $_SERVER['PHP_SELF'] . "?action=manage&id=" . $id . "\"><img src=\"images/edit.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'edit') . "\"></a>", "<a href=\"#\" onClick=\"doDelete('" . $id . "');return false\"><img src=\"images/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'delete') . "\"></a>" );
    }
    echo $aInt->sortableTable(array( $aInt->lang('addons', 'name'), '', '' ), $tabledata);
}
else
{
    if( $action == 'manage' )
    {
        if( $id )
        {
            $edittitle = "Edit Rule";
            $result = select_query('tblticketescalations', '', array( 'id' => $id ));
            $data = mysql_fetch_array($result);
            $id = $data['id'];
            $name = $data['name'];
            $departments = $data['departments'];
            $statuses = $data['statuses'];
            $priorities = $data['priorities'];
            $timeelapsed = $data['timeelapsed'];
            $newdepartment = $data['newdepartment'];
            $newstatus = $data['newstatus'];
            $newpriority = $data['newpriority'];
            $flagto = $data['flagto'];
            $notify = $data['notify'];
            $addreply = $data['addreply'];
            $departments = explode(',', $departments);
            $statuses = explode(',', $statuses);
            $priorities = explode(',', $priorities);
            $notify = explode(',', $notify);
        }
        else
        {
            $edittitle = "Add New Rule";
            $departments = $statuses = $priorities = $notify = array(  );
        }
        echo "<h2>" . $edittitle . "</h2>";
        echo "\n<form method=\"post\" action=\"";
        echo $_SERVER['PHP_SELF'];
        echo "?action=save\">\n<input type=\"hidden\" name=\"id\" value=\"";
        echo $id;
        echo "\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"20%\" class=\"fieldlabel\">";
        echo $aInt->lang('addons', 'name');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"name\" size=\"50\" value=\"";
        echo $name;
        echo "\"></td></tr>\n</table>\n\n<p><b>";
        echo $aInt->lang('supportticketescalations', 'conditions');
        echo "</b></p>\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"20%\" class=\"fieldlabel\">";
        echo $aInt->lang('supportticketescalations', 'departments');
        echo "</td><td class=\"fieldarea\"><select name=\"departments[]\" size=\"4\" multiple=\"true\">";
        $result = select_query('tblticketdepartments', '', '', 'name', 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $departmentid = $data['id'];
            $departmentname = $data['name'];
            echo "<option value=\"" . $departmentid . "\"";
            if( in_array($departmentid, $departments) )
            {
                echo " selected";
            }
            echo ">" . $departmentname . "</option>";
        }
        echo "</select></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('supportticketescalations', 'statuses');
        echo "</td><td class=\"fieldarea\"><select name=\"statuses[]\" size=\"4\" multiple=\"true\">\n";
        $result = select_query('tblticketstatuses', '', '', 'sortorder', 'ASC');
        while( $data = mysql_fetch_assoc($result) )
        {
            $title = $data['title'];
            echo "<option";
            if( in_array($title, $statuses) )
            {
                echo " selected";
            }
            echo ">" . $title . "</option>";
        }
        echo "</select></td></tr>\n<tr><td class=\"fieldlabel\">Priorities</td><td class=\"fieldarea\"><select name=\"priorities[]\" size=\"3\" multiple=\"true\">\n<option value=\"Low\"";
        if( in_array('Low', $priorities) )
        {
            echo " selected";
        }
        echo ">";
        echo $aInt->lang('supportticketescalations', 'prioritylow');
        echo "</option>\n<option value=\"Medium\"";
        if( in_array('Medium', $priorities) )
        {
            echo " selected";
        }
        echo ">";
        echo $aInt->lang('supportticketescalations', 'prioritymedium');
        echo "</option>\n<option value=\"High\"";
        if( in_array('High', $priorities) )
        {
            echo " selected";
        }
        echo ">";
        echo $aInt->lang('supportticketescalations', 'priorityhigh');
        echo "</option>\n</select></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('supportticketescalations', 'timeelapsed');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"timeelapsed\" size=\"10\" value=\"";
        echo $timeelapsed;
        echo "\"> ";
        echo $aInt->lang('supportticketescalations', 'minsincelastreply');
        echo "</td></tr>\n</table>\n\n<p><b>";
        echo $aInt->lang('supportticketescalations', 'actions');
        echo "</b></p>\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"20%\" class=\"fieldlabel\">";
        echo $aInt->lang('supportticketescalations', 'department');
        echo "</td><td class=\"fieldarea\"><select name=\"newdepartment\"><option value=\"\">- ";
        echo $aInt->lang('supportticketescalations', 'nochange');
        echo " -</option>";
        $result = select_query('tblticketdepartments', '', '', 'name', 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $departmentid = $data['id'];
            $departmentname = $data['name'];
            echo "<option value=\"" . $departmentid . "\"";
            if( $newdepartment == $departmentid )
            {
                echo " selected";
            }
            echo ">" . $departmentname . "</option>";
        }
        echo "</select></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'status');
        echo "</td><td class=\"fieldarea\"><select name=\"newstatus\"><option value=\"\">- ";
        echo $aInt->lang('supportticketescalations', 'nochange');
        echo " -</option>\n";
        $result = select_query('tblticketstatuses', '', '', 'sortorder', 'ASC');
        while( $data = mysql_fetch_assoc($result) )
        {
            $title = $data['title'];
            echo "<option";
            if( $title == $newstatus )
            {
                echo " selected";
            }
            echo ">" . $title . "</option>";
        }
        echo "</select></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('supportticketescalations', 'priority');
        echo "</td><td class=\"fieldarea\"><select name=\"newpriority\"><option value=\"\">- ";
        echo $aInt->lang('supportticketescalations', 'nochange');
        echo " -</option>\n<option";
        if( $newpriority == 'Low' )
        {
            echo " selected";
        }
        echo ">";
        echo $aInt->lang('supportticketescalations', 'prioritylow');
        echo "</option>\n<option";
        if( $newpriority == 'Medium' )
        {
            echo " selected";
        }
        echo ">";
        echo $aInt->lang('supportticketescalations', 'prioritymedium');
        echo "</option>\n<option";
        if( $newpriority == 'High' )
        {
            echo " selected";
        }
        echo ">";
        echo $aInt->lang('supportticketescalations', 'priorityhigh');
        echo "</option>\n</select></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('supportticketescalations', 'flagto');
        echo "</td><td class=\"fieldarea\"><select name=\"flagto\"><option value=\"\">- ";
        echo $aInt->lang('supportticketescalations', 'nochange');
        echo " -</option>";
        $result = select_query('tbladmins', '', '', 'username', 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $flag_adminid = $data['id'];
            $flag_adminusername = $data['username'];
            echo "<option value=\"" . $flag_adminid . "\"";
            if( $flag_adminid == $flagto )
            {
                echo " selected";
            }
            echo ">" . $flag_adminusername . "</option>";
        }
        echo "</select></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('supportticketescalations', 'notifyadmins');
        echo "</td><td class=\"fieldarea\">\n<label><input type=\"checkbox\" name=\"notify[]\" value=\"all\"";
        if( in_array('all', $notify) )
        {
            echo " checked";
        }
        echo " /> ";
        echo $aInt->lang('supportticketescalations', 'notifyadminsdesc');
        echo "</label>\n<div style=\"padding:5px;\">";
        echo $aInt->lang('supportticketescalations', 'alsonotify');
        echo ":</div>\n";
        $result = select_query('tbladmins', '', '', 'username', 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            echo "<label><input type=\"checkbox\" name=\"notify[]\" value=\"" . $data['id'] . "\"";
            if( in_array($data['id'], $notify) )
            {
                echo " checked";
            }
            echo " /> ";
            if( $data['disabled'] == 1 )
            {
                echo "<span class=\"disabledtext\">";
            }
            echo $data['username'] . " (" . $data['firstname'] . " " . $data['lastname'] . ")";
            if( $data['disabled'] == 1 )
            {
                echo " - " . $aInt->lang('global', 'disabled') . "</span> ";
            }
            echo "</label>";
        }
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('support', 'addreply');
        echo "</td><td class=\"fieldarea\"><textarea name=\"addreply\" rows=\"15\" style=\"width:90%;\">";
        echo $addreply;
        echo "</textarea></td></tr>\n</table>\n\n<p align=\"center\"><input type=\"submit\" value=\"";
        echo $aInt->lang('global', 'savechanges');
        echo "\" class=\"button\" /></p>\n\n</form>\n\n";
    }
}
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jquerycode = $jquerycode;
$aInt->jscode = $jscode;
$aInt->display();