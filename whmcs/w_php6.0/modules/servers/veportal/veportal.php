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
function veportal_ConfigOptions()
{
    if( !mysql_num_rows(full_query("SHOW TABLES LIKE 'mod_veportal'")) )
    {
        full_query("CREATE TABLE `mod_veportal` (\n`id` INT( 250 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,\n`relid` TEXT NULL ,\n`veid` TEXT NULL ,\n`hostname` TEXT NULL ,\n`ipad` TEXT NULL ,\n`lastmod` TEXT NULL\n) ENGINE = MYISAM ");
    }
    $configarray = array( "Package ID" => array( 'Type' => 'text', 'Size' => '25' ), "UBC Set ID" => array( 'Type' => 'text', 'Size' => '25' ), "Welcome Email" => array( 'Type' => 'yesno', 'Description' => "Send vePortal Welcome eMail (Reccomended)" ) );
    return $configarray;
}
function veportal_getAdminUsername($id)
{
    $get = full_query("SELECT * FROM tbladmins WHERE id = " . (int) $id);
    $r = mysql_fetch_array($get);
    return $r['username'];
}
function veportal_updatePackageNotes($id, $cmd, $newnotes)
{
    $get = full_query("SELECT * FROM tblhosting WHERE id = " . (int) $id);
    $r = mysql_fetch_array($get);
    $previous = $r['notes'];
    $date = date("d/m/y @ H:i:s");
    $username = veportal_getadminusername($_SESSION['adminid']);
    $new = $previous . "\n-----------------------------------------------\nDate: " . $date . " User: " . $username . "\n-----------------------------------------------\nModule Command: " . $cmd . " \n" . $newnotes;
    update_query('tblhosting', array( 'notes' => $new ), array( 'id' => (int) $id ));
}
function veportal_getPackageFieldID($field, $pid)
{
    $get = full_query("SELECT * FROM tblcustomfields WHERE relid = '" . (int) $pid . "' AND fieldname = '" . db_escape_string($field) . "'");
    $r = mysql_fetch_array($get);
    return $r['id'];
}
function veportal_getPackageFields($params)
{
    $fieldid['hostname'] = veportal_getpackagefieldid('Hostname', $params['pid']);
    $fieldid['veid'] = veportal_getpackagefieldid('VEID', $params['pid']);
    $fieldid['ipad'] = veportal_getpackagefieldid('IP', $params['pid']);
    return $fieldid;
}
function veportal_updateCustomData($params, $veid, $ip, $hostname)
{
    $cfield = veportal_getpackagefields($params);
    full_query("UPDATE tblcustomfieldsvalues SET value='" . db_escape_string($veid) . "' WHERE fieldid='" . (int) $cfield['veid'] . "' AND relid = '" . (int) $params['serviceid'] . "'") or exit( mysql_error() );
    full_query("UPDATE tblcustomfieldsvalues SET value='" . db_escape_string($hostname) . "' WHERE fieldid='" . (int) $cfield['hostname'] . "' AND relid = '" . (int) $params['serviceid'] . "'") or exit( mysql_error() );
    full_query("UPDATE tblcustomfieldsvalues SET value='" . db_escape_string($ip) . "' WHERE fieldid='" . (int) $cfield['ipad'] . "' AND relid = '" . (int) $params['serviceid'] . "'") or exit( mysql_error() );
}
function veportal_updateVPSinfo($veid, $ip, $hostname, $serviceid, $params)
{
    if( empty($hostname) )
    {
        $hostname = $params['domain'];
    }
    full_query("UPDATE tblhosting SET domain='" . db_escape_string($hostname) . "', dedicatedip='DO NOT EDIT THESE VALUES;veid=" . db_escape_string($veid . ";ip=" . $ip . ";hostname=" . $hostname) . "' WHERE id=" . (int) $serviceid) or exit( mysql_error() );
    veportal_updatecustomdata($params, $veid, $ip, $hostname);
}
function veportal_changeServiceStatus($id, $status)
{
    update_query('tblhosting', array( 'domainstatus' => $status ), array( 'id' => (int) $id ));
}
function veportal_getUniqueCode($length)
{
    $code = md5(uniqid(rand(), true));
    if( $length != '' )
    {
        return substr($code, 0, $length);
    }
    return $code;
}
function veportal_generateUsername($domain, $id)
{
    $domain = str_replace(".", '', $domain);
    $domain = str_replace('-', '', $domain);
    $domain = str_replace('_', '', $domain);
    $hash = veportal_getuniquecode('5');
    $username = $domain['0'] . $domain['1'] . $domain['2'] . $domain['3'] . $domain['4'] . $hash;
    update_query('tblhosting', array( 'username' => $username ), array( 'id' => (int) $id ));
    return $username;
}
function veportal_getvePortalAccountInfo($serviceid)
{
    $get = select_query('mod_veportal', '', array( 'relid' => (int) $serviceid ));
    $r = mysql_fetch_array($get);
    $params['veid'] = $r['veid'];
    $params['hostname'] = $r['hostname'];
    $params['ipaddress'] = $r['ipad'];
    return $params;
}
function veportal_processAPI($api, $postfields, $params)
{
    $api['user'] = $params['serverusername'];
    $api['key'] = $params['serverpassword'];
    $api['sslmode'] = $params['serversecure'];
    $api['host'] = $params['serverip'];
    $postfields['apikey'] = $api['key'];
    $postfields['apiuser'] = $api['user'];
    $postfields['apifunc'] = $api['function'];
    if( $api['sslmode'] != 'on' )
    {
        $url = "http://" . $api['host'] . ":2407/api.php";
    }
    else
    {
        $url = "https://" . $api['host'] . ":2408/api.php";
    }
    $query_string = http_build_query($postfields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
    $data = curl_exec($ch);
    curl_close($ch);
    $data = explode(';', $data);
    foreach( $data as $temp )
    {
        $temp = explode("=", $temp);
        $results[$temp[0]] = $temp[1];
    }
    return $results;
}
function veportal_CreateAccount($params)
{
    $paramsb = veportal_getveportalaccountinfo($params['serviceid']);
    $params = array_merge($params, $paramsb);
    $serviceid = $params['serviceid'];
    $pid = $params['pid'];
    $domain = $params['domain'];
    $username = $params['username'];
    $password = $params['password'];
    $clientsdetails = $params['clientsdetails'];
    $customfields = $params['customfields'];
    $configoptions = $params['configoptions'];
    $api['function'] = 'newacct';
    $post['package'] = $params['configoption1'];
    $post['ubcset'] = $params['configoption2'];
    $post['welcomeemail'] = $params['configoption13'];
    $post['ostemplate'] = $params['configoptions']["OS Template"];
    $post['email'] = $params['clientsdetails']['email'];
    $post['hostname'] = $params['customfields']['Hostname'];
    $post['server'] = 'localhost';
    $post['ippool'] = 'any';
    $post['password'] = $params['password'];
    $post['username'] = veportal_generateusername($post['hostname'], $serviceid);
    $apiResult = veportal_processapi($api, $post, $params);
    if( $apiResult['return'] == 'error' )
    {
        veportal_updatepackagenotes($pid, "Create Account", "Failed Account Creation");
        if( $apiResult['problem'] == 'useridtaken' )
        {
            $result = "Username Taken!";
        }
        else
        {
            if( $apiResult['problem'] == 'wrongip' )
            {
                $result = "Incorrect API IP";
            }
            else
            {
                if( $apiResult['problem'] == 'wrongkey' )
                {
                    $result = "Incorrect API Key";
                }
                else
                {
                    if( $apiResult['problem'] == 'wrongrskey' )
                    {
                        $result = "Incorrect API Key For Reseller";
                    }
                }
            }
        }
    }
    else
    {
        $successful = true;
        $result = 'success';
        $cfield = veportal_getpackagefields($params);
        delete_query('tblcustomfieldsvalues', array( 'fieldid' => (int) $cfield['hostname'], 'relid' => (int) $params['serviceid'] ));
        delete_query('tblcustomfieldsvalues', array( 'fieldid' => (int) $cfield['veid'], 'relid' => (int) $params['serviceid'] ));
        delete_query('tblcustomfieldsvalues', array( 'fieldid' => (int) $cfield['ipad'], 'relid' => (int) $params['serviceid'] ));
        full_query("INSERT INTO tblcustomfieldsvalues (fieldid, relid, value)\n        VALUES ('" . (int) $cfield['veid'] . "', '" . (int) $params['serviceid'] . "', '--Not Populated--')");
        full_query("INSERT INTO tblcustomfieldsvalues (fieldid, relid, value)\n        VALUES ('" . (int) $cfield['hostname'] . "', '" . (int) $params['serviceid'] . "', '--Not Populated--')");
        full_query("INSERT INTO tblcustomfieldsvalues (fieldid, relid, value)\n        VALUES ('" . (int) $cfield['ipad'] . "', '" . (int) $params['serviceid'] . "', '--Not Populated--')");
        veportal_updatepackagenotes($serviceid, "Create Account", "Created VEID: " . $apiResult['veid'] . '');
        veportal_changeservicestatus($serviceid, 'Active');
        veportal_updatevpsinfo($apiResult['veid'], $apiResult['ipad'], $post['hostname'], $serviceid, $params);
        full_query("INSERT INTO mod_veportal (relid, veid, hostname, ipad, lastmod) \n        VALUES ('" . (int) $serviceid . "', '" . db_escape_string($apiResult['veid']) . "', '" . db_escape_string($apiResult['hostname']) . "', '" . db_escape_string($apiResult['ipad']) . "', '" . time() . "')");
    }
    return $result;
}
function veportal_TerminateAccount($params)
{
    $paramsb = veportal_getveportalaccountinfo($params['serviceid']);
    $params = array_merge($params, $paramsb);
    $api['function'] = 'destroyacct';
    $post['veid'] = $params['veid'];
    $apiResult = veportal_processapi($api, $post, $params);
    delete_query('mod_veportal', array( 'relid' => (int) $params['serviceid'] ));
    veportal_updatepackagenotes($params['serviceid'], "Terminate Account", "Terminated Account");
    if( $apiResult['return'] == 'error' )
    {
        if( $apiResult['problem'] == 'wrongip' )
        {
            $result = "Incorrect API IP";
        }
        else
        {
            if( $apiResult['problem'] == 'wrongkey' )
            {
                $result = "Incorrect API Key";
            }
            else
            {
                if( $apiResult['problem'] == 'nolicense' )
                {
                    $result = "vePortal Node Not Licensed. <b>Visit <a href='http://www.veportal.com/'>vePortal</a> To Purchase a License</b>";
                }
            }
        }
    }
    else
    {
        $successful = true;
        $result = 'success';
    }
    return $result;
}
function veportal_SuspendAccount($params)
{
    $paramsb = veportal_getveportalaccountinfo($params['serviceid']);
    $params = array_merge($params, $paramsb);
    $api['function'] = 'suspendacct';
    $post['veid'] = $params['veid'];
    $post['username'] = $params['username'];
    $apiResult = veportal_processapi($api, $post, $params);
    veportal_updatepackagenotes($params['serviceid'], "Suspend Account", "Suspended Account");
    veportal_changeservicestatus($params['serviceid'], 'Suspended');
    if( $apiResult['return'] == 'error' )
    {
        if( $apiResult['problem'] == 'wrongip' )
        {
            $result = "Incorrect API IP";
        }
        else
        {
            if( $apiResult['problem'] == 'wrongkey' )
            {
                $result = "Incorrect API Key";
            }
            else
            {
                if( $apiResult['problem'] == 'nolicense' )
                {
                    $result = "vePortal Node Not Licensed. <b>Visit <a href='http://www.veportal.com/'>vePortal</a> To Purchase a License</b>";
                }
            }
        }
    }
    else
    {
        $successful = true;
        $result = 'success';
    }
    return $result;
}
function veportal_UnsuspendAccount($params)
{
    $paramsb = veportal_getveportalaccountinfo($params['serviceid']);
    $params = array_merge($params, $paramsb);
    $api['function'] = 'unsuspendacct';
    $post['veid'] = $params['veid'];
    $post['username'] = $params['username'];
    $apiResult = veportal_processapi($api, $post, $params);
    veportal_updatepackagenotes($params['serviceid'], "Unsuspend Account", "Unsuspended Account");
    veportal_changeservicestatus($params['serviceid'], 'Active');
    if( $apiResult['return'] == 'error' )
    {
        if( $apiResult['problem'] == 'wrongip' )
        {
            $result = "Incorrect API IP";
        }
        else
        {
            if( $apiResult['problem'] == 'wrongkey' )
            {
                $result = "Incorrect API Key";
            }
            else
            {
                if( $apiResult['problem'] == 'nolicense' )
                {
                    $result = "vePortal Node Not Licensed. <b>Visit <a href='http://www.veportal.com/'>vePortal</a> To Purchase a License</b>";
                }
            }
        }
    }
    else
    {
        $successful = true;
        $result = 'success';
    }
    return $result;
}
function veportal_ChangePassword($params)
{
    $paramsb = veportal_getveportalaccountinfo($params['serviceid']);
    $params = array_merge($params, $paramsb);
    $api['function'] = 'changepass';
    $post['newpass'] = $params['password'];
    $post['username'] = $params['username'];
    $post['veid'] = $params['veid'];
    $apiResult = veportal_processapi($api, $post, $params);
    veportal_updatepackagenotes($params['serviceid'], "Change Password", "Account password changed to " . $params['password'] . '');
    if( $apiResult['return'] == 'error' )
    {
        if( $apiResult['problem'] == 'wrongip' )
        {
            $result = "Incorrect API IP";
        }
        else
        {
            if( $apiResult['problem'] == 'wrongkey' )
            {
                $result = "Incorrect API Key";
            }
            else
            {
                if( $apiResult['problem'] == 'nolicense' )
                {
                    $result = "vePortal Node Not Licensed. <b>Visit <a href='http://www.veportal.com/'>vePortal</a> To Purchase a License</b>";
                }
            }
        }
    }
    else
    {
        $successful = true;
        $result = 'success';
    }
    return $result;
}
function veportal_ChangePackage($params)
{
    $paramsb = veportal_getveportalaccountinfo($params['serviceid']);
    $params = array_merge($params, $paramsb);
    veportal_updatevpsinfo($params['veid'], $params['ipaddress'], $params['hostname'], $serviceid, $params);
    $api['function'] = 'upgradevps';
    $post['veid'] = $params['veid'];
    $post['package'] = $params['configoption1'];
    $post['ubcset'] = $params['configoption2'];
    $apiResult = veportal_processapi($api, $post, $params);
    veportal_updatepackagenotes($params['serviceid'], "Package Upgrade", "Account package upgraded");
    if( $apiResult['return'] == 'error' )
    {
        if( $apiResult['problem'] == 'wrongip' )
        {
            $result = "Incorrect API IP";
        }
        else
        {
            if( $apiResult['problem'] == 'wrongkey' )
            {
                $result = "Incorrect API Key";
            }
            else
            {
                if( $apiResult['problem'] == 'nolicense' )
                {
                    $result = "vePortal Node Not Licensed. <b>Visit <a href='http://www.veportal.com/'>vePortal</a> To Purchase a License</b>";
                }
            }
        }
    }
    else
    {
        $successful = true;
        $result = 'success';
    }
    return $result;
}
function veportal_ClientArea($params)
{
    global $_LANG;
    if( $params['username'] )
    {
        $code = sprintf("<a href=\"http://%s:2407/login.php?user=%s&pass=%s\" target=\"_blank\">%s</a>", WHMCS_Input_Sanitize::encode($params['serverip']), WHMCS_Input_Sanitize::encode($params['username']), WHMCS_Input_Sanitize::encode($params['password']), $_LANG['veportallogin']);
    }
    else
    {
        $code = "<s>" . $_LANG['veportallogin'] . "</s>";
    }
    return $code;
}
function veportal_AdminLink($params)
{
    $form = sprintf("<form action=\"http://%s:2407/login.php\" method=\"post\" target=\"_blank\">" . "<input type=\"hidden\" name=\"username\" value=\"%s\" />" . "<input type=\"submit\" value=\"%s\" />" . "</form>", WHMCS_Input_Sanitize::encode($params['serverip']), WHMCS_Input_Sanitize::encode($params['serverusername']), "Login to vePortal");
    return $form;
}
function veportal_LoginLink($params)
{
    if( $params['username'] )
    {
        $code = sprintf("<a href=\"http://%s:2407/login.php?user=%s&pass=%s\" target=\"_blank\" class=\"moduleloginlink\">%s</a>", WHMCS_Input_Sanitize::encode($params['serverip']), WHMCS_Input_Sanitize::encode($params['username']), WHMCS_Input_Sanitize::encode($params['password']), "Login to vePortal");
    }
    else
    {
        $code = "<s>Login to vePortal</s>";
    }
    return $code;
}
function veportal_AdminCustomButtonArray()
{
    $buttonarray = array( "Change Username" => 'chusername', "Start VPS" => 'startvps', "Stop VPS" => 'stopvps', "Reboot VPS" => 'rebootvps', "Backup VPS" => 'backupvps', "Reload VPS OS" => 'reloadvps', "Update Resource Usage" => 'updateusage' );
    return $buttonarray;
}
function veportal_reloadvps($params)
{
    $paramsb = veportal_getveportalaccountinfo($params['serviceid']);
    $params = array_merge($params, $paramsb);
    $api['function'] = 'reloadvpsos';
    $post['veid'] = $params['veid'];
    $post['rootpass'] = $params['configoptions']["OS Template"];
    $post['ostemplate'] = $params['password'];
    $apiResult = veportal_processapi($api, $post, $params);
    veportal_updatepackagenotes($params['serviceid'], "Reload VPS OS", "VPS OS Reloaded");
    if( $apiResult['return'] == 'error' )
    {
        if( $apiResult['problem'] == 'wrongip' )
        {
            $result = "Incorrect API IP";
        }
        else
        {
            if( $apiResult['problem'] == 'wrongkey' )
            {
                $result = "Incorrect API Key";
            }
            else
            {
                if( $apiResult['problem'] == 'nolicense' )
                {
                    $result = "vePortal Node Not Licensed. <b>Visit <a href='http://www.veportal.com/'>vePortal</a> To Purchase a License</b>";
                }
            }
        }
    }
    else
    {
        $successful = true;
        $result = 'success';
    }
    return $result;
}
function veportal_backupvps($params)
{
    $paramsb = veportal_getveportalaccountinfo($params['serviceid']);
    $params = array_merge($params, $paramsb);
    $api['function'] = 'backupvps';
    $post['veid'] = $params['veid'];
    $apiResult = veportal_processapi($api, $post, $params);
    veportal_updatepackagenotes($params['serviceid'], "Backup VPS", "VPS Backup Creation");
    if( $apiResult['return'] == 'error' )
    {
        if( $apiResult['problem'] == 'wrongip' )
        {
            $result = "Incorrect API IP";
        }
        else
        {
            if( $apiResult['problem'] == 'wrongkey' )
            {
                $result = "Incorrect API Key";
            }
            else
            {
                if( $apiResult['problem'] == 'nolicense' )
                {
                    $result = "vePortal Node Not Licensed. <b>Visit <a href='http://www.veportal.com/'>vePortal</a> To Purchase a License</b>";
                }
            }
        }
    }
    else
    {
        $successful = true;
        $result = 'success';
    }
    return $result;
}
function veportal_startvps($params)
{
    $paramsb = veportal_getveportalaccountinfo($params['serviceid']);
    $params = array_merge($params, $paramsb);
    $api['function'] = 'commandvps';
    $post['veid'] = $params['veid'];
    $post['command'] = 'start';
    $apiResult = veportal_processapi($api, $post, $params);
    veportal_updatepackagenotes($params['serviceid'], "Start VPS", "VPS Started");
    if( $apiResult['return'] == 'error' )
    {
        if( $apiResult['problem'] == 'wrongip' )
        {
            $result = "Incorrect API IP";
        }
        else
        {
            if( $apiResult['problem'] == 'wrongkey' )
            {
                $result = "Incorrect API Key";
            }
            else
            {
                if( $apiResult['problem'] == 'nolicense' )
                {
                    $result = "vePortal Node Not Licensed. <b>Visit <a href='http://www.veportal.com/'>vePortal</a> To Purchase a License</b>";
                }
            }
        }
    }
    else
    {
        $successful = true;
        $result = 'success';
    }
    return $result;
}
function veportal_stopvps($params)
{
    $paramsb = veportal_getveportalaccountinfo($params['serviceid']);
    $params = array_merge($params, $paramsb);
    $api['function'] = 'commandvps';
    $post['veid'] = $params['veid'];
    $post['command'] = 'stop';
    $apiResult = veportal_processapi($api, $post, $params);
    veportal_updatepackagenotes($params['serviceid'], "Stop VPS", "VPS Stopped");
    if( $apiResult['return'] == 'error' )
    {
        if( $apiResult['problem'] == 'wrongip' )
        {
            $result = "Incorrect API IP";
        }
        else
        {
            if( $apiResult['problem'] == 'wrongkey' )
            {
                $result = "Incorrect API Key";
            }
            else
            {
                if( $apiResult['problem'] == 'nolicense' )
                {
                    $result = "vePortal Node Not Licensed. <b>Visit <a href='http://www.veportal.com/'>vePortal</a> To Purchase a License</b>";
                }
            }
        }
    }
    else
    {
        $successful = true;
        $result = 'success';
    }
    return $result;
}
function veportal_rebootvps($params)
{
    $paramsb = veportal_getveportalaccountinfo($params['serviceid']);
    $params = array_merge($params, $paramsb);
    $api['function'] = 'commandvps';
    $post['veid'] = $params['veid'];
    $post['command'] = 'restart';
    $apiResult = veportal_processapi($api, $post, $params);
    veportal_updatepackagenotes($params['serviceid'], "Reboot VPS", "VPS Restarted");
    if( $apiResult['return'] == 'error' )
    {
        if( $apiResult['problem'] == 'wrongip' )
        {
            $result = "Incorrect API IP";
        }
        else
        {
            if( $apiResult['problem'] == 'wrongkey' )
            {
                $result = "Incorrect API Key";
            }
            else
            {
                if( $apiResult['problem'] == 'nolicense' )
                {
                    $result = "vePortal Node Not Licensed. <b>Visit <a href='http://www.veportal.com/'>vePortal</a> To Purchase a License</b>";
                }
            }
        }
    }
    else
    {
        $successful = true;
        $result = 'success';
    }
    return $result;
}
function veportal_chusername($params)
{
    $paramsb = veportal_getveportalaccountinfo($params['serviceid']);
    $params = array_merge($params, $paramsb);
    $api['function'] = 'chusername';
    $post['veid'] = $params['veid'];
    $post['username'] = $params['username'];
    $apiResult = veportal_processapi($api, $post, $params);
    veportal_updatepackagenotes($params['serviceid'], "Change Username", "vePortal Username Changed To " . $post['username'] . '');
    if( $apiResult['return'] == 'error' )
    {
        if( $apiResult['problem'] == 'wrongip' )
        {
            $result = "Incorrect API IP";
        }
        else
        {
            if( $apiResult['problem'] == 'wrongkey' )
            {
                $result = "Incorrect API Key";
            }
            else
            {
                if( $apiResult['problem'] == 'nolicense' )
                {
                    $result = "vePortal Node Not Licensed. <b>Visit <a href='http://www.veportal.com/'>vePortal</a> To Purchase a License</b>";
                }
            }
        }
    }
    else
    {
        $successful = true;
        $result = 'success';
    }
    return $result;
}
function veportal_updateusage($params)
{
    $paramsb = veportal_getveportalaccountinfo($params['serviceid']);
    $params = array_merge($params, $paramsb);
    $api['function'] = 'getvmusage';
    $post['veid'] = $params['veid'];
    $apiResult = veportal_processapi($api, $post, $params);
    $hdd = $apiResult['hdd'] * 1024;
    $hdd = number_format($hdd, 0, ".", '');
    $bw = $apiResult['bw'] * 1024;
    $bw = number_format($bw, 0, ".", '');
    $get = select_query('tblhosting', '', array( 'id' => (int) $params['serviceid'] ));
    $r = mysql_fetch_array($get);
    $currentbw = $r['bwusage'];
    $currenthdd = $r['diskusage'];
    $hdd = $currenthdd + $hdd;
    $bw = $currentbw + $bw;
    update_query('tblhosting', array( 'bwusage' => $bw, 'diskusage' => $hdd ), array( 'id' => (int) $params['serviceid'] ));
    if( $apiResult['return'] == 'error' )
    {
        if( $apiResult['problem'] == 'wrongip' )
        {
            $result = "Incorrect API IP";
        }
        else
        {
            if( $apiResult['problem'] == 'wrongkey' )
            {
                $result = "Incorrect API Key";
            }
            else
            {
                if( $apiResult['problem'] == 'nolicense' )
                {
                    $result = "vePortal Node Not Licensed. <b>Visit <a href='http://www.veportal.com/'>vePortal</a> To Purchase a License</b>";
                }
            }
        }
    }
    else
    {
        $successful = true;
        $result = 'success';
    }
    return $result;
}