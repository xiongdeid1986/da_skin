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
function interworx_ConfigOptions()
{
    $configarray = array( "Package Name" => array( 'Type' => 'text', 'Size' => '25' ), 'Theme' => array( 'Type' => 'text', 'Size' => '25' ), "Disk & BW Overselling" => array( 'Type' => 'yesno', 'Description' => "If reseller, tick to allow" ) );
    return $configarray;
}
function interworx_ClientArea($params)
{
    global $_LANG;
    $domain = $params['serverhostname'] ? $params['serverhostname'] : $params['serverip'];
    if( $params['type'] == 'reselleraccount' )
    {
        $form = sprintf("<form action=\"https://%s:2443/nodeworx/index.php?action=login\" method=\"post\" target=\"_blank\">" . "<input type=\"hidden\" name=\"email\" value=\"%s\" />" . "<input type=\"hidden\" name=\"password\" value=\"%s\" />" . "<input type=\"submit\" value=\"%s\" class=\"button\" />" . "</form>", WHMCS_Input_Sanitize::encode($domain), WHMCS_Input_Sanitize::encode($params['username']), WHMCS_Input_Sanitize::encode($params['password']), $_LANG['nodeworxlogin']);
    }
    else
    {
        $form = sprintf("<form action=\"https://%s:2443/siteworx/index.php?action=login\" method=\"post\" target=\"_blank\">" . "<input type=\"hidden\" name=\"email\" value=\"%s\" />" . "<input type=\"hidden\" name=\"password\" value=\"%s\" />" . "<input type=\"hidden\" name=\"domain\" value=\"%s\" />" . "<input type=\"submit\" value=\"%s\" class=\"button\" />" . "</form>", WHMCS_Input_Sanitize::encode($domain), WHMCS_Input_Sanitize::encode($params['clientsdetails']['email']), WHMCS_Input_Sanitize::encode($params['password']), WHMCS_Input_Sanitize::encode($params['domain']), $_LANG['siteworxlogin']);
    }
    return $form;
}
function interworx_AdminLink($params)
{
    $domain = $params['serverhostname'] ? $params['serverhostname'] : $params['serverip'];
    $form = sprintf("<form action=\"https://%s:2443/nodeworx/\" method=\"post\" target=\"_blank\">" . "<input type=\"submit\" value=\"%s\" />" . "</form>", WHMCS_Input_Sanitize::encode($domain), "InterWorx Panel");
    return $form;
}
function interworx_CreateAccount($params)
{
    $key = $params['serveraccesshash'];
    $api_controller = '/nodeworx/siteworx';
    if( $params['configoptions']["Dedicated IP"] )
    {
        $action = 'listDedicatedFreeIps';
        $client = new soapclient("https://" . $params['serverip'] . ":2443/nodeworx/soap?wsdl");
        $result = $client->route($key, $api_controller, $action, $input);
        logModuleCall('interworx', $action, $input, $result);
        if( $result['status'] )
        {
            return $result['status'] . " - " . $result['payload'];
        }
    }
    else
    {
        $action = 'listFreeIps';
        $client = new soapclient("https://" . $params['serverip'] . ":2443/nodeworx/soap?wsdl");
        $result = $client->route($key, $api_controller, $action, $input);
        logModuleCall('interworx', $action, $input, $result);
        if( $result['status'] )
        {
            return $result['status'] . " - " . $result['payload'];
        }
    }
    $ipaddress = $result['payload'][0][0];
    if( $params['type'] == 'reselleraccount' )
    {
        $overselling = $params['configoption3'] ? '1' : '0';
        $api_controller = '/nodeworx/reseller';
        $action = 'add';
        $input = array( 'nickname' => strtolower($params['clientsdetails']['firstname'] . $params['clientsdetails']['lastname']), 'email' => $params['clientsdetails']['email'], 'password' => $params['password'], 'confirm_password' => $params['password'], 'language' => 'en-us', 'theme' => $params['configoption2'], 'billing_day' => '1', 'status' => 'active', 'packagetemplate' => $params['configoption1'], 'RSL_OPT_OVERSELL_STORAGE' => $overselling, 'RSL_OPT_OVERSELL_BANDWIDTH' => $overselling, 'ips' => $ipaddress, 'database_servers' => 'localhost' );
        update_query('tblhosting', array( 'username' => $params['clientsdetails']['email'] ), array( 'id' => $params['serviceid'] ));
    }
    else
    {
        $action = 'add';
        $input = array( 'domainname' => $params['domain'], 'ipaddress' => $ipaddress, 'uniqname' => $params['username'], 'nickname' => strtolower($params['clientsdetails']['firstname'] . $params['clientsdetails']['lastname']), 'email' => $params['clientsdetails']['email'], 'password' => $params['password'], 'confirm_password' => $params['password'], 'language' => 'en-us', 'theme' => $params['configoption2'], 'packagetemplate' => $params['configoption1'] );
    }
    $client = new soapclient("https://" . $params['serverip'] . ":2443/nodeworx/soap?wsdl");
    $result = $client->route($key, $api_controller, $action, $input);
    logModuleCall('interworx', $action, $input, $result);
    if( $result['status'] )
    {
        return $result['status'] . " - " . $result['payload'];
    }
    return 'success';
}
function interworx_TerminateAccount($params)
{
    $key = $params['serveraccesshash'];
    if( $params['type'] == 'reselleraccount' )
    {
        $resellers = interworx_GetResellers($params);
        $email = $params['clientsdetails']['email'];
        $resellerid = $resellers[$email];
        if( !$resellerid )
        {
            return "Reseller ID Not Found";
        }
        $api_controller = '/nodeworx/reseller';
        $action = 'delete';
        $input = array( 'reseller_id' => $resellerid );
    }
    else
    {
        $api_controller = '/nodeworx/siteworx';
        $action = 'delete';
        $input = array( 'domain' => $params['domain'], 'confirm_action' => '1' );
    }
    $client = new soapclient("https://" . $params['serverip'] . ":2443/nodeworx/soap?wsdl");
    $result = $client->route($key, $api_controller, $action, $input);
    logModuleCall('interworx', $action, $input, $result);
    if( $result['status'] )
    {
        return $result['status'] . " - " . $result['payload'];
    }
    return 'success';
}
function interworx_UsageUpdate($params)
{
    $key = $params['serveraccesshash'];
    $api_controller = '/nodeworx/siteworx';
    $action = 'listBandwidthAndStorage';
    $input = array(  );
    $client = new soapclient("https://" . $params['serverip'] . ":2443/nodeworx/soap?wsdl");
    $result = $client->route($key, $api_controller, $action, $input);
    logModuleCall('interworx', $action, $input, $result);
    $domainsdata = $result['payload'];
    foreach( $domainsdata as $data )
    {
        $domain = $data['domain'];
        $bandwidth_used = $data['bandwidth_used'];
        $bandwidth = $data['bandwidth'];
        $storage_used = $data['storage_used'];
        $storage = $data['storage'];
        update_query('tblhosting', array( 'diskusage' => $storage_used, 'disklimit' => $storage, 'bwusage' => $bandwidth_used, 'bwlimit' => $bandwidth, 'lastupdate' => "now()" ), array( 'domain' => $domain, 'server' => $params['serverid'] ));
    }
}
function interworx_SuspendAccount($params)
{
    $key = $params['serveraccesshash'];
    if( $params['type'] == 'reselleraccount' )
    {
        $resellers = interworx_GetResellers($params);
        $email = $params['clientsdetails']['email'];
        $resellerid = $resellers[$email];
        if( !$resellerid )
        {
            return "Reseller ID Not Found";
        }
        $api_controller = '/nodeworx/reseller';
        $action = 'edit';
        $input = array( 'reseller_id' => $resellerid, 'status' => 'inactive' );
    }
    else
    {
        $api_controller = '/nodeworx/siteworx';
        $action = 'edit';
        $input = array( 'domain' => $params['domain'], 'status' => '0' );
    }
    $client = new soapclient("https://" . $params['serverip'] . ":2443/nodeworx/soap?wsdl");
    $result = $client->route($key, $api_controller, $action, $input);
    logModuleCall('interworx', $action, $input, $result);
    if( $result['status'] )
    {
        return $result['status'] . " - " . $result['payload'];
    }
    return 'success';
}
function interworx_UnsuspendAccount($params)
{
    $key = $params['serveraccesshash'];
    if( $params['type'] == 'reselleraccount' )
    {
        $resellers = interworx_GetResellers($params);
        $email = $params['clientsdetails']['email'];
        $resellerid = $resellers[$email];
        if( !$resellerid )
        {
            return "Reseller ID Not Found";
        }
        $api_controller = '/nodeworx/reseller';
        $action = 'edit';
        $input = array( 'reseller_id' => $resellerid, 'status' => 'active' );
    }
    else
    {
        $api_controller = '/nodeworx/siteworx';
        $action = 'edit';
        $input = array( 'domain' => $params['domain'], 'status' => '1' );
    }
    $client = new soapclient("https://" . $params['serverip'] . ":2443/nodeworx/soap?wsdl");
    $result = $client->route($key, $api_controller, $action, $input);
    logModuleCall('interworx', $action, $input, $result);
    if( $result['status'] )
    {
        return $result['status'] . " - " . $result['payload'];
    }
    return 'success';
}
function interworx_ChangePassword($params)
{
    $key = $params['serveraccesshash'];
    if( $params['type'] == 'reselleraccount' )
    {
        $resellers = interworx_GetResellers($params);
        $email = $params['clientsdetails']['email'];
        $resellerid = $resellers[$email];
        if( !$resellerid )
        {
            return "Reseller ID Not Found";
        }
        $api_controller = '/nodeworx/reseller';
        $action = 'edit';
        $input = array( 'reseller_id' => $resellerid, 'password' => $params['password'], 'confirm_password' => $params['password'] );
    }
    else
    {
        $api_controller = '/nodeworx/siteworx';
        $action = 'edit';
        $input = array( 'domain' => $params['domain'], 'password' => $params['password'], 'confirm_password' => $params['password'] );
    }
    $client = new soapclient("https://" . $params['serverip'] . ":2443/nodeworx/soap?wsdl");
    $result = $client->route($key, $api_controller, $action, $input);
    logModuleCall('interworx', $action, $input, $result);
    if( $result['status'] )
    {
        return $result['status'] . " - " . $result['payload'];
    }
    return 'success';
}
function interworx_ChangePackage($params)
{
    $key = $params['serveraccesshash'];
    if( $params['type'] == 'reselleraccount' )
    {
        $resellers = interworx_GetResellers($params);
        $email = $params['clientsdetails']['email'];
        $resellerid = $resellers[$email];
        if( !$resellerid )
        {
            return "Reseller ID Not Found";
        }
        $overselling = $params['configoption3'] ? '1' : '0';
        $api_controller = '/nodeworx/reseller';
        $action = 'edit';
        $input = array( 'reseller_id' => $resellerid, 'package_template' => $params['configoption1'], 'RSL_OPT_OVERSELL_STORAGE' => $overselling, 'RSL_OPT_OVERSELL_BANDWIDTH' => $overselling );
    }
    else
    {
        $api_controller = '/nodeworx/siteworx';
        $action = 'edit';
        $input = array( 'domain' => $params['domain'], 'package_template' => $params['configoption1'] );
    }
    $client = new soapclient("https://" . $params['serverip'] . ":2443/nodeworx/soap?wsdl");
    $result = $client->route($key, $api_controller, $action, $input);
    logModuleCall('interworx', $action, $input, $result);
    if( $result['status'] )
    {
        return $result['status'] . " - " . $result['payload'];
    }
    return 'success';
}
function interworx_GetResellers($params)
{
    $key = $params['serveraccesshash'];
    $api_controller = '/nodeworx/reseller';
    $action = 'listIds';
    $input = array(  );
    $client = new soapclient("https://" . $params['serverip'] . ":2443/nodeworx/soap?wsdl");
    $result = $client->route($key, $api_controller, $action, $input);
    logModuleCall('interworx', $action, $input, $result);
    $resellers = array(  );
    foreach( $result['payload'] as $reseller )
    {
        $resellerid = $reseller[0];
        $reselleremail = $reseller[1];
        $reselleremail = explode("(", $reselleremail, 2);
        $reselleremail = $reselleremail[1];
        $reselleremail = substr($reselleremail, 0, 0 - 1);
        $resellers[$reselleremail] = $resellerid;
    }
    return $resellers;
}