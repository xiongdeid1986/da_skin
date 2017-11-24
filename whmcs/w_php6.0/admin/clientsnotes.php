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
if( $action == 'edit' )
{
    $reqperm = "Add/Edit Client Notes";
}
else
{
    $reqperm = "View Clients Notes";
}
$aInt = new WHMCS_Admin($reqperm);
$aInt->inClientsProfile = true;
$aInt->valUserID($userid);
$id = (int) $id;
$aInt->assertClientBoundary($userid);
if( $sub == 'add' )
{
    check_token("WHMCS.admin.default");
    checkPermission("Add/Edit Client Notes");
    insert_query('tblnotes', array( 'userid' => $userid, 'adminid' => $_SESSION['adminid'], 'created' => "now()", 'modified' => "now()", 'note' => $note, 'sticky' => $sticky ));
    logActivity("Added Note - User ID: " . $userid);
    redir("userid=" . $userid);
}
else
{
    if( $sub == 'save' )
    {
        check_token("WHMCS.admin.default");
        checkPermission("Add/Edit Client Notes");
        update_query('tblnotes', array( 'note' => $note, 'sticky' => $sticky, 'modified' => "now()" ), array( 'id' => $id ));
        logActivity("Updated Note - User ID: " . $userid . " - ID: " . $id);
        redir("userid=" . $userid);
    }
    else
    {
        if( $sub == 'delete' )
        {
            check_token("WHMCS.admin.default");
            checkPermission("Delete Client Notes");
            delete_query('tblnotes', array( 'id' => $id ));
            logActivity("Deleted Note - User ID: " . $userid . " - ID: " . $id);
            redir("userid=" . $userid);
        }
    }
}
$aInt->deleteJSConfirm('doDelete', 'clients', 'deletenote', "clientsnotes.php?userid=" . $userid . "&sub=delete&id=");
ob_start();
$aInt->sortableTableInit('created', 'ASC');
$result = select_query('tblnotes', "COUNT(*)", array( 'userid' => $userid ), 'created', 'ASC', '', "tbladmins ON tbladmins.id=tblnotes.adminid");
$data = mysql_fetch_array($result);
$numrows = $data[0];
$result = select_query('tblnotes', "tblnotes.*,(SELECT CONCAT(firstname,' ',lastname) FROM tbladmins WHERE tbladmins.id=tblnotes.adminid) AS adminuser", array( 'userid' => $userid ), 'modified', 'DESC');
while( $data = mysql_fetch_array($result) )
{
    $noteid = $data['id'];
    $created = $data['created'];
    $modified = $data['modified'];
    $note = $data['note'];
    $admin = $data['adminuser'];
    if( !$admin )
    {
        $admin = "Admin Deleted";
    }
    $note = nl2br($note);
    $note = autoHyperLink($note);
    $created = fromMySQLDate($created, 'time');
    $modified = fromMySQLDate($modified, 'time');
    $importantnote = $data['sticky'] ? 'high' : 'low';
    $tabledata[] = array( $created, $note, $admin, $modified, "<img src=\"images/" . $importantnote . "priority.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('clientsummary', 'importantnote') . "\">", "<a href=\"?userid=" . $userid . "&action=edit&id=" . $noteid . "\"><img src=\"images/edit.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'edit') . "\"></a>", "<a href=\"#\" onClick=\"doDelete('" . $noteid . "');return false\"><img src=\"images/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'delete') . "\"></a>" );
}
echo $aInt->sortableTable(array( $aInt->lang('fields', 'created'), $aInt->lang('fields', 'note'), $aInt->lang('fields', 'admin'), $aInt->lang('fields', 'lastmodified'), '', '', '' ), $tabledata);
echo "\n<br>\n\n";
if( $action == 'edit' )
{
    $notesdata = get_query_vals('tblnotes', "note, sticky", array( 'userid' => $userid, 'id' => $id ));
    $note = $notesdata['note'];
    $importantnote = $notesdata['sticky'] ? " checked" : '';
    echo "<form method=\"post\" action=\"";
    echo $whmcs->getPhpSelf();
    echo "?userid=";
    echo $userid;
    echo "&sub=save&id=";
    echo $id;
    echo "\">\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td class=\"fieldarea\"><textarea name=\"note\" rows=\"6\" style=\"width:99%;\">";
    echo $note;
    echo "</textarea></td><td align=\"center\" width=\"60\"><input type=\"submit\" value=\"";
    echo $aInt->lang('global', 'savechanges');
    echo "\" class=\"button\"><br /><label><input type=\"checkbox\" class=\"checkbox\" name=\"sticky\" value=\"1\"";
    echo $importantnote;
    echo " /> ";
    echo $aInt->lang('clientsummary', 'stickynotescheck');
    echo "</label></td></tr>\n</table>\n</form>\n";
}
else
{
    echo "<form method=\"post\" action=\"";
    echo $whmcs->getPhpSelf();
    echo "?userid=";
    echo $userid;
    echo "&sub=add\">\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td class=\"fieldarea\"><textarea name=\"note\" rows=\"6\" style=\"width:99%;\"></textarea></td><td align=\"center\" width=\"60\"><input type=\"submit\" value=\"";
    echo $aInt->lang('global', 'addnew');
    echo "\" class=\"button\" /><br /><label><input type=\"checkbox\" class=\"checkbox\" name=\"sticky\" value=\"1\" /> ";
    echo $aInt->lang('clientsummary', 'stickynotescheck');
    echo "</label></td></tr>\n</table>\n</form>\n";
}
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jquerycode = $jquerycode;
$aInt->jscode = $jscode;
$aInt->display();