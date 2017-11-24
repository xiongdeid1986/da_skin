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
function websitepanel_ConfigOptions()
{
    $configarray = array( "Package Name" => array( 'Type' => 'text', 'Size' => '25' ), "Web Space Quota" => array( 'Type' => 'text', 'Size' => '5', 'Description' => 'MB' ), "Bandwidth Limit" => array( 'Type' => 'text', 'Size' => '5', 'Description' => 'MB' ), 'PlanID' => array( 'Type' => 'text', 'Size' => '3', 'Description' => " *DNP Hosting Plan ID" ), "Parent SpaceId" => array( 'Type' => 'text', 'Size' => '3', 'Description' => "* SpaceID that all accounts are created under" ), "Enterprise Server Port" => array( 'Type' => 'text', 'Size' => '5', 'Description' => "* Required" ), "Different Potal URL" => array( 'Type' => 'yesno', 'Description' => "Tick if portal address is different to server address" ), "Portal URL" => array( 'Type' => 'text', 'Size' => '25', 'Description' => "Portal URL, with http://, no trailing slash" ), "Send DNP Account Summary email" => array( 'Type' => 'yesno', 'Description' => "Tick to send DNP Account Summary" ), "Send DNP Hosting Space Summary email" => array( 'Type' => 'yesno', 'Description' => "Tick to send Hosting Space Summary" ), "Create Mail account" => array( 'Type' => 'yesno', 'Description' => "Tick to create mail account" ), "Create FTP account" => array( 'Type' => 'yesno', 'Description' => "Tick to create FTP account" ), "Temporary domain" => array( 'Type' => 'yesno', 'Description' => "Tick to create a temp domain" ), "HTML email" => array( 'Type' => 'yesno', 'Description' => "Tick enable HTML email from DNP" ), "Create Website" => array( 'Type' => 'yesno', 'Description' => "Tick to create Website" ), "Count Bandwidth/Diskspace" => array( 'Type' => 'yesno', 'Description' => "Tick to update diskpace/bandwidth in WHMCS" ) );
    return $configarray;
}
function websitepanel_CreateAccount($params)
{
    $serverip = $params['serverip'];
    $serverusername = $params['serverusername'];
    $serverpassword = $params['serverpassword'];
    $secure = $params['serversecure'];
    $domain = $params['domain'];
    $packagetype = $params['type'];
    $username = $params['username'];
    $password = $params['password'];
    $accountid = $params['accountid'];
    $packageid = $params['packageid'];
    $clientsdetails = $params['clientsdetails'];
    $planId = $params['configoption4'];
    $parentPackageId = $params['configoption5'];
    $esport = $params['configoption6'];
    if( !class_exists('SoapClient') )
    {
        return "SOAP is missing. Please recompile PHP with the SOAP module included.";
    }
    if( $params['configoption11'] == 'on' )
    {
        $createMailAccount = true;
    }
    else
    {
        $createMailAccount = false;
    }
    if( $params['configoption9'] == 'on' )
    {
        $sendAccountLetter = true;
    }
    else
    {
        $sendAccountLetter = false;
    }
    if( $params['configoption10'] == 'on' )
    {
        $sendPackageLetter = true;
    }
    else
    {
        $sendPackageLetter = false;
    }
    if( $params['configoption13'] == 'on' )
    {
        $tempDomain = true;
    }
    else
    {
        $tempDomain = false;
    }
    if( $params['configoption12'] == 'on' )
    {
        $createFtpAccount = true;
    }
    else
    {
        $createFtpAccount = false;
    }
    if( $params['configoption14'] == 'on' )
    {
        $htmlMail = true;
    }
    else
    {
        $htmlMail = false;
    }
    if( $params['configoption15'] == 'on' )
    {
        $website = true;
    }
    else
    {
        $website = false;
    }
    if( $packagetype == 'reselleraccount' )
    {
        $roleid = 2;
    }
    else
    {
        $roleid = 3;
    }
    $param = array( 'parentPackageId' => $parentPackageId, 'username' => $username, 'password' => $password, 'roleId' => $roleid, 'firstName' => $clientsdetails['firstname'], 'lastName' => $clientsdetails['lastname'], 'email' => $clientsdetails['email'], 'htmlMail' => $htmlMail, 'sendAccountLetter' => $sendAccountLetter, 'createPackage' => true, 'planId' => $planId, 'sendPackageLetter' => $sendPackageLetter, 'domainName' => $domain, 'tempDomain' => $tempDomain, 'createWebSite' => $website, 'createFtpAccount' => $createFtpAccount, 'ftpAccountName' => $username, 'createMailAccount' => $createMailAccount );
    $result = websitepanel_call($params, 'CreateUserWizard', $param);
    return $result;
}
function websitepanel_call($params, $func, $param, $retdata = '')
{
    $wsdlfile = 'esusers';
    if( $func == 'CreateUserWizard' || $func == 'GetMyPackages' || $func == 'UpdatePackageLiteral' || $func == 'GetPackageBandwidth' || $func == 'GetPackageDiskspace' )
    {
        $wsdlfile = 'espackages';
    }
    $http = $params['serversecure'] ? 'https' : 'http';
    $serverip = $params['serverip'];
    $serverusername = $params['serverusername'];
    $serverpassword = $params['serverpassword'];
    $esport = $params['configoption6'];
    $soapaddress = $http . "://" . $serverip . ":" . $esport . '/' . $wsdlfile . ".asmx?WSDL";
    try
    {
        $client = new SoapClient($soapaddress, array( 'login' => $serverusername, 'password' => $serverpassword ));
        $result = (array) $client->$func($param);
    }
    catch( Exception $e )
    {
        logModuleCall('websitepanel', $func, $param, $e->getMessage());
        return "Caught exception: " . $e->getMessage();
    }
    logModuleCall('websitepanel', $func, $param, $result);
    if( $retdata )
    {
        return $result[$func . 'Result'];
    }
    if( is_soap_fault($result) )
    {
        return "SOAP Fault Code: " . $result->faultcode . " - Error: " . $result->faultstring;
    }
    $returnCode = $result[$func . 'Result'];
    if( 0 <= $returnCode )
    {
        return 'success';
    }
    if( $returnCode == '-1100' )
    {
        return "User account with the specified username already exists on the server";
    }
    if( $returnCode == '-700' )
    {
        return "Specified mail domain already exists on the service";
    }
    if( $returnCode == '-701' )
    {
        return "Mail resource is unavailable for the selected hosting space";
    }
    if( $returnCode == '-502' )
    {
        return "Specified domain already exists";
    }
    if( $returnCode == '-301' )
    {
        return "The hosting space could not be deleted because it has child spaces";
    }
    return "WebsitePanel API Error Code: " . $returnCode;
}
function websitepanel_TerminateAccount($params)
{
    $wspuserid = websitepanel_getuserid($params);
    if( !$wspuserid )
    {
        return "Username '" . $params['username'] . "' not found in WebsitePanel";
    }
    $param = array( 'userId' => $wspuserid );
    $result = websitepanel_call($params, 'DeleteUser', $param);
    return $result;
}
function websitepanel_SuspendAccount($params)
{
    $wspuserid = websitepanel_getuserid($params);
    if( !$wspuserid )
    {
        return "Username '" . $params['username'] . "' not found in WebsitePanel";
    }
    $param = array( 'userId' => $wspuserid, 'status' => 'Suspended' );
    $result = websitepanel_call($params, 'ChangeUserStatus', $param);
    return $result;
}
function websitepanel_UnsuspendAccount($params)
{
    $wspuserid = websitepanel_getuserid($params);
    if( !$wspuserid )
    {
        return "Username '" . $params['username'] . "' not found in WebsitePanel";
    }
    $param = array( 'userId' => $wspuserid, 'status' => 'Active' );
    $result = websitepanel_call($params, 'ChangeUserStatus', $param);
    return $result;
}
function websitepanel_ChangePassword($params)
{
    $wspuserid = websitepanel_getuserid($params);
    if( !$wspuserid )
    {
        return "Username '" . $params['username'] . "' not found in WebsitePanel";
    }
    $param = array( 'userId' => $wspuserid, 'password' => $params['password'] );
    $result = websitepanel_call($params, 'ChangeUserPassword', $param);
    return $result;
}
function websitepanel_ChangePackage($params)
{
    $wspuserid = websitepanel_getuserid($params);
    if( !$wspuserid )
    {
        return "Username '" . $params['username'] . "' not found in WebsitePanel";
    }
    $param = array( 'packageId' => websitepanel_getpackageid($params, $wspuserid), 'statusId' => 1, 'planId' => $params['configoption4'], 'purchaseDate' => date('c'), 'packageName' => $params['configoption1'], 'packageComments' => '' );
    $result = websitepanel_call($params, 'UpdatePackageLiteral', $param);
    return $result;
}
function websitepanel_ClientArea($params)
{
    global $_LANG;
    $username = $params['username'];
    $url = $params['configoption7'];
    $urladdress = $params['configoption8'];
    if( !$url )
    {
        $domain = $params['serverhostname'] ? $params['serverhostname'] : $params['serverip'];
        $urladdress = ($params['serversecure'] ? 'https' : 'http') . "://" . $domain;
    }
    $form = sprintf("<form method=\"post\" action=\"%s/Default.aspx\" target=\"_blank\">" . "<input type=\"hidden\" name=\"pid\" value=\"Login\" />" . "<input type=\"hidden\" name=\"user\" value=\"%s\" />" . "<input type=\"hidden\" name=\"password\" value=\"%s\" />" . "<input type=\"submit\" value=\"%s\" />" . "</form>", WHMCS_Input_Sanitize::encode($urladdress), WHMCS_Input_Sanitize::encode($params['username']), WHMCS_Input_Sanitize::encode($params['password']), $_LANG['websitepanellogin']);
    return $form;
}
function websitepanel_AdminLink($params)
{
    $serverip = $params['serverip'];
    $serveridquery = select_query('tblservers', 'id', array( 'ipaddress' => $serverip ));
    $serveridqueryresult = mysql_fetch_array($serveridquery);
    $serverid = $serveridqueryresult['id'];
    $query = full_query("SELECT configoption7,configoption8 FROM tblproducts WHERE id = (SELECT packageid FROM tblhosting where server = " . (int) $serverid . " limit 1) AND servertype = 'websitepanel'");
    $queryresult = mysql_fetch_array($query);
    $url = $queryresult['configoption7'];
    $urladdress = $queryresult['configoption8'];
    if( !$url )
    {
        $domain = $params['serverhostname'] ? $params['serverhostname'] : $params['serverip'];
        $urladdress = ($params['serversecure'] ? 'https' : 'http') . "://" . $domain;
    }
    $form = sprintf("<form method=\"post\" action=\"%s/Default.aspx\" target=\"_blank\">" . "<input type=\"hidden\" name=\"pid\" value=\"Login\" />" . "<input type=\"hidden\" name=\"user\" value=\"%s\" />" . "<input type=\"hidden\" name=\"password\" value=\"%s\" />" . "<input type=\"submit\" value=\"%s\" />" . "</form>", WHMCS_Input_Sanitize::encode($urladdress), WHMCS_Input_Sanitize::encode($params['serverusername']), WHMCS_Input_Sanitize::encode($params['serverpassword']), "Login to Control Panel");
    return $form;
}
function websitepanel_LoginLink($params)
{
    $pid = $params['pid'];
    $username = $params['username'];
    $url = $params['configoption7'];
    $urladdress = $params['configoption8'];
    if( !$url )
    {
        $domain = $params['serverhostname'] ? $params['serverhostname'] : $params['serverip'];
        $urladdress = ($params['serversecure'] ? 'https' : 'http') . "://" . $domain;
    }
    $code = sprintf("<a href=\"%s/Default.aspx?pid=Login&user=%s&password=%s\" target=\"_blank\" class=\"moduleloginlink\">%s</a>", WHMCS_Input_Sanitize::encode($urladdress), WHMCS_Input_Sanitize::encode($params['username']), WHMCS_Input_Sanitize::encode($params['password']), "login to control panel");
    return $code;
}
function websitepanel_getuserid($params)
{
    $param = array( 'username' => $params['username'] );
    $result = websitepanel_call($params, 'GetUserByUsername', $param, true);
    return $result->UserId;
}
function websitepanel_getpackageid($params, $user)
{
    $param = array( 'userId' => $user );
    $result = websitepanel_call($params, 'GetMyPackages', $param, true);
    return $result->PackageInfo->PackageId;
}
function websitepanel_UsageUpdate($params)
{
    $serverid = $params['serverid'];
    $serverip = $params['serverip'];
    $serverusername = $params['serverusername'];
    $serverpassword = $params['serverpassword'];
    $query = full_query("SELECT username,packageid,regdate FROM tblhosting WHERE server=" . (int) $serverid . " AND domainstatus IN ('Active','Suspended')");
    while( $row = mysql_fetch_array($query) )
    {
        try
        {
            $username = $row['username'];
            $whmcspackageID = $row['packageid'];
            $packagequery = full_query("SELECT configoption2,configoption3,configoption6,configoption16 FROM tblproducts where id = " . (int) $whmcspackageID);
            $packagequeryresult = mysql_fetch_array($packagequery);
            if( $packagequeryresult['configoption16'] == 'on' )
            {
                $esport = $packagequeryresult['configoption6'];
                $dslimit = $packagequeryresult['configoption2'];
                $bwlimit = $packagequeryresult['configoption3'];
                $params['configoption6'] = $esport;
                $params['username'] = $username;
                $userID = websitepanel_getuserid($params);
                $packageID = websitepanel_getpackageid($params, $userID);
                $startDate = websitepanel_calculateDate($row['regdate']);
                $bandwidth = websitepanel_getBandwidth($params, $packageID, $startDate);
                $diskspace = websitepanel_getDiskspace($params, $packageID);
                update_query('tblhosting', array( 'diskusage' => $diskspace, 'disklimit' => $dslimit, 'bwusage' => $bandwidth, 'bwlimit' => $bwlimit, 'lastupdate' => "now()" ), array( 'server' => $params['serverid'], 'username' => $username ));
                full_query($updatequery);
            }
        }
        catch( Exception $e )
        {
        }
    }
}
function websitepanel_getBandwidth($params, $packageID, $startDate)
{
    $param = array( 'packageId' => $packageID, 'startDate' => $startDate, 'endDate' => date('Y-m-d', time()) );
    $result = websitepanel_call($params, 'GetPackageBandwidth', $param, true);
    $xml = simplexml_load_string($result->any);
    $total = 0;
    foreach( $xml->NewDataSet->Table as $Table )
    {
        $total = $total + $Table->MegaBytesTotal;
    }
    return $total;
}
function websitepanel_getDiskspace($params, $packageID)
{
    $param = array( 'packageId' => $packageID );
    $result = websitepanel_call($params, 'GetPackageDiskspace', $param, true);
    $xml = simplexml_load_string($result->any);
    $total = 0;
    foreach( $xml->NewDataSet->Table as $Table )
    {
        $total = $total + $Table->Diskspace;
    }
    return $total;
}
function websitepanel_calculateDate($date)
{
    $dateexplode = explode('-', $date);
    $currentyear = date('Y');
    $currentmonth = date('m');
    $newdate = $currentyear . '-' . $currentmonth . '-' . $dateexplode[2];
    $dateDiff = time() - strtotime("+1 hour", strtotime($newdate));
    $fullDays = floor($dateDiff / (60 * 60 * 24));
    if( $fullDays < 0 )
    {
        return date('Y-m-d', strtotime("-1 month", strtotime($newdate)));
    }
    return $newdate;
}