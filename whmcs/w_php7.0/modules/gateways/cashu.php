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
$GATEWAYMODULE['cashuname'] = 'cashu';
$GATEWAYMODULE['cashuvisiblename'] = 'CashU';
$GATEWAYMODULE['cashutype'] = 'Invoices';
$GATEWAYMODULE['cashunotes'] = "You must set the 'thanx_url' in your CashU Control Panel to: " . $CONFIG['SystemURL'] . "/modules/gateways/callback/cashu.php";
function cashu_activate()
{
    defineGatewayField('cashu', 'text', 'merchantid', '', "Merchant ID", '20', '');
    defineGatewayField('cashu', 'text', 'encryptionkeyword', '', "Encryption Keyword", '20', '');
    defineGatewayField('cashu', 'yesno', 'demomode', '', "Demo Mode", '', '');
}
function cashu_link($params)
{
    if( $params['cconvert'] == 'on' )
    {
        $params['amount'] = number_format($params['amount'] / $params['ccrate'], 2, ".", '');
        $params['currency'] = $params['cccurrency'];
    }
    $token = md5($params['merchantid'] . ":" . $params['amount'] . ":" . strtolower($params['currency']) . ":" . $params['encryptionkeyword']);
    $code = "<form action=\"https://www.cashu.com/cgi-bin/pcashu.cgi\" method=\"post\">\n<input type=\"hidden\" name=\"merchant_id\" value=\"" . $params['merchantid'] . "\">\n<input type=\"hidden\" name=\"token\" value=\"" . $token . "\">\n<input type=\"hidden\" name=\"display_text\" value=\"" . $params['description'] . "\">\n<input type=\"hidden\" name=\"currency\" value=\"" . $params['currency'] . "\">\n<input type=\"hidden\" name=\"amount\" value=\"" . $params['amount'] . "\">\n<input type=\"hidden\" name=\"language\" value=\"en\">\n<input type=\"hidden\" name=\"email\" value=\"" . $params['clientdetails']['email'] . "\">\n<input type=\"hidden\" name=\"session_id\" value=\"" . $params['invoiceid'] . "\">\n<input type=\"hidden\" name=\"txt1\" value=\"" . $params['description'] . "\">";
    if( $params['demomode'] == 'on' )
    {
        $code .= "<input type=\"hidden\" name=\"test_mode\" value=\"1\">";
    }
    $code .= "\n<input type=\"submit\" value=\"" . $params['langpaynow'] . "\">\n</form>";
    return $code;
}