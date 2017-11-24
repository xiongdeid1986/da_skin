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
echo $headeroutput;
if( !project_management_checkperm("View Recent Activity") )
{
    echo "<p>You do not have permission to view recent activity.</p>";
    return false;
}
$aInt->sortableTableInit('duedate', 'ASC');
$tabledata = '';
$where = array(  );
if( $_REQUEST['projectid'] )
{
    $where['projectid'] = (int) $_REQUEST['projectid'];
}
$result = select_query('mod_projectlog', "COUNT(*)", $where);
$data = mysql_fetch_array($result);
$numrows = $data[0];
$result = select_query('mod_projectlog', "mod_projectlog.*,(SELECT CONCAT(firstname,' ',lastname) FROM tbladmins WHERE tbladmins.id=mod_projectlog.adminid) AS admin,(SELECT title FROM mod_project WHERE mod_project.id=mod_projectlog.projectid) AS projectname, (SELECT adminid FROM mod_project WHERE mod_project.id=mod_projectlog.projectid) as assignedadminid", $where, 'id', 'DESC', $page * $limit . ',' . $limit);
while( $data = mysql_fetch_array($result) )
{
    $date = $data['date'];
    $projectid = $data['projectid'];
    $projectname = project_management_check_viewproject($projectid) ? "<a href=\"" . $modulelink . "&m=view&projectid=" . $projectid . "\">" . $data['projectname'] . "</a>" : $data['projectname'];
    $msg = $data['msg'];
    $admin = $data['admin'];
    $date = fromMySQLDate($date, true);
    $tabledata[] = array( $date, $projectname, $msg, $admin );
}
echo $aInt->sortableTable(array( 'Date', 'Project', "Log Entry", "Admin User" ), $tabledata);