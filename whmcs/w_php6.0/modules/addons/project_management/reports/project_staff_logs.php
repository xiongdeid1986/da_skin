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
$reportdata['title'] = "Project Management Staff Logs";
$reportdata['description'] = "This report shows the amount of time logged per member of staff, per day, over a customisable date range.";
if( !$datefrom )
{
    $datefrom = fromMySQLDate(date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 7, date('Y'))));
}
if( !$dateto )
{
    $dateto = getTodaysDate();
}
$reportdata['headertext'] = "<form method=\"post\" action=\"" . $requeststr . "\">\n<table align=\"center\">\n<tr><td>Date Range - From</td><td><input type=\"text\" name=\"datefrom\" value=\"" . $datefrom . "\" class=\"datepick\" /></td><td width=\"20\"></td><td>To</td><td><input type=\"text\" name=\"dateto\" value=\"" . $dateto . "\" class=\"datepick\" /></td><td width=\"20\"></td><td><input type=\"submit\" value=\"Submit\" /></tr>\n</table>\n</form>";
$datefromsql = toMySQLDate($datefrom);
$datetosql = toMySQLDate($dateto);
$reportdata['tableheadings'] = array( "Staff Member" );
$startday = substr($datefromsql, 8, 2);
$startmonth = substr($datefromsql, 5, 2);
$startyear = substr($datefromsql, 0, 4);
for( $i = 0; $i <= 365; $i++ )
{
    $date = date('Y-m-d', mktime(0, 0, 0, $startmonth, $startday + $i, $startyear));
    $reportdata['tableheadings'][] = $date;
    if( str_replace('-', '', $date) == str_replace('-', '', $datetosql) )
    {
        break;
    }
}
$reportdata['tableheadings'][] = 'Totals';
$daytotals = array(  );
$r = 0;
for( $result = select_query('tbladmins', 'id,firstname,lastname', '', 'firstname', 'ASC'); $data = mysql_fetch_array($result); $r++ )
{
    $adminid = $data['id'];
    $firstname = $data['firstname'];
    $lastname = $data['lastname'];
    $reportdata['tablevalues'][$r] = array( $firstname . " " . $lastname );
    $totalduration = 0;
    for( $i = 0; $i <= 365; $i++ )
    {
        $date = date('Y-m-d', mktime(0, 0, 0, $startmonth, $startday + $i, $startyear));
        $datestart = mktime(0, 0, 0, $startmonth, $startday + $i, $startyear);
        $dateend = mktime(0, 0, 0, $startmonth, $startday + $i + 1, $startyear);
        $duration = 0;
        $result2 = select_query('mod_projecttimes', 'start,end', "start>='" . $datestart . "' AND start<'" . $dateend . "' AND adminid=" . $adminid);
        while( $data = mysql_fetch_array($result2) )
        {
            $starttime = $data['start'];
            $endtime = $data['end'];
            $time = $endtime - $starttime;
            $duration += $time;
            $totalduration += $time;
            $daytotals[$date] += $time;
        }
        $reportdata['tablevalues'][$r][] = project_staff_logs_time($duration);
        if( str_replace('-', '', $date) == str_replace('-', '', $datetosql) )
        {
            break;
        }
    }
    $reportdata['tablevalues'][$r][] = "<strong>" . project_staff_logs_time($totalduration) . "</strong>";
}
$reportdata['tablevalues'][$r][] = "<strong>Totals</strong>";
for( $i = 0; $i <= 365; $i++ )
{
    $date = date('Y-m-d', mktime(0, 0, 0, $startmonth, $startday + $i, $startyear));
    $reportdata['tablevalues'][$r][] = "<strong>" . project_staff_logs_time($daytotals[$date]) . "</strong>";
    if( str_replace('-', '', $date) == str_replace('-', '', $datetosql) )
    {
        break;
    }
}
$total = 0;
foreach( $daytotals as $v )
{
    $total += $v;
}
$reportdata['tablevalues'][$r][] = "<strong>" . project_staff_logs_time($total) . "</strong>";
function project_staff_logs_time($sec, $padHours = false)
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