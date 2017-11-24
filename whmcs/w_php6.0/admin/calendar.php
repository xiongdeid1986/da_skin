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
define('ADMINAREA', true);
require("../init.php");
$aInt = new WHMCS_Admin('Calendar');
$aInt->title = $aInt->lang('utilities', 'calendar');
$aInt->sidebar = 'utilities';
$aInt->icon = 'calendar';
if( !function_exists('json_encode') )
{
    $aInt->gracefulExit("The JSON library is required to be available in your PHP build for this page to be able to function. Please add it and then try again.");
}
if( $CONFIG['DateFormat'] == 'DD/MM/YYYY' || $CONFIG['DateFormat'] == "DD.MM.YYYY" || $CONFIG['DateFormat'] == 'DD-MM-YYYY' )
{
    $localdateformat = 'dd/mm/yy';
}
else
{
    if( $CONFIG['DateFormat'] == 'MM/DD/YYYY' )
    {
        $localdateformat = 'mm/dd/yy';
    }
    else
    {
        if( $CONFIG['DateFormat'] == 'YYYY/MM/DD' || $CONFIG['DateFormat'] == 'YYYY-MM-DD' )
        {
            $localdateformat = 'yy/mm/dd';
        }
    }
}
if( $action == 'fetch' )
{
    check_token("WHMCS.admin.default");
    echo "<p align=\"center\"><b>Add New Event</b></p><p>Title<br /><input type=\"text\" name=\"title\" style=\"width:80%;\" /></p>\n<p>Description<br /><input type=\"text\" name=\"desc\" style=\"width:90%;\" /></p>\n<table>\n    <tr>\n        <td width=\"160\">Start Date/Time<br /><input type=\"text\" name=\"start\" class=\"datepick\" id=\"start\" value=\"" . fromMySQLDate(substr($ymd, 0, 4) . '-' . substr($ymd, 4, 2) . '-' . substr($ymd, 6, 2)) . " 00:00:00" . "\" style=\"width:145px;\" /></td>\n        <td width=\"160\">End Date/Time<br /><input type=\"text\" name=\"end\" class=\"datepick\" id=\"end\" value=\"" . fromMySQLDate(substr($ymd, 0, 4) . '-' . substr($ymd, 4, 2) . '-' . substr($ymd, 6, 2)) . " 23:59:59\" disabled style=\"width:145px;\" /></td>\n    </tr>\n</table>\n<p><label><input type=\"checkbox\" name=\"allday\" id=\"allday\" value=\"1\" checked /> All Day</label></p>\n<p><label>Recur Every <input type=\"text\" style=\"width:25px;\" name=\"recurevery\" /></label> <select name=\"recurtype\"><option value=\"days\">Days</option><option value=\"weeks\">Weeks</option><option value=\"months\">Months</option><option value=\"years\">Years</option></select> for <label><input type=\"text\" style=\"width:25px;\" name=\"recurtimes\" />  times*</label></p>\n<p>*0 = Unlimited</label></p>\n<p align=\"center\"><input type=\"submit\" value=\"Save\" /> <input type=\"button\" value=\"Cancel\" onclick=\"jQuery('#caledit').fadeOut()\" /></p>";
    exit();
}
if( $action == 'refresh' )
{
    check_token("WHMCS.admin.default");
    WHMCS_Cookie::set('CalendarDisplayTypes', $displaytypes, time() + 86400 * 365);
    redir();
}
if( $action == 'save' )
{
    check_token("WHMCS.admin.default");
    $start = toMySQLDate($start);
    $start = strtotime($start, time());
    $end = toMySQLDate($end);
    $end = !$allday && $end ? strtotime($end, time()) : '';
    if( $id )
    {
        update_query('tblcalendar', array( 'title' => $title, 'desc' => $desc, 'start' => $start, 'end' => $end, 'allday' => $allday ), array( 'id' => $id ));
    }
    else
    {
        $neweventid = insert_query('tblcalendar', array( 'title' => $title, 'desc' => $desc, 'start' => $start, 'end' => $end, 'allday' => $allday ));
        if( $recurevery && $recurtype )
        {
            if( $recurtimes == 0 )
            {
                $recurtimes = 99;
                $recurtype = 'years';
            }
            for( $i = 1; $i <= $recurtimes - 1; $i++ )
            {
                $nexttime = $nexttime ? strtotime("+" . $recurevery . " " . $recurtype, $nexttime) : $start;
                $rstart = strtotime(date('Ymd', strtotime("+" . $recurevery . " " . $recurtype, $nexttime)) . $starttime);
                $rend = $endtime ? strtotime(date('Ymd', strtotime("+" . $recurevery . " " . $recurtype, $nexttime)) . $endtime) : '';
                insert_query('tblcalendar', array( 'title' => $title, 'desc' => $desc, 'start' => $rstart, 'end' => $rend, 'allday' => $allday, 'recurid' => $neweventid ));
                update_query('tblcalendar', array( 'recurid' => $neweventid ), array( 'id' => $neweventid ));
            }
        }
    }
    redir();
}
if( $action == 'update' )
{
    check_token("WHMCS.admin.default");
    if( $type == 'move' )
    {
        $start = get_query_val('tblcalendar', 'start', array( 'id' => $id ));
        $start = $start + $days * 24 * 60 * 60 + $minutes * 60;
        $allday = $allday == 'true' ? '1' : '0';
        update_query('tblcalendar', array( 'start' => $start, 'allday' => $allday ), array( 'id' => $id ));
    }
    else
    {
        if( $type == 'resize' )
        {
            $data = get_query_vals('tblcalendar', 'start,end', array( 'id' => $id ));
            $start = $data['start'];
            $end = $data['end'];
            if( !$end )
            {
                $end = $start;
            }
            $end = $end + $days * 24 * 60 * 60 + $minutes * 60;
            update_query('tblcalendar', array( 'end' => $end ), array( 'id' => $id ));
        }
    }
    exit();
}
if( $action == 'delete' )
{
    check_token("WHMCS.admin.default");
    delete_query('tblcalendar', array( 'id' => $id ));
    exit();
}
if( $action == 'recurdelete' )
{
    check_token("WHMCS.admin.default");
    delete_query('tblcalendar', array( 'recurid' => $recurid ));
    redir();
}
$caldisplaytypes = WHMCS_Cookie::get('CalendarDisplayTypes', 1);
if( $caldisplaytypes['events'] == 'on' )
{
    add_hook('CalendarEvents', '-999', 'calendar_core_calendar');
}
if( $caldisplaytypes['services'] == 'on' )
{
    add_hook('CalendarEvents', '-998', 'calendar_core_products');
}
if( $caldisplaytypes['addons'] == 'on' )
{
    add_hook('CalendarEvents', '-997', 'calendar_core_addons');
}
if( $caldisplaytypes['domains'] == 'on' )
{
    add_hook('CalendarEvents', '-996', 'calendar_core_domains');
}
if( $caldisplaytypes['todo'] == 'on' )
{
    add_hook('CalendarEvents', '-995', 'calendar_core_todoitems');
}
$calevents = array(  );
foreach( $hooks['CalendarEvents'] as $calfeed )
{
    $calevents[] = $calfeed['hook_function'];
}
if( $_REQUEST['getcalfeed'] )
{
    $feed = $_REQUEST['feed'];
    $start = (int) $_REQUEST['start'];
    $end = (int) $_REQUEST['end'];
    if( in_array($feed, $calevents) )
    {
        $events = call_user_func($feed, array( 'start' => $start, 'end' => $end ));
        if( !is_array($events) )
        {
            $events = array(  );
        }
        echo json_encode($events);
    }
    exit();
}
if( $_REQUEST['editentry'] )
{
    check_token("WHMCS.admin.default");
    $data = get_query_vals('tblcalendar', '', array( 'id' => $id ));
    $starttime = date('Y-m-d', $data['start']);
    $endtime = !$data['allday'] && $data['end'] ? $data['end'] : '';
    $editcontent = array( 'defaultsdate' => date("Y, n, j", $data['start']), 'defaultedate' => $data['end'] ? date("Y, n, j", $data['end']) : date("Y, n, j", $data['start']), 'defaultsh' => date('H', $data['start']), 'defaultsm' => date('i', $data['start']), 'defaulteh' => date('H', $data['end']), 'defaultem' => date('i', $data['end']), 'html' => "<div align=\"center\"><b>Edit Event</b></div><input type=\"hidden\" name=\"id\" value=\"" . $data['id'] . "\" />\n        <p>Title<br /><input type=\"text\" name=\"title\" style=\"width:80%;\" value=\"" . $data['title'] . "\" /></p>\n        <p>Description<br /><input type=\"text\" name=\"desc\" style=\"width:90%;\" value=\"" . $data['desc'] . "\" /></p>\n        <table>\n            <tr>\n                <td width=\"160\">Start Time<br /><input type=\"text\" name=\"start\" id=\"start\" value=\"" . fromMySQLDate(date("Y-m-d H:i", $data['start']), 1) . ":" . date('s', $data['start']) . "\" style=\"width:145px;\" /></td>\n                <td width=\"160\">End Time<br /><input type=\"text\" name=\"end\" id=\"end\" value=\"" . ($endtime ? fromMySQLDate(date("Y-m-d H:i", $endtime), 1) . ":" . date('s', $endtime) : '') . "\" style=\"width:145px;\" /></td>\n            </tr>\n        </table>\n        <p><label><input type=\"checkbox\" value=\"1\" name=\"allday\"" . ($data['allday'] ? " checked" : '') . " /> All Day</label>" );
    if( $data['recurid'] )
    {
        $editcontent['html'] .= "<label style=\"float:right;margin-right:9%;\"><a href=\"calendar.php?action=recurdelete&recurid=" . $data['recurid'] . generate_token('link') . "\">Delete Recurring Event</a></label>";
    }
    $editcontent['html'] .= "</p><div align=\"center\"><input type=\"submit\" value=\"Save\" /> <input type=\"button\" value=\"Delete\" onclick=\"deleteEntry('" . $data['id'] . "')\" /> <input type=\"button\" value=\"Cancel\" onclick=\"jQuery('#caledit').fadeOut()\" /></div>";
    echo json_encode($editcontent);
    exit();
}
ob_start();
$calcolors = array(  );
$calcolors[] = array( 'bg' => '3366CC', 'text' => 'ffffff' );
$calcolors[] = array( 'bg' => 'FBE983', 'text' => '000000' );
$calcolors[] = array( 'bg' => 'F83A22', 'text' => 'ffffff' );
$calcolors[] = array( 'bg' => 'B3DC6C', 'text' => '000000' );
$calcolors[] = array( 'bg' => 'CAD5D5', 'text' => '000000' );
$calcolors[] = array( 'bg' => 'F83A22', 'text' => 'ffffff' );
$calcolors[] = array( 'bg' => 'B3DC6C', 'text' => '000000' );
$calcolors[] = array( 'bg' => 'cc0000', 'text' => 'ffffff' );
echo "\n<link rel='stylesheet' type='text/css' href='../includes/jscript/css/fullcalendar.css' />\n<link rel='stylesheet' type='text/css' href='../includes/jscript/css/fullcalendar.print.css' media='print' />\n<link rel=\"stylesheet\" type=\"text/css\" href=\"../includes/jscript/css/jquery-ui-timepicker-addon.css\" />\n<script type='text/javascript' src='../includes/jscript/fullcalendar.min.js'></script>\n<script type=\"text/javascript\" src=\"../includes/jscript/jquery-ui-timepicker-addon.js\"></script>\n<script type='text/javascript'>\n\$(document).ready(function() {\nvar date = new Date();\nvar d = date.getDate();\nvar m = date.getMonth();\nvar y = date.getFullYear();\n\n\$('#calendar').fullCalendar({\n\n    header: {\n        left: 'prev,next today',\n        center: 'title',\n        right: 'month,agendaWeek,agendaDay'\n    },\n\n    buttonText: {\n        today: 'Today',\n        month: 'Month',\n        week: 'Week',\n        day: 'Day',\n    },\n\n    timeFormat: 'H:mm',\n\n    dayClick: function(date, allDay, jsEvent, view) {\n        var dateclicked = \$.fullCalendar.formatDate(date, 'yyyyMMdd');\n        var xpos = jsEvent.pageX;\n        if (xpos>(\$(window).width()-400)) xpos = xpos-350;\n        \$(\"#caledit\").css(\"top\",jsEvent.pageY);\n        \$(\"#caledit\").css(\"left\",xpos);\n        \$(\"#caledit\").load(\"calendar.php?action=fetch&ymd=\"+dateclicked+\"&token=";
echo generate_token('plain');
echo "\", function() {\n            \$('#allday').live('click', function() {\n                if(\$('#allday').attr(\"checked\")){\n                    \$('#end').attr(\"disabled\",true);\n                } else {\n                    \$('#end').attr(\"disabled\",false);\n                    \$('#end').live('click', function() {\n                        \$(this).datetimepicker({\n                            hour: 23,\n                            minute: 59,\n                            second: 59,\n                            defaultDate: date,\n                            showSecond:true,\n                            ampm:false,\n                            dateFormat: \"";
echo $localdateformat;
echo "\",\n                            timeFormat: \"hh:mm:ss\",\n                            showOn: \"focus\"\n                        }).focus();\n                    });\n                }\n            });\n            \$('#start').live('click', function() {\n                \$(this).datetimepicker({\n                    hour: 00,\n                    minute: 00,\n                    second: 00,\n                    defaultDate: date,\n                    showSecond:true,\n                    ampm:false,\n                    dateFormat: \"";
echo $localdateformat;
echo "\",\n                    timeFormat: \"hh:mm:ss\",\n                    showOn: \"focus\"\n                }).focus();\n            });\n        });     \n        \$(\"#caledit\").fadeIn();\n\n        //alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);\n        //alert('Current view: ' + view.name);\n        // change the day's background color just for fun\n        //\$(this).css('background-color', 'red');\n\n    },\n    eventClick: function(calEvent, jsEvent, view) {\n\n        var xpos = jsEvent.pageX;\n        if (xpos>(\$(window).width()-400)) xpos = xpos-350;\n        \$(\"#caledit\").css(\"top\",jsEvent.pageY);\n        \$(\"#caledit\").css(\"left\",xpos);\n        \$(\"#caledit\").html('<img src=\"images/loading.gif\" /> ";
echo $aInt->lang('global', 'loading', 1);
echo "');\n        \$.post(\"calendar.php\", { editentry: \"1\", id: calEvent.id, token: \"";
echo generate_token('plain');
echo "\" }, function(data) {\n            data = JSON.parse(data);\n/*\n            alert(data.defaultsh);\n            alert(data.defaultsm);\n            alert(data.defaulteh);\n            alert(data.defaultem);\n*/          \n            \$(\"#caledit\").html(data.html);\n            /* Disable End Field if All Days is selected\n            if(\$('#allday').attr(\"checked\")){\n                \$('#end').attr(\"disabled\",true);\n            } else {\n                \$('#end').attr(\"disabled\",false);\n            }\n            */          \n            \$('#start').datetimepicker({\n                hour: data.defaultsh,\n                minute: data.defaultsm,\n                defaultDate: new Date(data.defaultsdate),\n                showSecond:true,\n                ampm:false,\n                dateFormat: \"";
echo $localdateformat;
echo "\",\n                timeFormat: \"hh:mm:ss\",\n            });\n            \$('#end').datetimepicker({\n                hour: data.defaulteh,\n                minute: data.defaultem,\n                defaultDate: new Date(data.defaultedate),\n                showSecond:true,\n                ampm:false,\n                dateFormat: \"";
echo $localdateformat;
echo "\",\n                timeFormat: \"hh:mm:ss\",\n            });\n        });\n        \$(\"#caledit\").fadeIn();\n\n        //alert('Event: ' + calEvent.id);\n        //alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);\n        //alert('View: ' + view.name);\n        // change the border color just for fun\n        //\$(this).css('border-color', 'red');\n\n    },\n    eventDrop: function(calEvent,dayDelta,minuteDelta,allDay,revertFunc) {\n\n        \$.post(\"calendar.php\", { action: \"update\", id: calEvent.id, type: \"move\", days: dayDelta, minutes: minuteDelta, allday: allDay, token: \"";
echo generate_token('plain');
echo "\" });\n\n    },\n    eventResize: function(calEvent,dayDelta,minuteDelta,revertFunc) {\n\n        \$.post(\"calendar.php\", { action: \"update\", id: calEvent.id, type: \"resize\", days: dayDelta, minutes: minuteDelta, token: \"";
echo generate_token('plain');
echo "\" });\n\n    },\n    eventSources: [\n        ";
$i = 0;
foreach( $calevents as $calevent )
{
    if( !isset($calcolors[$i]) )
    {
        $i = 0;
    }
    echo "{ url: 'calendar.php?getcalfeed=1&feed=" . $calevent . "', color: '#" . $calcolors[$i]['bg'] . "', textColor: '#" . $calcolors[$i]['text'] . "' },";
    $i++;
}
echo "    ]\n\n});\n\n});\n\nfunction deleteEntry(id) {\n    jQuery(\"#calendar\").fullCalendar('removeEvents',id);\n    \$.post(\"calendar.php\", { action: \"delete\", id: id, token: \"";
echo generate_token('plain');
echo "\" });\n    jQuery(\"#caledit\").fadeOut();\n}\n\n</script>\n<style type=\"text/css\">\n#calendar {\n    margin: 0 auto;\n    width: 90%;\n    max-width: 1200px;\n}\n#caledit {\n    display:none;\n    position:absolute;\n    padding:8px;\n    background-color:#f2f2f2;\n    border:1px solid #ccc;\n    width:350px;\n    min-height:150px;\n    z-index:100;\n    -moz-border-radius: 5px;\n    -webkit-border-radius: 5px;\n    -o-border-radius: 5px;\n    border-radius: 5px;\n}\n#caledit p {\n    margin: 0 0 0 5px;\n}\n#calendarcontrols {\n    float: right;\n    margin: -45px 0 0 0;\n    padding: 5px 15px;\n    background-color: #F2F2F2;\n    border: 1px dashed #CCC;\n    font-size: 11px;\n    -moz-border-radius: 5px;\n    -webkit-border-radius: 5px;\n    -o-border-radius: 5px;\n    border-radius: 5px;\n}\n#calendarcontrols table td {\n    font-size: 11px;\n}\n</style>\n\n<div id=\"calendarcontrols\"><form method=\"post\" name=\"refreshform\" action=\"calendar.php?action=refresh\"><table cellpadding=\"0\"><tr><td><strong>Show/Hide:</strong></td><td><input type=\"checkbox\" onclick=\"document.refreshform.submit()\" name=\"displaytypes[services]\" ";
if( $caldisplaytypes['services'] == 'on' )
{
    echo 'checked';
}
echo " /></td><td>Products/Services</td><td><input type=\"checkbox\" onclick=\"document.refreshform.submit()\" name=\"displaytypes[addons]\"  ";
if( $caldisplaytypes['addons'] == 'on' )
{
    echo 'checked';
}
echo " /></td><td>Addons</td><td><input type=\"checkbox\" onclick=\"document.refreshform.submit()\" name=\"displaytypes[domains]\"  ";
if( $caldisplaytypes['domains'] == 'on' )
{
    echo 'checked';
}
echo " /></td><td>Domains</td><td><input type=\"checkbox\" onclick=\"document.refreshform.submit()\" name=\"displaytypes[todo]\"  ";
if( $caldisplaytypes['todo'] == 'on' )
{
    echo 'checked';
}
echo " /></td><td>To-Do Items</td><td><input type=\"checkbox\" onclick=\"document.refreshform.submit()\" name=\"displaytypes[events]\"  ";
if( $caldisplaytypes['events'] == 'on' )
{
    echo 'checked';
}
echo " /></td><td>Events</td></tr></table></form></div>\n\n<div id=\"calendar\"></div>\n\n<form method=\"post\" action=\"calendar.php?action=save\">\n<div id=\"caledit\"></div>\n</form>\n\n";
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->display();
function calendar_core_calendar($vars)
{
    $events = array(  );
    $result = select_query('tblcalendar', '', "start>=" . $vars['start'] . " AND end<=" . $vars['end']);
    while( $data = mysql_fetch_assoc($result) )
    {
        $events[] = array( 'id' => $data['id'], 'title' => $data['title'], 'start' => $data['start'], 'end' => $data['end'], 'allDay' => $data['allday'] ? true : false, 'editable' => true );
    }
    return $events;
}
function calendar_core_products($vars)
{
    $events = array(  );
    $result = select_query('tblhosting', "tblhosting.id,tblhosting.domain,tblhosting.nextduedate,tblproducts.name", "domainstatus IN ('Active','Suspended') AND nextduedate BETWEEN '" . date('Y-m-d', $vars['start']) . "' AND '" . date('Y-m-d', $vars['end']) . "'", '', '', '', "tblproducts ON tblproducts.id=tblhosting.packageid");
    while( $data = mysql_fetch_assoc($result) )
    {
        $events[] = array( 'id' => $data['id'], 'title' => $data['name'] . ($data['domain'] ? " - " . $data['domain'] : ''), 'start' => strtotime($data['nextduedate']) + 86400, 'allDay' => true, 'editable' => false, 'url' => "clientshosting.php?id=" . $data['id'] );
    }
    return $events;
}
function calendar_core_addons($vars)
{
    $addons = array(  );
    $result = select_query('tbladdons', 'id,name', '');
    while( $data = mysql_fetch_array($result) )
    {
        $addon_id = $data['id'];
        $addons[$addon_id] = $data['name'];
    }
    $events = array(  );
    $result = select_query('tblhostingaddons', 'id,addonid,name,hostingid,nextduedate', "status IN ('Active','Suspended') AND nextduedate BETWEEN '" . date('Y-m-d', $vars['start']) . "' AND '" . date('Y-m-d', $vars['end']) . "'");
    while( $data = mysql_fetch_assoc($result) )
    {
        $name = 0 < strlen($data['name']) ? $data['name'] : $addons[$data['addonid']];
        $events[] = array( 'id' => $data['id'], 'title' => $name, 'start' => strtotime($data['nextduedate']), 'allDay' => true, 'editable' => false, 'url' => "clientsservices.php?id=" . $data['hostingid'] . "&aid=" . $data['id'] );
    }
    return $events;
}
function calendar_core_domains($vars)
{
    $events = array(  );
    $result = select_query('tbldomains', '', "status IN ('Active','Suspended') AND nextduedate BETWEEN '" . date('Y-m-d', $vars['start']) . "' AND '" . date('Y-m-d', $vars['end']) . "'");
    while( $data = mysql_fetch_assoc($result) )
    {
        $events[] = array( 'id' => $data['id'], 'title' => "Domain Renewal - " . $data['domain'], 'start' => strtotime($data['nextduedate']) + 86400, 'allDay' => true, 'editable' => false, 'url' => "clientsdomains.php?id=" . $data['id'] );
    }
    return $events;
}
function calendar_core_todoitems($vars)
{
    $events = array(  );
    $result = select_query('tbltodolist', '', "duedate BETWEEN '" . date('Y-m-d', $vars['start']) . "' AND '" . date('Y-m-d', $vars['end']) . "'");
    while( $data = mysql_fetch_assoc($result) )
    {
        $events[] = array( 'id' => 'td' . $data['id'], 'title' => $data['title'], 'start' => strtotime($data['duedate']), 'allDay' => true, 'editable' => true, 'url' => "todolist.php?action=edit&id=" . $data['id'] );
    }
    return $events;
}