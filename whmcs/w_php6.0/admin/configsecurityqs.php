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
$aInt = new WHMCS_Admin("Configure Security Questions");
$aInt->title = $aInt->lang('setup', 'securityqs');
$aInt->sidebar = 'config';
$aInt->icon = 'securityquestions';
$aInt->helplink = "Security Questions";
if( $action == 'savequestion' )
{
    check_token("WHMCS.admin.default");
    if( $id )
    {
        update_query('tbladminsecurityquestions', array( 'question' => encrypt($addquestion) ), array( 'id' => $id ));
        redir("update=true");
    }
    else
    {
        insert_query('tbladminsecurityquestions', array( 'question' => encrypt($addquestion) ));
        redir("added=true");
    }
}
if( $action == 'delete' )
{
    check_token("WHMCS.admin.default");
    $result = select_query('tblclients', '', array( 'securityqid' => $id ));
    $numaccounts = mysql_num_rows($result);
    if( 0 < $numaccounts )
    {
        redir("deleteerror=true");
    }
    else
    {
        delete_query('tbladminsecurityquestions', array( 'id' => $id ));
        redir("deletesuccess=true");
    }
}
ob_start();
if( $deletesuccess )
{
    infoBox($aInt->lang('securityquestionconfig', 'delsuccess'), $aInt->lang('securityquestionconfig', 'delsuccessinfo'));
}
if( $deleteerror )
{
    infoBox($aInt->lang('securityquestionconfig', 'error'), $aInt->lang('securityquestionconfig', 'errorinfo'));
}
if( $added )
{
    infoBox($aInt->lang('securityquestionconfig', 'addsuccess'), $aInt->lang('securityquestionconfig', 'changesuccessinfo'));
}
if( $update )
{
    infoBox($aInt->lang('securityquestionconfig', 'changesuccess'), $aInt->lang('securityquestionconfig', 'changesuccessinfo'));
}
echo $infobox;
$aInt->deleteJSConfirm('doDelete', 'securityquestionconfig', 'delsuresecurityquestion', "?action=delete&id=");
echo "\n<h2>";
echo $aInt->lang('securityquestionconfig', 'questions');
echo "</h2>\n\n";
$aInt->sortableTableInit('nopagination');
$result = select_query('tbladminsecurityquestions', '', '');
while( $data = mysql_fetch_assoc($result) )
{
    $count = select_query('tblclients', "count(securityqid) as cnt", array( 'securityqid' => $data['id'] ));
    $count_data = mysql_fetch_assoc($count);
    $cnt = is_null($count_data['cnt']) ? '0' : $count_data['cnt'];
    $tabledata[] = array( decrypt($data['question']), $cnt, "<a href=\"" . $_SERVER['PHP_SELF'] . "?action=edit&id=" . $data['id'] . "\"><img src=\"images/edit.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'edit') . "\"></a>", "<a href=\"#\" onClick=\"doDelete('" . $data['id'] . "');return false\"><img src=\"images/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'delete') . "\"></a>" );
}
echo $aInt->sortableTable(array( $aInt->lang('securityquestionconfig', 'question'), $aInt->lang('securityquestionconfig', 'uses'), '', '' ), $tabledata);
echo "\n<h2>";
if( $action == 'edit' )
{
    $result = select_query('tbladminsecurityquestions', '', array( 'id' => $id ));
    $data = mysql_fetch_array($result);
    $question = decrypt($data['question']);
    echo $aInt->lang('securityquestionconfig', 'edit');
}
else
{
    echo $aInt->lang('securityquestionconfig', 'add');
}
echo "</h2>\n\n<form method=\"post\" action=\"";
echo $whmcs->getPhpSelf();
echo "?action=savequestion&id=";
echo $id;
echo "\">\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">";
echo $aInt->lang('fields', 'securityquestion');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"addquestion\" value=\"";
echo $question;
echo "\" size=\"100\" /></td></tr>\n</table>\n<p align=center><input type=\"submit\" value=\"";
echo $aInt->lang('global', 'savechanges');
echo "\" class=\"button\"></p>\n</form>\n\n";
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jquerycode = $jquerycode;
$aInt->jscode = $jscode;
$aInt->display();