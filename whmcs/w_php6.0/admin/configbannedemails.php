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
$aInt = new WHMCS_Admin("Configure Banned Emails");
$aInt->title = $aInt->lang('bans', 'emailtitle');
$aInt->sidebar = 'config';
$aInt->icon = 'configbans';
$aInt->helplink = "Security/Ban Control";
if( $email )
{
    check_token("WHMCS.admin.default");
    insert_query('tblbannedemails', array( 'domain' => $email ));
    redir("success=true");
}
if( $action == 'delete' )
{
    check_token("WHMCS.admin.default");
    delete_query('tblbannedemails', array( 'id' => $id ));
    redir("delete=true");
}
ob_start();
if( $success )
{
    infoBox($aInt->lang('bans', 'emailaddsuccess'), $aInt->lang('bans', 'emailaddsuccessinfo'));
}
if( $delete )
{
    infoBox($aInt->lang('bans', 'emaildelsuccess'), $aInt->lang('bans', 'emaildelsuccessinfo'));
}
echo $infobox;
$aInt->deleteJSConfirm('doDelete', 'bans', 'emaildelsure', "?action=delete&id=");
echo $aInt->Tabs(array( $aInt->lang('global', 'add') ), true);
echo "\n<div id=\"tab0box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n<form method=\"post\" action=\"";
echo $whmcs->getPhpSelf();
echo "\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">";
echo $aInt->lang('fields', 'email');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"email\" size=\"50\"> (";
echo $aInt->lang('bans', 'onlydomain');
echo ")</td></tr>\n</table>\n\n<img src=\"images/spacer.gif\" height=\"10\" width=\"1\"><br>\n<div align=\"center\"><input type=\"submit\" value=\"";
echo $aInt->lang('bans', 'addbannedemail');
echo "\" class=\"button\"></div>\n\n</form>\n\n  </div>\n</div>\n\n<br>\n\n";
$aInt->sortableTableInit('nopagination');
$result = select_query('tblbannedemails', '', '', 'domain', 'ASC');
while( $data = mysql_fetch_array($result) )
{
    $id = $data['id'];
    $domain = $data['domain'];
    $count = $data['count'];
    $tabledata[] = array( $domain, $count, "<a href=\"#\" onClick=\"doDelete('" . $id . "');return false\"><img src=\"images/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'delete') . "\"></a>" );
}
echo $aInt->sortableTable(array( $aInt->lang('bans', 'emaildomain'), $aInt->lang('bans', 'usagecount'), '' ), $tabledata);
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jquerycode = $jquerycode;
$aInt->jscode = $jscode;
$aInt->display();