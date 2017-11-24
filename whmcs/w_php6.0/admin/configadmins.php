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
$aInt = new WHMCS_Admin("Configure Administrators", false);
$aInt->title = $aInt->lang('administrators', 'title');
$aInt->sidebar = 'config';
$aInt->icon = 'admins';
$aInt->helplink = 'Administrators';
$validate = new WHMCS_Validate();
$file = new WHMCS_File_Directory($whmcs->get_admin_folder_name() . DIRECTORY_SEPARATOR . 'templates');
$adminTemplates = $file->getSubdirectories();
if( $action == 'save' )
{
    check_token("WHMCS.admin.default");
    if( defined('DEMO_MODE') )
    {
        redir("demo=1");
    }
    $auth = new WHMCS_Auth();
    $auth->getInfobyID(WHMCS_Session::get('adminid'));
    if( !$auth->comparePassword($whmcs->get_req_var('confirmpassword')) )
    {
        $_ADMINLANG['administrators']['confirmexistingpw'] = "You must confirm your existing administrator password";
        $validate->addError(array( 'administrators', 'confirmexistingpw' ));
    }
    else
    {
        $validate->validate('required', 'firstname', array( 'administrators', 'namerequired' ));
        if( $validate->validate('required', 'email', array( 'administrators', 'emailerror' )) )
        {
            $validate->validate('email', 'email', array( 'administrators', 'emailinvalid' ));
        }
        if( $validate->validate('required', 'username', array( 'administrators', 'usererror' )) )
        {
            $existingid = get_query_val('tbladmins', 'id', array( 'username' => $username ));
            if( !$id && $existingid || $id && $existingid && $id != $existingid )
            {
                $validate->addError(array( 'administrators', 'userexists' ));
            }
        }
        if( !$id && $validate->validate('required', 'password', array( 'administrators', 'pwerror' )) )
        {
            $validate->validate('match_value', 'password', array( 'administrators', 'pwmatcherror' ), 'password2');
        }
    }
    if( $validate->hasErrors() )
    {
        $action = 'manage';
    }
    else
    {
        $supportdepts = implode(',', $deptids);
        $ticketnotify = implode(',', $ticketnotify);
        $disabled = $disabled == 'on' ? 1 : 0;
        if( !in_array($template, $adminTemplates) )
        {
            $template = $adminTemplates[0];
        }
        $language = $whmcs->validateLanguage($language, true);
        $adminDetails = array( 'roleid' => $roleid, 'username' => $username, 'firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'signature' => $signature, 'disabled' => $disabled, 'notes' => $notes, 'template' => $template, 'language' => $language, 'supportdepts' => $supportdepts, 'ticketnotifications' => $ticketnotify );
        $userProvidedPassword = $whmcs->get_req_var('password');
        if( $id )
        {
            update_query('tbladmins', $adminDetails, array( 'id' => $id ));
            $adminToUpdate = new WHMCS_Auth();
            $adminToUpdate->getInfobyID($id, null, false);
            if( $adminToUpdate->getAdminID() && $userProvidedPassword && ($userProvidedPassword = trim($userProvidedPassword)) )
            {
                if( $adminToUpdate->generateNewPasswordHashAndStore($userProvidedPassword) )
                {
                    $adminToUpdate->generateNewPasswordHashAndStoreForApi(md5($userProvidedPassword));
                    if( $id == $auth->getAdminID() )
                    {
                        $adminToUpdate->setSessionVars();
                    }
                }
                else
                {
                    logActivity(sprintf("Failed to update password hash for admin %s.", $adminDetails['username']));
                }
            }
            redir("saved=true");
        }
        else
        {
            $adminDetails['password'] = crypt_random_string(21);
            insert_query('tbladmins', $adminDetails);
            $newAdmin = new WHMCS_Auth();
            $newAdmin->getInfobyUsername($adminDetails['username'], null, false);
            $userProvidedPassword = trim($userProvidedPassword);
            if( $newAdmin->getAdminID() && $userProvidedPassword && $newAdmin->generateNewPasswordHashAndStore($userProvidedPassword) )
            {
                $newAdmin->generateNewPasswordHashAndStoreForApi(md5($userProvidedPassword));
            }
            else
            {
                logActivity(sprintf("Failed to assign password hash for new admin %s." . " Account will stay locked until properly reset.", $adminDetails['username']));
            }
            redir("added=true");
        }
        exit();
    }
}
if( $action == 'delete' )
{
    check_token("WHMCS.admin.default");
    if( defined('DEMO_MODE') )
    {
        redir("demo=1");
    }
    delete_query('tbladmins', array( 'id' => $id ));
    redir("deleted=true");
}
ob_start();
if( $action == '' )
{
    $infobox = '';
    if( defined('DEMO_MODE') )
    {
        infoBox("Demo Mode", "Actions on this page are unavailable while in demo mode. Changes will not be saved.");
    }
    if( $saved )
    {
        infoBox($aInt->lang('administrators', 'changesuccess'), $aInt->lang('administrators', 'changesuccessinfo'));
    }
    else
    {
        if( $added )
        {
            infoBox($aInt->lang('administrators', 'addsuccess'), $aInt->lang('administrators', 'addsuccessinfo'));
        }
        else
        {
            if( $deleted )
            {
                infoBox($aInt->lang('administrators', 'deletesuccess'), $aInt->lang('administrators', 'deletesuccessinfo'));
            }
        }
    }
    echo $infobox;
    $data = get_query_vals('tbladmins', "COUNT(id),id", array( 'roleid' => '1' ));
    $numrows = $data[0];
    $onlyadminid = $numrows == '1' ? $data['id'] : 0;
    $jscode = "function doDelete(id) {\n    if(id != " . $onlyadminid . "){\n        if (confirm(\"" . $aInt->lang('administrators', 'deletesure', 1) . "\")) {\n        window.location='" . $_SERVER['PHP_SELF'] . "?action=delete&id='+id+'" . generate_token('link') . "';\n        }\n    } else alert(\"" . $aInt->lang('administrators', 'deleteonlyadmin', 1) . "\");\n    }";
    echo "<p>";
    echo $aInt->lang('administrators', 'description');
    echo "</p>\n<p><b>";
    echo $aInt->lang('fields', 'options');
    echo ":</b> <a href=\"configadmins.php?action=manage\">";
    echo $aInt->lang('administrators', 'addnew');
    echo "</a></p>\n\n";
    echo "<h2>" . $aInt->lang('administrators', 'active') . " </h2>";
    $aInt->sortableTableInit('nopagination');
    $result = select_query('tbladmins', "tbladmins.*,tbladminroles.name", array( 'disabled' => '0' ), "firstname` ASC,`lastname", 'ASC', '', "tbladminroles ON tbladmins.roleid=tbladminroles.id");
    while( $data = mysql_fetch_array($result) )
    {
        $departments = $deptnames = array(  );
        $supportdepts = db_build_in_array(explode(',', $data['supportdepts']));
        if( $supportdepts )
        {
            $resultdeptids = select_query('tblticketdepartments', 'name', "id IN (" . $supportdepts . ")");
            while( $data_resultdeptids = mysql_fetch_array($resultdeptids) )
            {
                $deptnames[] = $data_resultdeptids[0];
            }
        }
        if( !count($deptnames) )
        {
            $deptnames[] = $aInt->lang('global', 'none');
        }
        $tabledata[] = array( $data['firstname'] . " " . $data['lastname'], "<a href=\"mailto:" . $data['email'] . "\">" . $data['email'] . "</a>", $data['username'], $data['name'], implode(", ", $deptnames), "<a href=\"?action=manage&id=" . $data['id'] . "\"><img src=\"images/edit.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"Edit\"></a>", "<a href=\"#\" onClick=\"doDelete('" . $data['id'] . "')\"><img src=\"images/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"Delete\"></a>" );
    }
    echo $aInt->sortableTable(array( $aInt->lang('fields', 'name'), $aInt->lang('fields', 'email'), $aInt->lang('fields', 'username'), $aInt->lang('administrators', 'adminrole'), $aInt->lang('administrators', 'assigneddepts'), '', '' ), $tabledata);
    echo "<h2>" . $aInt->lang('administrators', 'inactive') . " </h2>";
    $tabledata = array(  );
    $result = select_query('tbladmins', "tbladmins.*,tbladminroles.name", array( 'disabled' => '1' ), "firstname` ASC,`lastname", 'ASC', '', "tbladminroles ON tbladmins.roleid=tbladminroles.id");
    while( $data = mysql_fetch_array($result) )
    {
        $departments = $deptnames = array(  );
        $supportdepts = db_build_in_array(explode(',', $data['supportdepts']));
        if( $supportdepts )
        {
            $resultdeptids = select_query('tblticketdepartments', 'name', "id IN (" . $supportdepts . ")");
            while( $data_resultdeptids = mysql_fetch_array($resultdeptids) )
            {
                $deptnames[] = $data_resultdeptids[0];
            }
        }
        if( !count($deptnames) )
        {
            $deptnames[] = $aInt->lang('global', 'none');
        }
        $tabledata[] = array( $data['firstname'] . " " . $data['lastname'], "<a href=\"mailto:" . $data['email'] . "\">" . $data['email'] . "</a>", $data['username'], $data['name'], implode(", ", $deptnames), "<a href=\"?action=manage&id=" . $data['id'] . "\"><img src=\"images/edit.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"Edit\"></a>", "<a href=\"#\" onClick=\"doDelete('" . $data['id'] . "')\"><img src=\"images/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"Delete\"></a>" );
    }
    echo $aInt->sortableTable(array( $aInt->lang('fields', 'name'), $aInt->lang('fields', 'email'), $aInt->lang('fields', 'username'), $aInt->lang('administrators', 'adminrole'), $aInt->lang('administrators', 'assigneddepts'), '', '' ), $tabledata);
}
else
{
    if( $action == 'manage' )
    {
        if( $id )
        {
            $result = select_query('tbladmins', '', array( 'id' => $id ));
            $data = mysql_fetch_array($result);
            $supportdepts = $data['supportdepts'];
            $ticketnotifications = $data['ticketnotifications'];
            $supportdepts = explode(',', $supportdepts);
            $ticketnotify = explode(',', $ticketnotifications);
            if( !$validate->hasErrors() )
            {
                $roleid = $data['roleid'];
                $firstname = $data['firstname'];
                $lastname = $data['lastname'];
                $email = $data['email'];
                $username = $data['username'];
                $signature = $data['signature'];
                $notes = $data['notes'];
                $template = $data['template'];
                $language = $data['language'];
                $disabled = $data['disabled'];
            }
            $numrows = get_query_vals('tbladmins', "COUNT(id)", array( 'roleid' => '1' ));
            $onlyadmin = $numrows == '1' && $roleid == '1' ? true : false;
            $managetitle = $aInt->lang('administrators', 'editadmin');
        }
        else
        {
            $supportdepts = $ticketnotify = array(  );
            $managetitle = $aInt->lang('administrators', 'addadmin');
        }
        $language = $whmcs->validateLanguage($language, true);
        $infobox = '';
        if( defined('DEMO_MODE') )
        {
            infoBox("Demo Mode", "Actions on this page are unavailable while in demo mode. Changes will not be saved.");
        }
        echo $infobox;
        echo "<p><b>" . $managetitle . "</b></p>";
        if( $validate->hasErrors() )
        {
            infoBox($aInt->lang('global', 'validationerror'), $validate->getHTMLErrorOutput(), 'error');
            echo $infobox;
        }
        echo "\n<form method=\"post\" action=\"";
        echo $whmcs->getPhpSelf();
        echo "?action=save&id=";
        echo $id;
        echo "\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"20%\" class=\"fieldlabel\">";
        echo $aInt->lang('administrators', 'role');
        echo "</td><td class=\"fieldarea\"><select name=\"roleid\"";
        if( $onlyadmin )
        {
            echo " disabled";
        }
        echo ">";
        $result = select_query('tbladminroles', '', '', 'name', 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $select_roleid = $data['id'];
            $select_rolename = $data['name'];
            echo "<option value=\"" . $select_roleid . "\"";
            if( $roleid == $select_roleid )
            {
                echo " selected";
            }
            echo ">" . $select_rolename . "</option>";
        }
        echo "</select></td></tr>\n<tr><td class=\"fieldlabel\">";
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
        echo $aInt->lang('fields', 'username');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"username\" size=\"25\" value=\"";
        echo $username;
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'password');
        echo "</td><td class=\"fieldarea\"><input type=\"password\" name=\"password\" size=\"20\">";
        if( $id )
        {
            echo " (" . $aInt->lang('administrators', 'entertochange') . ")";
        }
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'confpassword');
        echo "</td><td class=\"fieldarea\"><input type=\"password\" name=\"password2\" size=\"20\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('administrators', 'assigneddepts');
        echo "</td><td class=\"fieldarea\">";
        $nodepartments = true;
        $result = select_query('tblticketdepartments', '', '', 'order', 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $deptid = $data['id'];
            $deptname = $data['name'];
            echo "<label><input type=\"checkbox\" name=\"deptids[]\" value=\"" . $deptid . "\"";
            if( in_array($deptid, $supportdepts) )
            {
                echo " checked";
            }
            echo "> " . $deptname . "</label> <label><input type=\"checkbox\" name=\"ticketnotify[]\" value=\"" . $deptid . "\"";
            if( in_array($deptid, $ticketnotify) )
            {
                echo " checked";
            }
            echo "> Enable Ticket Notifications</label><br />";
            $nodepartments = false;
        }
        if( $nodepartments )
        {
            echo $aInt->lang('administrators', 'nosupportdepts');
        }
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('administrators', 'supportsig');
        echo "</td><td class=\"fieldarea\"><textarea name=\"signature\" cols=80 rows=4>";
        echo $signature;
        echo "</textarea></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('administrators', 'privatenotes');
        echo "</td><td class=\"fieldarea\"><textarea name=\"notes\" cols=\"80\" rows=\"4\">";
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
        echo "</select></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'disable');
        echo "</td><td class=\"fieldarea\"><label><input type=\"checkbox\" name=\"disabled\"";
        if( $disabled == 1 )
        {
            echo " checked";
        }
        if( $onlyadmin || $id == $_SESSION['adminid'] )
        {
            echo " disabled";
        }
        echo " /> ";
        echo $aInt->lang('administrators', 'disableinfo');
        echo "</label></td></tr>\n</table>\n\n<p>Please confirm your admin password to add or make changes to administrator account details.</p>\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"20%\" class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'confpassword');
        echo "</td><td class=\"fieldarea\"><input type=\"password\" name=\"confirmpassword\" size=\"20\"></td></tr>\n</table>\n\n<p align=\"center\"><input type=\"submit\" value=\"";
        echo $aInt->lang('global', 'savechanges');
        echo "\" class=\"button\"></p>\n\n</form>\n\n";
    }
}
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jscode = $jscode;
$aInt->display();