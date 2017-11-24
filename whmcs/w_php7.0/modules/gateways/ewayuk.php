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
function ewayuk_config()
{
    $configarray = array( 'FriendlyName' => array( 'Type' => 'System', 'Value' => "eWay UK" ), 'customerid' => array( 'FriendlyName' => "Customer ID", 'Type' => 'text', 'Size' => '20' ), 'username' => array( 'FriendlyName' => 'Username', 'Type' => 'text', 'Size' => '20' ), 'testmode' => array( 'FriendlyName' => "Test Mode", 'Type' => 'yesno', 'Description' => "Tick this to enable test mode" ) );
    return $configarray;
}
function ewayuk_link($params)
{
    $query = '';
    $gatewaytestmode = $params['testmode'];
    if( $gatewaytestmode == 'on' )
    {
        $query .= "CustomerID=87654321";
        $query .= "&UserName=TestAccount";
    }
    else
    {
        $query .= "CustomerID=" . $params['customerid'];
        $query .= "&UserName=" . $params['username'];
    }
    $query .= "&MerchantInvoice=" . $params['invoiceid'];
    $query .= "&MerchantReference=" . $params['invoiceid'];
    $query .= "&Amount=" . urlencode($params['amount']);
    $query .= "&Currency=" . $params['currency'];
    $query .= "&CustomerFirstName=" . $params['clientdetails']['firstname'];
    $query .= "&CustomerLastName=" . $params['clientdetails']['lastname'];
    $query .= "&CustomerAddress=" . $params['clientdetails']['address1'] . " " . $params['clientdetails']['address2'];
    $query .= "&CustomerCity=" . $params['clientdetails']['city'];
    $query .= "&CustomerState=" . $params['clientdetails']['state'];
    $query .= "&CustomerPostCode=" . $params['clientdetails']['postcode'];
    $query .= "&CustomerCountry=" . $params['clientdetails']['country'];
    $query .= "&CustomerEmail=" . $params['clientdetails']['email'];
    $query .= "&CustomerPhone=" . $params['clientdetails']['phonenumber'];
    $query .= "&CancelUrl=" . urlencode($params['systemurl'] . "/viewinvoice.php?id=" . $params['invoiceid']);
    $query .= "&ReturnUrl=" . urlencode($params['systemurl'] . "/modules/gateways/callback/ewayuk.php");
    $query = str_replace(" ", "%20", $query);
    $posturl = "https://payment.ewaygateway.com/Request/?" . $query;
    $response = curlCall($posturl, '');
    $responsemode = strtolower(ewayuk_fetch_data($response, "<Result>", "</Result>"));
    if( $responsemode == 'true' )
    {
        $redirecturl = ewayuk_fetch_data($response, "<Uri>", "</Uri>");
        $code = "<input type=\"button\" value=\"" . $params['langpaynow'] . "\" onclick=\"window.location='" . $redirecturl . "'\" />\n</form>";
        return $code;
    }
    logTransaction("eWay UK", $response, 'Error');
    return "An Error Occurred. Please try again later or submit a ticket if the error persists.";
}
function ewayuk_fetch_data($string, $start_tag, $end_tag)
{
    $position = stripos($string, $start_tag);
    $str = substr($string, $position);
    $str_second = substr($str, strlen($start_tag));
    $second_positon = stripos($str_second, $end_tag);
    $str_third = substr($str_second, 0, $second_positon);
    $ewayukhp_fetch_data = trim($str_third);
    return $ewayukhp_fetch_data;
}