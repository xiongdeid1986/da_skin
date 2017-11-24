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
$reportdata['title'] = "Project Management Project Time Logs";
$reportdata['description'] = "This report shows the amount of time logged on a per task basis, per staff member, for a given date range.";
if( !$datefrom )
{
    $datefrom = fromMySQLDate(date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 7, date('Y'))));
}
if( !$dateto )
{
    $dateto = fromMySQLDate(date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + 1, date('Y'))));
}
$admindropdown = "<select name=\"adminid\"><option value=\"0\">- Any -</option>";
$result = select_query('tbladmins', 'id,firstname,lastname', '', "firstname` ASC,`lastname", 'ASC');
while( $data = mysql_fetch_array($result) )
{
    $aid = $data['id'];
    $admindropdown .= "<option value=\"" . $aid . "\"";
    if( $aid == $adminid )
    {
        $admindropdown .= " selected";
    }
    $admindropdown .= ">" . $data['firstname'] . " " . $data['lastname'] . "</option>";
}
$admindropdown .= "</select>";
$reportdata['headertext'] = "<form method=\"post\" action=\"" . $requeststr . "\">\n<table align=\"center\">\n<tr><td>Date Range - From</td><td><input type=\"text\" name=\"datefrom\" value=\"" . $datefrom . "\" class=\"datepick\" /></td><td width=\"20\"></td><td>To</td><td><input type=\"text\" name=\"dateto\" value=\"" . $dateto . "\" class=\"datepick\" /></td><td width=\"20\"></td><td>Filter by Staff Member</td><td>" . $admindropdown . "</td><td width=\"20\"></td><td><input type=\"submit\" value=\"Submit\" /></tr>\n</table>\n</form>";
$reportdata['tableheadings'] = array( "Project Name", "Task Name", "Total Time" );
$i = 0;
$adminquery = $adminid ? " AND adminid='" . (int) $adminid . "'" : '';
for( $result = select_query('tbladmins', 'id,firstname,lastname', '', 'firstname', 'ASC'); $data = mysql_fetch_array($result); $i++ )
{
    $adminid = $data['id'];
    $adminfirstname = $data['firstname'];
    $adminlastname = $data['lastname'];
    $reportdata['tablevalues'][$i] = array( "**<strong>" . $adminfirstname . " " . $adminlastname . "</strong>" );
    $i++;
    $totalduration = 0;
    for( $result2 = select_query('mod_projecttimes', "mod_project.id,mod_project.title,mod_projecttasks.task,mod_projecttimes.start,mod_projecttimes.end", "(mod_projecttimes.start>='" . strtotime(toMySQLDate($datefrom)) . "' AND mod_projecttimes.end<='" . strtotime(toMySQLDate($dateto) . " 23:59:59") . "') AND mod_projecttimes.adminid=" . $adminid, 'start', 'ASC', '', "mod_project ON mod_projecttimes.projectid = mod_project.id INNER JOIN mod_projecttasks ON mod_projecttasks.id = mod_projecttimes.taskid"); $data = mysql_fetch_array($result2); $i++ )
    {
        $projectid = $data['id'];
        $projecttitle = $data['title'];
        $projecttask = $data['task'];
        $time = $data['end'] - $data['start'];
        $totalduration += $time;
        $reportdata['tablevalues'][$i] = array( "<a href=\"addonmodules.php?module=project_management&m=view&projectid=" . $projectid . "\">" . $projecttitle . "</a>", $projecttask, project_task_logs_time($time) );
    }
    $reportdata['tablevalues'][$i] = array( '', '', "<strong>" . project_task_logs_time($totalduration) . "</strong>" );
}
function project_task_logs_time($sec, $padHours = false)
{
    if( $sec <= 0 )
    {
        $sec = 0;
    }
    $hms = '';
    $hours = intval(intval($sec) / 3600);
    $hms .= $padHours ? str_pad($hours, 2, '0', STR_PAD_LEFT) . ":" : $hours . ":";
    $minutes = intval(($sec / 60) % 60);
    $hms .= str_pad($minutes, 2, '0', STR_PAD_LEFT) . ":";
    $seconds = intval($sec % 60);
    $hms .= str_pad($seconds, 2, '0', STR_PAD_LEFT);
    return $hms;
}