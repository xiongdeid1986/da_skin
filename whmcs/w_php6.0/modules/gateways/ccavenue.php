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
$GATEWAYMODULE['ccavenuename'] = 'ccavenue';
$GATEWAYMODULE['ccavenuevisiblename'] = 'CCAvenue';
$GATEWAYMODULE['ccavenuetype'] = 'Invoices';
function ccavenue_activate()
{
    defineGatewayField('ccavenue', 'text', 'merchantid', '', "Merchant ID", '20', "Enter your User ID for CCAvenue here");
    defineGatewayField('ccavenue', 'text', 'workingkey', '', "Working Key", '40', "Enter the Working Key here");
    defineGatewayField('ccavenue', 'text', 'infomsg', '', "Information Message", '125', "<br />An optional message to be displayed on the Invoice Payment client area screen informing of a manual review before the invoice is marked paid.");
}
function ccavenue_link($params)
{
    $Merchant_Id = $params['merchantid'];
    $Amount = sprintf("%.2f", $params['amount']);
    $Order_Id = $params['invoiceid'] . '_' . date('YmdHis');
    $Redirect_Url = $params['systemurl'] . "/modules/gateways/callback/ccavenue.php";
    $WorkingKey = $params['workingkey'];
    $Checksum = ccavenue_getCheckSum($Merchant_Id, $Amount, $Order_Id, $Redirect_Url, $WorkingKey);
    $strRet = "<form name=ccavenue method=\"post\" action=\"https://www.ccavenue.com/shopzone/cc_details.jsp\">";
    $strRet .= "<input type=hidden name=Merchant_Id value=\"" . $Merchant_Id . "\">";
    $strRet .= "<input type=hidden name=Amount value=\"" . $Amount . "\">";
    $strRet .= "<input type=hidden name=Order_Id value=\"" . $Order_Id . "\">";
    $strRet .= "<input type=hidden name=Redirect_Url value=\"" . $Redirect_Url . "\">";
    $strRet .= "<input type=hidden name=Checksum value=\"" . $Checksum . "\">";
    $strRet .= "<input type=\"hidden\" name=\"billing_cust_name\" value=\"" . $params['clientdetails']['firstname'] . " " . $params['clientdetails']['lastname'] . "\">";
    $strRet .= "<input type=\"hidden\" name=\"billing_cust_address\" value=\"" . $params['clientdetails']['address1'] . "\">";
    $strRet .= "<input type=\"hidden\" name=\"billing_cust_country\" value=\"" . $params['clientdetails']['country'] . "\">";
    $strRet .= "<input type=\"hidden\" name=\"billing_cust_tel\" value=\"" . $params['clientdetails']['phonenumber'] . "\">";
    $strRet .= "<input type=\"hidden\" name=\"billing_cust_email\" value=\"" . $params['clientdetails']['email'] . "\">";
    $strRet .= "<input type=\"hidden\" name=\"delivery_cust_name\" value=\"" . $params['clientdetails']['firstname'] . " " . $params['clientdetails']['lastname'] . "\">";
    $strRet .= "<input type=\"hidden\" name=\"delivery_cust_address\" value=\"" . $params['clientdetails']['address1'] . "\">";
    $strRet .= "<input type=\"hidden\" name=\"delivery_cust_tel\" value=\"" . $params['clientdetails']['phonenumber'] . "\">";
    $strRet .= "<input type=\"hidden\" name=\"delivery_cust_notes\" value=\"Invoice #" . $Order_Id . "\">";
    $strRet .= "<input type=\"submit\" value=\"" . $params['langpaynow'] . "\">";
    $strRet .= "</form>";
    $strRet .= "<br />" . $params['infomsg'];
    return $strRet;
}
function ccavenue_getchecksum($MerchantId, $Amount, $OrderId, $URL, $WorkingKey)
{
    $str = $MerchantId . "|" . $OrderId . "|" . $Amount . "|" . $URL . "|" . $WorkingKey;
    $adler = 1;
    $adler = ccavenue_adler32($adler, $str);
    return $adler;
}
function ccavenue_adler32($adler, $str)
{
    $BASE = 65521;
    $s1 = $adler & 65535;
    $s2 = $adler >> 16 & 65535;
    for( $i = 0; $i < strlen($str); $i++ )
    {
        $s1 = ($s1 + Ord($str[$i])) % $BASE;
        $s2 = ($s2 + $s1) % $BASE;
    }
    return ccavenue_leftshift($s2, 16) + $s1;
}
function ccavenue_leftshift($str, $num)
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
    return ccavenue_cdec($str);
}
function ccavenue_cdec($num)
{
    for( $n = 0; $n < strlen($num); $n++ )
    {
        $temp = $num[$n];
        $dec = $dec + $temp * pow(2, strlen($num) - $n - 1);
    }
    return $dec;
}