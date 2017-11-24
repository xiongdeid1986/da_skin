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
$GATEWAY = getGatewayVariables('ccavenue');
if( !$GATEWAY['type'] )
{
    exit( "Module Not Activated" );
}
$Order_Id = $_POST['Order_Id'];
$WorkingKey = $GATEWAY['workingkey'];
$Amount = $_POST['Amount'];
$AuthDesc = $_POST['AuthDesc'];
$Checksum = $_POST['Checksum'];
$Merchant_Id = $_POST['Merchant_Id'];
$signup = $_POST['Merchant_Param'];
$Checksum = ccavenue_verifyChecksum($Merchant_Id, $Order_Id, $Amount, $AuthDesc, $Checksum, $WorkingKey);
$invoiceid = explode('_', $Order_Id);
$invoiceid = $invoiceid[0];
$invoiceid = checkCbInvoiceID($invoiceid, 'CCAvenue');
if( $Checksum == 'true' && $AuthDesc == 'Y' )
{
    addInvoicePayment($invoiceid, $Order_Id, '', '', 'ccavenue');
    logTransaction('CCAvenue', $_REQUEST, 'Successful');
    redirSystemURL("id=" . $invoiceid . "&paymentsuccess=true", "viewinvoice.php");
}
else
{
    logTransaction('CCAvenue', $_REQUEST, 'Error');
    redirSystemURL("id=" . $invoiceid . "&paymentfailed=true", "viewinvoice.php");
}
function ccavenue_verifychecksum($MerchantId, $OrderId, $Amount, $AuthDesc, $CheckSum, $WorkingKey)
{
    $str = $MerchantId . "|" . $OrderId . "|" . $Amount . "|" . $AuthDesc . "|" . $WorkingKey;
    $adler = 1;
    $adler = ccavenuecb_adler32($adler, $str);
    if( $adler == $CheckSum )
    {
        return 'true';
    }
    return 'false';
}
function ccavenuecb_adler32($adler, $str)
{
    $BASE = 65521;
    $s1 = $adler & 65535;
    $s2 = $adler >> 16 & 65535;
    for( $i = 0; $i < strlen($str); $i++ )
    {
        $s1 = ($s1 + Ord($str[$i])) % $BASE;
        $s2 = ($s2 + $s1) % $BASE;
    }
    return ccavenuecb_leftshift($s2, 16) + $s1;
}
function ccavenuecb_leftshift($str, $num)
{
    $str = DecBin($str);
    for( $i = 0; $i < 64 - strlen($str); $i++ )
    {
        $str = '0' . $str;
    }
    for( $i = 0; $i < $num; $i++ )
    {
        $str = $str . '0';
        $str = substr($str, 1);
    }
    return ccavenuecb_cdec($str);
}
function ccavenuecb_cdec($num)
{
    for( $n = 0; $n < strlen($num); $n++ )
    {
        $temp = $num[$n];
        $dec = $dec + $temp * pow(2, strlen($num) - $n - 1);
    }
    return $dec;
}