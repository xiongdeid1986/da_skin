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
function onlinenic_getConfigArray()
{
    $query = "CREATE TABLE IF NOT EXISTS `mod_onlinenic` (`id` int(10) NOT NULL auto_increment,`domain` VARCHAR(255) NOT NULL,`lockstatus` BOOL NOT NULL DEFAULT '0',PRIMARY KEY  (`id`),KEY `domainid` (`domain`))";
    $result = full_query($query);
    $configarray = array( 'FriendlyName' => array( 'Type' => 'System', 'Value' => 'OnlineNIC' ), 'Username' => array( 'Type' => 'text', 'Size' => '20', 'Description' => "Onlinenic ID" ), 'Password' => array( 'Type' => 'password', 'Size' => '20', 'Description' => 'Password' ), 'TestMode' => array( 'Type' => 'yesno' ), 'SyncNextDueDate' => array( 'Type' => 'yesno', 'Description', "Tick this box if you want the expiry date sync script to update the expiry and next due dates (cron must be configured)" ) );
    return $configarray;
}
function onlinenic_GetNameservers($params)
{
    $username = $params['Username'];
    $password = md5($params['Password']);
    $testmode = $params['TestMode'];
    $tld = $params['tld'];
    $sld = $params['sld'];
    $domain = $sld . "." . $tld;
    if( $testmode )
    {
        $username = 135610;
        $password = md5('654123');
    }
    $values = onlinenic_Login($fp, $username, $password, $testmode);
    if( $values['error'] )
    {
        return $values;
    }
    $domain_type = onlinenic_getDomainType($tld);
    $clTrid = substr(md5($domain), 0, 10) . mt_rand(1000000000, 9999999999);
    $checksum = md5($username . $password . $clTrid . 'getdomaininfo');
    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n            <epp>\n            <command>\n            <getdomaininfo>\n                <clID>" . $username . "</clID>\n                <domain>" . $domain . "</domain>\n                <domain:type>" . $domain_type . "</domain:type>\n                <options>\n                <version>1.0</version>\n                <lang>en</lang>\n                </options>\n                </getdomaininfo>\n                <clTRID>" . $clTrid . "</clTRID>\n                <chksum>" . $checksum . "</chksum>\n            </command>\n            </epp>";
    $result = onlinenic_sendCommand($fp, $xml);
    if( !$result )
    {
        return array( 'error' => "Domain not found" );
    }
    $resultcode = onlinenic_getResultCode($result);
    onlinenic_Logout($fp, $username, $password);
    if( $resultcode != '1000' )
    {
        $msg = onlinenic_GetValue($result, "<msg>", "</msg>");
        $error = onlinenic_GetValue($result, "<value>", "</value>");
        $error = $msg . " - " . $error;
        $errormsg = onlinenic_getResultText($resultcode);
        $values['error'] = $resultcode . " - " . $errormsg . ": " . $error;
    }
    else
    {
        $nameserver1 = onlinenic_GetValue($result, "<dns1>", "</dns1>");
        $nameserver2 = onlinenic_GetValue($result, "<dns2>", "</dns2>");
        $values['ns1'] = trim($nameserver1);
        $values['ns2'] = trim($nameserver2);
        $values['ns3'] = trim($nameserver3);
        $values['ns4'] = trim($nameserver4);
        $values['ns5'] = trim($nameserver5);
    }
    return $values;
}
function onlinenic_SaveNameservers($params)
{
    $username = $params['Username'];
    $password = md5($params['Password']);
    $testmode = $params['TestMode'];
    $tld = $params['tld'];
    $sld = $params['sld'];
    if( $testmode )
    {
        $username = 135610;
        $password = md5('654123');
    }
    $domain = $sld . "." . $tld;
    $values = onlinenic_Login($fp, $username, $password, $testmode);
    if( $values['error'] )
    {
        return $values;
    }
    $domain_type = onlinenic_getDomainType($tld);
    $dns1 = $params['ns1'];
    $dns2 = $params['ns2'];
    $dns3 = $params['ns3'];
    $dns4 = $params['ns4'];
    $dns5 = $params['ns5'];
    $clTrid = substr(md5($domain), 0, 10) . mt_rand(1000000000, 9999999999);
    $checksum = md5($username . $password . $clTrid . 'upddomain' . $domain_type . $domain . $dns1 . $dns2);
    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n            <epp>\n                    <command>\n                            <update>\n                                    <domain:update>\n                                            <domain:type>" . $domain_type . "</domain:type>\n                                            <domain:name>" . $domain . "</domain:name>\n                                            <domain:rep>\n                                                    <domain:ns1>" . $dns1 . "</domain:ns1>\n                                                    <domain:ns2>" . $dns2 . "</domain:ns2>\n                                                    <domain:ns3>" . $dns3 . "</domain:ns3>\n                                                    <domain:ns4>" . $dns4 . "</domain:ns4>\n                                                    <domain:ns5>" . $dns5 . "</domain:ns5>\n                                            </domain:rep>\n                                    </domain:update>\n                            </update>\n                            <clTRID>" . $clTrid . "</clTRID>\n                            <chksum>" . $checksum . "</chksum>\n                    </command>\n            </epp>";
    $result = onlinenic_sendCommand($fp, $xml);
    $resultcode = onlinenic_getResultCode($result);
    onlinenic_Logout($fp, $username, $password);
    if( $resultcode != '1000' )
    {
        $errormsg = onlinenic_getResultText($resultcode);
        $msg = onlinenic_GetValue($result, "<msg>", "</msg>");
        $error = onlinenic_GetValue($result, "<value>", "</value>");
        $error = $msg . " - " . $error;
        $values['error'] = $resultcode . " - " . $errormsg . ": " . $error;
    }
    return $values;
}
function onlinenic_GetRegistrarLock($params)
{
    $username = $params['Username'];
    $password = $params['Password'];
    $testmode = $params['TestMode'];
    $tld = $params['tld'];
    $sld = $params['sld'];
    $domainname = $sld . "." . $tld;
    if( $testmode )
    {
        $username = 135610;
        $password = md5('654123');
    }
    $queryresult = select_query('mod_onlinenic', 'lockstatus', "domain='" . $domainname . "'");
    $data = mysql_fetch_array($queryresult);
    $lock = (string) $data['lockstatus'];
    if( $lock )
    {
        $lockstatus = 'locked';
    }
    else
    {
        $lockstatus = 'unlocked';
    }
    return $lockstatus;
}
function onlinenic_SaveRegistrarLock($params)
{
    $username = $params['Username'];
    $password = md5($params['Password']);
    $testmode = $params['TestMode'];
    $tld = $params['tld'];
    $sld = $params['sld'];
    if( $params['lockenabled'] == 'locked' )
    {
        $locked = true;
    }
    else
    {
        $locked = false;
    }
    if( $testmode )
    {
        $username = 135610;
        $password = md5('654123');
    }
    $domain = $sld . "." . $tld;
    $values = onlinenic_Login($fp, $username, $password, $testmode);
    if( $values['error'] )
    {
        return $values;
    }
    $domain_type = onlinenic_getDomainType($tld);
    $clTrid = rand();
    $checksum = md5($username . $password . $clTrid . 'upddomain' . $domain_type . $domain);
    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n                    <epp>\n                        <command>\n                            <update>\n                            <domain:update>\n                                <domain:type>" . $domain_type . "</domain:type>\n                                <domain:name>" . $domain . "</domain:name>";
    if( $locked )
    {
        $xml .= "\n                             <domain:add>\n                                    <domain:status s=\"clientTransferProhibited\"/>\n                                </domain:add>";
    }
    else
    {
        $xml .= "\n                             <domain:rem>\n                                    <domain:status s=\"clientTransferProhibited\"/>\n                                </domain:rem>";
    }
    $xml .= "\n                             </domain:update>\n                            </update>\n                            <clTRID>" . $clTrid . "</clTRID>\n                            <chksum>" . $checksum . "</chksum>\n                        </command>\n                    </epp>";
    $result = onlinenic_sendCommand($fp, $xml);
    onlinenic_Logout($fp, $username, $password);
    $resultcode = onlinenic_getResultCode($result);
    if( $resultcode != '1000' )
    {
        $errormsg = onlinenic_getResultText($resultcode);
        $msg = onlinenic_GetValue($result, "<msg>", "</msg>");
        $error = onlinenic_GetValue($result, "<value>", "</value>");
        $error = $msg . " - " . $error;
        $values['error'] = $resultcode . " - " . $errormsg . ": " . $error;
    }
    else
    {
        $queryresult = select_query('mod_onlinenic', "*", "domain='" . $domain . "'");
        $check = mysql_num_rows($queryresult);
        if( $check != '0' )
        {
            $result = update_query('mod_onlinenic', array( 'lockstatus' => $locked ), array( 'domain' => $domain ));
        }
        else
        {
            $result = insert_query('mod_onlinenic', array( 'lockstatus' => $locked, 'domain' => $domain ));
        }
    }
    return $values;
}
function onlinenicX_GetDNS($params)
{
    $username = $params['Username'];
    $password = $params['Password'];
    $testmode = $params['TestMode'];
    $tld = $params['tld'];
    $sld = $params['sld'];
    if( $testmode )
    {
        $username = 135610;
        $password = md5('654123');
    }
    $hostrecords = array(  );
    $hostrecords[] = array( 'hostname' => 'ns1', 'type' => 'A', 'address' => "192.168.0.1" );
    $hostrecords[] = array( 'hostname' => 'ns2', 'type' => 'A', 'address' => "192.168.0.2" );
    return $hostrecords;
}
function onlinenicX_SaveDNS($params)
{
    $username = $params['Username'];
    $password = $params['Password'];
    $testmode = $params['TestMode'];
    $tld = $params['tld'];
    $sld = $params['sld'];
    if( $testmode )
    {
        $username = 135610;
        $password = md5('654123');
    }
    foreach( $params['dnsrecords'] as $key => $values )
    {
        $hostname = $values['hostname'];
        $type = $values['type'];
        $address = $values['address'];
    }
    $values['error'] = $Enom->Values['Err1'];
    return $values;
}
function onlinenic_RegisterDomain($params)
{
    $username = $params['Username'];
    $password = md5($params['Password']);
    $testmode = $params['TestMode'];
    $tld = $params['tld'];
    $sld = $params['sld'];
    $domain = $sld . "." . $tld;
    if( $testmode )
    {
        $username = 135610;
        $password = md5('654123');
    }
    $values = onlinenic_Login($fp, $username, $password, $testmode);
    if( $values['error'] )
    {
        return $values;
    }
    $domain_type = onlinenic_getDomainType($tld);
    $year = $params['regperiod'];
    $dns1 = $params['ns1'];
    $dns2 = $params['ns2'];
    $dns3 = $params['ns3'];
    $dns4 = $params['ns4'];
    $dns5 = $params['ns5'];
    $RegistrantFirstName = $params['firstname'];
    $RegistrantLastName = $params['lastname'];
    $RegistrantCompany = $params['companyname'];
    $RegistrantAddress1 = $params['address1'];
    $RegistrantAddress2 = $params['address2'];
    $RegistrantCity = $params['city'];
    $RegistrantStateProvince = $params['state'];
    $RegistrantPostalCode = $params['postcode'];
    $RegistrantCountry = $params['country'];
    $RegistrantEmailAddress = $params['email'];
    $RegistrantPhone = $params['phonenumber'];
    $values = onlinenic_RegisterContact($fp, $username, $password, $domain_type, $RegistrantFirstName, $RegistrantLastName, $RegistrantCompany, $RegistrantAddress1, $RegistrantAddress2, $RegistrantCity, $RegistrantStateProvince, $RegistrantCountry, $RegistrantPostalCode, $RegistrantPhone, $RegistrantPhone, $RegistrantEmailAddress);
    if( $values['error'] )
    {
        return $values;
    }
    $registrant = $values['contactid'];
    $AdminFirstName = $params['adminfirstname'];
    $AdminLastName = $params['adminlastname'];
    $AdminCompany = $params['companyname'];
    $AdminAddress1 = $params['adminaddress1'];
    $AdminAddress2 = $params['adminaddress2'];
    $AdminCity = $params['admincity'];
    $AdminStateProvince = $params['adminstate'];
    $AdminPostalCode = $params['adminpostcode'];
    $AdminCountry = $params['admincountry'];
    $AdminEmailAddress = $params['adminemail'];
    $AdminPhone = $params['adminphonenumber'];
    $values = onlinenic_RegisterContact($fp, $username, $password, $domain_type, $AdminFirstName, $AdminLastName, $AdminCompany, $AdminAddress1, $AdminAddress2, $AdminCity, $AdminStateProvince, $AdminCountry, $AdminPostalCode, $AdminPhone, $AdminPhone, $AdminEmailAddress);
    if( $values['error'] )
    {
        return $values;
    }
    $admin = $values['contactid'];
    $tech = $admin;
    $billing = $admin;
    $clTrid = substr(md5($domain), 0, 10) . mt_rand(1000000000, 9999999999);
    $password1 = onlinenic_genpw();
    if( $tld == 'eu' || $tld == 'cc' )
    {
        $checksum = md5($username . $password . $clTrid . 'crtdomain' . $domain_type . $domain . $year . $dns1 . $dns2 . $registrant . $password1);
    }
    else
    {
        if( $tld == 'asia' )
        {
            $checksum = md5($username . $password . $clTrid . 'crtdomain' . $domain_type . $domain . $year . $dns1 . $dns2 . $registrant . $admin . $tech . $billing . $password1);
        }
        else
        {
            if( $tld == 'tv' )
            {
                $checksum = md5($username . $password . $clTrid . 'crtdomain' . $domain_type . $domain . $year . $dns1 . $dns2 . $registrant . $tech . $password1);
            }
            else
            {
                $checksum = md5($username . $password . $clTrid . 'crtdomain' . $domain_type . $domain . $year . $dns1 . $dns2 . $registrant . $admin . $tech . $billing . $password1);
            }
        }
    }
    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n            <epp>\n                    <command>\n                            <create>\n                                    <domain:create>\n                                            <domain:type>" . $domain_type . "</domain:type>\n                                            <domain:name>" . $domain . "</domain:name>\n                                            <domain:period>" . $year . "</domain:period>\n                                            <domain:ns1>" . $dns1 . "</domain:ns1>\n                                            <domain:ns2>" . $dns2 . "</domain:ns2>\n                                            <domain:ns3>" . $dns3 . "</domain:ns3>\n                                            <domain:ns4>" . $dns4 . "</domain:ns4>\n                                            <domain:ns5>" . $dns5 . "</domain:ns5>\n                                            <domain:registrant>" . $registrant . "</domain:registrant>\n                                            <domain:contact type=\"admin\">" . $admin . "</domain:contact>\n                                            <domain:contact type=\"tech\">" . $tech . "</domain:contact>\n                                            <domain:contact type=\"billing\">" . $billing . "</domain:contact>\n                                            <domain:authInfo type=\"pw\">" . $password1 . "</domain:authInfo>\n                                    </domain:create>\n                            </create>\n                            <clTRID>" . $clTrid . "</clTRID>\n                            <chksum>" . $checksum . "</chksum>\n                    </command>\n            </epp>";
    $result = onlinenic_sendCommand($fp, $xml);
    $resultcode = onlinenic_getResultCode($result);
    onlinenic_Logout($fp, $username, $password);
    if( $resultcode != '1000' )
    {
        $errormsg = onlinenic_getResultText($resultcode);
        $msg = onlinenic_GetValue($result, "<msg>", "</msg>");
        $error = onlinenic_GetValue($result, "<value>", "</value>");
        $error = $msg . " - " . $error;
        $values['error'] = $resultcode . " - " . $errormsg . ": " . $error;
        return $values;
    }
}
function onlinenic_FormatPhone($telephone, $country)
{
    require(dirname(__FILE__) . "/../../../includes/countriescallingcodes.php");
    $prefix = $countrycallingcodes[$country];
    $telephone = preg_replace("/[^0-9]/", '', $telephone);
    if( $telephone == '' )
    {
        return "+" . $prefix . ".0000000";
    }
    $StartsWith001 = strcmp(substr($telephone, 0, 3), '001') == 0;
    $StartsWith011 = strcmp(substr($telephone, 0, 3), '011') == 0;
    $StartsWithPrefix = strcmp(substr($telephone, 0, strlen($prefix)), $prefix) == 0;
    if( $StartsWith001 || $StartsWith011 )
    {
        $telephone = substr($telephone, 3, strlen($telephone) - 3);
    }
    if( $StartsWithPrefix )
    {
        $telephone = substr($telephone, strlen($prefix), strlen($telephone) - strlen($prefix));
    }
    return "+" . $prefix . "." . $telephone;
}
function onlinenic_RegisterContact($fp, $username, $password, $domain_type, $firstname, $lastname, $companyname, $address1, $address2, $city, $province, $country, $postalcode, $telephone, $fax, $email)
{
    $fullname = $firstname . " " . $lastname;
    if( trim($companyname) == '' )
    {
        $companyname = 'None';
    }
    $telephone = onlinenic_formatphone($telephone, $country);
    $fax = onlinenic_formatphone($fax, $country);
    $password1 = onlinenic_genpw();
    $clTrid = substr(md5($domain), 0, 10) . mt_rand(1000000000, 9999999999);
    $checksum = md5($username . $password . $clTrid . 'crtcontact' . $fullname . $companyname . $email);
    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n<epp>\n        <command>\n                <create>\n                        <contact:create>\n                                <contact:domaintype>" . $domain_type . "</contact:domaintype>\n                                <contact:ascii>\n                                        <contact:name>" . $fullname . "</contact:name>\n                                        <contact:org>" . $companyname . "</contact:org>\n                                        <contact:addr>\n                                                <contact:street1>" . $address1 . "</contact:street1>\n";
    if( $address2 != '' )
    {
        $xml .= "<contact:street2>" . $address2 . "</contact:street2>\n";
    }
    $xml .= "                                                <contact:city>" . $city . "</contact:city>\n                                                <contact:sp>" . $province . "</contact:sp>\n                                                <contact:pc>" . $postalcode . "</contact:pc>\n                                                <contact:cc>" . $country . "</contact:cc>\n                                        </contact:addr>\n                                </contact:ascii>\n                                <contact:voice>" . $telephone . "</contact:voice>\n                                <contact:fax>" . $fax . "</contact:fax>\n                                <contact:email>" . $email . "</contact:email>\n                                <contact:pw>" . $password1 . "</contact:pw>\n                        </contact:create>\n</create>\n";
    $xml .= "               <clTRID>" . $clTrid . "</clTRID>\n                <chksum>" . $checksum . "</chksum>\n        </command>\n</epp>";
    $result = onlinenic_sendCommand($fp, $xml);
    $resultcode = onlinenic_getResultCode($result);
    if( $resultcode != '1000' )
    {
        $errormsg = onlinenic_getResultText($resultcode);
        $msg = onlinenic_GetValue($result, "<msg>", "</msg>");
        $error = onlinenic_GetValue($result, "<value>", "</value>");
        $error = $msg . " - " . $error;
        $values['error'] = $resultcode . " - " . $errormsg . ": " . $error;
    }
    $values['contactid'] = onlinenic_GetValue($result, "<contact:id>", "</contact:id>");
    return $values;
}
function onlinenic_TransferDomain($params)
{
    $username = $params['Username'];
    $password = md5($params['Password']);
    $testmode = $params['TestMode'];
    $tld = $params['tld'];
    $sld = $params['sld'];
    $domain = $sld . "." . $tld;
    if( $testmode )
    {
        $username = 135610;
        $password = md5('654123');
    }
    $values = onlinenic_Login($fp, $username, $password, $testmode);
    if( $values['error'] )
    {
        return $values;
    }
    $domain_type = onlinenic_getDomainType($tld);
    $password1 = onlinenic_genpw();
    $clTrid = substr(md5($domain), 0, 10) . mt_rand(1000000000, 9999999999);
    $checksum = md5($username . $password . $clTrid . 'transferdomain' . $domain_type . $domain);
    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n            <epp>\n            <command>\n            <transfer>\n            <domain:transfer>\n            <domain:name>" . $domain . "</domain:name>\n            <domain:type>" . $domain_type . "</domain:type>\n            <domain:pw>" . $password1 . "</domain:pw>\n            </domain:transfer>\n            </transfer>\n            <unspec/>\n            <clTRID>" . $clTrid . "</clTRID>\n            <chksum>" . $checksum . "</chksum>\n            </command>\n            </epp>";
    $result = onlinenic_sendCommand($fp, $xml);
    $resultcode = onlinenic_getResultCode($result);
    onlinenic_Logout($fp, $username, $password);
    if( $resultcode != '1000' && $resultcode != '1001' )
    {
        $errormsg = onlinenic_getResultText($resultcode);
        $msg = onlinenic_GetValue($result, "<msg>", "</msg>");
        $error = onlinenic_GetValue($result, "<value>", "</value>");
        $error = $msg . " - " . $error;
        $values['error'] = $resultcode . " - " . $errormsg . ": " . $error;
        return $values;
    }
}
function onlinenic_RenewDomain($params)
{
    $username = $params['Username'];
    $password = md5($params['Password']);
    $testmode = $params['TestMode'];
    $tld = $params['tld'];
    $sld = $params['sld'];
    $domain = $sld . "." . $tld;
    if( $testmode )
    {
        $username = 135610;
        $password = md5('654123');
    }
    $year = $params['regperiod'];
    $values = onlinenic_Login($fp, $username, $password, $testmode);
    if( $values['error'] )
    {
        return $values;
    }
    $domain_type = onlinenic_getDomainType($tld);
    $clTrid = substr(md5($domain), 0, 10) . mt_rand(1000000000, 9999999999);
    $checksum = md5($username . $password . $clTrid . 'renewdomain' . $domain_type . $domain . $year);
    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n            <epp>\n                    <command>\n                            <renew>\n                                    <domain:renew>\n                                            <domain:type>" . $domain_type . "</domain:type>\n                                            <domain:name>" . $domain . "</domain:name>\n                                            <domain:period>" . $year . "</domain:period>\n                                    </domain:renew>\n                            </renew>\n                            <clTRID>" . $clTrid . "</clTRID>\n                            <chksum>" . $checksum . "</chksum>\n                    </command>\n            </epp>";
    $result = onlinenic_sendCommand($fp, $xml);
    $resultcode = onlinenic_getResultCode($result);
    onlinenic_Logout($fp, $username, $password);
    if( $resultcode != '1000' )
    {
        $errormsg = onlinenic_getResultText($resultcode);
        $msg = onlinenic_GetValue($result, "<msg>", "</msg>");
        $error = onlinenic_GetValue($result, "<value>", "</value>");
        $error = $msg . " - " . $error;
        $values['error'] = $resultcode . " - " . $errormsg . ": " . $error;
        return $values;
    }
}
function onlinenic_GetContactDetails($params)
{
    $username = $params['Username'];
    $password = md5($params['Password']);
    $testmode = $params['TestMode'];
    $tld = $params['tld'];
    $sld = $params['sld'];
    $domain = $sld . "." . $tld;
    if( $testmode )
    {
        $username = 135610;
        $password = md5('654123');
    }
    $values = onlinenic_Login($fp, $username, $password, $testmode);
    if( $values['error'] )
    {
        return $values;
    }
    $domain_type = onlinenic_getDomainType($tld);
    $clTrid = substr(md5($domain), 0, 10) . mt_rand(1000000000, 9999999999);
    $checksum = md5($username . $password . $clTrid . 'getdomaininfo');
    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n        <epp>\n        <command>\n        <getdomaininfo>\n        <clID>" . $username . "</clID>\n        <domain>" . $domain . "</domain>\n        <domain:type>" . $domain_type . "</domain:type>\n        <options>\n        <version>1.0</version>\n        <lang>en</lang>\n        </options>\n        </getdomaininfo>\n        <clTRID>" . $clTrid . "</clTRID>\n        <chksum>" . $checksum . "</chksum>\n        </command>\n        </epp>";
    $result = onlinenic_sendCommand($fp, $xml);
    $resultcode = onlinenic_getResultCode($result);
    onlinenic_Logout($fp, $username, $password);
    if( $resultcode != '1000' )
    {
        $errormsg = onlinenic_getResultText($resultcode);
        $msg = onlinenic_GetValue($result, "<msg>", "</msg>");
        $error = onlinenic_GetValue($result, "<value>", "</value>");
        $error = $msg . " - " . $error;
        $values['error'] = $resultcode . " - " . $errormsg . ": " . $error;
    }
    else
    {
        $name = onlinenic_GetValue($result, "<r_name>", "</r_name>");
        $company = onlinenic_GetValue($result, "<r_org>", "</r_org>");
        $address = onlinenic_GetValue($result, "<r_addr>", "</r_addr>");
        $city = onlinenic_GetValue($result, "<r_city>", "</r_city>");
        $state = onlinenic_GetValue($result, "<r_sp>", "</r_sp>");
        $postcode = onlinenic_GetValue($result, "<r_pc>", "</r_pc>");
        $country = onlinenic_GetValue($result, "<r_cc>", "</r_cc>");
        $tel = onlinenic_GetValue($result, "<r_phone>", "</r_phone>");
        $fax = onlinenic_GetValue($result, "<r_fax>", "</r_fax>");
        $email = onlinenic_GetValue($result, "<r_email>", "</r_email>");
        $values['Registrant']["Full Name"] = $name;
        $values['Registrant']["Company Name"] = $company;
        $values['Registrant']['Address'] = $address;
        $values['Registrant']['City'] = $city;
        $values['Registrant']['State'] = $state;
        $values['Registrant']['Postcode'] = $postcode;
        $values['Registrant']['Country'] = $country;
        $values['Registrant']["Phone Number"] = $tel;
        $values['Registrant']["Fax Number"] = $fax;
        $values['Registrant']['Email'] = $email;
        $name = onlinenic_GetValue($result, "<a_name>", "</a_name>");
        $company = onlinenic_GetValue($result, "<a_org>", "</a_org>");
        $address = onlinenic_GetValue($result, "<a_addr>", "</a_addr>");
        $city = onlinenic_GetValue($result, "<a_city>", "</a_city>");
        $state = onlinenic_GetValue($result, "<a_sp>", "</a_sp>");
        $postcode = onlinenic_GetValue($result, "<a_pc>", "</a_pc>");
        $country = onlinenic_GetValue($result, "<a_cc>", "</a_cc>");
        $tel = onlinenic_GetValue($result, "<a_phone>", "</a_phone>");
        $fax = onlinenic_GetValue($result, "<a_fax>", "</a_fax>");
        $email = onlinenic_GetValue($result, "<a_email>", "</a_email>");
        $values['Admin']["Full Name"] = $name;
        $values['Admin']["Company Name"] = $company;
        $values['Admin']['Address'] = $address;
        $values['Admin']['City'] = $city;
        $values['Admin']['State'] = $state;
        $values['Admin']['Postcode'] = $postcode;
        $values['Admin']['Country'] = $country;
        $values['Admin']["Phone Number"] = $tel;
        $values['Admin']["Fax Number"] = $fax;
        $values['Admin']['Email'] = $email;
        $name = onlinenic_GetValue($result, "<t_name>", "</t_name>");
        $company = onlinenic_GetValue($result, "<t_org>", "</t_org>");
        $address = onlinenic_GetValue($result, "<t_addr>", "</t_addr>");
        $city = onlinenic_GetValue($result, "<t_city>", "</t_city>");
        $state = onlinenic_GetValue($result, "<t_sp>", "</t_sp>");
        $postcode = onlinenic_GetValue($result, "<t_pc>", "</t_pc>");
        $country = onlinenic_GetValue($result, "<t_cc>", "</t_cc>");
        $tel = onlinenic_GetValue($result, "<t_phone>", "</t_phone>");
        $fax = onlinenic_GetValue($result, "<t_fax>", "</t_fax>");
        $email = onlinenic_GetValue($result, "<t_email>", "</t_email>");
        $values['Tech']["Full Name"] = $name;
        $values['Tech']["Company Name"] = $company;
        $values['Tech']['Address'] = $address;
        $values['Tech']['City'] = $city;
        $values['Tech']['State'] = $state;
        $values['Tech']['Postcode'] = $postcode;
        $values['Tech']['Country'] = $country;
        $values['Tech']["Phone Number"] = $tel;
        $values['Tech']["Fax Number"] = $fax;
        $values['Tech']['Email'] = $email;
    }
    return $values;
}
function onlinenic_SaveContactDetails($params)
{
    $username = $params['Username'];
    $password = md5($params['Password']);
    $testmode = $params['TestMode'];
    $tld = $params['tld'];
    $sld = $params['sld'];
    $domain = $sld . "." . $tld;
    if( $testmode )
    {
        $username = 135610;
        $password = md5('654123');
    }
    $values = onlinenic_Login($fp, $username, $password, $testmode);
    if( $values['error'] )
    {
        return $values;
    }
    $domain_type = onlinenic_getDomainType($tld);
    $clTrid = substr(md5($domain), 0, 10) . mt_rand(1000000000, 9999999999);
    $checksum = md5($username . $password . $clTrid . 'getdomaininfo');
    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n    <epp>\n        <command>\n        <getdomaininfo>\n        <clID>" . $username . "</clID>\n        <domain>" . $domain . "</domain>\n        <domain:type>" . $domain_type . "</domain:type>\n        <options>\n        <version>1.0</version>\n        <lang>en</lang>\n        </options>\n        </getdomaininfo>\n        <clTRID>" . $clTrid . "</clTRID>\n        <chksum>" . $checksum . "</chksum>\n        </command>\n    </epp>";
    $result = onlinenic_sendCommand($fp, $xml);
    $resultcode = onlinenic_getResultCode($result);
    if( $resultcode != '1000' )
    {
        $errormsg = onlinenic_getResultText($resultcode);
        $msg = onlinenic_GetValue($result, "<msg>", "</msg>");
        $error = onlinenic_GetValue($result, "<value>", "</value>");
        $error = $msg . " - " . $error;
        $values['error'] = $resultcode . " - " . $errormsg . ": " . $error;
    }
    else
    {
        $password1 = onlinenic_GetValue($result, "<pwd>", "</pwd>");
    }
    $contact_type = '4';
    $name = '';
    $company = $params['contactdetails']['Registrant']["Company Name"];
    $address = $params['contactdetails']['Registrant']['Address'];
    $city = $params['contactdetails']['Registrant']['City'];
    $state = $params['contactdetails']['Registrant']['State'];
    $postcode = $params['contactdetails']['Registrant']['Postcode'];
    $country = $params['contactdetails']['Registrant']['Country'];
    $tel = $params['contactdetails']['Registrant']["Phone Number"];
    $fax = $params['contactdetails']['Registrant']["Fax Number"];
    $email = $params['contactdetails']['Registrant']['Email'];
    $password1 = onlinenic_genpw();
    $clTrid = substr(md5($domain), 0, 10) . mt_rand(1000000000, 9999999999);
    $checksum = md5($username . $password . $clTrid . 'updcontact' . $domain_type . $domain . $contact_type . $name . $company . $email);
    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n    <epp>\n        <command>\n        <update>\n            <contact:update>\n                <contact:domaintype>" . $domain_type . "</contact:domaintype>\n                <contact:domain>" . $domain . "</contact:domain>\n                <contact:contacttype>" . $contact_type . "</contact:contacttype>\n                <contact:ascii>\n                <contact:name>" . $name . "</contact:name>\n                <contact:org>" . $company . "</contact:org>\n                <contact:addr>\n                <contact:street1>" . $address . "</contact:street1>\n                <contact:city>" . $city . "</contact:city>\n                <contact:sp>" . $state . "</contact:sp>\n                <contact:pc>" . $postcode . "</contact:pc>\n                <contact:cc>" . $country . "</contact:cc>\n                </contact:addr>\n                </contact:ascii>\n                <contact:voice>" . $tel . "</contact:voice>\n                <contact:fax>" . $fax . "</contact:fax>\n                <contact:email>" . $email . "</contact:email>\n                <contact:pw>" . $password1 . "</contact:pw>\n            </contact:update>\n        </update>\n        <clTRID>" . $clTrid . "</clTRID>\n        <chksum>" . $checksum . "</chksum>\n        </command>\n    </epp>";
    $result = onlinenic_sendCommand($fp, $xml);
    $resultcode = onlinenic_getResultCode($result);
    if( $resultcode != '1000' )
    {
        $errormsg = onlinenic_getResultText($resultcode);
        $msg = onlinenic_GetValue($result, "<msg>", "</msg>");
        $error = onlinenic_GetValue($result, "<value>", "</value>");
        $error = $msg . " - " . $error;
        $values['error'] = $resultcode . " - " . $errormsg . ": " . $error;
        return $values;
    }
    $contact_type = '1';
    $name = $params['contactdetails']['Admin']["Full Name"];
    $company = $params['contactdetails']['Admin']["Company Name"];
    $address = $params['contactdetails']['Admin']['Address'];
    $city = $params['contactdetails']['Admin']['City'];
    $state = $params['contactdetails']['Admin']['State'];
    $postcode = $params['contactdetails']['Admin']['Postcode'];
    $country = $params['contactdetails']['Admin']['Country'];
    $tel = $params['contactdetails']['Admin']["Phone Number"];
    $fax = $params['contactdetails']['Admin']["Fax Number"];
    $email = $params['contactdetails']['Admin']['Email'];
    $clTrid = substr(md5($domain), 0, 10) . mt_rand(1000000000, 9999999999);
    $checksum = md5($username . $password . $clTrid . 'updcontact' . $domain_type . $domain . $contact_type . $name . $company . $email);
    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n    <epp>\n        <command>\n        <update>\n            <contact:update>\n                <contact:domaintype>" . $domain_type . "</contact:domaintype>\n                <contact:domain>" . $domain . "</contact:domain>\n                <contact:contacttype>" . $contact_type . "</contact:contacttype>\n                <contact:ascii>\n                <contact:name>" . $name . "</contact:name>\n                <contact:org>" . $company . "</contact:org>\n                <contact:addr>\n                <contact:street1>" . $address . "</contact:street1>\n                <contact:city>" . $city . "</contact:city>\n                <contact:sp>" . $state . "</contact:sp>\n                <contact:pc>" . $postcode . "</contact:pc>\n                <contact:cc>" . $country . "</contact:cc>\n                </contact:addr>\n                </contact:ascii>\n                <contact:voice>" . $tel . "</contact:voice>\n                <contact:fax>" . $fax . "</contact:fax>\n                <contact:email>" . $email . "</contact:email>\n                <contact:pw>" . $password1 . "</contact:pw>\n            </contact:update>\n        </update>\n        <clTRID>" . $clTrid . "</clTRID>\n        <chksum>" . $checksum . "</chksum>\n        </command>\n    </epp>";
    $result = onlinenic_sendCommand($fp, $xml);
    $resultcode = onlinenic_getResultCode($result);
    if( $resultcode != '1000' )
    {
        $errormsg = onlinenic_getResultText($resultcode);
        $msg = onlinenic_GetValue($result, "<msg>", "</msg>");
        $error = onlinenic_GetValue($result, "<value>", "</value>");
        $error = $msg . " - " . $error;
        $values['error'] = $resultcode . " - " . $errormsg . ": " . $error;
        return $values;
    }
    $contact_type = '2';
    $name = $params['contactdetails']['Tech']["Full Name"];
    $company = $params['contactdetails']['Tech']["Company Name"];
    $address = $params['contactdetails']['Tech']['Address'];
    $city = $params['contactdetails']['Tech']['City'];
    $state = $params['contactdetails']['Tech']['State'];
    $postcode = $params['contactdetails']['Tech']['Postcode'];
    $country = $params['contactdetails']['Tech']['Country'];
    $tel = $params['contactdetails']['Tech']["Phone Number"];
    $fax = $params['contactdetails']['Tech']["Fax Number"];
    $email = $params['contactdetails']['Tech']['Email'];
    $clTrid = substr(md5($domain), 0, 10) . mt_rand(1000000000, 9999999999);
    $checksum = md5($username . $password . $clTrid . 'updcontact' . $domain_type . $domain . $contact_type . $name . $company . $email);
    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n    <epp>\n        <command>\n        <update>\n            <contact:update>\n                <contact:domaintype>" . $domain_type . "</contact:domaintype>\n                <contact:domain>" . $domain . "</contact:domain>\n                <contact:contacttype>" . $contact_type . "</contact:contacttype>\n                <contact:ascii>\n                <contact:name>" . $name . "</contact:name>\n                <contact:org>" . $company . "</contact:org>\n                <contact:addr>\n                <contact:street1>" . $address . "</contact:street1>\n                <contact:city>" . $city . "</contact:city>\n                <contact:sp>" . $state . "</contact:sp>\n                <contact:pc>" . $postcode . "</contact:pc>\n                <contact:cc>" . $country . "</contact:cc>\n                </contact:addr>\n                </contact:ascii>\n                <contact:voice>" . $tel . "</contact:voice>\n                <contact:fax>" . $fax . "</contact:fax>\n                <contact:email>" . $email . "</contact:email>\n                <contact:pw>" . $password1 . "</contact:pw>\n            </contact:update>\n        </update>\n        <clTRID>" . $clTrid . "</clTRID>\n        <chksum>" . $checksum . "</chksum>\n        </command>\n    </epp>";
    $result = onlinenic_sendCommand($fp, $xml);
    $resultcode = onlinenic_getResultCode($result);
    onlinenic_Logout($fp, $username, $password);
    if( $resultcode != '1000' )
    {
        $errormsg = onlinenic_getResultText($resultcode);
        $msg = onlinenic_GetValue($result, "<msg>", "</msg>");
        $error = onlinenic_GetValue($result, "<value>", "</value>");
        $error = $msg . " - " . $error;
        $values['error'] = $resultcode . " - " . $errormsg . ": " . $error;
        return $values;
    }
    return $values;
}
function onlinenic_RegisterNameserver($params)
{
    $username = $params['Username'];
    $password = md5($params['Password']);
    $testmode = $params['TestMode'];
    $tld = $params['tld'];
    $sld = $params['sld'];
    $nameserver = $params['nameserver'];
    $ipaddress = $params['ipaddress'];
    if( $testmode )
    {
        $username = 135610;
        $password = md5('654123');
    }
    $clTrid = substr(md5($domain), 0, 10) . mt_rand(1000000000, 9999999999);
    $domain = $sld . "." . $tld;
    $domain_type = onlinenic_getDomainType($tld);
    $checksum = md5($username . $password . $clTrid . 'crthost' . $domain_type . $nameserver . $ipaddress);
    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n            <epp xmlns=\"urn:iana:xml:ns:epp-1.0\"\n            xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\n            xsi:schemaLocation=\"urn:iana:xml:ns:epp-1.0 epp-1.0.xsd\">\n                <command>\n                <create>\n                    <host:create xmlns:host=\"urn:iana:xml:ns:host-1.0\"\n                    xsi:schemaLocation=\"urn:iana:xml:ns:host-1.0 host-1.0.xsd\">\n                        <host:domaintype>" . $domain_type . "</host:domaintype>\n                        <host:name>" . $nameserver . "</host:name>\n                        <host:addr ip=\"v4\">" . $ipaddress . "</host:addr>\n                    </host:create>\n                    </create>\n                <unspec/>\n                <clTRID>" . $clTrid . "</clTRID>\n                <chksum>" . $checksum . "</chksum>\n                </command>\n            </epp>";
    $values = onlinenic_Login($fp, $username, $password, $testmode);
    if( $values['error'] )
    {
        return $values;
    }
    $result = onlinenic_sendCommand($fp, $xml);
    $resultcode = onlinenic_getResultCode($result);
    onlinenic_Logout($fp, $username, $password);
    if( $resultcode != '1000' )
    {
        $errormsg = onlinenic_getResultText($resultcode);
        $msg = onlinenic_GetValue($result, "<msg>", "</msg>");
        $error = onlinenic_GetValue($result, "<value>", "</value>");
        $error = $msg . " - " . $error;
        $values['error'] = $resultcode . " - " . $errormsg . ": " . $error;
    }
    return $values;
}
function onlinenic_ModifyNameserver($params)
{
    $username = $params['Username'];
    $password = md5($params['Password']);
    $testmode = $params['TestMode'];
    $tld = $params['tld'];
    $sld = $params['sld'];
    $nameserver = $params['nameserver'];
    $currentipaddress = $params['currentipaddress'];
    $newipaddress = $params['newipaddress'];
    if( $testmode )
    {
        $username = 135610;
        $password = md5('654123');
    }
    $clTrid = substr(md5($domain), 0, 10) . mt_rand(1000000000, 9999999999);
    $domain = $sld . "." . $tld;
    $domain_type = onlinenic_getDomainType($tld);
    $checksum = md5($username . $password . $clTrid . 'updhost' . $domain_type . $nameserver . $newipaddress . $currentipaddress);
    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n            <epp xmlns=\"urn:iana:xml:ns:epp-1.0\"\n            xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\n            xsi:schemaLocation=\"urn:iana:xml:ns:epp-1.0 epp-1.0.xsd\">\n                <command>\n                <update>\n                    <host:update xmlns:host=\"urn:iana:xml:ns:host-1.0\"\n                    xsi:schemaLocation=\"urn:iana:xml:ns:host-1.0 host-1.0.xsd\">\n                        <host:domaintype>" . $domain_type . "</host:domaintype>\n                        <host:name>" . $nameserver . "</host:name>\n                        <host:add>\n                            <host:addr ip=\"v4\">" . $newipaddress . "</host:addr>\n                            </host:add>\n                            <host:rem>\n                            <host:addr ip=\"v4\">" . $currentipaddress . "</host:addr>\n                            </host:rem>\n                    </host:update>\n                    </update>\n                <unspec/>\n                <clTRID>" . $clTrid . "</clTRID>\n                <chksum>" . $checksum . "</chksum>\n                </command>\n            </epp>";
    $values = onlinenic_Login($fp, $username, $password, $testmode);
    if( $values['error'] )
    {
        return $values;
    }
    $result = onlinenic_sendCommand($fp, $xml);
    $resultcode = onlinenic_getResultCode($result);
    onlinenic_Logout($fp, $username, $password);
    if( $resultcode != '1000' )
    {
        $errormsg = onlinenic_getResultText($resultcode);
        $msg = onlinenic_GetValue($result, "<msg>", "</msg>");
        $error = onlinenic_GetValue($result, "<value>", "</value>");
        $error = $msg . " - " . $error;
        $values['error'] = $resultcode . " - " . $errormsg . ": " . $error;
    }
    return $values;
}
function onlinenic_DeleteNameserver($params)
{
    $username = $params['Username'];
    $password = md5($params['Password']);
    $testmode = $params['TestMode'];
    $tld = $params['tld'];
    $sld = $params['sld'];
    $nameserver = $params['nameserver'];
    if( $testmode )
    {
        $username = 135610;
        $password = md5('654123');
    }
    $clTrid = substr(md5($domain), 0, 10) . mt_rand(1000000000, 9999999999);
    $domain = $sld . "." . $tld;
    $domain_type = onlinenic_getDomainType($tld);
    $checksum = md5($username . $password . $clTrid . 'delhost' . $domain_type . $nameserver . $ipaddress);
    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n            <epp xmlns=\"urn:iana:xml:ns:epp-1.0\"\n            xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\n            xsi:schemaLocation=\"urn:iana:xml:ns:epp-1.0 epp-1.0.xsd\">\n                <command>\n                    <delete>\n                    <host:delete xmlns:host=\"urn:iana:xml:ns:host-1.0\"\n                    xsi:schemaLocation=\"urn:iana:xml:ns:host-1.0 host-1.0.xsd\">\n                        <host:domaintype>" . $domain_type . "</host:domaintype>\n                        <host:name>" . $nameserver . "</host:name>\n                    </host:delete>\n                    </delete>\n                <unspec/>\n                <clTRID>" . $clTrid . "</clTRID>\n                <chksum>" . $checksum . "</chksum>\n                </command>\n            </epp>";
    $values = onlinenic_Login($fp, $username, $password, $testmode);
    if( $values['error'] )
    {
        return $values;
    }
    $result = onlinenic_sendCommand($fp, $xml);
    $resultcode = onlinenic_getResultCode($result);
    onlinenic_Logout($fp, $username, $password);
    if( $resultcode != '1000' )
    {
        $errormsg = onlinenic_getResultText($resultcode);
        $msg = onlinenic_GetValue($result, "<msg>", "</msg>");
        $error = onlinenic_GetValue($result, "<value>", "</value>");
        $error = $msg . " - " . $error;
        $values['error'] = $resultcode . " - " . $errormsg . ": " . $error;
    }
    return $values;
}
function onlinenic_GetExpirationDate($fp, $username, $password, $domainname, $domainext)
{
    $domain = $domainname . "." . $domainext;
    $domain_type = onlinenic_getDomainType($domainext);
    $clTrid = rand();
    $checksum = md5($username . $password . $clTrid . 'getdomaininfo');
    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n            <epp>\n                <command>\n                    <getdomaininfo>\n                        <clID>" . $username . "</clID>\n                        <domain>" . $domain . "</domain>\n                        <domain:type>" . $domain_type . "</domain:type>\n                        <options>\n                            <version>1.0</version>\n                            <lang>en</lang>\n                        </options>\n                    </getdomaininfo>\n                    <clTRID>" . $clTrid . "</clTRID>\n                    <chksum>" . $checksum . "</chksum>\n                </command>\n            </epp>";
    $result = onlinenic_sendCommand($fp, $xml);
    $resultcode = onlinenic_getResultCode($result);
    if( $resultcode != '1000' )
    {
        $errormsg = onlinenic_getResultText($resultcode);
        $msg = onlinenic_GetValue($result, "<msg>", "</msg>");
        $error = onlinenic_GetValue($result, "<value>", "</value>");
        $error = $msg . " - " . $error;
        $values['error'] = $resultcode . " - " . $errormsg . ": " . $error;
    }
    else
    {
        $values['expirydate'] = onlinenic_GetValue($result, "<expdate>", "</expdate>");
    }
    return $values;
}
function onlinenic_Logout($fp, $username, $password)
{
    $clTrid = substr(md5($domain), 0, 10) . mt_rand(1000000000, 9999999999);
    $checksum = md5($username . $password . $clTrid . 'logout');
    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n        <epp>\n        <command>\n        <logout/>\n        <unspec/>\n        <clTRID>" . $clTrid . "</clTRID>\n        <chksum>" . $checksum . "</chksum>\n        </command>\n        </epp>";
    $result = onlinenic_sendCommand($fp, $xml);
    $resultcode = onlinenic_getResultCode($result);
    if( $resultcode != '1500' )
    {
        $errormsg = onlinenic_getResultText($resultcode);
        $msg = onlinenic_GetValue($result, "<msg>", "</msg>");
        $error = onlinenic_GetValue($result, "<value>", "</value>");
        $error = $msg . " - " . $error;
        $values['error'] = $resultcode . " - " . $errormsg . ": " . $error;
    }
    return $values;
}
function onlinenic_GetValue($msg, $str1, $str2)
{
    $start_pos = strpos($msg, $str1);
    $stop_post = strpos($msg, $str2);
    $start_pos += strlen($str1);
    return substr($msg, $start_pos, $stop_post - $start_pos);
}
function onlinenic_getResultCode($result)
{
    $start_pos = strpos($result, "<result code=\"");
    return substr($result, $start_pos + 14, 4);
}
function onlinenic_Login(&$fp, $username, $password, $testmode)
{
    $server = "www.onlinenic.com";
    $port = 20001;
    if( $testmode )
    {
        $server = "218.5.81.149";
    }
    if( !($fp = fsockopen($server, $port, $errno, $errstr, 90)) )
    {
        $values['error'] = "Connection Failed - " . $errno . " - " . $errstr;
        return $values;
    }
    $i = 0;
    while( !feof($fp) )
    {
        $i++;
        $line = fgets($fp, 2);
        $result .= $line;
        if( ereg("</epp>\$", $result) )
        {
            break;
        }
        if( 5000 < $i )
        {
            break;
        }
    }
    if( ereg("</greeting></epp>\$", $result) )
    {
        $clTrid = substr(md5($domain), 0, 10) . mt_rand(1000000000, 9999999999);
        $checksum = md5($username . $password . $clTrid . 'login');
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n<epp>\n        <command>\n                <creds>\n                        <clID>" . $username . "</clID>\n                        <options>\n                                <version>1.0</version>\n                                <lang>en</lang>\n                        </options>\n                </creds>\n                <clTRID>" . $clTrid . "</clTRID>\n                <login>\n                        <chksum>" . $checksum . "</chksum>\n                </login>\n        </command>\n</epp>";
        $result = onlinenic_sendCommand($fp, $xml, $username, $password);
        $resultcode = onlinenic_getresultcode($result);
        if( $resultcode != '1000' )
        {
            $errormsg = onlinenic_getResultText($resultcode);
            $msg = onlinenic_getvalue($result, "<msg>", "</msg>");
            $error = onlinenic_getvalue($result, "<value>", "</value>");
            $error = $msg . " - " . $error;
            $values['error'] = $resultcode . " - " . $errormsg . ": " . $error;
        }
        return $values;
    }
    $values['error'] = "An Error Occurred with Connection";
    return $values;
}
function onlinenic_getDomainType($tld)
{
    switch( $tld )
    {
        case 'ws':
            $domain_type = 301;
            break;
        case 'tv':
            $domain_type = 400;
            break;
        case 'us':
            $domain_type = 806;
            break;
        case 'mobi':
            $domain_type = 903;
            break;
        case 'cn':
            $domain_type = 220;
            break;
        case 'cc':
            $domain_type = 600;
            break;
        case 'biz':
            $domain_type = 800;
            break;
        case 'info':
            $domain_type = 805;
            break;
        case 'in':
            $domain_type = 808;
            break;
        case 'eu':
            $domain_type = 902;
            break;
        case 'co':
            $domain_type = 908;
            break;
        case "co.uk":
            break;
        case "me.uk":
            break;
        case "org.uk":
            $domain_type = 901;
            break;
        case 'me':
            $domain_type = 906;
            break;
        default:
            $domain_type = 0;
            break;
    }
    return $domain_type;
}
function onlinenic_getResultText($resultCode)
{
    switch( $resultCode )
    {
        case '1000':
            return "Command completed successfully";
            break;
        case '1300':
            return "Command completed successfully; no messages";
            break;
        case '1500':
            return "Command completed successfully; ending session";
            break;
        case '1700':
            return "Command completed successfully; not in lib";
            break;
        case '2001':
            return "Command syntax error";
            break;
        case '2002':
            return "Command use error";
            break;
        case '2003':
            return "Required Parameter missing";
            break;
        case '2004':
            return "Parameter value range err";
            break;
        case '2005':
            return "Parameter value syntax error";
            break;
        case '2104':
            return "Billing fail; Not enough funds ?";
            break;
        case '2201':
            return "Authorization error";
            break;
        case '2302':
            return "Domain is currently with OnlineNIC";
            break;
        case '2303':
            return "Object does not exist";
            break;
        case '2304':
            return "Object status prohibits operation";
            break;
        case '2305':
            return "Object association prohibits operation";
            break;
        case '2306':
            return "Parameter value policy error";
            break;
        case '2400':
            return "Command fail";
            break;
        case '2500':
            return "Command failed;server ending session";
            break;
        case '2501':
            return "Timeout;server ending session";
            break;
        case '5000':
            return "Something error in netware";
            break;
        case '5500':
            return "Did not login";
            break;
        case '6000':
            return "Checksum error";
            break;
        default:
            return "No response from OnlineNIC";
            break;
    }
}
function onlinenic_sendCommand($fp, $command, $username = '', $password = '')
{
    fputs($fp, $command);
    $i = 0;
    while( !feof($fp) )
    {
        $i++;
        $line = fgets($fp, 2);
        $result .= $line;
        if( ereg("</epp>\$", $result) )
        {
            break;
        }
        if( 5000 < $i )
        {
            break;
        }
    }
    $xmlinput = XMLtoArray($command);
    $xmlinput = array_keys($xmlinput['EPP']['COMMAND']);
    $xmlinput = $xmlinput[2];
    logModuleCall('onlinenic', $xmlinput, $command, $result, '', array( $username, $password ));
    return $result;
}
function onlinenic_genpw()
{
    $pw = '';
    $length = 3;
    $seeds = '0123456789';
    $seeds_count = strlen($seeds) - 1;
    for( $i = 0; $i < $length; $i++ )
    {
        $pw .= $seeds[rand(0, $seeds_count)];
    }
    $seeds = 'abcdefghijklmnopqrstuvwxyz';
    $seeds_count = strlen($seeds) - 1;
    for( $i = 0; $i < $length; $i++ )
    {
        $pw .= $seeds[rand(0, $seeds_count)];
    }
    $seeds = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $seeds_count = strlen($seeds) - 1;
    for( $i = 0; $i < $length; $i++ )
    {
        $pw .= $seeds[rand(0, $seeds_count)];
    }
    $seeds = "!#\$%()*+,-./=?@[]^";
    $seeds_count = strlen($seeds) - 1;
    for( $i = 0; $i < $length; $i++ )
    {
        $pw .= $seeds[rand(0, $seeds_count)];
    }
    return $pw;
}
function onlinenic_Sync($params)
{
    $username = $params['Username'];
    $password = md5($params['Password']);
    $testmode = $params['TestMode'];
    if( $testmode )
    {
        $username = 135610;
        $password = md5('654123');
    }
    $values = onlinenic_login($fp, $username, $password, $testmode);
    if( $values['error'] )
    {
        return $values;
    }
    $values = onlinenic_getexpirationdate($fp, $username, $password, $params['sld'], $params['tld']);
    if( $values['error'] )
    {
        return $values;
    }
    $expirydate = strtotime($values['expirydate']);
    $expirydate = date('Y-m-d', $expirydate);
    return array( 'active' => true, 'expirydate' => $expirydate );
}