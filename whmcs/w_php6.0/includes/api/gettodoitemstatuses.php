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
$statuses = array( 'New' => array( 'count' => 0, 'overdue' => 0 ), 'Pending' => array( 'count' => 0, 'overdue' => 0 ), "In Progress" => array( 'count' => 0, 'overdue' => 0 ), 'Completed' => array( 'count' => 0, 'overdue' => 0 ), 'Postponed' => array( 'count' => 0, 'overdue' => 0 ) );
$todo_result = full_query("SELECT status, COUNT(*) AS count FROM tbltodolist GROUP BY status");
while( $todo = mysql_fetch_assoc($todo_result) )
{
    $statuses[$todo['status']]['count'] = $todo['count'];
}
$todo_over_due_result = full_query("SELECT status, COUNT(*) AS count FROM tbltodolist WHERE DATE(duedate) <= CURDATE() GROUP BY status");
while( $todo_over_due = mysql_fetch_assoc($todo_over_due_result) )
{
    $statuses[$todo_over_due['status']]['overdue'] = $todo_over_due['count'];
}
$apiresults = array( 'result' => 'success', 'totalresults' => 5 );
foreach( $statuses as $key => $status )
{
    $apiresults['todoitemstatuses']['status'][] = array( 'type' => $key, 'count' => $status['count'], 'overdue' => $status['overdue'] );
}
$responsetype = 'xml';