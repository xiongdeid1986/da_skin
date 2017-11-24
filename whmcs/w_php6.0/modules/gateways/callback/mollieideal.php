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
$gatewaymodule = 'mollieideal';
$GATEWAY = getGatewayVariables($gatewaymodule);
if( !$GATEWAY['type'] )
{
    exit( "Module Not Activated" );
}
$invoiceid = urldecode($_GET['invoiceid']);
$transid = $_GET['transaction_id'];
$amount = urldecode($_GET['amount']);
$fee = urldecode($_GET['fee']);
checkCbTransID($transid);
if( isset($transid) )
{
    $iDEAL = new iDEAL_Payment($GATEWAY['partnerid']);
    $iDEAL->checkPayment($_GET['transaction_id']);
    if( $iDEAL->getPaidStatus() == true )
    {
        addInvoicePayment($invoiceid, $transid, $amount, $fee, $gatewaymodule);
        logTransaction($GATEWAY['name'], $_GET, 'Successful');
        return 1;
    }
    logTransaction($GATEWAY['name'], $_GET, 'Unsuccessful');
}