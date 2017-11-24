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
function sagepaytokens_config()
{
    $configarray = array( 'FriendlyName' => array( 'Type' => 'System', 'Value' => "SagePay Tokens" ), 'vendorid' => array( 'FriendlyName' => "Vendor ID", 'Type' => 'text', 'Size' => '20' ), 'testmode' => array( 'FriendlyName' => "Test Mode", 'Type' => 'yesno' ) );
    return $configarray;
}
function sagepaytokens_storeremote($params)
{
    $subdomain = $params['testmode'] ? 'test' : 'live';
    if( $params['action'] == 'delete' )
    {
        if( !$params['gatewayid'] || $params['gatewayid'] == 'x' )
        {
            return array( 'status' => 'error', 'rawdata' => "Clear Attempt but No Existing Token Stored" );
        }
        $url = "https://" . $subdomain . ".sagepay.com/gateway/service/removetoken.vsp";
        $fields = array(  );
        $fields['VPSProtocol'] = "3.00";
        $fields['TxType'] = 'REMOVETOKEN';
        $fields['Vendor'] = $params['vendorid'];
        $fields['Token'] = $params['gatewayid'];
        $results = sagepaytokens_call($url, $fields);
        if( $results['Status'] == 'OK' )
        {
            return array( 'status' => 'success', 'rawdata' => $results, 'gatewayid' => '' );
        }
        return array( 'status' => 'error', 'rawdata' => $results );
    }
    if( $params['action'] == 'update' )
    {
        $url = "https://" . $subdomain . ".sagepay.com/gateway/service/removetoken.vsp";
        $fields = array(  );
        $fields['VPSProtocol'] = "3.00";
        $fields['TxType'] = 'REMOVETOKEN';
        $fields['Vendor'] = $params['vendorid'];
        $fields['Token'] = $params['gatewayid'];
        $results = sagepaytokens_call($url, $fields);
    }
    $url = "https://" . $subdomain . ".sagepay.com/gateway/service/directtoken.vsp";
    $fields = array(  );
    $fields['VPSProtocol'] = "3.00";
    $fields['TxType'] = 'TOKEN';
    $fields['Vendor'] = $params['vendorid'];
    $fields['Currency'] = $params['currency'];
    $fields['CardHolder'] = $params['clientdetails']['firstname'] . " " . $params['clientdetails']['lastname'];
    $fields['CardNumber'] = $params['cardnum'];
    if( $params['cardstart'] )
    {
        $fields['StartDate'] = $params['cardstart'];
    }
    $fields['ExpiryDate'] = $params['cardexp'];
    if( $params['cardissuenum'] )
    {
        $fields['IssueNumber'] = $params['cardissuenum'];
    }
    if( $params['cardcvv'] )
    {
        $fields['CV2'] = $params['cardcvv'];
    }
    $fields['CardType'] = sagepaytokens_getcardtype($params['cardtype']);
    $results = sagepaytokens_call($url, $fields);
    if( $results['Status'] == 'OK' )
    {
        return array( 'status' => 'success', 'rawdata' => $results, 'gatewayid' => $results['Token'] );
    }
    return array( 'status' => 'error', 'rawdata' => $results );
}
function sagepaytokens_3dsecure($params)
{
    $subdomain = $params['testmode'] ? 'test' : 'live';
    $url = "https://" . $subdomain . ".sagepay.com/gateway/service/vspdirect-register.vsp";
    $fields = array(  );
    $fields['VPSProtocol'] = "3.00";
    $fields['TxType'] = 'PAYMENT';
    $fields['Vendor'] = $params['vendorid'];
    $fields['VendorTxCode'] = $params['invoiceid'] . '-' . date('YmdHis');
    $fields['Amount'] = $params['amount'];
    $fields['Currency'] = $params['currency'];
    $fields['Description'] = $params['companyname'] . " - Invoice #" . $params['invoiceid'];
    $fields['Token'] = $params['gatewayid'];
    $fields['StoreToken'] = '1';
    if( $params['cccvv'] )
    {
        $fields['CV2'] = $params['cccvv'];
    }
    $fields['BillingSurname'] = $params['clientdetails']['lastname'];
    $fields['BillingFirstnames'] = $params['clientdetails']['firstname'];
    $fields['BillingAddress1'] = $params['clientdetails']['address1'];
    $fields['BillingAddress2'] = $params['clientdetails']['address2'];
    $fields['BillingCity'] = $params['clientdetails']['city'];
    if( $params['clientdetails']['country'] == 'US' )
    {
        $fields['BillingState'] = $params['clientdetails']['state'];
    }
    $fields['BillingPostCode'] = $params['clientdetails']['postcode'];
    $fields['BillingCountry'] = $params['clientdetails']['country'];
    $fields['BillingPhone'] = $params['clientdetails']['phonenumber'];
    $fields['CustomerEMail'] = $params['clientdetails']['email'];
    $fields['ClientIPAddress'] = $remote_ip;
    $fields['CardType'] = sagepaytokens_getcardtype($params['cardtype']);
    $results = sagepaytokens_call($url, $fields);
    $baseStatus = $results['Status'];
    switch( $baseStatus )
    {
        case '3DAUTH':
            logTransaction("SagePay Tokens 3DAuth", $results, 'Ok');
            $_SESSION['sagepayrepeatsinvoiceid'] = $params['invoiceid'];
            $code = "<form method=\"post\" action=\"" . $results['ACSURL'] . "\">\n            <input type=\"hidden\" name=\"PaReq\" value=\"" . $results['PAReq'] . "\">\n            <input type=\"hidden\" name=\"TermUrl\" value=\"" . $params['systemurl'] . "/modules/gateways/callback/sagepaytokens.php?invoiceid=" . $params['invoiceid'] . "\">\n            <input type=\"hidden\" name=\"MD\" value=\"" . $results['MD'] . "\">\n            <noscript>\n            <div class=\"errorbox\"><b>JavaScript is currently disabled or is not supported by your browser.</b><br />Please click the continue button to proceed with the processing of your transaction.</div>\n            <p align=\"center\"><input type=\"submit\" value=\"Continue >>\" /></p>\n            </noscript>\n            </form>";
            return $code;
            break;
        case 'OK':
            addInvoicePayment($params['invoiceid'], $response['VPSTxId'], '', '', 'sagepaytokens', 'on');
            logTransaction("SagePay Tokens 3DAuth", $results, 'Successful');
            sendMessage("Credit Card Payment Confirmation", $params['invoiceid']);
            $result = 'success';
            return $result;
            break;
        case 'NOTAUTHED':
            logTransaction("SagePay Tokens 3DAuth", $results, "Not Authed");
            sendMessage("Credit Card Payment Failed", $params['invoiceid']);
            break;
        case 'REJECTED':
            logTransaction("SagePay Tokens 3DAuth", $results, 'Rejected');
            sendMessage("Credit Card Payment Failed", $params['invoiceid']);
            break;
        case 'FAIL':
            logTransaction("SagePay Tokens 3DAuth", $results, 'Failed');
            sendMessage("Credit Card Payment Failed", $params['invoiceid']);
            break;
        default:
            logTransaction("SagePay Tokens 3DAuth", $results, 'Error');
            sendMessage("Credit Card Payment Failed", $params['invoiceid']);
            break;
    }
    $result = 'declined';
    return $result;
}
function sagepaytokens_capture($params)
{
    global $remote_ip;
    $subdomain = $params['testmode'] ? 'test' : 'live';
    $url = "https://" . $subdomain . ".sagepay.com/gateway/service/vspdirect-register.vsp";
    $fields = array(  );
    $fields['VPSProtocol'] = "3.00";
    $fields['TxType'] = 'PAYMENT';
    $fields['Vendor'] = $params['vendorid'];
    $fields['VendorTxCode'] = $params['invoiceid'] . '-' . date('YmdHis');
    $fields['Amount'] = $params['amount'];
    $fields['Currency'] = $params['currency'];
    $fields['Description'] = $params['companyname'] . " - Invoice #" . $params['invoiceid'];
    $fields['Token'] = $params['gatewayid'];
    $fields['StoreToken'] = '1';
    if( $params['cccvv'] )
    {
        $fields['CV2'] = $params['cccvv'];
    }
    $fields['BillingSurname'] = $params['clientdetails']['lastname'];
    $fields['BillingFirstnames'] = $params['clientdetails']['firstname'];
    $fields['BillingAddress1'] = $params['clientdetails']['address1'];
    $fields['BillingAddress2'] = $params['clientdetails']['address2'];
    $fields['BillingCity'] = $params['clientdetails']['city'];
    if( $params['clientdetails']['country'] == 'US' )
    {
        $fields['BillingState'] = $params['clientdetails']['state'];
    }
    $fields['BillingPostCode'] = $params['clientdetails']['postcode'];
    $fields['BillingCountry'] = $params['clientdetails']['country'];
    $fields['BillingPhone'] = $params['clientdetails']['phonenumber'];
    $fields['CustomerEMail'] = $params['clientdetails']['email'];
    $fields['ClientIPAddress'] = $remote_ip;
    $fields['CardType'] = sagepaytokens_getcardtype($params['cardtype']);
    $fields['ApplyAVSCV2'] = '2';
    $fields['Apply3DSecure'] = '2';
    $fields['AccountType'] = 'C';
    if( $params['cardtype'] == 'Maestro' || $params['cardtype'] == 'Solo' )
    {
        $fields['AccountType'] = 'M';
    }
    if( $params['cardtype'] == "American Express" || $params['cardtype'] == 'Laser' )
    {
        $fields['AccountType'] = 'E';
    }
    $results = sagepaytokens_call($url, $fields);
    if( $results['Status'] == 'OK' )
    {
        return array( 'status' => 'success', 'rawdata' => $results, 'transid' => $results['VPSTxId'] );
    }
    return array( 'status' => 'error', 'rawdata' => $results );
}
function sagepaytokens_call($url, $fields)
{
    $data = curlCall($url, $fields);
    $lines = explode("\n", $data);
    $results = array(  );
    foreach( $lines as $line )
    {
        $line = explode("=", $line, 2);
        $results[trim($line[0])] = trim($line[1]);
    }
    return $results;
}
function sagepaytokens_getcardtype($cardtype)
{
    if( $cardtype == 'Visa' )
    {
        $cardtype = 'VISA';
    }
    else
    {
        if( $cardtype == 'MasterCard' )
        {
            $cardtype = 'MC';
        }
        else
        {
            if( $cardtype == "American Express" )
            {
                $cardtype = 'AMEX';
            }
            else
            {
                if( $cardtype == "Diners Club" )
                {
                    $cardtype = 'DC';
                }
                else
                {
                    if( $cardtype == 'Discover' )
                    {
                        $cardtype = 'DC';
                    }
                    else
                    {
                        if( $cardtype == 'EnRoute' )
                        {
                            $cardtype = 'VISA';
                        }
                        else
                        {
                            if( $cardtype == 'JCB' )
                            {
                                $cardtype = 'JCB';
                            }
                            else
                            {
                                if( $cardtype == 'Delta' )
                                {
                                    $cardtype = 'DELTA';
                                }
                                else
                                {
                                    if( $cardtype == 'Solo' )
                                    {
                                        $cardtype = 'SOLO';
                                    }
                                    else
                                    {
                                        if( $cardtype == 'Switch' )
                                        {
                                            $cardtype = 'SWITCH';
                                        }
                                        else
                                        {
                                            if( $cardtype == 'Maestro' )
                                            {
                                                $cardtype = 'MAESTRO';
                                            }
                                            else
                                            {
                                                if( $cardtype == 'Electron' )
                                                {
                                                    $cardtype = 'UKE';
                                                }
                                                else
                                                {
                                                    if( $cardtype == "Visa Electron" )
                                                    {
                                                        $cardtype = 'UKE';
                                                    }
                                                    else
                                                    {
                                                        if( $cardtype == "Visa Delta" )
                                                        {
                                                            $cardtype = 'DELTA';
                                                        }
                                                        else
                                                        {
                                                            if( $cardtype == "Visa Debit" )
                                                            {
                                                                $cardtype = 'DELTA';
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return $cardtype;
}