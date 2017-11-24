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
$GATEWAY = getGatewayVariables('paypal');
if( !$GATEWAY['type'] )
{
    exit( "Module Not Activated" );
}
$postipn = "cmd=_notify-validate";
$orgipn = '';
foreach( $_POST as $key => $value )
{
    $orgipn .= $key . " => " . $value . "\n";
    $postipn .= "&" . $key . "=" . urlencode(WHMCS_Input_Sanitize::decode($value));
}
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.paypal.com/cgi-bin/webscr");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postipn);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 100);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_USERAGENT, "WHMCS V" . $whmcs->getVersion()->getCasual());
$reply = curl_exec($ch);
curl_close($ch);
if( !strcmp($reply, 'VERIFIED') )
{
    $paypalemail = $_POST['receiver_email'];
    $payment_status = $_POST['payment_status'];
    $subscr_id = $_POST['subscr_id'];
    $txn_type = $_POST['txn_type'];
    $txn_id = $_POST['txn_id'];
    $mc_gross = $_POST['mc_gross'];
    $mc_fee = $_POST['mc_fee'];
    $idnumber = $_POST['custom'];
    $paypalcurrency = $_REQUEST['mc_currency'];
    $paypalemails = explode(',', strtolower($GATEWAY['email']));
    array_walk($paypalemails, 'paypal_email_trim');
    if( !in_array(strtolower($paypalemail), $paypalemails) )
    {
        logTransaction('PayPal', $orgipn, "Invalid Receiver Email");
        exit();
    }
    if( $payment_status == 'Pending' )
    {
        logTransaction('PayPal', $orgipn, 'Pending');
        exit();
    }
    if( $txn_id )
    {
        checkCbTransID($txn_id);
    }
    if( !is_numeric($idnumber) )
    {
        $idnumber = '';
    }
    if( $txn_type == 'web_accept' && $_POST['invoice'] && $payment_status == 'Completed' )
    {
        update_query('tblaccounts', array( 'fees' => $mc_fee ), array( 'transid' => $txn_id ));
    }
    $result = select_query('tblcurrencies', '', array( 'code' => $paypalcurrency ));
    $data = mysql_fetch_array($result);
    $paypalcurrencyid = $data['id'];
    $currencyconvrate = $data['rate'];
    if( !$paypalcurrencyid )
    {
        logTransaction('PayPal', $orgipn, "Unrecognised Currency");
        exit();
    }
    switch( $txn_type )
    {
        case 'subscr_signup':
            logTransaction('PayPal', $orgipn, "Subscription Signup");
            exit();
            break;
        case 'subscr_cancel':
            update_query('tblhosting', array( 'subscriptionid' => '' ), array( 'subscriptionid' => $subscr_id ));
            logTransaction('PayPal', $orgipn, "Subscription Cancelled");
            exit();
            break;
        case 'subscr_payment':
            if( $payment_status != 'Completed' )
            {
                logTransaction('PayPal', $orgipn, 'Incomplete');
                exit();
            }
            $query = "SELECT tblinvoices.id,tblinvoices.userid FROM tblinvoiceitems INNER JOIN tblinvoices ON tblinvoices.id=tblinvoiceitems.invoiceid WHERE tblinvoiceitems.relid=" . (int) $idnumber . " AND tblinvoiceitems.type='Hosting' AND tblinvoices.status='Unpaid' ORDER BY tblinvoices.id ASC";
            $result = full_query($query);
            $data = mysql_fetch_array($result);
            $invoiceid = $data['id'];
            $userid = $data['userid'];
            if( $invoiceid )
            {
                $orgipn .= "Invoice Found from Product ID Match => " . $invoiceid . "\n";
            }
            else
            {
                $query = "SELECT tblinvoiceitems.invoiceid,tblinvoices.userid FROM tblhosting INNER JOIN tblinvoiceitems ON tblhosting.id=tblinvoiceitems.relid INNER JOIN tblinvoices ON tblinvoices.id=tblinvoiceitems.invoiceid WHERE tblinvoices.status='Unpaid' AND tblhosting.subscriptionid='" . db_escape_string($subscr_id) . "' AND tblinvoiceitems.type='Hosting' ORDER BY tblinvoiceitems.invoiceid ASC";
                $result = full_query($query);
                $data = mysql_fetch_array($result);
                $invoiceid = $data['invoiceid'];
                $userid = $data['userid'];
                if( $invoiceid )
                {
                    $orgipn .= "Invoice Found from Subscription ID Match => " . $invoiceid . "\n";
                }
            }
            if( !$invoiceid )
            {
                $query = "SELECT tblinvoices.id,tblinvoices.userid FROM tblinvoiceitems INNER JOIN tblinvoices ON tblinvoices.id=tblinvoiceitems.invoiceid WHERE tblinvoiceitems.relid=" . (int) $idnumber . " AND tblinvoiceitems.type='Hosting' AND tblinvoices.status='Paid' ORDER BY tblinvoices.id DESC";
                $result = full_query($query);
                $data = mysql_fetch_array($result);
                $invoiceid = $data['id'];
                $userid = $data['userid'];
                if( $invoiceid )
                {
                    $orgipn .= "Paid Invoice Found from Product ID Match => " . $invoiceid . "\n";
                }
            }
            break;
        case 'web_accept':
            if( $payment_status != 'Completed' )
            {
                logTransaction('PayPal', $orgipn, 'Incomplete');
                exit();
            }
            $result = select_query('tblinvoices', '', array( 'id' => $idnumber ));
            $data = mysql_fetch_array($result);
            $invoiceid = $data['id'];
            $userid = $data['userid'];
            break;
    }
    if( $invoiceid )
    {
        logTransaction('PayPal', $orgipn, 'Successful');
        $currency = getCurrency($userid);
        if( $paypalcurrencyid != $currency['id'] )
        {
            $mc_gross = convertCurrency($mc_gross, $paypalcurrencyid, $currency['id']);
            $mc_fee = convertCurrency($mc_fee, $paypalcurrencyid, $currency['id']);
            $result = select_query('tblinvoices', 'total', array( 'id' => $invoiceid ));
            $data = mysql_fetch_array($result);
            $total = $data['total'];
            if( $total < $mc_gross + 1 && $mc_gross - 1 < $total )
            {
                $mc_gross = $total;
            }
        }
        addInvoicePayment($invoiceid, $txn_id, $mc_gross, $mc_fee, 'paypal');
        $result = select_query('tblinvoiceitems', '', array( 'invoiceid' => $invoiceid, 'type' => 'Hosting' ));
        $data = mysql_fetch_array($result);
        $relid = $data['relid'];
        update_query('tblhosting', array( 'subscriptionid' => $subscr_id ), array( 'id' => $relid ));
        exit();
    }
    if( $txn_type == 'subscr_payment' )
    {
        $result = select_query('tblhosting', 'userid', array( 'subscriptionid' => $subscr_id ));
        $data = mysql_fetch_array($result);
        $userid = $data['userid'];
        if( $userid )
        {
            $orgipn .= "User ID Found from Subscription ID Match: User ID => " . $userid . "\n";
            insert_query('tblaccounts', array( 'userid' => $userid, 'currency' => $paypalcurrencyid, 'gateway' => 'paypal', 'date' => "now()", 'description' => "PayPal Subscription Payment", 'amountin' => $mc_gross, 'fees' => $mc_fee, 'rate' => $currencyconvrate, 'transid' => $txn_id ));
            insert_query('tblcredit', array( 'clientid' => $userid, 'date' => "now()", 'description' => "PayPal Subscription Transaction ID " . $txn_id, 'amount' => $mc_gross ));
            update_query('tblclients', array( 'credit' => "+=" . $mc_gross ), array( 'id' => (int) $userid ));
            logTransaction('PayPal', $orgipn, "Credit Added");
        }
        else
        {
            logTransaction('PayPal', $orgipn, "Invoice Not Found");
        }
    }
    else
    {
        logTransaction('PayPal', $orgipn, "Not Supported");
    }
}
else
{
    if( !strcmp($reply, 'INVALID') )
    {
        logTransaction('PayPal', $orgipn, "IPN Handshake Invalid");
        header("HTTP/1.0 406 Not Acceptable");
        exit();
    }
    logTransaction('PayPal', $orgipn . "\n" . "\nIPN Handshake Response => " . $reply, "IPN Handshake Error");
    header("HTTP/1.0 406 Not Acceptable");
    exit();
}
function paypal_email_trim(&$value)
{
    $value = trim($value);
}