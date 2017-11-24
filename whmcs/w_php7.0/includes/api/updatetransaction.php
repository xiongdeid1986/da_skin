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
if( !function_exists('getClientsDetails') )
{
    require(ROOTDIR . "/includes/clientfunctions.php");
}
if( !function_exists('addTransaction') )
{
    require(ROOTDIR . "/includes/invoicefunctions.php");
}
$updateqry = array(  );
if( isset($_REQUEST['userid']) )
{
    $updateqry['userid'] = $_REQUEST['userid'];
}
if( isset($_REQUEST['currency']) )
{
    $updateqry['currency'] = $_REQUEST['currency'];
}
if( isset($_REQUEST['gateway']) )
{
    $updateqry['gateway'] = $_REQUEST['gateway'];
}
if( isset($_REQUEST['date']) )
{
    $updateqry['date'] = $_REQUEST['date'];
}
if( isset($_REQUEST['description']) )
{
    $updateqry['description'] = $_REQUEST['description'];
}
if( isset($_REQUEST['amountin']) )
{
    $updateqry['amountin'] = $_REQUEST['amountin'];
}
if( isset($_REQUEST['fees']) )
{
    $updateqry['fees'] = $_REQUEST['fees'];
}
if( isset($_REQUEST['amountout']) )
{
    $updateqry['amountout'] = $_REQUEST['amountout'];
}
if( isset($_REQUEST['rate']) )
{
    $updateqry['rate'] = $_REQUEST['rate'];
}
if( isset($_REQUEST['transid']) )
{
    $updateqry['transid'] = $_REQUEST['transid'];
}
if( isset($_REQUEST['invoiceid']) )
{
    $updateqry['invoiceid'] = $_REQUEST['invoiceid'];
}
if( isset($_REQUEST['refundid']) )
{
    $updateqry['refundid'] = $_REQUEST['refundid'];
}
update_query('tblaccounts', $updateqry, array( 'id' => $transactionid ));
$apiresults = array( 'result' => 'success', 'transactionid' => $transactionid );