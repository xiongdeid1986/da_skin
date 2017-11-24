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
function protx_config()
{
    $configArray = array( 'FriendlyName' => array( 'Type' => 'System', 'Value' => 'SagePay' ), 'vendorid' => array( 'FriendlyName' => "Vendor ID", 'Type' => 'text', 'Size' => '20', 'Description' => "Main Account Vendor ID used for First Payment" ), 'recurringvendorid' => array( 'FriendlyName' => "Vendor ID", 'Type' => 'text', 'Size' => '20', 'Description' => "Vendor ID of Continuous Authority Merchant Account used for Recurring Payments" ), 'testmode' => array( 'FriendlyName' => "Test Mode", 'Type' => 'yesno' ) );
    return $configArray;
}
/**
 * Attempt to load the 3dSecure details for a SagePay payment.
 *
 * @param array $params An array of parameters for the invoice being paid
 *
 * @return string Returns either the 3dSecure form code, or a success/failed response
 */
function protx_3dsecure($params)
{
    $whmcs = WHMCS_Application::getinstance();
    $TargetURL = "https://live.sagepay.com/gateway/service/vspdirect-register.vsp";
    if( $params['testmode'] == 'on' )
    {
        $TargetURL = "https://test.sagepay.com/gateway/service/vspdirect-register.vsp";
    }
    $data = array(  );
    $data['VPSProtocol'] = "3.00";
    $data['TxType'] = 'PAYMENT';
    $data['Vendor'] = $params['vendorid'];
    $data['VendorTxCode'] = date('YmdHis') . $params['invoiceid'];
    $data['Amount'] = $params['amount'];
    $data['Currency'] = $params['currency'];
    $data['Description'] = $params['companyname'] . " - Invoice #" . $params['invoiceid'];
    $cardType = protx_getcardtype($params['cardtype']);
    $data['CardHolder'] = $params['clientdetails']['fullname'];
    $data['CardType'] = $cardType;
    $data['CardNumber'] = $params['cardnum'];
    $data['ExpiryDate'] = $params['cardexp'];
    if( !empty($params['cccvv']) )
    {
        $data['CV2'] = $params['cccvv'];
    }
    $data['BillingSurname'] = $params['clientdetails']['lastname'];
    $data['BillingFirstnames'] = $params['clientdetails']['firstname'];
    $data['BillingAddress1'] = $params['clientdetails']['address1'];
    $data['BillingAddress2'] = $params['clientdetails']['address2'];
    $data['BillingCity'] = $params['clientdetails']['city'];
    if( $params['clientdetails']['country'] == 'US' )
    {
        $data['BillingState'] = $params['clientdetails']['state'];
    }
    $data['BillingPostCode'] = $params['clientdetails']['postcode'];
    $data['BillingCountry'] = $params['clientdetails']['country'];
    $data['BillingPhone'] = $params['clientdetails']['phonenumber'];
    $data['DeliverySurname'] = $params['clientdetails']['lastname'];
    $data['DeliveryFirstnames'] = $params['clientdetails']['firstname'];
    $data['DeliveryAddress1'] = $params['clientdetails']['address1'];
    $data['DeliveryAddress2'] = $params['clientdetails']['address2'];
    $data['DeliveryCity'] = $params['clientdetails']['city'];
    if( $params['clientdetails']['country'] == 'US' )
    {
        $data['DeliveryState'] = $params['clientdetails']['state'];
    }
    $data['DeliveryPostCode'] = $params['clientdetails']['postcode'];
    $data['DeliveryCountry'] = $params['clientdetails']['country'];
    $data['DeliveryPhone'] = $params['clientdetails']['phonenumber'];
    $data['CustomerEMail'] = $params['clientdetails']['email'];
    $data['ClientIPAddress'] = $whmcs->getRemoteIp();
    $data = protx_formatData($data);
    $response = protx_requestPost($TargetURL, $data);
    $baseStatus = $response['Status'];
    switch( $baseStatus )
    {
        case '3DAUTH':
            logTransaction("SagePay 3DAuth", $response, "3D Auth Required");
            WHMCS_Session::set('protxinvoiceid', $params['invoiceid']);
            $termUrl = $params['systemurl'] . "/modules/gateways/callback/protxthreedsecure.php?invoiceid=" . $params['invoiceid'];
            $code = "<form method=\"post\" action=\"" . $response['ACSURL'] . "\" name=\"paymentfrm\">\n    <input type=\"hidden\" name=\"PaReq\" value=\"" . $response['PAReq'] . "\">\n    <input type=\"hidden\" name=\"TermUrl\" value=\"" . $termUrl . "\">\n    <input type=\"hidden\" name=\"MD\" value=\"" . $response['MD'] . "\">\n    <noscript>\n        <div class=\"errorbox\">\n            <strong>\n                JavaScript is currently disabled or is not supported by your browser.\n            </strong>\n            <br />\n            Please click the continue button to proceed with the processing of your transaction.\n        </div>\n        <p align=\"center\">\n            <input type=\"submit\" value=\"Continue >>\" />\n        </p>\n    </noscript>\n</form>";
            return $code;
            break;
        case 'OK':
            addInvoicePayment($params['invoiceid'], $response['VPSTxId'], '', '', 'protx', 'on');
            logTransaction("SagePay 3DAuth", $response, 'Successful');
            sendMessage("Credit Card Payment Confirmation", $params['invoiceid']);
            $result = 'success';
            return $result;
            break;
        case 'NOTAUTHED':
            $resultText = "Not Authorised";
            break;
        case 'REJECTED':
            $resultText = 'Rejected';
            break;
        case 'FAIL':
            $resultText = 'Failed';
            break;
        default:
            $resultText = 'Error';
            break;
    }
    logTransaction("SagePay 3DAuth", $response, $resultText);
    sendMessage("Credit Card Payment Failed", $params['invoiceid']);
    $result = 'declined';
    return $result;
}
/**
 * Attempt to capture with SagePay the payment for an invoice
 *
 * @param array $params An array of parameters for the invoice being paid
 *
 * @return string
 */
function protx_capture($params)
{
    $whmcs = WHMCS_Application::getinstance();
    $TargetURL = "https://live.sagepay.com/gateway/service/vspdirect-register.vsp";
    if( $params['testmode'] == 'on' )
    {
        $TargetURL = "https://test.sagepay.com/gateway/service/vspdirect-register.vsp";
    }
    $data = array(  );
    $data['VPSProtocol'] = "3.00";
    $data['TxType'] = 'PAYMENT';
    $data['Vendor'] = $params['recurringvendorid'];
    $data['VendorTxCode'] = date('YmdHis') . $params['invoiceid'];
    $data['Amount'] = $params['amount'];
    $data['Currency'] = $params['currency'];
    $data['Description'] = $params['companyname'] . " - Invoice #" . $params['invoiceid'];
    $cardType = protx_getcardtype($params['cardtype']);
    $data['CardHolder'] = $params['clientdetails']['firstname'] . " " . $params['clientdetails']['lastname'];
    $data['CardType'] = $cardType;
    $data['CardNumber'] = $params['cardnum'];
    $data['ExpiryDate'] = $params['cardexp'];
    if( !empty($params['cccvv']) )
    {
        $data['CV2'] = $params['cccvv'];
    }
    $data['BillingSurname'] = $params['clientdetails']['lastname'];
    $data['BillingFirstnames'] = $params['clientdetails']['firstname'];
    $data['BillingAddress1'] = $params['clientdetails']['address1'];
    $data['BillingAddress2'] = $params['clientdetails']['address2'];
    $data['BillingCity'] = $params['clientdetails']['city'];
    if( $params['clientdetails']['country'] == 'US' )
    {
        $data['BillingState'] = $params['clientdetails']['state'];
    }
    $data['BillingPostCode'] = $params['clientdetails']['postcode'];
    $data['BillingCountry'] = $params['clientdetails']['country'];
    $data['BillingPhone'] = $params['clientdetails']['phonenumber'];
    $data['DeliverySurname'] = $params['clientdetails']['lastname'];
    $data['DeliveryFirstnames'] = $params['clientdetails']['firstname'];
    $data['DeliveryAddress1'] = $params['clientdetails']['address1'];
    $data['DeliveryAddress2'] = $params['clientdetails']['address2'];
    $data['DeliveryCity'] = $params['clientdetails']['city'];
    if( $params['clientdetails']['country'] == 'US' )
    {
        $data['DeliveryState'] = $params['clientdetails']['state'];
    }
    $data['DeliveryPostCode'] = $params['clientdetails']['postcode'];
    $data['DeliveryCountry'] = $params['clientdetails']['country'];
    $data['DeliveryPhone'] = $params['clientdetails']['phonenumber'];
    $data['CustomerEMail'] = $params['clientdetails']['email'];
    $data['ClientIPAddress'] = $whmcs->getRemoteIp();
    $data['ApplyAVSCV2'] = '2';
    $data['Apply3DSecure'] = '2';
    switch( $params['cardtype'] )
    {
        case "American Express":
        case 'Laser':
            $data['AccountType'] = 'E';
            break;
        case 'Maestro':
            $data['AccountType'] = 'M';
            break;
        default:
            $data['AccountType'] = 'C';
            break;
    }
    $data = protx_formatData($data);
    $response = protx_requestPost($TargetURL, $data);
    $baseStatus = $response['Status'];
    $result = array(  );
    switch( $baseStatus )
    {
        case 'OK':
            $result['status'] = 'success';
            $result['transid'] = $response['VPSTxId'];
            break;
        case 'NOTAUTHED':
            $result['status'] = "Not Authorised";
            break;
        case 'REJECTED':
            $result['status'] = 'Rejected';
            break;
        case 'FAIL':
            $result['status'] = 'Failed';
            break;
        default:
            $result['status'] = 'Error';
            break;
    }
    $result['rawdata'] = $response;
    $result['fee'] = 0;
    if( $params['cardtype'] == 'Maestro' )
    {
        $userId = get_query_val('tblinvoices', 'userid', array( 'id' => $params['invoiceid'] ));
        update_query('tblclients', array( 'cardtype' => '', 'cardnum' => '', 'expdate' => '', 'issuenumber' => '', 'startdate' => '' ), array( 'id' => $userId ));
    }
    return $result;
}
/**
 * Send the formatted request to the SagePay server and return the response.
 *
 * @param string $url
 * @param string $data
 *
 * @return array
 */
function protx_requestPost($url, $data)
{
    set_time_limit(60);
    $output = array(  );
    $curlSession = curl_init();
    curl_setopt($curlSession, CURLOPT_URL, $url);
    curl_setopt($curlSession, CURLOPT_HEADER, 0);
    curl_setopt($curlSession, CURLOPT_POST, 1);
    curl_setopt($curlSession, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curlSession, CURLOPT_TIMEOUT, 60);
    curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 1);
    $response = explode(chr(10), curl_exec($curlSession));
    if( curl_error($curlSession) )
    {
        $output['Status'] = 'FAIL';
        $output['StatusDetail'] = curl_error($curlSession);
    }
    curl_close($curlSession);
    for( $i = 0; $i < count($response); $i++ )
    {
        $splitAt = strpos($response[$i], "=");
        $output[trim(substr($response[$i], 0, $splitAt))] = trim(substr($response[$i], $splitAt + 1));
    }
    return $output;
}
/**
 * Format the passed array into a &name=value string
 * @param array $data
 *
 * @return string
 */
function protx_formatData($data)
{
    $output = '';
    foreach( $data as $key => $value )
    {
        $output .= "&" . $key . "=" . urlencode($value);
    }
    $output = substr($output, 1);
    return $output;
}
/**
 * Use the WHMCS Saved cardType and return the SagePay expected value.
 *
 * @param string $cardType
 *
 * @return string
 */
function protx_getcardtype($cardType)
{
    switch( $cardType )
    {
        case 'EnRoute':
        case 'Visa':
            $cardType = 'VISA';
            break;
        case 'MasterCard':
            $cardType = 'MC';
            break;
        case "American Express":
            $cardType = 'AMEX';
            break;
        case "Diners Club":
            break;
        case 'Discover':
            $cardType = 'DC';
            break;
        case 'JCB':
            $cardType = 'JCB';
            break;
        case "Visa Debit":
            $cardType = 'DELTA';
            break;
        case 'Maestro':
            $cardType = 'MAESTRO';
            break;
        case "Visa Electron":
            $cardType = 'UKE';
            break;
        case 'Laser':
            $cardType = 'LASER';
    }
    return $cardType;
    break;
}