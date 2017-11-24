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
function pleskreseller_ConfigOptions()
{
    $configarray = array( "Domain Template Name" => array( 'Type' => 'text', 'Size' => '25' ), "IP Address" => array( 'Type' => 'text', 'Size' => '20' ), "Physical hosting management" => array( 'Type' => 'yesno', 'Description' => "Webspace, bandwidth etc" ), "Manage FTP password" => array( 'Type' => 'yesno', 'Description' => "Changing of FTP password" ), "Management of SSH access to server" => array( 'Type' => 'yesno', 'Description' => "Full SSH access" ), "Management of chrooted SSH access to server" => array( 'Type' => 'yesno', 'Description' => "Chrooted SSH access" ), "Hard disk quota assignment" => array( 'Type' => 'yesno', 'Description' => "Hard disk quota" ), "Subdomains management" => array( 'Type' => 'yesno', 'Description' => "Management of subdomains" ), "Domain aliases management" => array( 'Type' => 'yesno', 'Description' => "Management of domain aliases" ), "Log rotation management" => array( 'Type' => 'yesno', 'Description' => "Management of log rotation" ), "Anonymous FTP management" => array( 'Type' => 'yesno', 'Description' => "Management of anonymous FTP" ), "Scheduler management" => array( 'Type' => 'yesno', 'Description' => "Management of scheduled tasks" ), "DNS zone management" => array( 'Type' => 'yesno', 'Description' => "Management of DNS records" ), "Java applications management" => array( 'Type' => 'yesno', 'Description' => "Management of Tomcat apps" ), "Web statistics management" => array( 'Type' => 'yesno', 'Description' => "Management of web statistics" ), "Mailing lists management" => array( 'Type' => 'yesno', 'Description' => "Management of mailing lists" ), "Spam filter management" => array( 'Type' => 'yesno', 'Description' => "Management of spam filter" ), "Antivirus management" => array( 'Type' => 'yesno', 'Description' => "Management of anti virus" ), "Allow local backups" => array( 'Type' => 'yesno', 'Description' => "Local backups" ), "Allow FTP backups" => array( 'Type' => 'yesno', 'Description' => "FTP backups" ), "Ability to use Sitebuilder" => array( 'Type' => 'yesno', 'Description' => "Access to Sitebuilder admin" ), "Home page management" => array( 'Type' => 'yesno', 'Description' => "Management of Plesk home page" ), "Allow multiple sessions" => array( 'Type' => 'yesno', 'Description' => "Multiple login sessions to Plesk" ) );
    return $configarray;
}
function pleskreseller_ClientArea($params)
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
    $port = $params['serveraccesshash'] ? $params['serveraccesshash'] : '8443';
    $secure = $params['serversecure'] ? 'https' : 'http';
    $form = sprintf("<form action=\"%s://%s:%s/login_up.php3\" method=\"post\" target=\"_blank\">" . "<input type=\"hidden\" name=\"login_name\" value=\"%s\" />" . "<input type=\"hidden\" name=\"passwd\" value=\"%s\" />" . "<input type=\"submit\" value=\"%s\" class=\"button\" />" . "</form>", $secure, WHMCS_Input_Sanitize::encode($domain), WHMCS_Input_Sanitize::encode($port), WHMCS_Input_Sanitize::encode($params['domain']), WHMCS_Input_Sanitize::encode($params['password']), $_LANG['plesklogin']);
    return $form;
}
function pleskreseller_AdminLink($params)
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
function pleskreseller_CreateAccount($params)
{
    global $clientid;
    $ipaddress = $params['configoption2'];
    $params['configoption3'] = $params['configoption3'] ? 'true' : 'false';
    $params['configoption4'] = $params['configoption4'] ? 'true' : 'false';
    $params['configoption5'] = $params['configoption5'] ? 'true' : 'false';
    $params['configoption6'] = $params['configoption6'] ? 'true' : 'false';
    $params['configoption7'] = $params['configoption7'] ? 'true' : 'false';
    $params['configoption8'] = $params['configoption8'] ? 'true' : 'false';
    $params['configoption9'] = $params['configoption9'] ? 'true' : 'false';
    $params['configoption10'] = $params['configoption10'] ? 'true' : 'false';
    $params['configoption11'] = $params['configoption11'] ? 'true' : 'false';
    $params['configoption12'] = $params['configoption12'] ? 'true' : 'false';
    $params['configoption13'] = $params['configoption13'] ? 'true' : 'false';
    $params['configoption14'] = $params['configoption14'] ? 'true' : 'false';
    $params['configoption15'] = $params['configoption15'] ? 'true' : 'false';
    $params['configoption16'] = $params['configoption16'] ? 'true' : 'false';
    $params['configoption17'] = $params['configoption17'] ? 'true' : 'false';
    $params['configoption18'] = $params['configoption18'] ? 'true' : 'false';
    $params['configoption19'] = $params['configoption19'] ? 'true' : 'false';
    $params['configoption20'] = $params['configoption20'] ? 'true' : 'false';
    $params['configoption21'] = $params['configoption21'] ? 'true' : 'false';
    $params['configoption22'] = $params['configoption22'] ? 'true' : 'false';
    $params['configoption23'] = $params['configoption23'] ? 'true' : 'false';
    $clientsdetails = $params['clientsdetails'];
    $packet = "<domain>\n    <add>\n        <gen_setup>\n            <name>" . $params['domain'] . "</name>\n            <ip_address>" . $ipaddress . "</ip_address>\n            <htype>vrt_hst</htype>\n            <status>0</status>\n        </gen_setup>\n        <hosting>\n            <vrt_hst>\n                <ftp_login>" . $params['username'] . "</ftp_login>\n                <ftp_password>" . $params['password'] . "</ftp_password>\n                <ip_address>" . $ipaddress . "</ip_address>\n            </vrt_hst>\n        </hosting>\n        <user>\n            <enabled>true</enabled>\n            <password>" . $params['password'] . "</password>\n            <cname>" . $clientsdetails['companyname'] . "</cname>\n            <pname>" . $clientsdetails['firstname'] . " " . $clientsdetails['lastname'] . "</pname>\n            <phone>" . $clientsdetails['phonenumber'] . "</phone>\n            <email>" . $clientsdetails['email'] . "</email>\n            <address>" . $clientsdetails['address1'] . "</address>\n            <city>" . $clientsdetails['city'] . "</city>\n            <state>" . $clientsdetails['state'] . "</state>\n            <pcode>" . $clientsdetails['postcode'] . "</pcode>\n            <country>" . $clientsdetails['country'] . "</country>\n            <multiply_login>" . $params['configoption23'] . "</multiply_login>\n            <perms>\n                <manage_phosting>" . $params['configoption3'] . "</manage_phosting>\n                <manage_ftp_password>" . $params['configoption4'] . "</manage_ftp_password>\n                <manage_not_chroot_shell>" . $params['configoption5'] . "</manage_not_chroot_shell>\n                <manage_sh_access>" . $params['configoption6'] . "</manage_sh_access>\n                <manage_quota>" . $params['configoption7'] . "</manage_quota>\n                <manage_subdomains>" . $params['configoption8'] . "</manage_subdomains>\n                <manage_domain_aliases>" . $params['configoption9'] . "</manage_domain_aliases>\n                <manage_log>" . $params['configoption10'] . "</manage_log>\n                <manage_anonftp>" . $params['configoption11'] . "</manage_anonftp>\n                <manage_crontab>" . $params['configoption12'] . "</manage_crontab>\n                <manage_dns>" . $params['configoption13'] . "</manage_dns>\n                <manage_webapps>" . $params['configoption14'] . "</manage_webapps>\n                <manage_maillists>" . $params['configoption16'] . "</manage_maillists>\n                <manage_spamfilter>" . $params['configoption17'] . "</manage_spamfilter>\n                <manage_drweb>" . $params['configoption18'] . "</manage_drweb>\n                <allow_local_backups>" . $params['configoption19'] . "</allow_local_backups>\n                <allow_ftp_backups>" . $params['configoption20'] . "</allow_ftp_backups>\n                <site_builder>" . $params['configoption21'] . "</site_builder>\n                <manage_dashboard>" . $params['configoption22'] . "</manage_dashboard>\n            </perms>\n        </user>\n        <template-name>" . $params['configoption1'] . "</template-name>\n    </add>\n</domain>";
    $result = pleskreseller_connection($params, $packet);
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
function pleskreseller_TerminateAccount($params)
{
    $packet = "<domain>\n    <del>\n        <filter>\n            <domain_name>" . $params['domain'] . "</domain_name>\n        </filter>\n    </del>\n</domain>";
    $result = pleskreseller_connection($params, $packet);
    if( $result['curlerror'] )
    {
        return $result['curlerror'];
    }
    if( $result['PACKET']['SYSTEM']['STATUS'] == 'error' )
    {
        return $result['PACKET']['SYSTEM']['ERRCODE'] . " - " . $result['PACKET']['SYSTEM']['ERRTEXT'];
    }
    if( $result['PACKET']['DOMAIN']['DEL']['RESULT']['STATUS'] == 'error' )
    {
        return $result['PACKET']['DOMAIN']['DEL']['RESULT']['ERRCODE'] . " - " . $result['PACKET']['DOMAIN']['DEL']['RESULT']['ERRTEXT'];
    }
    return 'success';
}
function pleskreseller_SuspendAccount($params)
{
    $packet = "<domain>\n<set>\n    <filter>\n        <domain_name>" . $params['domain'] . "</domain_name>\n    </filter>\n    <values>\n        <gen_setup>\n            <status>64</status>\n        </gen_setup>\n    </values>\n</set>\n</domain>";
    $result = pleskreseller_connection($params, $packet);
    if( $result['curlerror'] )
    {
        return $result['curlerror'];
    }
    if( $result['PACKET']['SYSTEM']['STATUS'] == 'error' )
    {
        return $result['PACKET']['SYSTEM']['ERRCODE'] . " - " . $result['PACKET']['SYSTEM']['ERRTEXT'];
    }
    if( $result['PACKET']['DOMAIN']['SET']['RESULT']['STATUS'] == 'error' )
    {
        return $result['PACKET']['DOMAIN']['SET']['RESULT']['ERRCODE'] . " - " . $result['PACKET']['DOMAIN']['SET']['RESULT']['ERRTEXT'];
    }
    return 'success';
}
function pleskreseller_UnsuspendAccount($params)
{
    $packet = "<domain>\n<set>\n    <filter>\n        <domain_name>" . $params['domain'] . "</domain_name>\n    </filter>\n    <values>\n        <gen_setup>\n            <status>0</status>\n        </gen_setup>\n    </values>\n</set>\n</domain>";
    $result = pleskreseller_connection($params, $packet);
    if( $result['curlerror'] )
    {
        return $result['curlerror'];
    }
    if( $result['PACKET']['SYSTEM']['STATUS'] == 'error' )
    {
        return $result['PACKET']['SYSTEM']['ERRCODE'] . " - " . $result['PACKET']['SYSTEM']['ERRTEXT'];
    }
    if( $result['PACKET']['DOMAIN']['SET']['RESULT']['STATUS'] == 'error' )
    {
        return $result['PACKET']['DOMAIN']['SET']['RESULT']['ERRCODE'] . " - " . $result['PACKET']['DOMAIN']['SET']['RESULT']['ERRTEXT'];
    }
    return 'success';
}
function pleskreseller_ChangePassword($params)
{
    $packet = "<domain>\n<set>\n<filter>\n<domain_name>" . $params['domain'] . "</domain_name>\n</filter>\n<values>\n<hosting>\n<vrt_hst>\n<ftp_login>" . $params['username'] . "</ftp_login>\n<ftp_password>" . $params['password'] . "</ftp_password>\n<ip_address>" . $params['configoption2'] . "</ip_address>\n</vrt_hst>\n</hosting>\n<user>\n<enabled>true</enabled>\n<password>" . $params['password'] . "</password>\n</user>\n</values>\n</set>\n</domain>";
    $result = pleskreseller_connection($params, $packet);
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
function pleskreseller_connection($params, $packet)
{
    global $clientid;
    global $pleskpacketversion;
    if( !$pleskpacketversion )
    {
        $pleskpacketversion = "1.4.1.0";
    }
    $url = "https://" . $params['serverip'] . ":8443/enterprise/control/agent.php";
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
    logModuleCall('pleskreseller', '', $packet, $retval, $result);
    return $result;
}