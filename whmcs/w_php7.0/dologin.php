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
define('CLIENTAREA', true);
require("init.php");
include("includes/clientfunctions.php");
$username = trim($whmcs->get_req_var('username'));
$password = trim($whmcs->get_req_var('password'));
$hash = $whmcs->get_req_var('hash');
$goto = $whmcs->get_req_var('goto');
$gotourl = '';
if( $goto )
{
    $goto = trim($goto);
    if( substr($goto, 0, 7) == "http://" || substr($goto, 0, 8) == "https://" )
    {
        $goto = '';
    }
    $gotourl = html_entity_decode($goto);
}
else
{
    if( isset($_SESSION['loginurlredirect']) )
    {
        $gotourl = $_SESSION['loginurlredirect'];
        if( substr($gotourl, 0 - 15) == "&incorrect=true" || substr($gotourl, 0 - 15) == "?incorrect=true" )
        {
            $gotourl = substr($gotourl, 0, strlen($gotourl) - 15);
        }
        if( substr($gotourl, 0 - 28) == "&incorrect=true&backupcode=1" || substr($gotourl, 0 - 28) == "?incorrect=true&backupcode=1" || substr($gotourl, 0 - 28) == "&backupcode=1&incorrect=true" || substr($gotourl, 0 - 28) == "?backupcode=1&incorrect=true" )
        {
            $gotourl = substr($gotourl, 0, strlen($gotourl) - 28);
        }
        unset($_SESSION['loginurlredirect']);
    }
}
if( !$gotourl )
{
    $gotourl = "clientarea.php";
}
if( $whmcs->get_req_var('newbackupcode') )
{
    if( isset($_SESSION['2fafromcart']) )
    {
        unset($_SESSION['2fafromcart']);
        redir("a=checkout", "cart.php");
    }
    header("Location: " . $gotourl);
    exit( dirname(__FILE__) . " | line".__LINE__ );
}
$loginsuccess = $istwofa = false;
$twofa = new WHMCS_2FA();
if( $twofa->isActiveClients() && isset($_SESSION['2faverifyc']) )
{
    $twofa->setClientID($_SESSION['2faclientid']);
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
        validateClientLogin(get_query_val('tblclients', 'email', array( 'id' => $_SESSION['2faclientid'] )), '', true);
        if( $_SESSION['2farememberme'] )
        {
            WHMCS_Cookie::set('User', $_SESSION['uid'] . ":" . sha1($_SESSION['upw'] . $whmcs->get_hash()), time() + 60 * 60 * 24 * 365);
        }
        else
        {
            WHMCS_Cookie::delete('User');
        }
        WHMCS_Session::delete('2faclientid');
        WHMCS_Session::delete('2farememberme');
        WHMCS_Session::delete('2faverifyc');
        if( $whmcs->get_req_var('backupcode') )
        {
            WHMCS_Session::set('2fabackupcodenew', true);
            $gotourl = "clientarea.php?newbackupcode=true";
            header("Location: " . $gotourl);
            exit( dirname(__FILE__) . " | line".__LINE__ );
        }
        $loginsuccess = true;
    }
    else
    {
        if( strpos($gotourl, "?") )
        {
            $gotourl .= "&";
        }
        else
        {
            $gotourl .= "?";
        }
        $gotourl .= "incorrect=true";
        header("Location: " . $gotourl);
        exit( dirname(__FILE__) . " | line".__LINE__ );
    }
}
if( !$loginsuccess )
{
    if( validateClientLogin($username, $password) )
    {
        $loginsuccess = true;
        if( $rememberme )
        {
            WHMCS_Cookie::set('User', $_SESSION['uid'] . ":" . sha1($_SESSION['upw'] . $whmcs->get_hash()), time() + 60 * 60 * 24 * 365);
        }
        else
        {
            WHMCS_Cookie::delete('User');
        }
    }
    else
    {
        if( isset($_SESSION['2faverifyc']) )
        {
            $istwofa = true;
        }
        else
        {
            if( $hash )
            {
                $autoauthkey = '';
                require("configuration.php");
                if( $autoauthkey )
                {
                    $login_uid = $login_cid = '';
                    if( $timestamp < time() - 15 * 60 || time() < $timestamp )
                    {
                        exit( "Link expired" );
                    }
                    $hashverify = sha1($email . $timestamp . $autoauthkey);
                    if( $hashverify == $hash )
                    {
                        $result = select_query('tblclients', 'id,password,language', array( 'email' => $email, 'status' => array( 'sqltype' => 'NEQ', 'value' => 'Closed' ) ));
                        $data = mysqli_fetch_array($result);
                        $login_uid = $data['id'];
                        $login_pwd = $data['password'];
                        $language = $data['language'];
                        if( !$login_uid )
                        {
                            $result = select_query('tblcontacts', 'id,userid,password', array( 'email' => $email, 'subaccount' => '1', 'password' => array( 'sqltype' => 'NEQ', 'value' => '' ) ));
                            $data = mysqli_fetch_array($result);
                            $login_cid = $data['id'];
                            $login_uid = $data['userid'];
                            $login_pwd = $data['password'];
                            $result = select_query('tblclients', 'id,language', array( 'id' => $login_uid, 'status' => array( 'sqltype' => 'NEQ', 'value' => 'Closed' ) ));
                            $data = mysqli_fetch_array($result);
                            $login_uid = $data['id'];
                            $language = $data['language'];
                        }
                        if( $login_uid )
                        {
                            $fullhost = gethostbyaddr($remote_ip);
                            update_query('tblclients', array( 'lastlogin' => "now()", 'ip' => $remote_ip, 'host' => $fullhost ), array( 'id' => $login_uid ));
                            $_SESSION['uid'] = $login_uid;
                            if( $login_cid )
                            {
                                $_SESSION['cid'] = $login_cid;
                            }
                            $haship = $CONFIG['DisableSessionIPCheck'] ? '' : WHMCS_Utility_Environment_CurrentUser::getip();
                            $_SESSION['upw'] = sha1($login_uid . $login_cid . $login_pwd . $haship . substr(sha1($whmcs->get_hash()), 0, 20));
                            $_SESSION['tkval'] = genRandomVal();
                            if( $language )
                            {
                                $_SESSION['Language'] = $language;
                            }
                            run_hook('ClientLogin', array( 'userid' => $login_uid ));
                            $loginsuccess = true;
                        }
                    }
                }
            }
        }
    }
}
if( !$istwofa && !$loginsuccess )
{
    if( strpos($gotourl, "?") )
    {
        $gotourl .= "&incorrect=true";
    }
    else
    {
        $gotourl .= "?incorrect=true";
    }
}
if( $loginsuccess && isset($_SESSION['2fafromcart']) )
{
    unset($_SESSION['2fafromcart']);
    redir("a=checkout", "cart.php");
}
header("Location: " . $gotourl);
exit( dirname(__FILE__) . " | line".__LINE__ );