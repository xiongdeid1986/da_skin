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
require("../../../init.php");
$whmcs->load_function('gateway');
$whmcs->load_function('invoice');
$gateway = WHMCS_Module_Gateway::factory('paypalexpress');
$gatewayParams = $gateway->getParams();
$token = '';
if( isset($_REQUEST['token']) )
{
    $token = $_REQUEST['token'];
}
if( !$token )
{
    logTransaction("PayPal Express Callback", $_REQUEST, "Missing Token");
    exit();
}
$postfields = array(  );
$postfields['TOKEN'] = $token;
$results = paypalexpress_api_call($gatewayParams, 'GetExpressCheckoutDetails', $postfields);
$ack = strtoupper($results['ACK']);
if( $ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING' )
{
    logTransaction("PayPal Express Callback", $results, 'Successful');
    $email = $results['EMAIL'];
    $payerId = $results['PAYERID'];
    $payerStatus = $results['PAYERSTATUS'];
    $salutation = $results['SALUTATION'];
    $firstName = $results['FIRSTNAME'];
    $middleName = $results['MIDDLENAME'];
    $lastName = $results['LASTNAME'];
    $suffix = $results['SUFFIX'];
    $cntryCode = $results['COUNTRYCODE'];
    $business = $results['BUSINESS'];
    $shipToName = $results['PAYMENTREQUEST_0_SHIPTONAME'];
    $shipToStreet = $results['PAYMENTREQUEST_0_SHIPTOSTREET'];
    $shipToStreet2 = $results['PAYMENTREQUEST_0_SHIPTOSTREET2'];
    $shipToCity = $results['PAYMENTREQUEST_0_SHIPTOCITY'];
    $shipToState = $results['PAYMENTREQUEST_0_SHIPTOSTATE'];
    $shipToCntryCode = $results['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE'];
    $shipToZip = $results['PAYMENTREQUEST_0_SHIPTOZIP'];
    $addressStatus = $results['ADDRESSSTATUS'];
    $invoiceNumber = $results['INVNUM'];
    $phonNumber = $results['PHONENUM'];
    $_SESSION['paypalexpress']['payerid'] = $payerId;
    if( $_SESSION['uid'] )
    {
        redirSystemURL("a=checkout", "cart.php");
    }
    $is_registered = get_query_val('tblclients', 'id', array( 'email' => $email ));
    if( $is_registered )
    {
        redirSystemURL("a=login", "cart.php");
    }
    $_SESSION['cart']['user'] = array( 'firstname' => $firstName, 'lastname' => $lastName, 'companyname' => $business, 'email' => $email, 'address1' => $shipToStreet, 'address2' => $shipToStreet2, 'city' => $shipToCity, 'state' => $shipToState, 'postcode' => $shipToZip, 'country' => $shipToCntryCode, 'phonenumber' => $phonNumber );
    redirSystemURL("a=checkout", "cart.php");
}
else
{
    logTransaction("PayPal Express Callback", $results, 'Error');
    echo "An Error Occurred. Please contact support.";
}