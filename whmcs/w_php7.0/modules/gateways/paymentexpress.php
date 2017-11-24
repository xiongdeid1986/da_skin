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
function paymentexpress_MetaData()
{
    return array( 'DisplayName' => "Payment Express", 'APIVersion' => "1.1" );
}
function paymentexpress_config()
{
    $configarray = array( 'FriendlyName' => array( 'Type' => 'System', 'Value' => "Payment Express" ), 'pxpayuserid' => array( 'FriendlyName' => "User ID", 'Type' => 'text', 'Size' => '20', 'Description' => "Your account's user ID" ), 'pxpaykey' => array( 'FriendlyName' => "Post Password", 'Type' => 'text', 'Size' => '70', 'Description' => "Your account's 64 character key" ) );
    return $configarray;
}
function paymentexpress_link($params)
{
    $url = "https://sec.paymentexpress.com/pxpay/pxaccess.aspx";
    $xml = "<GenerateRequest>\n<PxPayUserId>" . $params['pxpayuserid'] . "</PxPayUserId>\n<PxPayKey>" . $params['pxpaykey'] . "</PxPayKey>\n<AmountInput>" . $params['amount'] . "</AmountInput>\n<CurrencyInput>" . $params['currency'] . "</CurrencyInput>\n<MerchantReference>" . $params['description'] . "</MerchantReference>\n<EmailAddress>" . $params['clientdetails']['email'] . "</EmailAddress>\n<TxnData1>" . $params['invoiceid'] . "</TxnData1>\n<TxnType>Purchase</TxnType>\n<TxnId>" . substr(time() . $params['invoiceid'], 0, 16) . "</TxnId>\n<BillingId></BillingId>\n<EnableAddBillCard>0</EnableAddBillCard>\n<UrlSuccess>" . $params['systemurl'] . "/modules/gateways/callback/paymentexpress.php</UrlSuccess>\n<UrlFail>" . $params['systemurl'] . "/clientarea.php</UrlFail>\n</GenerateRequest>";
    $data = curlCall($url, $xml);
    $xmlresponse = XMLtoArray($data);
    $uri = $xmlresponse['REQUEST']['URI'];
    $code = "<form method=\"post\" action=\"" . $uri . "\"><input type=\"submit\" value=\"" . $params['langpaynow'] . "\"></form>";
    return $code;
}