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
function whmsonic_ConfigOptions()
{
    $configarray = array( "Client Type" => array( 'Type' => 'dropdown', 'Options' => 'External,internal', 'Description' => "Notice: If Internal selected, please set the custom form field under the Custom Fields link, the client must enter cPanel username on the order page, that is also mean the client has already cPanel hosting account on the server. This option will not setup cPanel account! It will setup only radio under the provided cpanel user account." ), "Max Listeners Limit" => array( 'Type' => 'text', 'Size' => '3' ), "Max BitRate Limit" => array( 'Type' => 'dropdown', 'Options' => '64,128,24,32,48,96,192,384' ), "AutoDJ Feature" => array( 'Type' => 'yesno', 'Description' => "If yes, the user will access to AutoDJ features in their cPanel WHMSonic." ), "Hosting Space" => array( 'Type' => 'text', 'Size' => '25', 'Description' => "Hosting space is required by external clients only if autodj option is enabled to upload music files. Please enter a limit, ex: 100 = 100MB. Enter only numbers in this field." ), "Bandwidth Limit" => array( 'Type' => 'text', 'Size' => '25' ) );
    return $configarray;
}
function whmsonic_CreateAccount($params)
{
    $ctype = $params['configoption1'];
    $listeners = $params['configoption2'];
    $radioip = $params['serverip'];
    $bitrate = $params['configoption3'];
    $autodj = $params['configoption4'];
    $hspace = $params['configoption5'];
    $bandwidth = $params['configoption6'];
    $serverp = $params['serverpassword'];
    $connection = $params['serverip'];
    $auth = "root:" . $serverp;
    $orderid = $params['serviceid'];
    if( $params['serversecure'] == 'on' )
    {
        $serverport = '2087';
        $ht = 'https';
    }
    else
    {
        $serverport = '2086';
        $ht = 'http';
    }
    $client_email = $params['clientsdetails']['email'];
    $client_name = $params['clientsdetails']['firstname'];
    if( $params['configoption1'] == 'internal' )
    {
        $radiousername = $params['customfields']["cpanel username"];
    }
    else
    {
        $chars = 'abcdefghijkmnpqrstuvwxyz0123456789';
        srand((double) microtime() * 1000000);
        $i = 0;
        for( $exu = ''; $i <= 4; $i++ )
        {
            $num = rand() % 33;
            $tmp = substr($chars, $num, 1);
            $exu = $exu . $tmp;
        }
        $radiousername = 'sc_' . $exu;
    }
    $chars2 = 'abcdefghijkmnopqrstuvwxyz023456789';
    srand((double) microtime() * 1000000);
    $i = 0;
    for( $pass = ''; $i <= 7; $i++ )
    {
        $num = rand() % 33;
        $tmp = substr($chars2, $num, 1);
        $pass = $pass . $tmp;
    }
    update_query('tblhosting', array( 'username' => $radiousername, 'password' => $pass ), array( 'id' => (int) $params['serviceid'] ));
    $url = $ht . "://" . $connection . ":" . $serverport . "/whmsonic/modules/api.php?";
    $data = "cmd=setup&ctype=" . $ctype . "&ip=" . $radioip . "&bitrate=" . $bitrate . "&autodj=" . $autodj . "&bw=" . $bandwidth . "&semail=" . $wemail . "&limit=" . $listeners . "&cemail=" . $client_email . "&cname=" . $client_name . "&rad_username=" . $radiousername . "&pass=" . $pass . "&hspace=" . $hspace;
    return whmsonic_call($url, $auth, $data);
}
function whmsonic_TerminateAccount($params)
{
    $connection = $params['serverip'];
    $rad_username = $params['username'];
    $serverp = $params['serverpassword'];
    $auth = "root:" . $serverp;
    if( $params['serversecure'] == 'on' )
    {
        $serverport = '2087';
        $ht = 'https';
    }
    else
    {
        $serverport = '2086';
        $ht = 'http';
    }
    $url = $ht . "://" . $connection . ":" . $serverport . "/whmsonic/modules/api.php?";
    $data = "cmd=terminate&rad_username=" . $rad_username;
    return whmsonic_call($url, $auth, $data);
}
function whmsonic_SuspendAccount($params)
{
    $connection = $params['serverip'];
    $rad_username = $params['username'];
    $serverp = $params['serverpassword'];
    $auth = "root:" . $serverp;
    if( $params['serversecure'] == 'on' )
    {
        $serverport = '2087';
        $ht = 'https';
    }
    else
    {
        $serverport = '2086';
        $ht = 'http';
    }
    $url = $ht . "://" . $connection . ":" . $serverport . "/whmsonic/modules/api.php?";
    $data = "cmd=suspend&rad_username=" . $rad_username;
    return whmsonic_call($url, $auth, $data);
}
function whmsonic_UnsuspendAccount($params)
{
    $connection = $params['serverip'];
    $rad_username = $params['username'];
    $serverp = $params['serverpassword'];
    $auth = "root:" . $serverp;
    if( $params['serversecure'] == 'on' )
    {
        $serverport = '2087';
        $ht = 'https';
    }
    else
    {
        $serverport = '2086';
        $ht = 'http';
    }
    $url = $ht . "://" . $connection . ":" . $serverport . "/whmsonic/modules/api.php?";
    $data = "cmd=unsuspend&rad_username=" . $rad_username;
    return whmsonic_call($url, $auth, $data);
}
function whmsonic_ClientArea($params)
{
    global $_LANG;
    $form = sprintf("<form action=\"http://%s/cpanel/\" method=\"post\" target=\"_blank\">" . "<input type=\"hidden\" name=\"ip\" value=\"\" />" . "<input type=\"submit\" value=\"%s\" />" . "</form>", WHMCS_Input_Sanitize::encode($params['serverip']), $_LANG['whmsoniclogin']);
    return $form;
}
function whmsonic_AdminLink($params)
{
    $connection = $params['serverip'];
    if( $params['serversecure'] )
    {
        $serverport = '2087';
        $http = 'https';
    }
    else
    {
        $serverport = '2086';
        $http = 'http';
    }
    $form = sprintf("<form action=\"%s://%s:%s/whmsonic/main.php\" method=\"post\" target=\"_blank\">" . "<input type=\"hidden\" name=\"username\" value=\"%s\" />" . "<input type=\"hidden\" name=\"password\" value=\"%s\" />" . "<input type=\"submit\" value=\"%s\" />" . "</form>", $http, WHMCS_Input_Sanitize::encode($params['serverip']), $serverport, WHMCS_Input_Sanitize::encode($params['serverusername']), WHMCS_Input_Sanitize::encode($params['serverpassword']), "WHMSonic Login");
    return $form;
}
function whmsonic_call($url, $auth, $data)
{
    $ch = curl_init();
    curl_setopt($ch, CURLAUTH_BASIC, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_USERPWD, $auth);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 59);
    curl_setopt($ch, CURLOPT_URL, $url);
    $retval = curl_exec($ch);
    if( curl_errno($ch) )
    {
        $retval = "CURL Error: " . curl_errno($ch) . " - " . curl_error($ch) . " - Please check the radioIP in the package module configuration and check the root password in the servers settings of WHMCS.";
    }
    curl_close($ch);
    if( $retval == 'Complete' )
    {
        $result = 'success';
    }
    else
    {
        if( strpos($retval, "<title>WHM Login</title>") == true )
        {
            $result = "Login Failed. Please check root password configured for WHMSonic server.";
        }
        else
        {
            $result = $retval;
        }
    }
    return $result;
}