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
$GATEWAYMODULE['paymexname'] = 'paymex';
$GATEWAYMODULE['paymexvisiblename'] = 'Paymex';
$GATEWAYMODULE['paymextype'] = 'Invoices';
function paymex_activate()
{
    defineGatewayField('paymex', 'text', 'authcode', '', "Business ID", '40', "This your unique business ID given to you by Paymex.");
    defineGatewayField('paymex', 'yesno', 'testmode', '', "Test Mode", '', '');
}
function paymex_link($params)
{
    $code = "<form action=\"https://secure.paymex.co.nz/Process.aspx\" method=\"post\">";
    $code .= "<input type=\"hidden\" name=\"business\" value=\"" . $params['authcode'] . "\">";
    $code .= "<input type=\"hidden\" name=\"item_name\" value=\"" . $params['description'] . "\">";
    $code .= "<input type=\"hidden\" name=\"item_number\" value=\"" . $params['invoiceid'] . "\">";
    $code .= "<input type=\"hidden\" name=\"item_qty\" value=\"1\">";
    $code .= "<input type=\"hidden\" name=\"amount\" value=\"" . $params['amount'] . "\">";
    $code .= "<input type=\"hidden\" name=\"client_ref\" value=\"INV" . $params['invoiceid'] . "\">";
    $code .= "<input type=\"hidden\" name=\"retail_ref\" value=\"INV" . $params['invoiceid'] . "\">";
    $code .= "<input type=\"hidden\" name=\"return\" value=\"" . $params['systemurl'] . "/modules/gateways/callback/paymex.php?xresp=1&xinv=" . $params['invoiceid'] . "\">";
    $code .= "<input type=\"hidden\" name=\"return_cancel\" value=\"" . $params['systemurl'] . "\">";
    $code .= "<input type=\"hidden\" name=\"currency_code\" value=\"" . $params['currency'] . "\">";
    $code .= "<input type=\"hidden\" name=\"first_name\" value=\"" . $params['clientdetails']['firstname'] . "\">";
    $code .= "<input type=\"hidden\" name=\"last_name\" value=\"" . $params['clientdetails']['lastname'] . "\">";
    $code .= "<input type=\"hidden\" name=\"address1\" value=\"" . $params['clientdetails']['address1'] . "\">";
    $code .= "<input type=\"hidden\" name=\"address2\" value=\"" . $params['clientdetails']['address2'] . "\">";
    $code .= "<input type=\"hidden\" name=\"suburb\" value=\"" . $params['clientdetails']['state'] . "\">";
    $code .= "<input type=\"hidden\" name=\"city\" value=\"" . $params['clientdetails']['city'] . "\">";
    $code .= "<input type=\"hidden\" name=\"postcode\" value=\"" . $params['clientdetails']['postcode'] . "\">";
    $code .= "<input type=\"hidden\" name=\"country\" value=\"" . $params['clientdetails']['country'] . "\">";
    $code .= "<input type=\"hidden\" name=\"email\" value=\"" . $params['clientdetails']['email'] . "\">";
    $code .= "<input type=\"hidden\" name=\"phone\" value=\"" . $params['clientdetails']['phone'] . "\">";
    if( $params['testmode'] == 'on' )
    {
        $code .= "<input type=\"hidden\" name=\"test_mode\" value=\"1\">";
    }
    $code .= "<input type=\"submit\" value=\"" . $params['langpaynow'] . "\">";
    $code .= "</form>";
    return $code;
}