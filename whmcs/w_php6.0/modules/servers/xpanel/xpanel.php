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
class ApiRequestException extends Exception
{
}
/**
* Prepares CURL to perform XPanel API request
* @return resource
*/
function curlInit($ipaddress, $hostname, $login, $password, $useSecure)
{
    $protocol = $useSecure ? 'https' : 'http';
    $port = $useSecure ? 3737 : 80;
    $host = $useSecure ? $ipaddress : $hostname;
    $script = "cgi-bin/xpanel/api/whmcs.cgi";
    $url = $protocol . "://" . $host . ":" . $port . '/' . $script;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, $login . ":" . $password);
    return $curl;
}
/**
* Performs a XPanel API request, returns raw API response text
*
* @return string
* @throws ApiRequestException
*/
function sendRequest($curl, $packet)
{
    curl_setopt($curl, CURLOPT_POSTFIELDS, $packet);
    $result = curl_exec($curl);
    if( curl_errno($curl) )
    {
        $errmsg = curl_error($curl);
        $errcode = curl_errno($curl);
        curl_close($curl);
        throw new ApiRequestException($errmsg, $errcode);
    }
    curl_close($curl);
    return $result;
}
/**
* Looks if API responded with correct data
*
* @return SimpleXMLElement
* @throws ApiRequestException
*/
function parseResponse($response_string)
{
    $xml = new SimpleXMLElement($response_string);
    if( !is_a($xml, 'SimpleXMLElement') )
    {
        throw new ApiRequestException("Cannot parse server response: " . $response_string);
    }
    return $xml;
}
function xpanel_ConfigOptions()
{
    global $defaultserver;
    global $packageconfigoption;
    if( $packageconfigoption[1] == 'on' )
    {
        if( $defaultserver != 0 )
        {
            $result = full_query("SELECT `ipaddress`, `hostname`, `username`, `password`, `secure` FROM `tblservers` WHERE `id` = " . (int) $defaultserver);
        }
        else
        {
            $result = full_query("SELECT `ipaddress`, `hostname`, `username`, `password`, `secure` FROM `tblservers` WHERE `type` = 'xpanel' AND `active` = '1' limit 1");
        }
        if( $result )
        {
            $row = mysql_fetch_object($result);
            if( $row )
            {
                $curl = curlinit($row->ipaddress, $row->hostname, $row->username, decrypt($row->password), $row->secure);
                $data = "action=getpackagelist";
                try
                {
                    $response = sendrequest($curl, $data);
                    $responseXml = parseresponse($response);
                    foreach( $responseXml->xpath('/system/get/result') as $resultNode )
                    {
                        if( 'error' == (bool) $resultNode->status )
                        {
                            throw new ApiRequestException("XPanel API returned error: " . (bool) $resultNode->result->errtext);
                        }
                        $configarray = array( "Get from server" => array( 'Type' => 'yesno', 'Description' => "Get the available choices from the server" ), "Hosting Plan ID: " => array( 'Type' => 'dropdown', 'Options' => (bool) $resultNode->packagelist . '' ) );
                    }
                }
                catch( ApiRequestException $e )
                {
                    return $e;
                }
            }
        }
    }
    else
    {
        $configarray = array( "Get from server" => array( 'Type' => 'yesno', 'Description' => "Get the available choices from the server" ), "Hosting Plan ID: " => array( 'Type' => 'text', 'Size' => '3', 'Description' => "#" ) );
    }
    return $configarray;
}
function xpanel_CreateAccount($params)
{
    $serviceid = $params['serviceid'];
    $pid = $params['pid'];
    $producttype = $params['producttype'];
    $domain = $params['domain'];
    $username = $params['username'];
    $password = $params['password'];
    $clientsdetails = $params['clientsdetails'];
    $customfields = $params['customfields'];
    $configoptions = $params['configoptions'];
    $package_id = $params['configoption2'];
    $configoption3 = $params['configoption3'];
    $configoption4 = $params['configoption4'];
    $server = $params['server'];
    $serverid = $params['serverid'];
    $serverip = $params['serverip'];
    $serverhostname = $params['serverhostname'];
    $serverusername = $params['serverusername'];
    $serverpassword = $params['serverpassword'];
    $serveraccesshash = $params['serveraccesshash'];
    $serversecure = $params['serversecure'];
    $curl = curlinit($serverip, $serverhostname, $serverusername, $serverpassword, $serversecure);
    $result = full_query("SELECT `orderid`, `billingcycle`, `paymentmethod`, `nextduedate` FROM `tblhosting` WHERE `id` = " . (int) $serviceid . " LIMIT 1");
    $row = mysql_fetch_object($result);
    $orderid = $row->orderid;
    $billingcycle = $row->billingcycle;
    $paymentmethod = $row->paymentmethod;
    $nextduedate = $row->nextduedate . " 00:00:00";
    if( $billingcycle == "Free Account" )
    {
        $billing_cycle = '0';
    }
    else
    {
        if( $billingcycle == 'Quarterly' )
        {
            $billing_cycle = '3';
        }
        else
        {
            if( $billingcycle == 'Semi-Annually' )
            {
                $billing_cycle = '6';
            }
            else
            {
                if( $billingcycle == 'Annually' )
                {
                    $billing_cycle = '12';
                }
                else
                {
                    if( $billingcycle == 'Biennially' )
                    {
                        $billing_cycle = '24';
                    }
                    else
                    {
                        $billing_cycle = 1;
                    }
                }
            }
        }
    }
    if( $paymentmethod == 'tco' )
    {
        $payment_method = "Credit Card";
    }
    else
    {
        $payment_method = 'Free';
    }
    if( $clientsdetails['companyname'] )
    {
        $organization = "&organization=" . $clientsdetails['companyname'];
        $account_type = 1;
    }
    else
    {
        $account_type = 0;
        $organization = '';
    }
    $data = "action=createacct" . "&customer_id=" . $clientsdetails['userid'] . "&login_name=" . $clientsdetails['email'] . "&password=" . $password . "&first_name=" . $clientsdetails['firstname'] . "&last_name=" . $clientsdetails['lastname'] . $organization . "&address1=" . $clientsdetails['address1'] . "&address2=" . $clientsdetails['address2'] . "&city=" . $clientsdetails['city'] . "&state=" . $clientsdetails['state'] . "&postal_code=" . $clientsdetails['postcode'] . "&country=" . $clientsdetails['country'] . "&work_phone=" . $clientsdetails['phonenumber'] . "&email=" . $clientsdetails['email'] . "&account_type=" . $account_type . "&domain_name=" . $domain . "&package_id=" . $package_id . "&billing_cycle=" . $billing_cycle . "&paymentmethod=" . $payment_method . "&nextduedate=" . $nextduedate . "&account_id=" . $serviceid . "&order_id=" . $orderid . "&account_login_name=" . $username . "&account_password=" . $password;
    try
    {
        $response = sendrequest($curl, $data);
        $responseXml = parseresponse($response);
        foreach( $responseXml->xpath('/account/add/result') as $resultNode )
        {
            if( 'error' == (bool) $resultNode->status )
            {
                return (bool) $resultNode->errtext . "\n";
            }
            return 'success';
        }
    }
    catch( ApiRequestException $e )
    {
        return $e;
    }
}
function xpanel_TerminateAccount($params)
{
    $serviceid = $params['serviceid'];
    $serverip = $params['serverip'];
    $serverhostname = $params['serverhostname'];
    $serverusername = $params['serverusername'];
    $serverpassword = $params['serverpassword'];
    $serversecure = $params['serversecure'];
    $curl = curlinit($serverip, $serverhostname, $serverusername, $serverpassword, $serversecure);
    $data = "action=removeacct" . "&account_id=" . $serviceid;
    try
    {
        $response = sendrequest($curl, $data);
        $responseXml = parseresponse($response);
        foreach( $responseXml->xpath('/account/del/result') as $resultNode )
        {
            if( 'error' == (bool) $resultNode->status )
            {
                return (bool) $resultNode->errtext . "\n";
            }
            return 'success';
        }
    }
    catch( ApiRequestException $e )
    {
        return $e;
    }
}
function xpanel_SuspendAccount($params)
{
    $serviceid = $params['serviceid'];
    $serverip = $params['serverip'];
    $serverhostname = $params['serverhostname'];
    $serverusername = $params['serverusername'];
    $serverpassword = $params['serverpassword'];
    $serversecure = $params['serversecure'];
    $curl = curlinit($serverip, $serverhostname, $serverusername, $serverpassword, $serversecure);
    $data = "action=suspendacct" . "&account_id=" . $serviceid;
    try
    {
        $response = sendrequest($curl, $data);
        $responseXml = parseresponse($response);
        foreach( $responseXml->xpath('/account/suspend/result') as $resultNode )
        {
            if( 'error' == (bool) $resultNode->status )
            {
                return (bool) $resultNode->errtext . "\n";
            }
            return 'success';
        }
    }
    catch( ApiRequestException $e )
    {
        return $e;
    }
}
function xpanel_UnsuspendAccount($params)
{
    $serviceid = $params['serviceid'];
    $serverip = $params['serverip'];
    $serverhostname = $params['serverhostname'];
    $serverusername = $params['serverusername'];
    $serverpassword = $params['serverpassword'];
    $serversecure = $params['serversecure'];
    $curl = curlinit($serverip, $serverhostname, $serverusername, $serverpassword, $serversecure);
    $data = "action=unsuspendacct" . "&account_id=" . $serviceid;
    try
    {
        $response = sendrequest($curl, $data);
        $responseXml = parseresponse($response);
        foreach( $responseXml->xpath('/account/unsuspend/result') as $resultNode )
        {
            if( 'error' == (bool) $resultNode->status )
            {
                return (bool) $resultNode->errtext . "\n";
            }
            return 'success';
        }
    }
    catch( ApiRequestException $e )
    {
        return $e;
    }
}
function xpanel_ChangePassword($params)
{
    $serviceid = $params['serviceid'];
    $serverip = $params['serverip'];
    $serverhostname = $params['serverhostname'];
    $serverusername = $params['serverusername'];
    $serverpassword = $params['serverpassword'];
    $serversecure = $params['serversecure'];
    $username = $params['username'];
    $password = $params['password'];
    $curl = curlinit($serverip, $serverhostname, $serverusername, $serverpassword, $serversecure);
    $data = "action=passwd" . "&account_id=" . $serviceid . "&account_login_name=" . $username . "&account_password=" . $password;
    try
    {
        $response = sendrequest($curl, $data);
        $responseXml = parseresponse($response);
        foreach( $responseXml->xpath('/account/passwd/result') as $resultNode )
        {
            if( 'error' == (bool) $resultNode->status )
            {
                return (bool) $resultNode->errtext . "\n";
            }
            return 'success';
        }
    }
    catch( ApiRequestException $e )
    {
        return $e;
    }
}
function xpanel_ChangePackage($params)
{
    $serviceid = $params['serviceid'];
    $serverip = $params['serverip'];
    $serverhostname = $params['serverhostname'];
    $serverusername = $params['serverusername'];
    $serverpassword = $params['serverpassword'];
    $serversecure = $params['serversecure'];
    $package_id = $params['configoption2'];
    $curl = curlinit($serverip, $serverhostname, $serverusername, $serverpassword, $serversecure);
    $data = "action=changepackage" . "&account_id=" . $serviceid . "&package_id=" . $package_id;
    try
    {
        $response = sendrequest($curl, $data);
        $responseXml = parseresponse($response);
        foreach( $responseXml->xpath('/account/changepackage/result') as $resultNode )
        {
            if( 'error' == (bool) $resultNode->status )
            {
                return (bool) $resultNode->errtext . "\n";
            }
            return 'success';
        }
    }
    catch( ApiRequestException $e )
    {
        return $e;
    }
}
function xpanel_ClientArea($params)
{
    global $_LANG;
    $serverhostname = $params['serverhostname'];
    $serversecure = $params['serversecure'];
    $protocol = $serversecure ? 'https' : 'http';
    $port = $serversecure ? 3737 : 80;
    $script = "cgi-bin/xpanel/account_manager.cgi?a=log_in&privileges=account";
    $url = $protocol . "://" . $serverhostname . ":" . $port . '/' . $script;
    $form = sprintf("<form action=\"%s\" method=\"post\" target=\"_blank\">" . "<input type=\"hidden\" name=\"login_name\" value=\"%s\" />" . "<input type=\"hidden\" name=\"password\" value=\"%s\" />" . "<input type=\"submit\" value=\"%s\" />" . "<input type=\"button\" value=\"%s\" onclick=\"window.open('http://%s/webmail')\" />" . "</form>", WHMCS_Input_Sanitize::encode($url), WHMCS_Input_Sanitize::encode($params['username']), WHMCS_Input_Sanitize::encode($params['password']), $_LANG['xpanellogin'], $_LANG['xpanelmaillogin'], WHMCS_Input_Sanitize::encode($serverhostname));
    return $form;
}
function xpanel_AdminLink($params)
{
    $serverhostname = $params['serverhostname'];
    $serversecure = $params['serversecure'];
    $protocol = $serversecure ? 'https' : 'http';
    $port = $serversecure ? 3737 : 80;
    $script = "cgi-bin/xpanel/admin/index.cgi";
    $url = $protocol . "://" . $serverhostname . ":" . $port . '/' . $script;
    $form = sprintf("<form action=\"%s\" method=\"post\" target=\"_blank\">" . "<input type=\"hidden\" name=\"user\" value=\"%s\" />" . "<input type=\"hidden\" name=\"pass\" value=\"%s\" />" . "<input type=\"submit\" value=\"%s\" />" . "</form>", WHMCS_Input_Sanitize::encode($url), WHMCS_Input_Sanitize::encode($params['serverusername']), WHMCS_Input_Sanitize::encode($params['serverpassword']), 'XPanel');
    return $form;
}
function xpanel_LoginLink($params)
{
    $serverhostname = $params['serverhostname'];
    $serversecure = $params['serversecure'];
    $protocol = $serversecure ? 'https' : 'http';
    $port = $serversecure ? 3737 : 80;
    $script = "cgi-bin/xpanel/account_manager.cgi?a=log_in&privileges=account&login_link=1";
    $url = $protocol . "://" . $serverhostname . ":" . $port . '/' . $script;
    $form = sprintf("<a href=\"%s&amp;login_name=%s&amp;password=%s\" target=\"_blank\" class=\"moduleloginlink\">%s</a>", WHMCS_Input_Sanitize::encode($url), WHMCS_Input_Sanitize::encode($params['username']), WHMCS_Input_Sanitize::encode($params['password']), "login to control panel");
    return $form;
}