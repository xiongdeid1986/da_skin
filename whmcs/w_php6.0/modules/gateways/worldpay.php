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
function worldpay_config()
{
    global $CONFIG;
    $configarray = array( 'FriendlyName' => array( 'Type' => 'System', 'Value' => 'WorldPay' ), 'installationid' => array( 'FriendlyName' => "Installation ID", 'Type' => 'text', 'Size' => '20', 'Description' => "Enter your WorldPay Installation ID" ), 'prpassword' => array( 'FriendlyName' => "Payment Response Password", 'Type' => 'text', 'Size' => '20', 'Description' => "Enter your WorldPay Payment Response Password used in Callback Validations (Optional)" ), 'testmode' => array( 'FriendlyName' => "Test Mode", 'Type' => 'yesno' ) );
    return $configarray;
}
function worldpay_link($params)
{
    $testMode = $params['testmode'] == 'on' ? '-test' : '';
    $formUrl = "https://secure" . $testMode . ".worldpay.com/wcc/purchase";
    $address = $params['clientdetails']['address1'];
    if( $params['clientdetails']['address2'] )
    {
        $address .= "\n" . $params['clientdetails']['address2'];
    }
    $address .= "\n" . $params['clientdetails']['city'];
    $address .= "\n" . $params['clientdetails']['state'];
    $code = "<form action=\"" . $formUrl . "\" method=\"post\">\n<input type=\"hidden\" name=\"instId\" value=\"" . $params['installationid'] . "\">\n<input type=\"hidden\" name=\"cartId\" value=\"" . $params['invoiceid'] . "\">\n<input type=\"hidden\" name=\"desc\" value=\"" . $params['description'] . "\">\n<input type=\"hidden\" name=\"amount\" value=\"" . $params['amount'] . "\">\n<input type=\"hidden\" name=\"currency\" value=\"" . $params['currency'] . "\">\n<input type=\"hidden\" name=\"name\" value=\"" . $params['clientdetails']['firstname'] . " " . $params['clientdetails']['lastname'] . "\">\n<input type=\"hidden\" name=\"email\" value=\"" . $params['clientdetails']['email'] . "\">\n<input type=\"hidden\" name=\"address\" value=\"" . $address . "\">\n<input type=\"hidden\" name=\"postcode\" value=\"" . $params['clientdetails']['postcode'] . "\">\n<input type=\"hidden\" name=\"country\" value=\"" . $params['clientdetails']['country'] . "\">\n<input type=\"hidden\" name=\"tel\" value=\"" . $params['clientdetails']['phonenumber'] . "\">";
    if( $params['testmode'] == 'on' )
    {
        $code .= "\n<input type=\"hidden\" name=\"testMode\" value=\"100\">";
    }
    if( $params['authmode'] == 'on' )
    {
        $code .= "\n<input type=\"hidden\" name=\"authMode\" value=\"E\">";
    }
    $code .= "\n<INPUT TYPE=\"hidden\" NAME=\"MC_callback\" VALUE=\"" . $params['systemurl'] . "/modules/gateways/callback/worldpay.php\">\n<input type=\"submit\" value=\"" . $params['langpaynow'] . "\">\n</form>";
    return $code;
}