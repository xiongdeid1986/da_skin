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
function plesk8_ConfigOptions()
{
    $configarray = array( "Client Template Name" => array( 'Type' => 'text', 'Size' => '25' ), "Domain Template Name" => array( 'Type' => 'text', 'Size' => '25' ), "IP Address" => array( 'Type' => 'text', 'Size' => '20', 'Description' => "Only required if instructed by WHMCS Support" ) );
    return $configarray;
}
function plesk8_ClientArea($params)
{
    global $_LANG;
    if( $params['serverhostname'] )
    {
        $domain = $params['serverhostname'];
    }
    else
    {
        $domain = $params['serverip'];
    }
    $form = sprintf("<form action=\"https://%s:8443/login_up.php3\" method=\"post\" target=\"_blank\">" . "<input type=\"hidden\" name=\"login_name\" value=\"%s\" />" . "<input type=\"hidden\" name=\"passwd\" value=\"%s\" />" . "<input type=\"submit\" value=\"%s\" class=\"button\" />" . "</form>", WHMCS_Input_Sanitize::encode($domain), WHMCS_Input_Sanitize::encode($params['username']), WHMCS_Input_Sanitize::encode($params['password']), $_LANG['plesklogin']);
    return $form;
}
function plesk8_AdminLink($params)
{
    if( $params['serverhostname'] )
    {
        $domain = $params['serverhostname'];
    }
    else
    {
        $domain = $params['serverip'];
    }
    $form = sprintf("<form action=\"https://%s:8443/login_up.php3\" method=\"post\" target=\"_blank\">" . "<input type=\"hidden\" name=\"login_name\" value=\"%s\" />" . "<input type=\"hidden\" name=\"passwd\" value=\"%s\" />" . "<input type=\"submit\" value=\"%s\" />" . "</form>", WHMCS_Input_Sanitize::encode($domain), WHMCS_Input_Sanitize::encode($params['serverusername']), WHMCS_Input_Sanitize::encode($params['serverpassword']), 'Plesk');
    return $form;
}
function plesk8_CreateAccount($params)
{
    global $clientid;
    if( $params['clientsdetails']['country'] == 'UK' )
    {
        $params['clientsdetails']['country'] = 'GB';
    }
    $packet = "\n<client>\n<add>\n<gen_info>\n";
    if( $params['clientsdetails']['companyname'] )
    {
        $packet .= "<cname>" . $params['clientsdetails']['companyname'] . "</cname>";
    }
    $packet .= "<pname>" . $params['clientsdetails']['firstname'] . " " . $params['clientsdetails']['lastname'] . " " . $params['serviceid'] . "</pname>\n<login>" . $params['username'] . "</login>\n<passwd>" . $params['password'] . "</passwd>\n<status>0</status>\n<phone>" . $params['clientsdetails']['phonenumber'] . "</phone>\n<fax/>\n<email>" . $params['clientsdetails']['email'] . "</email>\n<address>" . $params['clientsdetails']['address1'] . "</address>\n<city>" . $params['clientsdetails']['city'] . "</city>\n<state>" . $params['clientsdetails']['state'] . "</state>\n<pcode>" . $params['clientsdetails']['postcode'] . "</pcode>\n<country>" . $params['clientsdetails']['country'] . "</country>\n</gen_info>\n<template-name>" . $params['configoption1'] . "</template-name>\n</add>\n</client>\n";
    $result = plesk8_connection($params, $packet);
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
    if( $params['configoption3'] )
    {
        $ipaddress = $params['configoption3'];
        $packet = "\n<client>\n<ippool_add_ip>\n<client_id>" . $clientid . "</client_id>\n<ip_address>" . $ipaddress . "</ip_address>\n</ippool_add_ip>\n</client>\n";
        $result = plesk8_connection($params, $packet);
        if( $result['curlerror'] )
        {
            return $result['curlerror'];
        }
    }
    else
    {
        $packet = "\n<client>\n    <get>\n        <filter>\n            <id>" . $clientid . "</id>\n        </filter>\n        <dataset>\n            <ippool/>\n        </dataset>\n    </get>\n</client>\n";
        $result = plesk8_connection($params, $packet);
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
            $ipaddress = $result['PACKET']['CLIENT']['GET']['RESULT']['DATA']['IPPOOL']['IP_ADDRESS'];
        }
    }
    $packet = "\n<domain>\n    <add>\n        <gen_setup>\n            <name>" . $params['domain'] . "</name>\n            <client_id>" . $clientid . "</client_id>\n            <ip_address>" . $ipaddress . "</ip_address>\n            <htype>vrt_hst</htype>\n            <status>0</status>\n        </gen_setup>\n        <hosting>\n            <vrt_hst>\n                <ftp_login>" . $params['username'] . "</ftp_login>\n                <ftp_password>" . $params['password'] . "</ftp_password>\n                <ip_address>" . $ipaddress . "</ip_address>\n            </vrt_hst>\n        </hosting>\n        <prefs>\n            <www>true</www>\n        </prefs>\n        <user>\n            <enabled>true</enabled>\n            <password>" . $params['password'] . "</password>\n        </user>\n        <template-name>" . $params['configoption2'] . "</template-name>\n    </add>\n</domain>\n";
    $result = plesk8_connection($params, $packet);
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
function plesk8_TerminateAccount($params)
{
    $packet = "<client>\n<del>\n<filter>\n<login>" . $params['username'] . "</login>\n</filter>\n</del>\n</client>";
    $result = plesk8_connection($params, $packet);
    if( $result['curlerror'] )
    {
        return $result['curlerror'];
    }
    if( $result[PACKET][SYSTEM][ERRCODE] )
    {
        return $result[PACKET][SYSTEM][ERRCODE] . " - " . $result[PACKET][SYSTEM][ERRTEXT];
    }
    if( $result['PACKET']['CLIENT']['DEL']['RESULT']['STATUS'] == 'error' )
    {
        return $result['PACKET']['CLIENT']['DEL']['RESULT']['ERRCODE'] . " - " . $result['PACKET']['CLIENT']['DEL']['RESULT']['ERRTEXT'];
    }
    return 'success';
}
function plesk8_SuspendAccount($params)
{
    $packet = "<client>\n<set>\n<filter>\n<login>" . $params['username'] . "</login>\n</filter>\n<values>\n<gen_info>\n<status>16</status>\n</gen_info>\n</values>\n</set>\n</client>";
    $result = plesk8_connection($params, $packet);
    if( $result['curlerror'] )
    {
        return $result['curlerror'];
    }
    if( $result[PACKET][SYSTEM][ERRCODE] )
    {
        return $result[PACKET][SYSTEM][ERRCODE] . " - " . $result[PACKET][SYSTEM][ERRTEXT];
    }
    if( $result['PACKET']['CLIENT']['SET']['RESULT']['STATUS'] == 'error' )
    {
        return $result['PACKET']['CLIENT']['SET']['RESULT']['ERRCODE'] . " - " . $result['PACKET']['CLIENT']['SET']['RESULT']['ERRTEXT'];
    }
    return 'success';
}
function plesk8_UnsuspendAccount($params)
{
    $packet = "<client>\n<set>\n<filter>\n<login>" . $params['username'] . "</login>\n</filter>\n<values>\n<gen_info>\n<status>0</status>\n</gen_info>\n</values>\n</set>\n</client>";
    $result = plesk8_connection($params, $packet);
    if( $result['curlerror'] )
    {
        return $result['curlerror'];
    }
    if( $result[PACKET][SYSTEM][ERRCODE] )
    {
        return $result[PACKET][SYSTEM][ERRCODE] . " - " . $result[PACKET][SYSTEM][ERRTEXT];
    }
    if( $result['PACKET']['CLIENT']['SET']['RESULT']['STATUS'] == 'error' )
    {
        return $result['PACKET']['CLIENT']['SET']['RESULT']['ERRCODE'] . " - " . $result['PACKET']['CLIENT']['SET']['RESULT']['ERRTEXT'];
    }
    return 'success';
}
function plesk8_ChangePassword($params)
{
    $packet = "<client>\n<set>\n<filter>\n<login>" . $params['username'] . "</login>\n</filter>\n<values>\n<gen_info>\n<passwd>" . $params['password'] . "</passwd>\n</gen_info>\n</values>\n</set>\n</client>";
    $result = plesk8_connection($params, $packet);
    if( $result['curlerror'] )
    {
        return $result['curlerror'];
    }
    if( $result[PACKET][SYSTEM][ERRCODE] )
    {
        return $result[PACKET][SYSTEM][ERRCODE] . " - " . $result[PACKET][SYSTEM][ERRTEXT];
    }
    if( $result['PACKET']['CLIENT']['SET']['RESULT']['STATUS'] == 'error' )
    {
        return $result['PACKET']['CLIENT']['SET']['RESULT']['ERRCODE'] . " - " . $result['PACKET']['CLIENT']['SET']['RESULT']['ERRTEXT'];
    }
    $packet = "<domain>\n<set>\n<filter>\n<client_login>" . $params['username'] . "</client_login>\n</filter>\n<values>\n<hosting>\n<vrt_hst>\n<ftp_password>" . $params['password'] . "</ftp_password>\n</vrt_hst>\n</hosting>\n</values>\n</set>\n</domain>";
    $result = plesk8_connection($params, $packet);
    if( $result['curlerror'] )
    {
        return $result['curlerror'];
    }
    if( $result[PACKET][SYSTEM][ERRCODE] )
    {
        return $result[PACKET][SYSTEM][ERRCODE] . " - " . $result[PACKET][SYSTEM][ERRTEXT];
    }
    if( $result['PACKET']['DOMAIN']['SET']['RESULT']['STATUS'] == 'error' )
    {
        return $result['PACKET']['DOMAIN']['SET']['RESULT']['ERRCODE'] . " - " . $result['PACKET']['DOMAIN']['SET']['RESULT']['ERRTEXT'];
    }
    return 'success';
}
function plesk8_connection($params, $packet)
{
    global $clientid;
    global $plesk8packetversion;
    if( !$plesk8packetversion )
    {
        $plesk8packetversion = "1.4.1.0";
    }
    $url = "https://" . $params['serverip'] . ":8443/enterprise/control/agent.php";
    $headers = array( "HTTP_AUTH_LOGIN: " . $params['serverusername'], "HTTP_AUTH_PASSWD: " . $params['serverpassword'], "Content-Type: text/xml" );
    $packet = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><packet version=\"" . $plesk8packetversion . "\">" . $packet . "</packet>";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
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
    logModuleCall('plesk8', $params['action'], $packet, $retval, $result);
    return $result;
}