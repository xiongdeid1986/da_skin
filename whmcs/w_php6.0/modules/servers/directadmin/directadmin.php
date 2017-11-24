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
if( !defined('WHMCS') )
{
    exit( "This file cannot be accessed directly" );
}
global $licensing;
if( defined('DACONFPACKAGEADDONLICENSE') )
{
    exit( "License Hacking Attempt Detected" );
}
define('DACONFPACKAGEADDONLICENSE', $licensing->isActiveAddon("Configurable Package Addon"));
function directadmin_ConfigOptions()
{
    $configarray = array( "Package Name" => array( 'Type' => 'text', 'Size' => '25' ), "Reseller IP" => array( 'Type' => 'dropdown', 'Options' => ',shared,sharedreseller,assign' ), "Dedicated IP" => array( 'Type' => 'yesno', 'Description' => "Tick to Auto-Assign Dedicated IP" ), "Suspend at Limit" => array( 'Type' => 'yesno', 'Description' => "Tick to Auto Suspend Users when reaching Bandwidth Limit" ) );
    return $configarray;
}
function directadmin_ClientArea($params)
{
    global $_LANG;
    $http = $params['serversecure'] ? 'https' : 'http';
    $host = $params['serverhostname'] ? $params['serverhostname'] : $params['serverip'];
    $form = sprintf("<form action=\"%s://%s:2222/CMD_LOGIN\" method=\"post\" target=\"_blank\">" . "<input type=\"hidden\" name=\"username\" value=\"%s\" />" . "<input type=\"hidden\" name=\"password\" value=\"%s\" />" . "<input type=\"submit\" value=\"%s\" class=\"button\" />" . "</form>", $http, WHMCS_Input_Sanitize::encode($host), WHMCS_Input_Sanitize::encode($params['username']), WHMCS_Input_Sanitize::encode($params['password']), $_LANG['directadminlogin']);
    return $form;
}
function directadmin_AdminLink($params)
{
    $http = $params['serversecure'] ? 'https' : 'http';
    $host = $params['serverhostname'] ? $params['serverhostname'] : $params['serverip'];
    $form = sprintf("<form action=\"%s://%s:2222/CMD_LOGIN\" method=\"post\" target=\"_blank\">" . "<input type=\"hidden\" name=\"username\" value=\"%s\" />" . "<input type=\"hidden\" name=\"password\" value=\"%s\" />" . "<input type=\"submit\" value=\"%s\" />" . "</form>", $http, WHMCS_Input_Sanitize::encode($host), WHMCS_Input_Sanitize::encode($params['serverusername']), WHMCS_Input_Sanitize::encode($params['serverpassword']), 'DirectAdmin');
    return $form;
}
function directadmin_CreateAccount($params)
{
    $fields = array(  );
    $ip = $params['serverip'];
    if( $params['configoption3'] || DACONFPACKAGEADDONLICENSE && $params['configoption1'] == 'Custom' && $params['configoptions']["Dedicated IP"] )
    {
        $command = 'CMD_API_SHOW_RESELLER_IPS';
        $params['getip'] = true;
        $fields['action'] = 'all';
        $results = directadmin_req($command, $fields, $params);
        foreach( $results as $ipaddress => $details )
        {
            if( $details['status'] == 'free' )
            {
                $ip = $ipaddress;
                break;
            }
        }
        update_query('tblhosting', array( 'dedicatedip' => $ip ), array( 'id' => $params['serviceid'] ));
    }
    $params['getip'] = '';
    if( DACONFPACKAGEADDONLICENSE && $params['configoption1'] == 'Custom' )
    {
        $command = 'CMD_API_ACCOUNT_USER';
        $fields['action'] = 'create';
        $fields['add'] = 'Submit';
        $fields['username'] = $params['username'];
        $fields['email'] = $params['clientsdetails']['email'];
        $fields['passwd'] = $params['password'];
        $fields['passwd2'] = $params['password'];
        $fields['domain'] = $params['domain'];
        $fields['ip'] = $ip;
        $fields['notify'] = 'no';
        if( $params['configoption4'] )
        {
            $fields['suspend_at_limit'] = 'ON';
        }
        if( $params['configoptions']["Disk Space"] )
        {
            $fields['quota'] = $params['configoptions']["Disk Space"];
        }
        if( $params['configoptions']['Bandwidth'] )
        {
            $fields['bandwidth'] = $params['configoptions']['Bandwidth'];
        }
        if( $params['configoptions']["FTP Accounts"] )
        {
            $fields['ftp'] = $params['configoptions']["FTP Accounts"];
        }
        else
        {
            $fields['uftp'] = 'ON';
        }
        if( $params['configoptions']["Email Accounts"] )
        {
            $fields['nemails'] = $params['configoptions']["Email Accounts"];
        }
        else
        {
            $fields['unemails'] = 'ON';
        }
        if( $params['configoptions']["MySQL Databases"] )
        {
            $fields['mysql'] = $params['configoptions']["MySQL Databases"];
        }
        else
        {
            $fields['umysql'] = 'ON';
        }
        if( $params['configoptions']['Subdomains'] )
        {
            $fields['nsubdomains'] = $params['configoptions']['Subdomains'];
        }
        else
        {
            $fields['unsubdomains'] = 'ON';
        }
        if( $params['configoptions']["Parked Domains"] )
        {
            $fields['domainptr'] = $params['configoptions']["Parked Domains"];
        }
        else
        {
            $fields['udomainptr'] = 'ON';
        }
        if( $params['configoptions']["Addon Domains"] )
        {
            $fields['vdomains'] = $params['configoptions']["Addon Domains"];
        }
        else
        {
            $fields['uvdomains'] = 'ON';
        }
        if( $params['configoptions']["CGI Access"] )
        {
            $fields['cgi'] = 'ON';
        }
        else
        {
            $fields['cgi'] = 'OFF';
        }
        if( $params['configoptions']["Shell Access"] )
        {
            $fields['ssh'] = 'ON';
        }
        else
        {
            $fields['ssh'] = 'OFF';
        }
        if( $params['configoptions']["Mailing Lists"] )
        {
            $fields['nemailml'] = $params['configoptions']["Mailing Lists"];
        }
        if( $params['configoptions']['PHP'] )
        {
            $fields['php'] = 'ON';
        }
        else
        {
            $fields['php'] = 'OFF';
        }
        if( $params['configoptions']['SSL'] )
        {
            $fields['ssl'] = 'ON';
        }
        else
        {
            $fields['ssl'] = 'OFF';
        }
        if( $params['configoptions']["System Info"] )
        {
            $fields['sysinfo'] = 'ON';
        }
        else
        {
            $fields['sysinfo'] = 'OFF';
        }
        if( $params['configoptions']["DNS Control"] )
        {
            $fields['dnscontrol'] = 'ON';
        }
        else
        {
            $fields['dnscontrol'] = 'OFF';
        }
        if( $params['configoptions']["Cron Jobs"] )
        {
            $fields['cron'] = 'ON';
        }
        else
        {
            $fields['cron'] = 'OFF';
        }
        if( $params['configoptions']["Catch All"] )
        {
            $fields['catchall'] = 'ON';
        }
        else
        {
            $fields['catchall'] = 'OFF';
        }
        if( $params['configoptions']["Spam Assassin"] )
        {
            $fields['spam'] = 'ON';
        }
        else
        {
            $fields['spam'] = 'OFF';
        }
        if( $params['configoptions']["Anon FTP"] )
        {
            $fields['aftp'] = 'ON';
        }
        else
        {
            $fields['aftp'] = 'OFF';
        }
        if( $params['configoptions']["Email Forwards"] )
        {
            if( is_numeric($params['configoptions']["Email Forwards"]) )
            {
                $fields['nemailf'] = $params['configoptions']["Email Forwards"];
            }
            else
            {
                $fields['unemailf'] = 'ON';
            }
        }
        else
        {
            $fields['unemailf'] = 'OFF';
        }
        if( $params['configoptions']["Mailing Lists"] )
        {
            if( is_numeric($params['configoptions']["Mailing Lists"]) )
            {
                $fields['nemailml'] = $params['configoptions']["Mailing Lists"];
            }
            else
            {
                $fields['nemailml'] = 'ON';
            }
        }
        else
        {
            $fields['nemailml'] = 'OFF';
        }
        if( $params['configoptions']["Auto Responders"] )
        {
            if( is_numeric($params['configoptions']["Auto Responders"]) )
            {
                $fields['nemailr'] = $params['configoptions']["Auto Responders"];
            }
            else
            {
                $fields['unemailr'] = 'ON';
            }
        }
        else
        {
            $fields['unemailr'] = 'OFF';
        }
        $results = directadmin_req($command, $fields, $params);
        if( $results['error'] )
        {
            $result = $results['details'];
        }
        else
        {
            $result = 'success';
        }
        return $result;
    }
    if( $params['type'] == 'hostingaccount' )
    {
        $fields['action'] = 'create';
        $fields['add'] = 'Submit';
        $fields['username'] = $params['username'];
        $fields['email'] = $params['clientsdetails']['email'];
        $fields['passwd'] = $params['password'];
        $fields['passwd2'] = $params['password'];
        $fields['domain'] = $params['domain'];
        $fields['package'] = $params['configoption1'];
        $fields['ip'] = $ip;
        $fields['notify'] = 'no';
        $command = 'CMD_API_ACCOUNT_USER';
    }
    else
    {
        $fields['action'] = 'create';
        $fields['add'] = 'Submit';
        $fields['username'] = $params['username'];
        $fields['email'] = $params['clientsdetails']['email'];
        $fields['passwd'] = $params['password'];
        $fields['passwd2'] = $params['password'];
        $fields['domain'] = $params['domain'];
        $fields['package'] = $params['configoption1'];
        if( $params['configoption2'] == 'sharedreseller' )
        {
            $fields['ip'] = 'sharedreseller';
        }
        else
        {
            if( $params['configoption2'] == 'assign' )
            {
                $fields['ip'] = 'assign';
            }
            else
            {
                $fields['ip'] = 'shared';
            }
        }
        $fields['notify'] = 'no';
        $command = 'CMD_ACCOUNT_RESELLER';
    }
    $results = directadmin_req($command, $fields, $params);
    if( $results['error'] )
    {
        $result = $results['details'];
    }
    else
    {
        $result = 'success';
    }
    return $result;
}
function directadmin_TerminateAccount($params)
{
    $fields = array(  );
    $fields['confirmed'] = 'Confirm';
    $fields['delete'] = 'yes';
    $fields['select0'] = $params['username'];
    $results = directadmin_req('CMD_SELECT_USERS', $fields, $params);
    if( $results['error'] )
    {
        $result = $results['details'];
    }
    else
    {
        $result = 'success';
    }
    return $result;
}
function directadmin_SuspendAccount($params)
{
    $fields = array(  );
    $fields['action'] = 'create';
    $fields['add'] = 'Submit';
    $fields['user'] = $params['username'];
    $results = directadmin_req('CMD_API_SHOW_USER_CONFIG', $fields, $params);
    if( $results['suspended'] == 'yes' )
    {
        $result = "Account is already suspended";
    }
    else
    {
        $fields = array(  );
        $fields['suspend'] = 'Suspend/Unsuspend';
        $fields['select0'] = $params['username'];
        $results = directadmin_req('CMD_SELECT_USERS', $fields, $params);
        if( $results['error'] )
        {
            $result = $results['details'];
        }
        else
        {
            $result = 'success';
        }
    }
    return $result;
}
function directadmin_UnsuspendAccount($params)
{
    $fields = array(  );
    $fields['action'] = 'create';
    $fields['add'] = 'Submit';
    $fields['user'] = $params['username'];
    $results = directadmin_req('CMD_API_SHOW_USER_CONFIG', $fields, $params);
    if( $results['suspended'] == 'no' )
    {
        $result = "Account is not suspended";
    }
    else
    {
        $fields = array(  );
        $fields['suspend'] = 'Suspend/Unsuspend';
        $fields['select0'] = $params['username'];
        $results = directadmin_req('CMD_SELECT_USERS', $fields, $params);
        if( $results['error'] )
        {
            $result = $results['details'];
        }
        else
        {
            $result = 'success';
        }
    }
    return $result;
}
function directadmin_ChangePassword($params)
{
    $fields = array(  );
    $fields['username'] = $params['username'];
    $fields['passwd'] = $params['password'];
    $fields['passwd2'] = $params['password'];
    $results = directadmin_req('CMD_API_USER_PASSWD', $fields, $params, true);
    if( $results['error'] )
    {
        $result = $results['details'];
    }
    else
    {
        $result = 'success';
    }
    return $result;
}
function directadmin_ChangePackage($params)
{
    $fields = array(  );
    $fields['action'] = 'package';
    $fields['user'] = $params['username'];
    $fields['package'] = $params['configoption1'];
    if( $params['type'] == 'reselleraccount' )
    {
        $results = directadmin_req('CMD_API_MODIFY_RESELLER', $fields, $params);
    }
    else
    {
        $results = directadmin_req('CMD_API_MODIFY_USER', $fields, $params);
    }
    if( $results['error'] )
    {
        $result = $results['details'];
    }
    else
    {
        $result = 'success';
    }
    return $result;
}
/**
 * This function obtains the usage data for all the accounts on a server.
 * Split in to two, one to obtain the usage for all user accounts and then
 * continues to obtain usage for reseller accounts.
 * When updating for Reseller accounts, the username for the server is passed
 * as serverusername|resellerusername to run the command as if you were the reseller
 *
 * @param array $params the parameters passed to the function
 */
function directadmin_UsageUpdate($params)
{
    $serverUsername = $params['serverusername'];
    $result = select_query('tblhosting', "domain, username", array( 'server' => $params['serverid'] ), '', '', '', "tblproducts ON tblproducts.id = tblhosting.packageid");
    while( $data = mysql_fetch_assoc($result) )
    {
        $username = $data['username'];
        $domain = $data['domain'];
        $fields = array( 'user' => $username );
        $results = directadmin_req('CMD_API_SHOW_USER_USAGE', $fields, $params);
        $quota = urldecode($results['quota']);
        $bandwidth = urldecode($results['bandwidth']);
        $diskUsed = round($quota);
        $bwUsed = round($bandwidth);
        $results = directadmin_req('CMD_API_SHOW_USER_CONFIG', $fields, $params);
        $quota = urldecode($results['quota']);
        $bandwidth = urldecode($results['bandwidth']);
        $diskLimit = $quota == 'unlimited' ? '0' : round($quota);
        $bwLimit = $bandwidth == 'unlimited' ? '0' : round($bandwidth);
        update_query('tblhosting', array( 'diskusage' => $diskUsed, 'disklimit' => $diskLimit, 'bwusage' => $bwUsed, 'bwlimit' => $bwLimit, 'lastupdate' => "now()" ), array( 'domain' => $domain, 'server' => $params['serverid'] ));
    }
    $result = select_query('tblhosting', "domain, username", array( 'server' => $params['serverid'], 'type' => 'reselleraccount' ), '', '', '', "tblproducts ON tblproducts.id = tblhosting.packageid");
    while( $data = mysql_fetch_array($result) )
    {
        $fields = array(  );
        $username = $data['username'];
        $domain = $data['domain'];
        $fields['user'] = $username;
        $params['serverusername'] = $serverUsername . "|" . $username;
        $results = directadmin_req('CMD_API_RESELLER_STATS', $fields, $params);
        if( $results['error'] === true )
        {
            break;
        }
        $diskLimit = $results['quota'] == 'unlimited' ? '0' : round(urldecode($results['quota']));
        $bwLimit = $results['bandwidth'] == 'unlimited' ? '0' : round(urldecode($results['bandwidth']));
        $fields['type'] = 'usage';
        $results = directadmin_req('CMD_API_RESELLER_STATS', $fields, $params);
        $diskUsed = round(urldecode($results['quota']));
        $bwUsed = round(urldecode($results['bandwidth']));
        update_query('tblhosting', array( 'diskusage' => $diskUsed, 'disklimit' => $diskLimit, 'bwusage' => $bwUsed, 'bwlimit' => $bwLimit, 'lastupdate' => "now()" ), array( 'domain' => $domain, 'server' => $params['serverid'] ));
        unset($fields);
        unset($username);
        unset($domain);
        $params['serverusername'] = $serverUsername;
    }
}
function directadmin_req($command, $fields, $params, $post = '')
{
    $host = $params['serverhostname'] ? $params['serverhostname'] : $params['serverip'];
    $user = $params['serverusername'];
    $pass = $params['serverpassword'];
    $usessl = $params['serversecure'];
    $resultsarray = array(  );
    $fieldstring = '';
    foreach( $fields as $key => $value )
    {
        $fieldstring .= $key . "=" . urlencode($value) . "&";
    }
    $authstr = $user . ":" . $pass;
    $directadminaccterr = '';
    $ch = curl_init();
    if( $usessl )
    {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $url = "https://" . $host . ":2222/" . $command . "?" . $fieldstring;
    }
    else
    {
        $url = "http://" . $host . ":2222/" . $command . "?" . $fieldstring;
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    if( $post )
    {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldstring);
    }
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $curlheaders[0] = "Authorization: Basic " . base64_encode($authstr);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $curlheaders);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    $data = curl_exec($ch);
    if( curl_errno($ch) )
    {
        $resultsarray['error'] = true;
        $resultsarray['details'] = curl_errno($ch) . " - " . curl_error($ch);
        $data = curl_errno($ch) . " - " . curl_error($ch);
    }
    curl_close($ch);
    if( !$resultsarray['error'] )
    {
        if( strpos($data, "DirectAdmin Login") == true )
        {
            $resultsarray = array( 'error' => '1', 'details' => "Login Failed" );
        }
        else
        {
            if( strpos($data, "Your IP is blacklisted") !== false )
            {
                $resultsarray = array( 'error' => '1', 'details' => "WHMCS Host Server IP is Blacklisted" );
            }
            else
            {
                if( $params['getip'] )
                {
                    $data2 = unhtmlentities($data);
                    parse_str($data2, $output);
                    foreach( $output as $key => $value )
                    {
                        $key = str_replace('_', ".", urldecode($key));
                        $value = explode("&", urldecode($value));
                        foreach( $value as $temp )
                        {
                            $temp = explode("=", $temp);
                            $resultsarray[urldecode($key)][$temp[0]] = $temp[1];
                        }
                    }
                }
                else
                {
                    $data = explode("&", $data);
                    foreach( $data as $temp )
                    {
                        $temp = explode("=", $temp);
                        $resultsarray[$temp[0]] = $temp[1];
                    }
                }
            }
        }
    }
    logModuleCall('directadmin', $command, $url, $data, $resultsarray);
    return $resultsarray;
}
function unhtmlentities($string)
{
    return preg_replace("~&#([0-9][0-9])~e", "chr(\\1)", $string);
}