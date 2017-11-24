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
echo "<h2>Reports</h2>";
if( !project_management_checkperm("View Reports") )
{
    echo "<p>You do not have permission to view reports.</p>";
    return false;
}
$pmReportsPath = array( 'modules', 'addons', 'project_management', 'reports' );
$reports = new WHMCS_File_Directory(implode(DIRECTORY_SEPARATOR, $pmReportsPath));
$reportFiles = $reports->listFiles();
echo "\n<div class=\"reports\">\n";
foreach( $reportFiles as $reportName )
{
    $reportName = str_replace(".php", '', $reportName);
    $displayName = titleCase(str_replace('_', " ", $reportName));
    echo "<a href=\"reports.php?moduletype=addons&modulename=project_management&subdir=reports&report=" . $reportName . "\">" . $displayName . "</a>";
}
echo "</div>\n\n<br />\n\n";
$chart = new WHMCS_Chart();
$chartData = array( 'cols' => array( array( 'label' => 'Project', 'type' => 'string' ), array( 'label' => "Completed Tasks", 'type' => 'number' ), array( 'label' => "Incomplete Tasks", 'type' => 'number' ) ), 'rows' => array(  ) );
$statuses = get_query_val('tbladdonmodules', 'value', array( 'module' => 'project_management', 'setting' => 'completedstatuses' ));
$statuses = explode(',', $statuses);
$result = select_query('mod_project', 'id,title', "status NOT IN (" . db_build_in_array($statuses) . ")");
while( $data = mysql_fetch_array($result) )
{
    $projectid = $data['id'];
    $title = $data['title'];
    $incompletetasks = get_query_val('mod_projecttasks', "COUNT(id)", array( 'projectid' => $projectid, 'completed' => '0' ));
    $completedtasks = get_query_val('mod_projecttasks', "COUNT(id)", array( 'projectid' => $projectid, 'completed' => '1' ));
    $chartData['rows'][] = array( 'c' => array( array( 'v' => $title ), array( 'v' => $completedtasks, 'f' => $completedtasks ), array( 'v' => $incompletetasks, 'f' => $incompletetasks ) ) );
}
$args = array( 'title' => "Task Status per Project", 'legendpos' => 'right', 'colors' => "#77CC56,#999", 'stacked' => true );
echo $chart->drawChart('Column', $chartData, $args, '600px', "100%");