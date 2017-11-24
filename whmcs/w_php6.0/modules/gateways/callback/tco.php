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
$gatewaymodule = 'tco';
$GATEWAY = getGatewayVariables($gatewaymodule);
if( !$GATEWAY['type'] )
{
    exit( "Module Not Activated" );
}
if( $GATEWAY['secretword'] )
{
    $string_to_hash = $_REQUEST['sale_id'] . $GATEWAY['vendornumber'] . $_REQUEST['invoice_id'] . $GATEWAY['secretword'];
    $check_key = strtoupper(md5($string_to_hash));
    if( $check_key != $_POST['md5_hash'] )
    {
        logTransaction($GATEWAY['name'], $_POST, "MD5 Hash Failure");
        exit();
    }
}
$message_type = $_POST['message_type'];
$serviceid = $_POST['vendor_order_id'];
$transid = $_POST['sale_id'];
$recurringtransid = $transid . '-' . $_POST['invoice_id'];
$amount = $_POST['invoice_list_amount'] ? $_POST['invoice_list_amount'] : $_POST['item_list_amount_1'];
$recurstatus = trim($_POST['item_rec_status_1']);
$invoiceid = $_POST['item_id_1'] ? $_POST['item_id_1'] : $_POST['item_id_2'];
$currency = $_POST['list_currency'];
$message_type = $_POST['message_type'];
$fee = $amount * 0.055;
$pos = strpos($fee, ".");
$pos = $pos + 3;
$fee = substr($fee, 0, $pos);
$fee = $fee + 0.45;
if( $message_type == 'FRAUD_STATUS_CHANGED' && !$GATEWAY['skipfraudcheck'] )
{
    $fraud_status = $_POST['fraud_status'];
    if( $fraud_status == 'pass' )
    {
        if( $recurstatus && $serviceid )
        {
            $invoiceid = findInvoiceID($serviceid, $transid);
        }
        $invoiceid = checkCbInvoiceID($invoiceid, $GATEWAY['name']);
        logTransaction($GATEWAY['name'], $_POST, "Fraud Status Pass");
        checkCbTransID($transid);
        $amount = tcoconvertcurrency($amount, $currency, $invoiceid);
        $fee = tcoconvertcurrency($fee, $currency, $invoiceid);
        addInvoicePayment($invoiceid, $transid, $amount, $fee, $gatewaymodule);
    }
    else
    {
        logTransaction($GATEWAY['name'], $_POST, "Fraud Status Fail");
    }
}
else
{
    if( $message_type == 'ORDER_CREATED' && $GATEWAY['skipfraudcheck'] )
    {
        if( $recurstatus && $serviceid )
        {
            $invoiceid = findInvoiceID($serviceid, $transid);
        }
        $invoiceid = checkCbInvoiceID($invoiceid, $GATEWAY['name']);
        logTransaction($GATEWAY['name'], $_POST, "Payment Success");
        checkCbTransID($transid);
        $amount = tcoconvertcurrency($amount, $currency, $invoiceid);
        $fee = tcoconvertcurrency($fee, $currency, $invoiceid);
        addInvoicePayment($invoiceid, $transid, $amount, $fee, $gatewaymodule);
    }
    else
    {
        if( $message_type == 'RECURRING_INSTALLMENT_SUCCESS' )
        {
            $invoiceid = findInvoiceID($serviceid, $transid);
            checkCbTransID($recurringtransid);
            if( !$invoiceid && !$serviceid )
            {
                logTransaction($GATEWAY['name'], array_merge(array( 'InvoiceLookup' => "No Service ID Found in Callback" ), $_POST), "Recurring Error");
            }
            if( !$invoiceid )
            {
                logTransaction($GATEWAY['name'], array_merge(array( 'InvoiceLookup' => "No invoice match found for Service ID " . $serviceid . " or Subscription ID" ), $_POST), "Recurring Error");
            }
            logTransaction($GATEWAY['name'], $_POST, "Recurring Success");
            $amount = tcoconvertcurrency($amount, $currency, $invoiceid);
            $fee = tcoconvertcurrency($fee, $currency, $invoiceid);
            addInvoicePayment($invoiceid, $recurringtransid, $amount, $fee, $gatewaymodule);
            if( $serviceid && $transid )
            {
                update_query('tblhosting', array( 'subscriptionid' => $transid ), array( 'id' => $serviceid ));
                return 1;
            }
        }
        else
        {
            if( $message_type == 'RECURRING_INSTALLMENT_FAILED' )
            {
                logTransaction($GATEWAY['name'], $_POST, "Recurring Failed");
                return 1;
            }
            logTransaction($GATEWAY['name'], $_POST, "Notification Only");
        }
    }
}
function tcoconvertcurrency($amount, $currency, $invoiceid)
{
    $result = select_query('tblcurrencies', 'id', array( 'code' => $currency ));
    $data = mysql_fetch_array($result);
    $currencyid = $data['id'];
    if( !$currencyid )
    {
        logTransaction($GATEWAY['name'], $_POST, "Unrecognised Currency");
        exit();
    }
    $result = select_query('tblinvoices', 'userid,total', array( 'id' => $invoiceid ));
    $data = mysql_fetch_array($result);
    $userid = $data['userid'];
    $total = $data['total'];
    $currency = getCurrency($userid);
    if( $currencyid != $currency['id'] )
    {
        $amount = convertCurrency($amount, $currencyid, $currency['id']);
        if( $total < $amount + 1 && $amount - 1 < $total )
        {
            $amount = $total;
        }
    }
    return $amount;
}