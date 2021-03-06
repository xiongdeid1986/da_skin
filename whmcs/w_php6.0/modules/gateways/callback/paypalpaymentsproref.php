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
require("../../../init.php");
$whmcs->load_function('gateway');
$whmcs->load_function('invoice');
$whmcs->load_function('client');
$whmcs->load_function('cc');
$gateway = WHMCS_Module_Gateway::factory('paypalpaymentsproref');
$gatewayParams = $gateway->getParams();
$callbacksuccess = false;
$pares = $_REQUEST['PaRes'];
$invoiceid = $_REQUEST['MD'];
if( strcasecmp('', $pares) != 0 && $pares != null && isset($_SESSION['Centinel_TransactionId']) )
{
    if( $gatewayParams['sandbox'] )
    {
        $mapurl = "https://centineltest.cardinalcommerce.com/maps/txns.asp";
    }
    else
    {
        $mapurl = "https://paypal.cardinalcommerce.com/maps/txns.asp";
    }
    $currency = '';
    if( $gatewayParams['currency'] == 'USD' )
    {
        $currency = '840';
    }
    if( $gatewayParams['currency'] == 'GBP' )
    {
        $currency = '826';
    }
    if( $gatewayParams['currency'] == 'EUR' )
    {
        $currency = '978';
    }
    if( $gatewayParams['currency'] == 'CAD' )
    {
        $currency = '124';
    }
    $postfields = array(  );
    $postfields['MsgType'] = 'cmpi_authenticate';
    $postfields['Version'] = "1.7";
    $postfields['ProcessorId'] = $gatewayParams['processorid'];
    $postfields['MerchantId'] = $gatewayParams['merchantid'];
    $postfields['TransactionPwd'] = $gatewayParams['transpw'];
    $postfields['TransactionType'] = 'C';
    $postfields['PAResPayload'] = $pares;
    $postfields['OrderId'] = $_SESSION['Centinel_OrderId'];
    $postfields['TransactionId'] = $_SESSION['Centinel_TransactionId'];
    $queryString = "<CardinalMPI>\n";
    foreach( $postfields as $name => $value )
    {
        $queryString .= "<" . $name . ">" . $value . "</" . $name . ">\n";
    }
    $queryString .= "</CardinalMPI>";
    $data = "cmpi_msg=" . urlencode($queryString);
    $response = curlCall($mapurl, $data);
    $xmlarray = XMLtoArray($response);
    $xmlarray = $xmlarray['CARDINALMPI'];
    $errorno = $xmlarray['ERRORNO'];
    $paresstatus = $xmlarray['PARESSTATUS'];
    $sigverification = $xmlarray['SIGNATUREVERIFICATION'];
    $cavv = $xmlarray['CAVV'];
    $eciflag = $xmlarray['ECIFLAG'];
    $xid = $xmlarray['XID'];
    if( (strcasecmp('0', $errorno) == 0 || strcasecmp('1140', $errorno) == 0) && strcasecmp('Y', $sigverification) == 0 && (strcasecmp('Y', $paresstatus) == 0 || strcasecmp('A', $paresstatus) == 0) )
    {
        logTransaction("PayPal Pro Reference 3D Secure Callback", $_REQUEST, "Auth Passed");
        $auth = array( 'paresstatus' => $paresstatus, 'cavv' => $cavv, 'eciflag' => $eciflag, 'xid' => $xid );
        $params = getCCVariables($invoiceid);
        if( isset($_SESSION['Centinel_Details']) )
        {
            $params['cardtype'] = $_SESSION['Centinel_Details']['cardtype'];
            $params['cardnum'] = $_SESSION['Centinel_Details']['cardnum'];
            $params['cardexp'] = $_SESSION['Centinel_Details']['cardexp'];
            $params['cccvv'] = $_SESSION['Centinel_Details']['cccvv'];
            $params['cardstart'] = $_SESSION['Centinel_Details']['cardstart'];
            $params['cardissuenum'] = $_SESSION['Centinel_Details']['cardissuenum'];
            unset($_SESSION['Centinel_Details']);
        }
        $result = paypalpaymentsproref_capture($params, $auth);
        if( $result['status'] == 'success' )
        {
            logTransaction("PayPal Pro Reference 3D Capture", $result['rawdata'], 'Successful');
            addInvoicePayment($invoiceid, $result['transid'], '', '', 'paypalpaymentsproref', 'on');
            sendMessage("Credit Card Payment Confirmation", $invoiceid);
            $callbacksuccess = true;
        }
        else
        {
            logTransaction("PayPal Pro Reference 3D Capture", $result['rawdata'], 'Failed');
        }
    }
    else
    {
        if( strcasecmp('N', $paresstatus) == 0 )
        {
            logTransaction("PayPal Pro Reference 3D Secure Callback", $_REQUEST, "Auth Failed");
        }
        else
        {
            logTransaction("PayPal Pro Reference 3D Secure Callback", $_REQUEST, "Unexpected Status, Capture Anyway");
            $auth = array( 'paresstatus' => $paresstatus, 'cavv' => $cavv, 'eciflag' => $eciflag, 'xid' => $xid );
            $params = getCCVariables($invoiceid);
            if( isset($_SESSION['Centinel_Details']) )
            {
                $params['cardtype'] = $_SESSION['Centinel_Details']['cardtype'];
                $params['cardnum'] = $_SESSION['Centinel_Details']['cardnum'];
                $params['cardexp'] = $_SESSION['Centinel_Details']['cardexp'];
                $params['cccvv'] = $_SESSION['Centinel_Details']['cccvv'];
                $params['cardstart'] = $_SESSION['Centinel_Details']['cardstart'];
                $params['cardissuenum'] = $_SESSION['Centinel_Details']['cardissuenum'];
                unset($_SESSION['Centinel_Details']);
            }
            $result = paypalpaymentsproref_capture($params, $auth);
            if( $result['status'] == 'success' )
            {
                logTransaction("PayPal Pro Reference 3D Capture", $result['rawdata'], 'Successful');
                addInvoicePayment($invoiceid, $result['transid'], '', '', 'paypalpaymentsproref', 'on');
                sendMessage("Credit Card Payment Confirmation", $invoiceid);
                $callbacksuccess = true;
            }
            else
            {
                logTransaction("PayPal Pro Reference 3D Capture", $result['rawdata'], 'Failed');
            }
        }
    }
}
else
{
    logTransaction("PayPal Pro Reference 3D Secure Callback", $_REQUEST, 'Error');
}
if( !$callbacksuccess )
{
    sendMessage("Credit Card Payment Failed", $invoiceid);
}
callback3DSecureRedirect($invoiceid, $callbacksuccess);