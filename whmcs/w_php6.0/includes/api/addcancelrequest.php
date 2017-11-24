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
if( !function_exists('updateInvoiceTotal') )
{
    require(ROOTDIR . "/includes/invoicefunctions.php");
}
if( !function_exists('createCancellationRequest') )
{
    require(ROOTDIR . "/includes/clientfunctions.php");
}
$result = select_query('tblhosting', 'id,userid', array( 'id' => $serviceid ));
$data = mysql_fetch_array($result);
$serviceid = $data[0];
$userid = $data[1];
if( !$serviceid )
{
    $apiresults = array( 'result' => 'error', 'message' => "Service ID Not Found" );
    return false;
}
$validtypes = array( 'Immediate', "End of Billing Period" );
if( !in_array($type, $validtypes) )
{
    $type = "End of Billing Period";
}
if( !$reason )
{
    $reason = "None Specified (API Submission)";
}
$result = createCancellationRequest($userid, $serviceid, $reason, $type);
if( $result == 'success' )
{
    $apiresults = array( 'result' => 'success', 'serviceid' => $serviceid, 'userid' => $userid );
}
else
{
    $apiresults = array( 'result' => 'error', 'message' => $result, 'serviceid' => $serviceid, 'userid' => $userid );
}