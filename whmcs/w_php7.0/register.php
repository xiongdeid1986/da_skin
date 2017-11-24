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
require("includes/customfieldfunctions.php");
if( isset($_SESSION['uid']) )
{
    redir('', "clientarea.php");
}
$capatacha = clientAreaInitCaptcha();
$securityquestions = getSecurityQuestions();
$firstname = $whmcs->get_req_var('firstname');
$lastname = $whmcs->get_req_var('lastname');
$companyname = $whmcs->get_req_var('companyname');
$email = $whmcs->get_req_var('email');
$address1 = $whmcs->get_req_var('address1');
$address2 = $whmcs->get_req_var('address2');
$city = $whmcs->get_req_var('city');
$state = $whmcs->get_req_var('state');
$postcode = $whmcs->get_req_var('postcode');
$country = $whmcs->get_req_var('country');
$phonenumber = $whmcs->get_req_var('phonenumber');
$password = $whmcs->get_req_var('password');
$securityqid = $whmcs->get_req_var('securityqid');
$securityqans = $whmcs->get_req_var('securityqans');
$customfield = $whmcs->get_req_var('customfield');
$errormessage = '';
if( $whmcs->get_req_var('register') )
{
    check_token();
    $errormessage = checkDetailsareValid('', true);
    if( !$errormessage )
    {
        $userid = addClient($firstname, $lastname, $companyname, $email, $address1, $address2, $city, $state, $postcode, $country, $phonenumber, $password, $securityqid, $securityqans);
        run_hook('ClientAreaRegister', array( 'userid' => $userid ));
        redir('', "clientarea.php");
    }
}
$pagetitle = $_LANG['clientregistertitle'];
$breadcrumbnav = "<a href=\"index.php\">" . $_LANG['globalsystemname'] . "</a> > <a href=\"register.php\">" . $_LANG['clientregistertitle'] . "</a>";
$pageicon = "images/order_big.gif";
initialiseClientArea($pagetitle, $pageicon, $breadcrumbnav);
$templatefile = 'clientregister';
if( !$CONFIG['AllowClientRegister'] )
{
    $smarty->assign('noregistration', true);
}
include("includes/countries.php");
$countriesdropdown = getCountriesDropDown($country);
$smarty->assign('errormessage', $errormessage);
$smarty->assign('clientfirstname', $firstname);
$smarty->assign('clientlastname', $lastname);
$smarty->assign('clientcompanyname', $companyname);
$smarty->assign('clientemail', $email);
$smarty->assign('clientaddress1', $address1);
$smarty->assign('clientaddress2', $address2);
$smarty->assign('clientcity', $city);
$smarty->assign('clientstate', $state);
$smarty->assign('clientpostcode', $postcode);
$smarty->assign('clientcountriesdropdown', $countriesdropdown);
$smarty->assign('clientphonenumber', $phonenumber);
$smarty->assign('securityquestions', $securityquestions);
$customfields = getCustomFields('client', '', '', '', 'on', $customfield);
$smarty->assign('customfields', $customfields);
$smarty->assign('capatacha', $capatacha);
$smarty->assign('recapatchahtml', clientAreaReCaptchaHTML());
$smarty->assign('accepttos', $CONFIG['EnableTOSAccept']);
$smarty->assign('tosurl', $CONFIG['TermsOfService']);
outputClientArea($templatefile);