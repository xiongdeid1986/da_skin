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
$result = select_query('tblorders', 'id,userid,ipaddress,invoiceid', array( 'id' => $orderid ));
$data = mysql_fetch_array($result);
$orderid = $data[0];
if( !$orderid )
{
    $apiresults = array( 'result' => 'error', 'message' => "Order ID Not Found" );
    return false;
}
$userid = $data['userid'];
$ipaddress = $data['ipaddress'];
$invoiceid = $data['invoiceid'];
if( isset($_REQUEST['ipaddress']) )
{
    $ipaddress = $_REQUEST['ipaddress'];
}
$fraudmodule = 'maxmind';
$results = $fraudresults = '';
$fraud = new WHMCS_Module_Fraud();
if( $fraud->load($fraudmodule) )
{
    $results = $fraud->doFraudCheck($orderid, $userid, $ipaddress);
    $fraudresults = $fraud->processResultsForDisplay($orderid, $results['fraudoutput']);
}
if( !is_array($results) )
{
    $results = array(  );
}
$error = $results['error'];
if( $results['userinput'] )
{
    $status = "User Input Required";
}
else
{
    if( $results['error'] )
    {
        $status = 'Fail';
        update_query('tblorders', array( 'status' => 'Fraud' ), array( 'id' => $orderid ));
        $result = select_query('tblhosting', 'id', array( 'orderid' => $orderid ));
        while( $data = mysql_fetch_array($result) )
        {
            update_query('tblhosting', array( 'domainstatus' => 'Fraud' ), array( 'id' => $data['id'], 'domainstatus' => 'Pending' ));
        }
        $result = select_query('tblhostingaddons', 'id', array( 'orderid' => $orderid ));
        while( $data = mysql_fetch_array($result) )
        {
            update_query('tblhostingaddons', array( 'status' => 'Fraud' ), array( 'id' => $data['id'], 'status' => 'Pending' ));
        }
        $result = select_query('tbldomains', 'id', array( 'orderid' => $orderid ));
        while( $data = mysql_fetch_array($result) )
        {
            update_query('tbldomains', array( 'status' => 'Fraud' ), array( 'id' => $data['id'], 'status' => 'Pending' ));
        }
        update_query('tblinvoices', array( 'status' => 'Cancelled' ), array( 'id' => $invoiceid, 'status' => 'Unpaid' ));
    }
    else
    {
        $status = 'Pass';
        update_query('tblorders', array( 'status' => 'Pending' ), array( 'id' => $orderid ));
        $result = select_query('tblhosting', 'id', array( 'orderid' => $orderid ));
        while( $data = mysql_fetch_array($result) )
        {
            update_query('tblhosting', array( 'domainstatus' => 'Pending' ), array( 'id' => $data['id'], 'domainstatus' => 'Fraud' ));
        }
        $result = select_query('tblhostingaddons', 'id', array( 'orderid' => $orderid ));
        while( $data = mysql_fetch_array($result) )
        {
            update_query('tblhostingaddons', array( 'status' => 'Pending' ), array( 'id' => $data['id'], 'status' => 'Fraud' ));
        }
        $result = select_query('tbldomains', 'id', array( 'orderid' => $orderid ));
        while( $data = mysql_fetch_array($result) )
        {
            update_query('tbldomains', array( 'status' => 'Pending' ), array( 'id' => $data['id'], 'status' => 'Fraud' ));
        }
        update_query('tblinvoices', array( 'status' => 'Unpaid' ), array( 'id' => $invoiceid, 'status' => 'Cancelled' ));
    }
}
$apiresults = array( 'result' => 'success', 'status' => $status, 'results' => serialize($fraudresults) );
$responsetype = 'xml';