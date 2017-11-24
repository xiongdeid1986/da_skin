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
$GATEWAYMODULE['chronopayname'] = 'chronopay';
$GATEWAYMODULE['chronopayvisiblename'] = 'ChronoPay';
$GATEWAYMODULE['chronopaytype'] = 'Invoices';
function chronopay_activate()
{
    defineGatewayField('chronopay', 'text', 'productid', '', "Product ID", '20', "The product ID of a generic product in your ChronoPay Account");
    defineGatewayField('chronopay', 'text', 'sharedsecret', '', "Shared Secret", '30', "The shared secret is a unique code known only by ChronoPay and the Merchant");
}
function chronopay_link($params)
{
    $operationChecksum = md5(sprintf("%s-%s-%s", $params['productid'], $params['amount'], $params['sharedsecret']));
    $code = "\n<form action=\"https://secure.chronopay.com/index_shop.cgi\" method=\"post\">\n<input type=\"hidden\" name=\"sign\" value=\"" . $operationChecksum . "\">\n<input type=\"hidden\" name=\"product_id\" value=\"" . $params['productid'] . "\">\n<input type=\"hidden\" name=\"product_name\" value=\"" . $params['description'] . "\">\n<input type=\"hidden\" name=\"product_price\" value=\"" . $params['amount'] . "\">\n<input type=\"hidden\" name=\"product_price_currency\" value=\"" . $params['currency'] . "\">\n<input type=\"hidden\" name=\"f_name\" value=\"" . $params['clientdetails']['firstname'] . "\">\n<input type=\"hidden\" name=\"s_name\" value=\"" . $params['clientdetails']['lastname'] . "\">\n<input type=\"hidden\" name=\"email\" value=\"" . $params['clientdetails']['email'] . "\">\n<input type=\"hidden\" name=\"street\" value=\"" . $params['clientdetails']['address1'] . "\">\n<input type=\"hidden\" name=\"city\" value=\"" . $params['clientdetails']['city'] . "\">\n<input type=\"hidden\" name=\"state\" value=\"" . $params['clientdetails']['state'] . "\">\n<input type=\"hidden\" name=\"zip\" value=\"" . $params['clientdetails']['postcode'] . "\">\n<input type=\"hidden\" name=\"country\" value=\"" . $params['clientdetails']['country'] . "\">\n<input type=\"hidden\" name=\"phone\" value=\"" . $params['clientdetails']['phonenumber'] . "\">\n<input type=\"hidden\" name=\"cs1\" value=\"" . $params['invoiceid'] . "\">\n<input type=\"hidden\" name=\"cb_url\" value=\"" . $params['systemurl'] . "/modules/gateways/callback/chronopay.php\">\n<input type=\"hidden\" name=\"cb_type\" value=\"P\">\n<input type=\"hidden\" name=\"decline_url\" value=\"" . $params['returnurl'] . "\">\n<input type=\"submit\" value=\"" . $params['langpaynow'] . "\">\n</form> \n";
    return $code;
}