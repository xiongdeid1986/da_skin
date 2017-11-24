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
add_hook('AdminAreaClientSummaryActionLinks', 1, 'hook_project_management_csoactions');
add_hook('AdminAreaViewTicketPage', 1, 'hook_project_management_adminticketinfo');
add_hook('AdminHomeWidgets', 1, 'widget_project_management');
add_hook('CalendarEvents', '0', 'hook_project_management_calendar');
add_hook('CalendarEvents', '0', 'hook_project_management_calendar_tasks');
function hook_project_management_csoactions($vars)
{
    return array( "<a href=\"addonmodules.php?module=project_management&view=user&userid=" . $_REQUEST['userid'] . "\"><img src=\"images/icons/invoices.png\" border=\"0\" align=\"absmiddle\" /> View Projects</a>" );
}
function hook_project_management_adminticketinfo($vars)
{
    global $aInt;
    global $jscode;
    global $jquerycode;
    $ticketid = $vars['ticketid'];
    $ticketdata = get_query_vals('tbltickets', 'userid,title,tid', array( 'id' => $ticketid ));
    $tid = $ticketdata['tid'];
    require(ROOTDIR . "/modules/addons/project_management/project_management.php");
    $projectrows = '';
    $result = select_query('mod_project', "mod_project.*,(SELECT CONCAT(firstname,' ',lastname) FROM tbladmins WHERE id=mod_project.adminid) AS adminname", "ticketids LIKE '%" . mysql_real_escape_string($tid) . "%'");
    while( $data = mysql_fetch_array($result) )
    {
        $timerid = get_query_val('mod_projecttimes', 'id', array( 'projectid' => $data['id'], 'end' => '', 'adminid' => $_SESSION['adminid'] ), 'start', 'DESC');
        $timetrackinglink = $timerid ? "<a href=\"#\" onclick=\"projectendtimer('" . $data['id'] . "');return false\"><img src=\"../modules/addons/project_management/images/notimes.png\" align=\"absmiddle\" border=\"0\" /> Stop Tracking Time</a>" : "<a href=\"#\" onclick=\"projectstarttimer('" . $data['id'] . "');return false\"><img src=\"../modules/addons/project_management/images/starttimer.png\" align=\"absmiddle\" border=\"0\" /> Start Tracking Time</a>";
        $projectrows .= "<tr><td><a href=\"addonmodules.php?module=project_management&m=view&projectid=" . $data['id'] . "\">" . $data['id'] . "</a></td><td><a href=\"addonmodules.php?module=project_management&m=view&projectid=" . $data['id'] . "\">" . $data['title'] . "</a> <span id=\"projecttimercontrol" . $data['id'] . "\" class=\"tickettimer\">" . $timetrackinglink . "</span></td><td>" . $data['adminname'] . "</td><td>" . fromMySQLDate($data['created']) . "</td><td>" . fromMySQLDate($data['duedate']) . "</td><td>" . fromMySQLDate($data['lastmodified']) . "</td><td>" . $data['status'] . "</td></tr>";
    }
    $code = "<link href=\"../modules/addons/project_management/css/style.css\" rel=\"stylesheet\" type=\"text/css\" />\n\n<div id=\"projectscont\" style=\"margin:0 0 10px 0;padding:5px;border:2px dashed #e0e0e0;background-color:#fff;-moz-border-radius: 6px;-webkit-border-radius: 6px;-o-border-radius: 6px;border-radius: 6px;" . ($projectrows ? '' : "display:none;") . "\">\n\n<h2 style=\"margin:0 0 5px 0;text-align:center;background-color:#f2f2f2;-moz-border-radius: 6px;-webkit-border-radius: 6px;-o-border-radius: 6px;border-radius: 6px;\">Projects</h2>\n\n<div class=\"tablebg\" style=\"padding:0 20px;\">\n<table class=\"datatable\" width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"3\" id=\"ticketprojectstbl\">\n<tr><th>Project ID</th><th>Title</th><th>Assigned To</th><th>Created</th><th>Due Date</th><th>Last Updated</th><th>Status</th></tr>\n" . $projectrows . "\n</table>\n</div>\n\n</div>\n\n";
    if( project_management_checkperm("Create New Projects") )
    {
        $code .= "<span style=\"float:right;padding:0 50px 15px 0;\"><a href=\"#\" onclick=\"createnewproject();return false\" class=\"create\"><img src=\"images/icons/add.png\" align=\"top\" border=\"0\" /> <b>Create New Project</b></a></span>";
    }
    $code .= "\n<script>\n\$(document).on(\"keyup\",\"#cpclientname\",function () {\n    var ticketuseridsearchlength = \$(\"#cpclientname\").val().length;\n    if (ticketuseridsearchlength>2) {\n    \$.post(\"search.php\", { ticketclientsearch: 1, value: \$(\"#cpclientname\").val(), token: \"" . generate_token('plain') . "\" },\n        function(data){\n            if (data) {\n                \$(\"#cpticketclientsearchresults\").html(data.replace(\"searchselectclient(\",\"projectsearchselectclient(\"));\n                \$(\"#cpticketclientsearchresults\").slideDown(\"slow\");\n                \$(\"#cpclientsearchcancel\").fadeIn();\n            }\n        });\n    }\n});\nfunction projectsearchselectclient(userid,name,email) {\n    \$(\"#cpclientname\").val(name);\n    \$(\"#cpuserid\").val(userid);\n    \$(\"#cpclientsearchcancel\").fadeOut();\n    \$(\"#cpticketclientsearchresults\").slideUp(\"slow\");\n}\n\nfunction createnewproject() {\n    \$(\"#popupcreatenew\").show();\n    \$(\"#popupstarttimer\").hide();\n    \$(\"#popupendtimer\").hide();\n    \$(\"#createnewcont\").slideDown();\n}\nfunction createproject() {\n    inputs = \$(\"#ajaxcreateprojectform\").serializeArray();\n    \$.post(\"addonmodules.php?module=project_management&createproj=1&ajax=1\",\n        {\n            input : inputs,\n            token: \"" . generate_token('plain') . "\"\n        },\n        function (data) {\n            if(data == \"0\"){\n                alert(\"You do not have permission to create project\");\n            } else {\n                \$(\"#createnewcont\").slideUp();\n                \$(\"#ticketprojectstbl\").append(data);\n                \$(\"#projectscont\").slideDown();\n            }\n        });\n}\n\nfunction projectstarttimer(projectid) {\n    \$(\"#ajaxstarttimerformprojectid\").val(projectid);\n    \$(\"#popupcreatenew\").hide();\n    \$(\"#popupstarttimer\").show();\n    \$(\"#popupendtimer\").hide();\n    \$(\"#createnewcont\").slideDown();\n}\n\nfunction projectendtimer(projectid) {\n    \$(\"#popupcreatenew\").hide();\n    \$(\"#popupstarttimer\").hide();\n    \$(\"#popupendtimer\").show();\n    \$(\"#createnewcont\").slideDown();\n}\n\nfunction projectstarttimersubmit() {\n    \$.post(\"addonmodules.php?module=project_management&m=view\", \"a=hookstarttimer&\"+\$(\"#ajaxstarttimerform\").serialize(),\n        function (data) {\n            if(data == \"0\"){\n                alert(\"Could not start timer.\");\n            } else {\n                \$(\"#createnewcont\").slideUp();\n                var projid = \$(\"#ajaxstarttimerformprojectid\").val();\n                \$(\"#projecttimercontrol\"+projid).html(\"<a href=\\\"#\\\" onclick=\\\"projectendtimer('\"+projid+\"');return false\\\"><img src=\\\"../modules/addons/project_management/images/notimes.png\\\" align=\\\"absmiddle\\\" border=\\\"0\\\" /> Stop Tracking Time</a>\");\n                \$(\"#activetimers\").html(data);\n            }\n        });\n}\nfunction projectendtimersubmit(projectid,timerid) {\n    \$.post(\"addonmodules.php?module=project_management&m=view\",\n        {\n            a: \"hookendtimer\",\n            timerid: timerid,\n            ticketnum: \"" . $tid . "\",\n            token: \"" . generate_token('plain') . "\"\n        },\n        function (data) {\n            if (data == \"0\") {\n                alert(\"Could not stop timer.\");\n            } else {\n                \$(\"#createnewcont\").slideUp();\n                \$(\"#projecttimercontrol\"+projectid).html(\"<a href=\\\"#\\\" onclick=\\\"projectstarttimer('\"+projectid+\"');return false\\\"><img src=\\\"../modules/addons/project_management/images/starttimer.png\\\" align=\\\"absmiddle\\\" border=\\\"0\\\" /> Start Tracking Time</a>\");\n                \$(\"#activetimers\").html(data);\n            }\n        }\n    );\n}\n\nfunction projectpopupcancel() {\n    \$(\"#createnewcont\").slideUp();\n}\n\n</script>\n\n<div class=\"projectmanagement\">\n\n<div id=\"createnewcont\" style=\"display:none;\">\n\n<div class=\"createnewcont2\">\n\n<div class=\"createnewproject\" id=\"popupcreatenew\" style=\"display:none\">\n<div class=\"title\">Create New Project</div>\n<form id=\"ajaxcreateprojectform\">\n<div class=\"label\">Title</div>\n<input type=\"text\" name=\"title\" class=\"title\" />\n<div class=\"float\">\n<div class=\"label\">Created</div>\n<input type=\"text\" name=\"created\" class=\"datepick\" value=\"" . getTodaysDate() . "\" />\n</div>\n<div class=\"float\">\n<div class=\"label\">Due Date</div>\n<input type=\"text\" name=\"duedate\" class=\"datepick\" value=\"" . getTodaysDate() . "\" />\n</div>\n<div class=\"float\">\n<div class=\"label\">Assigned To</div>\n<select class=\"title\" name=\"adminid\">";
    $code .= "<option value=\"0\">None</option>";
    $result = select_query('tbladmins', 'id,firstname,lastname', '', "firstname` ASC,`lastname", 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $aid = $data['id'];
        $adminfirstname = $data['firstname'];
        $adminlastname = $data['lastname'];
        $code .= "<option value=\"" . $aid . "\"";
        if( $aid == $adminid )
        {
            $code .= " selected";
        }
        $code .= ">" . $adminfirstname . " " . $adminlastname . "</option>";
    }
    $code .= "</select>\n</div>\n<div class=\"float\">\n<div class=\"label\">Ticket #</div>\n<input type=\"text\" name=\"ticketnum\" class=\"ticketnum\" value=\"" . $tid . "\" />\n</div>\n<div class=\"clear\"></div>\n<div class=\"float\">\n<div class=\"label\">Associated Client</div>\n<input type=\"hidden\" name=\"userid\" id=\"cpuserid\" /><input type=\"text\" id=\"cpclientname\" value=\"" . $clientname . "\" class=\"title\" onfocus=\"if(this.value=='" . addslashes($clientname) . "')this.value=''\" /> <img src=\"images/icons/delete.png\" alt=\"" . $vars['_lang']['cancel'] . "\" align=\"right\" id=\"clientsearchcancel\" height=\"16\" width=\"16\"><div id=\"cpticketclientsearchresults\" style=\"z-index:2000;\"></div>\n</div>\n<br /><br />\n<div align=\"center\"><input type=\"button\" value=\"Create\" onclick=\"createproject()\" class=\"create\" /> <input type=\"button\" value=\"Cancel\" class=\"create\" onclick=\"projectpopupcancel();return false\" /></div>\n</form>\n</div>\n\n<div class=\"createnewproject\" id=\"popupstarttimer\" style=\"display:none\">\n<div class=\"title\">Start Time Tracking</div>\n<form id=\"ajaxstarttimerform\">\n" . generate_token() . "\n<input type=\"hidden\" id=\"ajaxstarttimerformprojectid\" name=\"projectid\">\n<input type=\"hidden\" name=\"ticketnum\" value=\"" . $tid . "\" />\n<div class=\"label\">Select Existing Task</div>\n<select class=\"title\" style=\"min-width:450px\" name=\"taskid\">";
    $code .= "<option value=\"\">Choose one...</option>";
    $result = select_query('mod_projecttasks', "mod_project.title, mod_projecttasks.id, mod_projecttasks.projectid, mod_projecttasks.task", array( "mod_project.ticketids" => array( 'sqltype' => 'LIKE', 'value' => (int) $tid ) ), '', '', '', "mod_project ON mod_projecttasks.projectid=mod_project.id", '', '', '', "mod_project ON mod_projecttasks.projectid=mod_project.id");
    while( $data = mysql_fetch_array($result) )
    {
        $code .= "<option value=\"" . $data['id'] . "\"";
        $code .= ">" . $data['projectid'] . " - " . $data['title'] . " - " . $data['task'] . "</option>";
    }
    $code .= "</select><br />\n<div class=\"label\">Or Create New Task</div>\n<input type=\"text\" name=\"title\" class=\"title\" />\n<br />\n<div align=\"center\"><input type=\"button\" value=\"Start\" onclick=\"projectstarttimersubmit();return false\" class=\"create\" /> <input type=\"button\" value=\"Cancel\" class=\"create\" onclick=\"projectpopupcancel();return false\" /></div>\n</form>\n</div>\n</div>\n\n<div class=\"createnewproject\" id=\"popupendtimer\" style=\"display:none\">\n<div class=\"title\">Stop Time Tracking</div>\n<form id=\"ajaxendtimerform\">\n<input type=\"hidden\" id=\"ajaxendtimerformprojectid\" name=\"projectid\">\n<br />\n<b>Active Timers</b>:<br /><br />\n<div id=\"activetimers\">\n";
    $result = select_query('mod_projecttimes', "mod_projecttimes.id, mod_projecttimes.projectid, mod_project.title, mod_projecttimes.taskid, mod_projecttasks.task, mod_projecttimes.start", array( "mod_projecttimes.adminid" => $_SESSION['adminid'], "mod_projecttimes.end" => '', "mod_project.ticketids" => array( 'sqltype' => 'LIKE', 'value' => (int) $tid ) ), '', '', '', "mod_projecttasks ON mod_projecttimes.taskid=mod_projecttasks.id INNER JOIN mod_project ON mod_projecttimes.projectid=mod_project.id");
    while( $data = mysql_fetch_array($result) )
    {
        $code .= "<div class=\"stoptimer" . $data['id'] . "\" style=\"padding-bottom:10px;\"><em>" . $data['title'] . " - Project ID " . $data['projectid'] . "</em><br />&nbsp;&raquo; " . $data['task'] . "<br />Started at " . fromMySQLDate(date("Y-m-d H:i:s", $data['start']), 1) . ":" . date('s', $data['start']) . " - <a href=\"#\" onclick=\"projectendtimersubmit('" . $data['projectid'] . "','" . $data['id'] . "');return false\"><strong>Stop Timer</strong></a></div>";
    }
    $code .= "\n</div>\n<br />\n<div align=\"center\"><input type=\"button\" value=\"Cancel\" class=\"create\" onclick=\"projectpopupcancel();return false\" /></div>\n</form>\n</div>\n\n</div>\n\n</div>\n\n";
    return $code;
}
function widget_project_management($vars)
{
    global $whmcs;
    global $_ADMINLANG;
    $title = "Project Management";
    if( $whmcs->get_req_var('getprojectmanagementoverview') )
    {
        echo "<div class=\"tabs\"><div onclick=\"loadProjects('recentactivity')\"";
        if( $_POST['getprojectmanagementtab'] == 'recentactivity' )
        {
            echo " class=\"active\"";
        }
        echo ">Recent Activity</div><div onclick=\"loadProjects('dueprojects')\"";
        if( $_POST['getprojectmanagementtab'] == 'dueprojects' )
        {
            echo " class=\"active\"";
        }
        echo ">Due Projects</div><div onclick=\"loadProjects('myassigned')\"";
        if( $_POST['getprojectmanagementtab'] == 'myassigned' || !$_POST['getprojectmanagementtab'] )
        {
            echo " class=\"active\"";
        }
        echo ">My Assigned</div></div>\n<div class=\"clear\"></div>\n<div class=\"overviewcontainer\">\n<table width=\"100%\" bgcolor=\"#cccccc\" cellspacing=\"1\">";
        $noprojects = true;
        if( isset($_POST['getprojectmanagementtab']) && $_POST['getprojectmanagementtab'] == 'recentactivity' )
        {
            echo "<tr bgcolor=\"#efefef\" style=\"text-align:center;font-weight:bold;\"><td>Date</td><td>Log Entry</td><td>Admin User</td></tr>";
            $result = select_query('mod_projectlog', "mod_projectlog.projectid,mod_projectlog.date,mod_project.title,mod_projectlog.msg,mod_projectlog.adminid", '', 'date', 'DESC', '5', "mod_project ON mod_projectlog.projectid=mod_project.id");
            while( $data = mysql_fetch_array($result) )
            {
                $projectid = $data['projectid'];
                $date = $data['date'];
                $title = $data['title'];
                $msg = $data['msg'];
                $date = fromMySQLDate($date, 1);
                $admin = getAdminName($data['adminid']);
                echo "<tr bgcolor=\"#ffffff\"><td align=\"center\">" . $date . "</td><td align=\"center\"><a href=\"addonmodules.php?module=project_management&m=view&projectid=" . $projectid . "\">" . $title . "</a> - " . $msg . "</td><td align=\"center\">" . $admin . "</td></tr>";
                $noprojects = false;
            }
            if( $noprojects )
            {
                echo "<tr bgcolor=\"#ffffff\"><td colspan=\"3\" align=\"center\">No Records Found</td></tr>";
            }
        }
        else
        {
            echo "<tr bgcolor=\"#efefef\" style=\"text-align:center;font-weight:bold;\"><td>Title</td><td>Due Date</td><td>Days Left / Due In</td><td>Status</td></tr>";
            if( isset($_POST['getprojectmanagementtab']) && $_POST['getprojectmanagementtab'] == 'myassigned' )
            {
                $result = select_query('mod_project', '', array( 'completed' => 0, 'adminid' => $_SESSION['adminid'] ), 'duedate', 'ASC');
            }
            else
            {
                if( isset($_POST['getprojectmanagementtab']) && $_POST['getprojectmanagementtab'] == 'dueprojects' )
                {
                    $result = select_query('mod_project', '', "completed=0 AND duedate<='" . date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + 7, date('Y'))) . "'", 'duedate', 'ASC');
                }
            }
            while( $data = mysql_fetch_array($result) )
            {
                $id = $data['id'];
                $title = $data['title'];
                $duedate = fromMySQLDate($data['duedate']);
                $daysleft = project_management_hook_daysleft($data['duedate']);
                $status = $data['status'];
                echo "<tr bgcolor=\"#ffffff\"><td align=\"center\"><a href=\"addonmodules.php?module=project_management&m=view&projectid=" . $id . "\">" . $title . "</a></td><td align=\"center\">" . $duedate . "</td><td align=\"center\">" . $daysleft . "</td><td align=\"center\">" . $status . "</td></tr>";
                $noprojects = false;
            }
            if( $noprojects )
            {
                echo "<tr bgcolor=\"#ffffff\"><td colspan=\"4\" align=\"center\">No Records Found</td></tr>";
            }
        }
        echo "</table></div>";
        exit();
    }
    $content = "\n<style>\n.tabs div {\n    float: right;\n    margin: 0 5px 5px 0;\n    padding: 2px 7px;\n    background-color:#1A4D80;\n    border: 1px solid #1A4D80;\n    font-size: 11px;\n    color:#fff;\n    -moz-border-radius: 6px;\n    -webkit-border-radius: 6px;\n    -o-border-radius: 6px;\n    border-radius: 6px;\n}\n.tabs div.active {\n    background-color: #fff;\n    border: 1px solid #1A4D80;\n    color: #1A4D80;\n}\n.tabs div:hover {\n    background-color:#E5E5E5;\n    color:#000;\n    cursor: hand;\n    cursor: pointer;\n}\n#overviewcontainer {\n    max-height:150px;\n    overflow:auto;\n    padding-bottom: 10px;\n}\n</style>\n<div id=\"overviewtable\">" . $vars['loading'] . "</div>\n";
    $jscode = "function loadProjects(tab) {\n    \$(\"#overviewcontainer\").html(\"" . str_replace("\"", "\\\"", $vars['loading']) . "\");\n    jQuery.post(\"index.php\", { getprojectmanagementoverview: 1, getprojectmanagementtab: tab },\n        function(data){\n            jQuery(\"#overviewtable\").html(data);\n        });\n}";
    $jquerycode = "loadProjects(\"myassigned\");";
    return array( 'title' => $title, 'content' => $content, 'jquerycode' => $jquerycode, 'jscode' => $jscode );
}
function project_management_hook_daysleft($duedate)
{
    $datetime = strtotime('now');
    $date2 = strtotime($duedate);
    $days = ceil(($date2 - $datetime) / 86400);
    if( $days == '-0' )
    {
        $days = 0;
    }
    $dueincolor = $days < 2 ? 'cc0000' : '73BC10';
    if( 0 <= $days )
    {
        return "<span style=\"color:#" . $dueincolor . "\">Due In " . $days . " Days</span>";
    }
    return "<span style=\"color:#" . $dueincolor . "\">Due " . $days * (0 - 1) . " Days Ago</span>";
}
function hook_project_management_calendar($vars)
{
    $events = array(  );
    $result = select_query('mod_project', '', "duedate BETWEEN '" . date('Y-m-d', $vars['start']) . "' AND '" . date('Y-m-d', $vars['end']) . "'");
    while( $data = mysql_fetch_assoc($result) )
    {
        $projecttitle = "Project Due: " . $data['title'] . "\nStatus: " . $data['status'];
        if( $data['adminid'] )
        {
            $projecttitle .= " (" . getAdminName($data['adminid']) . ")";
        }
        $events[] = array( 'id' => 'prj' . $data['id'], 'title' => $projecttitle, 'start' => strtotime($data['duedate']), 'allDay' => true, 'url' => "addonmodules.php?module=project_management&m=view&projectid=" . $data['id'] );
    }
    return $events;
}
function hook_project_management_calendar_tasks($vars)
{
    $events = array(  );
    $result = select_query('mod_projecttasks', "mod_projecttasks.*,(SELECT title FROM mod_project WHERE mod_project.id=mod_projecttasks.projectid) AS projecttitle", "duedate BETWEEN '" . date('Y-m-d', $vars['start']) . "' AND '" . date('Y-m-d', $vars['end']) . "'");
    while( $data = mysql_fetch_assoc($result) )
    {
        $projecttitle = "Task Due: " . $data['task'] . "\n" . "Project: " . $data['projecttitle'] . "\nStatus: " . ($data['completed'] ? 'Completed' : 'Pending');
        if( $data['adminid'] )
        {
            $projecttitle .= " (" . getAdminName($data['adminid']) . ")";
        }
        $events[] = array( 'id' => 'prj' . $data['id'], 'title' => $projecttitle, 'start' => strtotime($data['duedate']), 'allDay' => true, 'url' => "addonmodules.php?module=project_management&m=view&projectid=" . $data['projectid'] );
    }
    return $events;
}