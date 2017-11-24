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
function enomtruste_ConfigOptions()
{
    global $id;
    $result = select_query('tblcustomfields', 'id', "type='product' AND relid=" . (int) $id . " AND fieldname LIKE 'Domain Name%'");
    $data = mysql_fetch_array($result);
    if( !$data[0] )
    {
        insert_query('tblcustomfields', array( 'type' => 'product', 'relid' => $id, 'fieldname' => "Domain Name", 'fieldtype' => 'text', 'required' => 'on', 'showorder' => 'on' ));
    }
    $configarray = array( "eNom Username" => array( 'Type' => 'text', 'Size' => '25', 'Description' => '' ), "eNom Password" => array( 'Type' => 'password', 'Size' => '25' ), "TRUSTe Seal" => array( 'Type' => 'yesno', 'Description' => "Tick to enable Seal" ), "Number of Years" => array( 'Type' => 'dropdown', 'Options' => '1,2,3' ), "Demo Mode" => array( 'Type' => 'yesno', 'Description' => "Before using Demo Mode, ensure you have setup your test account with eNom" ) );
    return $configarray;
}
function enomtruste_CreateAccount($params)
{
    updateService(array( 'username' => '', 'password' => '' ));
    $withseal = $params['configoption3'];
    $numyears = $params['configoption4'];
    $result = select_query('tblhosting', 'billingcycle', array( 'id' => $params['serviceid'] ));
    $data = mysql_fetch_array($result);
    $billingcycle = $data[0];
    if( $billingcycle == 'Biennially' )
    {
        $numyears = '2';
    }
    if( $billingcycle == 'Triennially' )
    {
        $numyears = '3';
    }
    if( $params['configoptions']['Seal'] )
    {
        $withseal = true;
    }
    if( $params['configoptions']['NumYears'] )
    {
        $numyears = $params['configoptions']['NumYears'];
    }
    $apiproducttype = $withseal ? 'TRUSTePrivacyPolicySeal' : 'TRUSTePrivacyPolicy';
    $postfields = array(  );
    $postfields['command'] = 'PurchaseServices';
    $postfields['Service'] = $apiproducttype;
    $postfields['NumYears'] = $numyears;
    $postfields['EmailNotify'] = '0';
    $xmldata = enomtruste_call($params, $postfields);
    if( $xmldata['INTERFACE-RESPONSE']['ERRCOUNT'] == 0 )
    {
        $result = 'success';
        if( !mysql_num_rows(full_query("SHOW TABLES LIKE 'mod_enomtruste'")) )
        {
            full_query("CREATE TABLE `mod_enomtruste` ( `serviceid` INT(10) NOT NULL , `subscrid` INT(10) NOT NULL )");
        }
        $subscrid = $xmldata['INTERFACE-RESPONSE']['SUBSCRIPTIONID'];
        insert_query('mod_enomtruste', array( 'serviceid' => $params['serviceid'], 'subscrid' => $subscrid ));
        $domain = $params['domain'];
        if( !$domain )
        {
            $domain = $params['customfields']["Domain Name"];
        }
        $postfields = array(  );
        $postfields['command'] = 'PP_UpdateSubscriptionDetails';
        $postfields['SubscriptionID'] = $subscrid;
        $postfields['DomainName'] = $domain;
        $postfields['EmailAddress'] = $params['clientsdetails']['email'];
        $xmldata = enomtruste_call($params, $postfields);
    }
    else
    {
        $result = $xmldata['INTERFACE-RESPONSE']['ERRORS']['ERR1'];
        if( !$result )
        {
            $result = "An Unknown Error Occurred";
        }
    }
    return $result;
}
function enomtruste_TerminateAccount($params)
{
    $result = select_query('mod_enomtruste', 'subscrid', array( 'serviceid' => $params['serviceid'] ));
    $data = mysql_fetch_array($result);
    $subscrid = $data[0];
    $postfields = array(  );
    $postfields['command'] = 'PP_CancelSubscription';
    $postfields['SubscriptionID'] = $subscrid;
    $postfields['ReasonID'] = '1';
    $postfields['Comment'] = 'None';
    $xmldata = enomtruste_call($params, $postfields);
    if( $xmldata['INTERFACE-RESPONSE']['ERRCOUNT'] == 0 )
    {
        $result = 'success';
    }
    else
    {
        $result = $xmldata['INTERFACE-RESPONSE']['ERRORS']['ERR1'];
        if( !$result )
        {
            $result = "An Unknown Error Occurred";
        }
    }
    return $result;
}
function enomtruste_ClientArea($params)
{
    global $_LANG;
    $domain = $params['domain'];
    if( !$domain )
    {
        $domain = $params['customfields']["Domain Name"];
    }
    $postfields = array(  );
    $postfields['command'] = 'PP_GetControlPanelLoginURL';
    $postfields['DomainName'] = $domain;
    $xmldata = enomtruste_call($params, $postfields);
    if( $xmldata['INTERFACE-RESPONSE']['ERRCOUNT'] == 0 )
    {
        $code = "<p align=\"center\"><img src=\"modules/servers/enomtruste/logo.png\" alt=\"TrustE Certified Privacy\" /><br />" . $_LANG['enomtrustedesc'] . "<br /><br /><input type=\"button\" value=\"" . $_LANG['enomtrustelogin'] . "\" onclick=\"window.open('" . $xmldata['INTERFACE-RESPONSE']['LOGINURL'] . "','truste')\" class=\"btn\" /></p>";
        return $code;
    }
    $result = $xmldata['INTERFACE-RESPONSE']['ERRORS']['ERR1'];
    if( !$result )
    {
        $result = "An Unknown Error Occurred";
    }
}
function enomtruste_call($params, $postfields)
{
    $enomusr = $params['configoption1'];
    $enompwd = $params['configoption2'];
    $demomode = $params['configoption5'];
    $postfields['uid'] = $enomusr;
    $postfields['pw'] = $enompwd;
    $postfields['ResponseType'] = 'XML';
    $url = $demomode ? 'test' : '';
    $url = "http://reseller" . $url . ".enom.com/interface.asp";
    $data = curlCall($url, $postfields);
    $xmldata = XMLtoArray($data);
    logModuleCall('enomtruste', $postfields['command'], $postfields, $data, $xmldata);
    return $xmldata;
}