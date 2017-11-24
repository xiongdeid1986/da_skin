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
$GATEWAY = getGatewayVariables('protxvspform');
if( !$GATEWAY['type'] )
{
    exit( "Module Not Activated" );
}
$strEncryptionPassword = $GATEWAY['xorencryptionpw'];
$strCrypt = $whmcs->get_req_var('crypt');
$cipher = new Crypt_AES();
$cipher->setKey($GATEWAY['xorencryptionpw']);
$cipher->setIV($GATEWAY['xorencryptionpw']);
$strDecoded = $cipher->decrypt(hex2bin(substr($strCrypt, 1)));
$values = getTokenX($strDecoded);
$strStatus = $values['Status'];
$strVendorTxCode = $values['VendorTxCode'];
$strVPSTxId = $values['VPSTxId'];
$invoiceId = (int) substr($strVendorTxCode, 14);
$invoiceId = checkCbInvoiceID($invoiceId, $GATEWAY['name']);
if( $strStatus == 'OK' )
{
    addInvoicePayment($invoiceId, $strVPSTxId, '', '', 'protxvspform');
    logTransaction($GATEWAY['name'], $values, 'Successful');
    redirSystemURL("id=" . $invoiceId . "&paymentsuccess=true", "viewinvoice.php");
}
else
{
    logTransaction($GATEWAY['name'], $values, 'Error');
    redirSystemURL("id=" . $invoiceId . "&paymentfailed=true", "viewinvoice.php");
}
/**
 * Format the provided string into a name -> value array
 *
 * @param string $thisString
 *
 * @return array
 */
function getTokenX($thisString)
{
    $tokens = array( 'Status', 'StatusDetail', 'VendorTxCode', 'VPSTxId', 'TxAuthNo', 'Amount', 'AVSCV2', 'AddressResult', 'PostCodeResult', 'CV2Result', 'GiftAid', '3DSecureStatus', 'CAVV', 'CardType', 'Last4Digits', 'DeclineCode', 'ExpiryDate', 'BankAuthCode' );
    $output = array(  );
    $resultArray = array(  );
    for( $i = count($tokens) - 1; 0 <= $i; $i-- )
    {
        $start = strpos($thisString, $tokens[$i]);
        if( $start !== false )
        {
            $resultArray[$i]->start = $start;
            $resultArray[$i]->token = $tokens[$i];
        }
    }
    sort($resultArray);
    for( $i = 0; $i < count($resultArray); $i++ )
    {
        $valueStart = $resultArray[$i]->start + strlen($resultArray[$i]->token) + 1;
        if( $i == count($resultArray) - 1 )
        {
            $output[$resultArray[$i]->token] = substr($thisString, $valueStart);
        }
        else
        {
            $valueLength = $resultArray[$i + 1]->start - $resultArray[$i]->start - strlen($resultArray[$i]->token) - 2;
            $output[$resultArray[$i]->token] = substr($thisString, $valueStart, $valueLength);
        }
    }
    return $output;
}