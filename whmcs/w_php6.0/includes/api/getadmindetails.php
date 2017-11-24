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
if( !function_exists('getAdminPermsArray') )
{
    require(ROOTDIR . "/includes/adminfunctions.php");
}
$result = select_query('tbladmins', 'id,firstname,lastname,notes,signature,roleid,supportdepts', array( 'id' => $_SESSION['adminid'] ));
$data = mysql_fetch_array($result);
$adminid = $data['id'];
$firstname = $data['firstname'];
$lastname = $data['lastname'];
$notes = $data['notes'];
$signature = $data['signature'];
$adminroleid = $data['roleid'];
$supportdepts = $data['supportdepts'];
$apiresults = array( 'result' => 'success', 'adminid' => $adminid, 'name' => $firstname . " " . $lastname, 'notes' => $notes, 'signature' => $signature );
$adminpermsarray = getAdminPermsArray();
$result = select_query('tbladminperms', '', array( 'roleid' => $adminroleid ));
while( $data = mysql_fetch_array($result) )
{
    $permid = $data['permid'];
    $apiresults['allowedpermissions'] .= $adminpermsarray[$permid] . ',';
}
$apiresults['departments'] .= $supportdepts;
$apiresults['allowedpermissions'] = substr($apiresults['allowedpermissions'], 0, 0 - 1);
if( $iphone )
{
    if( defined('IPHONELICENSE') )
    {
        exit( "License Hacking Attempt Detected" );
    }
    global $licensing;
    define('IPHONELICENSE', $licensing->isActiveAddon("iPhone App"));
    $apiresults['iphone'] = IPHONELICENSE;
}
if( $windows8app )
{
    if( defined('WINDOWS8APPLICENSE') )
    {
        exit( "License Hacking Attempt Detected" );
    }
    global $licensing;
    define('WINDOWS8APPLICENSE', $licensing->isActiveAddon("Windows 8 App"));
    $apiresults['windows8app'] = WINDOWS8APPLICENSE;
}
if( $android )
{
    if( defined('ANDROIDLICENSE') )
    {
        exit( "License Hacking Attempt Detected" );
    }
    global $licensing;
    define('ANDROIDLICENSE', $licensing->isActiveAddon("Android App"));
    $apiresults['android'] = ANDROIDLICENSE;
    $statuses = array(  );
    $result = select_query('tblticketstatuses', '', '', 'sortorder', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $statuses[$data['title']] = 0;
    }
    $where = '';
    if( $deptid )
    {
        $where = "WHERE did='" . mysql_real_escape_string($deptid) . "'";
    }
    $result = full_query("SELECT status, COUNT(*) AS count FROM tbltickets " . $where . " GROUP BY status");
    while( $data = mysql_fetch_array($result) )
    {
        $statuses[$data['status']] = $data['count'];
    }
    foreach( $statuses as $status => $ticketcount )
    {
        $apiresults['supportstatuses']['status'][] = array( 'title' => $status, 'count' => $ticketcount );
    }
    $deptartments = array(  );
    $result = full_query("SELECT id, name FROM tblticketdepartments");
    while( $data = mysql_fetch_assoc($result) )
    {
        $deptartments[$data['id']] = $data['name'];
    }
    foreach( $deptartments as $deptid => $deptname )
    {
        $apiresults['supportdepartments']['department'][] = array( 'id' => $deptid, 'name' => $deptname, 'count' => get_query_val('tbltickets', "COUNT(id)", array( 'did' => $deptid )) );
    }
    $gateways = array(  );
    $result = select_query('tblpaymentgateways', 'gateway,value', array( 'setting' => 'name' ));
    while( $data = mysql_fetch_assoc($result) )
    {
        $gateways[$data['gateway']] = $data['value'];
    }
    if( !function_exists('getGatewaysArray') )
    {
        require(ROOTDIR . "/includes/gatewayfunctions.php");
    }
    $paymentmethods = getGatewaysArray();
    foreach( $paymentmethods as $module => $name )
    {
        $apiresults['paymentmethods']['paymentmethod'][] = array( 'module' => $module, 'displayname' => $name );
    }
}
$apiresults['requesttime'] = date("Y-m-d H:i:s");