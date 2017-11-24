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
function imsp_config()
{
    $configarray = array( 'FriendlyName' => array( 'Type' => 'System', 'Value' => 'IMSP' ), 'merchantid' => array( 'FriendlyName' => "Merchant ID", 'Type' => 'text', 'Size' => '25' ), 'terminalid' => array( 'FriendlyName' => "Terminal ID", 'Type' => 'text', 'Size' => '25' ), 'passcode' => array( 'FriendlyName' => 'Passcode', 'Type' => 'text', 'Size' => '25' ), 'testmode' => array( 'FriendlyName' => "Test Mode", 'Type' => 'yesno' ) );
    return $configarray;
}
function imsp_3dsecure($params)
{
    global $remote_ip;
    $url = "https://test.imsp.com/staging/Request3DS.aspx";
    $currency = '978';
    $amount = str_pad($params['amount'] * 100, 12, '0', STR_PAD_LEFT);
    $signature = $params['passcode'] . $params['merchantid'] . $params['terminalid'] . $params['invoiceid'] . $params['passcode'] . $amount;
    $signature = sha1($signature);
    $postfields = array(  );
    $postfields['merchantid'] = $params['merchantid'];
    $postfields['terminalid'] = $params['terminalid'];
    $postfields['trxntype'] = 'Sale';
    $postfields['cardnumber'] = $params['cardnum'];
    $postfields['expirydate'] = $params['cardexp'];
    if( $params['cccvv'] )
    {
        $postfields['cardvervalue'] = $params['cccvv'];
    }
    $postfields['amount'] = $amount;
    $postfields['currency'] = $currency;
    $postfields['batchnumber'] = $params['invoiceid'];
    $postfields['invoicenumber'] = $params['invoiceid'];
    $postfields['ipaddress'] = $remote_ip;
    $postfields['signature'] = $signature;
    $postfields['responseurl'] = $params['systemurl'] . "/modules/gateways/callback/imsp.php";
    $data = curlCall($url, $postfields);
    $resultstemp = explode(';', $data);
    $results = array(  );
    foreach( $resultstemp as $v )
    {
        $v = explode("|", $v);
        if( $v[0] )
        {
            $results[$v[0]] = $v[1];
        }
    }
    print_r($results);
    $responsecode = $results['responsecode'];
    $responsereasoncode = $results['responsereasoncode'];
    $trxnid = $results['trxnid'];
    $acsurl = '';
    $pareq = '';
    $termurl = '';
    $Md = '';
    if( $responsecode == '5' && $responsereasoncode == '18' )
    {
        logTransaction("IMSP 3D Secure", $results, "3D Auth Forward");
        $code = "<form method=\"POST\" action=\"" . $acsurl . "\">\n                <input type=hidden name=\"PaReq\" value=\"" . $pareq . "\">\n                <input type=hidden name=\"TermUrl\" value=\"" . $termurl . "\">\n                <input type=hidden name=\"MD\" value=\"" . $Md . "\">\n                <noscript>\n                <center>\n                    <font color=\"red\">\n                        <h2>Processing your Payer Authentication Transaction</h2>\n                        <h3>JavaScript is currently disabled or is not supported by your browser.<br></h3>\n                        <h4>Please click Submit to continue the processing of your transaction.</h4>\n                    </font>\n                <input type=\"submit\" value=\"Continue\">\n                </center>\n                </noscript>\n            </form>";
        return $code;
    }
    if( $responsecode == '1' )
    {
        logTransaction("IMSP 3D Secure", $results, 'Successful');
        addInvoicePayment($params['invoiceid'], $trxnid, '', '', 'imsp', 'on');
        sendMessage("Credit Card Payment Confirmation", $params['invoiceid']);
        redir("id=" . $params['invoiceid'] . "&paymentsuccess=true", "viewinvoice.php");
    }
    else
    {
        if( $responsecode == '2' )
        {
            logTransaction("IMSP 3D Secure", $results, 'Declined');
        }
        else
        {
            if( $responsecode == '3' )
            {
                logTransaction("IMSP 3D Secure", $results, "Parse Error");
            }
            else
            {
                logTransaction("IMSP 3D Secure", $results, "System Error");
            }
        }
    }
    return 'declined';
}
function imsp_capture($params)
{
    return array( 'status' => 'error', 'rawdata' => "Not Supported" );
}