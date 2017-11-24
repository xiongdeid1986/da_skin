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
define('ADMINAREA', true);
require("../init.php");
session_regenerate_id();
$username = $whmcs->get_req_var('username');
$password = $whmcs->get_req_var('password');
$auth = new WHMCS_Auth();
$twofa = new WHMCS_2FA();
if( $twofa->isActiveAdmins() && isset($_SESSION['2faverify']) )
{
    $twofa->setAdminID($_SESSION['2faadminid']);
    if( WHMCS_Session::get('2fabackupcodenew') )
    {
        WHMCS_Session::delete('2fabackupcodenew');
        WHMCS_Session::delete('2faverify');
        WHMCS_Session::delete('2faadminid');
        WHMCS_Session::delete('2farememberme');
        if( WHMCS_Session::get('admloginurlredirect') )
        {
            $loginurlredirect = WHMCS_Session::get('admloginurlredirect');
            WHMCS_Session::delete('admloginurlredirect');
            $urlparts = explode("?", $loginurlredirect, 2);
            $filename = !empty($urlparts[0]) ? $urlparts[0] : '';
            $qry_string = !empty($urlparts[1]) ? $urlparts[1] : '';
            redir($qry_string, $filename);
        }
        else
        {
            redir('', "index.php");
        }
    }
    if( $whmcs->get_req_var('backupcode') )
    {
        $success = $twofa->verifyBackupCode($whmcs->get_req_var('code'));
    }
    else
    {
        $success = $twofa->moduleCall('verify');
    }
    if( $success )
    {
        $adminfound = $auth->getInfobyID($_SESSION['2faadminid']);
        $auth->setSessionVars();
        $auth->processLogin();
        if( $_SESSION['2farememberme'] )
        {
            $auth->setRememberMeCookie();
        }
        else
        {
            $auth->unsetRememberMeCookie();
        }
        if( $whmcs->get_req_var('backupcode') )
        {
            WHMCS_Session::set('2fabackupcodenew', true);
            redir("newbackupcode=1", "login.php");
        }
        WHMCS_Session::delete('2faverify');
        WHMCS_Session::delete('2faadminid');
        WHMCS_Session::delete('2farememberme');
        if( WHMCS_Session::get('admloginurlredirect') )
        {
            $loginurlredirect = WHMCS_Session::get('admloginurlredirect');
            WHMCS_Session::delete('admloginurlredirect');
            $urlparts = explode("?", $loginurlredirect, 2);
            $filename = !empty($urlparts[0]) ? $urlparts[0] : '';
            $qry_string = !empty($urlparts[1]) ? $urlparts[1] : '';
            redir($qry_string, $filename);
        }
        else
        {
            redir('', "index.php");
        }
    }
    redir(($whmcs->get_req_var('backupcode') ? "backupcode=1&" : '') . "incorrect=1", "login.php");
}
if( !trim($username) || !trim($password) )
{
    $auth->failedLogin();
    redir("incorrect=1", "login.php");
}
$adminfound = $auth->getInfobyUsername($username);
if( $adminfound && $auth->comparePassword($password) )
{
    if( $whmcs->get_req_var('language') )
    {
        $_SESSION['adminlang'] = $whmcs->get_req_var('language');
    }
    try
    {
        $hasher = new WHMCS_Security_Hash_Password();
        if( $auth->isAdminPWHashSet() )
        {
            if( $hasher->needsRehash($auth->getAdminPWHash()) )
            {
                $auth->generateNewPasswordHashAndStore($password);
            }
        }
        else
        {
            if( $auth->generateNewPasswordHashAndStore($password) )
            {
                $auth->generateNewPasswordHashAndStoreForApi(md5($password));
            }
        }
    }
    catch( Exception $e )
    {
        logActivity("Failed to validate password rehash: " . $e->getMessage());
    }
    if( $twofa->isActiveAdmins() && $auth->isTwoFactor() )
    {
        $_SESSION['2faverify'] = true;
        $_SESSION['2faadminid'] = $auth->getAdminID();
        $_SESSION['2farememberme'] = $whmcs->get_req_var('rememberme');
        redir('', "login.php");
    }
    $auth->setSessionVars();
    if( $whmcs->get_req_var('rememberme') )
    {
        $auth->setRememberMeCookie();
    }
    else
    {
        $auth->unsetRememberMeCookie();
    }
    $auth->processLogin();
    if( WHMCS_Session::get('admloginurlredirect') )
    {
        $loginurlredirect = WHMCS_Session::get('admloginurlredirect');
        WHMCS_Session::delete('admloginurlredirect');
        $urlparts = explode("?", $loginurlredirect, 2);
        $filename = !empty($urlparts[0]) ? $urlparts[0] : '';
        $qry_string = !empty($urlparts[1]) ? $urlparts[1] : '';
        redir($qry_string, $filename);
    }
    else
    {
        redir('', "index.php");
    }
}
$auth->failedLogin();
redir("incorrect=1", "login.php");