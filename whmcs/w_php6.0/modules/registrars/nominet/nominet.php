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
require(ROOTDIR . "/modules/registrars/nominet/class.Nominet.php");
function nominet_getConfigArray()
{
    $configarray = array( 'Description' => array( 'Type' => 'System', 'Value' => "The Official UK Domain Registry Module" ), 'Username' => array( 'Type' => 'text', 'Size' => '25', 'Description' => '' ), 'Password' => array( 'Type' => 'password', 'Size' => '25', 'Description' => '' ), 'TestMode' => array( 'Type' => 'yesno' ), 'AllowClientTAGChange' => array( 'Type' => 'yesno', 'Description' => "Tick to allow clients to change TAGs on domains" ), 'DeleteOnTransfer' => array( 'Type' => 'yesno', 'Description' => "Tick this box if you want the domain to be deleted entirely on RELEASE" ) );
    return $configarray;
}
function nominet_GetNameservers($params)
{
    $nominet = WHMCS_Nominet::init($params);
    if( $nominet->connectAndLogin() )
    {
        $xml = "  <command>\n            <info>\n              <domain:info\n                xmlns:domain=\"urn:ietf:params:xml:ns:domain-1.0\">\n                <domain:name hosts=\"all\">" . $nominet->getDomain() . "</domain:name>\n                </domain:info>\n            </info>\n            <clTRID>ABC-12345</clTRID>\n         </command>\n       </epp>";
        $success = $nominet->call($xml);
        if( $success )
        {
            if( $nominet->isErrorCode() )
            {
                return array( 'error' => $nominet->getErrorDesc() );
            }
            $x = 1;
            $values = array(  );
            $xmldata = $nominet->getResponseArray();
            foreach( $xmldata['EPP']['RESPONSE']['RESDATA']["DOMAIN:INFDATA"]["DOMAIN:NS"]["DOMAIN:HOSTOBJ"] as $discard => $nsdata )
            {
                $values['ns' . $x] = $nsdata;
                $x++;
            }
            return $values;
        }
        return array( 'error' => $nominet->getLastError() );
    }
    return array( 'error' => $nominet->getLastError() );
}
function nominet_SaveNameservers($params)
{
    $nominet = WHMCS_Nominet::init($params);
    if( $nominet->connectAndLogin() )
    {
        $removeNS = array(  );
        $removeNS = nominet_getnameservers($params);
        if( 0 < count($removeNS) )
        {
            $removeXML = "\n                            <domain:rem>\n                                   <domain:ns>\n                        ";
            foreach( $removeNS as $rm )
            {
                $removeXML .= "<domain:hostObj>" . $rm . "</domain:hostObj>\n                                ";
            }
            $removeXML .= " </domain:ns>\n                                      </domain:rem>\n                        ";
        }
        else
        {
            $removeXML = '';
        }
        $ns = array(  );
        $ns[1] = $params['ns1'];
        $ns[2] = $params['ns2'];
        $xml = "  <command>\n                    <update>\n                    <domain:update\n                    xmlns:domain=\"urn:ietf:params:xml:ns:domain-1.0\"\n                    xsi:schemaLocation=\"urn:ietf:params:xml:ns:domain-1.0\n                    domain-1.0.xsd\">\n                      <domain:name>" . $nominet->getDomain() . "</domain:name>\n                      <domain:add>\n                        <domain:ns>\n                          <domain:hostObj>" . $params['ns1'] . "</domain:hostObj>\n                          <domain:hostObj>" . $params['ns2'] . "</domain:hostObj>\n               ";
        if( $params['ns3'] )
        {
            $ns[3] = $params['ns3'];
            $xml .= "<domain:hostObj>" . $params['ns3'] . "</domain:hostObj>\n                    ";
        }
        if( $params['ns4'] )
        {
            $ns[4] = $params['ns4'];
            $xml .= "<domain:hostObj>" . $params['ns4'] . "</domain:hostObj>\n                    ";
        }
        if( $params['ns5'] )
        {
            $ns[5] = $params['ns5'];
            $xml .= "<domain:hostObj>" . $params['ns5'] . "</domain:hostObj>\n                    ";
        }
        $xml .= "</domain:ns>\n                </domain:add>" . $removeXML . "\n               </domain:update>\n             </update>\n           <clTRID>ABC-12345</clTRID>\n         </command>\n        </epp>";
        nominet_createHost($nominet, $ns);
        $success = $nominet->call($xml);
        if( $success )
        {
            if( $nominet->isErrorCode() )
            {
                return array( 'error' => $nominet->getErrorDesc() );
            }
            $x = 1;
            $values = array(  );
            $xmldata = $nominet->getResponseArray();
            foreach( $xmldata['EPP']['RESPONSE']['RESDATA']["DOMAIN:INFDATA"]["DOMAIN:NS"]["DOMAIN:HOSTOBJ"] as $discard => $nsdata )
            {
                $values['ns' . $x] = $nsdata;
                $x++;
            }
            return $values;
        }
        return array( 'error' => $nominet->getLastError() );
    }
    return array( 'error' => $nominet->getLastError() );
}
function nominet_getLegalTypeID($LegalType)
{
    if( $LegalType == 'Individual' )
    {
        $LegalTypeID = 'IND';
    }
    else
    {
        if( $LegalType == "UK Limited Company" )
        {
            $LegalTypeID = 'LTD';
        }
        else
        {
            if( $LegalType == "UK Public Limited Company" )
            {
                $LegalTypeID = 'PLC';
            }
            else
            {
                if( $LegalType == "UK Partnership" )
                {
                    $LegalTypeID = 'PTNR';
                }
                else
                {
                    if( $LegalType == "Sole Trader" )
                    {
                        $LegalTypeID = 'STRA';
                    }
                    else
                    {
                        if( $LegalType == "UK Limited Liability Partnership" )
                        {
                            $LegalTypeID = 'LLP';
                        }
                        else
                        {
                            if( $LegalType == "UK Industrial/Provident Registered Company" )
                            {
                                $LegalTypeID = 'IP';
                            }
                            else
                            {
                                if( $LegalType == "UK School" )
                                {
                                    $LegalTypeID = 'SCH';
                                }
                                else
                                {
                                    if( $LegalType == "UK Registered Charity" )
                                    {
                                        $LegalTypeID = 'RCHAR';
                                    }
                                    else
                                    {
                                        if( $LegalType == "UK Government Body" )
                                        {
                                            $LegalTypeID = 'GOV';
                                        }
                                        else
                                        {
                                            if( $LegalType == "UK Corporation by Royal Charter" )
                                            {
                                                $LegalTypeID = 'CRC';
                                            }
                                            else
                                            {
                                                if( $LegalType == "UK Statutory Body" )
                                                {
                                                    $LegalTypeID = 'STAT';
                                                }
                                                else
                                                {
                                                    if( $LegalType == "UK Entity (other)" )
                                                    {
                                                        $LegalTypeID = 'OTHER';
                                                    }
                                                    else
                                                    {
                                                        if( $LegalType == "Non-UK Individual (representing self)" )
                                                        {
                                                            $LegalTypeID = 'OTHER';
                                                        }
                                                        else
                                                        {
                                                            if( $LegalType == "Foreign Organization" )
                                                            {
                                                                $LegalTypeID = 'FCORP';
                                                            }
                                                            else
                                                            {
                                                                if( $LegalType == "Other foreign organizations" )
                                                                {
                                                                    $LegalTypeID = 'FOTHER';
                                                                }
                                                                else
                                                                {
                                                                    if( $LegalType == "Non-UK Individual" )
                                                                    {
                                                                        $LegalTypeID = 'FIND';
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
    return $LegalTypeID;
}
function nominet_RegisterDomain($params)
{
    $nominet = WHMCS_Nominet::init($params);
    if( $nominet->connectAndLogin() )
    {
        $RegistrantName = $params['additionalfields']["Registrant Name"];
        if( !$RegistrantName )
        {
            $RegistrantName = $params['additionalfields']["Company Name"];
        }
        if( !trim($RegistrantName) )
        {
            return array( 'error' => "Registrant Name is missing. Please check the contact fields on the domains tab." );
        }
        $LegalType = $params['additionalfields']["Legal Type"];
        $CompanyIDNumber = $params['additionalfields']["Company ID Number"];
        $WhoisOptOut = $params['additionalfields']["WHOIS Opt-out"];
        $LegalTypeID = nominet_getlegaltypeid($LegalType);
        if( !$LegalTypeID )
        {
            return array( 'error' => "Legal Type is missing. Please check field on domains tab" );
        }
        if( $LegalTypeID != 'IND' )
        {
            $WhoisOptOut = '';
        }
        $contactID = nominet_createContact($nominet, $params);
        if( is_array($contactID) )
        {
            return $contactID;
        }
        $ns = array(  );
        $ns[1] = $params['ns1'];
        $ns[2] = $params['ns2'];
        $xml = "\n            <command>\n              <create>\n                <domain:create\n                 xmlns:domain=\"urn:ietf:params:xml:ns:domain-1.0\"\n                 xsi:schemaLocation=\"urn:ietf:params:xml:ns:domain-1.0\n                 domain-1.0.xsd\">\n                   <domain:name>" . $nominet->getDomain() . "</domain:name>\n                   <domain:period unit=\"y\">" . $params['regperiod'] . "</domain:period>\n                     <domain:ns>\n                      <domain:hostObj>" . $ns['1'] . "</domain:hostObj>\n                      <domain:hostObj>" . $ns['2'] . "</domain:hostObj>\n                     ";
        if( $params['ns3'] )
        {
            $ns[3] = $params['ns3'];
            $xml .= "<domain:hostObj>" . $params['ns3'] . "</domain:hostObj>\n                                            ";
        }
        if( $params['ns4'] )
        {
            $ns[4] = $params['ns4'];
            $xml .= "<domain:hostObj>" . $params['ns4'] . "</domain:hostObj>\n                                            ";
        }
        if( $params['ns5'] )
        {
            $ns[5] = $params['ns5'];
            $xml .= "<domain:hostObj>" . $params['ns5'] . "</domain:hostObj>\n                                            ";
        }
        $xml .= " </domain:ns>\n                     <domain:registrant>" . $contactID . "</domain:registrant>\n                     <domain:authInfo>\n                       <domain:pw></domain:pw>\n                     </domain:authInfo>\n                  </domain:create>\n               </create>\n            <clTRID>ABC-12345</clTRID>\n          </command>\n        </epp>\n            ";
        nominet_createHost($nominet, $ns);
        $success = $nominet->call($xml);
        if( $success )
        {
            if( $nominet->isErrorCode() )
            {
                return array( 'error' => $nominet->getErrorDesc() );
            }
        }
        else
        {
            return array( 'error' => $nominet->getLastError() );
        }
    }
    else
    {
        return array( 'error' => $nominet->getLastError() );
    }
}
function nominet_TransferDomain($params)
{
    $nominet = WHMCS_Nominet::init($params);
    if( $nominet->connectAndLogin() )
    {
        $xml = "<command>\n            <info>\n              <domain:info\n                xmlns:domain=\"urn:ietf:params:xml:ns:domain-1.0\">\n                <domain:name hosts=\"all\">" . $nominet->getDomain() . "</domain:name>\n                </domain:info>\n            </info>\n            <clTRID>ABC-12345</clTRID>\n         </command>\n       </epp>";
        $success = $nominet->call($xml);
        if( $success )
        {
            if( $nominet->isErrorCode() )
            {
                return array( 'success' => true );
            }
            return array( 'error' => "Domain already exists at domain registrar" );
        }
        return array( 'error' => $nominet->getLastError() );
    }
    return array( 'error' => $nominet->getLastError() );
}
function nominet_RenewDomain($params)
{
    $nominet = WHMCS_Nominet::init($params);
    if( $nominet->connectAndLogin() )
    {
        $expiry = get_query_val('tbldomains', 'expirydate', array( 'id' => $params['domainid'] ));
        $xml = "  <command>\n                <renew>\n\t\t  <domain:renew\n\t\t  xmlns:domain=\"urn:ietf:params:xml:ns:domain-1.0\"\n\t\t  xsi:schemaLocation=\"urn:ietf:params:xml:ns:domain-1.0\n\t\t  domain-1.0.xsd\">\n                    <domain:name>" . $nominet->getDomain() . "</domain:name>\n                    <domain:curExpDate>" . $expiry . "</domain:curExpDate>\n                    <domain:period unit=\"y\">" . $params['regperiod'] . "</domain:period>\n                  </domain:renew>\n                </renew>\n         <clTRID>ABC-12345</clTRID>\n       </command>\n     </epp>";
        $success = $nominet->call($xml);
        if( $success )
        {
            if( $nominet->isErrorCode() )
            {
                return array( 'error' => $nominet->getErrorDesc() );
            }
            return array(  );
        }
        return array( 'error' => $nominet->getLastError() );
    }
    return array( 'error' => $nominet->getLastError() );
}
function nominet_GetContactDetails($params)
{
    $nominet = WHMCS_Nominet::init($params);
    if( $nominet->connectAndLogin() )
    {
        $xml = "  <command>\n            <info>\n              <domain:info\n                xmlns:domain=\"urn:ietf:params:xml:ns:domain-1.0\">\n                <domain:name hosts=\"all\">" . $nominet->getDomain() . "</domain:name>\n                </domain:info>\n            </info>\n            <clTRID>ABC-12345</clTRID>\n         </command>\n       </epp>";
        $success = $nominet->call($xml);
        if( $success )
        {
            if( $nominet->isErrorCode() )
            {
                return array( 'error' => $nominet->getErrorDesc() );
            }
            $xmldata = $nominet->getResponseArray();
            $contactID = $xmldata['EPP']['RESPONSE']['RESDATA']["DOMAIN:INFDATA"]["DOMAIN:REGISTRANT"];
            $xml = "  <command>\n                        <info>\n                        <contact:info xmlns:contact=\"urn:ietf:params:xml:ns:contact-1.0\"\n                          xsi:schemaLocation=\"urn:ietf:params:xml:ns:contact-1.0\n                          contact-1.0.xsd\">\n                            <contact:id>" . $contactID . "</contact:id>\n                          </contact:info>\n                        </info>\n                      <clTRID>ABC-12345</clTRID>\n                    </command>\n                  </epp>";
            $success = $nominet->call($xml);
            if( $success )
            {
                if( $nominet->isErrorCode() )
                {
                    return array( 'error' => $nominet->getErrorDesc() );
                }
                $xmldata = $nominet->getResponseArray();
                $values = array(  );
                $values['Registrant']["Contact Name"] = $xmldata['EPP']['RESPONSE']['RESDATA']["CONTACT:INFDATA"]["CONTACT:POSTALINFO"]["CONTACT:NAME"];
                $streetData = $xmldata['EPP']['RESPONSE']['RESDATA']["CONTACT:INFDATA"]["CONTACT:POSTALINFO"]["CONTACT:ADDR"]["CONTACT:STREET"];
                if( !is_array($streetData) )
                {
                    $streetData = array( $streetData );
                }
                for( $i = 0; $i <= 2; $i++ )
                {
                    $values['Registrant']["Street " . ($i + 1)] = isset($streetData[$i]) ? $streetData[$i] : '';
                }
                $values['Registrant']['City'] = $xmldata['EPP']['RESPONSE']['RESDATA']["CONTACT:INFDATA"]["CONTACT:POSTALINFO"]["CONTACT:ADDR"]["CONTACT:CITY"];
                $values['Registrant']['County'] = $xmldata['EPP']['RESPONSE']['RESDATA']["CONTACT:INFDATA"]["CONTACT:POSTALINFO"]["CONTACT:ADDR"]["CONTACT:SP"];
                $values['Registrant']['Postcode'] = $xmldata['EPP']['RESPONSE']['RESDATA']["CONTACT:INFDATA"]["CONTACT:POSTALINFO"]["CONTACT:ADDR"]["CONTACT:PC"];
                $values['Registrant']['Country'] = $xmldata['EPP']['RESPONSE']['RESDATA']["CONTACT:INFDATA"]["CONTACT:POSTALINFO"]["CONTACT:ADDR"]["CONTACT:CC"];
                $values['Registrant']["Phone Number"] = $xmldata['EPP']['RESPONSE']['RESDATA']["CONTACT:INFDATA"]["CONTACT:VOICE"];
                $values['Registrant']["Email Address"] = $xmldata['EPP']['RESPONSE']['RESDATA']["CONTACT:INFDATA"]["CONTACT:EMAIL"];
                return $values;
            }
            return array( 'error' => $nominet->getLastError() );
        }
        return array( 'error' => $nominet->getLastError() );
    }
    return array( 'error' => $nominet->getLastError() );
}
function nominet_SaveContactDetails($params)
{
    $nominet = WHMCS_Nominet::init($params);
    if( $nominet->connectAndLogin() )
    {
        $xml = "  <command>\n            <info>\n              <domain:info\n                xmlns:domain=\"urn:ietf:params:xml:ns:domain-1.0\">\n                <domain:name hosts=\"all\">" . $nominet->getDomain() . "</domain:name>\n                </domain:info>\n            </info>\n            <clTRID>ABC-12345</clTRID>\n         </command>\n       </epp>";
        $success = $nominet->call($xml);
        if( $success )
        {
            if( $nominet->isErrorCode() )
            {
                return array( 'error' => $nominet->getErrorDesc() );
            }
            $xmldata = $nominet->getResponseArray();
            $contactID = $xmldata['EPP']['RESPONSE']['RESDATA']["DOMAIN:INFDATA"]["DOMAIN:REGISTRANT"];
            $xml = "  <command>\n                        <update>\n                          <contact:update\n                          xmlns:contact=\"urn:ietf:params:xml:ns:contact-1.0\"\n                          xsi:schemaLocation=\"urn:ietf:params:xml:ns:contact-1.0\n                          contact-1.0.xsd\">\n                          <contact:id>" . $contactID . "</contact:id>\n                            <contact:chg>\n                              <contact:postalInfo type=\"loc\">\n                              <contact:name>" . $params['contactdetails']['Registrant']["Contact Name"] . "</contact:name>\n                              <contact:addr>";
            if( $params['contactdetails']['Registrant']["Street 1"] )
            {
                $xml .= "\n                                <contact:street>" . $params['contactdetails']['Registrant']["Street 1"] . "</contact:street>";
            }
            if( $params['contactdetails']['Registrant']["Street 2"] )
            {
                $xml .= "\n                                <contact:street>" . $params['contactdetails']['Registrant']["Street 2"] . "</contact:street>";
            }
            if( $params['contactdetails']['Registrant']["Street 3"] )
            {
                $xml .= "\n                                <contact:street>" . $params['contactdetails']['Registrant']["Street 3"] . "</contact:street>";
            }
            $xml .= "\n                                <contact:city>" . $params['contactdetails']['Registrant']['City'] . "</contact:city>\n                                <contact:sp>" . $params['contactdetails']['Registrant']['County'] . "</contact:sp>\n                                <contact:pc>" . strtoupper($params['contactdetails']['Registrant']['Postcode']) . "</contact:pc>\n                                <contact:cc>" . $params['contactdetails']['Registrant']['Country'] . "</contact:cc>\n                               </contact:addr>\n                              </contact:postalInfo>\n                            <contact:voice>" . $params['contactdetails']['Registrant']["Phone Number"] . "</contact:voice>\n                            <contact:email>" . $params['contactdetails']['Registrant']["Email Address"] . "</contact:email>\n                            </contact:chg>\n                          </contact:update>\n                         </update>\n                         <clTRID>ABC-12345</clTRID>\n                       </command>\n                     </epp>";
            $success = $nominet->call($xml);
            if( $success )
            {
                if( $nominet->isErrorCode() )
                {
                    return array( 'error' => $nominet->getErrorDesc() );
                }
                return array(  );
            }
        }
        else
        {
            return array( 'error' => $nominet->getLastError() );
        }
    }
    else
    {
        return array( 'error' => $nominet->getLastError() );
    }
}
function nominet_ReleaseDomain($params)
{
    $nominet = WHMCS_Nominet::init($params);
    if( $nominet->connectAndLogin() )
    {
        $transfertag = $params['transfertag'];
        $xml = "  <command>\n\t        <update>\n\t\t<r:release\n\t\txmlns:r=\"http://www.nominet.org.uk/epp/xml/std-release-1.0\"\n\t\txsi:schemaLocation=\"http://www.nominet.org.uk/epp/xml/std-release-1.0\n\t\tstd-release-1.0.xsd\">\n\t\t<r:domainName>" . $nominet->getDomain() . "</r:domainName>\n\t\t<r:registrarTag>" . $transfertag . "</r:registrarTag>\n\t\t</r:release>\n\t\t</update>\n               <clTRID>ABC-12345</clTRID>\n              </command>\n            </epp>";
        $success = $nominet->call($xml);
        if( $success )
        {
            if( $nominet->isErrorCode() )
            {
                return array( 'error' => $nominet->getErrorDesc() );
            }
            if( $nominet->getResultCode() == 1000 )
            {
                if( $params['DeleteOnTransfer'] )
                {
                    delete_query('tbldomains', array( 'id' => $params['domainid'] ));
                }
                else
                {
                    update_query('tbldomains', array( 'status' => 'Cancelled' ), array( 'id' => $params['domainid'] ));
                }
            }
        }
        else
        {
            return array( 'error' => $nominet->getLastError() );
        }
    }
    else
    {
        return array( 'error' => $nominet->getLastError() );
    }
}
/**
 * Update the status of domains being transfers w/the registrar.
 * Since the old nominetsync.php used the same code for pending domains and active domains,
 * this function restores that functionality.
 *
 * @param array $params from query in domainssync.php
 * @return array ret object which is parsed by domainssync.php
 */
function nominet_TransferSync($params)
{
    return nominet_Sync($params, 'Transfer');
}
/**
 * Sync the expiry date of the domain with the Nominet API.
 *
 * On a standard sync, a domain not on the tag can be cancelled and possibly
 * deleted. We do not want to do this on a Transfer sync.
 *
 * @param array $params
 * @param string $type The kind of Sync: Transfer or Active.
 *
 * @return array
 */
function nominet_Sync($params, $type = 'Active')
{
    $nominet = WHMCS_Nominet::init($params);
    if( $nominet->connectAndLogin() )
    {
        $xml = "  <command>\n                <info>\n\t\t<domain:info\n\t\txmlns:domain=\"urn:ietf:params:xml:ns:domain-1.0\">\n                  <domain:name hosts = \"all\">" . $nominet->getDomain() . "</domain:name>\n                </domain:info>\n                </info>\n                <clTRID>ABC-12345</clTRID>\n              </command>\n            </epp>";
        $success = $nominet->call($xml);
        if( $success )
        {
            if( $nominet->getResultCode() == 2201 && $type == 'Active' )
            {
                $return = array(  );
                if( $params['DeleteOnTransfer'] )
                {
                    delete_query('tbldomains', array( 'id' => $params['domainid'] ));
                    $return['error'] = "Domain Deleted per Nominet Module Configuration";
                }
                else
                {
                    $return['cancelled'] = true;
                }
                return $return;
            }
            if( $nominet->isErrorCode() )
            {
                return array( 'error' => $nominet->getErrorDesc() );
            }
            $xmldata = $nominet->getResponseArray();
            $expirydate = trim($xmldata['EPP']['RESPONSE']['RESDATA']["DOMAIN:INFDATA"]["DOMAIN:EXDATE"]);
            $expirydate = substr($expirydate, 0, 10);
            if( $expirydate )
            {
                $rtn = array(  );
                $rtn['expirydate'] = $expirydate;
                if( date('Ymd') <= str_replace('-', '', $expirydate) )
                {
                    $rtn['active'] = true;
                }
                else
                {
                    $rtn['expired'] = true;
                }
                return $rtn;
            }
        }
        else
        {
            return array( 'error' => $nominet->getLastError() );
        }
    }
    else
    {
        return array( 'error' => $nominet->getLastError() );
    }
}
function nominet_createContact($nominet, $params)
{
    $RegistrantName = $params['additionalfields']["Registrant Name"];
    $LegalType = $params['additionalfields']["Legal Type"];
    $CompanyIDNumber = $params['additionalfields']["Company ID Number"];
    $WhoisOptOut = $params['additionalfields']["WHOIS Opt-out"];
    $TradingName = $params['additionalfields']["Trading Name"];
    $LegalTypeID = nominet_getlegaltypeid($LegalType);
    if( $LegalTypeID != 'IND' )
    {
        $WhoisOptOut = '';
    }
    $WhoisOptOut = $WhoisOptOut ? 'Y' : 'N';
    if( $LegalTypeID == 'IND' )
    {
        $RegistrantOrgName = '';
    }
    else
    {
        $RegistrantOrgName = $RegistrantName;
        $RegistrantName = $params['firstname'] . " " . $params['lastname'];
    }
    $street = $params['address1'];
    $street2 = trim($params['address2']);
    $street2code = empty($street2) ? '' : "<contact:street>" . $street2 . "</contact:street>";
    $city = $params['city'];
    $county = $params['state'];
    $postcode = $params['postcode'];
    $country = $params['country'];
    $phonenumber = $params['fullphonenumber'];
    $email = $params['email'];
    $contactID = 'WHMCS' . $params['domainid'] . rand(1000, 9999);
    if( $RegistrantOrgName )
    {
        $RegistrantOrgName = "<contact:org>" . $RegistrantOrgName . "</contact:org>";
    }
    $xml = "  <command>\n\t     <create>\n\t     <contact:create\n\t\t     xmlns:contact=\"urn:ietf:params:xml:ns:contact-1.0\"\n\t\t     xsi:schemaLocation=\"urn:ietf:params:xml:ns:contact-1.0\n\t\t     contact-1.0.xsd\">\n\t\t\t     <contact:id>" . $contactID . "</contact:id>\n\t\t\t     <contact:postalInfo type=\"loc\">\n                 <contact:name>" . $RegistrantName . "</contact:name>\n                 " . $RegistrantOrgName . "\n                 <contact:addr>\n\t\t\t\t <contact:street>" . $street . "</contact:street>\n                " . $street2code . "\n\t\t\t\t <contact:city>" . $city . "</contact:city>\n\t\t\t\t <contact:sp>" . $county . "</contact:sp>\n\t\t\t\t <contact:pc>" . $postcode . "</contact:pc>\n\t\t\t\t<contact:cc>" . $country . "</contact:cc>\n\t\t\t\t     </contact:addr>\n\t\t\t     </contact:postalInfo>\n\t\t\t\t     <contact:voice>" . $phonenumber . "</contact:voice>\n\t\t\t\t     <contact:email>" . $email . "</contact:email>\n\t\t\t\t     <contact:authInfo>\n\t\t\t\t <contact:pw>" . substr(sha1(time()), 0, 15) . "</contact:pw>\n\t\t\t\t </contact:authInfo>\n\t\t\t     </contact:create>\n\t\t\t   </create>\n<extension>\n<contact-ext:create\nxmlns:contact-ext=\"http://www.nominet.org.uk/epp/xml/contact-nom-ext-1.0\">\n";
    if( $TradingName )
    {
        $xml .= "<contact-ext:trad-name>" . $TradingName . "</contact-ext:trad-name>\n";
    }
    $xml .= "<contact-ext:type>" . $LegalTypeID . "</contact-ext:type>\n";
    if( isset($CompanyIDNumber) && 0 < strlen($CompanyIDNumber) )
    {
        $xml .= "<contact-ext:co-no>" . $CompanyIDNumber . "</contact-ext:co-no>\n";
    }
    $xml .= "<contact-ext:opt-out>" . $WhoisOptOut . "</contact-ext:opt-out>\n</contact-ext:create>\n</extension>\n\t\t\t<clTRID>ABC-12345</clTRID>\n\t\t   </command>\n\t\t </epp>\n\t";
    $success = $nominet->call($xml);
    if( $success )
    {
        if( $nominet->isErrorCode() )
        {
            if( $nominet->getResultCode() == 2302 )
            {
                $params['contactCreateCount']++;
                if( 10 < $params['contactCreateCount'] )
                {
                    return array( 'error' => "Failed to create contact. Please contact support." );
                }
                return nominet_createContact($nominet, $params);
            }
            return array( 'error' => $nominet->getErrorDesc() );
        }
        $xmldata = $nominet->getResponseArray();
        return $xmldata['EPP']['RESPONSE']['RESDATA']["CONTACT:CREDATA"]["CONTACT:ID"];
    }
    return array( 'error' => $nominet->getLastError() );
}
function nominet_createHost($nominet, $ns = array(  ))
{
    foreach( $ns as $server )
    {
        $xml = "  <command>\n\t        <create>\n\t\t  <host:create xmlns:host=\"urn:ietf:params:xml:ns:host-1.0\"\n\t\t  xsi:schemaLocation=\"urn:ietf:params:xml:ns:host-1.0\n\t\t  host-1.0.xsd\">\n\t\t  ";
        $xml .= "<host:name>" . $server . "</host:name>\n\t\t  </host:create>\n\t\t</create>\n              <clTRID>ABC-12345</clTRID>\n\t    </command>\n\t  </epp>\n\t  ";
        $result = $nominet->call($xml);
    }
}