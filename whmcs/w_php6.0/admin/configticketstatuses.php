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
$aInt = new WHMCS_Admin("Configure Ticket Statuses");
$aInt->title = $aInt->lang('setup', 'ticketstatuses');
$aInt->sidebar = 'config';
$aInt->icon = 'clients';
$aInt->helplink = "Support Ticket Statuses";
if( $action == 'save' )
{
    check_token("WHMCS.admin.default");
    if( $id )
    {
        update_query('tblticketstatuses', array( 'title' => trim($title), 'color' => $color, 'sortorder' => $sortorder, 'showactive' => $showactive, 'showawaiting' => $showawaiting, 'autoclose' => $autoclose ), array( 'id' => $id ));
        redir("update=true");
    }
    else
    {
        insert_query('tblticketstatuses', array( 'title' => trim($title), 'color' => $color, 'sortorder' => $sortorder, 'showactive' => $showactive, 'showawaiting' => $showawaiting, 'autoclose' => $autoclose ));
        redir("added=true");
    }
}
if( $action == 'delete' )
{
    check_token("WHMCS.admin.default");
    $result = select_query('tblticketstatuses', 'title', array( 'id' => $id ));
    $data = mysql_fetch_assoc($result);
    $title = $data['title'];
    update_query('tbltickets', array( 'status' => 'Closed' ), array( 'status' => $title ));
    delete_query('tblticketstatuses', array( 'id' => $id ));
    redir("delete=true");
}
ob_start();
if( $added )
{
    infoBox($aInt->lang('ticketstatusconfig', 'statusaddtitle'), $aInt->lang('ticketstatusconfig', 'statusadddesc'));
}
if( $update )
{
    infoBox($aInt->lang('ticketstatusconfig', 'statusedittitle'), $aInt->lang('ticketstatusconfig', 'statuseditdesc'));
}
if( $delete )
{
    infoBox($aInt->lang('ticketstatusconfig', 'statusdeltitle'), $aInt->lang('ticketstatusconfig', 'statusdeldesc'));
}
echo $infobox;
$aInt->deleteJSConfirm('doDelete', 'ticketstatusconfig', 'delsureticketstatus', "?action=delete&id=");
echo "\n<p>";
echo $aInt->lang('ticketstatusconfig', 'pagedesc');
echo "</p>\n\n<p><strong>";
echo $aInt->lang('fields', 'options');
echo ":</strong> <a href=\"";
echo $whmcs->getPhpSelf();
echo "\"><img src=\"images/icons/add.png\" align=\"top\" /> ";
echo $aInt->lang('global', 'addnew');
echo "</a></p>\n\n";
$aInt->sortableTableInit('nopagination');
$result = select_query('tblticketstatuses', '', '', 'sortorder', 'ASC');
while( $data = mysql_fetch_assoc($result) )
{
    $statusid = $data['id'];
    $title = $data['title'];
    $color = $data['color'];
    $showactive = $data['showactive'];
    $showawaiting = $data['showawaiting'];
    $autoclose = $data['autoclose'];
    $sortorder = $data['sortorder'];
    $showactive = $showactive ? "<img src=\"images/icons/tick.png\">" : "<img src=\"images/icons/disabled.png\">";
    $showawaiting = $showawaiting ? "<img src=\"images/icons/tick.png\">" : "<img src=\"images/icons/disabled.png\">";
    $autoclose = $autoclose ? "<img src=\"images/icons/tick.png\">" : "<img src=\"images/icons/disabled.png\">";
    if( 4 < $statusid )
    {
        $delete = "<a href=\"#\" onClick=\"doDelete('" . $statusid . "');return false\"><img src=\"images/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'delete') . "\"></a>";
    }
    else
    {
        $delete = '';
    }
    $tabledata[] = array( "<span style=\"font-weight:bold;color:" . $color . "\">" . $title . "</span>", $showactive, $showawaiting, $autoclose, $sortorder, "<a href=\"" . $_SERVER['PHP_SELF'] . "?action=edit&id=" . $statusid . "\"><img src=\"images/edit.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'edit') . "\"></a>", $delete );
}
echo $aInt->sortableTable(array( $aInt->lang('fields', 'title'), $aInt->lang('ticketstatusconfig', 'includeinactivetickets'), $aInt->lang('ticketstatusconfig', 'includeinawaitingreply'), $aInt->lang('ticketstatusconfig', 'autoclose'), $aInt->lang('products', 'sortorder'), '', '' ), $tabledata);
echo "\n<script type=\"text/javascript\" src=\"../includes/jscript/jquery.miniColors.js\"></script>\n<link rel=\"stylesheet\" type=\"text/css\" href=\"../includes/jscript/css/jquery.miniColors.css\" />\n";
$jquerycode = "\$(\".colorpicker\").miniColors();";
echo "\n<h2>";
if( $action == 'edit' )
{
    $result = select_query('tblticketstatuses', '', array( 'id' => $id ));
    $data = mysql_fetch_array($result);
    $title = $data['title'];
    $color = $data['color'];
    $sortorder = $data['sortorder'];
    $showactive = $data['showactive'];
    $showawaiting = $data['showawaiting'];
    $autoclose = $data['autoclose'];
    echo $aInt->lang('ticketstatusconfig', 'edit');
}
else
{
    $title = $showactive = $showawaiting = $autoclose = '';
    $color = "#000000";
    echo $aInt->lang('ticketstatusconfig', 'add');
}
echo "</h2>\n\n<form method=\"post\" action=\"";
echo $whmcs->getPhpSelf();
echo "?action=save&id=";
echo $id;
echo "\">\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"25%\" class=\"fieldlabel\">";
echo $aInt->lang('clientsummary', 'filetitle');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"title\" size=\"30\" value=\"";
echo $title;
echo "\"";
if( $id && $id <= 4 )
{
    echo " readonly=\"true\"";
}
echo " /></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('ticketstatusconfig', 'statuscolor');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"color\" size=\"10\" value=\"";
echo $color;
echo "\" class=\"colorpicker\" /></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('ticketstatusconfig', 'includeinactivetickets');
echo "</td><td class=\"fieldarea\"><input type=\"checkbox\" name=\"showactive\" value=\"1\"";
if( $showactive )
{
    echo " checked";
}
echo " /></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('ticketstatusconfig', 'includeinawaitingreply');
echo "</td><td class=\"fieldarea\"><input type=\"checkbox\" name=\"showawaiting\" value=\"1\"";
if( $showawaiting )
{
    echo " checked";
}
echo " /></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('ticketstatusconfig', 'autoclose');
echo "</td><td class=\"fieldarea\"><input type=\"checkbox\" name=\"autoclose\" value=\"1\"";
if( $autoclose )
{
    echo " checked";
}
echo " /></td></tr>\n<tr><td width=\"25%\" class=\"fieldlabel\">";
echo $aInt->lang('products', 'sortorder');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"sortorder\" size=\"10\" value=\"";
echo $sortorder;
echo "\" /></td></tr>\n</table>\n<p align=center><input type=\"submit\" value=\"";
echo $aInt->lang('global', 'savechanges');
echo "\" class=\"button\"></p>\n</form>\n\n";
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jquerycode = $jquerycode;
$aInt->jscode = $jscode;
$aInt->display();