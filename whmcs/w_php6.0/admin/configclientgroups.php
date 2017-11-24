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
$aInt = new WHMCS_Admin("Configure Client Groups");
$aInt->title = $aInt->lang('clientgroups', 'title');
$aInt->sidebar = 'config';
$aInt->icon = 'clients';
$aInt->helplink = "Client Groups";
if( $action == 'savegroup' )
{
    check_token("WHMCS.admin.default");
    insert_query('tblclientgroups', array( 'groupname' => $groupname, 'groupcolour' => $groupcolour, 'discountpercent' => $discountpercent, 'susptermexempt' => $susptermexempt, 'separateinvoices' => $separateinvoices ));
    redir("added=true");
}
if( $action == 'updategroup' )
{
    check_token("WHMCS.admin.default");
    update_query('tblclientgroups', array( 'groupname' => $groupname, 'groupcolour' => $groupcolour, 'discountpercent' => $discountpercent, 'susptermexempt' => $susptermexempt, 'separateinvoices' => $separateinvoices ), array( 'id' => $groupid ));
    redir("update=true");
}
if( $action == 'delete' )
{
    check_token("WHMCS.admin.default");
    $result = select_query('tblclients', '', array( 'groupid' => $id ));
    $numaccounts = mysql_num_rows($result);
    if( 0 < $numaccounts )
    {
        redir("deleteerror=true");
    }
    else
    {
        delete_query('tblclientgroups', array( 'id' => $id ));
        foreach( array( 'domainregister', 'domaintransfer', 'domainrenew' ) as $type )
        {
            delete_query('tblpricing', array( 'type' => $type, 'tsetupfee' => $id ));
        }
        redir("deletesuccess=true");
    }
}
if( $action == 'edit' )
{
    $result = select_query('tblclientgroups', '', array( 'id' => $id ));
    $data = mysql_fetch_assoc($result);
    foreach( $data as $name => $value )
    {
        ${$name} = $value;
    }
}
ob_start();
if( $added )
{
    infoBox($aInt->lang('clientgroups', 'addsuccess'), $aInt->lang('clientgroups', 'addsuccessinfo'));
}
if( $update )
{
    infoBox($aInt->lang('clientgroups', 'editsuccess'), $aInt->lang('clientgroups', 'editsuccessinfo'));
}
if( $deletesuccess )
{
    infoBox($aInt->lang('clientgroups', 'delsuccess'), $aInt->lang('clientgroups', 'delsuccessinfo'));
}
if( $deleteerror )
{
    infoBox($aInt->lang('global', 'erroroccurred'), $aInt->lang('clientgroups', 'delerrorinfo'));
}
echo $infobox;
$jscode = "function doDelete(id) {\nif (confirm(\"" . $aInt->lang('clientgroups', 'delsure') . "\")) {\nwindow.location='" . $_SERVER['PHP_SELF'] . "?action=delete&id='+id+'" . generate_token('link') . "';\n}}";
echo "\n<p>";
echo $aInt->lang('clientgroups', 'info');
echo "</p>\n\n";
$aInt->sortableTableInit('nopagination');
$result = select_query('tblclientgroups', '', '');
while( $data = mysql_fetch_assoc($result) )
{
    $suspterm = $data['susptermexempt'] == 'on' ? $aInt->lang('global', 'yes') : $aInt->lang('global', 'no');
    $separateinv = $data['separateinvoices'] == 'on' ? $aInt->lang('global', 'yes') : $aInt->lang('global', 'no');
    $groupcol = $data['groupcolour'] ? "<div style=\"width:75px;background-color:" . $data['groupcolour'] . "\">" . $aInt->lang('clientgroups', 'sample') . "</div>" : '';
    $tabledata[] = array( $data['groupname'], $groupcol, $data['discountpercent'], $suspterm, $separateinv, "<a href=\"" . $_SERVER['PHP_SELF'] . "?action=edit&id=" . $data['id'] . "\"><img src=\"images/edit.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'edit') . "\"></a>", "<a href=\"#\" onClick=\"doDelete('" . $data['id'] . "');return false\"><img src=\"images/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'delete') . "\"></a>" );
}
echo $aInt->sortableTable(array( $aInt->lang('clientgroups', 'groupname'), $aInt->lang('clientgroups', 'groupcolour'), $aInt->lang('clientgroups', 'perdiscount'), $aInt->lang('clientgroups', 'susptermexempt'), $aInt->lang('clients', 'separateinvoices'), '', '' ), $tabledata);
$setaction = $action == 'edit' ? 'updategroup' : 'savegroup';
echo "\n<script type=\"text/javascript\" src=\"../includes/jscript/jquery.miniColors.js\"></script>\n<link rel=\"stylesheet\" type=\"text/css\" href=\"../includes/jscript/css/jquery.miniColors.css\" />\n";
$jquerycode = "\$(\".colorpicker\").miniColors();";
echo "\n<h2>";
if( $action == 'edit' )
{
    echo $aInt->lang('global', 'edit');
}
else
{
    echo $aInt->lang('global', 'add');
}
echo " ";
echo $aInt->lang('clientgroups', 'clientgroup');
echo "</h2>\n\n<form method=\"post\" action=\"";
echo $whmcs->getPhpSelf();
echo "?action=";
echo $setaction;
echo "\">\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"25%\" class=\"fieldlabel\">";
echo $aInt->lang('clientgroups', 'groupname');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"groupname\" size=\"40\" value=\"";
echo $groupname;
echo "\" /></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('clientgroups', 'groupcolour');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"groupcolour\" size=\"10\" value=\"";
echo $groupcolour;
echo "\" class=\"colorpicker\" /></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('clientgroups', 'grpdispercent');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"discountpercent\" size=\"10\" value=\"";
echo $discountpercent;
echo "\" /></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('clientgroups', 'exemptsusterm');
echo "</td><td class=\"fieldarea\"><input type=\"checkbox\" name=\"susptermexempt\"";
if( $susptermexempt )
{
    echo 'checked';
}
echo " /></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('clients', 'separateinvoicesdesc');
echo "</td><td class=\"fieldarea\"><input type=\"checkbox\" name=\"separateinvoices\"";
if( $separateinvoices )
{
    echo 'checked';
}
echo " /></td></tr>\n<input type=\"hidden\" name=\"groupid\" value=\"";
echo $id;
echo "\" />\n</table>\n<p align=center><input type=\"submit\" value=\"";
echo $aInt->lang('global', 'savechanges');
echo "\" class=\"button\"></p>\n</form>\n\n";
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jquerycode = $jquerycode;
$aInt->jscode = $jscode;
$aInt->display();