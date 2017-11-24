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
$aInt->title = $aInt->lang('supportticketdepts', 'supportticketdeptstitle');
$aInt->sidebar = 'config';
$aInt->icon = 'logs';
$aInt->helplink = "Support Departments";
if( $sub == 'add' )
{
    check_token("WHMCS.admin.default");
    if( $email == '' )
    {
        infoBox($aInt->lang('global', 'validationerror'), $aInt->lang('supportticketdepts', 'emailreqdfordept'));
        $action = 'add';
    }
    if( $name == '' )
    {
        infoBox($aInt->lang('global', 'validationerror'), $aInt->lang('supportticketdepts', 'namereqdfordept'));
        $action = 'add';
    }
    if( !$infobox )
    {
        $result = select_query('tblticketdepartments', '', '', 'order', 'DESC');
        $data = mysql_fetch_array($result);
        $order = $data['order'];
        $order++;
        $id = insert_query('tblticketdepartments', array( 'name' => $name, 'description' => WHMCS_Input_Sanitize::decode($description), 'email' => trim($email), 'clientsonly' => $clientsonly, 'piperepliesonly' => $piperepliesonly, 'noautoresponder' => $noautoresponder, 'hidden' => $hidden, 'order' => $order, 'host' => trim($host), 'port' => trim($port), 'login' => trim($login), 'password' => encrypt(trim(WHMCS_Input_Sanitize::decode($password))) ));
        $result = select_query('tbladmins', 'id,supportdepts', array( 'disabled' => '0' ));
        while( $data = mysql_fetch_array($result) )
        {
            $deptadminid = $data[0];
            $supportdepts = $data[1];
            $supportdepts = explode(',', $supportdepts);
            if( in_array($deptadminid, $admins) )
            {
                if( !in_array($id, $supportdepts) )
                {
                    $supportdepts[] = $id;
                }
            }
            else
            {
                if( in_array($id, $supportdepts) )
                {
                    $supportdepts = array_diff($supportdepts, array( $id ));
                }
            }
            update_query('tbladmins', array( 'supportdepts' => implode(',', $supportdepts) ), array( 'id' => $deptadminid ));
        }
        redir("createsuccess=1");
    }
}
if( $sub == 'save' )
{
    check_token("WHMCS.admin.default");
    if( $email == '' )
    {
        infoBox($aInt->lang('global', 'validationerror'), $aInt->lang('supportticketdepts', 'emailreqdfordept'));
        $action = 'edit';
    }
    if( $name == '' )
    {
        infoBox($aInt->lang('global', 'validationerror'), $aInt->lang('supportticketdepts', 'namereqdfordept'));
        $action = 'edit';
    }
    if( !$infobox )
    {
        $saveData = array( 'name' => $name, 'description' => WHMCS_Input_Sanitize::decode($description), 'email' => trim($email), 'clientsonly' => $clientsonly, 'piperepliesonly' => $piperepliesonly, 'noautoresponder' => $noautoresponder, 'hidden' => $hidden, 'host' => trim($host), 'port' => trim($port), 'login' => trim($login) );
        $newPassword = trim($whmcs->get_req_var('password'));
        $originalPassword = decrypt(get_query_val('tblticketdepartments', 'password', array( 'id' => $id )));
        $valueToStore = interpretMaskedPasswordChangeForStorage($newPassword, $originalPassword);
        if( $valueToStore !== false )
        {
            $saveData['password'] = $valueToStore;
        }
        update_query('tblticketdepartments', $saveData, array( 'id' => $id ));
        $result = select_query('tbladmins', 'id,supportdepts', '');
        while( $data = mysql_fetch_array($result) )
        {
            $deptadminid = $data[0];
            $supportdepts = $data[1];
            $supportdepts = explode(',', $supportdepts);
            if( in_array($deptadminid, $admins) )
            {
                if( !in_array($id, $supportdepts) )
                {
                    $supportdepts[] = $id;
                }
            }
            else
            {
                if( in_array($id, $supportdepts) )
                {
                    $supportdepts = array_diff($supportdepts, array( $id ));
                }
            }
            update_query('tbladmins', array( 'supportdepts' => implode(',', $supportdepts) ), array( 'id' => $deptadminid ));
        }
        if( $customfieldname )
        {
            foreach( $customfieldname as $fid => $value )
            {
                update_query('tblcustomfields', array( 'fieldname' => $value, 'fieldtype' => $customfieldtype[$fid], 'description' => $customfielddesc[$fid], 'fieldoptions' => $customfieldoptions[$fid], 'regexpr' => WHMCS_Input_Sanitize::decode($customfieldregexpr[$fid]), 'adminonly' => $customadminonly[$fid], 'required' => $customrequired[$fid], 'showorder' => $customshoworder[$fid], 'sortorder' => $customsortorder[$fid] ), array( 'id' => $fid ));
            }
        }
        if( $addfieldname )
        {
            insert_query('tblcustomfields', array( 'type' => 'support', 'relid' => $id, 'fieldname' => $addfieldname, 'fieldtype' => $addfieldtype, 'description' => $addcfdesc, 'fieldoptions' => $addfieldoptions, 'regexpr' => WHMCS_Input_Sanitize::decode($addregexpr), 'adminonly' => $addadminonly, 'required' => $addrequired, 'showorder' => $addshoworder, 'sortorder' => $addsortorder ));
        }
        redir("savesuccess=1");
    }
}
if( $sub == 'delete' )
{
    check_token("WHMCS.admin.default");
    $result = select_query('tblticketdepartments', '', array( 'id' => $id ));
    $data = mysql_fetch_array($result);
    $order = $data['order'];
    update_query('tblticketdepartments', array( 'order' => '-1' ), array( "`order`" => $order ));
    delete_query('tblticketdepartments', array( 'id' => $id ));
    $result = select_query('tblticketdepartments', "min(id) as id", array(  ));
    $data = mysql_fetch_array($result);
    $newdeptid = $data['id'];
    update_query('tbltickets', array( 'did' => $newdeptid ), array( 'did' => $id ));
    delete_query('tblcustomfields', array( 'type' => 'support', 'relid' => $id ));
    full_query("DELETE FROM tblcustomfieldsvalues WHERE fieldid NOT IN (SELECT id FROM tblcustomfields)");
    redir("delsuccess=1");
}
if( $sub == 'deletecustomfield' )
{
    check_token("WHMCS.admin.default");
    delete_query('tblcustomfields', array( 'id' => $id ));
    delete_query('tblcustomfieldsvalues', array( 'fieldid' => $id ));
    redir("savesuccess=1");
}
if( $sub == 'moveup' )
{
    check_token("WHMCS.admin.default");
    $result = select_query('tblticketdepartments', '', array( "`order`" => $order ));
    $data = mysql_fetch_array($result);
    $premid = $data['id'];
    $order1 = $order - 1;
    update_query('tblticketdepartments', array( 'order' => $order ), array( "`order`" => $order1 ));
    update_query('tblticketdepartments', array( 'order' => $order1 ), array( 'id' => $premid ));
    redir();
}
if( $sub == 'movedown' )
{
    check_token("WHMCS.admin.default");
    $result = select_query('tblticketdepartments', '', array( "`order`" => $order ));
    $data = mysql_fetch_array($result);
    $premid = $data['id'];
    $order1 = $order + 1;
    update_query('tblticketdepartments', array( 'order' => $order ), array( "`order`" => $order1 ));
    update_query('tblticketdepartments', array( 'order' => $order1 ), array( 'id' => $premid ));
    redir();
}
ob_start();
if( $createsuccess )
{
    infoBox($aInt->lang('supportticketdepts', 'deptaddsuccess'), $aInt->lang('supportticketdepts', 'deptaddsuccessdesc'));
}
if( $savesuccess )
{
    infoBox($aInt->lang('supportticketdepts', 'changessavesuccess'), $aInt->lang('supportticketdepts', 'changessavesuccessdesc'));
}
if( $delsuccess )
{
    infoBox($aInt->lang('global', 'success'), "The selected support department was deleted successfully");
}
echo $infobox;
if( $action == '' )
{
    $aInt->deleteJSConfirm('doDelete', 'supportticketdepts', 'delsuredept', "?sub=delete&id=");
    echo "\n<p>";
    echo $aInt->lang('supportticketdepts', 'supportticketdeptsconfigheredesc');
    echo "</p>\n\n<div class=\"contentbox\">\n";
    echo $aInt->lang('supportticketdepts', 'ticketimportusingef');
    echo ":<br><input type=\"text\" size=\"100\" value=\" | php -q ";
    $pos = strrpos($_SERVER['SCRIPT_FILENAME'], '/');
    $str = substr($_SERVER['SCRIPT_FILENAME'], 0, $pos);
    $pos = strrpos($str, '/');
    $str = substr($str, 0, $pos);
    echo $str;
    echo "/pipe/pipe.php\"><br><b>";
    echo $aInt->lang('global', 'or');
    echo "</b><br>\n";
    echo $aInt->lang('supportticketdepts', 'ticketimportusingpop3imap');
    echo ":<br><input type=\"text\" size=\"100\" value=\"*/5 * * * * php -q ";
    echo $str;
    echo "/pipe/pop.php\">\n</div>\n\n<p><strong>";
    echo $aInt->lang('fields', 'options');
    echo ":</strong> <a href=\"";
    echo $whmcs->getPhpSelf();
    echo "?action=add\">";
    echo $aInt->lang('supportticketdepts', 'addnewdept');
    echo "</a></p>\n\n";
    $result = select_query('tblticketdepartments', '', '', 'order', 'DESC');
    $data = mysql_fetch_array($result);
    $lastorder = $data['order'];
    $aInt->sortableTableInit('nopagination');
    $result = select_query('tblticketdepartments', '', '', 'order', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $id = $data['id'];
        $name = $data['name'];
        $description = $data['description'];
        $email = $data['email'];
        $hidden = $data['hidden'];
        $order = $data['order'];
        if( $hidden == 'on' )
        {
            $hidden = $aInt->lang('global', 'yes');
        }
        else
        {
            $hidden = $aInt->lang('global', 'no');
        }
        if( $order != '1' )
        {
            $moveup = "<a href=\"?sub=moveup&order=" . $order . generate_token('link') . "\"><img src=\"images/moveup.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('products', 'navmoveup') . "\"></a>";
        }
        else
        {
            $moveup = '';
        }
        if( $order != $lastorder )
        {
            $movedown = "<a href=\"?sub=movedown&order=" . $order . generate_token('link') . "\"><img src=\"images/movedown.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('products', 'navmovedown') . "\"></a>";
        }
        else
        {
            $movedown = '';
        }
        $tabledata[] = array( $name, $description, $email, $hidden, $moveup, $movedown, "<a href=\"?action=edit&id=" . $id . "\"><img src=\"images/edit.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'edit') . "\"></a>", "<a href=\"#\" onClick=\"doDelete('" . $id . "');return false\"><img src=\"images/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'delete') . "\"></a>" );
    }
    echo $aInt->sortableTable(array( $aInt->lang('supportticketdepts', 'deptname'), $aInt->lang('fields', 'description'), $aInt->lang('supportticketdepts', 'deptemail'), $aInt->lang('global', 'hidden'), '', '', '', '' ), $tabledata);
}
else
{
    if( $action == 'edit' )
    {
        if( !$infobox )
        {
            $result = select_query('tblticketdepartments', '', array( 'id' => $id ));
            $data = mysql_fetch_array($result);
            $name = $data['name'];
            $description = $data['description'];
            $email = $data['email'];
            $clientsonly = $data['clientsonly'];
            $piperepliesonly = $data['piperepliesonly'];
            $noautoresponder = $data['noautoresponder'];
            $hidden = $data['hidden'];
            $host = $data['host'];
            $port = $data['port'];
            $login = $data['login'];
            $password = decrypt($data['password']);
        }
        $aInt->deleteJSConfirm('deleteField', 'supportticketdepts', 'delsurefielddata', "?sub=deletecustomfield&id=");
        echo "\n<h2>";
        echo $aInt->lang('supportticketdepts', 'editdept');
        echo "</h2>\n\n<form method=\"post\" action=\"";
        echo $whmcs->getPhpSelf();
        echo "?sub=save\">\n<input type=\"hidden\" name=\"id\" value=\"";
        echo $id;
        echo "\">\n\n";
        echo $aInt->Tabs(array( 'Details', "Custom Fields" ));
        echo "\n<div id=\"tab0box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"20%\" class=\"fieldlabel\">";
        echo $aInt->lang('supportticketdepts', 'deptname');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"name\" size=\"25\" value=\"";
        echo $name;
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'description');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"description\" size=\"50\" value=\"";
        echo WHMCS_Input_Sanitize::encode($description);
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('supportticketdepts', 'deptemail');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"email\" size=\"40\" value=\"";
        echo $email;
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('supportticketdepts', 'assignedadmins');
        echo "</td><td class=\"fieldarea\">\n";
        $result = select_query('tbladmins', '', '', 'username', 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $supportdepts = $data['supportdepts'];
            $supportdepts = explode(',', $supportdepts);
            echo "<label><input type=\"checkbox\" name=\"admins[]\" value=\"" . $data['id'] . "\"";
            if( in_array($id, $supportdepts) )
            {
                echo " checked";
            }
            echo " /> ";
            if( $data['disabled'] == 1 )
            {
                echo "<span class=\"disabledtext\">";
            }
            echo $data['username'] . " (" . trim($data['firstname'] . " " . $data['lastname']) . ")";
            if( $data['disabled'] == 1 )
            {
                echo " - " . $aInt->lang('global', 'disabled') . "</span>";
            }
            echo "</label><br />";
        }
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('supportticketdepts', 'clientsonly');
        echo "</td><td class=\"fieldarea\"><label><input type=\"checkbox\" name=\"clientsonly\"";
        if( $clientsonly == 'on' )
        {
            echo " checked";
        }
        echo "> ";
        echo $aInt->lang('supportticketdepts', 'clientsonlydesc');
        echo "</label></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('supportticketdepts', 'piperepliesonly');
        echo "</td><td class=\"fieldarea\"><label><input type=\"checkbox\" name=\"piperepliesonly\"";
        if( $piperepliesonly == 'on' )
        {
            echo " checked";
        }
        echo "> ";
        echo $aInt->lang('supportticketdepts', 'ticketsclientareaonlydesc');
        echo "</label></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('supportticketdepts', 'noautoresponder');
        echo "</td><td class=\"fieldarea\"><label><input type=\"checkbox\" name=\"noautoresponder\"";
        if( $noautoresponder == 'on' )
        {
            echo " checked";
        }
        echo "> ";
        echo $aInt->lang('supportticketdepts', 'noautoresponderdesc');
        echo "</label></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('global', 'hidden');
        echo "?</td><td class=\"fieldarea\"><label><input type=\"checkbox\" name=\"hidden\"";
        if( $hidden == 'on' )
        {
            echo " checked";
        }
        echo "> ";
        echo $aInt->lang('supportticketdepts', 'hiddendesc');
        echo "</label></td></tr>\n</table>\n<p style=\"text-align:left;\"><b>";
        echo $aInt->lang('supportticketdepts', 'pop3importconfigtitle');
        echo "</b> ";
        echo $aInt->lang('supportticketdepts', 'pop3importconfigdesc');
        echo "</p>\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"20%\" class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'hostname');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"host\" size=\"40\" value=\"";
        echo $host;
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('supportticketdepts', 'pop3port');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"port\" size=\"10\" value=\"";
        echo $port;
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('supportticketdepts', 'pop3user');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"login\" size=\"40\" value=\"";
        echo $login;
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('supportticketdepts', 'pop3pass');
        echo "</td><td class=\"fieldarea\"><input type=\"password\" name=\"password\" size=\"20\" value=\"";
        echo replacePasswordWithMasks($password);
        echo "\"></td></tr>\n</table>\n\n  </div>\n</div>\n<div id=\"tab1box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n";
        $result = select_query('tblcustomfields', '', array( 'type' => 'support', 'relid' => $id ), 'id', 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $fid = $data['id'];
            $fieldname = $data['fieldname'];
            $fieldtype = $data['fieldtype'];
            $description = $data['description'];
            $fieldoptions = $data['fieldoptions'];
            $regexpr = $data['regexpr'];
            $adminonly = $data['adminonly'];
            $required = $data['required'];
            $sortorder = $data['sortorder'];
            echo "<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=100 class=\"fieldlabel\">";
            echo $aInt->lang('customfields', 'fieldname');
            echo "</td><td class=\"fieldarea\"><table width=\"98%\" cellspacing=\"0\" cellpadding=\"0\"><tr><td><input type=\"text\" name=\"customfieldname[";
            echo $fid;
            echo "]\" value=\"";
            echo $fieldname;
            echo "\" size=\"30\"></td><td align=\"right\">";
            echo $aInt->lang('customfields', 'order');
            echo "<input type=\"text\" name=\"customsortorder[";
            echo $fid;
            echo "]\" value=\"";
            echo $sortorder;
            echo "\" size=\"5\"></td></tr></table></td></tr>\n<tr><td class=\"fieldlabel\">";
            echo $aInt->lang('customfields', 'fieldtype');
            echo "</td><td class=\"fieldarea\"><select name=\"customfieldtype[";
            echo $fid;
            echo "]\">\n<option value=\"text\"";
            if( $fieldtype == 'text' )
            {
                echo " selected";
            }
            echo ">";
            echo $aInt->lang('customfields', 'typetextbox');
            echo "</option>\n<option value=\"link\"";
            if( $fieldtype == 'link' )
            {
                echo " selected";
            }
            echo ">";
            echo $aInt->lang('customfields', 'typelink');
            echo "</option>\n<option value=\"password\"";
            if( $fieldtype == 'password' )
            {
                echo " selected";
            }
            echo ">";
            echo $aInt->lang('customfields', 'typepassword');
            echo "</option>\n<option value=\"dropdown\"";
            if( $fieldtype == 'dropdown' )
            {
                echo " selected";
            }
            echo ">";
            echo $aInt->lang('customfields', 'typedropdown');
            echo "</option>\n<option value=\"tickbox\"";
            if( $fieldtype == 'tickbox' )
            {
                echo " selected";
            }
            echo ">";
            echo $aInt->lang('customfields', 'typetickbox');
            echo "</option>\n<option value=\"textarea\"";
            if( $fieldtype == 'textarea' )
            {
                echo " selected";
            }
            echo ">";
            echo $aInt->lang('customfields', 'typetextarea');
            echo "</option>\n</select></td></tr>\n<tr><td class=\"fieldlabel\">";
            echo $aInt->lang('fields', 'description');
            echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"customfielddesc[";
            echo $fid;
            echo "]\" value=\"";
            echo $description;
            echo "\" size=\"60\"> ";
            echo $aInt->lang('customfields', 'descriptioninfo');
            echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
            echo $aInt->lang('customfields', 'validation');
            echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"customfieldregexpr[";
            echo $fid;
            echo "]\" value=\"";
            echo WHMCS_Input_Sanitize::encode($regexpr);
            echo "\" size=\"60\"> ";
            echo $aInt->lang('customfields', 'validationinfo');
            echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
            echo $aInt->lang('customfields', 'selectoptions');
            echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"customfieldoptions[";
            echo $fid;
            echo "]\" value=\"";
            echo $fieldoptions;
            echo "\" size=\"60\"> ";
            echo $aInt->lang('customfields', 'selectoptionsinfo');
            echo "</td></tr>\n<tr><td class=\"fieldlabel\"></td><td class=\"fieldarea\"><table width=\"98%\" cellspacing=\"0\" cellpadding=\"0\"><tr><td><label><input type=\"checkbox\" name=\"customadminonly[";
            echo $fid;
            echo "]\"";
            if( $adminonly == 'on' )
            {
                echo " checked";
            }
            echo "> ";
            echo $aInt->lang('customfields', 'adminonly');
            echo "</label> <label><input type=\"checkbox\" name=\"customrequired[";
            echo $fid;
            echo "]\"";
            if( $required == 'on' )
            {
                echo " checked";
            }
            echo "> ";
            echo $aInt->lang('customfields', 'requiredfield');
            echo "</label></td><td align=\"right\"><a href=\"#\" onClick=\"deleteField('";
            echo $fid;
            echo "');return false\">";
            echo $aInt->lang('customfields', 'deletefield');
            echo "</a></td></tr></table></td></tr>\n</table><br>\n";
        }
        echo "<b>";
        echo $aInt->lang('customfields', 'addfield');
        echo "</b><br><br>\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=100 class=\"fieldlabel\">";
        echo $aInt->lang('customfields', 'fieldname');
        echo "</td><td class=\"fieldarea\"><table width=\"98%\" cellspacing=\"0\" cellpadding=\"0\"><tr><td><input type=\"text\" name=\"addfieldname\" size=\"30\"></td><td align=\"right\">";
        echo $aInt->lang('customfields', 'order');
        echo " <input type=\"text\" name=\"addsortorder\" size=\"5\" value=\"0\"></td></tr></table></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('customfields', 'fieldtype');
        echo "</td><td class=\"fieldarea\"><select name=\"addfieldtype\">\n<option value=\"text\">";
        echo $aInt->lang('customfields', 'typetextbox');
        echo "</option>\n<option value=\"link\">";
        echo $aInt->lang('customfields', 'typelink');
        echo "</option>\n<option value=\"password\">";
        echo $aInt->lang('customfields', 'typepassword');
        echo "</option>\n<option value=\"dropdown\">";
        echo $aInt->lang('customfields', 'typedropdown');
        echo "</option>\n<option value=\"tickbox\">";
        echo $aInt->lang('customfields', 'typetickbox');
        echo "</option>\n<option value=\"textarea\">";
        echo $aInt->lang('customfields', 'typetextarea');
        echo "</option>\n</select></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'description');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"addcfdesc\" size=\"60\"> ";
        echo $aInt->lang('customfields', 'descriptioninfo');
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('customfields', 'validation');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"addregexpr\" size=\"60\"> ";
        echo $aInt->lang('customfields', 'validationinfo');
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">Select Options</td><td class=\"fieldarea\"><input type=\"text\" name=\"addfieldoptions\" size=\"60\"> ";
        echo $aInt->lang('customfields', 'selectoptionsinfo');
        echo "</td></tr>\n<tr><td class=\"fieldlabel\"></td><td class=\"fieldarea\"><label><input type=\"checkbox\" name=\"addadminonly\"> ";
        echo $aInt->lang('customfields', 'adminonly');
        echo "</label> <label><input type=\"checkbox\" name=\"addrequired\"> ";
        echo $aInt->lang('customfields', 'requiredfield');
        echo "</label></td></tr>\n</table>\n\n  </div>\n</div>\n\n<p align=\"center\"><input type=\"submit\" value=\"";
        echo $aInt->lang('global', 'savechanges');
        echo "\" class=\"button\"> <input type=\"button\" value=\"";
        echo $aInt->lang('global', 'cancel');
        echo "\" onClick=\"window.location='";
        echo $whmcs->getPhpSelf();
        echo "'\" class=\"button\"></p>\n\n</form>\n\n";
    }
}
if( $action == 'add' )
{
    if( $port == '' )
    {
        $port = '110';
    }
    echo "\n<h2>";
    echo $aInt->lang('supportticketdepts', 'addnewdept');
    echo "</h2>\n\n<form method=\"post\" action=\"";
    echo $whmcs->getPhpSelf();
    echo "?sub=add\" autocomplete=\"off\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"20%\" class=\"fieldlabel\">";
    echo $aInt->lang('supportticketdepts', 'deptname');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"name\" size=\"25\" value=\"";
    echo $name;
    echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang('fields', 'description');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"description\" size=\"50\" value=\"";
    echo $description;
    echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang('supportticketdepts', 'deptemail');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"email\" size=\"40\" value=\"";
    echo $email;
    echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang('supportticketdepts', 'assignedadmins');
    echo "</td><td class=\"fieldarea\">\n";
    $result = select_query('tbladmins', '', '', 'username', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        echo "<label><input type=\"checkbox\" name=\"admins[]\" value=\"" . $data['id'] . "\"";
        echo " /> ";
        if( $data['disabled'] == 1 )
        {
            echo "<span class=\"disabledtext\">";
        }
        echo $data['username'] . " (" . $data['firstname'] . " " . $data['lastname'] . ")";
        if( $data['disabled'] == 1 )
        {
            echo " - " . $aInt->lang('global', 'disabled') . "</span>";
        }
        echo "</label><br />";
    }
    echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang('supportticketdepts', 'clientsonly');
    echo "</td><td class=\"fieldarea\"><label><input type=\"checkbox\" name=\"clientsonly\"";
    if( $clientsonly == 'on' )
    {
        echo " checked";
    }
    echo "> ";
    echo $aInt->lang('supportticketdepts', 'clientsonlydesc');
    echo "</label></td></tr>\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang('supportticketdepts', 'piperepliesonly');
    echo "</td><td class=\"fieldarea\"><label><input type=\"checkbox\" name=\"piperepliesonly\"";
    if( $piperepliesonly == 'on' )
    {
        echo " checked";
    }
    echo "> ";
    echo $aInt->lang('supportticketdepts', 'ticketsclientareaonlydesc');
    echo "</label></td></tr>\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang('supportticketdepts', 'noautoresponder');
    echo "</td><td class=\"fieldarea\"><label><input type=\"checkbox\" name=\"noautoresponder\"";
    if( $noautoresponder == 'on' )
    {
        echo " checked";
    }
    echo "> ";
    echo $aInt->lang('supportticketdepts', 'noautoresponderdesc');
    echo "</label></td></tr>\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang('global', 'hidden');
    echo "?</td><td class=\"fieldarea\"><label><input type=\"checkbox\" name=\"hidden\"";
    if( $hidden == 'on' )
    {
        echo " checked";
    }
    echo "> ";
    echo $aInt->lang('supportticketdepts', 'hiddendesc');
    echo "</label></td></tr>\n</table>\n<p><b>";
    echo $aInt->lang('supportticketdepts', 'pop3importconfigtitle');
    echo "</b> ";
    echo $aInt->lang('supportticketdepts', 'pop3importconfigdesc');
    echo "</p>\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"20%\" class=\"fieldlabel\">";
    echo $aInt->lang('fields', 'hostname');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"host\" size=\"40\" value=\"";
    echo $host;
    echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang('supportticketdepts', 'pop3port');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"port\" size=\"10\" value=\"";
    echo $port;
    echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang('supportticketdepts', 'pop3user');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"login\" size=\"40\" value=\"";
    echo $login;
    echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang('supportticketdepts', 'pop3pass');
    echo "</td><td class=\"fieldarea\"><input type=\"password\" name=\"password\" size=\"20\" value=\"";
    echo $password;
    echo "\"></td></tr>\n</table>\n\n<p align=\"center\"><input type=\"submit\" value=\"";
    echo $aInt->lang('supportticketdepts', 'addnewdept');
    echo "\" class=\"button\"> <input type=\"button\" value=\"";
    echo $aInt->lang('global', 'cancel');
    echo "\" onClick=\"window.location='";
    echo $whmcs->getPhpSelf();
    echo "'\" class=\"button\"></p>\n\n</form>\n\n";
}
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jquerycode = $jquerycode;
$aInt->jscode = $jscode;
$aInt->display();