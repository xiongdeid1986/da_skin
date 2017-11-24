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
function registereu_getConfigArray()
{
    $configarray = array( 'FriendlyName' => array( 'Type' => 'System', 'Value' => "Register.eu" ), 'Username' => array( 'Type' => 'text', 'Size' => '20', 'Description' => "Enter your username here" ), 'Password' => array( 'Type' => 'password', 'Size' => '20', 'Description' => "Enter your password here" ), 'TestMode' => array( 'Type' => 'yesno' ) );
    return $configarray;
}
function registereu_GetNameservers($params)
{
    return array( 'ns1' => "Enter value to change", 'ns2' => "Enter value to change" );
}
function registereu_SaveNameservers($params)
{
    $username = $params['Username'];
    $password = $params['Password'];
    $domain = $params['sld'];
    $domext = $params['tld'];
    $nameserver1 = $params['ns1'];
    $nameserver2 = $params['ns2'];
    $base_url = "http://www.register.eu/gateway/request.aspx?";
    $head_query = "version=2&action=ns_update&logon=" . $username . "&password=" . $password . "&domain=" . $domain . "&extension=" . $domext;
    $body_query .= "&ns1=" . $nameserver1 . "&ns2=" . $nameserver2;
    if( $params['TestMode'] == 'on' )
    {
        $body_query .= "&exec=0";
    }
    $query = $head_query . $body_query;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
    curl_setopt($ch, CURLOPT_URL, $base_url);
    $data = curl_exec($ch);
    curl_close($ch);
    if( $xmldata = XMLtoArray($data) )
    {
        if( $xmldata['GATEWAY']['STATUS'] != 'OK' )
        {
            $values['error'] = $xmldata['GATEWAY']['ERROR'];
        }
    }
    else
    {
        $values['error'] = "Invalid data returned by server.";
    }
    return $values;
}
function registereu_GetContactDetails($params)
{
    echo "<center>Country code must be in <strong>two letter</strong> <a href=\"http://en.wikipedia.org/wiki/ISO_3166-1#Officially_assigned_code_elements\" target=\"_blank\">ISO 3166-1</a> format</center><br /><br />";
    $values['Registrant']["First Name"] = "Enter value to change";
    $values['Registrant']["Last Name"] = "Enter value to change";
    $values['Registrant']['Address'] = "Enter value to change";
    $values['Registrant']['City'] = "Enter value to change";
    $values['Registrant']["Postal Code"] = "Enter value to change";
    $values['Registrant']['Country'] = "Enter value to change";
    $values['Registrant']['Phone'] = "Enter value to change";
    $values['Registrant']['Email'] = "Enter value to change";
    return $values;
}
function registereu_SaveContactDetails($params)
{
    $username = $params['Username'];
    $password = $params['Password'];
    $domain = $params['sld'];
    $domext = $params['tld'];
    $nameserver1 = $params['ns1'];
    $nameserver2 = $params['ns2'];
    $firstname = $params['contactdetails']['Registrant']["First Name"];
    $lastname = $params['contactdetails']['Registrant']["Last Name"];
    $address = $params['contactdetails']['Registrant']['Address'];
    $city = $params['contactdetails']['Registrant']['City'];
    $postalcode = $params['contactdetails']['Registrant']["Postal Code"];
    $country = $params['contactdetails']['Registrant']['Country'];
    $phonenumber = $params['contactdetails']['Registrant']['Phone'];
    $email = $params['contactdetails']['Registrant']['Email'];
    $base_url = "http://www.register.eu/gateway/request.aspx?";
    $head_query = "version=2&action=lic_update&logon=" . $username . "&password=" . $password . "&domain=" . $domain . "&extension=" . $domext;
    $body_query .= "&firstname=" . $firstname . "&lastname=" . $lastname . "&address=" . $address . "&city=" . $city . "&zipcode=" . $postalcode . "&countrycode=" . $country . "&phone=" . $phonenumber . "&email=" . $email;
    if( $params['TestMode'] == 'on' )
    {
        $body_query .= "&exec=0";
    }
    $query = $head_query . $body_query;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
    curl_setopt($ch, CURLOPT_URL, $base_url);
    $data = curl_exec($ch);
    curl_close($ch);
    if( $xmldata = XMLtoArray($data) )
    {
        if( $xmldata['GATEWAY']['STATUS'] != 'OK' )
        {
            $values['error'] = $xmldata['GATEWAY']['ERROR'];
        }
    }
    else
    {
        $values['error'] = "Invalid data returned by server.";
    }
    return $values;
}
function registereu_RegisterDomain($params)
{
    $username = $params['Username'];
    $password = $params['Password'];
    $domain = $params['sld'];
    $domext = $params['tld'];
    $nameserver1 = $params['ns1'];
    $nameserver2 = $params['ns2'];
    $regperiod = $params['regperiod'];
    $RegistrantFirstName = $params['firstname'];
    $RegistrantLastName = $params['lastname'];
    $RegistrantAddress1 = $params['address1'];
    $RegistrantAddress2 = $params['address2'];
    $RegistrantCity = $params['city'];
    $RegistrantStateProvince = $params['state'];
    $RegistrantPostalCode = $params['postcode'];
    $RegistrantCountry = $params['country'];
    $RegistrantEmailAddress = $params['email'];
    $RegistrantPhone = $params['phonenumber'];
    $base_url = "http://www.register.eu/gateway/request.aspx?";
    $head_query = "version=2&action=dom_create&logon=" . $username . "&password=" . $password . "&domain=" . $domain . "&extension=" . $domext;
    $body_query .= "&period=" . $regperiod . "&email=" . $RegistrantEmailAddress . "&firstname=" . $RegistrantFirstName . "&lastname=" . $RegistrantLastName . "&address=" . $RegistrantAddress1 . "&city=" . $RegistrantCity . "&zipcode=" . $RegistrantPostalCode . "&countrycode=" . strtoupper($RegistrantCountry) . "&phone=" . $RegistrantPhone . "&ns1=" . $nameserver1 . "&ns2=" . $nameserver2;
    if( $params['TestMode'] == 'on' )
    {
        $body_query .= "&exec=0";
    }
    $query = $head_query . $body_query;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
    curl_setopt($ch, CURLOPT_URL, $base_url);
    $data = curl_exec($ch);
    curl_close($ch);
    if( $xmldata = XMLtoArray($data) )
    {
        if( $xmldata['GATEWAY']['STATUS'] != 'OK' )
        {
            $values['error'] = $xmldata['GATEWAY']['ERROR'];
        }
    }
    else
    {
        $values['error'] = "Invalid data returned by server.";
    }
    return $values;
}
function registereu_TransferDomain($params)
{
    $username = $params['Username'];
    $password = $params['Password'];
    $domain = $params['sld'];
    $domext = $params['tld'];
    $nameserver1 = $params['ns1'];
    $nameserver2 = $params['ns2'];
    $transfersecret = $params['transfersecret'];
    $regperiod = $params['regperiod'];
    $RegistrantFirstName = $params['firstname'];
    $RegistrantLastName = $params['lastname'];
    $RegistrantAddress1 = $params['address1'];
    $RegistrantAddress2 = $params['address2'];
    $RegistrantCity = $params['city'];
    $RegistrantStateProvince = $params['state'];
    $RegistrantPostalCode = $params['postcode'];
    $RegistrantCountry = $params['country'];
    $RegistrantEmailAddress = $params['email'];
    $RegistrantPhone = $params['phonenumber'];
    $base_url = "http://www.register.eu/gateway/request.aspx?";
    $head_query = "version=2&action=dom_transfer&logon=" . $username . "&password=" . $password . "&domain=" . $domain . "&extension=" . $domext;
    $body_query .= "&period=" . $regperiod . "&email=" . $RegistrantEmailAddress . "&firstname=" . $RegistrantFirstName . "&lastname=" . $RegistrantLastName . "&address=" . $RegistrantAddress1 . "&city=" . $RegistrantCity . "&zipcode=" . $RegistrantPostalCode . "&countrycode=" . strtoupper($RegistrantCountry) . "&phone=" . $RegistrantPhone . "&ns1=" . $nameserver1 . "&ns2=" . $nameserver2;
    if( $params['TestMode'] == 'on' )
    {
        $body_query .= "&exec=0";
    }
    $query = $head_query . $body_query;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
    curl_setopt($ch, CURLOPT_URL, $base_url);
    $data = curl_exec($ch);
    curl_close($ch);
    if( $xmldata = XMLtoArray($data) )
    {
        if( $xmldata['GATEWAY']['STATUS'] != 'OK' )
        {
            $values['error'] = $xmldata['GATEWAY']['ERROR'];
        }
    }
    else
    {
        $values['error'] = "Invalid data returned by server.";
    }
    return $values;
}