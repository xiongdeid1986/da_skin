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
$gateway = WHMCS_Module_Gateway::factory('paymentexpress');
$gatewayParams = $gateway->getParams();
logTransaction("Payment Express", $_REQUEST, 'Received');
$url = "https://sec.paymentexpress.com/pxpay/pxaccess.aspx";
$xml = "<ProcessResponse>\n<PxPayUserId>" . $gatewayParams['pxpayuserid'] . "</PxPayUserId>\n<PxPayKey>" . $gatewayParams['pxpaykey'] . "</PxPayKey>\n<Response>" . WHMCS_Input_Sanitize::decode($_REQUEST['result']) . "</Response>\n</ProcessResponse>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
$outputXml = curl_exec($ch);
curl_close($ch);
$xmlresponse = XMLtoArray($outputXml);
$xmlresponse = $xmlresponse['RESPONSE'];
$success = $xmlresponse['SUCCESS'];
$invoiceid = (int) $xmlresponse['TXNDATA1'];
$transid = $xmlresponse['TXNID'];
if( $xmlresponse['SUCCESS'] == '1' )
{
    $invoiceid = checkCbInvoiceID($invoiceid, "Payment Express");
    $result = select_query('tblaccounts', 'invoiceid', array( 'transid' => $transid ));
    $data = mysql_fetch_array($result);
    $transinvoiceid = $data['invoiceid'];
    if( $transinvoiceid )
    {
        redirSystemURL("id=" . $invoiceid . "&paymentsuccess=true", "viewinvoice.php");
    }
    addInvoicePayment($invoiceid, $transid, '', '', 'paymentexpress');
    logTransaction("Payment Express", array_merge($_REQUEST, $xmlresponse), 'Successful');
    redirSystemURL("id=" . $invoiceid . "&paymentsuccess=true", "viewinvoice.php");
}
else
{
    logTransaction("Payment Express", array_merge($_REQUEST, $xmlresponse), 'Unsuccessful');
    if( $invoiceid )
    {
        redirSystemURL("id=" . $invoiceid . "&paymentfailed=true", "viewinvoice.php");
    }
    else
    {
        redirSystemURL("action=invoices", "clientarea.php");
    }
}