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
function ovh_getConfigArray()
{
    $configarray = array( 'FriendlyName' => array( 'Type' => 'System', 'Value' => 'OVH' ), 'Username' => array( 'Type' => 'text', 'Size' => '20', 'Description' => "Enter your nic handle here xxxxxxx-ovh" ), 'Password' => array( 'Type' => 'password', 'Size' => '20', 'Description' => "Enter your password here" ), 'TestMode' => array( 'Type' => 'yesno', 'Description' => "Enable Test Mode" ) );
    return $configarray;
}
function ovh_GetNameservers($params)
{
    try
    {
        $url = "https://www.ovh.com/soapi/soapi-re-1.14.wsdl";
        $soap = new SoapClient($url, array( 'trace' => 1 ));
        $username = $params['Username'];
        $password = $params['Password'];
        $testmode = $params['TestMode'] ? true : false;
        $session = $soap->login($username, $password, 'en', false);
        $tld = $params['tld'];
        $sld = $params['sld'];
        $domain = $sld . "." . $tld;
        $information = $soap->domainInfo($session, $domain);
        $values['ns1'] = $information->dns[0]->name;
        $values['ns2'] = $information->dns[1]->name;
        $values['ns3'] = $information->dns[2]->name;
        $values['ns4'] = $information->dns[3]->name;
    }
    catch( Exception $e )
    {
        logModuleCall('ovh', "Get Nameservers", $soap->__getLastRequest(), $e . $information, null, $session);
        if( $e->faultstring )
        {
            return array( 'error' => $e->faultstring );
        }
        return array( 'error' => "An unhandled error occurred" );
    }
    $soap->logout($session);
    return $values;
}
function ovh_SaveNameservers($params)
{
    try
    {
        $url = "https://www.ovh.com/soapi/soapi-re-1.14.wsdl";
        $soap = new SoapClient($url, array( 'trace' => 1 ));
        $username = $params['Username'];
        $password = $params['Password'];
        $testmode = $params['TestMode'] ? true : false;
        $session = $soap->login($username, $password, 'en', false);
        $tld = $params['tld'];
        $sld = $params['sld'];
        $domain = $sld . "." . $tld;
        $nameserver1 = $params['ns1'];
        $nameserver2 = $params['ns2'];
        $nameserver3 = $params['ns3'];
        $nameserver4 = $params['ns4'];
        $nameserver5 = $params['ns5'];
        $result = $soap->domainDnsUpdate($session, $domain, $nameserver1, '', $nameserver2, '', $nameserver3, '', $nameserver4, '', $nameserver5, '');
    }
    catch( Exception $e )
    {
        logModuleCall('ovh', "Save Nameservers", $soap->__getLastRequest(), $e . $result, null, $session);
        if( $e->faultstring )
        {
            return array( 'error' => $e->faultstring );
        }
        return array( 'error' => "An unhandled error occurred" );
    }
    $soap->logout($session);
    return $values;
}
function ovh_GetRegistrarLock($params)
{
    ini_set('display_errors', 'off');
    error_reporting(0);
    try
    {
        $url = "https://www.ovh.com/soapi/soapi-re-1.14.wsdl";
        $soap = new SoapClient($url, array( 'trace' => 1 ));
        $username = $params['Username'];
        $password = $params['Password'];
        $testmode = $params['TestMode'] ? true : false;
        $session = $soap->login($username, $password, 'en', false);
        $tld = $params['tld'];
        $sld = $params['sld'];
        $domain = $sld . "." . $tld;
        $information = $soap->domainLockStatus($session, $domain);
        $lock = $information;
    }
    catch( SoapFault $fault )
    {
        logModuleCall('ovh', "Get Registrar Lock", $soap->__getLastRequest(), $fault, null, $session);
        if( $fault )
        {
            return array( 'error' => $fault );
        }
        return array( 'error' => "An unhandled error occurred" );
    }
    if( $lock )
    {
        $lockstatus = 'locked';
    }
    else
    {
        $lockstatus = 'unlocked';
    }
    $soap->logout($session);
    return $lockstatus;
}
function ovh_SaveRegistrarLock($params)
{
    try
    {
        $url = "https://www.ovh.com/soapi/soapi-re-1.14.wsdl";
        $soap = new SoapClient($url, array( 'trace' => 1 ));
        $username = $params['Username'];
        $password = $params['Password'];
        $testmode = $params['TestMode'] ? true : false;
        $session = $soap->login($username, $password, 'en', false);
        $tld = $params['tld'];
        $sld = $params['sld'];
        $domain = $sld . "." . $tld;
        if( $params['lockenabled'] )
        {
            $information = $soap->domainLock($session, $domain);
        }
        else
        {
            $information = $soap->domainUnlock($session, $domain);
        }
    }
    catch( Exception $e )
    {
        logModuleCall('ovh', "Get Registrar Lock", $soap->__getLastRequest(), $e . $information, null, $session);
        if( $e->faultstring )
        {
            return array( 'error' => $e->faultstring );
        }
        return array( 'error' => "An unhandled error occurred" );
    }
    $soap->logout($session);
    return $values;
}
function ovh_RegisterDomain($params)
{
    try
    {
        $url = "https://www.ovh.com/soapi/soapi-re-1.14.wsdl";
        $soap = new SoapClient($url, array( 'trace' => 1 ));
        $username = $params['Username'];
        $password = $params['Password'];
        $testmode = $params['TestMode'] ? true : false;
        $session = $soap->login($username, $password, 'en', false);
        $tld = $params['tld'];
        $sld = $params['sld'];
        $domain = $sld . "." . $tld;
        $regperiod = $params['regperiod'];
        $nameserver1 = $params['ns1'];
        $nameserver2 = $params['ns2'];
        $nameserver3 = $params['ns3'];
        $nameserver4 = $params['ns4'];
        $nameserver5 = $params['ns5'];
        $RegistrantFirstName = $params['firstname'];
        $RegistrantLastName = $params['lastname'];
        $RegistrantCompanyName = $params['companyname'];
        $RegistrantAddress1 = $params['address1'];
        $RegistrantAddress2 = $params['address2'];
        $RegistrantCity = $params['city'];
        $RegistrantStateProvince = $params['state'];
        $RegistrantPostalCode = $params['postcode'];
        $RegistrantCountry = $params['country'];
        $RegistrantEmailAddress = $params['email'];
        $RegistrantPhone = $params['fullphonenumber'];
        $legalform = $params['additionalfields']["Legal Form"] ? $params['additionalfields']["Legal Form"] : ($RegistrantCompanyName ? 'corporation' : 'individual');
        $legalnumber = $params['additionalfields']["Legal Number"] ? $params['additionalfields']["Legal Number"] : '';
        $vat = $params['additionalfields']["VAT Number"] ? $params['additionalfields']["VAT Number"] : '';
        $sex = $params['additionalfields']['Sex'] ? $params['additionalfields']['Sex'] : 'Male';
        $birthday = $params['additionalfields']["Birth Day"] ? $params['additionalfields']["Birth Day"] : '';
        $birthcity = $params['additionalfields']["Birth City"] ? $params['additionalfields']["Birth City"] : $RegistrantCity;
        $nin = $params['additionalfields']["National Identification Number"] ? $params['additionalfields']["National Identification Number"] : '';
        $cnin = $params['additionalfields']["Company National Identification Number"] ? $params['additionalfields']["Company National Identification Number"] : 'Male';
        $corptype = $params['additionalfields']["Corporation Type"] ? $params['additionalfields']["Corporation Type"] : 'individuale';
        if( $tld == 'it' )
        {
            $owner = $soap->nicCreateIT($session, $RegistrantLastName, $RegistrantFirstName, $sex, md5($sld), $RegistrantEmailAddress, $RegistrantPhone, '', $RegistrantAddress1, $RegistrantCity, $RegistrantStateProvince, $RegistrantPostalCode, $RegistrantCountry, 'en', true, $legalform, $RegistrantCompanyName, $RegistrantFirstName . " " . $RegistrantLastName, $legalnumber, $vat, $birthday, $birthcity, $nin, $cnin, $corptype);
        }
        else
        {
            $owner = $soap->nicCreate($session, $RegistrantLastName, $RegistrantFirstName, md5($sld), $RegistrantEmailAddress, $RegistrantPhone, '', $RegistrantAddress1, $RegistrantCity, $RegistrantStateProvince, $RegistrantPostalCode, $RegistrantCountry, 'en', true, $legalform, $RegistrantCompanyName, $RegistrantFirstName . " " . $RegistrantLastName, $legalnumber, $vat);
        }
        $AdminFirstName = $params['adminfirstname'];
        $AdminLastName = $params['adminlastname'];
        $AdminCompanyName = $params['admincompanyname'];
        $AdminAddress1 = $params['adminaddress1'];
        $AdminAddress2 = $params['adminaddress2'];
        $AdminCity = $params['admincity'];
        $AdminStateProvince = $params['adminstate'];
        $AdminPostalCode = $params['adminpostcode'];
        $AdminCountry = $params['admincountry'];
        $AdminEmailAddress = $params['adminemail'];
        $AdminPhone = $params['adminfullphonenumber'];
        $legalform = $params['additionalfields']["Legal Form"] ? $params['additionalfields']["Legal Form"] : ($AdminCompanyName ? 'corporation' : 'individual');
        $admin = $soap->nicCreate($session, $AdminLastName, $AdminFirstName, md5($sld), $AdminEmailAddress, $AdminPhone, '', $AdminAddress1, $AdminCity, $AdminStateProvince, $AdminPostalCode, $AdminCountry, 'en', false, $legalform, $AdminCompanyName, $AdminFirstName . " " . $AdminLastName, '', '');
        $owo = 'no';
        $owoexts = array( ".com", ".net", ".org", ".info", ".biz" );
        if( $params['idprotection'] && in_array("{." . $tld . "}", $owoexts) )
        {
            $owo = 'yes';
        }
        if( $tld == 'fr' )
        {
            $method = $params['additionalfields']['method'];
            $legalName = $params['additionalfields']['legalName'];
            $legalNumber = $params['additionalfields']['legalNumber'];
            $afnicIdent = $params['additionalfields']['afnicIdent'];
            $birthDate = $params['additionalfields']['birthDate'];
            $birthCity = $params['additionalfields']['birthCity'];
            $birthDepartement = $params['additionalfields']['birthDepartement'];
            $birthCountry = $params['additionalfields']['birthCountry'];
        }
        if( $tld == 'asia' )
        {
            $cedcea = $params['additionalfields']['CEDCEA'];
            $localitycity = $params['additionalfields']['localityCity'];
            $localitysp = $params['additionalfields']['localitysp'];
            $cclocality = $params['additionalfields']['ccLocality'];
            $legalentitytype = $params['additionalfields']['legalEntityType'];
            $otherletype = $params['additionalfields']['otherLEType'];
            $identform = $params['additionalfields']['identForm'];
            $otheridentform = $params['additionalfields']['otherIdentForm'];
            $identno = $params['additionalfields']['identNumber'];
            $soap->resellerDomainCreateASIA($session, $domain, 'none', 'gold', 'whiteLabel', $owo, $owner, $username, $admin, $username, $nameserver1, $nameserver2, $nameserver3, $nameserver4, '', $cedcea, $owner, $localitycity, $localitysp, $cclocality, $legalentitytype, $otherletype, $identform, $otheridentform, $identno, $testmode);
        }
        else
        {
            if( $tld == 'cat' )
            {
                $reason = $params['additionalfields']['Reason'];
                $soap->resellerDomainCreateCAT($session, $domain, 'none', 'gold', 'whiteLabel', $owo, $owner, $username, $admin, $username, $nameserver1, $nameserver2, $nameserver3, $nameserver4, '', $reason, $testmode);
            }
            else
            {
                if( $tld == 'it' )
                {
                    $legalRepresentantFirstName = $params['additionalfields']['legalRepresentantFirstName'];
                    $legalRepresentantLastName = $params['additionalfields']['legalRepresentantLastName'];
                    $legalNumber = $params['additionalfields']['legalNumber'];
                    $vat = $params['additionalfields']['vat'];
                    $birthDate = $params['additionalfields']['birthDate'];
                    $birthCity = $params['additionalfields']['birthCity'];
                    $birthDepartement = $params['additionalfields']['birthDepartement'];
                    $birthCountry = $params['additionalfields']['birthCountry'];
                    $nationality = $params['additionalfields']['nationality'];
                    $soap->resellerDomainCreateIT($session, $domain, 'none', 'gold', 'whiteLabel', $owo, $owner, $username, $admin, $username, $nameserver1, $nameserver2, $nameserver3, $nameserver4, '', $legalRepresentantFirstName, $legalRepresentantLastName, $legalNumber, $vat, $birthDate, $birthCity, $birthDepartement, $birthCountry, $nationality, $testmode);
                }
                else
                {
                    $soap->resellerDomainCreate($session, $domain, 'none', 'gold', 'whiteLabel', $owo, $owner, $username, $admin, $username, $nameserver1, $nameserver2, $nameserver3, $nameserver4, '', $method, $legalName, $legalNumber, $afnicIdent, $birthDate, $birthCity, $birthDepartement, $birthCountry, $testmode);
                }
            }
        }
        return $values;
    }
    catch( Exception $e )
    {
        logModuleCall('ovh', "Register Domain", $soap->__getLastRequest(), $e . $url, null, $session);
        if( $e->faultstring )
        {
            return array( 'error' => $e->faultstring );
        }
        return array( 'error' => "An unhandled error occurred" );
    }
    $soap->logout($session);
}
function ovh_TransferDomain($params)
{
    try
    {
        $url = "https://www.ovh.com/soapi/soapi-re-1.14.wsdl";
        $soap = new SoapClient($url, array( 'trace' => 1 ));
        $username = $params['Username'];
        $password = $params['Password'];
        $testmode = $params['TestMode'] ? true : false;
        $transfersecret = $params['transfersecret'];
        $session = $soap->login($username, $password, 'en', false);
        $tld = $params['tld'];
        $sld = $params['sld'];
        $domain = $sld . "." . $tld;
        $regperiod = $params['regperiod'];
        $nameserver1 = $params['ns1'];
        $nameserver2 = $params['ns2'];
        $nameserver3 = $params['ns3'];
        $nameserver4 = $params['ns4'];
        $nameserver5 = $params['ns5'];
        $RegistrantFirstName = $params['firstname'];
        $RegistrantLastName = $params['lastname'];
        $RegistrantCompanyName = $params['companyname'];
        $RegistrantAddress1 = $params['address1'];
        $RegistrantAddress2 = $params['address2'];
        $RegistrantCity = $params['city'];
        $RegistrantStateProvince = $params['state'];
        $RegistrantPostalCode = $params['postcode'];
        $RegistrantCountry = $params['country'];
        $RegistrantEmailAddress = $params['email'];
        $RegistrantPhone = $params['fullphonenumber'];
        $legalform = $params['additionalfields']["Legal Form"] ? $params['additionalfields']["Legal Form"] : ($RegistrantCompanyName ? 'corporation' : 'individual');
        $legalnumber = $params['additionalfields']["Legal Number"] ? $params['additionalfields']["Legal Number"] : '';
        $vat = $params['additionalfields']["VAT Number"] ? $params['additionalfields']["VAT Number"] : '';
        $sex = $params['additionalfields']['Sex'] ? $params['additionalfields']['Sex'] : 'Male';
        $birthday = $params['additionalfields']["Birth Day"] ? $params['additionalfields']["Birth Day"] : '';
        $birthcity = $params['additionalfields']["Birth City"] ? $params['additionalfields']["Birth City"] : $RegistrantCity;
        $nin = $params['additionalfields']["National Identification Number"] ? $params['additionalfields']["National Identification Number"] : '';
        $cnin = $params['additionalfields']["Company National Identification Number"] ? $params['additionalfields']["Company National Identification Number"] : 'Male';
        $corptype = $params['additionalfields']["Corporation Type"] ? $params['additionalfields']["Corporation Type"] : 'individuale';
        if( $tld == 'it' )
        {
            $owner = $soap->nicCreateIT($session, $sld . $tld . 'Owner', $RegistrantFirstName, $sex, md5($sld), $RegistrantEmailAddress, $RegistrantPhone, '', $RegistrantAddress1, $RegistrantCity, $RegistrantStateProvince, $RegistrantPostalCode, $RegistrantCountry, 'en', true, $legalform, $RegistrantCompanyName, $RegistrantFirstName . " " . $RegistrantLastName, $legalnumber, $vat, $birthday, $birthcity, $nin, $cnin, $corptype);
        }
        else
        {
            $owner = $soap->nicCreate($session, $sld . $tld . 'Owner', $RegistrantFirstName, md5($sld), $RegistrantEmailAddress, $RegistrantPhone, '', $RegistrantAddress1, $RegistrantCity, $RegistrantStateProvince, $RegistrantPostalCode, $RegistrantCountry, 'en', true, $legalform, $RegistrantCompanyName, $RegistrantFirstName . " " . $RegistrantLastName, $legalnumber, $vat);
        }
        $AdminFirstName = $params['adminfirstname'];
        $AdminLastName = $params['adminlastname'];
        $AdminCompanyName = $params['admincompanyname'];
        $AdminAddress1 = $params['adminaddress1'];
        $AdminAddress2 = $params['adminaddress2'];
        $AdminCity = $params['admincity'];
        $AdminStateProvince = $params['adminstate'];
        $AdminPostalCode = $params['adminpostcode'];
        $AdminCountry = $params['admincountry'];
        $AdminEmailAddress = $params['adminemail'];
        $AdminPhone = $params['adminfullphonenumber'];
        $legalform = $params['additionalfields']["Legal Form"] ? $params['additionalfields']["Legal Form"] : ($AdminCompanyName ? 'corporation' : 'individual');
        $admin = $soap->nicCreate($session, $sld . $tld, $AdminFirstName, md5($sld), $AdminEmailAddress, $AdminPhone, '', $AdminAddress1, $AdminCity, $AdminStateProvince, $AdminPostalCode, $AdminCountry, 'en', false, $legalform, $AdminCompanyName, $AdminFirstName . " " . $AdminLastName, '', '');
        $owo = 'no';
        $owoexts = array( ".com", ".net", ".org", ".info", ".biz" );
        if( $params['idprotection'] && in_array("{." . $tld . "}", $owoexts) )
        {
            $owo = 'yes';
        }
        if( $tld == 'fr' )
        {
            $method = $params['additionalfields']['method'];
            $legalName = $params['additionalfields']['legalName'];
            $legalNumber = $params['additionalfields']['legalNumber'];
            $afnicIdent = $params['additionalfields']['afnicIdent'];
            $birthDate = $params['additionalfields']['birthDate'];
            $birthCity = $params['additionalfields']['birthCity'];
            $birthDepartement = $params['additionalfields']['birthDepartement'];
            $birthCountry = $params['additionalfields']['birthCountry'];
        }
        if( $tld == 'asia' )
        {
            $cedcea = $params['additionalfields']['CEDCEA'];
            $localitycity = $params['additionalfields']['localityCity'];
            $localitysp = $params['additionalfields']['localitysp'];
            $cclocality = $params['additionalfields']['ccLocality'];
            $legalentitytype = $params['additionalfields']['legalEntityType'];
            $otherletype = $params['additionalfields']['otherLEType'];
            $identform = $params['additionalfields']['identForm'];
            $otheridentform = $params['additionalfields']['otherIdentForm'];
            $identno = $params['additionalfields']['identNumber'];
            $soap->resellerDomainTransferASIA($session, $domain, $transfersecret, 'none', 'gold', 'whiteLabel', $owo, $owner, $username, $admin, $username, $nameserver1, $nameserver2, $nameserver3, $nameserver4, '', $cedcea, $owner, $localitycity, $localitysp, $cclocality, $legalentitytype, $otherletype, $identform, $otheridentform, $identno, $testmode);
        }
        else
        {
            if( $tld == 'it' )
            {
                $legalRepresentantFirstName = $params['additionalfields']['legalRepresentantFirstName'];
                $legalRepresentantLastName = $params['additionalfields']['legalRepresentantLastName'];
                $legalNumber = $params['additionalfields']['legalNumber'];
                $vat = $params['additionalfields']['vat'];
                $birthDate = $params['additionalfields']['birthDate'];
                $birthCity = $params['additionalfields']['birthCity'];
                $birthDepartement = $params['additionalfields']['birthDepartement'];
                $birthCountry = $params['additionalfields']['birthCountry'];
                $nationality = $params['additionalfields']['nationality'];
                $soap->resellerDomainTransferIT($session, $domain, $transfersecret, 'none', 'gold', 'whiteLabel', $owo, $owner, $username, $admin, $username, $nameserver1, $nameserver2, $nameserver3, $nameserver4, $nameserver5, $legalRepresentantFirstName, $legalRepresentantLastName, $legalNumber, $vat, $birthDate, $birthCity, $birthDepartement, $birthCountry, $nationality, $testmode);
            }
            else
            {
                $soap->resellerDomainTransfer($session, $domain, $transfersecret, 'none', 'gold', 'whiteLabel', $owo, $owner, $username, $admin, $username, $nameserver1, $nameserver2, $nameserver3, $nameserver4, $nameserver5, $method, $legalName, $legalNumber, $afnicIdent, $birthDate, $birthCity, $birthDepartement, $birthCountry, $testmode);
            }
        }
    }
    catch( Exception $e )
    {
        logModuleCall('ovh', "Transfer Domain", $soap->__getLastRequest(), $e . $url, null, $session);
        if( $e->faultstring )
        {
            return array( 'error' => $e->faultstring );
        }
        return array( 'error' => "An unhandled error occurred" );
    }
}
function ovh_RenewDomain($params)
{
    try
    {
        $url = "https://www.ovh.com/soapi/soapi-re-1.14.wsdl";
        $soap = new SoapClient($url, array( 'trace' => 1 ));
        $username = $params['Username'];
        $password = $params['Password'];
        $testmode = $params['TestMode'] ? true : false;
        $session = $soap->login($username, $password, 'en', false);
        $tld = $params['tld'];
        $sld = $params['sld'];
        $domain = $sld . "." . $tld;
        $soap->resellerDomainRenew($session, $domain, $testmode);
    }
    catch( Exception $e )
    {
        logModuleCall('ovh', "Renew Domain", $soap->__getLastRequest(), $e . $url, null, $session);
        if( $e->faultstring )
        {
            return array( 'error' => $e->faultstring );
        }
        return array( 'error' => "An unhandled error occurred" );
    }
    $soap->logout($session);
}
function ovh_GetContactDetails($params)
{
    try
    {
        $url = "https://www.ovh.com/soapi/soapi-re-1.14.wsdl";
        $soap = new SoapClient($url, array( 'trace' => 1 ));
        $username = $params['Username'];
        $password = $params['Password'];
        $testmode = $params['TestMode'] ? true : false;
        $session = $soap->login($username, $password, 'en', false);
        $tld = $params['tld'];
        $sld = $params['sld'];
        $domain = $sld . "." . $tld;
        $information = $soap->domainInfo($session, $domain);
        $tech = $information->nictech;
        $information = $soap->nicInfo($session, $tech);
        $values['Tech']["Last Name"] = $information->name;
        $values['Tech']["First Name"] = $information->firstname;
        $values['Tech']['Email'] = $information->email;
        $values['Tech']["Legal Form"] = $information->legalform;
        $values['Tech']['Organisation'] = $information->organisation;
        $values['Tech']["Legal Name"] = $information->legalName;
        $values['Tech']["Legal Number"] = $information->legalNumber;
        $values['Tech']['VAT'] = $information->vat;
    }
    catch( Exception $e )
    {
        logModuleCall('ovh', "Get Contact Details", $soap->__getLastRequest(), $e . $url, null, $session);
        if( $e->faultstring )
        {
            return array( 'error' => $e->faultstring );
        }
        return array( 'error' => "An unhandled error occurred" );
    }
    $soap->logout($session);
    return $values;
}
function ovh_SaveContactDetails($params)
{
    ini_set('display_errors', 'off');
    error_reporting(0);
    try
    {
        $url = "https://www.ovh.com/soapi/soapi-re-1.14.wsdl";
        $soap = new SoapClient($url, array( 'trace' => 1 ));
        $username = $params['Username'];
        $password = $params['Password'];
        $testmode = $params['TestMode'] ? true : false;
        $session = $soap->login($username, $password, 'en', false);
        $tld = $params['tld'];
        $sld = $params['sld'];
        $domain = $sld . "." . $tld;
        $information = $soap->domainInfo($session, $domain);
        $tech = $information->nictech;
        $techname = $params['contactdetails']['Tech']["Last Name"];
        $techfirstname = $params['contactdetails']['Tech']["First Name"];
        $techemail = $params['contactdetails']['Tech']['Email'];
        $techlegalform = $params['contactdetails']['Tech']["Legal Form"];
        $techorganisation = $params['contactdetails']['Tech']['Rrganisation'];
        $techlegalName = $params['contactdetails']['Tech']["Legal Name"];
        $techlegalNumber = $params['contactdetails']['Tech']["Legal Number"];
        $techvat = $params['contactdetails']['Tech']['VAT'];
        $soap->nicUpdate($session, $tech, $techname, $techfirstname, $techlegalform, $techorganisation, $techlegalName, $techlegalNumber, $techvat);
        $soap->nicModifyEmail($session, $tech, $techemail);
    }
    catch( Exception $e )
    {
        logModuleCall('ovh', "Save Contact Details", $soap->__getLastRequest(), $e . $url, null, $session);
        if( $e->faultstring )
        {
            return array( 'error' => $e->faultstring );
        }
        return array( 'error' => "An unhandled error occurred" );
    }
    $soap->logout($session);
    return $values;
}
function ovh_GetEPPCode($params)
{
    ini_set('display_errors', 'off');
    error_reporting(0);
    try
    {
        $url = "https://www.ovh.com/soapi/soapi-re-1.14.wsdl";
        $soap = new SoapClient($url, array( 'trace' => 1 ));
        $username = $params['Username'];
        $password = $params['Password'];
        $testmode = $params['TestMode'] ? true : false;
        $session = $soap->login($username, $password, 'en', false);
        $tld = $params['tld'];
        $sld = $params['sld'];
        $domain = $sld . "." . $tld;
        $information = $soap->domainInfo($session, $domain);
        $values['eppcode'] = $information->authinfo;
    }
    catch( Exception $e )
    {
        logModuleCall('ovh', "Get EPP Code", $soap->__getLastRequest(), $e . $url, null, $session);
        if( $e->faultstring )
        {
            return array( 'error' => $e->faultstring );
        }
        return array( 'error' => "An unhandled error occurred" );
    }
    $soap->logout($session);
    return $values;
}