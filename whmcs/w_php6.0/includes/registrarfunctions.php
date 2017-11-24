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
function checkDomain($domain)
{
    global $domainparts;
    if( preg_match("/^[a-z0-9][a-z0-9\\-]+[a-z0-9](\\.[a-z]{2,4})+\$/i", $domain) )
    {
        $domainparts = explode(".", $domain, 2);
        return true;
    }
    return false;
}
function getRegistrarsDropdownMenu($registrar, $name = 'registrar')
{
    global $aInt;
    $code = "<select name=\"" . $name . "\" id=\"registrarsDropDown\"><option value=\"\">" . $aInt->lang('global', 'none') . "</option>";
    $result = select_query('tblregistrars', "DISTINCT registrar", '', 'registrar', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $code .= "<option value=\"" . $data[0] . "\"";
        if( $registrar == $data[0] )
        {
            $code .= " selected";
        }
        $code .= ">" . ucfirst($data[0]) . "</option>";
    }
    $code .= "</select>";
    return $code;
}
function loadRegistrarModule($registrar)
{
    if( function_exists($registrar . '_getConfigArray') )
    {
        return true;
    }
    $module = new WHMCS_Module_Registrar();
    return $module->load($registrar);
}
function RegCallFunction($params, $function, $noarr = false)
{
    $registrar = $params['registrar'];
    $hookResults = run_hook('PreRegistrar' . $function, array( 'params' => $params ));
    try
    {
        if( processHookResults($registrar, $function, $hookResults) )
        {
            return array(  );
        }
    }
    catch( Exception $e )
    {
        return array( 'error' => $e->getMessage() );
    }
    $functionExists = $functionSuccessful = false;
    $module = new WHMCS_Module_Registrar();
    $module->setDomainID($params['domainid']);
    $module->load($registrar);
    if( $module->functionExists($function) )
    {
        $functionExists = true;
        $values = $module->call($function, $params);
        if( !is_array($values) && !$noarr )
        {
            $values = array(  );
        }
        if( empty($values['error']) )
        {
            $functionSuccessful = true;
        }
    }
    else
    {
        $values = array( 'na' => true );
    }
    $vars = array( 'params' => $params, 'results' => $values, 'functionExists' => $functionExists, 'functionSuccessful' => $functionSuccessful );
    $hookResults = run_hook('AfterRegistrar' . $function, $vars);
    try
    {
        if( processHookResults($registrar, $function, $hookResults) )
        {
            return array(  );
        }
    }
    catch( Exception $e )
    {
        return array( 'error' => $e->getMessage() );
    }
    return $values;
}
function getRegistrarConfigOptions($registrar)
{
    $module = new WHMCS_Module_Registrar();
    $module->load($registrar);
    return $module->getSettings();
}
function RegGetNameservers($params)
{
    return regcallfunction($params, 'GetNameservers');
}
function RegSaveNameservers($params)
{
    for( $i = 1; $i <= 5; $i++ )
    {
        $params['ns' . $i] = trim($params['ns' . $i]);
    }
    $values = regcallfunction($params, 'SaveNameservers');
    if( !$values )
    {
        return false;
    }
    $userid = get_query_val('tbldomains', 'userid', array( 'id' => $params['domainid'] ));
    if( $values['error'] )
    {
        logActivity("Domain Registrar Command: Save Nameservers - Failed: " . $values['error'] . " - Domain ID: " . $params['domainid'], $userid);
    }
    else
    {
        logActivity("Domain Registrar Command: Save Nameservers - Successful", $userid);
    }
    return $values;
}
function RegGetRegistrarLock($params)
{
    $values = regcallfunction($params, 'GetRegistrarLock', 1);
    if( is_array($values) )
    {
        return '';
    }
    return $values;
}
function RegSaveRegistrarLock($params)
{
    $values = regcallfunction($params, 'SaveRegistrarLock');
    if( !$values )
    {
        return false;
    }
    $userid = get_query_val('tbldomains', 'userid', array( 'id' => $params['domainid'] ));
    if( $values['error'] )
    {
        logActivity("Domain Registrar Command: Toggle Registrar Lock - Failed: " . $values['error'] . " - Domain ID: " . $params['domainid'], $userid);
    }
    else
    {
        logActivity("Domain Registrar Command: Toggle Registrar Lock - Successful", $userid);
    }
    return $values;
}
function RegGetURLForwarding($params)
{
    return regcallfunction($params, 'GetURLForwarding');
}
function RegSaveURLForwarding($params)
{
    return regcallfunction($params, 'SaveURLForwarding');
}
function RegGetEmailForwarding($params)
{
    return regcallfunction($params, 'GetEmailForwarding');
}
function RegSaveEmailForwarding($params)
{
    return regcallfunction($params, 'SaveEmailForwarding');
}
function RegGetDNS($params)
{
    return regcallfunction($params, 'GetDNS');
}
function RegSaveDNS($params)
{
    return regcallfunction($params, 'SaveDNS');
}
function RegRenewDomain($params)
{
    $domainid = $params['domainid'];
    $result = select_query('tbldomains', '', array( 'id' => $domainid ));
    $data = mysql_fetch_array($result);
    $userid = $data['userid'];
    $domain = $data['domain'];
    $orderid = $data['orderid'];
    $registrar = $data['registrar'];
    $registrationperiod = $data['registrationperiod'];
    $dnsmanagement = $data['dnsmanagement'] ? true : false;
    $emailforwarding = $data['emailforwarding'] ? true : false;
    $idprotection = $data['idprotection'] ? true : false;
    $domainObj = new WHMCS_Domains_Domain($domain);
    $params['registrar'] = $registrar;
    $params['sld'] = $domainObj->getSLD();
    $params['tld'] = $domainObj->getTLD();
    $params['regperiod'] = $registrationperiod;
    $params['dnsmanagement'] = $dnsmanagement;
    $params['emailforwarding'] = $emailforwarding;
    $params['idprotection'] = $idprotection;
    $params['domainObj'] = $domainObj;
    $values = regcallfunction($params, 'RenewDomain');
    if( !is_array($values) )
    {
        return false;
    }
    if( $values['na'] )
    {
        return array( 'error' => "Registrar Function Not Supported" );
    }
    if( $values['error'] )
    {
        logActivity("Domain Renewal Failed - Domain ID: " . $domainid . " - Domain: " . $domain . " - Error: " . $values['error'], $userid);
        run_hook('AfterRegistrarRenewalFailed', array( 'params' => $params, 'error' => $values['error'] ));
    }
    else
    {
        $result = select_query('tbldomains', 'expirydate,registrationperiod', array( 'id' => $domainid ));
        $data = mysql_fetch_array($result);
        $expirydate = $data['expirydate'];
        $registrationperiod = $data['registrationperiod'];
        $year = substr($expirydate, 0, 4);
        $month = substr($expirydate, 5, 2);
        $day = substr($expirydate, 8, 2);
        $year = $year + $registrationperiod;
        $expirydate = $year . '-' . $month . '-' . $day;
        $update = array( 'expirydate' => $expirydate, 'status' => 'Active', 'reminders' => '' );
        update_query('tbldomains', $update, array( 'id' => $domainid ));
        logActivity("Domain Renewed Successfully - Domain ID: " . $domainid . " - Domain: " . $domain, $userid);
        run_hook('AfterRegistrarRenewal', array( 'params' => $params ));
    }
    return $values;
}
function RegRegisterDomain($paramvars)
{
    global $CONFIG;
    $domainid = $paramvars['domainid'];
    $result = select_query('tbldomains', '', array( 'id' => $domainid ));
    $data = mysql_fetch_array($result);
    $userid = $data['userid'];
    $domain = $data['domain'];
    $orderid = $data['orderid'];
    $registrar = $data['registrar'];
    $registrationperiod = $data['registrationperiod'];
    $dnsmanagement = $data['dnsmanagement'] ? true : false;
    $emailforwarding = $data['emailforwarding'] ? true : false;
    $idprotection = $data['idprotection'] ? true : false;
    $result = select_query('tblorders', 'contactid', array( 'id' => $orderid ));
    $data = mysql_fetch_array($result);
    $contactid = $data['contactid'];
    if( !function_exists('getClientsDetails') )
    {
        require(dirname(__FILE__) . "/clientfunctions.php");
    }
    $clientsdetails = getClientsDetails($userid, $contactid);
    $clientsdetails['state'] = $clientsdetails['statecode'];
    $clientsdetails['fullphonenumber'] = $clientsdetails['phonenumberformatted'];
    global $params;
    $params = array_merge($paramvars, $clientsdetails);
    $domainObj = new WHMCS_Domains_Domain($domain);
    $params['registrar'] = $registrar;
    $params['sld'] = $domainObj->getSLD();
    $params['tld'] = $domainObj->getTLD();
    $params['regperiod'] = $registrationperiod;
    $params['dnsmanagement'] = $dnsmanagement;
    $params['emailforwarding'] = $emailforwarding;
    $params['idprotection'] = $idprotection;
    if( $CONFIG['RegistrarAdminUseClientDetails'] == 'on' )
    {
        $params['adminfirstname'] = $clientsdetails['firstname'];
        $params['adminlastname'] = $clientsdetails['lastname'];
        $params['admincompanyname'] = $clientsdetails['companyname'];
        $params['adminemail'] = $clientsdetails['email'];
        $params['adminaddress1'] = $clientsdetails['address1'];
        $params['adminaddress2'] = $clientsdetails['address2'];
        $params['admincity'] = $clientsdetails['city'];
        $params['adminfullstate'] = $clientsdetails['fullstate'];
        $params['adminstate'] = $clientsdetails['state'];
        $params['adminpostcode'] = $clientsdetails['postcode'];
        $params['admincountry'] = $clientsdetails['country'];
        $params['adminfullphonenumber'] = $clientsdetails['phonenumberformatted'];
        $params['adminphonenumber'] = $params['adminfullphonenumber'];
    }
    else
    {
        $params['adminfirstname'] = $CONFIG['RegistrarAdminFirstName'];
        $params['adminlastname'] = $CONFIG['RegistrarAdminLastName'];
        $params['admincompanyname'] = $CONFIG['RegistrarAdminCompanyName'];
        $params['adminemail'] = $CONFIG['RegistrarAdminEmailAddress'];
        $params['adminaddress1'] = $CONFIG['RegistrarAdminAddress1'];
        $params['adminaddress2'] = $CONFIG['RegistrarAdminAddress2'];
        $params['admincity'] = $CONFIG['RegistrarAdminCity'];
        $params['adminfullstate'] = $CONFIG['RegistrarAdminStateProvince'];
        $params['adminstate'] = convertStateToCode($CONFIG['RegistrarAdminStateProvince'], $CONFIG['RegistrarAdminCountry']);
        $params['adminpostcode'] = $CONFIG['RegistrarAdminPostalCode'];
        $params['admincountry'] = $CONFIG['RegistrarAdminCountry'];
        $params['adminfullphonenumber'] = $CONFIG['RegistrarAdminPhone'];
        $params['adminphonenumber'] = $params['adminfullphonenumber'];
    }
    if( !$params['ns1'] && !$params['ns2'] )
    {
        $result = select_query('tblorders', 'nameservers', array( 'id' => $orderid ));
        $data = mysql_fetch_array($result);
        $nameservers = $data['nameservers'];
        $result = select_query('tblhosting', 'server', array( 'domain' => $domain ));
        $data = mysql_fetch_array($result);
        $server = $data['server'];
        if( $server )
        {
            $result = select_query('tblservers', '', array( 'id' => $server ));
            $data = mysql_fetch_array($result);
            for( $i = 1; $i <= 5; $i++ )
            {
                $params['ns' . $i] = trim($data['nameserver' . $i]);
            }
        }
        else
        {
            if( $nameservers && $nameservers != ',' )
            {
                $nameservers = explode(',', $nameservers);
                for( $i = 1; $i <= 5; $i++ )
                {
                    $params['ns' . $i] = trim($nameservers[$i - 1]);
                }
            }
            else
            {
                for( $i = 1; $i <= 5; $i++ )
                {
                    $params['ns' . $i] = trim($CONFIG['DefaultNameserver' . $i]);
                }
            }
        }
    }
    else
    {
        for( $i = 1; $i <= 5; $i++ )
        {
            $params['ns' . $i] = trim($params['ns' . $i]);
        }
    }
    $additflds = new WHMCS_Domains_AdditionalFields();
    $params['additionalfields'] = $additflds->getFieldValuesFromDatabase($domainid);
    $originaldetails = $params;
    $params = foreignChrReplace($params);
    $params['original'] = $originaldetails;
    $params['domainObj'] = $domainObj;
    run_hook('PreDomainRegister', array( 'domain' => $domain ));
    $values = regcallfunction($params, 'RegisterDomain');
    if( !is_array($values) )
    {
        return false;
    }
    if( $values['na'] )
    {
        logActivity("Domain Registration Not Supported by Module - Domain ID: " . $domainid . " - Domain: " . $domain);
        return array( 'error' => "Registrar Function Not Supported" );
    }
    if( $values['error'] )
    {
        logActivity("Domain Registration Failed - Domain ID: " . $domainid . " - Domain: " . $domain . " - Error: " . $values['error'], $userid);
        run_hook('AfterRegistrarRegistrationFailed', array( 'params' => $params, 'error' => $values['error'] ));
    }
    else
    {
        $expirydate = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y') + $registrationperiod));
        update_query('tbldomains', array( 'registrationdate' => date('Ymd'), 'expirydate' => $expirydate, 'status' => 'Active' ), array( 'id' => $domainid ));
        logActivity("Domain Registered Successfully - Domain ID: " . $domainid . " - Domain: " . $domain, $userid);
        run_hook('AfterRegistrarRegistration', array( 'params' => $params ));
    }
    return $values;
}
function RegTransferDomain($paramvars)
{
    global $CONFIG;
    $domainid = $paramvars['domainid'];
    $passedepp = $paramvars['transfersecret'];
    $result = select_query('tbldomains', '', array( 'id' => $domainid ));
    $data = mysql_fetch_array($result);
    $userid = $data['userid'];
    $domain = $data['domain'];
    $orderid = $data['orderid'];
    $registrar = $data['registrar'];
    $registrationperiod = $data['registrationperiod'];
    $dnsmanagement = $data['dnsmanagement'] ? true : false;
    $emailforwarding = $data['emailforwarding'] ? true : false;
    $idprotection = $data['idprotection'] ? true : false;
    $result = select_query('tblorders', 'contactid,nameservers,transfersecret', array( 'id' => $orderid ));
    $data = mysql_fetch_array($result);
    $contactid = $data['contactid'];
    $nameservers = $data['nameservers'];
    $transfersecret = $data['transfersecret'];
    if( !function_exists('getClientsDetails') )
    {
        require(dirname(__FILE__) . "/clientfunctions.php");
    }
    $clientsdetails = getClientsDetails($userid, $contactid);
    $clientsdetails['state'] = $clientsdetails['statecode'];
    $clientsdetails['fullphonenumber'] = $clientsdetails['phonenumberformatted'];
    global $params;
    $params = array_merge($paramvars, $clientsdetails);
    $domainObj = new WHMCS_Domains_Domain($domain);
    $params['registrar'] = $registrar;
    $params['sld'] = $domainObj->getSLD();
    $params['tld'] = $domainObj->getTLD();
    $params['regperiod'] = $registrationperiod;
    $params['dnsmanagement'] = $dnsmanagement;
    $params['emailforwarding'] = $emailforwarding;
    $params['idprotection'] = $idprotection;
    if( $CONFIG['RegistrarAdminUseClientDetails'] == 'on' )
    {
        $params['adminfirstname'] = $clientsdetails['firstname'];
        $params['adminlastname'] = $clientsdetails['lastname'];
        $params['admincompanyname'] = $clientsdetails['companyname'];
        $params['adminemail'] = $clientsdetails['email'];
        $params['adminaddress1'] = $clientsdetails['address1'];
        $params['adminaddress2'] = $clientsdetails['address2'];
        $params['admincity'] = $clientsdetails['city'];
        $params['adminfullstate'] = $clientsdetails['fullstate'];
        $params['adminstate'] = $clientsdetails['state'];
        $params['adminpostcode'] = $clientsdetails['postcode'];
        $params['admincountry'] = $clientsdetails['country'];
        $params['adminfullphonenumber'] = $clientsdetails['phonenumberformatted'];
        $params['adminphonenumber'] = $params['adminfullphonenumber'];
    }
    else
    {
        $params['adminfirstname'] = $CONFIG['RegistrarAdminFirstName'];
        $params['adminlastname'] = $CONFIG['RegistrarAdminLastName'];
        $params['admincompanyname'] = $CONFIG['RegistrarAdminCompanyName'];
        $params['adminemail'] = $CONFIG['RegistrarAdminEmailAddress'];
        $params['adminaddress1'] = $CONFIG['RegistrarAdminAddress1'];
        $params['adminaddress2'] = $CONFIG['RegistrarAdminAddress2'];
        $params['admincity'] = $CONFIG['RegistrarAdminCity'];
        $params['adminstate'] = $CONFIG['RegistrarAdminStateProvince'];
        $params['adminpostcode'] = $CONFIG['RegistrarAdminPostalCode'];
        $params['admincountry'] = $CONFIG['RegistrarAdminCountry'];
        $params['adminfullphonenumber'] = $CONFIG['RegistrarAdminPhone'];
        $params['adminphonenumber'] = $params['adminfullphonenumber'];
    }
    if( !$params['ns1'] && !$params['ns2'] )
    {
        $result = select_query('tblorders', 'nameservers', array( 'id' => $orderid ));
        $data = mysql_fetch_array($result);
        $nameservers = $data['nameservers'];
        $result = select_query('tblhosting', 'server', array( 'domain' => $domain ));
        $data = mysql_fetch_array($result);
        $server = $data['server'];
        if( $server )
        {
            $result = select_query('tblservers', '', array( 'id' => $server ));
            $data = mysql_fetch_array($result);
            for( $i = 1; $i <= 5; $i++ )
            {
                $params['ns' . $i] = trim($data['nameserver' . $i]);
            }
        }
        else
        {
            if( $nameservers && $nameservers != ',' )
            {
                $nameservers = explode(',', $nameservers);
                for( $i = 1; $i <= 5; $i++ )
                {
                    $params['ns' . $i] = trim($nameservers[$i - 1]);
                }
            }
            else
            {
                for( $i = 1; $i <= 5; $i++ )
                {
                    $params['ns' . $i] = trim($CONFIG['DefaultNameserver' . $i]);
                }
            }
        }
    }
    else
    {
        for( $i = 1; $i <= 5; $i++ )
        {
            $params['ns' . $i] = trim($params['ns' . $i]);
        }
    }
    $additflds = new WHMCS_Domains_AdditionalFields();
    $params['additionalfields'] = $additflds->getFieldValuesFromDatabase($domainid);
    $originaldetails = $params;
    $params = foreignChrReplace($params);
    $params['original'] = $originaldetails;
    if( !$params['transfersecret'] )
    {
        $transfersecret = $transfersecret ? unserialize($transfersecret) : array(  );
        $params['eppcode'] = WHMCS_Input_Sanitize::decode($transfersecret[$domain]);
        $params['transfersecret'] = $params['eppcode'];
    }
    else
    {
        $params['eppcode'] = WHMCS_Input_Sanitize::decode($passedepp);
        $params['transfersecret'] = $params['eppcode'];
    }
    $params['domainObj'] = $domainObj;
    run_hook('PreDomainRegister', array( 'domain' => $domain ));
    $values = regcallfunction($params, 'TransferDomain');
    if( !is_array($values) )
    {
        return false;
    }
    if( $values['na'] )
    {
        logActivity("Domain Transfer Not Supported by Module - Domain ID: " . $domainid . " - Domain: " . $domain);
        return array( 'error' => "Registrar Function Not Supported" );
    }
    if( $values['error'] )
    {
        logActivity("Domain Transfer Failed - Domain ID: " . $domainid . " - Domain: " . $domain . " - Error: " . $values['error'], $userid);
        run_hook('AfterRegistrarTransferFailed', array( 'params' => $params, 'error' => $values['error'] ));
    }
    else
    {
        update_query('tbldomains', array( 'status' => "Pending Transfer" ), array( 'id' => $domainid ));
        $array = array( 'date' => "now()", 'title' => "Domain Pending Transfer", 'description' => "Check the transfer status of the domain " . $params['sld'] . "." . $params['tld'] . '', 'admin' => '', 'status' => "In Progress", 'duedate' => date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + 5, date('Y'))) );
        insert_query('tbltodolist', $array);
        logActivity("Domain Transfer Initiated Successfully - Domain ID: " . $domainid . " - Domain: " . $domain, $userid);
        run_hook('AfterRegistrarTransfer', array( 'params' => $params ));
    }
    return $values;
}
function RegGetContactDetails($params)
{
    return regcallfunction($params, 'GetContactDetails');
}
function RegSaveContactDetails($params)
{
    $domainObj = new WHMCS_Domains_Domain($params['sld'] . "." . $params['tld']);
    $domainid = get_query_val('tbldomains', 'id', array( 'domain' => $domainObj->getDomain() ));
    $additflds = new WHMCS_Domains_AdditionalFields();
    $params['additionalfields'] = $additflds->getFieldValuesFromDatabase($domainid);
    $originaldetails = $params;
    $params = foreignChrReplace($params);
    $params['original'] = $originaldetails;
    $params['domainObj'] = $domainObj;
    $values = regcallfunction($params, 'SaveContactDetails');
    if( !$values )
    {
        return false;
    }
    $result = select_query('tbldomains', 'userid', array( 'id' => $params['domainid'] ));
    $data = mysql_fetch_array($result);
    $userid = $data[0];
    if( $values['error'] )
    {
        logActivity("Domain Registrar Command: Update Contact Details - Failed: " . $values['error'] . " - Domain ID: " . $params['domainid'], $userid);
    }
    else
    {
        logActivity("Domain Registrar Command: Update Contact Details - Successful", $userid);
    }
    return $values;
}
function RegGetEPPCode($params)
{
    $values = regcallfunction($params, 'GetEPPCode');
    if( !$values )
    {
        return false;
    }
    if( $values['eppcode'] )
    {
        $values['eppcode'] = htmlentities($values['eppcode']);
    }
    return $values;
}
function RegRequestDelete($params)
{
    $values = regcallfunction($params, 'RequestDelete');
    if( !$values )
    {
        return false;
    }
    if( !$values['error'] )
    {
        update_query('tbldomains', array( 'status' => 'Cancelled' ), array( 'id' => $params['domainid'] ));
    }
    return $values;
}
function RegReleaseDomain($params)
{
    return regcallfunction($params, 'ReleaseDomain');
}
function RegRegisterNameserver($params)
{
    return regcallfunction($params, 'RegisterNameserver');
}
function RegModifyNameserver($params)
{
    return regcallfunction($params, 'ModifyNameserver');
}
function RegDeleteNameserver($params)
{
    return regcallfunction($params, 'DeleteNameserver');
}
function RegIDProtectToggle($params)
{
    $domainid = $params['domainid'];
    $result = select_query('tbldomains', 'idprotection', array( 'id' => $domainid ));
    $data = mysql_fetch_assoc($result);
    $idprotection = $data['idprotection'] ? true : false;
    $params['protectenable'] = $idprotection;
    return regcallfunction($params, 'IDProtectToggle');
}
function RegGetDefaultNameservers($params, $domain)
{
    global $CONFIG;
    $serverid = get_query_val('tblhosting', 'server', array( 'domain' => $domain ));
    if( $serverid )
    {
        $result = select_query('tblservers', '', array( 'id' => $serverid ));
        $data = mysql_fetch_array($result);
        for( $i = 1; $i <= 5; $i++ )
        {
            $params['ns' . $i] = trim($data['nameserver' . $i]);
        }
    }
    else
    {
        for( $i = 1; $i <= 5; $i++ )
        {
            $params['ns' . $i] = trim($CONFIG['DefaultNameserver' . $i]);
        }
    }
    return $params;
}
/**
 * Call the GetRegistrantContactEmailAddress function within a registrar module
 * and return the result.
 *
 * @param array $params
 *
 * @return array
 */
function RegGetRegistrantContactEmailAddress($params)
{
    $values = regcallfunction($params, 'GetRegistrantContactEmailAddress');
    if( isset($values['registrantEmail']) )
    {
        return array( 'registrantEmail' => $values['registrantEmail'] );
    }
    return array(  );
}
function RegCustomFunction($params, $func_name)
{
    return regcallfunction($params, $func_name);
}
function RebuildRegistrarModuleHookCache()
{
    $hooksarray = array(  );
    $registrar = new WHMCS_Module_Registrar();
    foreach( $registrar->getList() as $module )
    {
        if( is_file(ROOTDIR . '/modules/registrars/' . $module . "/hooks.php") && get_query_val('tblregistrars', "COUNT(*)", array( 'registrar' => $module )) )
        {
            $hooksarray[] = $module;
        }
    }
    $whmcs = WHMCS_Application::getinstance();
    $whmcs->set_config('RegistrarModuleHooks', implode(',', $hooksarray));
}
function injectDomainObjectIfNecessary($params)
{
    if( !isset($params['domainObj']) || !is_object($params['domainObj']) )
    {
        $params['domainObj'] = new WHMCS_Domains_Domain(sprintf("%s.%s", $params['sld'], $params['tld']));
    }
    return $params;
}