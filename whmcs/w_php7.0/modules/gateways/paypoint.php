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
function paypoint_config()
{
    $configarray = array( 'FriendlyName' => array( 'Type' => 'System', 'Value' => "PayPoint.net (SecPay)" ), 'merchantid' => array( 'FriendlyName' => "Merchant ID", 'Type' => 'text', 'Size' => '20' ), 'remotepw' => array( 'FriendlyName' => "Remote Password", 'Type' => 'text', 'Size' => '30' ), 'digestkey' => array( 'FriendlyName' => "Digest Key", 'Type' => 'text', 'Size' => '40' ), 'testmode' => array( 'FriendlyName' => "Test Mode", 'Type' => 'yesno' ) );
    return $configarray;
}
function paypoint_link($params)
{
    $transid = $params['invoiceid'] . '-' . date('Ymdhis');
    $digest = md5($transid . $params['amount'] . $params['remotepw']);
    $code = "<form method=\"post\" action=\"https://www.secpay.com/java-bin/ValCard\">\n<input type=\"hidden\" name=\"merchant\" value=\"" . $params['merchantid'] . "\" />\n<input type=\"hidden\" name=\"trans_id\" value=\"" . $transid . "\" />\n<input type=\"hidden\" name=\"amount\" value=\"" . $params['amount'] . "\" />\n<input type=\"hidden\" name=\"currency\" value=\"" . $params['currency'] . "\" />\n<input type=\"hidden\" name=\"repeat\" value=\"true\" />\n<input type=\"hidden\" name=\"callback\" value=\"" . $params['systemurl'] . "/modules/gateways/callback/paypoint.php\" />\n<input type=\"hidden\" name=\"options\" value=\"cb_post=true,md_flds=trans_id:amount:callback\">\n<input type=\"hidden\" name=\"digest\" value=\"" . $digest . "\" />";
    if( $params['testmode'] )
    {
        $code .= "<input type=\"hidden\" name=\"test_status\" value=\"true\">";
    }
    $code .= "<input type=\"submit\" value=\"" . $params['langpaynow'] . "\">\n</form>";
    return $code;
}