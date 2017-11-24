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
require("includes/clientfunctions.php");
$pagetitle = $_LANG['pwreset'];
$breadcrumbnav = "<a href=\"index.php\">" . $_LANG['globalsystemname'] . "</a> > <a href=\"clientarea.php\">" . $_LANG['clientareatitle'] . "</a> > <a href=\"pwreset.php\">" . $_LANG['pwreset'] . "</a>";
initialiseClientArea($pagetitle, '', $breadcrumbnav);
$securityquestion = '';
$action = $whmcs->get_req_var('action');
$email = $whmcs->get_req_var('email');
$answer = $whmcs->get_req_var('answer');
$key = $whmcs->get_req_var('key');
$success = $whmcs->get_req_var('success');
$smartyvalues['action'] = $action;
$smartyvalues['email'] = $email;
$smartyvalues['key'] = $key;
$smartyvalues['answer'] = $answer;
if( $action == 'reset' )
{
    check_token();
    $templatefile = 'pwreset';
    $errormessage = doResetPWEmail($email, $answer);
    if( $securityquestion )
    {
        $smartyvalues['securityquestion'] = $securityquestion;
    }
    if( $errormessage )
    {
        $smartyvalues['errormessage'] = $errormessage;
    }
    else
    {
        if( !$securityquestion || $securityquestion && $answer )
        {
            $smartyvalues['success'] = true;
        }
    }
}
else
{
    if( $key )
    {
        $invalidlink = doResetPWKeyCheck($key);
        if( $newpw && !$invalidlink )
        {
            $errormessage = doResetPW($key, $newpw, $confirmpw);
            if( !$errormessage )
            {
                $smartyvalues['success'] = true;
            }
        }
        $smartyvalues['invalidlink'] = $invalidlink;
        $smartyvalues['errormessage'] = $errormessage;
        $templatefile = 'pwresetvalidation';
    }
    else
    {
        if( $success )
        {
            $smartyvalues['success'] = true;
            $templatefile = 'pwresetvalidation';
        }
        else
        {
            $templatefile = 'pwreset';
        }
    }
}
outputClientArea($templatefile);