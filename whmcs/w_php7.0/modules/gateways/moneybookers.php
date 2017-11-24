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
$GATEWAYMODULE['moneybookersname'] = 'moneybookers';
$GATEWAYMODULE['moneybookersvisiblename'] = 'Skrill';
$GATEWAYMODULE['moneybookerstype'] = 'Invoices';
function moneybookers_activate()
{
    defineGatewayField('moneybookers', 'text', 'merchantemail', '', "Merchant Email", '50', "The email address used to identify you to Skrill");
    defineGatewayField('moneybookers', 'text', 'secretword', '', "Secret Word", '20', "Must match what is set in the Merchant Tools section of your Skrill Account");
}
function moneybookers_link($params)
{
    global $CONFIG;
    $language = $CONFIG['Language'];
    if( $params['clientdetails']['language'] )
    {
        $language = $params['clientdetails']['language'];
    }
    $languagecode = 'EN';
    if( $language == 'German' )
    {
        $languagecode = 'DE';
    }
    if( $language == 'Spanish' )
    {
        $languagecode = 'ES';
    }
    if( $language == 'French' )
    {
        $languagecode = 'FR';
    }
    if( $language == 'Turkish' )
    {
        $languagecode = 'TR';
    }
    if( $language == 'Italian' )
    {
        $languagecode = 'IT';
    }
    $code = "<form action=\"https://www.moneybookers.com/app/payment.pl\">\n<input type=\"hidden\" name=\"pay_to_email\" value=\"" . $params['merchantemail'] . "\">\n<input type=\"hidden\" name=\"pay_from_email\" value=\"" . $params['clientdetails']['email'] . "\">\n<input type=\"hidden\" name=\"language\" value=\"" . $languagecode . "\">\n<input type=\"hidden\" name=\"amount\" value=\"" . $params['amount'] . "\">\n<input type=\"hidden\" name=\"currency\" value=\"" . $params['currency'] . "\">\n<input type=\"hidden\" name=\"recipient_description\" value=\"" . $CONFIG['CompanyName'] . "\">\n<input type=\"hidden\" name=\"detail1_description\" value=\"" . $params['description'] . "\">\n<input type=\"hidden\" name=\"detail1_text\" value=\"" . $params['invoiceid'] . "\">\n<input type=\"hidden\" name=\"return_url\" value=\"" . $params['returnurl'] . "\">\n<input type=\"hidden\" name=\"cancel_url\" value=\"" . $params['returnurl'] . "&paymentfailed=true\">\n<input type=\"hidden\" name=\"status_url\" value=\"" . $params['systemurl'] . "/modules/gateways/callback/moneybookers.php\">\n<input type=\"hidden\" name=\"transaction_id\" value=\"" . substr($params['invoiceid'] . time(), 0, 100) . "\">\n<input type=\"hidden\" name=\"firstname\" value=\"" . $params['clientdetails']['firstname'] . "\">\n<input type=\"hidden\" name=\"lastname\" value=\"" . $params['clientdetails']['lastname'] . "\">\n<input type=\"hidden\" name=\"address\" value=\"" . $params['clientdetails']['address1'] . "\">\n<input type=\"hidden\" name=\"city\" value=\"" . $params['clientdetails']['city'] . "\">\n<input type=\"hidden\" name=\"state\" value=\"" . $params['clientdetails']['state'] . "\">\n<input type=\"hidden\" name=\"postal_code\" value=\"" . $params['clientdetails']['postcode'] . "\">\n<input type=\"hidden\" name=\"merchant_fields\" value=\"platform,invoice_id\">\n<input type=\"hidden\" name=\"platform\" value=\"21477273\">\n<input type=\"hidden\" name=\"invoice_id\" value=\"" . $params['invoiceid'] . "\">\n<input type=\"submit\" value=\"" . $params['langpaynow'] . "\">\n</form>";
    return $code;
}