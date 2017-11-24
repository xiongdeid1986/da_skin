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
function plesk9_ConfigOptions()
{
    $configarray = array( "Client Template Name" => array( 'Type' => 'text', 'Size' => '25' ), "Domain Template Name" => array( 'Type' => 'text', 'Size' => '25' ), "Reseller Template Name" => array( 'Type' => 'text', 'Size' => '25' ) );
    return $configarray;
}
function plesk9_ClientArea($params)
{
    global $_LANG;
    $domain = $params['serverhostname'] ? $params['serverhostname'] : $params['serverip'];
    $port = $params['serveraccesshash'] ? $params['serveraccesshash'] : '8443';
    $secure = $params['serversecure'] ? 'https' : 'http';
    $form = sprintf("<form action=\"%s://%s:%s/login_up.php3\" method=\"post\" target=\"_blank\">" . "<input type=\"hidden\" name=\"login_name\" value=\"%s\" />" . "<input type=\"hidden\" name=\"passwd\" value=\"%s\" />" . "<input type=\"submit\" value=\"%s\" class=\"button\" />" . "</form>", $secure, WHMCS_Input_Sanitize::encode($domain), WHMCS_Input_Sanitize::encode($port), WHMCS_Input_Sanitize::encode($params['username']), WHMCS_Input_Sanitize::encode($params['password']), $_LANG['plesklogin']);
    return $form;
}
function plesk9_AdminLink($params)
{
    if( $params['serverhostname'] )
    {
        $domain = $params['serverhostname'];
    }
    else
    {
        $domain = $params['serverip'];
    }
    $port = $params['serveraccesshash'] ? $params['serveraccesshash'] : '8443';
    $secure = $params['serversecure'] ? 'https' : 'http';
    $form = sprintf("<form action=\"%s://%s:%s/login_up.php3\" method=\"post\" target=\"_blank\">" . "<input type=\"hidden\" name=\"login_name\" value=\"%s\" />" . "<input type=\"hidden\" name=\"passwd\" value=\"%s\" />" . "<input type=\"submit\" value=\"%s\" />" . "</form>", $secure, WHMCS_Input_Sanitize::encode($domain), WHMCS_Input_Sanitize::encode($port), WHMCS_Input_Sanitize::encode($params['serverusername']), WHMCS_Input_Sanitize::encode($params['serverpassword']), 'Plesk');
    return $form;
}
function plesk9_CreateAccount($params)
{
    global $clientid;
    if( $params['type'] == 'reselleraccount' )
    {
        $packet = "\n<reseller>\n<add>\n<gen-info>\n<cname>" . $params['clientsdetails']['companyname'] . "</cname>\n<pname>" . $params['clientsdetails']['firstname'] . " " . $params['clientsdetails']['lastname'] . " " . $params['serviceid'] . "</pname>\n<login>" . $params['username'] . "</login>\n<passwd>" . $params['password'] . "</passwd>\n<status>0</status>\n<phone>" . $params['clientsdetails']['phonenumber'] . "</phone>\n<fax/>\n<email>" . $params['clientsdetails']['email'] . "</email>\n<address>" . $params['clientsdetails']['address1'] . "</address>\n<city>" . $params['clientsdetails']['city'] . "</city>\n<state>" . $params['clientsdetails']['state'] . "</state>\n<pcode>" . $params['clientsdetails']['postcode'] . "</pcode>\n<country>" . $params['clientsdetails']['country'] . "</country>\n</gen-info>\n<template-name>" . $params['configoption3'] . "</template-name>\n</add>\n</reseller>\n";
        $result = plesk9_connection($params, $packet);
        if( $result['curlerror'] )
        {
            return $result['curlerror'];
        }
        if( $result['PACKET']['RESELLER']['ADD']['RESULT']['STATUS'] != 'ok' )
        {
            return $result['PACKET']['RESELLER']['ADD']['RESULT']['ERRCODE'] . " - " . $result['PACKET']['RESELLER']['ADD']['RESULT']['ERRTEXT'];
        }
        $resellerid = $clientid = $result['PACKET']['RESELLER']['ADD']['RESULT']['ID'];
        $packet = "\n<reseller>\n    <get>\n        <filter>\n            <id>" . $resellerid . "</id>\n        </filter>\n        <dataset>\n            <ippool/>\n        </dataset>\n    </get>\n</reseller>\n";
        $result = plesk9_connection($params, $packet);
        if( $result['curlerror'] )
        {
            return $result['curlerror'];
        }
        if( $result['PACKET']['RESELLER']['IPPOOL_ADD_IP']['RESULT']['STATUS'] == 'error' )
        {
            return $result['PACKET']['RESELLER']['IPPOOL_ADD_IP']['RESULT']['ERRCODE'] . " - " . $result['PACKET']['RESELLER']['IPPOOL_ADD_IP']['RESULT']['ERRTEXT'];
        }
        $ipaddress = $result['PACKET']['RESELLER']['GET']['RESULT']['DATA']['IPPOOL']['IP-ADDRESS'];
        if( !$ipaddress )
        {
            $ipaddress = $result['PACKET']['RESELLER']['GET']['RESULT']['DATA']['IPPOOL']['IP']['IP-ADDRESS'];
        }
    }
    else
    {
        $packet = "<client>\n<add>\n<gen_info>";
        if( $params['clientsdetails']['companyname'] )
        {
            $packet .= "<cname>" . $params['clientsdetails']['companyname'] . "</cname>";
        }
        $packet .= "<pname>" . $params['clientsdetails']['firstname'] . " " . $params['clientsdetails']['lastname'] . " " . $params['serviceid'] . "</pname>\n<login>" . $params['username'] . "</login>\n<passwd>" . $params['password'] . "</passwd>\n<status>0</status>\n<phone>" . $params['clientsdetails']['phonenumber'] . "</phone>\n<fax/>\n<email>" . $params['clientsdetails']['email'] . "</email>\n<address>" . $params['clientsdetails']['address1'] . "</address>\n<city>" . $params['clientsdetails']['city'] . "</city>\n<state>" . $params['clientsdetails']['state'] . "</state>\n<pcode>" . $params['clientsdetails']['postcode'] . "</pcode>\n<country>" . $params['clientsdetails']['country'] . "</country>";
        if( $resellerid )
        {
            $packet .= "<owner-id>" . $resellerid . "</owner-id>";
        }
        $packet .= "</gen_info>\n<template-name>" . $params['configoption1'] . "</template-name>\n</add>\n</client>";
        $result = plesk9_connection($params, $packet);
        if( $result['curlerror'] )
        {
            return $result['curlerror'];
        }
        if( $result[PACKET][SYSTEM][ERRCODE] )
        {
            return $result[PACKET][SYSTEM][ERRCODE] . " - " . $result[PACKET][SYSTEM][ERRTEXT];
        }
        $clientid = $result['PACKET']['CLIENT']['ADD']['RESULT']['ID'];
        if( strlen($clientid) == 0 )
        {
            return $result['PACKET']['CLIENT']['ADD']['RESULT']['ERRCODE'] . " - " . $result['PACKET']['CLIENT']['ADD']['RESULT']['ERRTEXT'];
        }
        $packet = "\n<client>\n    <get>\n        <filter>\n            <id>" . $clientid . "</id>\n        </filter>\n        <dataset>\n            <ippool/>\n        </dataset>\n    </get>\n</client>\n";
        $result = plesk9_connection($params, $packet);
        if( $result['curlerror'] )
        {
            return $result['curlerror'];
        }
        if( $result['PACKET']['CLIENT']['IPPOOL_ADD_IP']['RESULT']['STATUS'] == 'error' )
        {
            return $result['PACKET']['CLIENT']['IPPOOL_ADD_IP']['RESULT']['ERRCODE'] . " - " . $result['PACKET']['CLIENT']['IPPOOL_ADD_IP']['RESULT']['ERRTEXT'];
        }
        $ipaddress = $result['PACKET']['CLIENT']['GET']['RESULT']['DATA']['IPPOOL']['IP-ADDRESS'];
        if( !$ipaddress )
        {
            $ipaddress = $result['PACKET']['CLIENT']['GET']['RESULT']['DATA']['IPPOOL']['IP']['IP-ADDRESS'];
        }
    }
    $packet = "\n<domain>\n    <add>\n        <gen_setup>\n            <name>" . $params['domain'] . "</name>\n            <owner-id>" . $clientid . "</owner-id>\n            <ip_address>" . $ipaddress . "</ip_address>\n            <htype>vrt_hst</htype>\n            <status>0</status>\n        </gen_setup>\n        <hosting>\n            <vrt_hst>\n                <property>\n                    <name>ftp_login</name>\n                    <value>" . $params['username'] . "</value>\n                </property>\n                <property>\n                    <name>ftp_password</name>\n                    <value>" . $params['password'] . "</value>\n                </property>\n                <ip_address>" . $ipaddress . "</ip_address>\n            </vrt_hst>\n        </hosting>\n        <prefs>\n            <www>true</www>\n        </prefs>\n        <user>\n            <enabled>true</enabled>\n            <password>" . $params['password'] . "</password>\n        </user>\n        <template-name>" . $params['configoption2'] . "</template-name>\n    </add>\n</domain>\n";
    $result = plesk9_connection($params, $packet);
    if( $result['curlerror'] )
    {
        return $result['curlerror'];
    }
    if( $result['PACKET']['SYSTEM']['STATUS'] == 'error' )
    {
        return $result['PACKET']['SYSTEM']['ERRCODE'] . " - " . $result['PACKET']['SYSTEM']['ERRTEXT'];
    }
    if( $result['PACKET']['DOMAIN']['ADD']['RESULT']['STATUS'] == 'error' )
    {
        return $result['PACKET']['DOMAIN']['ADD']['RESULT']['ERRCODE'] . " - " . $result['PACKET']['DOMAIN']['ADD']['RESULT']['ERRTEXT'];
    }
    return 'success';
}
function plesk9_TerminateAccount($params)
{
    if( $params['type'] == 'reselleraccount' )
    {
        $packet = "<reseller>\n<del>\n<filter>\n<login>" . $params['username'] . "</login>\n</filter>\n</del>\n</reseller>";
        $type = 'RESELLER';
    }
    else
    {
        $packet = "<client>\n<del>\n<filter>\n<login>" . $params['username'] . "</login>\n</filter>\n</del>\n</client>";
        $type = 'CLIENT';
    }
    $result = plesk9_connection($params, $packet);
    if( $result['curlerror'] )
    {
        return $result['curlerror'];
    }
    if( $result[PACKET][SYSTEM][ERRCODE] )
    {
        return $result[PACKET][SYSTEM][ERRCODE] . " - " . $result[PACKET][SYSTEM][ERRTEXT];
    }
    if( $result['PACKET'][$type]['DEL']['RESULT']['STATUS'] == 'error' )
    {
        return $result['PACKET'][$type]['DEL']['RESULT']['ERRCODE'] . " - " . $result['PACKET'][$type]['DEL']['RESULT']['ERRTEXT'];
    }
    return 'success';
}
function plesk9_SuspendAccount($params)
{
    if( $params['type'] == 'reselleraccount' )
    {
        $packet = "<reseller>\n<set>\n<filter>\n<login>" . $params['username'] . "</login>\n</filter>\n<values>\n<gen-info>\n<status>16</status>\n</gen-info>\n</values>\n</set>\n</reseller>";
        $type = 'RESELLER';
    }
    else
    {
        $packet = "<client>\n<set>\n<filter>\n<login>" . $params['username'] . "</login>\n</filter>\n<values>\n<gen_info>\n<status>16</status>\n</gen_info>\n</values>\n</set>\n</client>";
        $type = 'CLIENT';
    }
    $result = plesk9_connection($params, $packet);
    if( $result['curlerror'] )
    {
        return $result['curlerror'];
    }
    if( $result[PACKET][SYSTEM][ERRCODE] )
    {
        return $result[PACKET][SYSTEM][ERRCODE] . " - " . $result[PACKET][SYSTEM][ERRTEXT];
    }
    if( $result['PACKET'][$type]['SET']['RESULT']['STATUS'] != 'ok' )
    {
        return $result['PACKET'][$type]['SET']['RESULT']['ERRCODE'] . " - " . $result['PACKET'][$type]['SET']['RESULT']['ERRTEXT'];
    }
    return 'success';
}
function plesk9_UnsuspendAccount($params)
{
    if( $params['type'] == 'reselleraccount' )
    {
        $packet = "<reseller>\n<set>\n<filter>\n<login>" . $params['username'] . "</login>\n</filter>\n<values>\n<gen-info>\n<status>0</status>\n</gen-info>\n</values>\n</set>\n</reseller>";
        $type = 'RESELLER';
    }
    else
    {
        $packet = "<client>\n<set>\n<filter>\n<login>" . $params['username'] . "</login>\n</filter>\n<values>\n<gen_info>\n<status>0</status>\n</gen_info>\n</values>\n</set>\n</client>";
        $type = 'CLIENT';
    }
    $result = plesk9_connection($params, $packet);
    if( $result['curlerror'] )
    {
        return $result['curlerror'];
    }
    if( $result[PACKET][SYSTEM][ERRCODE] )
    {
        return $result[PACKET][SYSTEM][ERRCODE] . " - " . $result[PACKET][SYSTEM][ERRTEXT];
    }
    if( $result['PACKET'][$type]['SET']['RESULT']['STATUS'] == 'error' )
    {
        return $result['PACKET'][$type]['SET']['RESULT']['ERRCODE'] . " - " . $result['PACKET'][$type]['SET']['RESULT']['ERRTEXT'];
    }
    return 'success';
}
function plesk9_ChangePassword($params)
{
    if( $params['type'] == 'reselleraccount' )
    {
        $packet = "<reseller>\n<set>\n<filter>\n<login>" . $params['username'] . "</login>\n</filter>\n<values>\n<gen-info>\n<passwd>" . $params['password'] . "</passwd>\n</gen-info>\n</values>\n</set>\n</reseller>";
        $type = 'RESELLER';
    }
    else
    {
        $packet = "<domain>\n<set>\n<filter>\n<domain-name>" . $params['domain'] . "</domain-name>\n</filter>\n<values>\n<hosting>\n<vrt_hst>\n<property>\n<name>ftp_login</name>\n<value>" . $params['username'] . "</value>\n</property>\n<property>\n<name>ftp_password</name>\n<value>" . $params['password'] . "</value>\n</property>\n</vrt_hst>\n</hosting>\n</values>\n</set>\n</domain>";
        $result = plesk9_connection($params, $packet);
        $packet = "<client>\n<set>\n<filter>\n<login>" . $params['username'] . "</login>\n</filter>\n<values>\n<gen_info>\n<passwd>" . $params['password'] . "</passwd>\n</gen_info>\n</values>\n</set>\n</client>";
        $type = 'CLIENT';
    }
    $result = plesk9_connection($params, $packet);
    if( $result['curlerror'] )
    {
        return $result['curlerror'];
    }
    if( $result[PACKET][SYSTEM][ERRCODE] )
    {
        return $result[PACKET][SYSTEM][ERRCODE] . " - " . $result[PACKET][SYSTEM][ERRTEXT];
    }
    if( $result['PACKET'][$type]['SET']['RESULT']['STATUS'] == 'error' )
    {
        return $result['PACKET'][$type]['SET']['RESULT']['ERRCODE'] . " - " . $result['PACKET'][$type]['SET']['RESULT']['ERRTEXT'];
    }
    return 'success';
}
function plesk9_UsageUpdate($params)
{
    $packet = "<client>\n<get>\n<filter/>\n<dataset>\n<gen_info/>\n<stat/>\n<limits/>\n</dataset>\n</get>\n</client>";
    $result = plesk9_connection($params, $packet);
    foreach( $result['PACKET']['CLIENT']['GET'] as $client )
    {
        foreach( $client as $k => $v )
        {
            if( substr($k, 0, 4) == 'DATA' )
            {
                foreach( $v as $k => $v )
                {
                    if( substr($k, 0, 4) == 'GEN_' )
                    {
                        $username = $v['LOGIN'];
                    }
                    else
                    {
                        if( substr($k, 0, 4) == 'STAT' )
                        {
                            $diskused = $v['DISK_SPACE'];
                            $bwused = $v['TRAFFIC'];
                        }
                        else
                        {
                            if( substr($k, 0, 4) == 'LIMI' )
                            {
                                foreach( $v as $k1 => $v1 )
                                {
                                    if( $v1['NAME'] == 'disk_space' )
                                    {
                                        $disklimit = $v1['VALUE'];
                                    }
                                    if( $v1['NAME'] == 'max_traffic' )
                                    {
                                        $bwlimit = $v1['VALUE'];
                                    }
                                }
                            }
                        }
                    }
                }
                $diskused = is_null($diskused) ? 0 : $diskused / 1024 / 1024;
                $bwused = is_null($bwused) ? 0 : $bwused / 1024 / 1024;
                $disklimit = is_null($disklimit) ? 0 : $disklimit / 1024 / 1024;
                $bwlimit = is_null($diskused) ? 0 : $bwlimit / 1024 / 1024;
                update_query('tblhosting', array( 'diskusage' => $diskused, 'disklimit' => $disklimit, 'bwusage' => $bwused, 'bwlimit' => $bwlimit, 'lastupdate' => "now()" ), array( 'username' => $username, 'server' => $params['serverid'] ));
            }
        }
    }
}
function plesk9_connection($params, $packet)
{
    global $clientid;
    global $pleskpacketversion;
    if( !$pleskpacketversion )
    {
        $pleskpacketversion = "1.5.2.1";
    }
    $secure = $params['serversecure'] ? 'https' : 'http';
    $port = $params['serveraccesshash'] ? $params['serveraccesshash'] : '8443';
    $url = $secure . "://" . $params['serverip'] . ":" . $port . "/enterprise/control/agent.php";
    $headers = array( "HTTP_AUTH_LOGIN: " . $params['serverusername'], "HTTP_AUTH_PASSWD: " . $params['serverpassword'], "Content-Type: text/xml" );
    $packet = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><packet version=\"" . $pleskpacketversion . "\">" . $packet . "</packet>";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $packet);
    $retval = curl_exec($ch);
    if( curl_errno($ch) )
    {
        $result['curlerror'] = "CURL Error: " . curl_errno($ch) . " - " . curl_error($ch);
    }
    else
    {
        $result = XMLtoARRAY($retval);
    }
    curl_close($ch);
    logModuleCall('plesk9', $params['action'], $packet, $retval, $result);
    return $result;
}