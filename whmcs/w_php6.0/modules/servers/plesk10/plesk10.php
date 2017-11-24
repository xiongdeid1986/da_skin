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
/**
 * WHMCS Plesk 10 Module
 *
 * @package     WHMCS
 * @subpackage  modules.servers.plesk10
 * @copyright   Copyright (c) WHMCS Limited 2005-2012
 * @license     http://www.whmcs.com/license/ WHMCS Eula
 * @link        http://www.whmcs.com/ WHMCS
 */
function plesk10_ConfigOptions()
{
    $configarray = array( "Service Plan Name" => array( 'Type' => 'text', 'Size' => '25' ), "Reseller Plan Name" => array( 'Type' => 'text', 'Size' => '25' ) );
    return $configarray;
}
function plesk10_ClientArea($params)
{
    global $_LANG;
    $domain = $params['serverhostname'] ? $params['serverhostname'] : $params['serverip'];
    $port = $params['serveraccesshash'] ? $params['serveraccesshash'] : '8443';
    $secure = $params['serversecure'] ? 'https' : 'http';
    $result = select_query('tblhosting', 'username,password', array( 'server' => $params['serverid'], 'userid' => $params['clientsdetails']['userid'], 'domainstatus' => 'Active' ), 'id', 'ASC');
    $data = mysql_fetch_array($result);
    $form = sprintf("<form action=\"%s://%s:%s/login_up.php3\" method=\"post\" target=\"_blank\">" . "<input type=\"hidden\" name=\"login_name\" value=\"%s\" />" . "<input type=\"hidden\" name=\"passwd\" value=\"%s\" />" . "<input type=\"submit\" value=\"%s\" class=\"button\" />" . "</form>", $secure, WHMCS_Input_Sanitize::encode($domain), WHMCS_Input_Sanitize::encode($port), WHMCS_Input_Sanitize::encode($data['username']), WHMCS_Input_Sanitize::encode(decrypt($data['password'])), $_LANG['plesklogin']);
    return $form;
}
function plesk10_AdminLink($params)
{
    $domain = $params['serverhostname'] ? $params['serverhostname'] : $params['serverip'];
    $port = $params['serveraccesshash'] ? $params['serveraccesshash'] : '8443';
    $secure = $params['serversecure'] ? 'https' : 'http';
    $form = sprintf("<form action=\"%s://%s:%s/login_up.php3\" method=\"post\" target=\"_blank\">" . "<input type=\"hidden\" name=\"login_name\" value=\"%s\" />" . "<input type=\"hidden\" name=\"passwd\" value=\"%s\" />" . "<input type=\"submit\" value=\"%s\" />" . "</form>", $secure, WHMCS_Input_Sanitize::encode($domain), WHMCS_Input_Sanitize::encode($port), WHMCS_Input_Sanitize::encode($params['serverusername']), WHMCS_Input_Sanitize::encode($params['serverpassword']), 'Plesk');
    return $form;
}
function plesk10_CreateAccount($params)
{
    if( $params['type'] == 'reselleraccount' )
    {
        $packet = "<reseller>\n<add>\n<gen-info>";
        if( $params['clientsdetails']['companyname'] )
        {
            $packet .= "<cname>" . $params['clientsdetails']['companyname'] . "</cname>";
        }
        $packet .= "<pname>" . $params['clientsdetails']['firstname'] . " " . $params['clientsdetails']['lastname'] . "</pname>\n<login>" . $params['username'] . "</login>\n<passwd>" . $params['password'] . "</passwd>\n<status>0</status>\n<phone>" . $params['clientsdetails']['phonenumber'] . "</phone>\n<fax/>\n<email>" . $params['clientsdetails']['email'] . "</email>\n<address>" . $params['clientsdetails']['address1'] . "</address>\n<city>" . $params['clientsdetails']['city'] . "</city>\n<state>" . $params['clientsdetails']['state'] . "</state>\n<pcode>" . $params['clientsdetails']['postcode'] . "</pcode>\n<country>" . $params['clientsdetails']['country'] . "</country>\n</gen-info>\n<plan-name>" . $params['configoption2'] . "</plan-name>\n</add>\n</reseller>";
        $result = plesk10_connection($params, $packet);
        if( $result['curlerror'] )
        {
            return $result['curlerror'];
        }
        if( $result['PACKET']['SYSTEM']['ERRCODE'] )
        {
            return "Error Code: " . $result['PACKET']['SYSTEM']['ERRCODE'] . " - " . $result['PACKET']['SYSTEM']['ERRTEXT'];
        }
        if( $result['PACKET']['RESELLER']['ADD']['RESULT']['STATUS'] != 'ok' )
        {
            return "Error Code: " . $result['PACKET']['RESELLER']['ADD']['RESULT']['ERRCODE'] . " - " . $result['PACKET']['RESELLER']['ADD']['RESULT']['ERRTEXT'];
        }
        return 'success';
    }
    $sqlresult = select_query('tblhosting', 'username', array( 'userid' => $params['clientsdetails']['userid'] ));
    while( $data = mysql_fetch_array($sqlresult) )
    {
        $username = $data[0];
        $packet = "<customer>\n<get>\n<filter>\n<login>" . $username . "</login>\n</filter>\n<dataset>\n<gen_info/>\n</dataset>\n</get>\n</customer>";
        $result = plesk10_connection($params, $packet);
        $clientid = $result['PACKET']['CUSTOMER']['GET']['RESULT']['ID'];
        if( $clientid )
        {
            break;
        }
    }
    if( !$clientid )
    {
        $packet = "<customer>\n<add>\n<gen_info>";
        if( $params['clientsdetails']['companyname'] )
        {
            $packet .= "<cname>" . $params['clientsdetails']['companyname'] . "</cname>";
        }
        $packet .= "<pname>" . $params['clientsdetails']['firstname'] . " " . $params['clientsdetails']['lastname'] . "</pname>\n<login>" . $params['username'] . "</login>\n<passwd>" . $params['password'] . "</passwd>\n<status>0</status>\n<phone>" . $params['clientsdetails']['phonenumber'] . "</phone>\n<fax/>\n<email>" . $params['clientsdetails']['email'] . "</email>\n<address>" . $params['clientsdetails']['address1'] . "</address>\n<city>" . $params['clientsdetails']['city'] . "</city>\n<state>" . $params['clientsdetails']['state'] . "</state>\n<pcode>" . $params['clientsdetails']['postcode'] . "</pcode>\n<country>" . $params['clientsdetails']['country'] . "</country>";
        if( $resellerid )
        {
            $packet .= "<owner-id>" . $resellerid . "</owner-id>";
        }
        $packet .= "</gen_info>\n</add>\n</customer>";
        $result = plesk10_connection($params, $packet);
        if( $result['curlerror'] )
        {
            return $result['curlerror'];
        }
        if( $result['PACKET']['SYSTEM']['ERRCODE'] )
        {
            return "Error Code: " . $result['PACKET']['SYSTEM']['ERRCODE'] . " - " . $result['PACKET']['SYSTEM']['ERRTEXT'];
        }
        if( $result['PACKET']['CUSTOMER']['ADD']['RESULT']['STATUS'] != 'ok' )
        {
            return "Error Code: " . $result['PACKET']['CUSTOMER']['ADD']['RESULT']['ERRCODE'] . " - " . $result['PACKET']['CUSTOMER']['ADD']['RESULT']['ERRTEXT'];
        }
        $clientid = $result['PACKET']['CUSTOMER']['ADD']['RESULT']['ID'];
    }
    $packet = "<ip><get/></ip>";
    $result = plesk10_connection($params, $packet);
    if( $result['curlerror'] )
    {
        return $result['curlerror'];
    }
    $ipaddress = '';
    foreach( $result['PACKET']['IP']['GET']['RESULT']['ADDRESSES'] as $ipdata )
    {
        if( $ipdata['TYPE'] == 'shared' )
        {
            $ipaddress = $ipdata['IP_ADDRESS'];
            break;
        }
    }
    if( !$ipaddress )
    {
        $ipaddress = $params['serverip'];
    }
    $packet = "<webspace>\n<add>\n<gen_setup>\n<name>" . $params['domain'] . "</name>\n<owner-id>" . $clientid . "</owner-id>\n<ip_address>" . $ipaddress . "</ip_address>\n<htype>vrt_hst</htype>\n<status>0</status>\n</gen_setup>\n<hosting>\n<vrt_hst>\n<property>\n<name>ftp_login</name>\n<value>" . $params['username'] . "</value>\n</property>\n<property>\n<name>ftp_password</name>\n<value>" . $params['password'] . "</value>\n</property>\n<ip_address>" . $ipaddress . "</ip_address>\n</vrt_hst>\n</hosting>\n<prefs>\n<www>true</www>\n</prefs>\n<plan-name>" . $params['configoption1'] . "</plan-name>\n</add>\n</webspace>";
    $result = plesk10_connection($params, $packet);
    if( $result['curlerror'] )
    {
        return $result['curlerror'];
    }
    if( $result['PACKET']['SYSTEM']['ERRCODE'] )
    {
        return "Error Code: " . $result['PACKET']['SYSTEM']['ERRCODE'] . " - " . $result['PACKET']['SYSTEM']['ERRTEXT'];
    }
    if( $result['PACKET']['WEBSPACE']['ADD']['RESULT']['STATUS'] != 'ok' )
    {
        return "Error Code: " . $result['PACKET']['WEBSPACE']['ADD']['RESULT']['ERRCODE'] . " - " . $result['PACKET']['WEBSPACE']['ADD']['RESULT']['ERRTEXT'];
    }
    return 'success';
}
function plesk10_SuspendAccount($params)
{
    $suspendstatus = 16;
    if( $params['serverusername'] != 'root' && $params['serverusername'] != 'admin' )
    {
        $suspendstatus = 32;
    }
    if( $params['type'] == 'reselleraccount' )
    {
        $packet = "<reseller>\n<set>\n<filter>\n<login>" . $params['username'] . "</login>\n</filter>\n<values>\n<gen-info>\n<status>" . $suspendstatus . "</status>\n</gen-info>\n</values>\n</set>\n</reseller>";
        $result = plesk10_connection($params, $packet);
        if( $result['curlerror'] )
        {
            return $result['curlerror'];
        }
        if( $result['PACKET']['SYSTEM']['ERRCODE'] )
        {
            return "Error Code: " . $result['PACKET']['SYSTEM']['ERRCODE'] . " - " . $result['PACKET']['SYSTEM']['ERRTEXT'];
        }
        if( $result['PACKET']['RESELLER']['SET']['RESULT']['STATUS'] != 'ok' )
        {
            return "Error Code: " . $result['PACKET']['RESELLER']['SET']['RESULT']['ERRCODE'] . " - " . $result['PACKET']['RESELLER']['SET']['RESULT']['ERRTEXT'];
        }
    }
    else
    {
        $packet = "<webspace>\n<set>\n<filter>\n<name>" . $params['domain'] . "</name>\n</filter>\n<values>\n<gen_setup>\n<status>" . $suspendstatus . "</status>\n</gen_setup>\n</values>\n</set>\n</webspace>";
        $result = plesk10_connection($params, $packet);
        if( $result['curlerror'] )
        {
            return $result['curlerror'];
        }
        if( $result['PACKET']['SYSTEM']['ERRCODE'] )
        {
            return "Error Code: " . $result['PACKET']['SYSTEM']['ERRCODE'] . " - " . $result['PACKET']['SYSTEM']['ERRTEXT'];
        }
        if( $result['PACKET']['WEBSPACE']['SET']['RESULT']['STATUS'] != 'ok' )
        {
            return "Error Code: " . $result['PACKET']['WEBSPACE']['SET']['RESULT']['ERRCODE'] . " - " . $result['PACKET']['WEBSPACE']['SET']['RESULT']['ERRTEXT'];
        }
    }
    return 'success';
}
function plesk10_UnsuspendAccount($params)
{
    if( $params['type'] == 'reselleraccount' )
    {
        $packet = "<reseller>\n<set>\n<filter>\n<login>" . $params['username'] . "</login>\n</filter>\n<values>\n<gen-info>\n<status>0</status>\n</gen-info>\n</values>\n</set>\n</reseller>";
        $result = plesk10_connection($params, $packet);
        if( $result['curlerror'] )
        {
            return $result['curlerror'];
        }
        if( $result['PACKET']['SYSTEM']['ERRCODE'] )
        {
            return "Error Code: " . $result['PACKET']['SYSTEM']['ERRCODE'] . " - " . $result['PACKET']['SYSTEM']['ERRTEXT'];
        }
        if( $result['PACKET']['RESELLER']['SET']['RESULT']['STATUS'] != 'ok' )
        {
            return "Error Code: " . $result['PACKET']['RESELLER']['SET']['RESULT']['ERRCODE'] . " - " . $result['PACKET']['RESELLER']['SET']['RESULT']['ERRTEXT'];
        }
    }
    else
    {
        $packet = "<webspace>\n<set>\n<filter>\n<name>" . $params['domain'] . "</name>\n</filter>\n<values>\n<gen_setup>\n<status>0</status>\n</gen_setup>\n</values>\n</set>\n</webspace>";
        $result = plesk10_connection($params, $packet);
        if( $result['curlerror'] )
        {
            return $result['curlerror'];
        }
        if( $result['PACKET']['SYSTEM']['ERRCODE'] )
        {
            return "Error Code: " . $result['PACKET']['SYSTEM']['ERRCODE'] . " - " . $result['PACKET']['SYSTEM']['ERRTEXT'];
        }
        if( $result['PACKET']['WEBSPACE']['SET']['RESULT']['STATUS'] != 'ok' )
        {
            return "Error Code: " . $result['PACKET']['WEBSPACE']['SET']['RESULT']['ERRCODE'] . " - " . $result['PACKET']['WEBSPACE']['SET']['RESULT']['ERRTEXT'];
        }
    }
    return 'success';
}
function plesk10_TerminateAccount($params)
{
    if( $params['type'] == 'reselleraccount' )
    {
        $packet = "<reseller>\n<del>\n<filter>\n<login>" . $params['username'] . "</login>\n</filter>\n</del>\n</reseller>";
        $result = plesk10_connection($params, $packet);
        if( $result['curlerror'] )
        {
            return $result['curlerror'];
        }
        if( $result['PACKET']['SYSTEM']['ERRCODE'] )
        {
            return "Error Code: " . $result['PACKET']['SYSTEM']['ERRCODE'] . " - " . $result['PACKET']['SYSTEM']['ERRTEXT'];
        }
        if( $result['PACKET']['RESELLER']['DEL']['RESULT']['STATUS'] != 'ok' )
        {
            return "Error Code: " . $result['PACKET']['RESELLER']['DEL']['RESULT']['ERRCODE'] . " - " . $result['PACKET']['RESELLER']['DEL']['RESULT']['ERRTEXT'];
        }
    }
    else
    {
        $packet = "<webspace>\n<del>\n<filter>\n<name>" . $params['domain'] . "</name>\n</filter>\n</del>\n</webspace>";
        $result = plesk10_connection($params, $packet);
        if( $result['curlerror'] )
        {
            return $result['curlerror'];
        }
        if( $result['PACKET']['SYSTEM']['ERRCODE'] )
        {
            return "Error Code: " . $result['PACKET']['SYSTEM']['ERRCODE'] . " - " . $result['PACKET']['SYSTEM']['ERRTEXT'];
        }
        if( $result['PACKET']['WEBSPACE']['DEL']['RESULT']['STATUS'] != 'ok' )
        {
            return "Error Code: " . $result['PACKET']['WEBSPACE']['DEL']['RESULT']['ERRCODE'] . " - " . $result['PACKET']['WEBSPACE']['DEL']['RESULT']['ERRTEXT'];
        }
    }
    return 'success';
}
function plesk10_ChangePassword($params)
{
    if( $params['type'] == 'reselleraccount' )
    {
        $packet = "<reseller>\n<set>\n<filter>\n<login>" . $params['username'] . "</login>\n</filter>\n<values>\n<gen-info>\n<passwd>" . $params['password'] . "</passwd>\n</gen-info>\n</values>\n</set>\n</reseller>";
        $result = plesk10_connection($params, $packet);
        if( $result['curlerror'] )
        {
            return $result['curlerror'];
        }
        if( $result['PACKET']['SYSTEM']['ERRCODE'] )
        {
            return "Error Code: " . $result['PACKET']['SYSTEM']['ERRCODE'] . " - " . $result['PACKET']['SYSTEM']['ERRTEXT'];
        }
        if( $result['PACKET']['RESELLER']['SET']['RESULT']['STATUS'] != 'ok' )
        {
            return "Error Code: " . $result['PACKET']['RESELLER']['SET']['RESULT']['ERRCODE'] . " - " . $result['PACKET']['RESELLER']['SET']['RESULT']['ERRTEXT'];
        }
    }
    else
    {
        $packet = "<customer>\n<set>\n<filter>\n<login>" . $params['username'] . "</login>\n</filter>\n<values>\n<gen_info>\n<passwd>" . $params['password'] . "</passwd>\n</gen_info>\n</values>\n</set>\n</customer>";
        $result = plesk10_connection($params, $packet);
        $packet = "<ftp-user>\n<set>\n<filter>\n<name>" . $params['username'] . "</name>\n</filter>\n<values>\n<password>" . $params['password'] . "</password>\n</values>\n</set>\n</ftp-user>";
        $result = plesk10_connection($params, $packet);
        if( $result['curlerror'] )
        {
            return $result['curlerror'];
        }
        if( $result['PACKET']['SYSTEM']['ERRCODE'] )
        {
            return "Error Code: " . $result['PACKET']['SYSTEM']['ERRCODE'] . " - " . $result['PACKET']['SYSTEM']['ERRTEXT'];
        }
        if( $result['PACKET']['FTP-USER']['SET']['RESULT']['STATUS'] != 'ok' )
        {
            return "Error Code: " . $result['PACKET']['FTP-USER']['SET']['RESULT']['ERRCODE'] . " - " . $result['PACKET']['FTP-USER']['SET']['RESULT']['ERRTEXT'];
        }
    }
    return 'success';
}
function plesk10_ChangePackage($params)
{
    $packet = "<service-plan>\n<get>\n<filter>\n<name>" . $params['configoption1'] . "</name>\n</filter>\n</get>\n</service-plan>";
    $result = plesk10_connection($params, $packet);
    if( $result['curlerror'] )
    {
        return $result['curlerror'];
    }
    if( $result['PACKET']['SYSTEM']['ERRCODE'] )
    {
        return "Error Code: " . $result['PACKET']['SYSTEM']['ERRCODE'] . " - " . $result['PACKET']['SYSTEM']['ERRTEXT'];
    }
    if( $result['PACKET']['SERVICE-PLAN']['GET']['RESULT']['STATUS'] != 'ok' )
    {
        return "Error Code: " . $result['PACKET']['SERVICE-PLAN']['GET']['RESULT']['ERRCODE'] . " - " . $result['PACKET']['SERVICE-PLAN']['GET']['RESULT']['ERRTEXT'];
    }
    $guid = $result['PACKET']['SERVICE-PLAN']['GET']['RESULT']['GUID'];
    $packet = "<webspace>\n<switch-subscription>\n<filter>\n<name>" . $params['domain'] . "</name>\n</filter>\n<plan-guid>" . $guid . "</plan-guid>\n</switch-subscription>\n</webspace>";
    $result = plesk10_connection($params, $packet);
    if( $result['curlerror'] )
    {
        return $result['curlerror'];
    }
    if( $result['PACKET']['SYSTEM']['ERRCODE'] )
    {
        return "Error Code: " . $result['PACKET']['SYSTEM']['ERRCODE'] . " - " . $result['PACKET']['SYSTEM']['ERRTEXT'];
    }
    if( $result['PACKET']['WEBSPACE']['SWITCH-SUBSCRIPTION']['RESULT']['STATUS'] != 'ok' )
    {
        return "Error Code: " . $result['PACKET']['WEBSPACE']['SWITCH-SUBSCRIPTION']['RESULT']['ERRCODE'] . " - " . $result['PACKET']['WEBSPACE']['SWITCH-SUBSCRIPTION']['RESULT']['ERRTEXT'];
    }
    return 'success';
}
function plesk10_connection($params, $packet)
{
    global $plesk10packetversion;
    if( !$plesk10packetversion )
    {
        $plesk10packetversion = "1.6.3.0";
    }
    $secure = $params['serversecure'] ? 'https' : 'http';
    $hostname = $params['serverhostname'] ? $params['serverhostname'] : $params['serverip'];
    $port = $params['serveraccesshash'] ? $params['serveraccesshash'] : '8443';
    $url = $secure . "://" . $hostname . ":" . $port . "/enterprise/control/agent.php";
    $headers = array( "HTTP_AUTH_LOGIN: " . $params['serverusername'], "HTTP_AUTH_PASSWD: " . $params['serverpassword'], "Content-Type: text/xml" );
    $packet = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><packet version=\"" . $plesk10packetversion . "\">" . $packet . "</packet>";
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
    logModuleCall('plesk10', $params['action'], $packet, $retval, $result);
    return $result;
}