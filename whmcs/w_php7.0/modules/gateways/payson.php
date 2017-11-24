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
$GATEWAYMODULE['paysonname'] = 'payson';
$GATEWAYMODULE['paysonvisiblename'] = 'Payson';
$GATEWAYMODULE['paysontype'] = 'Invoices';
function payson_activate()
{
    defineGatewayField('payson', 'text', 'agentid', '', "Agent ID", '15', '');
    defineGatewayField('payson', 'text', 'email', '', "Seller Email", '50', '');
    defineGatewayField('payson', 'text', 'key', '', 'Key', '20', '');
    defineGatewayField('payson', 'yesno', 'guaranteeoffered', '', "Offer Payson Guarantee", '', '');
}
function payson_link($params)
{
    $AgentID = $params['agentid'];
    $Key = $params['key'];
    $Description = $params['description'];
    $SellerEmail = $params['email'];
    $BuyerEmail = $params['clientdetails']['email'];
    $BuyerFirstName = $params['clientdetails']['firstname'];
    $BuyerLastName = $params['clientdetails']['lastname'];
    $Cost = str_replace(".", ',', $params['amount']);
    $CurrencyCode = $params['currency'];
    $ExtraCost = '0';
    $OkUrl = $params['systemurl'] . "/modules/gateways/callback/payson.php";
    $CancelUrl = $params['returnurl'];
    $RefNr = $params['invoiceid'];
    $GuaranteeOffered = $params['guaranteeoffered'] ? '2' : '1';
    $MD5string = $SellerEmail . ":" . $Cost . ":" . $ExtraCost . ":" . $OkUrl . ":" . $GuaranteeOffered . $Key;
    $MD5Hash = md5($MD5string);
    $code = "\n<form action=\"https://www.payson.se/merchant/default.aspx\" method=\"post\">\n<input type=\"hidden\" name=\"BuyerEmail\" value=\"" . $BuyerEmail . "\"> \n<input type=\"hidden\" name=\"AgentID\" value=\"" . $AgentID . "\"> \n<input type=\"hidden\" name=\"Description\" value=\"" . $Description . "\"> \n<input type=\"hidden\" name=\"SellerEmail\" value=\"" . $SellerEmail . "\">\n<input type=\"hidden\" name=\"BuyerFirstName\" value=\"" . $BuyerFirstName . "\">\n<input type=\"hidden\" name=\"BuyerLastName\" value=\"" . $BuyerLastName . "\">\n<input type=\"hidden\" name=\"Cost\" value=\"" . $Cost . "\">\n<input type=\"hidden\" name=\"CurrencyCode\" value=\"" . $CurrencyCode . "\">\n<input type=\"hidden\" name=\"ExtraCost\" value=\"" . $ExtraCost . "\">\n<input type=\"hidden\" name=\"OkUrl\" value=\"" . $OkUrl . "\"> \n<input type=\"hidden\" name=\"CancelUrl\" value=\"" . $CancelUrl . "\"> \n<input type=\"hidden\" name=\"RefNr\" value=\"" . $RefNr . "\"> \n<input type=\"hidden\" name=\"MD5\" value=\"" . $MD5Hash . "\">\n<input type=\"hidden\" name=\"GuaranteeOffered\" value=\"" . $GuaranteeOffered . "\"> \n<input type=\"submit\" value=\"" . $params['langpaynow'] . "\">\n</form>\n";
    return $code;
}