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
class CEnomInterface
{
    public $PostString = NULL;
    public $RawData = NULL;
    public $Values = NULL;
    public function NewRequest()
    {
        $this->PostString = '';
        $this->RawData = '';
        $this->Values = '';
    }
    public function AddError($error)
    {
        $this->Values['ErrCount'] = '1';
        $this->Values['Err1'] = $error;
    }
    public function ParseResponse($buffer)
    {
        if( !$buffer || !is_string($buffer) )
        {
            $errorMsg = "Cannot parse empty response from server - ";
            $errorMsg .= "Please try again later";
            $this->AddError($errorMsg);
            return false;
        }
        $Lines = explode("\r", $buffer);
        $NumLines = count($Lines);
        $i = 0;
        while( !trim($Lines[$i]) )
        {
            $i = $i + 1;
        }
        $StartLine = $i;
        $GotValues = 0;
        for( $i = $StartLine; $i < $NumLines; $i++ )
        {
            if( substr($Lines[$i], 1, 1) != ';' )
            {
                $Result = explode("=", $Lines[$i]);
                if( 2 <= count($Result) )
                {
                    $name = trim($Result[0]);
                    $value = trim($Result[1]);
                    if( $name == 'ApproverEmail' )
                    {
                        $this->Values[$name][] = $value;
                    }
                    else
                    {
                        $this->Values[$name] = $value;
                    }
                    if( $name == 'ErrCount' )
                    {
                        $GotValues = 1;
                    }
                }
            }
        }
        if( $GotValues == 0 )
        {
            $this->AddError("Invalid data response from server - Please try again later");
            return false;
        }
        return true;
    }
    public function AddParam($Name, $Value)
    {
        $this->PostString = $this->PostString . $Name . "=" . urlencode($Value) . "&";
    }
    public function DoTransaction($params, $processResponse = true)
    {
        $Values = '';
        if( $params['TestMode'] )
        {
            $host = "resellertest.enom.com";
        }
        else
        {
            $host = "reseller.enom.com";
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://" . $host . "/interface.asp");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->PostString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $response = curl_exec($ch);
        $this->RawData = '';
        if( curl_error($ch) )
        {
            $responseMsgToPropagate = "CURL Error: " . curl_errno($ch) . " - " . curl_error($ch);
            $this->AddError($responseMsgToPropagate);
        }
        else
        {
            if( !$response )
            {
                $responseMsgToPropagate = "Empty data response from server - Please try again later";
            }
            else
            {
                $this->RawData = $responseMsgToPropagate = $response;
            }
        }
        curl_close($ch);
        if( $processResponse && $response )
        {
            $this->ParseResponse($response);
        }
        if( function_exists('logModuleCall') )
        {
            $action = $this->getActionFromQuery($this->PostString);
            logModuleCall('enom', $action, $this->PostString, $responseMsgToPropagate, '', array( $params['Username'], $params['Password'] ));
        }
        return $this->RawData;
    }
    /**
     * Obtain the action occurring within the eNom module to be used in the
     * logModuleCall function.
     * @param string $query the full string being sent to eNom
     * @return string the action being completed
     */
    public function getActionFromQuery($query)
    {
        $action = "Unknown Action";
        if( is_string($query) )
        {
            $queryParts = explode("command=", $query, 2);
            if( isset($queryParts[1]) )
            {
                $commandQuery = explode("&", $queryParts[1], 2);
                $action = $commandQuery[0];
            }
        }
        return $action;
    }
}
function enom_getConfigArray()
{
    $configarray = array( 'Description' => array( 'Type' => 'System', 'Value' => "Don't have an Enom Account yet? Get one here: <a href=\"http://nullrefer.com/?http://go.whmcs.com/82/enom\" target=\"_blank\">www.whmcs.com/partners/enom</a>" ), 'Username' => array( 'Type' => 'text', 'Size' => '20', 'Description' => "Enter your Enom Reseller Account Username here" ), 'Password' => array( 'Type' => 'password', 'Size' => '20', 'Description' => "Enter your Enom Reseller Account Password here" ), 'TestMode' => array( 'Type' => 'yesno' ), 'DefaultNameservers' => array( 'Type' => 'yesno', 'Description' => "Tick this box to use the default Enom nameservers for new domain registrations" ) );
    return $configarray;
}
function enom_GetNameservers($params)
{
    $params = injectDomainObjectIfNecessary($params);
    $Enom = new CEnomInterface();
    $Enom->NewRequest();
    $Enom->AddParam('uid', $params['Username']);
    $Enom->AddParam('pw', $params['Password']);
    $Enom->AddParam('tld', $params['tld']);
    $Enom->AddParam('sld', $params['sld']);
    $Enom->AddParam('command', 'getdns');
    $Enom->DoTransaction($params);
    $values = array(  );
    for( $i = 1; $i <= 12; $i++ )
    {
        $values['ns' . $i] = $Enom->Values['DNS' . $i];
    }
    if( $Enom->Values['Err1'] )
    {
        $values['error'] = $Enom->Values['Err1'];
    }
    return $values;
}
function enom_SaveNameservers($params)
{
    $params = injectDomainObjectIfNecessary($params);
    $Enom = new CEnomInterface();
    $Enom->AddParam('uid', $params['Username']);
    $Enom->AddParam('pw', $params['Password']);
    $Enom->AddParam('tld', $params['tld']);
    $Enom->AddParam('sld', $params['sld']);
    $Enom->AddParam('NS1', $params['ns1']);
    $Enom->AddParam('NS2', $params['ns2']);
    $Enom->AddParam('NS3', $params['ns3']);
    $Enom->AddParam('NS4', $params['ns4']);
    $Enom->AddParam('NS5', $params['ns5']);
    $Enom->AddParam('command', 'modifyns');
    $Enom->DoTransaction($params);
    $values['error'] = $Enom->Values['Err1'];
    return $values;
}
function enom_GetRegistrarLock($params)
{
    $params = injectDomainObjectIfNecessary($params);
    $Enom = new CEnomInterface();
    $Enom->NewRequest();
    $Enom->AddParam('uid', $params['Username']);
    $Enom->AddParam('pw', $params['Password']);
    $Enom->AddParam('tld', $params['tld']);
    $Enom->AddParam('sld', $params['sld']);
    $Enom->AddParam('command', 'getreglock');
    $Enom->DoTransaction($params);
    if( $Enom->Values['ErrCount'] == '0' )
    {
        $lock = $Enom->Values['RegLock'];
        if( $Enom->Values['IsLockable'] == 'True' )
        {
            if( $lock == '1' )
            {
                $lockstatus = 'locked';
            }
            else
            {
                $lockstatus = 'unlocked';
            }
        }
        return $lockstatus;
    }
}
function enom_SaveRegistrarLock($params)
{
    $params = injectDomainObjectIfNecessary($params);
    if( $params['lockenabled'] == 'locked' )
    {
        $lockstatus = '0';
    }
    else
    {
        $lockstatus = '1';
    }
    $Enom = new CEnomInterface();
    $Enom->NewRequest();
    $Enom->AddParam('uid', $params['Username']);
    $Enom->AddParam('pw', $params['Password']);
    $Enom->AddParam('tld', $params['tld']);
    $Enom->AddParam('sld', $params['sld']);
    $Enom->AddParam('UnlockRegistrar', $lockstatus);
    $Enom->AddParam('command', 'setreglock');
    $Enom->DoTransaction($params);
    if( $Enom->Values['ErrCount'] != '0' )
    {
        $values['error'] = $Enom->Values['Err1'];
    }
    return $values;
}
function enom_GetEmailForwarding($params)
{
    $params = injectDomainObjectIfNecessary($params);
    $Enom = new CEnomInterface();
    $Enom->AddParam('uid', $params['Username']);
    $Enom->AddParam('pw', $params['Password']);
    $Enom->AddParam('tld', $params['tld']);
    $Enom->AddParam('sld', $params['sld']);
    $Enom->AddParam('command', 'getforwarding');
    $Enom->DoTransaction($params);
    $counter = 1;
    while( $counter <= 100 )
    {
        if( $Enom->Values['Username' . $counter] )
        {
            $values[$counter]['prefix'] = $Enom->Values['Username' . $counter];
            $values[$counter]['forwardto'] = $Enom->Values['ForwardTo' . $counter];
        }
        $counter += 1;
    }
    return $values;
}
function enom_SaveEmailForwarding($params)
{
    $params = injectDomainObjectIfNecessary($params);
    $Enom = new CEnomInterface();
    $Enom->AddParam('uid', $params['Username']);
    $Enom->AddParam('pw', $params['Password']);
    $Enom->AddParam('tld', $params['tld']);
    $Enom->AddParam('sld', $params['sld']);
    foreach( $params['prefix'] as $key => $value )
    {
        $Enom->AddParam('Address' . $key, $params['prefix'][$key]);
        $Enom->AddParam('ForwardTo' . $key, $params['forwardto'][$key]);
    }
    $Enom->AddParam('command', 'forwarding');
    $Enom->DoTransaction($params);
    $values['error'] = $Enom->Values['Err1'];
    return $values;
}
function enom_GetDNS($params)
{
    $params = injectDomainObjectIfNecessary($params);
    $hostRecords = array(  );
    $Enom = new CEnomInterface();
    $Enom->AddParam('uid', $params['Username']);
    $Enom->AddParam('pw', $params['Password']);
    $Enom->AddParam('tld', $params['tld']);
    $Enom->AddParam('sld', $params['sld']);
    $Enom->AddParam('command', 'gethosts');
    $Enom->AddParam('ResponseType', 'XML');
    $xmlData = $Enom->DoTransaction($params, false);
    $arrayData = XMLtoArray($xmlData);
    if( $xmlData )
    {
        foreach( $arrayData['INTERFACE-RESPONSE'] as $k => $values )
        {
            if( substr($k, 0, 4) == 'HOST' )
            {
                $hostRecords[] = array( 'hostname' => $values['NAME'], 'type' => $values['TYPE'], 'address' => $values['ADDRESS'], 'priority' => $values['MXPREF'] );
            }
        }
    }
    return $hostRecords;
}
function enom_SaveDNS($params)
{
    $params = injectDomainObjectIfNecessary($params);
    foreach( $params['dnsrecords'] as $key => $values )
    {
        if( $values && $values['address'] )
        {
            $key++;
            $newvalues['HostName' . $key] = $values['hostname'];
            $newvalues['RecordType' . $key] = $values['type'];
            $newvalues['Address' . $key] = $values['address'];
            if( $values['type'] == 'MX' )
            {
                $newvalues['MXPref' . $key] = $values['priority'];
            }
        }
    }
    $Enom = new CEnomInterface();
    $Enom->AddParam('uid', $params['Username']);
    $Enom->AddParam('pw', $params['Password']);
    $Enom->AddParam('tld', $params['tld']);
    $Enom->AddParam('sld', $params['sld']);
    foreach( $newvalues as $key => $value )
    {
        $Enom->AddParam($key, $value);
    }
    $Enom->AddParam('command', 'sethosts');
    $Enom->DoTransaction($params);
    $values['error'] = $Enom->Values['Err1'];
    return $values;
}
function enom_RegisterDomain($params)
{
    $params = injectDomainObjectIfNecessary($params);
    $Enom = new CEnomInterface();
    $Enom->NewRequest();
    $Enom->AddParam('uid', $params['Username']);
    $Enom->AddParam('pw', $params['Password']);
    $Enom->AddParam('tld', $params['tld']);
    $Enom->AddParam('sld', $params['sld']);
    $Enom->AddParam('numyears', $params['regperiod']);
    $Enom->AddParam('IgnoreNSFail', 'Yes');
    $Enom->AddParam('EmailNotify', '1');
    if( $params['DefaultNameservers'] )
    {
        $Enom->AddParam('UseDNS', 'default');
    }
    else
    {
        $Enom->AddParam('NS1', $params['ns1']);
        $Enom->AddParam('NS2', $params['ns2']);
        $Enom->AddParam('NS3', $params['ns3']);
        $Enom->AddParam('NS4', $params['ns4']);
        $Enom->AddParam('NS5', $params['ns5']);
    }
    if( $params['companyname'] )
    {
        $jobtitle = 'Director';
    }
    $params = enom_NormalizeContactDetails($params);
    $Enom->AddParam('RegistrantFirstName', $params['firstname']);
    $Enom->AddParam('RegistrantLastName', $params['lastname']);
    $Enom->AddParam('RegistrantOrganizationName', $params['companyname']);
    $Enom->AddParam('RegistrantJobTitle', $jobtitle);
    $Enom->AddParam('RegistrantAddress1', $params['address1']);
    $Enom->AddParam('RegistrantAddress2', $params['address2']);
    $Enom->AddParam('RegistrantCity', $params['city']);
    $Enom->AddParam('RegistrantStateProvince', $params['state']);
    $Enom->AddParam('RegistrantPostalCode', $params['postcode']);
    $Enom->AddParam('RegistrantCountry', $params['country']);
    $Enom->AddParam('RegistrantEmailAddress', $params['email']);
    $Enom->AddParam('RegistrantPhone', $params['fullphonenumber']);
    if( $params['country'] == 'US' )
    {
        $Enom->AddParam('RegistrantStateProvinceChoice', 'S');
    }
    else
    {
        $Enom->AddParam('RegistrantStateProvinceChoice', 'P');
    }
    $contacttypes = array( 'Admin', 'Tech', 'AuxBilling' );
    foreach( $contacttypes as $contacttype )
    {
        $Enom->AddParam($contacttype . 'FirstName', $params['adminfirstname']);
        $Enom->AddParam($contacttype . 'LastName', $params['adminlastname']);
        $Enom->AddParam($contacttype . 'OrganizationName', $params['admincompanyname']);
        $Enom->AddParam($contacttype . 'JobTitle', $jobtitle);
        $Enom->AddParam($contacttype . 'Address1', $params['adminaddress1']);
        $Enom->AddParam($contacttype . 'Address2', $params['adminaddress2']);
        $Enom->AddParam($contacttype . 'City', $params['admincity']);
        $Enom->AddParam($contacttype . 'StateProvince', $params['adminstate']);
        $Enom->AddParam($contacttype . 'PostalCode', $params['adminpostcode']);
        $Enom->AddParam($contacttype . 'Country', $params['admincountry']);
        $Enom->AddParam($contacttype . 'EmailAddress', $params['adminemail']);
        $Enom->AddParam($contacttype . 'Phone', $params['adminfullphonenumber']);
    }
    if( $params['domainObj']->getLastTLDSegment() == 'us' )
    {
        $nexus = $params['additionalfields']["Nexus Category"];
        $countrycode = $params['additionalfields']["Nexus Country"];
        $purpose = $params['additionalfields']["Application Purpose"];
        if( $purpose == "Business use for profit" )
        {
            $purpose = 'P1';
        }
        else
        {
            if( $purpose == "Non-profit business" )
            {
                $purpose = 'P2';
            }
            else
            {
                if( $purpose == 'Club' )
                {
                    $purpose = 'P2';
                }
                else
                {
                    if( $purpose == 'Association' )
                    {
                        $purpose = 'P2';
                    }
                    else
                    {
                        if( $purpose == "Religious Organization" )
                        {
                            $purpose = 'P2';
                        }
                        else
                        {
                            if( $purpose == "Personal Use" )
                            {
                                $purpose = 'P3';
                            }
                            else
                            {
                                if( $purpose == "Educational purposes" )
                                {
                                    $purpose = 'P4';
                                }
                                else
                                {
                                    if( $purpose == "Government purposes" )
                                    {
                                        $purpose = 'P5';
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        switch( $nexus )
        {
            case 'C11':
            case 'C12':
                break;
            case 'C21':
                $Enom->AddParam('us_nexus', $nexus);
                break;
            case 'C31':
                break;
            case 'C32':
                $Enom->AddParam('us_nexus', $nexus);
                $Enom->AddParam('global_cc_us', $countrycode);
        }
        $Enom->AddParam('us_purpose', $purpose);
    }
    else
    {
        if( $params['domainObj']->getLastTLDSegment() == 'uk' )
        {
            if( $params['additionalfields']["Legal Type"] == "UK Limited Company" )
            {
                $uklegaltype = 'LTD';
            }
            else
            {
                if( $params['additionalfields']["Legal Type"] == "UK Public Limited Company" )
                {
                    $uklegaltype = 'PLC';
                }
                else
                {
                    if( $params['additionalfields']["Legal Type"] == "UK Partnership" )
                    {
                        $uklegaltype = 'PTNR';
                    }
                    else
                    {
                        if( $params['additionalfields']["Legal Type"] == "UK Limited Liability Partnership" )
                        {
                            $uklegaltype = 'LLP';
                        }
                        else
                        {
                            if( $params['additionalfields']["Legal Type"] == "Sole Trader" )
                            {
                                $uklegaltype = 'STRA';
                            }
                            else
                            {
                                if( $params['additionalfields']["Legal Type"] == "UK Registered Charity" )
                                {
                                    $uklegaltype = 'RCHAR';
                                }
                                else
                                {
                                    if( $params['additionalfields']["Legal Type"] == "UK Industrial/Provident Registered Company" )
                                    {
                                        $uklegaltype = 'IP';
                                    }
                                    else
                                    {
                                        if( $params['additionalfields']["Legal Type"] == "UK School" )
                                        {
                                            $uklegaltype = 'SCH';
                                        }
                                        else
                                        {
                                            if( $params['additionalfields']["Legal Type"] == "UK Government Body" )
                                            {
                                                $uklegaltype = 'GOV';
                                            }
                                            else
                                            {
                                                if( $params['additionalfields']["Legal Type"] == "UK Corporation by Royal Charter" )
                                                {
                                                    $uklegaltype = 'CRC';
                                                }
                                                else
                                                {
                                                    if( $params['additionalfields']["Legal Type"] == "UK Statutory Body" )
                                                    {
                                                        $uklegaltype = 'STAT';
                                                    }
                                                    else
                                                    {
                                                        if( $params['additionalfields']["Legal Type"] == "Non-UK Individual" )
                                                        {
                                                            $uklegaltype = 'FIND';
                                                        }
                                                        else
                                                        {
                                                            if( $params['additionalfields']["Legal Type"] == "Foreign Organization" )
                                                            {
                                                                $uklegaltype = 'FCORP';
                                                            }
                                                            else
                                                            {
                                                                if( $params['additionalfields']["Legal Type"] == "Other foreign organizations" )
                                                                {
                                                                    $uklegaltype = 'FOTHER';
                                                                }
                                                                else
                                                                {
                                                                    $uklegaltype = 'IND';
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $ukregoptout = 'n';
            if( $params['additionalfields']["WHOIS Opt-out"] && $uklegaltype == 'IND' )
            {
                $ukregoptout = 'y';
            }
            $Enom->AddParam('uk_legal_type', $uklegaltype);
            $Enom->AddParam('uk_reg_co_no', strtoupper($params['additionalfields']["Company ID Number"]));
            $Enom->AddParam('registered_for', $params['additionalfields']["Registrant Name"]);
            $Enom->AddParam('uk_reg_opt_out', $ukregoptout);
        }
        else
        {
            if( $params['domainObj']->getLastTLDSegment() == 'ca' )
            {
                if( $params['additionalfields']["Legal Type"] == 'Corporation' )
                {
                    $legaltype = 'CCO';
                }
                else
                {
                    if( $params['additionalfields']["Legal Type"] == "Canadian Citizen" )
                    {
                        $legaltype = 'CCT';
                    }
                    else
                    {
                        if( $params['additionalfields']["Legal Type"] == "Permanent Resident of Canada" )
                        {
                            $legaltype = 'RES';
                        }
                        else
                        {
                            if( $params['additionalfields']["Legal Type"] == 'Government' )
                            {
                                $legaltype = 'GOV';
                            }
                            else
                            {
                                if( $params['additionalfields']["Legal Type"] == "Canadian Educational Institution" )
                                {
                                    $legaltype = 'EDU';
                                }
                                else
                                {
                                    if( $params['additionalfields']["Legal Type"] == "Canadian Unincorporated Association" )
                                    {
                                        $legaltype = 'ASS';
                                    }
                                    else
                                    {
                                        if( $params['additionalfields']["Legal Type"] == "Canadian Hospital" )
                                        {
                                            $legaltype = 'HOP';
                                        }
                                        else
                                        {
                                            if( $params['additionalfields']["Legal Type"] == "Partnership Registered in Canada" )
                                            {
                                                $legaltype = 'PRT';
                                            }
                                            else
                                            {
                                                if( $params['additionalfields']["Legal Type"] == "Trade-mark registered in Canada" )
                                                {
                                                    $legaltype = 'TDM';
                                                }
                                                else
                                                {
                                                    $legaltype = 'CCO';
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $whoisoptout = 'FULL';
                if( $params['additionalfields']["WHOIS Opt-out"] && ($legaltype == 'CCT' || $legaltype == 'RES') )
                {
                    $whoisoptout = 'PRIVATE';
                }
                $ciraagreement = 'N';
                if( $params['additionalfields']["CIRA Agreement"] )
                {
                    $ciraagreement = 'Y';
                }
                $Enom->AddParam('cira_legal_type', $legaltype);
                $Enom->AddParam('cira_whois_display', $whoisoptout);
                $Enom->AddParam('cira_language', 'en');
                $Enom->AddParam('cira_agreement_version', "2.0");
                $Enom->AddParam('cira_agreement_value', $ciraagreement);
                if( $ciraagreement == 'N' )
                {
                    return array( 'error' => "The CIRA Agreement must be agreed to by the customer before the domain can be registered" );
                }
            }
            else
            {
                if( $params['domainObj']->getLastTLDSegment() == 'eu' )
                {
                    $Enom->AddParam('eu_whoispolicy', "I AGREE");
                    $Enom->AddParam('eu_agreedelete', 'YES');
                    $Enom->AddParam('eu_adr_lang', 'EN');
                }
                else
                {
                    if( $params['domainObj']->getLastTLDSegment() == 'it' )
                    {
                        $Enom->AddParam('it_consentforpublishing', $params['additionalfields']["Publish Personal Data"] ? '1' : '0');
                        $Enom->AddParam('it_personal_data_for_reg', $params['additionalfields']["Consent for Processing of Information"] ? '1' : '0');
                        $Enom->AddParam('it_datafordiffusion', $params['additionalfields']["Consent for Dissemination and Accessibility via the Internet"] ? '1' : '0');
                        $Enom->AddParam('it_agreedelete', 'YES');
                        $Enom->AddParam('it_sect3_liability', $params['additionalfields']["Accept Section 3 of .IT registrar contract"] ? '1' : '0');
                        $Enom->AddParam('it_explicit_acceptance', $params['additionalfields']["Explicit Acceptance of Registry Terms"] ? '1' : '0');
                        $Enom->AddParam('it_pin', $params['additionalfields']["Tax ID"]);
                        $Enom->AddParam('it_entity_type', substr($params['additionalfields']["Type of Registrant Entity"], 0, 1));
                    }
                    else
                    {
                        if( $params['domainObj']->getLastTLDSegment() == 'de' )
                        {
                            $Enom->AddParam('confirmaddress', 'DE');
                            $Enom->AddParam('de_agreedelete', 'YES');
                        }
                        else
                        {
                            if( $params['domainObj']->getLastTLDSegment() == 'nl' )
                            {
                                $Enom->AddParam('nl_agreedelete', 'YES');
                            }
                            else
                            {
                                if( $params['domainObj']->getLastTLDSegment() == 'fm' )
                                {
                                    $Enom->AddParam('fm_agreedelete', 'YES');
                                }
                                else
                                {
                                    if( $params['domainObj']->getLastTLDSegment() == 'be' )
                                    {
                                        $Enom->AddParam('be_agreedelete', 'YES');
                                    }
                                    else
                                    {
                                        if( $params['domainObj']->getLastTLDSegment() == 'nz' )
                                        {
                                            $Enom->AddParam("co.nz_agreedelete", 'YES');
                                        }
                                        else
                                        {
                                            if( $params['domainObj']->getLastTLDSegment() == 'tel' )
                                            {
                                                $telregoptout = 'NO';
                                                if( $params['additionalfields']["Registrant Type"] == "Legal Person" )
                                                {
                                                    $regtype = 'legal_person';
                                                }
                                                else
                                                {
                                                    $regtype = 'natural_person';
                                                    if( $params['additionalfields']["WHOIS Opt-out"] )
                                                    {
                                                        $telregoptout = 'YES';
                                                    }
                                                }
                                                $telpw = '';
                                                $length = 10;
                                                $seeds = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVYWXYZ';
                                                $seeds_count = strlen($seeds) - 1;
                                                for( $i = 0; $i < $length; $i++ )
                                                {
                                                    $telpw .= $seeds[rand(0, $seeds_count)];
                                                }
                                                if( is_numeric(substr($telpw, 0, 1)) )
                                                {
                                                    $telpw = 'a' . $telpw;
                                                }
                                                $Enom->AddParam('tel_whoistype', $regtype);
                                                $Enom->AddParam('tel_publishwhois', $telregoptout);
                                                $Enom->AddParam('tel_username', strtolower($params['firstname'] . $params['lastname'] . $params['domainid']));
                                                $Enom->AddParam('tel_password', $telpw);
                                                $Enom->AddParam('tel_emailaddress', $params['email']);
                                            }
                                            else
                                            {
                                                if( $params['domainObj']->getLastTLDSegment() == 'pro' )
                                                {
                                                    $Enom->AddParam('pro_profession', $params['additionalfields']['Profession']);
                                                }
                                                else
                                                {
                                                    if( $params['domainObj']->getLastTLDSegment() == 'es' )
                                                    {
                                                        $params['additionalfields']["ID Form Type"] = explode("|", $params['additionalfields']["ID Form Type"]);
                                                        $idType = $params['additionalfields']["ID Form Type"][0];
                                                        switch( $idType )
                                                        {
                                                            case 'DNI':
                                                            case 'NIF':
                                                                break;
                                                            case "Tax Identification Number":
                                                                break;
                                                            case "Tax Identification Code":
                                                                $idType = 3;
                                                                break;
                                                            case 'NIE':
                                                                break;
                                                            case "Foreigner Identification Number":
                                                                $idType = 1;
                                                                break;
                                                            default:
                                                                $idType = 0;
                                                                break;
                                                        }
                                                        if( !empty($params['additionalfields']["Legal Form"]) )
                                                        {
                                                            $params['additionalfields']["Legal Form"] = explode("|", $params['additionalfields']["Legal Form"]);
                                                            $legalForm = $params['additionalfields']["Legal Form"][0];
                                                            if( !is_int($legalForm) )
                                                            {
                                                                switch( $legalForm )
                                                                {
                                                                    case "Economic Interest Group":
                                                                        $legalForm = 39;
                                                                        break;
                                                                    case 'Association':
                                                                        $legalForm = 47;
                                                                        break;
                                                                    case "Sports Association":
                                                                        $legalForm = 59;
                                                                        break;
                                                                    case "Professional Association":
                                                                        $legalForm = 68;
                                                                        break;
                                                                    case "Savings Bank":
                                                                        $legalForm = 124;
                                                                        break;
                                                                    case "Community Property":
                                                                        $legalForm = 150;
                                                                        break;
                                                                    case "Community of Owners":
                                                                        $legalForm = 152;
                                                                        break;
                                                                    case "Order or Religious Institution":
                                                                        $legalForm = 164;
                                                                        break;
                                                                    case 'Consulate':
                                                                        $legalForm = 181;
                                                                        break;
                                                                    case "Public Law Association":
                                                                        $legalForm = 197;
                                                                        break;
                                                                    case 'Embassy':
                                                                        $legalForm = 203;
                                                                        break;
                                                                    case "Local Authority":
                                                                        $legalForm = 229;
                                                                        break;
                                                                    case "Sports Federation":
                                                                        $legalForm = 269;
                                                                        break;
                                                                    case 'Foundation':
                                                                        $legalForm = 286;
                                                                        break;
                                                                    case "Mutual Insurance Company":
                                                                        $legalForm = 365;
                                                                        break;
                                                                    case "Regional Government Body":
                                                                        $legalForm = 434;
                                                                        break;
                                                                    case "Central Government Body":
                                                                        $legalForm = 436;
                                                                        break;
                                                                    case "Political Party":
                                                                        $legalForm = 439;
                                                                        break;
                                                                    case "Trade Union":
                                                                        $legalForm = 476;
                                                                        break;
                                                                    case "Farm Partnership":
                                                                        $legalForm = 510;
                                                                        break;
                                                                    case "Public Limited Company":
                                                                        $legalForm = 524;
                                                                        break;
                                                                    case "Civil Society":
                                                                        $legalForm = 554;
                                                                        break;
                                                                    case "General Partnership":
                                                                        $legalForm = 560;
                                                                        break;
                                                                    case "General and Limited Partnership":
                                                                        $legalForm = 562;
                                                                        break;
                                                                    case 'Cooperative':
                                                                        $legalForm = 566;
                                                                        break;
                                                                    case "Worker-owned Company":
                                                                        $legalForm = 608;
                                                                        break;
                                                                    case "Limited Company":
                                                                        $legalForm = 612;
                                                                        break;
                                                                    case "Spanish Office":
                                                                        $legalForm = 713;
                                                                        break;
                                                                    case "Temporary Alliance of Enterprises":
                                                                        $legalForm = 717;
                                                                        break;
                                                                    case "Worker-owned Limited Company":
                                                                        $legalForm = 744;
                                                                        break;
                                                                    case "Regional Public Entity":
                                                                        $legalForm = 745;
                                                                        break;
                                                                    case "National Public Entity":
                                                                        $legalForm = 746;
                                                                        break;
                                                                    case "Local Public Entity":
                                                                        $legalForm = 747;
                                                                        break;
                                                                    case 'Others':
                                                                        $legalForm = 877;
                                                                        break;
                                                                    case "Designation of Origin Supervisory Council":
                                                                        $legalForm = 878;
                                                                        break;
                                                                    case "Entity Managing Natural Areas":
                                                                        $legalForm = 879;
                                                                        break;
                                                                    default:
                                                                        $legalForm = 1;
                                                                        break;
                                                                }
                                                            }
                                                        }
                                                        else
                                                        {
                                                            $legalForm = 1;
                                                        }
                                                        $Enom->AddParam('es_legalform', $legalForm);
                                                        if( $legalForm == 1 )
                                                        {
                                                            $Enom->AddParam('es_accepttac', true);
                                                        }
                                                        $Enom->AddParam('es_registrantidtype', $idType);
                                                        $Enom->AddParam('es_registrantid', $params['additionalfields']["ID Form Number"]);
                                                        $Enom->AddParam('es_adminidtype', $idType);
                                                        $Enom->AddParam('es_adminid', $params['additionalfields']["ID Form Number"]);
                                                    }
                                                    else
                                                    {
                                                        if( $params['domainObj']->getLastTLDSegment() == 'au' )
                                                        {
                                                            $idtype = $params['additionalfields']["Registrant ID Type"];
                                                            if( $idtype == "Business Registration Number" )
                                                            {
                                                                $idtype = 'RBN';
                                                            }
                                                            $idnumber = $params['additionalfields']["Eligibility ID"] ? $params['additionalfields']["Eligibility ID"] : $params['additionalfields']["Registrant ID"];
                                                            $Enom->AddParam('au_registrantidtype', $idtype);
                                                            $Enom->AddParam('au_registrantid', $params['additionalfields']["Registrant ID"]);
                                                        }
                                                        else
                                                        {
                                                            if( $params['domainObj']->getLastTLDSegment() == 'sg' )
                                                            {
                                                                $idnumber = $params['additionalfields']["RCB Singapore ID"];
                                                                $Enom->AddParam('sg_rcbid', $idnumber);
                                                            }
                                                            else
                                                            {
                                                                if( $params['domainObj']->getLastTLDSegment() == 'fr' )
                                                                {
                                                                    $additional = $params['additionalfields'];
                                                                    $Enom->AddParam('fr_legaltype', $additional["Legal Type"]);
                                                                    if( $params['countrycode'] == 'FR' )
                                                                    {
                                                                        $Enom->AddParam('fr_registrantbirthplace', $additional["Birthplace Postcode"] . ", " . $additional["Birthplace City"]);
                                                                        $Enom->AddParam('fr_registrantbirthdate', $additional['Birthdate']);
                                                                    }
                                                                    else
                                                                    {
                                                                        $Enom->AddParam('fr_registrantbirthplace', $params['countrycode']);
                                                                        $Enom->AddParam('fr_registrantbirthdate', $additional['Birthdate']);
                                                                    }
                                                                    if( $additional["Legal Type"] == 'Company' )
                                                                    {
                                                                        if( !empty($additional["SIRET Number"]) )
                                                                        {
                                                                            $Enom->AddParam('fr_registrantlegalid', $additional["SIRET Number"]);
                                                                        }
                                                                        if( !empty($additional["DUNS Number"]) )
                                                                        {
                                                                            $Enom->AddParam('fr_registrantdunsnumber', $additional["DUNS Number"]);
                                                                        }
                                                                    }
                                                                }
                                                                else
                                                                {
                                                                    if( $params['domainObj']->getLastTLDSegment() == 'quebec' )
                                                                    {
                                                                        $intendedUse = $params['additionalfields']["Intended Use"];
                                                                        $intendedUseTruncated = substr($intendedUse, 0, 2048);
                                                                        $Enom->AddParam('core_intendeduse', $intendedUseTruncated);
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    $Enom->AddParam('command', 'purchase');
    $Enom->DoTransaction($params);
    $values['error'] = $Enom->Values['Err1'];
    if( !$values['error'] && $Enom->Values['RRPCode'] != '200' )
    {
        $values['error'] = $Enom->Values['RRPText'];
    }
    if( $params['idprotection'] && !$values['error'] )
    {
        $Enom->NewRequest();
        $Enom->AddParam('uid', $params['Username']);
        $Enom->AddParam('pw', $params['Password']);
        $Enom->AddParam('ProductType', 'IDProtect');
        $Enom->AddParam('TLD', $params['tld']);
        $Enom->AddParam('SLD', $params['sld']);
        $Enom->AddParam('Quantity', $params['regperiod']);
        $Enom->AddParam('ClearItems', 'yes');
        $Enom->AddParam('command', 'AddToCart');
        $Enom->DoTransaction($params);
        $Enom->NewRequest();
        $Enom->AddParam('uid', $params['Username']);
        $Enom->AddParam('pw', $params['Password']);
        $Enom->AddParam('command', 'InsertNewOrder');
        $Enom->DoTransaction($params);
    }
    return $values;
    break;
}
function enom_TransferDomain($params)
{
    $params = injectDomainObjectIfNecessary($params);
    if( $params['companyname'] )
    {
        $jobtitle = 'Director';
    }
    $Enom = new CEnomInterface();
    $Enom->NewRequest();
    $Enom->AddParam('uid', $params['Username']);
    $Enom->AddParam('pw', $params['Password']);
    $Enom->AddParam('DomainCount', '1');
    $Enom->AddParam('OrderType', 'Autoverification');
    $Enom->AddParam('TLD1', $params['tld']);
    $Enom->AddParam('SLD1', $params['sld']);
    $Enom->AddParam('AuthInfo1', $params['transfersecret']);
    $Enom->AddParam('UseContacts', '1');
    $Enom->AddParam('Lock', '1');
    $Enom->AddParam('EmailNotify', '1');
    $params = enom_NormalizeContactDetails($params);
    if( in_array($params['domainObj']->getLastTLDSegment(), array( 'eu', 'ca' )) )
    {
        $Enom->AddParam('RegistrantFirstName', $params['firstname']);
        $Enom->AddParam('RegistrantLastName', $params['lastname']);
        $Enom->AddParam('RegistrantOrganizationName', $params['companyname']);
        $Enom->AddParam('RegistrantJobTitle', $jobtitle);
        $Enom->AddParam('RegistrantAddress1', $params['address1']);
        $Enom->AddParam('RegistrantAddress2', $params['address2']);
        $Enom->AddParam('RegistrantCity', $params['city']);
        $Enom->AddParam('RegistrantStateProvince', $params['state']);
        $Enom->AddParam('RegistrantPostalCode', $params['postcode']);
        $Enom->AddParam('RegistrantCountry', $params['country']);
        $Enom->AddParam('RegistrantEmailAddress', $params['email']);
        $Enom->AddParam('RegistrantPhone', $params['fullphonenumber']);
        $Enom->AddParam('eu_whoispolicy', "I AGREE");
        $Enom->AddParam('eu_agreedelete', 'YES');
        $Enom->AddParam('eu_adr_lang', 'EN');
    }
    else
    {
        if( $params['domainObj']->getLastTLDSegment() == 'it' )
        {
            $Enom->AddParam('it_agreedelete', 'YES');
        }
        else
        {
            if( $params['domainObj']->getLastTLDSegment() == 'de' )
            {
                $Enom->AddParam('confirmaddress', 'DE');
                $Enom->AddParam('de_agreedelete', 'YES');
            }
            else
            {
                if( $params['domainObj']->getLastTLDSegment() == 'nl' )
                {
                    $Enom->AddParam('nl_agreedelete', 'YES');
                }
                else
                {
                    if( $params['domainObj']->getLastTLDSegment() == 'fm' )
                    {
                        $Enom->AddParam('fm_agreedelete', 'YES');
                    }
                }
            }
        }
    }
    $Enom->AddParam('command', 'TP_CreateOrder');
    $Enom->DoTransaction($params);
    $values['error'] = $Enom->Values['Err1'];
    return $values;
}
function enom_RenewDomain($params)
{
    $params = injectDomainObjectIfNecessary($params);
    $Enom = new CEnomInterface();
    $Enom->NewRequest();
    $Enom->AddParam('uid', $params['Username']);
    $Enom->AddParam('pw', $params['Password']);
    $Enom->AddParam('tld', $params['tld']);
    $Enom->AddParam('sld', $params['sld']);
    $Enom->AddParam('command', 'getcontacts');
    $Enom->DoTransaction($params);
    $nNumErrors = $Enom->Values['ErrCount'];
    if( 0 < $nNumErrors )
    {
        $errormessage = "An Error Occurred";
    }
    else
    {
        $RegistrantOrganizationName = $Enom->Values['RegistrantOrganizationName'];
        $RegistrantFirstName = $Enom->Values['RegistrantFirstName'];
        $RegistrantLastName = $Enom->Values['RegistrantLastName'];
        $RegistrantAddress1 = $Enom->Values['RegistrantAddress1'];
        $RegistrantCity = $Enom->Values['RegistrantCity'];
        $RegistrantEmailAddress = $Enom->Values['RegistrantEmailAddress'];
        $RegistrantPostalCode = $Enom->Values['RegistrantPostalCode'];
    }
    $Enom->NewRequest();
    $Enom->AddParam('uid', $params['Username']);
    $Enom->AddParam('pw', $params['Password']);
    $Enom->AddParam('sld', $params['sld']);
    $Enom->AddParam('tld', $params['tld']);
    $Enom->AddParam('command', 'GetDomainInfo');
    $Enom->DoTransaction($params);
    if( 0 < $Enom->Values['ErrCount'] )
    {
        $errormessage = "An Error Occurred When Getting The Domain Status";
    }
    else
    {
        $RegistrationStatus = $Enom->Values['registrationstatus'];
    }
    if( stripos($RegistrationStatus, 'Registered') !== false )
    {
        $Enom->NewRequest();
        $Enom->AddParam('uid', $params['Username']);
        $Enom->AddParam('pw', $params['Password']);
        $Enom->AddParam('tld', $params['tld']);
        $Enom->AddParam('sld', $params['sld']);
        $Enom->AddParam('NumYears', $params['regperiod']);
        $Enom->AddParam('OverrideOrder', 0);
        $Enom->AddParam('RegistrantEmailAddress', $RegistrantEmailAddress);
        $Enom->AddParam('RegistrantCity', $RegistrantCity);
        $Enom->AddParam('RegistrantAddress1', $RegistrantAddress1);
        $Enom->AddParam('RegistrantLastName', $RegistrantLastName);
        $Enom->AddParam('RegistrantFirstName', $RegistrantFirstName);
        $Enom->AddParam('RegistrantOrganizationName', $RegistrantOrganizationName);
        $Enom->AddParam('RegistrantPostalCode', $RegistrantPostalCode);
        $Enom->AddParam('command', 'extend');
        $Enom->DoTransaction($params);
    }
    else
    {
        $Enom->NewRequest();
        $Enom->AddParam('uid', $params['Username']);
        $Enom->AddParam('pw', $params['Password']);
        $Enom->AddParam('DomainName', $params['sld'] . "." . $params['tld']);
        $Enom->AddParam('NumYears', $params['regperiod']);
        $Enom->AddParam('command', 'UpdateExpiredDomains');
        $Enom->DoTransaction($params);
    }
    $values['error'] = $Enom->Values['Err1'];
    if( $params['idprotection'] && !$values['error'] )
    {
        $Enom->NewRequest();
        $Enom->AddParam('uid', $params['Username']);
        $Enom->AddParam('pw', $params['Password']);
        $Enom->AddParam('ProductType', 'IDProtectRenewal');
        $Enom->AddParam('TLD', $params['tld']);
        $Enom->AddParam('SLD', $params['sld']);
        $Enom->AddParam('Quantity', $params['regperiod']);
        $Enom->AddParam('ClearItems', 'yes');
        $Enom->AddParam('command', 'AddToCart');
        $Enom->DoTransaction($params);
        $Enom->NewRequest();
        $Enom->AddParam('uid', $params['Username']);
        $Enom->AddParam('pw', $params['Password']);
        $Enom->AddParam('command', 'InsertNewOrder');
        $Enom->DoTransaction($params);
    }
    return $values;
}
function enom_GetContactDetails($params)
{
    $params = injectDomainObjectIfNecessary($params);
    $Enom = new CEnomInterface();
    $Enom->NewRequest();
    $Enom->AddParam('uid', $params['Username']);
    $Enom->AddParam('pw', $params['Password']);
    $Enom->AddParam('tld', $params['tld']);
    $Enom->AddParam('sld', $params['sld']);
    $Enom->AddParam('command', 'getcontacts');
    $Enom->DoTransaction($params);
    $contacttypes = array( 'Registrant', 'Admin', 'Tech' );
    for( $i = 0; $i <= 2; $i++ )
    {
        if( ($Enom->Values['RegistrantUpdatable'] == 'False' || $Enom->Values['RegistrantUpdatable'] === False) && $contacttypes[$i] == 'Registrant' )
        {
            continue;
        }
        $values[$contacttypes[$i]]["First Name"] = $Enom->Values[$contacttypes[$i] . 'FirstName'];
        $values[$contacttypes[$i]]["Last Name"] = $Enom->Values[$contacttypes[$i] . 'LastName'];
        $values[$contacttypes[$i]]["Organisation Name"] = $Enom->Values[$contacttypes[$i] . 'OrganizationName'];
        $values[$contacttypes[$i]]["Job Title"] = $Enom->Values[$contacttypes[$i] . 'JobTitle'];
        $values[$contacttypes[$i]]['Email'] = $Enom->Values[$contacttypes[$i] . 'EmailAddress'];
        $values[$contacttypes[$i]]["Address 1"] = $Enom->Values[$contacttypes[$i] . 'Address1'];
        $values[$contacttypes[$i]]["Address 2"] = $Enom->Values[$contacttypes[$i] . 'Address2'];
        $values[$contacttypes[$i]]['City'] = $Enom->Values[$contacttypes[$i] . 'City'];
        $values[$contacttypes[$i]]['State'] = $Enom->Values[$contacttypes[$i] . 'StateProvince'];
        $values[$contacttypes[$i]]['Postcode'] = $Enom->Values[$contacttypes[$i] . 'PostalCode'];
        $values[$contacttypes[$i]]['Country'] = $Enom->Values[$contacttypes[$i] . 'Country'];
        $values[$contacttypes[$i]]['Phone'] = $Enom->Values[$contacttypes[$i] . 'Phone'];
        $values[$contacttypes[$i]]['Fax'] = $Enom->Values[$contacttypes[$i] . 'Fax'];
    }
    return $values;
}
/**
 * Obtain the registrant contact email address and return it to be used for the
 * domain reminders.
 *
 * @param array $params
 *
 * @return array
 */
function enom_GetRegistrantContactEmailAddress($params)
{
    $params = injectDomainObjectIfNecessary($params);
    $eNom = new CEnomInterface();
    $eNom->NewRequest();
    $eNom->AddParam('uid', $params['Username']);
    $eNom->AddParam('pw', $params['Password']);
    $eNom->AddParam('tld', $params['tld']);
    $eNom->AddParam('sld', $params['sld']);
    $eNom->AddParam('command', 'GetContacts');
    $eNom->DoTransaction($params);
    $values = array(  );
    $values['registrantEmail'] = $eNom->Values['RegistrantEmailAddress'];
    if( 0 < $eNom->Values['ErrCount'] )
    {
        for( $i = 1; $i <= $eNom->Values['ErrCount']; $i++ )
        {
            $values['error'] .= $eNom->Values['Err' . $i] . ". ";
        }
    }
    return $values;
}
function enom_SaveContactDetails($params)
{
    $params = injectDomainObjectIfNecessary($params);
    require(ROOTDIR . "/includes/countriescallingcodes.php");
    $Enom = new CEnomInterface();
    $Enom->NewRequest();
    $Enom->AddParam('uid', $params['Username']);
    $Enom->AddParam('pw', $params['Password']);
    $Enom->AddParam('tld', $params['tld']);
    $Enom->AddParam('sld', $params['sld']);
    $params = enom_NormalizeContactDetails($params);
    $contacttypes = array( 'Registrant', 'Admin', 'Tech' );
    for( $i = 0; $i <= 2; $i++ )
    {
        $phonenumber = $params['contactdetails'][$contacttypes[$i]]['Phone'];
        $country = $params['contactdetails'][$contacttypes[$i]]['Country'];
        $phoneprefix = $countrycallingcodes[$country];
        if( substr($phonenumber, 0, 1) != "+" && $phoneprefix )
        {
            $params['contactdetails'][$contacttypes[$i]]['Phone'] = "+" . $phoneprefix . "." . $phonenumber;
        }
        $Enom->AddParam($contacttypes[$i] . 'Fax', $params['contactdetails'][$contacttypes[$i]]['Fax']);
        $Enom->AddParam($contacttypes[$i] . 'Phone', $params['contactdetails'][$contacttypes[$i]]['Phone']);
        $Enom->AddParam($contacttypes[$i] . 'Country', $params['contactdetails'][$contacttypes[$i]]['Country']);
        $Enom->AddParam($contacttypes[$i] . 'PostalCode', $params['contactdetails'][$contacttypes[$i]]['Postcode']);
        $Enom->AddParam($contacttypes[$i] . 'StateProvince', $params['contactdetails'][$contacttypes[$i]]['State']);
        if( $params['contactdetails'][$contacttypes[$i]]['Country'] == 'US' )
        {
            $Enom->AddParam($contacttypes[$i] . 'StateProvinceChoice', 'S');
        }
        else
        {
            $Enom->AddParam($contacttypes[$i] . 'StateProvinceChoice', 'P');
        }
        $Enom->AddParam($contacttypes[$i] . 'City', $params['contactdetails'][$contacttypes[$i]]['City']);
        $Enom->AddParam($contacttypes[$i] . 'EmailAddress', $params['contactdetails'][$contacttypes[$i]]['Email']);
        $Enom->AddParam($contacttypes[$i] . 'Address2', $params['contactdetails'][$contacttypes[$i]]["Address 2"]);
        $Enom->AddParam($contacttypes[$i] . 'Address1', $params['contactdetails'][$contacttypes[$i]]["Address 1"]);
        $Enom->AddParam($contacttypes[$i] . 'JobTitle', $params['contactdetails'][$contacttypes[$i]]["Job Title"]);
        $Enom->AddParam($contacttypes[$i] . 'LastName', $params['contactdetails'][$contacttypes[$i]]["Last Name"]);
        $Enom->AddParam($contacttypes[$i] . 'FirstName', $params['contactdetails'][$contacttypes[$i]]["First Name"]);
        $Enom->AddParam($contacttypes[$i] . 'OrganizationName', $params['contactdetails'][$contacttypes[$i]]["Organisation Name"]);
    }
    if( $params['domainObj']->getLastTLDSegment() == 'us' )
    {
        $nexus = $params['additionalfields']["Nexus Category"];
        $countrycode = $params['additionalfields']["Nexus Country"];
        $purpose = $params['additionalfields']["Application Purpose"];
        if( $purpose == "Business use for profit" )
        {
            $purpose = 'P1';
        }
        else
        {
            if( $purpose == "Non-profit business" )
            {
                $purpose = 'P2';
            }
            else
            {
                if( $purpose == 'Club' )
                {
                    $purpose = 'P2';
                }
                else
                {
                    if( $purpose == 'Association' )
                    {
                        $purpose = 'P2';
                    }
                    else
                    {
                        if( $purpose == "Religious Organization" )
                        {
                            $purpose = 'P2';
                        }
                        else
                        {
                            if( $purpose == "Personal Use" )
                            {
                                $purpose = 'P3';
                            }
                            else
                            {
                                if( $purpose == "Educational purposes" )
                                {
                                    $purpose = 'P4';
                                }
                                else
                                {
                                    if( $purpose == "Government purposes" )
                                    {
                                        $purpose = 'P5';
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        switch( $nexus )
        {
            case 'C11':
            case 'C12':
                break;
            case 'C21':
                $Enom->AddParam('us_nexus', $nexus);
                break;
            case 'C31':
                break;
            case 'C32':
                $Enom->AddParam('us_nexus', $nexus);
                $Enom->AddParam('global_cc_us', $countrycode);
        }
        $Enom->AddParam('us_purpose', $purpose);
    }
    else
    {
        if( $params['domainObj']->getLastTLDSegment() == 'uk' )
        {
            if( $params['additionalfields']["Legal Type"] == "UK Limited Company" )
            {
                $uklegaltype = 'LTD';
            }
            else
            {
                if( $params['additionalfields']["Legal Type"] == "UK Public Limited Company" )
                {
                    $uklegaltype = 'PLC';
                }
                else
                {
                    if( $params['additionalfields']["Legal Type"] == "UK Partnership" )
                    {
                        $uklegaltype = 'PTNR';
                    }
                    else
                    {
                        if( $params['additionalfields']["Legal Type"] == "UK Limited Liability Partnership" )
                        {
                            $uklegaltype = 'LLP';
                        }
                        else
                        {
                            if( $params['additionalfields']["Legal Type"] == "Sole Trader" )
                            {
                                $uklegaltype = 'STRA';
                            }
                            else
                            {
                                if( $params['additionalfields']["Legal Type"] == "UK Registered Charity" )
                                {
                                    $uklegaltype = 'RCHAR';
                                }
                                else
                                {
                                    if( $params['additionalfields']["Legal Type"] == "UK Industrial/Provident Registered Company" )
                                    {
                                        $uklegaltype = 'IP';
                                    }
                                    else
                                    {
                                        if( $params['additionalfields']["Legal Type"] == "UK School" )
                                        {
                                            $uklegaltype = 'SCH';
                                        }
                                        else
                                        {
                                            if( $params['additionalfields']["Legal Type"] == "UK Government Body" )
                                            {
                                                $uklegaltype = 'GOV';
                                            }
                                            else
                                            {
                                                if( $params['additionalfields']["Legal Type"] == "UK Corporation by Royal Charter" )
                                                {
                                                    $uklegaltype = 'CRC';
                                                }
                                                else
                                                {
                                                    if( $params['additionalfields']["Legal Type"] == "UK Statutory Body" )
                                                    {
                                                        $uklegaltype = 'STAT';
                                                    }
                                                    else
                                                    {
                                                        if( $params['additionalfields']["Legal Type"] == "Non-UK Individual" )
                                                        {
                                                            $uklegaltype = 'FIND';
                                                        }
                                                        else
                                                        {
                                                            if( $params['additionalfields']["Legal Type"] == "Foreign Organization" )
                                                            {
                                                                $uklegaltype = 'CORP';
                                                            }
                                                            else
                                                            {
                                                                if( $params['additionalfields']["Legal Type"] == "Other foreign organizations" )
                                                                {
                                                                    $uklegaltype = 'FOTHER';
                                                                }
                                                                else
                                                                {
                                                                    $uklegaltype = 'IND';
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $Enom->AddParam('uk_legal_type', $uklegaltype);
            $Enom->AddParam('uk_reg_co_no', $params['additionalfields']["Company ID Number"]);
            $Enom->AddParam('registered_for', $params['additionalfields']["Registrant Name"]);
        }
        else
        {
            if( $params['domainObj']->getLastTLDSegment() == 'ca' )
            {
                if( $params['additionalfields']["Legal Type"] == 'Corporation' )
                {
                    $legaltype = 'CCO';
                }
                else
                {
                    if( $params['additionalfields']["Legal Type"] == "Canadian Citizen" )
                    {
                        $legaltype = 'CCT';
                    }
                    else
                    {
                        if( $params['additionalfields']["Legal Type"] == "Permanent Resident of Canada" )
                        {
                            $legaltype = 'RES';
                        }
                        else
                        {
                            if( $params['additionalfields']["Legal Type"] == 'Government' )
                            {
                                $legaltype = 'GOV';
                            }
                            else
                            {
                                if( $params['additionalfields']["Legal Type"] == "Canadian Educational Institution" )
                                {
                                    $legaltype = 'EDU';
                                }
                                else
                                {
                                    if( $params['additionalfields']["Legal Type"] == "Canadian Unincorporated Association" )
                                    {
                                        $legaltype = 'ASS';
                                    }
                                    else
                                    {
                                        if( $params['additionalfields']["Legal Type"] == "Canadian Hospital" )
                                        {
                                            $legaltype = 'HOP';
                                        }
                                        else
                                        {
                                            if( $params['additionalfields']["Legal Type"] == "Partnership Registered in Canada" )
                                            {
                                                $legaltype = 'PRT';
                                            }
                                            else
                                            {
                                                if( $params['additionalfields']["Legal Type"] == "Trade-mark registered in Canada" )
                                                {
                                                    $legaltype = 'TDM';
                                                }
                                                else
                                                {
                                                    $legaltype = 'CCO';
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $whoisoptout = 'FULL';
                if( $params['additionalfields']["WHOIS Opt-out"] && ($legaltype == 'CCT' || $legaltype == 'RES') )
                {
                    $whoisoptout = 'PRIVATE';
                }
                $Enom->AddParam('cira_legal_type', $legaltype);
                $Enom->AddParam('cira_whois_display', $whoisoptout);
                $Enom->AddParam('cira_language', 'en');
                $Enom->AddParam('cira_agreement_version', "2.0");
                $Enom->AddParam('cira_agreement_value', 'Y');
            }
            else
            {
                if( $params['domainObj']->getLastTLDSegment() == 'eu' )
                {
                    $Enom->AddParam('eu_whoispolicy', "I AGREE");
                    $Enom->AddParam('eu_agreedelete', 'YES');
                }
                else
                {
                    if( $params['domainObj']->getLastTLDSegment() == 'it' )
                    {
                        $Enom->AddParam('it_agreedelete', 'YES');
                    }
                    else
                    {
                        if( $params['domainObj']->getLastTLDSegment() == 'de' )
                        {
                            $Enom->AddParam('confirmaddress', 'DE');
                            $Enom->AddParam('de_agreedelete', 'YES');
                        }
                        else
                        {
                            if( $params['domainObj']->getLastTLDSegment() == 'tel' )
                            {
                                $telregoptout = 'NO';
                                if( $params['additionalfields']["Registrant Type"] == "Legal Person" )
                                {
                                    $regtype = 'legal_person';
                                }
                                else
                                {
                                    $regtype = 'natural_person';
                                    if( $params['additionalfields']["WHOIS Opt-out"] )
                                    {
                                        $telregoptout = 'YES';
                                    }
                                }
                                $telpw = '';
                                $length = 10;
                                $seeds = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVYWXYZ';
                                $seeds_count = strlen($seeds) - 1;
                                for( $i = 0; $i < $length; $i++ )
                                {
                                    $telpw .= $seeds[rand(0, $seeds_count)];
                                }
                                $Enom->AddParam('tel_whoistype', $regtype);
                                $Enom->AddParam('tel_publishwhois', $telregoptout);
                                $Enom->AddParam('tel_username', strtolower($params['contactdetails']['Registrant']["First Name"] . $params['contactdetails']['Registrant']["Last Name"] . $params['domainid']));
                                $Enom->AddParam('tel_password', $telpw);
                                $Enom->AddParam('tel_emailaddress', $params['contactdetails']['Registrant']['Email']);
                            }
                            else
                            {
                                if( $params['domainObj']->getLastTLDSegment() == 'pro' )
                                {
                                    $Enom->AddParam('pro_profession', $params['additionalfields']['Profession']);
                                }
                                else
                                {
                                    if( $params['domainObj']->getLastTLDSegment() == 'es' )
                                    {
                                        $params['additionalfields']["ID Form Type"] = explode("|", $params['additionalfields']["ID Form Type"]);
                                        $idtype = $params['additionalfields']["ID Form Type"][0];
                                        $Enom->AddParam('es_registrantidtype', $idtype);
                                        $Enom->AddParam('es_registrantid', $params['additionalfields']["ID Form Number"]);
                                    }
                                    else
                                    {
                                        if( $params['domainObj']->getLastTLDSegment() == 'sg' )
                                        {
                                            $idnumber = $params['additionalfields']["RCB Singapore ID"];
                                            $Enom->AddParam('sg_rcbid', $idnumber);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    $Enom->AddParam('command', 'contacts');
    $Enom->DoTransaction($params);
    $errorMsgs = array(  );
    $errorCount = $Enom->Values['ErrCount'];
    for( $i = 1; $i <= $errorCount; $i++ )
    {
        $errorMsgs[] = trim($Enom->Values['Err' . $i]);
    }
    $values['error'] = implode(", ", $errorMsgs);
    return $values;
    break;
}
function enom_GetEPPCode($params)
{
    $params = injectDomainObjectIfNecessary($params);
    $Enom = new CEnomInterface();
    $Enom->NewRequest();
    $Enom->AddParam('uid', $params['Username']);
    $Enom->AddParam('pw', $params['Password']);
    $Enom->AddParam('tld', $params['tld']);
    $Enom->AddParam('sld', $params['sld']);
    $Enom->AddParam('command', 'SynchAuthInfo');
    $Enom->AddParam('EmailEPP', 'True');
    $Enom->AddParam('RunSynchAutoInfo', 'True');
    $Enom->DoTransaction($params);
    $values['error'] = $Enom->Values['Err1'];
    return $values;
}
function enom_RegisterNameserver($params)
{
    $params = injectDomainObjectIfNecessary($params);
    $Enom = new CEnomInterface();
    $Enom->NewRequest();
    $Enom->AddParam('command', 'RegisterNameServer');
    $Enom->AddParam('uid', $params['Username']);
    $Enom->AddParam('pw', $params['Password']);
    $Enom->AddParam('Add', 'true');
    $Enom->AddParam('NSName', $params['nameserver']);
    $Enom->AddParam('IP', $params['ipaddress']);
    $Enom->DoTransaction($params);
    if( $Enom->Values['Err1'] )
    {
        $error = $Enom->Values['Err1'];
    }
    if( $Enom->Values['ResponseString1'] )
    {
        $error = $Enom->Values['ResponseString1'];
    }
    $values['error'] = $error;
    return $values;
}
function enom_ModifyNameserver($params)
{
    $params = injectDomainObjectIfNecessary($params);
    $Enom = new CEnomInterface();
    $Enom->NewRequest();
    $Enom->AddParam('command', 'UpdateNameServer');
    $Enom->AddParam('uid', $params['Username']);
    $Enom->AddParam('pw', $params['Password']);
    $Enom->AddParam('Add', 'true');
    $Enom->AddParam('NS', $params['nameserver']);
    $Enom->AddParam('OldIP', $params['currentipaddress']);
    $Enom->AddParam('NewIP', $params['newipaddress']);
    $Enom->DoTransaction($params);
    if( $Enom->Values['Err1'] )
    {
        $error = $Enom->Values['Err1'];
    }
    if( $Enom->Values['ResponseString1'] )
    {
        $error = $Enom->Values['ResponseString1'];
    }
    $values['error'] = $error;
    return $values;
}
function enom_DeleteNameserver($params)
{
    $params = injectDomainObjectIfNecessary($params);
    $Enom = new CEnomInterface();
    $Enom->NewRequest();
    $Enom->AddParam('command', 'DeleteNameServer');
    $Enom->AddParam('uid', $params['Username']);
    $Enom->AddParam('pw', $params['Password']);
    $Enom->AddParam('Add', 'true');
    $Enom->AddParam('NS', $params['nameserver']);
    $Enom->DoTransaction($params);
    if( $Enom->Values['Err1'] )
    {
        $error = $Enom->Values['Err1'];
    }
    if( $Enom->Values['ResponseString1'] )
    {
        $error = $Enom->Values['ResponseString1'];
    }
    $values['error'] = $error;
    return $values;
}
function enom_AdminCustomButtonArray($params)
{
    $buttonarray = array(  );
    if( $params['regtype'] == 'Transfer' )
    {
        $buttonarray["Resend Transfer Approval Email"] = 'resendtransferapproval';
        $buttonarray["Cancel Domain Transfer"] = 'canceldomaintransfer';
    }
    return $buttonarray;
}
function enom_resendtransferapproval($params)
{
    $Enom = new CEnomInterface();
    $Enom->NewRequest();
    $Enom->AddParam('command', 'TP_CancelOrder');
    $Enom->AddParam('uid', $params['Username']);
    $Enom->AddParam('pw', $params['Password']);
    $Enom->AddParam('sld', $params['sld']);
    $Enom->AddParam('tld', $params['tld']);
    $Enom->DoTransaction($params);
    if( $Enom->Values['Err1'] || $Enom->Values['ResponseString1'] )
    {
        $values['error'] = $Enom->Values['Err1'];
    }
    else
    {
        $values['message'] = "Successfully resent the transfer approval email";
    }
    return $values;
}
function enom_getorderid($params)
{
    $Enom = new CEnomInterface();
    $Enom->NewRequest();
    $Enom->AddParam('command', 'StatusDomain');
    $Enom->AddParam('uid', $params['Username']);
    $Enom->AddParam('pw', $params['Password']);
    $Enom->AddParam('sld', $params['sld']);
    $Enom->AddParam('tld', $params['tld']);
    $Enom->AddParam('OrderType', 'Transfer');
    $Enom->DoTransaction($params, true);
    if( $Enom->Values['Err1'] || !$Enom->Values['OrderID'] )
    {
        $errmsg = "Unable to Find Domain Order";
        if( $Enom->Values['Err1'] )
        {
            $errmsg .= " - " . $Enom->Values['Err1'];
        }
        return $errmsg;
    }
    return $Enom->Values['OrderID'];
}
function enom_canceldomaintransfer($params)
{
    $orderid = enom_getorderid($params);
    if( !is_numeric($orderid) )
    {
        $values['error'] = $orderid;
        return $values;
    }
    $Enom = new CEnomInterface();
    $Enom->NewRequest();
    $Enom->AddParam('command', 'TP_CancelOrder');
    $Enom->AddParam('uid', $params['Username']);
    $Enom->AddParam('pw', $params['Password']);
    $Enom->AddParam('TransferOrderID', $orderid);
    $Enom->DoTransaction($params);
    if( $Enom->Values['Err1'] || $Enom->Values['ResponseString1'] )
    {
        $values['error'] = $Enom->Values['Err1'];
    }
    else
    {
        $values['message'] = "Successfully cancelled the domain transfer";
    }
    return $values;
}
function enom_Sync($params)
{
    $params = injectDomainObjectIfNecessary($params);
    $Enom = new CEnomInterface();
    $Enom->NewRequest();
    $Enom->AddParam('command', 'GetDomainExp');
    $Enom->AddParam('uid', $params['Username']);
    $Enom->AddParam('pw', $params['Password']);
    $Enom->AddParam('sld', $params['sld']);
    $Enom->AddParam('tld', $params['tld']);
    $Enom->DoTransaction($params);
    $values = array(  );
    if( $Enom->Values['Err1'] || $Enom->Values['ResponseString1'] )
    {
        $values['error'] = $Enom->Values['Err1'];
    }
    else
    {
        $expirydate = $Enom->Values['ExpirationDate'];
        if( $expirydate )
        {
            $expirydate = explode(" ", $expirydate);
            $expirydate = explode('/', $expirydate[0]);
            $day = $expirydate[1];
            $month = $expirydate[0];
            $year = $expirydate[2];
            $expirydate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
            if( trim($year) )
            {
                $values['status'] = 'Active';
            }
            $values['expirydate'] = $expirydate;
        }
    }
    return $values;
}
function enom_TransferSync($params)
{
    $params = injectDomainObjectIfNecessary($params);
    $cancelledstatusids = array( '2', '4', '6', '7', '8', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '30', '31', '32', '33', '34', '36', '37', '45' );
    $pendingstatusids = array( '0', '1', '3', '9', '10', '11', '12', '13', '14', '28', '29', '35' );
    $values = array(  );
    $Enom = new CEnomInterface();
    $Enom->NewRequest();
    $Enom->AddParam('command', 'TP_GetDetailsByDomain');
    $Enom->AddParam('uid', $params['Username']);
    $Enom->AddParam('pw', $params['Password']);
    $Enom->AddParam('sld', $params['sld']);
    $Enom->AddParam('tld', $params['tld']);
    $Enom->DoTransaction($params);
    if( $Enom->Values['Err1'] || $Enom->Values['ResponseString1'] )
    {
        $values['error'] = $Enom->Values['Err1'];
    }
    else
    {
        $count = $Enom->Values['ordercount'];
        $statusid = $Enom->Values['statusid' . $count];
        $statusdesc = $Enom->Values['statusdesc' . $count];
        if( $statusid == '5' )
        {
            if( $params['idprotection'] )
            {
                $Enom->NewRequest();
                $Enom->AddParam('uid', $params['Username']);
                $Enom->AddParam('pw', $params['Password']);
                $Enom->AddParam('ProductType', 'IDProtect');
                $Enom->AddParam('TLD', $params['tld']);
                $Enom->AddParam('SLD', $params['sld']);
                $Enom->AddParam('Quantity', $params['regperiod']);
                $Enom->AddParam('ClearItems', 'yes');
                $Enom->AddParam('command', 'AddToCart');
                $Enom->DoTransaction($params);
                $Enom->NewRequest();
                $Enom->AddParam('uid', $params['Username']);
                $Enom->AddParam('pw', $params['Password']);
                $Enom->AddParam('command', 'InsertNewOrder');
                $Enom->DoTransaction($params);
            }
            $values['completed'] = true;
        }
        else
        {
            if( in_array($statusid, $cancelledstatusids) )
            {
                $values['failed'] = true;
                $values['reason'] = $statusdesc;
            }
            else
            {
                if( in_array($statusid, $pendingstatusids) )
                {
                    $values['pendingtransfer'] = true;
                    $values['reason'] = $statusdesc;
                }
            }
        }
    }
    return $values;
}
/**
 * Munge contact details for eNom's validation checks
 *
 * With the new ICCAN rules requiring validation of addresses and other contact data,
 * enom has added a number of validation filters on the contact details. Sometimes they
 * make sense, other times we need to normalize the data we are sending to them to ensure
 * they accept it.
 *
 * This function is called each place we get user inputed contact details to send to enom.
 *
 * Currently it filters the Canadian postal codes, which are normally stored as ANA NAN but
 * enom expects to be ANANAN.
 *
 * @param  array $params the full set of parameters we are going to pull from to send to enom
 * @return array $params the same set of parameters, normalized for enom's filtering
 */
function enom_NormalizeContactDetails($params)
{
    if( array_key_exists('country', $params) && $params['country'] == 'NL' )
    {
        $modifyKeys = array( 'fullstate', 'state', 'statecode', 'adminfullstate', 'adminstate' );
        foreach( $modifyKeys as $key )
        {
            $params[$key] = str_replace('-', '', $params[$key]);
        }
    }
    if( array_key_exists($params['country']) && $params['country'] == 'CA' )
    {
        $params['postcode'] = preg_replace("/\\s/", '', $params['postcode']);
    }
    if( array_key_exists($params['admincountry']) && $params['admincountry'] == 'CA' )
    {
        $params['adminpostcode'] = preg_replace("/\\s/", '', $params['adminpostcode']);
    }
    for( $i = 0; $i <= 2; $i++ )
    {
        if( array_key_exists($params['contactdetails'][$contacttypes[$i]]['Country']) )
        {
            $country = $params['contactdetails'][$contacttypes[$i]]['Country'];
            if( $country == 'CA' )
            {
                $params['contactdetails'][$contacttypes[$i]]['Postcode'] = preg_replace("/\\s/", '', $params['contactdetails'][$contacttypes[$i]]['Postcode']);
            }
        }
    }
    return $params;
}