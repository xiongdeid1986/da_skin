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
function getModuleType($id)
{
    $result = select_query('tblservers', 'type', array( 'id' => $id ));
    $data = mysql_fetch_array($result);
    $type = $data['type'];
    return $type;
}
function ModuleBuildParams($serviceID)
{
    $server = new WHMCS_Module_Server();
    if( !$server->loadByServiceID($serviceID) )
    {
        logActivity("Required Product Module '" . $server->getServiceModule() . "' Missing");
    }
    return $server->buildParams();
}
function ModuleCallFunction($function, $serviceID, $extraParams)
{
    $server = new WHMCS_Module_Server();
    if( !$server->loadByServiceID($serviceID) )
    {
        logActivity("Required Product Module '" . $server->getServiceModule() . "' Missing");
        return "Module Not Found";
    }
    $params = $server->buildParams();
    if( is_array($extraParams) )
    {
        $params = array_merge($params, $extraParams);
    }
    $serviceid = (int) $params['serviceid'];
    $userid = (int) $params['userid'];
    $hookresults = run_hook('PreModule' . $function, array( 'params' => $params ));
    $hookabort = false;
    foreach( $hookresults as $hookvals )
    {
        foreach( $hookvals as $k => $v )
        {
            if( $k == 'abortcmd' && $v === true )
            {
                $hookabort = true;
                $result = "Function Aborted by Action Hook Code";
            }
        }
        if( !$hookabort )
        {
            $params = recursiveReplace($params, $hookvals);
        }
    }
    $logname = $function;
    if( $logname == 'ChangePackage' )
    {
        $logname = "Change Package";
    }
    else
    {
        if( $logname == 'ChangePassword' )
        {
            $logname = "Change Password";
        }
    }
    if( !$hookabort )
    {
        $modfuncname = in_array($function, array( 'Create', 'Suspend', 'Unsuspend', 'Terminate' )) ? $function . 'Account' : $function;
        if( $server->functionExists($modfuncname) )
        {
            $result = $server->call($modfuncname, $extraParams);
            if( $result == 'success' )
            {
                $extra_log_info = '';
                if( $function == 'Suspend' )
                {
                    $suspendReason = isset($extraParams['suspendreason']) && $extraParams['suspendreason'] != "Overdue on Payment" ? $extraParams['suspendreason'] : '';
                    if( $suspendReason )
                    {
                        $extra_log_info = " - Reason: " . $suspendReason;
                    }
                }
                logActivity("Module " . $logname . " Successful" . $extra_log_info . " - Service ID: " . $serviceid, $userid);
                $updatearray = array(  );
                if( $function == 'Create' )
                {
                    $updatearray = array( 'domainstatus' => 'Active' );
                }
                else
                {
                    if( $function == 'Suspend' )
                    {
                        $updatearray = array( 'domainstatus' => 'Suspended', 'suspendreason' => $suspendReason );
                    }
                    else
                    {
                        if( $function == 'Unsuspend' )
                        {
                            $updatearray = array( 'domainstatus' => 'Active', 'suspendreason' => '' );
                        }
                        else
                        {
                            if( $function == 'Terminate' )
                            {
                                $updatearray = array( 'domainstatus' => 'Terminated' );
                                $result2 = select_query('tblhostingaddons', 'id,addonid', "hostingid=" . $serviceid . " AND status IN ('Active','Suspended')");
                                while( $data = mysql_fetch_array($result2) )
                                {
                                    $aid = $data['id'];
                                    $addonid = $data['addonid'];
                                    update_query('tblhostingaddons', array( 'status' => 'Terminated' ), array( 'id' => $aid ));
                                    run_hook('AddonTerminated', array( 'id' => $aid, 'userid' => $userid, 'serviceid' => $serviceid, 'addonid' => $addonid ));
                                }
                            }
                        }
                    }
                }
                if( 0 < count($updatearray) )
                {
                    update_query('tblhosting', $updatearray, array( 'id' => $serviceid ));
                }
                run_hook('AfterModule' . $function, array( 'params' => $params ));
                return 'success';
            }
        }
        else
        {
            $result = "Function Not Supported by Module";
            if( $function == 'Renew' )
            {
                return $result;
            }
        }
    }
    logActivity("Module " . $logname . " Failed - Service ID: " . $serviceid . " - Error: " . $result, $userid);
    return $result;
}
function ServerCreateAccount($serviceID)
{
    $params = modulebuildparams($serviceID);
    if( !$params['username'] )
    {
        $usernamegenhook = run_hook('OverrideModuleUsernameGeneration', $params);
        $username = '';
        if( count($usernamegenhook) )
        {
            foreach( $usernamegenhook as $usernameval )
            {
                if( is_string($usernameval) )
                {
                    $username = $usernameval;
                }
            }
        }
        if( !$username )
        {
            $username = createServerUsername($params['domain']);
        }
        update_query('tblhosting', array( 'username' => $username ), array( 'id' => $serviceID ));
    }
    if( !$params['password'] )
    {
        update_query('tblhosting', array( 'password' => encrypt(createServerPassword()) ), array( 'id' => $serviceID ));
    }
    return modulecallfunction('Create', $serviceID);
}
function ServerSuspendAccount($serviceID, $suspendreason = '')
{
    $extraParams = array( 'suspendreason' => $suspendreason ? $suspendreason : "Overdue on Payment" );
    return modulecallfunction('Suspend', $serviceID, $extraParams);
}
function ServerUnsuspendAccount($serviceID)
{
    return modulecallfunction('Unsuspend', $serviceID);
}
function ServerTerminateAccount($serviceID)
{
    return modulecallfunction('Terminate', $serviceID);
}
function ServerRenew($serviceID)
{
    $result = modulecallfunction('Renew', $serviceID);
    if( $result == "Function Not Supported by Module" )
    {
        $result = 'notsupported';
    }
    return $result;
}
function ServerChangePassword($serviceID)
{
    return modulecallfunction('ChangePassword', $serviceID);
}
function ServerLoginLink($serviceID)
{
    $server = new WHMCS_Module_Server();
    $server->loadByServiceID($serviceID);
    if( $server->functionExists('LoginLink') )
    {
        return $server->call('LoginLink');
    }
    return '';
}
function ServerChangePackage($serviceID)
{
    return modulecallfunction('ChangePackage', $serviceID);
}
function ServerCustomFunction($serviceID, $func_name)
{
    $server = new WHMCS_Module_Server();
    $server->loadByServiceID($serviceID);
    return $server->call($func_name, $serviceID);
}
function ServerClientArea($serviceID)
{
    $server = new WHMCS_Module_Server();
    $server->loadByServiceID($serviceID);
    if( $server->functionExists('ClientArea') )
    {
        return $server->call('ClientArea');
    }
    return '';
}
function ServerUsageUpdate()
{
    $result2 = select_query('tblservers', '', array( 'disabled' => '0' ), 'name', 'ASC');
    while( $data = mysql_fetch_array($result2) )
    {
        $servertype = $data['type'];
        $params = array(  );
        $params['serverid'] = $data['id'];
        $params['serverip'] = $data['ipaddress'];
        $params['serverhostname'] = $data['hostname'];
        $params['serverusername'] = $data['username'];
        $params['serverpassword'] = decrypt($data['password']);
        $params['serveraccesshash'] = $data['accesshash'];
        $params['serversecure'] = $data['secure'];
        $server = new WHMCS_Module_Server();
        $server->load($servertype);
        if( $server->functionExists('UsageUpdate') )
        {
            logActivity("Cron Job: Running Usage Stats Update for Server ID " . (int) $data['id']);
            $server->call('UsageUpdate', $params);
        }
    }
}
function createServerUsername($domain)
{
    global $CONFIG;
    if( !$domain && !$CONFIG['GenerateRandomUsername'] )
    {
        return '';
    }
    if( !$CONFIG['GenerateRandomUsername'] )
    {
        $domain = strtolower($domain);
        $username = preg_replace("/[^a-z]/", '', $domain);
        $username = substr($username, 0, 8);
        $result = select_query('tblhosting', "COUNT(*)", array( 'username' => $username ));
        $data = mysql_fetch_array($result);
        $username_exists = $data[0];
        $suffix = 0;
        while( 0 < $username_exists )
        {
            $suffix++;
            $trimlength = 8 - strlen($suffix);
            $username = substr($username, 0, $trimlength) . $suffix;
            $result = select_query('tblhosting', "COUNT(*)", array( 'username' => $username ));
            $data = mysql_fetch_array($result);
            $username_exists = $data[0];
        }
    }
    else
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $str = '';
        $seeds_count = strlen($lowercase) - 1;
        for( $i = 0; $i < 8; $i++ )
        {
            $str .= $lowercase[rand(0, $seeds_count)];
        }
        $username = '';
        for( $i = 0; $i < 8; $i++ )
        {
            $randomnum = rand(0, strlen($str) - 1);
            $username .= $str[$randomnum];
            $str = substr($str, 0, $randomnum) . substr($str, $randomnum + 1);
        }
        $result = select_query('tblhosting', "COUNT(*)", array( 'username' => $username ));
        $data = mysql_fetch_array($result);
        $username_exists = $data[0];
        while( 0 < $username_exists )
        {
            $username = '';
            $str = '';
            for( $i = 0; $i < 8; $i++ )
            {
                $str .= $lowercase[rand(0, $seeds_count)];
            }
            for( $i = 0; $i < 8; $i++ )
            {
                $randomnum = rand(0, strlen($str) - 1);
                $username .= $str[$randomnum];
                $str = substr($str, 0, $randomnum) . substr($str, $randomnum + 1);
            }
            $result = select_query('tblhosting', "COUNT(*)", array( 'username' => $username ));
            $data = mysql_fetch_array($result);
            $username_exists = $data[0];
        }
    }
    return $username;
}
function createServerPassword()
{
    $numbers = '0123456789';
    $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVYWXYZ';
    $str = '';
    $seeds_count = strlen($numbers) - 1;
    for( $i = 0; $i < 4; $i++ )
    {
        $str .= $numbers[rand(0, $seeds_count)];
    }
    $seeds_count = strlen($lowercase) - 1;
    for( $i = 0; $i < 4; $i++ )
    {
        $str .= $lowercase[rand(0, $seeds_count)];
    }
    $seeds_count = strlen($uppercase) - 1;
    for( $i = 0; $i < 2; $i++ )
    {
        $str .= $uppercase[rand(0, $seeds_count)];
    }
    $password = '';
    for( $i = 0; $i < 10; $i++ )
    {
        $randomnum = rand(0, strlen($str) - 1);
        $password .= $str[$randomnum];
        $str = substr($str, 0, $randomnum) . substr($str, $randomnum + 1);
    }
    return $password;
}
function getServerID($servertype, $servergroup)
{
    if( !$servergroup )
    {
        $result = select_query('tblservers', "id,maxaccounts,(SELECT COUNT(id) FROM tblhosting WHERE tblhosting.server=tblservers.id AND (domainstatus='Active' OR domainstatus='Suspended')) AS usagecount", array( 'type' => $servertype, 'active' => '1', 'disabled' => '0' ));
        $data = mysql_fetch_array($result);
        $serverid = $data['id'];
        $maxaccounts = $data['maxaccounts'];
        $usagecount = $data['usagecount'];
        if( $serverid && $maxaccounts <= $usagecount )
        {
            $result = full_query("SELECT id,((SELECT COUNT(id) FROM tblhosting WHERE tblhosting.server=tblservers.id AND (domainstatus='Active' OR domainstatus='Suspended'))/maxaccounts) AS percentusage FROM tblservers WHERE type='" . $servertype . "' AND id!='" . $serverid . "' AND disabled=0 ORDER BY percentusage ASC");
            $data = mysql_fetch_array($result);
            if( $data['id'] )
            {
                $serverid = $data['id'];
                update_query('tblservers', array( 'active' => '' ), array( 'type' => $servertype ));
                update_query('tblservers', array( 'active' => '1' ), array( 'type' => $servertype, 'id' => $serverid ));
            }
        }
    }
    else
    {
        $result = select_query('tblservergroups', 'filltype', array( 'id' => $servergroup ));
        $data = mysql_fetch_array($result);
        $filltype = $data['filltype'];
        $serverslist = array(  );
        $result = select_query('tblservergroupsrel', 'serverid', array( 'groupid' => $servergroup ));
        while( $data = mysql_fetch_array($result) )
        {
            $serverslist[] = $data['serverid'];
        }
        if( $filltype == 1 )
        {
            $result = full_query("SELECT id,((SELECT COUNT(id) FROM tblhosting WHERE tblhosting.server=tblservers.id AND (domainstatus='Active' OR domainstatus='Suspended'))/maxaccounts) AS percentusage FROM tblservers WHERE id IN (" . db_build_in_array($serverslist) . ") AND disabled=0 ORDER BY percentusage ASC");
            $data = mysql_fetch_array($result);
            $serverid = $data['id'];
        }
        else
        {
            if( $filltype == 2 )
            {
                $result = select_query('tblservers', "id,maxaccounts,(SELECT COUNT(id) FROM tblhosting WHERE tblhosting.server=tblservers.id AND (domainstatus='Active' OR domainstatus='Suspended')) AS usagecount", "id IN (" . db_build_in_array($serverslist) . ") AND active='1' AND disabled=0");
                $data = mysql_fetch_array($result);
                $serverid = $data['id'];
                $maxaccounts = $data['maxaccounts'];
                $usagecount = $data['usagecount'];
                if( $serverid && $maxaccounts <= $usagecount )
                {
                    $result = full_query("SELECT id,((SELECT COUNT(id) FROM tblhosting WHERE tblhosting.server=tblservers.id AND (domainstatus='Active' OR domainstatus='Suspended'))/maxaccounts) AS percentusage FROM tblservers WHERE id IN (" . db_build_in_array($serverslist) . ") AND disabled=0 AND id!=" . (int) $serverid . " ORDER BY percentusage ASC");
                    $data = mysql_fetch_array($result);
                    if( $data['id'] )
                    {
                        $serverid = $data['id'];
                        update_query('tblservers', array( 'active' => '' ), array( 'type' => $servertype ));
                        update_query('tblservers', array( 'active' => '1' ), array( 'type' => $servertype, 'id' => $serverid ));
                    }
                }
            }
        }
    }
    return $serverid;
}
function RebuildModuleHookCache()
{
    $hooksarray = array(  );
    $server = new WHMCS_Module_Server();
    foreach( $server->getList() as $module )
    {
        if( is_file(ROOTDIR . '/modules/servers/' . $module . "/hooks.php") )
        {
            $hooksarray[] = $module;
        }
    }
    $whmcs = WHMCS_Application::getinstance();
    $whmcs->set_config('ModuleHooks', implode(',', $hooksarray));
}
function moduleConfigFieldOutput($values)
{
    if( is_null($values['Value']) )
    {
        $values['Value'] = isset($values['Default']) ? $values['Default'] : '';
    }
    $values['Value'] = htmlspecialchars($values['Value']);
    if( $values['Type'] == 'text' )
    {
        $code = "<input type=\"text\" name=\"" . $values['Name'] . "\" size=\"" . $values['Size'] . "\" value=\"" . $values['Value'] . "\" />";
        if( $values['Description'] )
        {
            $code .= " " . $values['Description'];
        }
    }
    else
    {
        if( $values['Type'] == 'password' )
        {
            $code = "<input type=\"password\" name=\"" . $values['Name'] . "\" size=\"" . $values['Size'] . "\" value=\"" . $values['Value'] . "\" />";
            if( $values['Description'] )
            {
                $code .= " " . $values['Description'];
            }
        }
        else
        {
            if( $values['Type'] == 'yesno' )
            {
                $code = "<label><input type=\"checkbox\" name=\"" . $values['Name'] . "\"";
                if( !empty($values['Value']) )
                {
                    $code .= " checked=\"checked\"";
                }
                $code .= " /> " . $values['Description'] . "</label>";
            }
            else
            {
                if( $values['Type'] == 'dropdown' )
                {
                    $code = "<select name=\"" . $values['Name'] . "\">";
                    $options = explode(',', $values['Options']);
                    foreach( $options as $tempval )
                    {
                        $code .= "<option value=\"" . $tempval . "\"";
                        if( $values['Value'] == $tempval )
                        {
                            $code .= " selected=\"selected\"";
                        }
                        $code .= ">" . $tempval . "</option>";
                    }
                    $code .= "</select>";
                    if( $values['Description'] )
                    {
                        $code .= " " . $values['Description'];
                    }
                }
                else
                {
                    if( $values['Type'] == 'radio' )
                    {
                        $code = '';
                        if( $values['Description'] )
                        {
                            $code .= $values['Description'] . "<br />";
                        }
                        $options = explode(',', $values['Options']);
                        if( !$values['Value'] )
                        {
                            $values['Value'] = $options[0];
                        }
                        foreach( $options as $tempval )
                        {
                            $code .= "<label><input type=\"radio\" name=\"" . $values['Name'] . "\" value=\"" . $tempval . "\"";
                            if( $values['Value'] == $tempval )
                            {
                                $code .= " checked=\"checked\"";
                            }
                            $code .= " /> " . $tempval . "</label><br />";
                        }
                    }
                    else
                    {
                        if( $values['Type'] == 'textarea' )
                        {
                            $cols = $values['Cols'] ? $values['Cols'] : '60';
                            $rows = $values['Rows'] ? $values['Rows'] : '5';
                            $code = "<textarea name=\"" . $values['Name'] . "\" cols=\"" . $cols . "\" rows=\"" . $rows . "\">" . $values['Value'] . "</textarea>";
                            if( $values['Description'] )
                            {
                                $code .= "<br />" . $values['Description'];
                            }
                        }
                        else
                        {
                            $code = $values['Description'];
                        }
                    }
                }
            }
        }
    }
    return $code;
}