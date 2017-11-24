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
$aInt = new WHMCS_Admin("My Account", false);
$aInt->title = $aInt->lang('global', 'myaccount');
$aInt->sidebar = 'config';
$aInt->icon = 'home';
$aInt->requiredFiles(array( 'ticketfunctions' ));
$action = $whmcs->get_req_var('action');
$errormessage = '';
$twofa = new WHMCS_2FA();
$twofa->setAdminID($_SESSION['adminid']);
if( $whmcs->get_req_var('2fasetup') )
{
    if( !$twofa->isActiveAdmins() )
    {
        exit( "Access denied" );
    }
    ob_start();
    if( $twofa->isEnabled() )
    {
        echo "<div class=\"content\"><div style=\"padding:15px;\">";
        $disabled = $incorrect = false;
        if( $password = $whmcs->get_req_var('pwverify') )
        {
            $auth = new WHMCS_Auth();
            $auth->getInfobyID($_SESSION['adminid']);
            if( $auth->comparePassword($password) )
            {
                $twofa->disableUser();
                $disabled = true;
            }
            else
            {
                $incorrect = true;
            }
        }
        echo "<h2>" . $aInt->lang('twofa', 'disable') . "</h2>";
        if( !$disabled )
        {
            echo "<p>" . $aInt->lang('twofa', 'disableintro') . "</p>";
            if( $incorrect )
            {
                echo "<div class=\"errorbox\"><strong>Password Incorrect</strong><br />Please try again...</div>";
            }
            echo "<form onsubmit=\"dialogSubmit();return false\"><input type=\"hidden\" name=\"2fasetup\" value=\"1\" /><p align=\"center\">" . $aInt->lang('fields', 'password') . ": <input type=\"password\" name=\"pwverify\" value=\"\" size=\"20\" /><p><p align=\"center\"><input type=\"button\" value=\"" . $aInt->lang('global', 'disable') . "\" class=\"btn\" onclick=\"dialogSubmit()\" /></p></form>";
        }
        else
        {
            echo "<p>" . $aInt->lang('twofa', 'disabledconfirmation') . "</p><p align=\"center\"><input type=\"button\" value=\"" . $aInt->lang('global', 'close') . "\" onclick=\"window.location='myaccount.php'\" /></p>";
        }
        echo "<script type=\"text/javascript\">\n\$(\"#admindialogcont input:password:visible:first\").focus();\n</script>\n</div></div>";
    }
    else
    {
        $modules = $twofa->getAvailableModules();
        if( isset($module) && in_array($module, $modules) )
        {
            $output = $twofa->moduleCall('activate', $module);
            if( is_array($output) && isset($output['completed']) )
            {
                $msg = isset($output['msg']) ? $output['msg'] : '';
                $settings = isset($output['settings']) ? $output['settings'] : array(  );
                $backupcode = $twofa->activateUser($module, $settings);
                $output = '';
                if( $backupcode )
                {
                    $output = "<div align=\"center\"><h2>" . $aInt->lang('twofa', 'activationcomplete') . "</h2>";
                    if( $msg )
                    {
                        $output .= "<div style=\"margin:20px;padding:10px;background-color:#f7f7f7;border:1px dashed #cccccc;text-align:center;\">" . $msg . "</div>";
                    }
                    $output .= "<h2>" . $aInt->lang('twofa', 'backupcodeis') . ":</h2><div style=\"margin:20px auto;padding:10px;width:280px;background-color:#F2D4CE;border:1px dashed #AE432E;text-align:center;font-size:20px;\">" . $backupcode . "</div><p>" . $aInt->lang('twofa', 'backupcodeexpl') . "</p>";
                    $output .= "<p><input type=\"button\" value=\"" . $aInt->lang('global', 'close') . "\" onclick=\"window.location='myaccount.php'\" /></p></div>";
                }
                else
                {
                    $output = $aInt->lang('twofa', 'activationerror');
                }
            }
            if( !$output )
            {
                echo "<div class=\"content\"><div style=\"padding:15px;\">";
                echo $aInt->lang('twofa', 'generalerror');
                echo "</div></div>";
            }
            else
            {
                echo "<div class=\"content\"><div style=\"padding:15px;\">";
                echo $output;
                echo "</div></div>";
            }
        }
        else
        {
            echo "<div class=\"content\"><div style=\"padding:15px;\">";
            echo "<h2>" . $aInt->lang('twofa', 'enable') . "</h2>";
            if( $twofa->isForced() )
            {
                echo "<div class=\"infobox\">" . $aInt->lang('twofa', 'enforced') . "</div>";
            }
            echo "<p>" . $aInt->lang('twofa', 'activateintro') . "</p>\n<form><input type=\"hidden\" name=\"2fasetup\" value=\"1\" />";
            if( 1 < count($modules) )
            {
                echo "<p>" . $aInt->lang('twofa', 'choose') . "</p>";
                $mod = new WHMCS_Module('security');
                $first = true;
                foreach( $modules as $module )
                {
                    $mod->load($module);
                    $configarray = $mod->call('config');
                    echo " &nbsp;&nbsp;&nbsp;&nbsp; <label><input type=\"radio\" name=\"module\" value=\"" . $module . "\"" . ($first ? " checked" : '') . " /> " . (isset($configarray['FriendlyName']['Value']) ? $configarray['FriendlyName']['Value'] : ucfirst($module)) . "</label><br />";
                    $first = false;
                }
            }
            else
            {
                echo "<input type=\"hidden\" name=\"module\" value=\"" . $modules[0] . "\" />";
            }
            echo "<p align=\"center\"><br /><input type=\"button\" value=\"" . $aInt->lang('twofa', 'getstarted') . " &raquo;\" onclick=\"dialogSubmit()\" class=\"btn btn-primary\" /></form>";
            echo "</div></div>";
        }
    }
    echo "<script type=\"text/javascript\">\n\$(\"#admindialogcont input:text:visible:first\").focus();\n</script>";
    $content = ob_get_contents();
    ob_end_clean();
    echo $content;
    exit();
}
$file = new WHMCS_File_Directory($whmcs->get_admin_folder_name() . DIRECTORY_SEPARATOR . 'templates');
$adminTemplates = $file->getSubdirectories();
if( $action == 'save' )
{
    check_token("WHMCS.admin.default");
    if( defined('DEMO_MODE') )
    {
        redir("demo=1");
    }
    $newPassword = $whmcs->get_req_var('password');
    $newPassword = $newPassword ? trim($newPassword) : '';
    $passwordRetype = $whmcs->get_req_var('password2');
    $passwordRetype = $passwordRetype ? trim($passwordRetype) : '';
    if( $newPassword != $passwordRetype )
    {
        $errormessage = $aInt->lang('administrators', 'pwmatcherror');
        $action = 'edit';
    }
    else
    {
        if( !in_array($template, $adminTemplates) )
        {
            $template = $adminTemplates[0];
        }
        $language = $whmcs->validateLanguage($language, true);
        update_query('tbladmins', array( 'firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'signature' => $signature, 'notes' => $notes, 'template' => $template, 'language' => $language, 'ticketnotifications' => implode(',', $ticketnotify) ), array( 'id' => $_SESSION['adminid'] ));
        unset($_SESSION['adminlang']);
        logActivity("Administrator Account Modified (" . $firstname . " " . $lastname . ")");
        if( $newPassword )
        {
            $auth = new WHMCS_Auth();
            $auth->getInfobyID(WHMCS_Session::get('adminid'));
            if( $auth->generateNewPasswordHashAndStore($newPassword) )
            {
                $auth->generateNewPasswordHashAndStoreForApi(md5($newPassword));
                $auth->setSessionVars();
            }
        }
        redir("success=true");
    }
}
WHMCS_Session::release();
$result = select_query('tbladmins', "tbladmins.*,tbladminroles.name", array( "tbladmins.id" => $_SESSION['adminid'] ), '', '', '', "tbladminroles ON tbladminroles.id=tbladmins.roleid");
$data = mysql_fetch_array($result);
if( !$errormessage )
{
    $firstname = $data['firstname'];
    $lastname = $data['lastname'];
    $email = $data['email'];
    $signature = $data['signature'];
    $notes = $data['notes'];
    $template = $data['template'];
    $language = $data['language'];
    $ticketnotifications = $data['ticketnotifications'];
    $ticketnotify = explode(',', $ticketnotifications);
}
else
{
    if( !is_array($ticketnotify) )
    {
        $ticketnotify = array(  );
    }
}
$username = $data['username'];
$adminrole = $data['name'];
ob_start();
$aInt->dialog('2fasetup');
$infobox = '';
if( defined('DEMO_MODE') )
{
    infoBox("Demo Mode", "Actions on this page are unavailable while in demo mode. Changes will not be saved.");
}
if( $whmcs->get_req_var('success') )
{
    infoBox($aInt->lang('administrators', 'changesuccess'), $aInt->lang('administrators', 'changesuccessinfo2'));
}
if( $errormessage )
{
    infoBox($aInt->lang('global', 'validationerror'), $errormessage);
}
echo $infobox;
echo "\n<form method=\"post\" action=\"";
echo $whmcs->getPhpSelf();
echo "?action=save\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"20%\" class=\"fieldlabel\">";
echo $aInt->lang('fields', 'username');
echo "</td><td class=\"fieldarea\"><b>";
echo $username;
echo "</b></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('administrators', 'role');
echo "</td><td class=\"fieldarea\"><strong>";
echo $adminrole;
echo "</strong></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'firstname');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"firstname\" size=\"30\" value=\"";
echo $firstname;
echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'lastname');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"lastname\" size=\"30\" value=\"";
echo $lastname;
echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'email');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"email\" size=\"50\" value=\"";
echo $email;
echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('administrators', 'ticketnotifications');
echo "</td><td class=\"fieldarea\">";
$nodepartments = true;
$supportdepts = getAdminDepartmentAssignments();
foreach( $supportdepts as $deptid )
{
    $deptname = get_query_val('tblticketdepartments', 'name', array( 'id' => $deptid ));
    if( $deptname )
    {
        echo "<label><input type=\"checkbox\" name=\"ticketnotify[]\" value=\"" . $deptid . "\"" . (in_array($deptid, $ticketnotify) ? " checked" : '') . " /> " . $deptname . "</label><br />";
        $nodepartments = false;
    }
}
if( $nodepartments )
{
    echo $aInt->lang('administrators', 'nosupportdeptsassigned');
}
echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('administrators', 'supportsig');
echo "</td><td class=\"fieldarea\"><textarea name=\"signature\" cols=80 rows=4>";
echo $signature;
echo "</textarea></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('global', 'mynotes');
echo "</td><td class=\"fieldarea\"><textarea name=\"notes\" cols=80 rows=4>";
echo $notes;
echo "</textarea></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'template');
echo "</td><td class=\"fieldarea\"><select name=\"template\">";
foreach( $adminTemplates as $temp )
{
    echo "<option value=\"" . $temp . "\"";
    if( $temp == $template )
    {
        echo " selected";
    }
    echo ">" . ucfirst($temp) . "</option>";
}
echo "</select></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('global', 'language');
echo "</td><td class=\"fieldarea\"><select name=\"language\">";
foreach( $whmcs->getValidLanguages(true) as $lang )
{
    echo "<option value=\"" . $lang . "\"";
    if( $lang == $language )
    {
        echo " selected=\"selected\"";
    }
    echo ">" . ucfirst($lang) . "</option>";
}
echo "</select></td></tr>\n";
if( $twofa->isActiveAdmins() )
{
    echo "<tr><td class=\"fieldlabel\">";
    echo $aInt->lang('twofa', 'title');
    echo "</td><td class=\"fieldarea\">";
    echo $twofa->isEnabled() ? "<input type=\"button\" value=\"" . $aInt->lang('twofa', 'disableclickhere') . "\" onclick=\"dialogOpen()\" class=\"btn btn-danger\" />" : "<input type=\"button\" value=\"" . $aInt->lang('twofa', 'enableclickhere') . "\" onclick=\"dialogOpen()\" class=\"btn btn-success\" />";
    echo "</td></td></tr>\n";
}
echo "</table>\n\n<p>";
echo $aInt->lang('administrators', 'entertochange');
echo "</p>\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"20%\" class=\"fieldlabel\">";
echo $aInt->lang('fields', 'password');
echo "</td><td class=\"fieldarea\"><input type=\"password\" name=\"password\" size=\"25\"></td></tr>\n<tr><td class=\"fieldlabel\" >";
echo $aInt->lang('fields', 'confpassword');
echo "</td><td class=\"fieldarea\"><input type=\"password\" name=\"password2\" size=\"25\"></td></tr>\n</table>\n\n<p align=\"center\"><input type=\"submit\" value=\"";
echo $aInt->lang('global', 'savechanges');
echo "\" class=\"button\"></p>\n\n</form>\n\n";
if( $whmcs->get_req_var('2faenforce') )
{
    $aInt->jquerycode = "dialogOpen();";
}
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->display();