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
$silent = 'true';
require("../init.php");
require("adminfunctions.php");
define('APICALL', true);
$userProvidedUsername = $whmcs->get_req_var('username');
$userProvidedPassword = $whmcs->get_req_var('password');
$incomingAccessKey = $whmcs->get_req_var('accesskey');
$incomingAction = $whmcs->get_req_var('action');
$userProvidedResponseType = $whmcs->get_req_var('responsetype');
$httpRequestProtocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : "HTTP/1.1";
$responsetype = $userProvidedResponseType;
$action = preg_replace("/[^0-9a-z]/i", '', strtolower($incomingAction));
$apiresults = array(  );
$allowed = true;
$api_access_key = $whmcsAppConfig['api_access_key'];
$api_enable_logging = $whmcsAppConfig['api_enable_logging'];
if( $whmcs->isVisitorIPBanned() )
{
    $apiresults = array( 'result' => 'error', 'message' => "IP Banned" );
    $allowed = false;
}
if( $allowed )
{
    if( $incomingAccessKey && $api_access_key )
    {
        if( $incomingAccessKey != $api_access_key )
        {
            $apiresults = array( 'result' => 'error', 'message' => "Invalid Access Key" );
            $allowed = false;
        }
    }
    else
    {
        $apiallowedips = $whmcs->get_config('APIAllowedIPs');
        $apiallowedips = unserialize($apiallowedips);
        $allowedips = array(  );
        foreach( $apiallowedips as $allowedip )
        {
            if( 0 < strlen(trim($allowedip['ip'])) )
            {
                $allowedips[] = $allowedip['ip'];
            }
        }
        if( !in_array($remote_ip, $allowedips) )
        {
            $apiresults = array( 'result' => 'error', 'message' => "Invalid IP " . $remote_ip );
            $allowed = false;
        }
    }
}
if( !$allowed )
{
    header($httpRequestProtocol . " 403 Forbidden");
}
$validPasswordProvided = false;
if( $allowed )
{
    $hasher = new WHMCS_Security_Hash_Password();
    try
    {
        $info = $hasher->getInfo($userProvidedPassword);
        if( $info['algoName'] == WHMCS_Security_Hash_Password::HASH_MD5 )
        {
            $validPasswordFormatProvided = true;
        }
    }
    catch( Exception $e )
    {
        logActivity("Unable to inspect user provided API password");
    }
}
if( $validPasswordFormatProvided )
{
    $adminAuth = new WHMCS_Auth();
    if( $adminAuth->getInfobyUsername($userProvidedUsername, false) )
    {
        if( !$adminAuth->isActive() )
        {
            $apiresults = array( 'result' => 'error', 'message' => "Administrator Account Disabled" );
            $allowed = false;
        }
        $verifiedPassword = false;
        $verifiedPassword = $adminAuth->compareApiPassword($userProvidedPassword);
        if( $verifiedPassword )
        {
            $adminAuth->setSessionVars();
            if( checkPermission("API Access", true) )
            {
                $createLogEntry = $whmcs->get_config('LogAPIAuthentication') ? true : false;
                $adminAuth->processLogin($createLogEntry);
                $needsRehash = false;
                try
                {
                    $needsRehash = $hasher->needsRehash($adminAuth->getLegacyAdminPW());
                }
                catch( Exception $e )
                {
                    logActivity("Failed to validate password rehash: " . $e->getMessage());
                }
                if( $needsRehash )
                {
                    $adminAuth->generateNewPasswordHashAndStoreForApi($userProvidedPassword);
                }
            }
            else
            {
                $adminAuth->logout();
                $apiresults = array( 'result' => 'error', 'message' => "Access Denied" );
                $allowed = false;
            }
        }
        else
        {
            $adminAuth->failedLogin();
            $apiresults = array( 'result' => 'error', 'message' => "Authentication Failed" );
            $allowed = false;
        }
    }
    else
    {
        $adminAuth->failedLogin();
        $apiresults = array( 'result' => 'error', 'message' => "Authentication Failed" );
        $allowed = false;
    }
    if( !$allowed )
    {
        header($httpRequestProtocol . " 403 Forbidden");
    }
    if( $allowed )
    {
        if( isValidforPath($action) )
        {
            switch( $action )
            {
                case 'adduser':
                    $action = 'addclient';
                    break;
                case 'getclientsdata':
                    break;
                case 'getclientsdatabyemail':
                    $action = 'getclientsdetails';
            }
            $apiFilePath = ROOTDIR . '/includes/api/' . $action . ".php";
            if( file_exists($apiFilePath) )
            {
                include($apiFilePath);
            }
            else
            {
                $apiresults = array( 'result' => 'error', 'message' => "Command Not Found" );
            }
        }
        else
        {
            $apiresults = array( 'result' => 'error', 'message' => "Invalid API Command Value" );
        }
    }
}
else
{
    if( $allowed )
    {
        $apiresults = array( 'result' => 'error', 'message' => "Authentication Failed" );
        $allowed = false;
        header($httpRequestProtocol . " 403 Forbidden");
    }
}
$responseType = $userProvidedResponseType;
if( $responseType != $responsetype && $responseType != 'xml' && $responseType != 'json' )
{
    $responseType = 'xml';
}
ob_start();
if( count($apiresults) )
{
    if( $responseType == 'json' )
    {
        echo json_encode($apiresults);
    }
    else
    {
        if( $responseType == 'xml' )
        {
            $charset = $whmcs->get_config('Charset');
            $version = $allowed ? $whmcs->getVersion()->getCasual() : '';
            echo "<?xml version=\"1.0\" encoding=\"" . $charset . "\"?>\n" . "<whmcsapi version=\"" . $version . "\">\n" . "<action>" . $action . "</action>\n" . apixmloutput($apiresults) . "</whmcsapi>";
        }
        else
        {
            if( $responseType )
            {
                exit( "result=error;message=This API function can only return XML response format;" );
            }
            foreach( $apiresults as $k => $v )
            {
                echo $k . "=" . $v . ';';
            }
        }
    }
}
$apiOutput = ob_get_contents();
ob_end_clean();
echo $apiOutput;
if( $api_enable_logging )
{
    $fh = fopen("apilog.txt", 'a');
    $stringData = "\nDate: " . date("Y-m-d H:i:s") . "\n\n";
    $stringData .= "Request: " . print_r($_REQUEST, true) . "\n\n";
    $stringData .= "Response: " . $apiOutput . "\n----------------------";
    fwrite($fh, $stringData);
    fclose($fh);
}
function apiXMLOutput($val, $lastk = '')
{
    $output = '';
    foreach( $val as $k => $v )
    {
        if( is_array($v) )
        {
            if( is_numeric($k) )
            {
                $output .= "<" . $lastk . ">\n";
            }
            else
            {
                if( !is_numeric(key($v)) && count($v) )
                {
                    $output .= "<" . $k . ">\n";
                }
            }
            $output .= apiXMLOutput($v, $k);
            if( is_numeric($k) )
            {
                $output .= "</" . $lastk . ">\n";
            }
            else
            {
                if( !is_numeric(key($v)) && count($v) )
                {
                    $output .= "</" . $k . ">\n";
                }
            }
        }
        else
        {
            $v = WHMCS_Input_Sanitize::decode($v);
            if( strpos($v, "<![CDATA[") === false && htmlspecialchars($v) != $v )
            {
                $v = "<![CDATA[" . $v . "]" . "]>";
            }
            $output .= "<" . $k . ">" . $v . "</" . $k . ">\n";
        }
    }
    return $output;
}