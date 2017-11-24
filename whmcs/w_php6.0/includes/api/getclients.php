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
$whmcs = WHMCS_Application::getinstance();
$limitStart = (int) $whmcs->get_req_var('limitstart');
$limitNum = (int) $whmcs->get_req_var('limitnum');
$sorting = strtoupper($whmcs->get_req_var('sorting'));
$search = $whmcs->get_req_var('search');
if( !$limitStart )
{
    $limitStart = 0;
}
if( !$limitNum || $limitNum == 0 )
{
    $limitNum = 25;
}
if( !in_array($sorting, array( 'ASC', 'DESC' )) )
{
    $sorting = 'ASC';
}
$search = mysql_real_escape_string($search);
if( 0 < strlen(trim($search)) )
{
    $whereStmt = "WHERE email LIKE '" . $search . "%' OR firstname LIKE '" . $search . "%' " . "OR lastname LIKE '" . $search . "%' OR companyname LIKE '" . $search . "%'" . "OR CONCAT(firstname, ' ', lastname) LIKE '" . $search . "%'";
}
else
{
    $whereStmt = '';
}
$sql = "SELECT SQL_CALC_FOUND_ROWS id, firstname, lastname, companyname, email, groupid, datecreated, status\n        FROM tblclients\n        " . $whereStmt . "\n        ORDER BY lastname " . $sorting . ", firstname " . $sorting . ", companyname " . $sorting . "\n        LIMIT " . (int) $limitStart . ", " . (int) $limitNum;
$result = full_query($sql);
$resultCount = full_query("SELECT FOUND_ROWS()");
$data = mysql_fetch_array($resultCount);
$totalResults = $data[0];
$apiresults = array( 'result' => 'success', 'totalresults' => $totalResults, 'startnumber' => $limitStart, 'numreturned' => mysql_num_rows($result) );
while( $data = mysql_fetch_array($result) )
{
    $id = $data['id'];
    $firstName = $data['firstname'];
    $lastName = $data['lastname'];
    $companyName = $data['companyname'];
    $email = $data['email'];
    $groupID = $data['groupid'];
    $dateCreated = $data['datecreated'];
    $status = $data['status'];
    $apiresults['clients']['client'][] = array( 'id' => $id, 'firstname' => $firstName, 'lastname' => $lastName, 'companyname' => $companyName, 'email' => $email, 'datecreated' => $dateCreated, 'groupid' => $groupID, 'status' => $status );
}
$responsetype = 'xml';