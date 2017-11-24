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
$GATEWAYMODULE['epathname'] = 'epath';
$GATEWAYMODULE['epathvisiblename'] = 'e-Path';
$GATEWAYMODULE['epathtype'] = 'Invoices';
function epath_activate()
{
    defineGatewayField('epath', 'text', 'submiturl', "http://e-path.com.au/demo1/demo1/demo1.php", "Submit URL", '50', "Your unique secure e-Path payment page");
    defineGatewayField('epath', 'text', 'returl', "http://www.yourdomain.com/success.html", "Return URL", '50', "The URL you want users returning to once complete");
}
function epath_link($params)
{
    $invoiceid = $params['invoiceid'];
    $invoicetotal = $params['amount'];
    $result = select_query('tblinvoiceitems', '', array( 'invoiceid' => (int) $invoiceid, 'type' => 'Hosting' ));
    $data = mysql_fetch_array($result);
    $relid = $data['relid'];
    $result = select_query('tblhosting', 'billingcycle', array( 'id' => (int) $relid ));
    $data = mysql_fetch_array($result);
    if( $data )
    {
        $billingcycle = $data['billingcycle'];
    }
    else
    {
        $billingcycle = "Only Only";
    }
    $description = preg_replace("/[^A-Za-z0-9 -]/", '', $params['description']);
    $code = "<form action=\"" . $params['submiturl'] . "\" method=\"post\" name=\"\">\n<input type=\"hidden\" name=\"ord\" value=\"" . $params['invoiceid'] . "\">\n<input type=\"hidden\" name=\"des\" value=\"" . $description . "\">\n<input type=\"hidden\" name=\"amt\" value=\"" . $params['amount'] . "\">\n<input type=\"hidden\" name=\"frq\" value=\"" . $billingcycle . "\">\n<input type=\"hidden\" name=\"ceml\" value=\"" . $params['clientdetails']['email'] . "\">\n<input type=\"hidden\" name=\"ret\" value=\"" . $params['returl'] . "\">\n<input type=\"submit\" name=\"\" value=\"" . $params['langpaynow'] . "\">\n</form>";
    return $code;
}