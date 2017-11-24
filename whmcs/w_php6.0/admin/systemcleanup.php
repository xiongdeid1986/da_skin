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
$aInt = new WHMCS_Admin("System Cleanup Operations");
$aInt->title = $aInt->lang('system', 'cleanupoperations');
$aInt->sidebar = 'utilities';
$aInt->icon = 'cleanup';
$aInt->helplink = "System Utilities#System Cleanup";
ob_start();
if( $action == 'pruneclientactivity' && $date )
{
    check_token("WHMCS.admin.default");
    $sqldate = toMySQLDate($date);
    $query = "DELETE FROM tblactivitylog WHERE userid>0 AND date<'" . db_escape_string($sqldate) . "'";
    $result = full_query($query);
    infoBox($aInt->lang('system', 'cleanupsuccess'), $aInt->lang('system', 'deleteactivityinfo') . " " . $date . " (" . mysql_affected_rows() . ")");
    logActivity("Cleanup Operation: Pruned Client Activity Logs from before " . $date);
}
if( $action == 'deletemessages' && $date )
{
    check_token("WHMCS.admin.default");
    $sqldate = toMySQLDate($date);
    $query = "DELETE FROM tblemails WHERE date<'" . db_escape_string($sqldate) . "'";
    $result = full_query($query);
    infoBox($aInt->lang('system', 'cleanupsuccess'), $aInt->lang('system', 'deletemessagesinfo') . " " . $date . " (" . mysql_affected_rows() . ")");
    logActivity("Cleanup Operation: Pruned Messages Sent before " . $date);
}
if( $action == 'cleargatewaylog' )
{
    check_token("WHMCS.admin.default");
    $query = "TRUNCATE tblgatewaylog";
    $result = full_query($query);
    infoBox($aInt->lang('system', 'cleanupsuccess'), $aInt->lang('system', 'deletegatewaylog'));
    logActivity("Cleanup Operation: Gateway Log Emptied");
}
if( $action == 'clearmailimportlog' )
{
    check_token("WHMCS.admin.default");
    $query = "TRUNCATE tblticketmaillog";
    $result = full_query($query);
    infoBox($aInt->lang('system', 'cleanupsuccess'), $aInt->lang('system', 'deleteticketlog'));
    logActivity("Cleanup Operation: Ticket Mail Import Log Emptied");
}
if( $action == 'clearwhoislog' )
{
    check_token("WHMCS.admin.default");
    $query = "TRUNCATE tblwhoislog";
    $result = full_query($query);
    infoBox($aInt->lang('system', 'cleanupsuccess'), $aInt->lang('system', 'deletewhoislog'));
    logActivity("Cleanup Operation: WHOIS Lookup Log Emptied");
}
if( $action == 'emptytemplatecache' )
{
    check_token("WHMCS.admin.default");
    $smarty = new WHMCS_Smarty();
    $smarty->clearAllCaches();
    infoBox($aInt->lang('system', 'cleanupsuccess'), $aInt->lang('system', 'deletecacheinfo'));
    logActivity("Cleanup Operation: Template Cache Emptied");
}
if( $action == 'deleteattachments' && $date )
{
    check_token("WHMCS.admin.default");
    $sqldate = toMySQLDate($date);
    $result = select_query('tbltickets', '', "date<='" . db_escape_string($sqldate) . "' AND attachment!=''");
    while( $data = mysql_fetch_array($result) )
    {
        $attachments = $data['attachment'];
        $attachments = explode("|", $attachments);
        foreach( $attachments as $filename )
        {
            try
            {
                $file = new WHMCS_File($whmcs->getAttachmentsDir() . $filename);
                $file->delete();
            }
            catch( WHMCS_Exception_File_NotFound $e )
            {
            }
        }
    }
    $result = select_query('tblticketreplies', '', "date<='" . db_escape_string($sqldate) . "' AND attachment!=''");
    while( $data = mysql_fetch_array($result) )
    {
        $attachments = $data['attachment'];
        $attachments = explode("|", $attachments);
        foreach( $attachments as $filename )
        {
            try
            {
                $file = new WHMCS_File($whmcs->getAttachmentsDir() . $filename);
                $file->delete();
            }
            catch( WHMCS_Exception_File_NotFound $e )
            {
            }
        }
    }
    logActivity("Cleanup Operation: Pruned Attachments Uploaded before " . $date);
    infoBox($aInt->lang('system', 'cleanupsuccess'), $aInt->lang('system', 'deleteattachinfo') . " " . $date);
}
$attachmentssize = $attachmentscount = 0;
$dh = opendir($attachments_dir);
while( false !== ($file = readdir($dh)) )
{
    $fullpath = $attachments_dir . $file;
    if( is_file($fullpath) && $file != "index.php" )
    {
        $attachmentssize += filesize($fullpath);
        $attachmentscount++;
    }
}
closedir($dh);
$attachmentssize /= 1024 * 1024;
$attachmentssize = round($attachmentssize, 2);
echo $infobox;
echo "\n<p>";
echo $aInt->lang('system', 'cleanupdescription');
echo "</p>\n\n<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\"><tr><td width=\"49%\">\n\n<div class=\"contentbox\">\n<form method=\"post\" action=\"";
echo $whmcs->getPhpSelf();
echo "\"><input type=\"hidden\" name=\"action\" value=\"cleargatewaylog\" />\n<b>";
echo $aInt->lang('system', 'emptygwlog');
echo "</b> <input id=\"system-empty-gateway-log\" type=\"submit\" value=\" ";
echo $aInt->lang('global', 'go');
echo " &raquo; \" class=\"button\" />\n</form>\n</div>\n\n<br>\n\n<div class=\"contentbox\">\n<form method=\"post\" action=\"";
echo $whmcs->getPhpSelf();
echo "\"><input type=\"hidden\" name=\"action\" value=\"clearmailimportlog\" />\n<b>";
echo $aInt->lang('system', 'emptytmlog');
echo "</b> <input id=\"system-empty-ticket-mail-input-log\"  type=\"submit\" value=\" ";
echo $aInt->lang('global', 'go');
echo " &raquo; \" class=\"button\" />\n</form>\n</div>\n\n</td><td width=\"2%\"></td><td width=\"49%\">\n\n<div class=\"contentbox\">\n<form method=\"post\" action=\"";
echo $whmcs->getPhpSelf();
echo "\"><input type=\"hidden\" name=\"action\" value=\"clearwhoislog\" />\n<b>";
echo $aInt->lang('system', 'emptywllog');
echo "</b> <input id=\"system-empty-whois-lookup-log\"  type=\"submit\" value=\" ";
echo $aInt->lang('global', 'go');
echo " &raquo; \" class=\"button\" />\n</form>\n</div>\n\n<br>\n\n<div class=\"contentbox\">\n<form method=\"post\" action=\"";
echo $whmcs->getPhpSelf();
echo "\"><input type=\"hidden\" name=\"action\" value=\"emptytemplatecache\" />\n<b>";
echo $aInt->lang('system', 'emptytc');
echo "</b> <input id=\"system-empty-template-cache\"  type=\"submit\" value=\" ";
echo $aInt->lang('global', 'go');
echo " &raquo; \" class=\"button\" />\n</form>\n</div>\n\n</td></tr></table>\n\n<br>\n\n<div class=\"contentbox\">\n<form method=\"post\" action=\"";
echo $whmcs->getPhpSelf();
echo "?action=pruneclientactivity\">\n<b>";
echo $aInt->lang('system', 'prunecal');
echo "</b><br>\n";
$result = select_query('tblactivitylog', "COUNT(*)", "userid>0");
$data = mysql_fetch_array($result);
$num_rows = $data[0];
echo $aInt->lang('system', 'totallogentries') . ": <b>" . $num_rows . "</b>";
echo "<br>\n";
echo $aInt->lang('system', 'deleteentriesbefore');
echo ": <input id=\"system-empty-activity-log-date\"  type=\"text\" name=\"date\" class=\"datepick\"> <input id=\"system-empty-activity-log-delete\"  type=\"submit\" value=\"";
echo $aInt->lang('global', 'delete');
echo "\" class=\"button\"></form>\n</div>\n\n<br>\n\n<div class=\"contentbox\">\n<form method=\"post\" action=\"";
echo $whmcs->getPhpSelf();
echo "?action=deletemessages\">\n<b>";
echo $aInt->lang('system', 'prunese');
echo "</b><br>\n";
$result = select_query('tblemails', "COUNT(*)", '');
$data = mysql_fetch_array($result);
$num_rows = $data[0];
echo $aInt->lang('system', 'totalsavedemails') . ": <b>" . $num_rows . "</b>";
echo "<br>\n";
echo $aInt->lang('system', 'deletemailsbefore');
echo ": <input id=\"system-empty-saved-emails-date\" type=\"text\" name=\"date\" class=\"datepick\"> <input id=\"system-empty-saved-emails-delete\" type=\"submit\" value=\"";
echo $aInt->lang('global', 'delete');
echo "\" class=\"button\"></form>\n</div>\n\n<br>\n\n<div class=\"contentbox\">\n<form method=\"post\" action=\"";
echo $whmcs->getPhpSelf();
echo "?action=deleteattachments\">\n<b>";
echo $aInt->lang('system', 'pruneoa');
echo "</b><br>\n";
echo $aInt->lang('system', 'nosavedattachments') . ": <b>" . $attachmentscount . "</b><br>" . $aInt->lang('system', 'filesizesavedatt') . ": <b>" . $attachmentssize . " " . $aInt->lang('fields', 'mb') . "</b>";
echo "<br>\n";
echo $aInt->lang('system', 'deleteattachbefore');
echo ": <input id=\"system-empty-atachements-date\" type=\"text\" name=\"date\" class=\"datepick\"> <input id=\"system-empty-atachments-delete\" type=\"submit\" value=\"";
echo $aInt->lang('global', 'delete');
echo "\" class=\"button\"></form>\n</div>\n\n";
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->display();