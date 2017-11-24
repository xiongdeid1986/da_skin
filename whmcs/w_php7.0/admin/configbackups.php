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
$aInt = new WHMCS_Admin("Configure Database Backups");
$aInt->title = $aInt->lang('backups', 'title');
$aInt->sidebar = 'config';
$aInt->icon = 'dbbackups';
$aInt->helplink = 'Backups';
if( $sub == 'save' )
{
    check_token("WHMCS.admin.default");
    $save_arr = array( 'DailyEmailBackup' => $dailyemailbackup, 'FTPBackupHostname' => $ftpbackuphostname, 'FTPBackupPort' => (int) $ftpbackupport, 'FTPBackupUsername' => $ftpbackupusername, 'FTPBackupDestination' => $ftpbackupdestination, 'FTPPassiveMode' => $ftppassivemode );
    $newPassword = trim($whmcs->get_req_var('ftpbackuppassword'));
    $originalPassword = decrypt($CONFIG['FTPBackupPassword']);
    $valueToStore = interpretMaskedPasswordChangeForStorage($newPassword, $originalPassword);
    if( $valueToStore !== false )
    {
        $save_arr['FTPBackupPassword'] = $valueToStore;
    }
    foreach( $save_arr as $k => $v )
    {
        if( !isset($CONFIG[$k]) )
        {
            insert_query('tblconfiguration', array( 'setting' => $k, 'value' => trim($v) ));
        }
        else
        {
            update_query('tblconfiguration', array( 'value' => trim($v) ), array( 'setting' => $k ));
        }
    }
    redir("success=true");
}
if( !isset($CONFIG['FTPBackupPort']) )
{
    insert_query('tblconfiguration', array( 'setting' => 'FTPBackupPort', 'value' => '21' ));
    $CONFIG['FTPBackupPort'] = '21';
}
ob_start();
if( $success )
{
    infoBox($aInt->lang('backups', 'changesuccess'), $aInt->lang('backups', 'changesuccessinfo'));
    echo $infobox;
}
echo "<form method=\"post\" action=\"";
echo $whmcs->getPhpSelf();
echo "?sub=save\">\n\n<p>";
echo $aInt->lang('backups', 'description');
echo "</p>\n\n<p><b>";
echo $aInt->lang('backups', 'dailyemail');
echo "</b></p>\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">";
echo $aInt->lang('fields', 'email');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"dailyemailbackup\" value=\"";
echo $CONFIG['DailyEmailBackup'];
echo "\" size=\"40\"> ";
echo $aInt->lang('backups', 'emailinfo');
echo " (";
echo $aInt->lang('backups', 'blanktodisable');
echo ")</td></tr>\n</table>\n\n<p><b>";
echo $aInt->lang('backups', 'dailyftp');
echo "</b></p>\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\"  class=\"fieldlabel\">";
echo $aInt->lang('backups', 'ftphost');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"ftpbackuphostname\" value=\"";
echo $CONFIG['FTPBackupHostname'];
echo "\" size=\"30\"> ";
echo $aInt->lang('backups', 'hostnameinfo');
echo " (";
echo $aInt->lang('backups', 'blanktodisable');
echo ")</td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('backups', 'ftpport');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"ftpbackupport\" value=\"";
echo $CONFIG['FTPBackupPort'];
echo "\" size=\"6\"></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('backups', 'ftpuser');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"ftpbackupusername\" value=\"";
echo $CONFIG['FTPBackupUsername'];
echo "\" size=\"30\"></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('backups', 'ftppass');
echo "</td><td class=\"fieldarea\"><input type=\"password\" name=\"ftpbackuppassword\" value=\"";
echo replacePasswordWithMasks(decrypt($CONFIG['FTPBackupPassword']));
echo "\" size=\"30\"></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('backups', 'ftppath');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"ftpbackupdestination\" value=\"";
echo $CONFIG['FTPBackupDestination'];
echo "\" size=\"30\"> ";
echo $aInt->lang('backups', 'relativepath');
echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('backups', 'ftppassivemode');
echo "</td><td class=\"fieldarea\"><label><input type=\"checkbox\" name=\"ftppassivemode\"";
if( $CONFIG['FTPPassiveMode'] )
{
    echo " checked";
}
echo " /> ";
echo $aInt->lang('global', 'ticktoenable');
echo "</label></td></tr>\n</table>\n\n<p align=\"center\"><input type=\"submit\" value=\"";
echo $aInt->lang('global', 'savechanges');
echo "\" class=\"button\"></p>\n\n</form>\n\n";
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->display();