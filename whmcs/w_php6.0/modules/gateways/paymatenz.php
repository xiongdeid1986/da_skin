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
$GATEWAYMODULE['paymatenzname'] = 'paymatenz';
$GATEWAYMODULE['paymatenzvisiblename'] = "Paymate NZ";
$GATEWAYMODULE['paymatenztype'] = 'Invoices';
function paymatenz_activate()
{
    defineGatewayField('paymatenz', 'text', 'mid', '', "Member ID", '20', '');
}
function paymatenz_link($params)
{
    $code = "<form action=\"https://www.paymate.com/PayMate/GenExpressPayment\" method=\"post\">\n<input type=\"hidden\" name=\"cmd\" value=\"_xclick\">\n<input type=\"hidden\" name=\"mid\" value=\"" . $params['mid'] . "\">\n<input type=\"hidden\" name=\"amt\" value=\"" . $params['amount'] . "\">\n<input type=\"hidden\" name=\"amt_editable\" value=\"N\">\n<input type=\"hidden\" name=\"currency\" value=\"" . $params['currency'] . "\">\n<input type=\"hidden\" name=\"ref\" value=\"" . $params['invoiceid'] . "\">\n<input type=\"hidden\" name=\"return\" value=\"" . $params['systemurl'] . "/modules/gateways/callback/paymate.php\">\n<input type=\"hidden\" name=\"back\" value=\"" . $params['systemurl'] . "/modules/gateways/callback/paymate.php\">\n<input type=\"hidden\" name=\"notify\" value=\"place holder for notify url\">\n<input type=\"hidden\" name=\"popup\" value=\"false\">\n<input type=\"hidden\" name=\"pmt_sender_email\" value=\"" . $params['clientdetails']['email'] . "\">\n<input type=\"hidden\" name=\"pmt_contact_firstname\" value=\"" . $params['clientdetails']['firstname'] . "\">\n<input type=\"hidden\" name=\"pmt_contact_surname\" value=\"" . $params['clientdetails']['lastname'] . "\">\n<input type=\"hidden\" name=\"pmt_contact_phone\" value=\"" . $params['clientdetails']['phonenumber'] . "\">\n<input type=\"hidden\" name=\"pmt_country\" value=\"" . $params['clientdetails']['country'] . "\">\n<input type=\"hidden\" name=\"regindi_sub\" value=\"" . $params['clientdetails']['city'] . "\">\n<input type=\"hidden\" name=\"regindi_state\" value=\"" . $params['clientdetails']['state'] . "\">\n<input type=\"hidden\" name=\"regindi_address1\" value=\"" . $params['clientdetails']['address1'] . "\">\n<input type=\"hidden\" name=\"regindi_address2\" value=\"" . $params['clientdetails']['address2'] . "\">\n<input type=\"hidden\" name=\"regindi_pcode\" value=\"" . $params['clientdetails']['postcode'] . "\">\n<input type=\"submit\" value=\"" . $params['langpaynow'] . "\">\n</form>";
    return $code;
}