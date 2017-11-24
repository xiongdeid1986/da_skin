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
$GATEWAYMODULE['asiapayname'] = 'asiapay';
$GATEWAYMODULE['asiapayvisiblename'] = 'AsiaPay';
$GATEWAYMODULE['asiapaytype'] = 'CC';
function asiapay_activate()
{
    defineGatewayField('asiapay', 'text', 'merchantid', '', "Merchant ID", '20', '');
    defineGatewayField('asiapay', 'text', 'secureHashKey', '', "Secure Hash Key (if enabled)", '40', '');
    defineGatewayField('asiapay', 'yesno', 'testmode', '', "Test Mode", '', '');
}
function asiapay_link($params)
{
    if( $params['testmode'] )
    {
        $posturl = "https://test.paydollar.com/b2cDemo/eng/payment/payForm.jsp";
    }
    else
    {
        $posturl = "https://www.paydollar.com/b2c2/eng/payment/payForm.jsp";
    }
    if( isset($params['cardtype']) && 0 < strlen($params['cardtype']) )
    {
        if( $params['cardtype'] == 'Visa' )
        {
            $payMethod = 'VISA';
        }
        else
        {
            if( $params['cardtype'] == 'MasterCard' )
            {
                $payMethod = 'Master';
            }
            else
            {
                if( $params['cardtype'] == "Diners Club" )
                {
                    $payMethod = 'Diners';
                }
                else
                {
                    if( $params['cardtype'] == "American Express" )
                    {
                        $payMethod = 'AMEX';
                    }
                    else
                    {
                        $payMethod = $params['cardtype'];
                    }
                }
            }
        }
    }
    else
    {
        $payMethod = 'ALL';
    }
    $merchantId = $params['merchantid'];
    $amount = $params['amount'];
    $orderRef = $params['invoiceid'];
    $mpsMode = 'NIL';
    $successUrl = $params['systemurl'] . "/modules/gateways/callback/asiapay.php";
    $failUrl = $params['systemurl'] . "/modules/gateways/callback/asiapay.php";
    $cancelUrl = $params['systemurl'] . "/modules/gateways/callback/asiapay.php";
    $payType = 'N';
    $lang = 'E';
    $currCodeArr = array( 'HKD' => 344, 'SGD' => 702, 'CNY' => 156, 'JPY' => 392, 'TWD' => 901, 'AUD' => '036', 'EUR' => 978, 'GBP' => 826, 'CAD' => 124, 'MOP' => 446, 'PHP' => 608, 'THB' => 764, 'MYR' => 458, 'IDR' => 360, 'KRW' => 410, 'SAR' => 682, 'NZD' => 784, 'BND' => '096' );
    if( array_key_exists($params['currency'], $currCodeArr) )
    {
        $currCode = $currCodeArr[$params['currency']];
    }
    else
    {
        $currCode = 840;
    }
    if( isset($params['secureHashKey']) && 0 < strlen(trim($params['secureHashKey'])) )
    {
        $hashArr = array( $merchantId, $orderRef, $currCode, $amount, $payType, $params['secureHashKey'] );
        $hash = sha1(implode("|", $hashArr));
        $secureHashCode = "<input type=\"hidden\" name=\"secureHash\" value=\"" . $hash . "\">";
    }
    else
    {
        $secureHashCode = '';
    }
    $link = "<form name=\"payFormCcard\" method=\"post\" action=\"" . $posturl . "\">\n<input type=\"hidden\" name=\"merchantId\" value=\"" . $merchantId . "\">\n<input type=\"hidden\" name=\"amount\" value=\"" . $amount . "\" >\n<input type=\"hidden\" name=\"orderRef\" value=\"" . $orderRef . "\">\n<input type=\"hidden\" name=\"currCode\" value=\"" . $currCode . "\" >\n<input type=\"hidden\" name=\"mpsMode\" value=\"" . $mpsMode . "\" >\n<input type=\"hidden\" name=\"successUrl\" value=\"" . $successUrl . "\">\n<input type=\"hidden\" name=\"failUrl\" value=\"" . $failUrl . "\">\n<input type=\"hidden\" name=\"cancelUrl\" value=\"" . $cancelUrl . "\">\n<input type=\"hidden\" name=\"payType\" value=\"" . $payType . "\">\n<input type=\"hidden\" name=\"lang\" value=\"" . $lang . "\">\n<input type=\"hidden\" name=\"payMethod\" value=\"" . $payMethod . "\">\n" . $secureHashCode . "\n<input type=\"submit\" name=\"submit\" value=\"Submit\">\n</form>\n";
    return $link;
}