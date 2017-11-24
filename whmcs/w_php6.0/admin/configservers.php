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
$aInt = new WHMCS_Admin("Configure Servers");
$aInt->title = 'Servers';
$aInt->sidebar = 'config';
$aInt->icon = 'servers';
$aInt->helplink = 'Servers';
if( $action == 'cantestconnection' )
{
    check_token("WHMCS.admin.default");
    $moduleName = $whmcs->get_req_var('type');
    if( cantestconnection($moduleName) )
    {
        throw new WHMCS_Exception_Fatal('1');
    }
    throw new WHMCS_Exception_Fatal('0');
}
if( $action == 'testconnection' )
{
    check_token("WHMCS.admin.default");
    $moduleName = $whmcs->get_req_var('type');
    $moduleInterface = new WHMCS_Module_Server();
    if( !$moduleInterface->load($moduleName) )
    {
        throw new WHMCS_Exception_Fatal("Invalid Server Module Type");
    }
    if( $moduleInterface->functionExists('TestConnection') )
    {
        $passwordToTest = WHMCS_Input_Sanitize::decode($whmcs->get_req_var('password'));
        $serverId = $whmcs->get_req_var('serverid');
        if( $serverId )
        {
            $storedPassword = get_query_val('tblservers', 'password', array( 'id' => $serverId ));
            $storedPassword = decrypt($storedPassword);
            if( !hasMaskedPasswordChanged($passwordToTest, $storedPassword) )
            {
                $passwordToTest = $storedPassword;
            }
        }
        $params = array(  );
        $params['server'] = true;
        $params['serverip'] = $whmcs->get_req_var('ipaddress');
        $params['serverhostname'] = $whmcs->get_req_var('hostname');
        $params['serverusername'] = $whmcs->get_req_var('username');
        $params['serverpassword'] = $passwordToTest;
        $params['serveraccesshash'] = $whmcs->get_req_var('accesshash');
        $params['serversecure'] = $whmcs->get_req_var('secure');
        $connectionTestResult = $moduleInterface->call('TestConnection', $params);
        if( array_key_exists('success', $connectionTestResult) && $connectionTestResult['success'] == true )
        {
            $htmlOutput = "<span style=\"padding:2px 10px;background-color:#5bb75b;color:#fff;font-weight:bold;\">" . $aInt->lang('configservers', 'testconnectionsuccess') . "</div>";
        }
        else
        {
            $errorMsg = array_key_exists('error', $connectionTestResult) ? $connectionTestResult['error'] : $aInt->lang('configservers', 'testconnectionunknownerror');
            $htmlOutput = "<span style=\"padding:2px 10px;background-color:#cc0000;color:#fff;\"><strong>" . $aInt->lang('configservers', 'testconnectionfailed') . ":</strong> " . WHMCS_Input_Sanitize::makesafeforoutput($errorMsg) . "</div>";
        }
        throw new WHMCS_Exception_Fatal($htmlOutput);
    }
    throw new WHMCS_Exception_Fatal($aInt->lang('configservers', 'testconnectionnotsupported'));
}
if( $action == 'delete' )
{
    check_token("WHMCS.admin.default");
    $numaccounts = get_query_val('tblhosting', "COUNT(*)", array( 'server' => $id ));
    if( 0 < $numaccounts )
    {
        redir("deleteerror=true");
    }
    else
    {
        run_hook('ServerDelete', array( 'serverid' => $id ));
        delete_query('tblservers', array( 'id' => $id ));
        redir("deletesuccess=true");
    }
}
if( $action == 'deletegroup' )
{
    check_token("WHMCS.admin.default");
    delete_query('tblservergroups', array( 'id' => $id ));
    delete_query('tblservergroupsrel', array( 'serverid' => $id ));
    redir("deletegroupsuccess=true");
}
if( $action == 'save' )
{
    check_token("WHMCS.admin.default");
    if( $id )
    {
        $result = select_query('tblservers', 'active,type', array( 'id' => $id ));
        $data = mysql_fetch_array($result);
        if( $type == $data['type'] )
        {
            $active = $data['active'];
        }
        else
        {
            $active = '';
        }
        $saveData = array( 'name' => $name, 'type' => $type, 'ipaddress' => trim($ipaddress), 'assignedips' => trim($assignedips), 'hostname' => trim($hostname), 'monthlycost' => trim($monthlycost), 'noc' => $noc, 'statusaddress' => trim($statusaddress), 'nameserver1' => trim($nameserver1), 'nameserver1ip' => trim($nameserver1ip), 'nameserver2' => trim($nameserver2), 'nameserver2ip' => trim($nameserver2ip), 'nameserver3' => trim($nameserver3), 'nameserver3ip' => trim($nameserver3ip), 'nameserver4' => trim($nameserver4), 'nameserver4ip' => trim($nameserver4ip), 'nameserver5' => trim($nameserver5), 'nameserver5ip' => trim($nameserver5ip), 'maxaccounts' => trim($maxaccounts), 'username' => trim($username), 'accesshash' => trim($accesshash), 'secure' => $secure, 'disabled' => $disabled, 'active' => $active );
        $newPassword = trim($whmcs->get_req_var('password'));
        $originalPassword = decrypt(get_query_val('tblservers', 'password', array( 'id' => $id )));
        $valueToStore = interpretMaskedPasswordChangeForStorage($newPassword, $originalPassword);
        if( $valueToStore !== false )
        {
            $saveData['password'] = $valueToStore;
        }
        update_query('tblservers', $saveData, array( 'id' => $id ));
        run_hook('ServerEdit', array( 'serverid' => $id ));
        redir("savesuccess=true");
    }
    else
    {
        $result = select_query('tblservers', 'id', array( 'type' => $type, 'active' => '1' ));
        $data = mysql_fetch_array($result);
        $active = $data['id'] ? '' : '1';
        $newid = insert_query('tblservers', array( 'name' => $name, 'type' => $type, 'ipaddress' => trim($ipaddress), 'assignedips' => trim($assignedips), 'hostname' => trim($hostname), 'monthlycost' => trim($monthlycost), 'noc' => $noc, 'statusaddress' => trim($statusaddress), 'nameserver1' => trim($nameserver1), 'nameserver1ip' => trim($nameserver1ip), 'nameserver2' => trim($nameserver2), 'nameserver2ip' => trim($nameserver2ip), 'nameserver3' => trim($nameserver3), 'nameserver3ip' => trim($nameserver3ip), 'nameserver4' => trim($nameserver4), 'nameserver4ip' => trim($nameserver4ip), 'nameserver5' => trim($nameserver5), 'nameserver5ip' => trim($nameserver5ip), 'maxaccounts' => trim($maxaccounts), 'username' => trim($username), 'password' => encrypt(trim($password)), 'accesshash' => trim($accesshash), 'secure' => $secure, 'active' => $active, 'disabled' => $disabled ));
        run_hook('ServerAdd', array( 'serverid' => $newid ));
        redir("createsuccess=true");
    }
}
if( $action == 'savegroup' )
{
    check_token("WHMCS.admin.default");
    if( $id )
    {
        update_query('tblservergroups', array( 'name' => $name, 'filltype' => $filltype ), array( 'id' => $id ));
        delete_query('tblservergroupsrel', array( 'groupid' => $id ));
    }
    else
    {
        $id = insert_query('tblservergroups', array( 'name' => $name, 'filltype' => $filltype ));
    }
    if( $selectedservers )
    {
        foreach( $selectedservers as $serverid )
        {
            insert_query('tblservergroupsrel', array( 'groupid' => $id, 'serverid' => $serverid ));
        }
    }
    redir("savesuccess=1");
}
if( $action == 'enable' )
{
    check_token("WHMCS.admin.default");
    update_query('tblservers', array( 'disabled' => '0' ), array( 'id' => $id ));
    redir("enablesuccess=1");
}
if( $action == 'disable' )
{
    check_token("WHMCS.admin.default");
    update_query('tblservers', array( 'disabled' => '1' ), array( 'id' => $id ));
    redir("disablesuccess=1");
}
if( $action == 'makedefault' )
{
    check_token("WHMCS.admin.default");
    $result = select_query('tblservers', '', array( 'id' => $id ));
    $data = mysql_fetch_array($result);
    $type = $data['type'];
    update_query('tblservers', array( 'active' => '' ), array( 'type' => $type ));
    update_query('tblservers', array( 'active' => '1' ), array( 'id' => $id ));
    redir("makedefault=1");
}
ob_start();
if( $action == '' )
{
    if( $createsuccess )
    {
        infoBox($aInt->lang('configservers', 'addedsuccessful'), $aInt->lang('configservers', 'addedsuccessfuldesc'));
    }
    if( $deletesuccess )
    {
        infoBox($aInt->lang('configservers', 'delsuccessful'), $aInt->lang('configservers', 'delsuccessfuldesc'));
    }
    if( $deletegroupsuccess )
    {
        infoBox($aInt->lang('configservers', 'groupdelsuccessful'), $aInt->lang('configservers', 'groupdelsuccessfuldesc'));
    }
    if( $deleteerror )
    {
        infoBox($aInt->lang('configservers', 'error'), $aInt->lang('configservers', 'errordesc'));
    }
    if( $savesuccess )
    {
        infoBox($aInt->lang('configservers', 'changesuccess'), $aInt->lang('configservers', 'changesuccessdesc'));
    }
    if( $enablesuccess )
    {
        infoBox($aInt->lang('configservers', 'enabled'), $aInt->lang('configservers', 'enableddesc'));
    }
    if( $disablesuccess )
    {
        infoBox($aInt->lang('configservers', 'disabled'), $aInt->lang('configservers', 'disableddesc'));
    }
    if( $makedefault )
    {
        infoBox($aInt->lang('configservers', 'defaultchange'), $aInt->lang('configservers', 'defaultchangedesc'));
    }
    echo $infobox;
    $aInt->deleteJSConfirm('doDelete', 'configservers', 'delserverconfirm', "?action=delete&id=");
    $aInt->deleteJSConfirm('doDeleteGroup', 'configservers', 'delgroupconfirm', "?action=deletegroup&id=");
    echo "\n<p>";
    echo $aInt->lang('configservers', 'pagedesc');
    echo "</p>\n\n<p><B>";
    echo $aInt->lang('fields', 'options');
    echo ":</B> <a href=\"";
    echo $whmcs->getPhpSelf();
    echo "?action=manage\">";
    echo $aInt->lang('configservers', 'addnewserver');
    echo "</a> | <a href=\"";
    echo $whmcs->getPhpSelf();
    echo "?action=managegroup\">";
    echo $aInt->lang('configservers', 'createnewgroup');
    echo "</a></p>\n\n";
    $server = new WHMCS_Module_Server();
    $modulesarray = $server->getList();
    $aInt->sortableTableInit('nopagination');
    $result3 = select_query('tblservers', "DISTINCT type", '', 'type', 'ASC');
    while( $data = mysql_fetch_array($result3) )
    {
        $servertype = $data['type'];
        $tabledata[] = array( 'dividingline', ucfirst($servertype) );
        $disableddata = array(  );
        $result = select_query('tblservers', '', array( 'type' => $data['type'] ), 'name', 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $id = $data['id'];
            $name = $data['name'];
            $ipaddress = $data['ipaddress'];
            $hostname = $data['hostname'];
            $maxaccounts = $data['maxaccounts'];
            $username = $data['username'];
            $password = decrypt($data['password']);
            $accesshash = $data['accesshash'];
            $secure = $data['secure'];
            $active = $data['active'];
            $type = $data['type'];
            $disabled = $data['disabled'];
            $active = $active ? "*" : '';
            $result2 = select_query('tblhosting', "COUNT(*)", "server='" . $id . "' AND (domainstatus='Active' OR domainstatus='Suspended')");
            $data = mysql_fetch_array($result2);
            $numaccounts = $data[0];
            $percentuse = @round($numaccounts / $maxaccounts * 100, 0);
            $params = array(  );
            $params['serverip'] = $ipaddress;
            $params['serverhostname'] = $hostname;
            $params['serverusername'] = $username;
            $params['serverpassword'] = $password;
            $params['serversecure'] = $secure;
            $params['serveraccesshash'] = $accesshash;
            if( in_array($type, $modulesarray) )
            {
                $server->load($type);
                $adminlogincode = $server->functionExists('AdminLink') ? $server->call('AdminLink', $params) : '-';
            }
            else
            {
                $adminlogincode = $aInt->lang('global', 'modulefilemissing');
            }
            if( $disabled )
            {
                $disableddata[] = array( "<i>" . $name . " (" . $aInt->lang('emailtpls', 'disabled') . ")</i>", "<i>" . $ipaddress . "</i>", "<i>" . $numaccounts . '/' . $maxaccounts . "</i>", "<i>" . $percentuse . "%</i>", $adminlogincode, "<div align=\"center\"><a href=\"?action=enable&id=" . $id . generate_token('link') . "\" title=\"" . $aInt->lang('configservers', 'enableserver') . "\"><img src=\"images/icons/disabled.png\"></a></div>", "<a href=\"?action=manage&id=" . $id . "\" title=\"" . $aInt->lang('global', 'edit') . "\"><img src=\"images/edit.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"Edit\"></a>", "<a href=\"#\" onClick=\"doDelete('" . $id . "');return false\" title=\"" . $aInt->lang('global', 'delete') . "\"><img src=\"images/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'delete') . "\"></a>" );
            }
            else
            {
                $tabledata[] = array( "<a href=\"?action=makedefault&id=" . $id . generate_token('link') . "\" title=\"" . $aInt->lang('configservers', 'defaultsignups') . "\">" . $name . "</a> " . $active, $ipaddress, $numaccounts . '/' . $maxaccounts, $percentuse . "%", $adminlogincode, "<div align=\"center\"><a href=\"?action=disable&id=" . $id . generate_token('link') . "\" title=\"" . $aInt->lang('configservers', 'disableserverclick') . "\"><img src=\"images/icons/tick.png\"></a></div>", "<a href=\"?action=manage&id=" . $id . "\" title=\"" . $aInt->lang('global', 'edit') . "\">\n                <img src=\"images/edit.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'edit') . "\"></a>", "<a href=\"#\" onClick=\"doDelete('" . $id . "');return false\" title=\"" . $aInt->lang('global', 'delete') . "\">\n            <img src=\"images/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'delete') . "\"></a>" );
            }
        }
        foreach( $disableddata as $data )
        {
            $tabledata[] = $data;
        }
    }
    echo $aInt->sortableTable(array( $aInt->lang('configservers', 'servername'), $aInt->lang('fields', 'ipaddress'), $aInt->lang('configservers', 'activeaccounts'), $aInt->lang('configservers', 'usage'), " ", $aInt->lang('fields', 'status'), '', '' ), $tabledata);
    echo "\n<h2>";
    echo $aInt->lang('configservers', 'groups');
    echo "</h2>\n\n<p>";
    echo $aInt->lang('configservers', 'groupsdesc');
    echo "</p>\n\n";
    $tabledata = '';
    $result = select_query('tblservergroups', '', '', 'name', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $id = $data['id'];
        $name = $data['name'];
        $filltype = $data['filltype'];
        if( $filltype == 1 )
        {
            $filltype = $aInt->lang('configservers', 'addleast');
        }
        else
        {
            if( $filltype == 2 )
            {
                $filltype = $aInt->lang('configservers', 'fillactive');
            }
        }
        $servers = '';
        $result2 = select_query('tblservergroupsrel', "tblservers.name", array( 'groupid' => $id ), 'name', 'ASC', '', "tblservers ON tblservers.id=tblservergroupsrel.serverid");
        while( $data = mysql_fetch_array($result2) )
        {
            $servers .= $data['name'] . ", ";
        }
        $servers = substr($servers, 0, 0 - 2);
        $tabledata[] = array( $name, $filltype, $servers, "<a href=\"?action=managegroup&id=" . $id . "\"><img src=\"images/edit.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'edit') . "\"></a>", "<a href=\"#\" onClick=\"doDeleteGroup('" . $id . "');return false\"><img src=\"images/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $aInt->lang('global', 'delete') . "\"></a>" );
    }
    echo $aInt->sortableTable(array( $aInt->lang('configservers', 'groupname'), $aInt->lang('fields', 'filltype'), $aInt->lang('setup', 'servers'), '', '' ), $tabledata);
}
else
{
    if( $action == 'manage' )
    {
        if( $id )
        {
            $result = select_query('tblservers', '', array( 'id' => $id ));
            $data = mysql_fetch_array($result);
            $id = $data['id'];
            $type = $data['type'];
            $name = $data['name'];
            $ipaddress = $data['ipaddress'];
            $assignedips = $data['assignedips'];
            $hostname = $data['hostname'];
            $monthlycost = $data['monthlycost'];
            $noc = $data['noc'];
            $statusaddress = $data['statusaddress'];
            $nameserver1 = $data['nameserver1'];
            $nameserver1ip = $data['nameserver1ip'];
            $nameserver2 = $data['nameserver2'];
            $nameserver2ip = $data['nameserver2ip'];
            $nameserver3 = $data['nameserver3'];
            $nameserver3ip = $data['nameserver3ip'];
            $nameserver4 = $data['nameserver4'];
            $nameserver4ip = $data['nameserver4ip'];
            $nameserver5 = $data['nameserver5'];
            $nameserver5ip = $data['nameserver5ip'];
            $maxaccounts = $data['maxaccounts'];
            $username = $data['username'];
            $password = decrypt($data['password']);
            $accesshash = $data['accesshash'];
            $secure = $data['secure'];
            $active = $data['active'];
            $disabled = $data['disabled'];
            $managetitle = $aInt->lang('configservers', 'editserver');
        }
        else
        {
            $managetitle = $aInt->lang('configservers', 'addserver');
            if( !$maxaccounts )
            {
                $maxaccounts = '200';
            }
        }
        echo "<h2>" . $managetitle . "</h2>";
        echo "\n<form method=\"post\" action=\"";
        echo $_SERVER['PHP_SELF'];
        echo "?action=save";
        if( $id )
        {
            echo "&id=" . $id;
        }
        echo "\" id=\"frmServerConfig\">\n<input type=\"hidden\" name=\"serverid\" value=\"";
        echo $id;
        echo "\" />\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"23%\" class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'name');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"name\" size=\"30\" value=\"";
        echo $name;
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'hostname');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"hostname\" size=\"40\" value=\"";
        echo $hostname;
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'ipaddress');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"ipaddress\" size=\"20\" value=\"";
        echo $ipaddress;
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('configservers', 'assignedips');
        echo "<br />";
        echo $aInt->lang('configservers', 'assignedipsdesc');
        echo "</td><td class=\"fieldarea\"><textarea name=\"assignedips\" cols=\"60\" rows=\"8\">";
        echo $assignedips;
        echo "</textarea></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('configservers', 'monthlycost');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"monthlycost\" size=\"10\" value=\"";
        echo $monthlycost;
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('configservers', 'datacenter');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"noc\" size=\"30\" value=\"";
        echo $noc;
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('configservers', 'maxaccounts');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"maxaccounts\" size=\"6\" value=\"";
        echo $maxaccounts;
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('configservers', 'statusaddress');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"statusaddress\" size=\"60\" value=\"";
        echo $statusaddress;
        echo "\"><br>";
        echo $aInt->lang('configservers', 'statusaddressdesc');
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('general', 'enabledisable');
        echo "</td><td class=\"fieldarea\"><label><input type=\"checkbox\" name=\"disabled\" value=\"1\"";
        if( $disabled )
        {
            echo 'checked';
        }
        echo "> ";
        echo $aInt->lang('configservers', 'disableserver');
        echo "</label></td></tr>\n</table>\n<p><b>";
        echo $aInt->lang('configservers', 'nameservers');
        echo "</b></p>\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"23%\" class=\"fieldlabel\">";
        echo $aInt->lang('configservers', 'primarynameserver');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"nameserver1\" size=\"40\" value=\"";
        echo $nameserver1;
        echo "\"> ";
        echo $aInt->lang('fields', 'ipaddress');
        echo ": <input type=\"text\" name=\"nameserver1ip\" size=\"25\" value=\"";
        echo $nameserver1ip;
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('configservers', 'secondarynameserver');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"nameserver2\" size=\"40\" value=\"";
        echo $nameserver2;
        echo "\"> ";
        echo $aInt->lang('fields', 'ipaddress');
        echo ": <input type=\"text\" name=\"nameserver2ip\" size=\"25\" value=\"";
        echo $nameserver2ip;
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('configservers', 'thirdnameserver');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"nameserver3\" size=\"40\" value=\"";
        echo $nameserver3;
        echo "\"> ";
        echo $aInt->lang('fields', 'ipaddress');
        echo ": <input type=\"text\" name=\"nameserver3ip\" size=\"25\" value=\"";
        echo $nameserver3ip;
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('configservers', 'fourthnameserver');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"nameserver4\" size=\"40\" value=\"";
        echo $nameserver4;
        echo "\"> ";
        echo $aInt->lang('fields', 'ipaddress');
        echo ": <input type=\"text\" name=\"nameserver4ip\" size=\"25\" value=\"";
        echo $nameserver4ip;
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('configservers', 'fifthnameserver');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"nameserver5\" size=\"40\" value=\"";
        echo $nameserver5;
        echo "\"> ";
        echo $aInt->lang('fields', 'ipaddress');
        echo ": <input type=\"text\" name=\"nameserver5ip\" size=\"25\" value=\"";
        echo $nameserver5ip;
        echo "\"></td></tr>\n</table>\n<p><b>";
        echo $aInt->lang('configservers', 'serverdetails');
        echo "</b></p>\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"23%\" class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'type');
        echo "</td><td class=\"fieldarea\"><select name=\"type\" id=\"server-type\">";
        $server = new WHMCS_Module_Server();
        $modulesarray = $server->getList();
        foreach( $modulesarray as $module )
        {
            echo "<option value=\"" . $module . "\"";
            if( $module == $type )
            {
                echo " selected";
            }
            echo ">" . ucfirst($module) . "</option>";
        }
        echo "</select> <input type=\"button\" value=\"";
        echo $aInt->lang('configservers', 'testconnection');
        echo "\" id=\"connectionTestBtn\" class=\"btn-danger btn-small\"";
        echo cantestconnection($type) ? '' : " style=\"display:none;\"";
        echo " /> <span id=\"connectionTestResult\"></span></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'username');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"username\" size=\"25\" value=\"";
        echo $username;
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'password');
        echo "</td><td class=\"fieldarea\"><input type=\"password\" name=\"password\" size=\"25\" value=\"";
        echo replacePasswordWithMasks($password);
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('configservers', 'accesshash');
        echo "<br>";
        echo $aInt->lang('configservers', 'accesshashdesc');
        echo "</td><td class=\"fieldarea\"><textarea name=\"accesshash\" cols=\"60\" rows=\"8\">";
        echo $accesshash;
        echo "</textarea></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('configservers', 'secure');
        echo "</td><td class=\"fieldarea\"><label><input type=\"checkbox\" name=\"secure\"";
        if( $secure )
        {
            echo " checked";
        }
        echo "> ";
        echo $aInt->lang('configservers', 'usessl');
        echo "</label></td></tr>\n</table>\n<p align=\"center\"><input type=\"submit\" value=\"";
        echo $aInt->lang('global', 'savechanges');
        echo "\" class=\"button\"></p>\n</form>\n\n";
        $connectionTestJSCode = "\n\n\$(\"#server-type\").change(function() {\n    \$.post(\"configservers.php\", 'token=" . generate_token('plain') . "&action=cantestconnection&type=' + \$(\"#server-type\").val(),\n    function(data) {\n        \$(\"#connectionTestResult\").fadeOut();\n        if (data==\"1\") {\n            \$(\"#connectionTestBtn\").fadeIn();\n        } else {\n            \$(\"#connectionTestBtn\").fadeOut();\n        }\n    });\n});\n\n\$(\"#connectionTestBtn\").click(function() {\n    \$(\"#connectionTestResult\").html(\"<img src=\\\"images/loading.gif\\\" align=\\\"absmiddle\\\" /> " . addslashes($aInt->lang('configservers', 'testconnectionloading')) . "\");\n    \$(\"#connectionTestResult\").show();\n    \$.post(\"configservers.php\", \$(\"#frmServerConfig\").serialize() + '&action=testconnection',\n    function(data) {\n        \$(\"#connectionTestResult\").html(data);\n    });\n});\n\n";
        $aInt->addInternalJQueryCode($connectionTestJSCode);
    }
    else
    {
        if( $action == 'managegroup' )
        {
            if( $id )
            {
                $managetitle = $aInt->lang('configservers', 'editgroup');
                $result = select_query('tblservergroups', '', array( 'id' => $id ));
                $data = mysql_fetch_array($result);
                $id = $data['id'];
                $name = $data['name'];
                $filltype = $data['filltype'];
            }
            else
            {
                $managetitle = $aInt->lang('configservers', 'newgroup');
                $filltype = '1';
            }
            echo "<h2>" . $managetitle . "</h2>";
            $jquerycode = "\$(\"#serveradd\").click(function () {\n  \$(\"#serverslist option:selected\").appendTo(\"#selectedservers\");\n  return false;\n});\n\$(\"#serverrem\").click(function () {\n  \$(\"#selectedservers option:selected\").appendTo(\"#serverslist\");\n  return false;\n});";
            echo "\n<form method=\"post\" action=\"";
            echo $_SERVER['PHP_SELF'];
            echo "?action=savegroup&id=";
            echo $id;
            echo "\">\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">";
            echo $aInt->lang('fields', 'name');
            echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"name\" size=\"40\" value=\"";
            echo $name;
            echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
            echo $aInt->lang('fields', 'filltype');
            echo "</td><td class=\"fieldarea\"><input type=\"radio\" name=\"filltype\" value=\"1\"";
            if( $filltype == 1 )
            {
                echo " checked";
            }
            echo "> ";
            echo $aInt->lang('configservers', 'addleast');
            echo "<br /><input type=\"radio\" name=\"filltype\" value=\"2\"";
            if( $filltype == 2 )
            {
                echo " checked";
            }
            echo "> ";
            echo $aInt->lang('configservers', 'fillactive');
            echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
            echo $aInt->lang('fields', 'selectedservers');
            echo "</td><td class=\"fieldarea\"><table><td><td><select size=\"10\" multiple=\"multiple\" id=\"serverslist\" style=\"width:200px;\">";
            $selectedservers = array(  );
            $result = select_query('tblservergroupsrel', "tblservers.id,tblservers.name,tblservers.disabled", array( 'groupid' => $id ), 'name', 'ASC', '', "tblservers ON tblservers.id=tblservergroupsrel.serverid");
            while( $data = mysql_fetch_array($result) )
            {
                $id = $data['id'];
                $name = $data['name'];
                $disabled = $data['disabled'];
                if( $disabled )
                {
                    $name .= " (" . $aInt->lang('emailtpls', 'disabled') . ")";
                }
                $selectedservers[$id] = $name;
            }
            $result = select_query('tblservers', '', '', 'name', 'ASC');
            while( $data = mysql_fetch_array($result) )
            {
                $id = $data['id'];
                $name = $data['name'];
                $disabled = $data['disabled'];
                if( $disabled )
                {
                    $name .= " (Disabled)";
                }
                if( !array_key_exists($id, $selectedservers) )
                {
                    echo "<option value=\"" . $id . "\">" . $name . "</option>";
                }
            }
            echo "</select></td><td align=\"center\"><input type=\"button\" id=\"serveradd\" value=\"";
            echo $aInt->lang('global', 'add');
            echo " &raquo;\"><br /><br /><input type=\"button\" id=\"serverrem\" value=\"&laquo; ";
            echo $aInt->lang('global', 'remove');
            echo "\"></td><td><select size=\"10\" multiple=\"multiple\" id=\"selectedservers\" name=\"selectedservers[]\" style=\"width:200px;\">";
            foreach( $selectedservers as $id => $name )
            {
                echo "<option value=\"" . $id . "\">" . $name . "</option>";
            }
            echo "</select></td></td></table></td></tr>\n</table>\n<p align=\"center\"><input type=\"submit\" value=\"";
            echo $aInt->lang('global', 'savechanges');
            echo "\" onclick=\"\$('#selectedservers *').attr('selected','selected')\" class=\"button\"></p>\n</form>\n\n";
        }
    }
}
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jscode = $jscode;
$aInt->jquerycode = $jquerycode;
$aInt->display();
function canTestConnection($moduleName)
{
    $moduleInterface = new WHMCS_Module_Server();
    if( $moduleInterface->load($moduleName) && $moduleInterface->functionExists('TestConnection') )
    {
        return true;
    }
    return false;
}