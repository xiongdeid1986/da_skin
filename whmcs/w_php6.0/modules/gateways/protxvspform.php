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
function protxvspform_config()
{
    $configArray = array( 'FriendlyName' => array( 'Type' => 'System', 'Value' => "Sage Pay Form" ), 'vendorname' => array( 'FriendlyName' => "Vendor Name", 'Type' => 'text', 'Size' => '25', 'Description' => "The Vendor Name assigned to you by Sage Pay" ), 'xorencryptionpw' => array( 'FriendlyName' => "Encryption Password", 'Type' => 'text', 'Size' => '25', 'Description' => "The AES Encryption Password assigned to you by Sage Pay" ), 'vendoremail' => array( 'FriendlyName' => "Vendor Email", 'Type' => 'text', 'Size' => '40', 'Description' => "The email address you want Sage Pay to send receipts to (leave blank for none)" ), 'testmode' => array( 'FriendlyName' => "Test Mode", 'Type' => 'yesno' ) );
    return $configArray;
}
/**
 * Generate and output a payment link for the Sage Pay Form gateway.
 *
 * @param array $params An array of parameters for the invoice being paid
 *
 * @return string The form output for the payment to occur
 */
function protxvspform_link($params)
{
    $strEncryptionPassword = $params['xorencryptionpw'];
    $strVendorTxCode = date('YmdHis') . $params['invoiceid'];
    $strPost = "VendorTxCode=" . $strVendorTxCode;
    $strPost .= "&Amount=" . number_format($params['amount'], 2);
    $strPost .= "&Currency=" . $params['currency'];
    $strPost .= "&Description=" . $params['description'];
    $strPost .= "&SuccessURL=" . $params['systemurl'] . "/modules/gateways/callback/protxvspform.php?invoiceid=" . $params['invoiceid'];
    $strPost .= "&FailureURL=" . $params['systemurl'] . "/modules/gateways/callback/protxvspform.php?invoiceid=" . $params['invoiceid'];
    $strPost .= "&CustomerName=" . $params['clientdetails']['fullname'];
    if( !empty($params['vendoremail']) )
    {
        $strPost .= "&VendorEMail=" . $params['vendoremail'];
    }
    $strPost .= "&BillingSurname=" . $params['clientdetails']['lastname'];
    $strPost .= "&BillingFirstnames=" . $params['clientdetails']['firstname'];
    $strPost .= "&BillingAddress1=" . $params['clientdetails']['address1'];
    $strPost .= "&BillingCity=" . $params['clientdetails']['city'];
    $strPost .= "&BillingPostCode=" . $params['clientdetails']['postcode'];
    $strPost .= "&BillingCountry=" . $params['clientdetails']['countrycode'];
    $strPost .= "&DeliverySurname=" . $params['clientdetails']['lastname'];
    $strPost .= "&DeliveryFirstnames=" . $params['clientdetails']['firstname'];
    $strPost .= "&DeliveryAddress1=" . $params['clientdetails']['address1'];
    $strPost .= "&DeliveryCity=" . $params['clientdetails']['city'];
    $strPost .= "&DeliveryPostCode=" . $params['clientdetails']['postcode'];
    $strPost .= "&DeliveryCountry=" . $params['clientdetails']['countrycode'];
    $cipher = new Crypt_AES();
    $cipher->setKey($strEncryptionPassword);
    $cipher->setIV($strEncryptionPassword);
    $strCrypt = strtoupper(bin2hex($cipher->encrypt($strPost)));
    $strPurchaseURL = "https://live.sagepay.com/gateway/service/vspform-register.vsp";
    if( $params['testmode'] )
    {
        $strPurchaseURL = "https://test.sagepay.com/gateway/service/vspform-register.vsp";
    }
    $code = "<form action=\"" . $strPurchaseURL . "\" method=\"post\">\n    <input type=\"hidden\" name=\"VPSProtocol\" value=\"3.00\">\n    <input type=\"hidden\" name=\"TxType\" value=\"PAYMENT\">\n    <input type=\"hidden\" name=\"Vendor\" value=\"" . $params['vendorname'] . "\">\n    <input type=\"hidden\" name=\"Crypt\" value=\"@" . $strCrypt . "\">\n    <input type=\"submit\" value=\"" . $params['langpaynow'] . "\">\n    </form><br />";
    return $code;
}