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
if( !defined('WHMCS') )
{
    exit( "This file cannot be accessed directly" );
}
if( !function_exists('calcCartTotals') )
{
    require(ROOTDIR . "/includes/orderfunctions.php");
}
if( !function_exists('checkDetailsareValid') )
{
    require(ROOTDIR . "/includes/clientfunctions.php");
}
if( !function_exists('saveCustomFields') )
{
    require(ROOTDIR . "/includes/customfieldfunctions.php");
}
$clientIp = $whmcs->get_req_var('clientip');
$customFields = $whmcs->get_req_var('customfields');
$skipValidation = $whmcs->get_req_var('skipvalidation');
$noEmail = $whmcs->get_req_var('noemail');
if( $clientIp )
{
    $remote_ip = $clientIp;
}
$errorMessage = checkDetailsareValid('', false, true, true, false);
$currency = (int) $whmcs->get_req_var('currency');
$language = $whmcs->get_req_var('language');
$firstName = $whmcs->get_req_var('firstname');
$lastName = $whmcs->get_req_var('lastname');
$companyName = $whmcs->get_req_var('companyname');
$email = $whmcs->get_req_var('email');
$address1 = $whmcs->get_req_var('address1');
$address2 = $whmcs->get_req_var('address2');
$city = $whmcs->get_req_var('city');
$state = $whmcs->get_req_var('state');
$postcode = $whmcs->get_req_var('postcode');
$country = $whmcs->get_req_var('country');
$phoneNumber = $whmcs->get_req_var('phonenumber');
$password2 = $whmcs->get_req_var('password2');
$securityQuestionId = (int) $whmcs->get_req_var('securityqid');
$securityQuestionAnswer = $whmcs->get_req_var('securityqans');
$clientGroupId = $whmcs->get_req_var('groupid');
$notes = $whmcs->get_req_var('notes');
$customFieldsErrors = array(  );
if( !empty($customFields) )
{
    $customFields = safe_unserialize(base64_decode($customFields));
    $validate = new WHMCS_Validate();
    $validate->validateCustomFields('client', '', false, $customFields);
    $customFieldsErrors = $validate->getErrors();
}
if( ($errorMessage || 0 < count($customFieldsErrors)) && !$skipValidation )
{
    if( $errorMessage )
    {
        $errorMessage = explode("<li>", $errorMessage);
        $error = $errorMessage[1];
        $error = strip_tags($error);
    }
    else
    {
        $error = implode(", ", $customFieldsErrors);
    }
    $apiresults = array( 'result' => 'error', 'message' => $error );
}
else
{
    if( $errorMessage )
    {
        $errMsgArr = explode("<li>", $errorMessage);
        $error = strip_tags($errMsgArr[1]);
        $emailErrLang = $_LANG['ordererroruserexists'];
        if( stripos($emailErrLang, $error) !== false )
        {
            $apiresults = array( 'result' => 'error', 'message' => $error );
            return NULL;
        }
    }
    $_SESSION['currency'] = $currency;
    $sendEmail = $noEmail ? false : true;
    $langAtStart = $_SESSION['Language'];
    if( $language )
    {
        $_SESSION['Language'] = $language;
    }
    $clientId = addClient($firstName, $lastName, $companyName, $email, $address1, $address2, $city, $state, $postcode, $country, $phoneNumber, $password2, $securityQuestionId, $securityQuestionAnswer, $sendEmail, array( 'notes' => $notes, 'groupid' => $clientGroupId ));
    $cardType = $whmcs->get_req_var('cardtype');
    if( !$cardType )
    {
        $cardType = $whmcs->get_req_var('cctype');
    }
    if( $cardType )
    {
        if( !function_exists('updateCCDetails') )
        {
            require(ROOTDIR . "/includes/ccfunctions.php");
        }
        $cardNumber = $whmcs->get_req_var('cardnum');
        $cardCVV = $whmcs->get_req_var('cvv');
        $cardExpiry = $whmcs->get_req_var('expdate');
        $cardStartDate = $whmcs->get_req_var('startdate');
        $cardIssueNumber = $whmcs->get_req_var('issuenumber');
        updateCCDetails($clientId, $cardType, $cardNumber, $cardCVV, $cardExpiry, $cardStartDate, $cardIssueNumber);
        unset($cardNumber);
        unset($cardCVV);
        unset($cardExpiry);
        unset($cardStartDate);
        unset($cardIssueNumber);
    }
    if( $customFields )
    {
        saveCustomFields($clientId, $customFields);
    }
    run_hook('ClientAdd', array_merge(array( 'userid' => $clientId, 'firstname' => $firstName, 'lastname' => $lastName, 'companyname' => $companyName, 'email' => $email, 'address1' => $address1, 'address2' => $address2, 'city' => $city, 'state' => $state, 'postcode' => $postcode, 'country' => $country, 'phonenumber' => $phoneNumber, 'password' => $password2 ), array( 'notes' => $notes, 'groupid' => $clientGroupId ), array( 'customfields' => $customFields )));
    $apiresults = array( 'result' => 'success', 'clientid' => $clientId );
    $_SESSION['Language'] = $langAtStart;
}