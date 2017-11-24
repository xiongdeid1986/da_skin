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
$GATEWAYMODULE['nochexname'] = 'nochex';
$GATEWAYMODULE['nochexvisiblename'] = 'NoChex';
$GATEWAYMODULE['nochextype'] = 'Invoices';
function nochex_activate()
{
    defineGatewayField('nochex', 'text', 'email', '', "NoChex Merchant ID", '50', "This is the email you have registered with NoChex");
    defineGatewayField('nochex', 'yesno', 'hide', '', "Hide Details", '0', "Tick to stop customer details being repeated on Nochex payment page");
    defineGatewayField('nochex', 'yesno', 'testmode', '', "Test Mode", '0', "Tick to enable test transaction mode");
}
function nochex_link($params)
{
    $code = "<form action=\"https://secure.nochex.com/\" method=\"post\">\n<input type=hidden name=merchant_id value=\"" . $params['email'] . "\">\n<input type=hidden name=amount value=\"" . $params['amount'] . "\">\n<input type=hidden name=order_id value=\"" . $params['invoiceid'] . "\">\n<input type=hidden name=description value=\"" . $params['description'] . "\">\n<input type=hidden name=billing_fullname value=\"" . $params['clientdetails']['firstname'] . " " . $params['clientdetails']['lastname'] . "\">\n<input type=hidden name=billing_address value=\"" . $params['clientdetails']['address1'] . "\r\n" . $params['clientdetails']['address2'] . "\r\n" . $params['clientdetails']['city'] . "\r\n" . $params['clientdetails']['state'] . "\r\n" . $params['clientdetails']['country'] . "\">\n<input type=hidden name=billing_postcode value=\"" . $params['clientdetails']['postcode'] . "\">\n<input type=hidden name=customer_phone_number value=\"" . $params['clientdetails']['phonenumber'] . "\">\n<input type=hidden name=email_address value=\"" . $params['clientdetails']['email'] . "\">\n<input type=hidden name=success_url value=\"" . $params['systemurl'] . "/viewinvoice.php?id=" . $params['invoiceid'] . "&paymentsuccess=true\">\n<input type=hidden name=cancel_url value=\"" . $params['systemurl'] . "/viewinvoice.php?id=" . $params['invoiceid'] . "&paymentfailed=true\">\n<input type=hidden name=decline_url value=\"" . $params['systemurl'] . "/viewinvoice.php?id=" . $params['invoiceid'] . "&paymentfailed=true\">\n<input type=hidden name=responderurl value=\"" . $params['systemurl'] . "/modules/gateways/callback/nochex.php\">\n<input type=hidden name=callback_url value=\"" . $params['systemurl'] . "/modules/gateways/callback/nochex.php\">\n";
    if( $params['hide'] )
    {
        $code .= "<input type=hidden name=hide_billing_details value=\"true\">";
    }
    if( $params['testmode'] )
    {
        $code .= "<input type=hidden name=test_transaction value=\"100\">\n<input type=hidden name=test_success_url value=\"" . $params['systemurl'] . "/viewinvoice.php?id=" . $params['invoiceid'] . "\">";
    }
    $code .= "\n<input type=\"submit\" value=\"" . $params['langpaynow'] . "\">\n</form>";
    return $code;
}