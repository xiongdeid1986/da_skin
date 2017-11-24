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
require(ROOTDIR . "/includes/gatewayfunctions.php");
require(ROOTDIR . "/includes/ticketfunctions.php");
$projectid = (int) $_REQUEST['projectid'];
$modulelink .= "&projectid=" . (int) $projectid;
if( !project_management_check_viewproject($projectid) )
{
    redir("module=project_management");
}
if( $a == 'addticket' )
{
    check_token("WHMCS.admin.default");
    if( project_management_checkperm("Associate Tickets") )
    {
        $ticketnum = $_REQUEST['ticketnum'];
        if( !trim($ticketnum) )
        {
            exit( $vars['_lang']['youmustenterticketnumber'] );
        }
        $data = get_query_vals('tbltickets', 'id,tid,date,title,status,lastreply', array( 'tid' => $ticketnum ));
        $ticketnum = $data['tid'];
        if( !$ticketnum )
        {
            exit( $vars['_lang']['ticketnumberenterednotfound'] );
        }
        $ticketids = get_query_val('mod_project', 'ticketids', array( 'id' => $projectid ));
        $ticketids = explode(',', $ticketids);
        if( in_array($ticketnum, $ticketids) )
        {
            exit( $vars['_lang']['ticketnumberalreadyassociated'] );
        }
        $ticketids[] = $ticketnum;
        update_query('mod_project', array( 'ticketids' => implode(',', $ticketids), 'lastmodified' => "now()" ), array( 'id' => $projectid ));
        project_management_log($projectid, $vars['_lang']['addedticketassociation'] . $ticketnum);
        if( $_REQUEST['ajax'] )
        {
            echo $projectid;
            exit();
        }
        $ticketid = $data['id'];
        $ticketdate = $data['date'];
        $ticketnum = $data['tid'];
        $tickettitle = $data['title'];
        $ticketstatus = $data['status'];
        $ticketlastreply = $data['lastreply'];
        foreach( $ticketids as $i => $ajaxticketnum )
        {
            if( $ticketnum == $ajaxticketnum )
            {
                $ajaxticketid = $i;
            }
        }
        echo "<tr id=\"ticketholder" . $i . "\"><td>" . fromMySQLDate($ticketdate, true) . "</td><td><a href=\"supporttickets.php?action=viewticket&id=" . $ticketid . "\" target=\"_blank\"><strong>#" . $ticketnum . " - " . $tickettitle . "</strong></a></td><td>" . getStatusColour($ticketstatus) . "</td><td>" . fromMySQLDate($ticketlastreply, true) . "</td><td>" . (project_management_checkperm("Associate Tickets") ? "<a class=\"deleteticket\" id=\"deleteticket" . $i . "\"><img src=\"images/delete.gif\"></a>" : '') . "</td></tr>";
    }
    else
    {
        echo $vars['_lang']['noticketassociatepermissions'];
    }
    exit();
}
if( $a == 'addinvoice' )
{
    check_token("WHMCS.admin.default");
    $invoicenum = $_REQUEST['invoicenum'];
    if( !trim($invoicenum) )
    {
        exit( $vars['_lang']['youmustenterinvoicenumber'] );
    }
    $data = get_query_vals('tblinvoices', 'id,date,datepaid,total,paymentmethod,status', array( 'id' => $invoicenum ));
    $invoicenum = $data['id'];
    if( !$invoicenum )
    {
        exit( $vars['_lang']['invoicenumberenterednotfound'] );
    }
    $invoiceids = get_query_val('mod_project', 'invoiceids', array( 'id' => $projectid ));
    $invoiceids = explode(',', $invoiceids);
    if( in_array($invoicenum, $invoiceids) )
    {
        exit( $vars['_lang']['invoicenumberalreadyassociated'] );
    }
    $invoiceids[] = $invoicenum;
    update_query('mod_project', array( 'invoiceids' => implode(',', $invoiceids), 'lastmodified' => "now()" ), array( 'id' => $projectid ));
    project_management_log($projectid, $vars['_lang']['addedinvoiceassociation'] . $invoicenum);
    $invoiceid = $data['id'];
    $invoicedate = $data['date'];
    $invoicedatepaid = $data['datepaid'] != "0000-00-00 00:00:00" ? fromMySQLDate($data['datepaid']) : '-';
    $invoicetotal = $data['total'];
    $paymentmethod = get_query_val('tblpaymentgateways', 'value', array( 'gateway' => $data['paymentmethod'], 'setting' => 'name' ));
    $invoicestatus = $data['status'];
    echo "<tr id=\"invoiceholder" . $i . "\"><td><a href=\"invoices.php?action=edit&id=" . $invoiceid . "\" target=\"_blank\">" . $invoiceid . "</a></td><td>" . fromMySQLDate($invoicedate) . "</td><td>" . $invoicedatepaid . "</td><td>" . $invoicetotal . "</td><td>" . $paymentmethod . "</td><td>" . getInvoiceStatusColour($invoicestatus) . "</td></tr>";
    exit();
}
if( $a == 'addmsg' )
{
    check_token("WHMCS.admin.default");
    if( project_management_checkperm("Post Messages") )
    {
        $message = ticketAutoHyperlinks(nl2br($_POST['msg']));
        $projectsdir = $attachments_dir . 'projects/' . $projectid . '/';
        $projectsdir2 = $attachments_dir . 'projects/';
        $attachments = array(  );
        if( isset($_FILES['attachments']) )
        {
            if( !is_dir($projectsdir2) )
            {
                mkdir($projectsdir2);
            }
            if( !file_exists($projectsdir2 . "index.php") )
            {
                $src = "<?php\nheader(\"Location: ../../index.php\");";
                try
                {
                    $file = new WHMCS_File($projectsdir2 . "index.php");
                    $file->create($src);
                }
                catch( Exception $e )
                {
                }
            }
            if( !is_dir($projectsdir) )
            {
                mkdir($projectsdir);
            }
            if( !file_exists($projectsdir . "index.php") )
            {
                $src = "<?php\nheader(\"Location: ../../../index.php\");";
                try
                {
                    $file = new WHMCS_File($projectsdir . "index.php");
                    $file->create($src);
                }
                catch( Exception $e )
                {
                }
            }
            foreach( $_FILES['attachments']['name'] as $num => $filename )
            {
                try
                {
                    $file = new WHMCS_File_Upload('attachments', $num);
                    $prefix = "{RAND}_";
                    $attachments[] = $file->move($projectsdir, $prefix);
                }
                catch( WHMCS_Exception_File_NotUploaded $e )
                {
                }
            }
        }
        insert_query('mod_projectmessages', array( 'projectid' => $projectid, 'date' => "now()", 'message' => $message, 'attachments' => implode(',', $attachments), 'adminid' => $_SESSION['adminid'] ));
        project_management_log($projectid, $vars['_lang']['newmsgposted']);
        redir("module=project_management&m=view&projectid=" . $projectid);
    }
}
else
{
    if( $a == 'updatestaffmsg' )
    {
        check_token("WHMCS.admin.default");
        $msgid = $_POST['msgid'];
        $msgtxt = WHMCS_Input_Sanitize::decode($_POST['msgtxt']);
        update_query('mod_projectmessages', array( 'message' => $msgtxt ), array( 'id' => $msgid ));
        project_management_log($projectid, "Edited Staff Message");
        echo nl2br(ticketAutoHyperlinks($msgtxt));
        exit();
    }
    if( $a == 'deletestaffmsg' )
    {
        check_token("WHMCS.admin.default");
        if( project_management_checkperm("Delete Messages") )
        {
            $msgid = (int) $_REQUEST['id'];
            $attachments = explode(',', get_query_val('mod_projectmessages', 'attachments', array( 'id' => $msgid )));
            $whmcs = WHMCS_Application::getinstance();
            $projectsdir = $whmcs->getAttachmentsDir() . 'projects/' . (int) $projectid . '/';
            foreach( $attachments as $i => $attachment )
            {
                if( $attachment )
                {
                    try
                    {
                        $file = new WHMCS_File($projectsdir . $attachment);
                        $file->delete();
                        project_management_log($projectid, $vars['_lang']['deletedattachment'] . " " . substr($attachment, 7));
                        unset($attachments[$i]);
                    }
                    catch( WHMCS_Exception_File_NotFound $e )
                    {
                    }
                }
            }
            delete_query('mod_projectmessages', array( 'id' => $msgid ));
            project_management_log($projectid, "Deleted Staff Message");
            echo $msgid;
        }
        else
        {
            echo '0';
        }
        exit();
    }
    if( $a == 'hookstarttimer' )
    {
        check_token("WHMCS.admin.default");
        $projectid = $_REQUEST['projectid'];
        $ticketnum = $_REQUEST['ticketnum'];
        $taskid = $_REQUEST['taskid'];
        $title = $_REQUEST['title'];
        if( !$taskid && $title )
        {
            $taskid = insert_query('mod_projecttasks', array( 'projectid' => $projectid, 'task' => $title, 'created' => "now()" ));
            project_management_log($projectid, $vars['_lang']['addedtask'] . $title);
        }
        $timerid = insert_query('mod_projecttimes', array( 'projectid' => $projectid, 'taskid' => $taskid, 'start' => time(), 'adminid' => $_SESSION['adminid'] ));
        project_management_log($projectid, $vars['_lang']['startedtimerfortask'] . get_query_val('mod_projecttasks', 'task', array( 'id' => $taskid )));
        if( $timerid )
        {
            $result = select_query('mod_projecttimes', "mod_projecttimes.id, mod_projecttimes.projectid, mod_project.title, mod_projecttimes.taskid, mod_projecttasks.task, mod_projecttimes.start", array( "mod_projecttimes.adminid" => $_SESSION['adminid'], "mod_projecttimes.end" => '', "mod_project.ticketids" => array( 'sqltype' => 'LIKE', 'value' => (int) $ticketnum ) ), '', '', '', "mod_projecttasks ON mod_projecttimes.taskid=mod_projecttasks.id INNER JOIN mod_project ON mod_projecttimes.projectid=mod_project.id");
            while( $data = mysql_fetch_array($result) )
            {
                echo "<div class=\"stoptimer" . $data['id'] . "\" style=\"padding-bottom:10px;\"><em>" . $data['title'] . " - Project ID " . $data['projectid'] . "</em><br />&nbsp;&raquo; " . $data['task'] . "<br />Started at " . fromMySQLDate(date("Y-m-d H:i:s", $data['start']), 1) . ":" . date('s', $data['start']) . " - <a href=\"#\" onclick=\"projectendtimersubmit('" . $data['projectid'] . "','" . $data['id'] . "');return false\"><strong>Stop Timer</strong></a></div>";
            }
        }
        else
        {
            echo '0';
        }
        exit();
    }
    if( $a == 'hookendtimer' )
    {
        check_token("WHMCS.admin.default");
        $timerid = $_POST['timerid'];
        $ticketnum = $_POST['ticketnum'];
        $taskid = get_query_val('mod_projecttimes', 'taskid', array( 'id' => $timerid, 'adminid' => $_SESSION['adminid'] ));
        $projectid = get_query_val('mod_projecttimes', 'projectid', array( 'id' => $timerid, 'adminid' => $_SESSION['adminid'] ));
        update_query('mod_projecttimes', array( 'end' => time() ), array( 'id' => $timerid, 'taskid' => $taskid, 'adminid' => $_SESSION['adminid'] ));
        project_management_log($projectid, $vars['_lang']['stoppedtimerfortask'] . get_query_val('mod_projecttasks', 'task', array( 'id' => $taskid )));
        if( !$taskid )
        {
            echo '0';
        }
        else
        {
            $result = select_query('mod_projecttimes', "mod_projecttimes.id, mod_projecttimes.projectid, mod_project.title, mod_projecttimes.taskid, mod_projecttasks.task, mod_projecttimes.start", array( "mod_projecttimes.adminid" => $_SESSION['adminid'], "mod_projecttimes.end" => '', "mod_project.ticketids" => array( 'sqltype' => 'LIKE', 'value' => (int) $ticketnum ) ), '', '', '', "mod_projecttasks ON mod_projecttimes.taskid=mod_projecttasks.id INNER JOIN mod_project ON mod_projecttimes.projectid=mod_project.id");
            while( $data = mysql_fetch_array($result) )
            {
                echo "<div class=\"stoptimer" . $data['id'] . "\" style=\"padding-bottom:10px;\"><em>" . $data['title'] . " - Project ID " . $data['projectid'] . "</em><br />&nbsp;&raquo; " . $data['task'] . "<br />Started at " . fromMySQLDate(date("Y-m-d H:i:s", $data['start']), 1) . ":" . date('s', $data['start']) . " - <a href=\"#\" onclick=\"projectendtimersubmit('" . $data['projectid'] . "','" . $data['id'] . "');return false\"><strong>Stop Timer</strong></a></div>";
            }
        }
        exit();
    }
    if( $a == 'starttimer' )
    {
        check_token("WHMCS.admin.default");
        $projectid = (int) $_REQUEST['projectid'];
        $taskid = (int) $_REQUEST['taskid'];
        $activetimers = select_query('mod_projecttimes', 'id', array( 'end' => '', 'projectid' => $projectid, 'taskid' => $taskid, 'adminid' => $_SESSION['adminid'] ));
        while( $activetimersdata = mysql_fetch_assoc($activetimers) )
        {
            update_query('mod_projecttimes', array( 'end' => time() ), array( 'id' => $activetimersdata['id'] ));
        }
        $timerstart = time();
        if( $projectid && $taskid )
        {
            $timerid = insert_query('mod_projecttimes', array( 'projectid' => $projectid, 'taskid' => $taskid, 'start' => $timerstart, 'adminid' => $_SESSION['adminid'] ));
            project_management_log($projectid, $vars['_lang']['startedtimerfortask'] . get_query_val('mod_projecttasks', 'task', array( 'id' => $taskid )));
            if( $timerid )
            {
                $timeradmin = get_query_val('tbladmins', "CONCAT(firstname,' ',lastname)", array( 'id' => $_SESSION['adminid'] ));
                $starttime = fromMySQLDate(date("Y-m-d H:i:s", $timerstart), 1) . ":" . date('s', $timerstart);
                $endtimerlink = $timerdata['adminid'] == $_SESSION['adminid'] || project_management_check_masteradmin() ? "<a rel=\"" . $timerid . "\" id=\"ajaxendtimertaskid" . $taskid . "\" class=\"ajaxendtimer timerlink\">" . $vars['_lang']['endtimer'] . "</a>" : $vars['_lang']['inprogress'];
                $deltimerlink = $timerdata['adminid'] == $_SESSION['adminid'] || project_management_check_masteradmin() ? "<a onclick=\"deleteTimer('" . $timerid . "','" . $taskid . "')\" href=\"#\"><img src=\"images/delete.gif\"></a>" : '';
                $endtime = $timerend ? fromMySQLDate(date("Y-m-d H:i:s", $timerend), 1) . ":" . date('s', $timerend) : $endtimerlink;
                $totaltime = $timerend ? project_management_sec2hms($timerend - $timerstart) : "In Progress";
                echo "<tr bgcolor=\"#ffffff\" class=\"time taskholder" . $taskid . "\"><td>" . $timeradmin . "</td><td>" . $starttime . "</td><td id=\"ajaxendtimertaskholderid" . $timerid . "\">" . $endtime . "</td><td id=\"ajaxtimerstatusholderid" . $timerid . "\">" . $totaltime . "</td><td>" . $deltimerlink . "</td></tr>";
            }
        }
        else
        {
            echo $projectid . " " . $taskid;
        }
        exit();
    }
    if( $a == 'endtimer' )
    {
        check_token("WHMCS.admin.default");
        $timerid = $_REQUEST['timerid'];
        $projectid = $_REQUEST['projectid'];
        $taskid = $_REQUEST['taskid'];
        update_query('mod_projecttimes', array( 'end' => time() ), array( 'id' => $timerid, 'taskid' => $taskid ));
        logActivity(get_query_val('mod_projecttimes', 'end-start', array( 'id' => $timerid, 'taskid' => $taskid )));
        $duration = project_management_sec2hms(get_query_val('mod_projecttimes', 'end-start', array( 'id' => $timerid, 'taskid' => $taskid )));
        project_management_log($projectid, $vars['_lang']['stoppedtimerfortask'] . get_query_val('mod_projecttasks', 'task', array( 'id' => $taskid )));
        if( $_REQUEST['ajax'] )
        {
            echo json_encode(array( 'time' => fromMySQLDate(date("Y-m-d H:i:s"), 1) . ":" . date('s'), 'duration' => $duration ));
        }
        else
        {
            redir("module=project_management&m=view&projectid=" . $projectid);
        }
        exit();
    }
    if( $a == 'deletetimer' )
    {
        check_token("WHMCS.admin.default");
        $timerid = $_REQUEST['id'];
        $taskid = $_REQUEST['taskid'];
        delete_query('mod_projecttimes', array( 'id' => $timerid, 'taskid' => $taskid ));
        project_management_log($projectid, $vars['_lang']['deletedtimerfortask'] . get_query_val('mod_projecttasks', 'task', array( 'id' => $taskid )));
        redir("module=project_management&m=view&projectid=" . $projectid);
    }
    else
    {
        if( $a == 'addtask' )
        {
            check_token("WHMCS.admin.default");
            $newtask = trim($_POST['newtask']);
            $maxorder = get_query_val('mod_projecttasks', "MAX(`order`)", array( 'projectid' => $projectid ));
            if( $newtask )
            {
                $taskid = insert_query('mod_projecttasks', array( 'projectid' => $projectid, 'task' => $newtask, 'created' => "now()", 'order' => $maxorder + 1 ));
                project_management_log($projectid, $vars['_lang']['addedtask'] . $newtask);
            }
            $taskedit = project_management_checkperm("Edit Tasks") ? " <a href=\"" . str_replace("&m=view", "&m=edittask", $modulelink) . "&id=" . $taskid . "\"><img src=\"images/edit.gif\" align=\"absmiddle\" /></a>" : '';
            $taskdelete = project_management_checkperm("Delete Tasks") ? " <a href=\"#\" onclick=\"deleteTask(" . $taskid . ");return false\"><img src=\"images/delete.gif\" align=\"absmiddle\" /></a>" : '';
            $timesoutput = project_management_timesoutput($vars, $taskid);
            $notesoutput = "<div style=\"margin-top:5px;\"><table width=\"95%\" align=\"center\"><tr><td><textarea rows=\"3\" style=\"width:100%\" id=\"tasknotestxtarea" . $taskid . "\">" . $tasknotes . "</textarea></td><td width=\"120\" align=\"right\"><input type=\"button\" id=\"savetasknotestxtarea" . $taskid . "\" class=\"savetasknotestxtarea\" value=\"" . $vars['_lang']['savenotes'] . "\" /></td></tr></table></div>";
            $tasknotes = "<a class=\"tasknotestoggler\" id=\"tasknotestogglerclicker" . $taskid . "\"><img src=\"../modules/addons/project_management/images/" . ($tasknotes ? '' : 'no') . "notes.png\" align=\"absmiddle\" title=\"View/Edit Notes\" /></a>";
            $tmptaskshtml = '';
            $taskshtml = "<tr id=\"taskholder" . $taskid . "\">\n    <td class=\"sortcol\"></td>\n    <td>\n    <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\n        <tr><td align=\"left\"><input type=\"checkbox\" name=\"task[" . $taskid . "]\" id=\"tk" . $taskid . "\" value=\"1\"" . $taskcompleted . " onclick=\"updatetaskstatus('" . $taskid . "')\" /> " . $taskadmin . "<label for=\"tk" . $taskid . "\">" . $newtask . "</label> " . $taskduedate . " <span class=\"taskbox\">" . project_management_sec2hms(0) . " Hrs</span> " . $tasknotes . " <div style=\"float:right;\"><a class=\"ajaxstarttimer tasktimerexpander\" id=\"ajaxstarttimer" . $taskid . "\"><img src=\"../modules/addons/project_management/images/starttimer.png\" align=\"absmiddle\" title=\"Start Timer\" /></a> <a id=\"tasktimertoggleclicker" . $taskid . "\" class=\"tasktimertoggle\"><img src=\"../modules/addons/project_management/images/notimes.png\" align=\"absmiddle\" title=\"View Times\" /></a> " . $taskedit . $taskdelete . "</div></td></tr>\n        <tr style=\"display:none\" id=\"tasktimerexpandholder" . $taskid . "\"><td>" . $timesoutput . "</td></tr>\n        <tr style=\"display:none\" id=\"tasknotesexpandholder" . $taskid . "\"><td>" . $notesoutput . "</td></tr>\n    </table>\n    </td>\n</tr>";
            echo $taskshtml;
            exit();
        }
        if( $a == 'updatetask' )
        {
            check_token("WHMCS.admin.default");
            if( $_REQUEST['taskid'] )
            {
                update_query('mod_projecttasks', array( 'completed' => $_REQUEST['status'] == 'checked' ? '1' : '0' ), array( 'id' => (int) $_REQUEST['taskid'] ));
            }
            $taskstatusdata = project_management_tasksstatus($projectid, $vars);
            echo $taskstatusdata['html'];
            exit();
        }
        if( $a == 'savetasksorder' )
        {
            check_token("WHMCS.admin.default");
            $torderarr = explode("&amp;", $_REQUEST['torderarr']);
            $tonum = 0;
            foreach( $torderarr as $v )
            {
                $v = explode("tasks[]=taskholder", $v);
                if( $v[1] )
                {
                    update_query('mod_projecttasks', array( 'order' => $tonum ), array( 'id' => $v[1] ));
                    $tonum++;
                }
            }
            exit();
        }
        if( $a == 'savetasknotes' )
        {
            check_token("WHMCS.admin.default");
            if( $_REQUEST['taskid'] )
            {
                update_query('mod_projecttasks', array( 'notes' => $_REQUEST['notes'] ), array( 'id' => (int) $_REQUEST['taskid'] ));
                echo '1';
            }
            else
            {
                echo '0';
            }
            exit();
        }
        if( $a == 'deletetask' )
        {
            check_token("WHMCS.admin.default");
            if( project_management_checkperm("Delete Tasks") )
            {
                $id = $_REQUEST['id'];
                delete_query('mod_projecttasks', array( 'projectid' => $projectid, 'id' => $id ));
                delete_query('mod_projecttimes', array( 'taskid' => $id ));
                project_management_log($projectid, $vars['_lang']['deletedtask']);
                echo $id;
                exit();
            }
        }
        else
        {
            if( $a == 'deleteticket' )
            {
                check_token("WHMCS.admin.default");
                if( project_management_checkperm("Associate Tickets") )
                {
                    $result = select_query('mod_project', 'ticketids', array( 'id' => $projectid ));
                    $data = mysql_fetch_array($result);
                    $ticketids = explode(',', $data['ticketids']);
                    project_management_log($projectid, $vars['_lang']['deletedticketrelationship'] . $ticketids[$_REQUEST['id']]);
                    unset($ticketids[$_REQUEST['id']]);
                    update_query('mod_project', array( 'ticketids' => implode(',', $ticketids), 'lastmodified' => "now()" ), array( 'id' => $projectid ));
                    echo $_REQUEST['id'];
                    exit();
                }
            }
            else
            {
                if( $a == 'projectsave' )
                {
                    check_token("WHMCS.admin.default");
                    $logmsg = '';
                    $result = select_query('mod_project', '', array( 'id' => $projectid ));
                    $data = mysql_fetch_array($result);
                    $updateqry['userid'] = $_POST['userid'];
                    $updateqry['title'] = $_POST['title'];
                    $updateqry['adminid'] = $_POST['adminid'];
                    $updateqry['created'] = toMySQLDate($_POST['created']);
                    $updateqry['duedate'] = toMySQLDate($_POST['duedate']);
                    $updateqry['lastmodified'] = "now()";
                    if( $_POST['completed'] )
                    {
                        update_query('mod_projecttasks', array( 'completed' => '1' ), array( 'projectid' => $projectid ));
                    }
                    if( !$logmsg )
                    {
                        if( $updateqry['title'] && $updateqry['title'] != $data['title'] )
                        {
                            $changes[] = $vars['_lang']['titlechangedfrom'] . $data['title'] . " to " . $updateqry['title'];
                        }
                        if( isset($updateqry['userid']) && $updateqry['userid'] != $data['userid'] )
                        {
                            $changes[] = $vars['_lang']['assignedclientchangedfrom'] . $data['userid'] . " " . $vars['_lang']['to'] . " " . $updateqry['userid'];
                        }
                        if( $updateqry['adminid'] != $data['adminid'] )
                        {
                            $changes[] = $vars['_lang']['assignedadminchangedfrom'] . ($data['adminid'] ? getAdminName($data['adminid']) : 'Nobody') . " " . $vars['_lang']['to'] . " " . ($updateqry['adminid'] ? getAdminName($updateqry['adminid']) : 'Nobody');
                        }
                        if( $_POST['created'] && $_POST['created'] != fromMySQLDate($data['created']) )
                        {
                            $changes[] = $vars['_lang']['creationdatechangedfrom'] . fromMySQLDate($data['created']) . " to " . $_POST['created'];
                        }
                        if( $_POST['duedate'] && $_POST['duedate'] != fromMySQLDate($data['duedate']) )
                        {
                            $changes[] = $vars['_lang']['duedatechangedfrom'] . fromMySQLDate($data['duedate']) . " to " . $_POST['duedate'];
                        }
                        if( $_POST['newticketid'] )
                        {
                            $changes[] = $vars['_lang']['addednewrelatedticket'] . $_POST['newticketid'];
                        }
                        if( $updateqry['notes'] && $updateqry['notes'] != $data['notes'] )
                        {
                            $changes[] = $vars['_lang']['notesupdated'];
                        }
                        if( $updateqry['completed'] && $updateqry['completed'] != $data['completed'] )
                        {
                            $changes[] = $vars['_lang']['projectmarkedcompleted'];
                        }
                        $logmsg = $vars['_lang']['updatedproject'] . implode(", ", $changes);
                    }
                    if( count($changes) )
                    {
                        project_management_log($projectid, $logmsg);
                    }
                    update_query('mod_project', $updateqry, array( 'id' => $projectid ));
                    echo project_management_daysleft(toMySQLDate($_POST['duedate']), $vars);
                    exit();
                }
                if( $a == 'statussave' )
                {
                    check_token("WHMCS.admin.default");
                    if( project_management_checkperm("Update Status") )
                    {
                        $status = db_escape_string($_POST['status']);
                        $statuses = explode(',', $vars['statusvalues']);
                        $statusarray = array(  );
                        foreach( $statuses as $tmpstatus )
                        {
                            $tmpstatus = explode("|", $tmpstatus, 2);
                            $statusarray[] = $tmpstatus[0];
                        }
                        if( in_array($status, $statusarray) )
                        {
                            $oldstatus = get_query_val('mod_project', 'status', array( 'id' => $projectid ));
                            $updateqry = array( 'status' => $status );
                            if( in_array($status, explode(',', $vars['completedstatuses'])) )
                            {
                                $updateqry['completed'] = '1';
                            }
                            else
                            {
                                $updateqry['completed'] = '0';
                            }
                            update_query('mod_project', $updateqry, array( 'id' => $projectid ));
                            project_management_log($projectid, $vars['_lang']['statuschangedfrom'] . $oldstatus . " " . $vars['_lang']['to'] . " " . $status);
                        }
                    }
                    exit();
                }
                if( $a == 'addattachment' )
                {
                    check_token("WHMCS.admin.default");
                    $projectsdir = $attachments_dir . 'projects/' . $projectid . '/';
                    $projectsdir2 = $attachments_dir . 'projects/';
                    if( !is_dir($projectsdir2) )
                    {
                        mkdir($projectsdir2);
                    }
                    if( !file_exists($projectsdir2 . "index.php") )
                    {
                        $src = "<?php\nheader(\"Location: ../../index.php\");";
                        try
                        {
                            $file = new WHMCS_File($projectsdir2 . "index.php");
                            $file->create($src);
                        }
                        catch( Exception $e )
                        {
                        }
                    }
                    if( !is_dir($projectsdir) )
                    {
                        mkdir($projectsdir);
                    }
                    if( !file_exists($projectsdir . "index.php") )
                    {
                        $src = "<?php\nheader(\"Location: ../../../index.php\");";
                        try
                        {
                            $file = new WHMCS_File($projectsdir . "index.php");
                            $file->create($src);
                        }
                        catch( Exception $e )
                        {
                        }
                    }
                    $attachments = explode(',', get_query_val('mod_project', 'attachments', array( 'id' => $projectid )));
                    if( empty($attachments[0]) )
                    {
                        unset($attachments[0]);
                    }
                    if( isset($_FILES['attachments']) )
                    {
                        foreach( $_FILES['attachments']['name'] as $num => $filename )
                        {
                            try
                            {
                                $file = new WHMCS_File_Upload('attachments', $num);
                                $prefix = "{RAND}_";
                                $filename = $file->move($projectsdir, $prefix);
                                $attachments[] = $filename;
                                project_management_log($projectid, $vars['_lang']['addedattachment'] . " " . $file->getCleanName());
                            }
                            catch( WHMCS_Exception_File_NotUploaded $e )
                            {
                            }
                        }
                    }
                    update_query('mod_project', array( 'attachments' => implode(',', $attachments) ), array( 'id' => $projectid ));
                    redir("module=project_management&m=view&projectid=" . $projectid);
                }
                else
                {
                    if( $a == 'deleteattachment' )
                    {
                        check_token("WHMCS.admin.default");
                        if( project_management_check_masteradmin() )
                        {
                            $attachments = explode(',', get_query_val('mod_project', 'attachments', array( 'id' => $projectid )));
                            $projectsdir = $whmcs->getAttachmentsDir() . 'projects/' . (int) $projectid . '/';
                            $i = (int) $_REQUEST['i'];
                            try
                            {
                                $file = new WHMCS_File($projectsdir . $attachments[$i]);
                                $file->delete();
                            }
                            catch( WHMCS_Exception_File_NotFound $e )
                            {
                            }
                            project_management_log($projectid, $vars['_lang']['deletedattachment'] . " " . substr($attachments[$i], 7));
                            unset($attachments[$i]);
                            update_query('mod_project', array( 'attachments' => implode(',', $attachments), 'lastmodified' => "now()" ), array( 'id' => $projectid ));
                        }
                        redir("module=project_management&m=view&projectid=" . $projectid);
                    }
                    else
                    {
                        if( $a == 'addquickinvoice' )
                        {
                            check_token("WHMCS.admin.default");
                            $newinvoice = trim($_REQUEST['newinvoice']);
                            $newinvoiceamt = trim($_REQUEST['newinvoiceamt']);
                            if( $newinvoice && $newinvoiceamt )
                            {
                                $userid = get_query_val('mod_project', 'userid', array( 'id' => $projectid ));
                                $gateway = function_exists('getClientsPaymentMethod') ? getClientsPaymentMethod($userid) : 'paypal';
                                if( $CONFIG['TaxEnabled'] == 'on' )
                                {
                                    $clientsdetails = getClientsDetails($userid);
                                    if( !$clientsdetails['taxexempt'] )
                                    {
                                        $state = $clientsdetails['state'];
                                        $country = $clientsdetails['country'];
                                        $taxdata = getTaxRate(1, $state, $country);
                                        $taxdata2 = getTaxRate(2, $state, $country);
                                        $taxrate = $taxdata['rate'];
                                        $taxrate2 = $taxdata2['rate'];
                                    }
                                }
                                $invoiceid = insert_query('tblinvoices', array( 'date' => "now()", 'duedate' => "now()", 'userid' => $userid, 'status' => 'Unpaid', 'paymentmethod' => $gateway, 'taxrate' => $taxrate, 'taxrate2' => $taxrate2 ));
                                insert_query('tblinvoiceitems', array( 'invoiceid' => $invoiceid, 'userid' => $userid, 'type' => 'Project', 'relid' => $projectid, 'description' => $newinvoice, 'paymentmethod' => $gateway, 'amount' => $newinvoiceamt, 'taxed' => '1' ));
                                updateInvoiceTotal($invoiceid);
                                $invoiceids = get_query_val('mod_project', 'invoiceids', array( 'id' => $projectid ));
                                $invoiceids = explode(',', $invoiceids);
                                $invoiceids[] = $invoiceid;
                                $invoiceids = implode(',', $invoiceids);
                                update_query('mod_project', array( 'invoiceids' => $invoiceids ), array( 'id' => $projectid ));
                                project_management_log($projectid, $vars['_lang']['addedquickinvoice'] . " " . $invoiceid, $userid);
                                WHMCS_Invoices::adjustincrementfornextinvoice($invoiceid);
                                $invoiceArr = array( 'source' => 'adminarea', 'user' => WHMCS_Session::get('adminid'), 'invoiceid' => $invoiceid );
                                run_hook('InvoiceCreation', $invoiceArr);
                                run_hook('InvoiceCreationAdminArea', $invoiceArr);
                            }
                            redir("module=project_management&m=view&projectid=" . $projectid);
                        }
                        else
                        {
                            if( $a == 'gettimesheethead' )
                            {
                                check_token("WHMCS.admin.default");
                                echo "<link href=\"../includes/jscript/css/ui.all.css\" type=\"text/css /><script src=\"../includes/jscript/jquery.js\"></script><script src=\"../includes/jscript/jqueryui.js\"></script>";
                                exit();
                            }
                            if( $a == 'gettimesheet' )
                            {
                                check_token("WHMCS.admin.default");
                                if( project_management_checkperm("Bill Tasks") )
                                {
                                    echo "<form method=\"post\" action=\"" . $modulelink . "&a=dynamicinvoicegenerate\">\n        " . generate_token() . "\n<div class=\"box\">\n<table width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\" class=\"tasks\" id=\"tasks\"><tr bgcolor=\"#efefef\">\n    <th width=\"60%\">" . $vars['_lang']['description'] . "</th><th width=\"10%\">" . $vars['_lang']['hours'] . "</th><th width=\"14%\">" . $vars['_lang']['rate'] . "</th><th width=\"15%\">" . $vars['_lang']['amount'] . "</th><th width=\"20\"></th></tr>";
                                    $dti = 0;
                                    for( $tasksresult = select_query('mod_projecttasks', 'id,task', array( 'projectid' => $projectid, 'billed' => '0' )); $tasksdata = mysql_fetch_assoc($tasksresult); $dti++ )
                                    {
                                        $dynamictimes[$dti]['seconds'] = get_query_val('mod_projecttimes', "SUM(end-start)", array( 'taskid' => $tasksdata['id'], 'donotbill' => 0 ));
                                        $dynamictimes[$dti]['description'] = $tasksdata['task'];
                                        $dynamictimes[$dti]['rate'] = $vars['hourlyrate'];
                                        $dynamictimes[$dti]['amount'] = $dynamictimes[$dti]['rate'] * $dynamictimes[$dti]['seconds'] / 3600;
                                        if( 0 < $dynamictimes[$dti]['seconds'] )
                                        {
                                            echo "<tr id=\"dynamictaskinvoiceitemholder" . $dti . "\">\n            <td><input type=\"hidden\" name=\"taskid[" . $dti . "]\" value=\"" . $tasksdata['id'] . "\" /><input style=\"width:99%\" type=\"text\" name=\"description[" . $dti . "]\" value=\"" . $dynamictimes[$dti]['description'] . "\" /></td>\n            <td><input type=\"hidden\" id=\"dynamicbillhours" . $dti . "\" name=\"hours[" . $dti . "]\" value=\"" . round($dynamictimes[$dti]['seconds'] / 3600, 2) . "\" /><input type=\"text\" name=\"displayhours[" . $dti . "]\" class=\"dynamicbilldisplayhours\" id=\"dynamicbilldisplayhours" . $dti . "\" name=\"hours[" . $dti . "]\" value=\"" . project_management_sec2hms($dynamictimes[$dti]['seconds']) . "\" /></td>\n            <td><input type=\"text\" class=\"dynamicbillrate\" id=\"dynamicbillrate" . $dti . "\" name=\"rate[" . $dti . "]\" value=\"" . format_as_currency($dynamictimes[$dti]['rate']) . "\" /></td>\n            <td><input type=\"text\" id=\"dynamicbillamount" . $dti . "\" name=\"amount[" . $dti . "]\" value=\"" . format_as_currency($dynamictimes[$dti]['amount'], 2) . "\" /></td>\n            <td><a class=\"deldynamictaskinvoice\" id=\"deldynamictaskinvoice" . $dti . "\"><img src=\"images/delete.gif\"></a></td></tr>";
                                        }
                                    }
                                    echo "</table></div>\n        <p align=\"center\">\n            <input type=\"submit\" value=\"" . $vars['_lang']['generatenow'] . "\" />&nbsp;\n            <input type=\"submit\" onClick=\"form.action='" . $modulelink . "&a=dynamicinvoicegenerate&sendinvoicegenemail=true&token=" . generate_token('plain') . "'\" value=\"" . $vars['_lang']['generatenowandemail'] . "\" />&nbsp;\n            <input type=\"button\" id=\"dynamictasksinvoicecancel\" value=\"" . $vars['_lang']['cancel'] . "\" />\n        </p>\n        </form>";
                                }
                                exit();
                            }
                            if( $a == 'dynamicinvoicegenerate' )
                            {
                                check_token("WHMCS.admin.default");
                                if( !project_management_checkperm("Bill Tasks") )
                                {
                                    redir("module=project_management");
                                }
                                $userid = get_query_val('mod_project', 'userid', array( 'id' => $projectid ));
                                $gateway = function_exists('getClientsPaymentMethod') ? getClientsPaymentMethod($userid) : 'paypal';
                                if( $CONFIG['TaxEnabled'] == 'on' )
                                {
                                    $clientsdetails = getClientsDetails($userid);
                                    if( !$clientsdetails['taxexempt'] )
                                    {
                                        $state = $clientsdetails['state'];
                                        $country = $clientsdetails['country'];
                                        $taxdata = getTaxRate(1, $state, $country);
                                        $taxdata2 = getTaxRate(2, $state, $country);
                                        $taxrate = $taxdata['rate'];
                                        $taxrate2 = $taxdata2['rate'];
                                    }
                                }
                                $invoiceid = insert_query('tblinvoices', array( 'date' => "now()", 'duedate' => "now()", 'userid' => $userid, 'status' => 'Unpaid', 'paymentmethod' => $gateway, 'taxrate' => $taxrate, 'taxrate2' => $taxrate2 ));
                                WHMCS_Invoices::adjustincrementfornextinvoice($invoiceid);
                                foreach( $_REQUEST['taskid'] as $taski => $taskid )
                                {
                                    update_query('mod_projecttasks', array( 'billed' => 1 ), array( 'id' => $taskid ));
                                }
                                foreach( $_REQUEST['description'] as $desci => $description )
                                {
                                    if( $description && $_REQUEST['displayhours'][$desci] && $_REQUEST['rate'][$desci] && $_REQUEST['amount'][$desci] )
                                    {
                                        $description .= " - " . $_REQUEST['displayhours'][$desci] . " " . $vars['_lang']['hours'];
                                        if( $_REQUEST['rate'][$desci] != $vars['hourlyrate'] )
                                        {
                                            $amount = $_REQUEST['hours'][$desci] * $_REQUEST['rate'][$desci];
                                        }
                                        else
                                        {
                                            $amount = $_REQUEST['amount'][$desci];
                                        }
                                        insert_query('tblinvoiceitems', array( 'invoiceid' => $invoiceid, 'userid' => $userid, 'type' => 'Project', 'relid' => $projectid, 'description' => $description, 'paymentmethod' => $gateway, 'amount' => round($amount, 2), 'taxed' => '1' ));
                                        updateInvoiceTotal($invoiceid);
                                    }
                                }
                                $invoiceids = get_query_val('mod_project', 'invoiceids', array( 'id' => $projectid ));
                                $invoiceids = explode(',', $invoiceids);
                                $invoiceids[] = $invoiceid;
                                $invoiceids = implode(',', $invoiceids);
                                update_query('mod_project', array( 'invoiceids' => $invoiceids ), array( 'id' => $projectid ));
                                if( $invoiceid && $_REQUEST['sendinvoicegenemail'] == 'true' )
                                {
                                    sendMessage("Invoice Created", $invoiceid);
                                }
                                project_management_log($projectid, $vars['_lang']['createdtimebasedinvoice'] . " " . $invoiceid, $userid);
                                $invoiceArr = array( 'source' => 'adminarea', 'user' => WHMCS_Session::get('adminid'), 'invoiceid' => $invoiceid );
                                run_hook('InvoiceCreation', $invoiceArr);
                                run_hook('InvoiceCreationAdminArea', $invoiceArr);
                                redir("module=project_management&m=view&projectid=" . $projectid);
                            }
                            else
                            {
                                if( $a == 'savetasklist' )
                                {
                                    check_token("WHMCS.admin.default");
                                    $tasksarray = array(  );
                                    $result = select_query('mod_projecttasks', '', array( 'projectid' => $_REQUEST['projectid'] ), 'order', 'ASC');
                                    while( $data = mysql_fetch_array($result) )
                                    {
                                        $tasksarray[] = array( 'task' => $data['task'], 'notes' => $data['notes'], 'adminid' => $data['adminid'], 'duedate' => $data['duedate'] );
                                    }
                                    insert_query('mod_projecttasktpls', array( 'name' => $_REQUEST['taskname'], 'tasks' => serialize($tasksarray) ));
                                }
                                else
                                {
                                    if( $a == 'loadtasklist' )
                                    {
                                        check_token("WHMCS.admin.default");
                                        $maxorder = get_query_val('mod_projecttasks', "MAX(`order`)", array( 'projectid' => $_REQUEST['projectid'] ));
                                        $result = select_query('mod_projecttasktpls', 'tasks', array( 'id' => $_REQUEST['tasktplid'] ));
                                        $data = mysql_fetch_array($result);
                                        $tasks = unserialize($data['tasks']);
                                        foreach( $tasks as $task )
                                        {
                                            $maxorder++;
                                            insert_query('mod_projecttasks', array( 'projectid' => $_REQUEST['projectid'], 'task' => $task['task'], 'notes' => $task['notes'], 'adminid' => $task['adminid'], 'created' => "now()", 'order' => $maxorder ));
                                        }
                                        redir("module=project_management&m=view&projectid=" . $projectid);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
if( $projectid )
{
    $result = select_query('mod_project', '', array( 'id' => $projectid ));
    $data = mysql_fetch_array($result);
    $projectid = $data['id'];
    if( !$projectid )
    {
        echo "<p><b>" . $vars['_lang']['viewingproject'] . "</b></p><p>" . $vars['_lang']['projectidnotfound'] . "</p>";
        return NULL;
    }
    $title = $data['title'];
    $attachments = $data['attachments'];
    $ticketids = $data['ticketids'];
    $notes = $data['notes'];
    $userid = $data['userid'];
    $adminid = $data['adminid'];
    $created = $data['created'];
    $duedate = $data['duedate'];
    $completed = $data['completed'];
    $projectstatus = $data['status'];
    $lastmodified = $data['lastmodified'];
    $daysleft = project_management_daysleft($duedate, $vars);
    $attachments = explode(',', $attachments);
    $ticketids = explode(',', $ticketids);
    $created = fromMySQLDate($created);
    $duedate = fromMySQLDate($duedate);
    $lastmodified = fromMySQLDate($lastmodified, true);
    $client = '';
    if( !$userid )
    {
        foreach( $ticketids as $i => $ticketnum )
        {
            if( $ticketnum )
            {
                $result = select_query('tbltickets', 'userid', array( 'tid' => $ticketnum ));
                $data = mysql_fetch_array($result);
                $userid = $data['userid'];
                update_query('mod_project', array( 'userid' => $userid ), array( 'id' => $projectid ));
            }
        }
    }
    if( $userid )
    {
        $result = select_query('tblclients', 'id,firstname,lastname,companyname', array( 'id' => $userid ));
        $data = mysql_fetch_array($result);
        $clientname = $data[1] . " " . $data[2];
        if( $data[3] )
        {
            $clientname .= " (" . $data[3] . ")";
        }
        $client = "<a href=\"clientssummary.php?userid=" . $userid . "\">" . $clientname . "</a>";
    }
    $headtitle = $title;
}
else
{
    $headtitle = $vars['_lang']['newproject'];
    $daysleft = $client = '';
    $created = getTodaysDate();
    $duedate = getTodaysDate();
}
$admin = trim(get_query_val('tbladmins', "CONCAT(firstname,' ',lastname)", array( 'id' => $adminid )));
if( !$admin )
{
    $admin = $vars['_lang']['none'];
}
if( !$client )
{
    $client = $vars['_lang']['none'];
}
$jquerycode .= "\$(\"#addattachment\").click(function () {\n    \$(\"#attachments\").append(\"<input type=\\\"file\\\" name=\\\"attachments[]\\\" size=\\\"30\\\" /><br />\");\n    return false;\n});\n\$(\"#addmsgattachment\").click(function () {\n    \$(\"#msgattachments\").append(\"<input type=\\\"file\\\" name=\\\"attachments[]\\\" size=\\\"30\\\" /><br />\");\n    return false;\n});";
if( $projectid )
{
    $jquerycode .= "\n\$(\"#statuschange\").change(function () {\n    \$(\"#savesuccess\").fadeIn();\n    \$.post(\"" . $modulelink . "&a=statussave\",\n        {\n            status : \$(\"#statuschange\").val(),\n            token: \"" . generate_token('plain') . "\"\n        },\n        function (data) {\n            \$(\"#savesuccess\").fadeOut(5000);\n        }\n    );\n});\n\$(\"#editprojectbtn\").click(function() {\n    \$(\".displayval\").fadeOut(\"fast\", function() {\n        \$(\".editfield\").fadeIn();\n    });\n    \$(\"#editprojectform\").fadeIn();\n    \$(\"#editprojectbtn\").hide();\n    \$(\"#saveprojectbtn\").fadeIn();\n    \$(\"#cancelsaveprojectbtn\").fadeIn();\n});\n\$(\"#cancelsaveprojectbtn\").click(function() {\n    \$(\"#saveprojectbtn\").hide();\n    \$(\"#cancelsaveprojectbtn\").hide();\n    \$(\"#editprojectbtn\").show();\n    \$(\".editfield\").fadeOut(\"fast\", function() {\n        \$(\".displayval\").fadeIn();\n    });\n});\n\$(\"#saveprojectbtn\").click(function() {\n    saveProject();\n});\n\$(\"#projecttitleeditfield\").bind(\"keypress\", function(e) {\n    if((e.keyCode ? e.keyCode : e.which) == 13) {\n        saveProject();\n    }\n});\n\$(document).on(\"click\",\".ajaxstarttimer\",function(){\n    var extraParams = {\n        taskid: \$(this).attr(\"id\").replace(\"ajaxstarttimer\", \"\"),\n    };\n    \$.post(\"" . $modulelink . "&a=starttimer\",\n        {\n            taskid: extraParams.taskid,\n            token: \"" . generate_token('plain') . "\"\n        },\n        function(data){\n            ajaxstarttimercallback(data, extraParams)\n        }\n    );\n\n    function ajaxstarttimercallback(data,extraParams) {\n        \$(\".taskholder\"+extraParams.taskid+\":last\").after(data);\n        \$(\"#notasktimersexist\"+extraParams.taskid).hide();\n    };\n});\n\$(document).on(\"click\",\".ajaxendtimer\", function(){\n    var extraParams = {\n        taskid: \$(this).attr(\"id\").replace(\"ajaxendtimertaskid\", \"\"),\n        timerid: \$(this).attr(\"rel\"),\n    };\n\n    \$.post(\"" . $modulelink . "&a=endtimer&ajax=1\",\n        {\n            taskid: extraParams.taskid,\n            timerid: extraParams.timerid,\n            token: \"" . generate_token('plain') . "\"\n        },\n        function(data) {\n            ajaxendtimercallback(data, extraParams)\n        }\n    );\n\n    function ajaxendtimercallback(data,extraParams) {\n        data = \$.parseJSON(data);\n        \$(\"#ajaxendtimertaskholderid\"+extraParams.timerid).html(data.time);\n        \$(\"#ajaxtimerstatusholderid\"+extraParams.timerid).html(data.duration);\n    };\n\n});\n\$(document).on(\"keyup\",\".dynamicbilldisplayhours\", function(){\n    hms = \$(this).val().split(\":\");\n    hours = Number(hms[0])+Number(hms[1]/60)+Number(hms[2]/3600);\n    thisidattrval = \$(this).attr(\"id\").replace(\"dynamicbilldisplayhours\",\"\");\n    \$(\"#dynamicbillhours\"+thisidattrval).val(hours);\n    amount = \$(\"#dynamicbillhours\"+thisidattrval).val()*\$(\"#dynamicbillrate\"+thisidattrval).val();\n    \$(\"#dynamicbillamount\"+thisidattrval).val(amount.toFixed(2));\n\n});\n\$(document).on(\"keyup\",\".dynamicbillrate\", function(){  \$(\"#dynamicbillamount\"+\$(this).attr(\"id\").replace(\"dynamicbillrate\",\"\")).val(parseFloat(\$(\"#dynamicbillhours\"+\$(this).attr(\"id\").replace(\"dynamicbillrate\",\"\")).val() * \$(this).attr(\"value\")).toFixed(2));\n});\n\$(document).on(\"click\",\".deleteticket\", function(){\n    if (confirm('Are you sure to delete this ticket?')) {\n        \$.post(\"" . $modulelink . "&a=deleteticket\",\n            {\n                id: \$(this).attr(\"id\").replace(\"deleteticket\", \"\"),\n                token: \"" . generate_token('plain') . "\"\n            },\n            function(data) {\n                if (data!=0) {\n                    \$(\"#ticketholder\"+data).hide();\n                } else {\n                    alert(\"" . $vars['_lang']['youmustbeanadmintodeleteticket'] . "\");\n                }\n            }\n        );\n    }\n});\n\$(document).on(\"click\",\"#dynamictasksinvoicegen\", function(){\n    \$(\"#dynamictasksinvoiceloading\").show();\n    \$(\"#dynamictasksinvoicegen\").attr(\"disabled\",\"true\");\n    dynamictasksinvoicegencalled = true;\n    \$.get(\"" . $modulelink . "&a=gettimesheet&token=" . generate_token('plain') . "\", function(data) {\n      \$(\"#dynamictasksinvoiceholder\").append(data);\n      \$(\"#dynamictasksinvoiceloading\").hide();\n      \$(\"#dynamictasksinvoicegen\").slideUp();\n      \$(\"#dynamictasksinvoiceholder\").slideDown();\n    });\n});\n\$(document).on(\"click\",\"#dynamictasksinvoicecancel\", function(){\n    \$(\"#dynamictasksinvoiceloading\").show();\n    \$(\"#dynamictasksinvoiceholder\").html(\"\");\n    \$(\"#dynamictasksinvoicegen\").removeAttr(\"disabled\");\n    \$.get(\"" . $modulelink . "&a=gettimesheethead&token=" . generate_token('plain') . "\", function(data) {\n      \$(\"#dynamictasksinvoiceholder\").html(data);\n        \$(\"#dynamictasksinvoiceloading\").hide();\n        \$(\"#dynamictasksinvoiceholder\").slideUp();\n        \$(\"#dynamictasksinvoicegen\").slideDown();\n    });\n});\n\$(document).on(\"click\",\".deldynamictaskinvoice\", function(){\n    \$(\"#dynamictaskinvoiceitemholder\"+\$(this).attr(\"id\").replace(\"deldynamictaskinvoice\",\"\")).remove();\n});\n\$(document).on(\"click\",\".deletestaffmsg\", function(){\n    if (confirm('" . $vars['_lang']['confirmdeletestaffmsg'] . "')) {\n        \$.post(\"" . $modulelink . "&a=deletestaffmsg\",\n            {\n                id: \$(this).attr(\"id\").replace(\"deletestaffmsg\", \"\"),\n                token: \"" . generate_token('plain') . "\"\n            },\n            function(data) {\n                if(data!=0){\n                    \$(\"#msg\"+data).hide();\n                } else {\n                    alert(\"" . $vars['_lang']['youmustbeanadmintodeletemsg'] . "\");\n                }\n            }\n        );\n    }\n});\n\$(\"#tasks\").tableDnD({\n    onDrop: function(table, row) {\n        \$.post(\"" . $modulelink . "\",\n            {\n                a: \"savetasksorder\",\n                torderarr: \$(\"#tasks\").tableDnDSerialize(),\n                token: \"" . generate_token('plain') . "\"\n            }\n        );\n    },\n    dragHandle: \"sortcol\"\n});\n\$(document).on(\"click\",\".tasktimertoggle\", function(){\n    \$(\"#tasktimerexpandholder\"+\$(this).attr(\"id\").replace(\"tasktimertoggleclicker\",\"\")).fadeToggle(\"slow\");\n});\n\$(document).on(\"click\",\".tasktimerexpander\", function(){\n    \$(\"#tasktimerexpandholder\"+\$(this).attr(\"id\").replace(\"ajaxstarttimer\",\"\")).fadeIn(\"slow\");\n});\n\$(document).on(\"click\",\".tasknotestoggler\", function(){\n    \$(\"#tasknotesexpandholder\"+\$(this).attr(\"id\").replace(\"tasknotestogglerclicker\",\"\")).fadeToggle(\"slow\");\n});\n\$(document).on(\"click\",\".savetasknotestxtarea\", function(){\n    var thisid = \$(this).attr(\"id\");\n    \$(\"#\"+thisid).val(\"" . $vars['_lang']['saving'] . "\");\n    \$.post(\"" . $modulelink . "\",\n        {\n            a: \"savetasknotes\",\n            taskid:\$(this).attr(\"id\").replace(\"savetasknotestxtarea\",\"\"),\n            notes: \$(\"#tasknotestxtarea\"+\$(this).attr(\"id\").replace(\"savetasknotestxtarea\",\"\")).val(),\n            token: \"" . generate_token('plain') . "\"\n        },\n        function(data){\n            if(data == \"1\"){\n                \$(\"#\"+thisid).hide().val(\"" . $vars['_lang']['savenotes'] . "\").fadeIn(\"slow\");\n            } else {\n                \$(\"#\"+thisid).hide().val(\"" . $vars['_lang']['savenotesfailed'] . "\").fadeIn(\"slow\");\n            }\n        }\n    );\n});\n\$(\".editstaffmsg\").click(function() {\n    var msgid = \$(this).attr(\"id\").replace(\"editstaffmsg\",\"\");\n    \$(\"#msgholder\"+msgid).hide();\n    \$(\"#msgeditorholder\"+msgid).fadeIn(\"slow\");\n});\n\$(\".msgeditorsavechanges\").click(function() {\n    var msgid = \$(this).attr(\"id\").replace(\"msgeditorsavechanges\",\"\");\n    var msgtxt =  \$(\"#msgeditor\"+msgid).val();\n    \$.post(\"" . $modulelink . "\",\n        {\n            a: \"updatestaffmsg\",\n            msgid:msgid,\n            msgtxt: msgtxt,\n            token: \"" . generate_token('plain') . "\"\n        },\n    function(data){\n        \$(\"#msgeditorholder\"+msgid).hide();\n        \$(\"#msgholder\"+msgid).html(data);\n        \$(\"#msgholder\"+msgid).fadeIn(\"slow\");\n    });\n});\n\$(\"#clientname\").keyup(function () {\n    var ticketuseridsearchlength = \$(\"#clientname\").val().length;\n    if (ticketuseridsearchlength>2) {\n    \$.post(\"search.php\", { ticketclientsearch: 1, value: \$(\"#clientname\").val(), token: \"" . generate_token('plain') . "\" },\n        function(data){\n            if (data) {\n                \$(\"#ticketclientsearchresults\").html(data);\n                \$(\"#ticketclientsearchresults\").slideDown(\"slow\");\n                \$(\"#clientsearchcancel\").fadeIn();\n            }\n        });\n    }\n});\n\$(\"#clientsearchcancel\").click(function () {\n    \$(\"#ticketclientsearchresults\").slideUp(\"slow\");\n    \$(\"#clientsearchcancel\").fadeOut();\n});\n\$(\"#clientRemove\").click(function() {\n    \$(\"#userid\").val(\"\");\n    \$(\"#clientname\").val(\"\");\n    saveProject();\n});\n";
}
$jscode .= "function loadTaskList() {\n    \$(\"#loadtasklist\").dialog(\"open\");\n}\nfunction saveTaskList() {\n    \$(\"#savetasklist\").dialog(\"open\");\n}\nfunction uploadAttachment() {\n    \$(\".attachmentupload\").fadeToggle();\n}\nfunction deleteTask(id) {\n    if (confirm('" . $vars['_lang']['confirmdeletetask'] . "')) {\n        \$.post(\"" . $modulelink . "&a=deletetask\",\n            {\n                id: id,\n                token: \"" . generate_token('plain') . "\"\n            },\n            function(data) {\n                if(data!=0){\n                    \$(\"#taskholder\"+data).hide();\n                    \$(\".taskholder\"+data).hide();\n                } else {\n                    alert(\"" . addslashes($vars['_lang']['youmustbeanadmintodeletetask']) . "\");\n                }\n            });\n    }\n}\nfunction deleteAttachment(id) {\n    if (confirm(\"" . $vars['_lang']['confirmdeleteattachment'] . "\")) {\n        window.location='" . $modulelink . "&a=deleteattachment&i='+id+'&token=" . generate_token('plain') . "';\n    }\n}\nfunction deleteTimer(id,taskid) {\n    if (confirm(\"" . $vars['_lang']['confirmdeletetimer'] . "\")) {\n        window.location='" . $modulelink . "&a=deletetimer&projectid=" . $projectid . "&id='+id+'&taskid='+taskid+'&token=" . generate_token('plain') . "';\n    }\n}\nfunction addtask() {\n    if (\$(\"#newtask\").val()) {\n        \$(\"#tasksnone\").fadeOut();\n        \$(\"#taskloading\").fadeIn();\n        \$.post(\"" . $modulelink . "\",\n        {\n            a: \"addtask\",\n            newtask: \$(\"#newtask\").val(),\n            token: \"" . generate_token('plain') . "\"\n        },\n        function(data){\n            \$(\"#taskloading\").fadeOut(\"fast\", function() {\n                \$(\"#tasks tr#taskloading\").before(data);\n                \$(\"#newtask\").val(\"\");\n                \$.post(\"" . $modulelink . "\",\n                    {\n                        a: \"updatetask\",\n                        token: \"" . generate_token('plain') . "\"\n                    },\n                    function(data){\n                        \$(\"#taskssummary\").html(data);\n                    }\n                );\n            });\n        });\n    }\n}\nfunction updatetaskstatus(taskid) {\n    \$.post(\"" . $modulelink . "\",\n        {\n            a: \"updatetask\",\n            taskid: taskid,\n            status: \$(\"#tk\"+taskid).attr(\"checked\"),\n            token: \"" . generate_token('plain') . "\"\n        },\n        function(data){\n            \$(\"#taskssummary\").html(data);\n        });\n}\nfunction addticket() {\n    \$(\"#assocticketsloading\").show();\n    \$.post(\"" . $modulelink . "\",\n        {\n            a: \"addticket\",\n            ticketnum: \$(\"#newticketid\").val(),\n            token: \"" . generate_token('plain') . "\"\n        },\n        function(data){\n            if (data.substring(0,20)=='<tr id=\"ticketholder') {\n                \$(\"#assocticketsnone\").fadeOut();\n                \$(\"#assoctickets tr:last\").after(data);\n                \$(\"#newticketid\").val(\"\");\n            } else alert(data);\n            \$(\"#assocticketsloading\").fadeOut();\n        }\n    );\n}\nfunction addinvoice() {\n    \$(\"#associnvoicesloading\").show();\n    \$.post(\"" . $modulelink . "\",\n        {\n            a: \"addinvoice\",\n            invoicenum: \$(\"#newinvoiceid\").val(),\n            token: \"" . generate_token('plain') . "\"\n        },\n        function(data){\n            if (data.substring(0,21)=='<tr id=\"invoiceholder') {\n                \$(\"#associnvoicesnone\").fadeOut();\n                \$(\"#associnvoices tr:last\").after(data);\n                \$(\"#newinvoiceid\").val(\"\");\n                \$(\"#noassociatedinvoicesfound\").hide();\n            } else alert(data);\n            \$(\"#associnvoicesloading\").fadeOut();\n        }\n    );\n}\nfunction saveProject() {\n    \$(\"#saveprojectbtn\").hide();\n    \$(\"#cancelsaveprojectbtn\").hide();\n    \$(\"#editprojectbtn\").show();\n    \$(\"#saveprocess\").fadeIn();\n    \$.post(\"" . $modulelink . "&a=projectsave\",\n        {\n            title : \$(\"#title input\").val(),\n            created : \$(\"#created input\").val(),\n            adminid: \$(\"#adminid select\").val(),\n            userid: \$(\"#userid\").val(),\n            duedate: \$(\"#duedate input\").val(),\n            token: \"" . generate_token('plain') . "\"\n        },\n        function (data) {\n            \$(\"#title .displayval\").html(\$(\"<div/>\").text(\$(\"#title input\").val()).html());\n            \$(\"#created .displayval\").html(\$(\"#created input\").val());\n            \$(\"#adminid .displayval\").html(\$(\"#adminid select option:selected\").text());\n            \$(\"#client .displayval\").html(\$(\"#clientname\").val());\n            \$(\"#duedate .displayval\").html(\$(\"#duedate input\").val());\n            \$(\"#daysleft\").html(data);\n            \$(\".editfield\").fadeOut(\"fast\", function() {\n                \$(\".displayval\").fadeIn();\n            });\n            \$(\"#quickcreatebtn\").removeAttr(\"disabled\");\n            \$(\"#dynamictasksinvoicegen\").removeAttr(\"disabled\");\n            \$(\"#saveprocess\").hide();\n            \$(\"#savesuccess\").show();\n            \$(\"#savesuccess\").fadeOut(5000);\n        }\n    );\n}";
echo $headeroutput;
if( project_management_checkperm("Edit Project Details") )
{
    echo "\n<div class=\"editbtn\"><a id=\"editprojectbtn\">" . $vars['_lang']['edit'] . "</a><a id=\"saveprojectbtn\" style=\"display:none\">" . $vars['_lang']['save'] . "</a>&nbsp;<a id=\"cancelsaveprojectbtn\" style=\"display:none\">" . $vars['_lang']['cancel'] . "</a></div>\n<div id=\"saveprocess\" class=\"loading\"><img src=\"images/loading.gif\" /> " . $vars['_lang']['saving'] . "</div>\n<div id=\"savesuccess\" class=\"loading\">" . $vars['_lang']['changessaved'] . "</div>";
}
echo "<script src=\"../includes/jscript/jqueryro.js\"></script>\n\n<div id=\"title\" class=\"title\"><div class=\"displayval\">" . $headtitle . "</div><div class=\"editfield\"><input id=\"projecttitleeditfield\" type=\"text\" value=\"" . $headtitle . "\" /></div></div>\n<div id=\"daysleft\" class=\"daysleft\">" . $daysleft . "</div><br />\n\n<div class=\"infobar\">\n\n<table width=\"100%\">\n<tr>\n<th>" . $vars['_lang']['created'] . "</th>\n<th>" . $vars['_lang']['assignedto'] . "</th>\n<th>" . $vars['_lang']['associatedclient'] . "</th>\n<th>" . $vars['_lang']['duedate'] . "</th>\n<th>" . $vars['_lang']['totaltime'] . "</th>\n<th style=\"border:0;\">" . $vars['_lang']['status'] . "</th>\n</tr>\n<tr>\n<td id=\"created\"><div class=\"displayval\">" . $created . "</div><div class=\"editfield\"><input type=\"text\" class=\"datepick\" value=\"" . $created . "\" /></div></td>\n<td id=\"adminid\"><div class=\"displayval\">" . $admin . "</div><div class=\"editfield\"><select><option value=\"0\">" . $vars['_lang']['none'] . "</option>";
$totalprojecttime = project_management_sec2hms(get_query_val('mod_projecttimes', "SUM(end-start)", array( 'projectid' => $projectid, 'end' => array( 'sqltype' => 'NEQ', 'value' => '' ) )));
$result = select_query('tbladmins', 'id,firstname,lastname', array( 'disabled' => '0' ), "firstname` ASC,`lastname", 'ASC');
while( $data = mysql_fetch_array($result) )
{
    $aid = $data['id'];
    $adminfirstname = $data['firstname'];
    $adminlastname = $data['lastname'];
    echo "<option value=\"" . $aid . "\"";
    if( $aid == $adminid )
    {
        echo " selected";
    }
    echo ">" . $adminfirstname . " " . $adminlastname . "</option>";
}
echo "</select></div></td>";
$slashedClientName = addslashes($clientname);
echo "<td id=\"client\">\n    <div class=\"displayval\">" . $client . "</div>\n    <div class=\"editfield\">\n        <input type=\"hidden\" id=\"userid\" value=\"" . $userid . "\" />\n        <input type=\"text\" id=\"clientname\" value=\"" . $clientname . "\"\n            onfocus=\"if(this.value==\\'" . $slashedClientName . "\\')this.value=\\'\\'\" />\n        <img src=\"images/icons/adminroles.png\" alt=\"" . $vars['_lang']['removeClient'] . "\"\n            class=\"absmiddle\" id=\"clientRemove\" height=\"16\" width=\"16\" />\n        <img src=\"images/icons/delete.png\" alt=\"Cancel\" class=\"absmiddle\"\n            id=\"clientsearchcancel\" height=\"16\" width=\"16\">\n        <div id=\"ticketclientsearchresults\" style=\"z-index:2000;\"></div>\n    </div>\n</td>\n<td id=\"duedate\">\n    <div class=\"displayval\">" . $duedate . "</div>\n    <div class=\"editfield\">\n        <input type=\"text\" class=\"datepick\" value=\"" . $duedate . "\" />\n    </div>\n</td>\n<td>\n    <div>" . $totalprojecttime . "</div>\n</td>\n<td style=\"border:0;\">\n    <div>";
if( project_management_checkperm("Update Status") )
{
    echo "<select name=\"status\" id=\"statuschange\">";
    $statuses = explode(',', $vars['statusvalues']);
    foreach( $statuses as $status )
    {
        $status = explode("|", $status, 2);
        if( $status[1] )
        {
            echo "<option style=\"background-color:" . $status[1] . "\" value=\"" . $status[0] . "\"";
        }
        else
        {
            echo "<option value=\"" . $status[0] . "\"";
        }
        if( $status[0] == $projectstatus )
        {
            echo " selected";
        }
        echo ">" . $status[0] . "</option>";
    }
    echo "</select>";
}
else
{
    echo $projectstatus;
}
echo "</div></td>\n</tr>\n</table>\n</div>\n\n<table width=\"100%\" align=\"center\"><tr><td width=\"50%\" valign=\"top\">";
global $currency;
$currency = getCurrency($userid);
$gateways = getGatewaysArray();
$taskshtml = '';
$taski = $totaltimecount = 0;
$result = select_query('mod_projecttasks', '', array( 'projectid' => $projectid ), 'order', 'ASC');
while( $data = mysql_fetch_array($result) )
{
    $taskid = $data['id'];
    $task = $data['task'];
    $taskadminid = $data['adminid'];
    $taskduedate = $data['duedate'];
    $tasknotes = $data['notes'];
    $taskcompleted = $data['completed'];
    $taskadmin = $taskadminid ? "<span class=\"taskbox\">" . getAdminName($data['adminid']) . "</span> " : '';
    $taskduedate = $taskduedate != '0000-00-00' ? " <span class=\"taskdue\">" . project_management_daysleft($data['duedate'], $vars) . " (" . fromMySQLDate($data['duedate']) . ")</span>" : '';
    $taskcompleted = $taskcompleted ? " checked=\"checked\"" : '';
    $taskedit = project_management_checkperm("Edit Tasks") ? " <a href=\"" . str_replace("&m=view", "&m=edittask", $modulelink) . "&id=" . $taskid . "\"><img src=\"images/edit.gif\" align=\"absmiddle\" title=\"Edit Task\" /></a>" : '';
    $taskdelete = project_management_checkperm("Delete Tasks") ? " <a href=\"#\" onclick=\"deleteTask(" . $taskid . ");return false\"><img src=\"images/delete.gif\" align=\"absmiddle\" /></a>" : '';
    $notesoutput = "<div align=\"center\" style=\"margin-top:5px;\"><table width=\"95%\" align=\"center\"><tr><td><textarea rows=\"3\" style=\"width:100%\" id=\"tasknotestxtarea" . $taskid . "\">" . $tasknotes . "</textarea></td><td width=\"120\" align=\"right\"><input type=\"button\" id=\"savetasknotestxtarea" . $taskid . "\" class=\"savetasknotestxtarea\" value=\"" . $vars['_lang']['savenotes'] . "\" /></td></tr></table></div>";
    $tasknotes = "<a class=\"tasknotestoggler\" id=\"tasknotestogglerclicker" . $taskid . "\"><img src=\"../modules/addons/project_management/images/" . ($tasknotes ? '' : 'no') . "notes.png\" align=\"absmiddle\" title=\"View/Edit Notes\" /></a>";
    $taski++;
    $invoicelinedesc = $taski . ". " . $task . "\n";
    $timesoutput = project_management_timesoutput($vars, $taskid);
    $timerid = $GLOBALS['timerid'];
    $timecount = $GLOBALS['timecount'];
    $invoicelinedesc = $GLOBALS['invoicelinedesc'];
    $csstimerdisplay = !get_query_val('mod_projecttimes', 'id', array( 'end' => '', 'projectid' => $projectid, 'taskid' => $taskid, 'adminid' => $_SESSION['adminid'] )) ? "style=\"display:none\"" : '';
    $taskshtml .= "<tr id=\"taskholder" . $taskid . "\">\n    <td class=\"sortcol\"></td>\n    <td>\n        <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\n            <tr><td width=\"35%\" align=\"left\"><input type=\"checkbox\" name=\"task[" . $taskid . "]\" id=\"tk" . $taskid . "\" value=\"1\"" . $taskcompleted . " onclick=\"updatetaskstatus('" . $taskid . "')\" /> " . $taskadmin . "<label for=\"tk" . $taskid . "\">" . $task . "</label> " . $taskduedate . " <span class=\"taskbox\">" . project_management_sec2hms($timecount) . " Hrs</span> " . $tasknotes . " <div style=\"float:right;\"><a class=\"ajaxstarttimer tasktimerexpander\" id=\"ajaxstarttimer" . $taskid . "\"><img src=\"../modules/addons/project_management/images/starttimer.png\" align=\"absmiddle\" title=\"Start Timer\" /></a> <a id=\"tasktimertoggleclicker" . $taskid . "\" class=\"tasktimertoggle\"><img src=\"../modules/addons/project_management/images/" . ($timerid ? '' : 'no') . "times.png\" align=\"absmiddle\" title=\"View Times\" /></a> " . $taskedit . $taskdelete . "</div></td></tr>\n            <tr " . $csstimerdisplay . " id=\"tasktimerexpandholder" . $taskid . "\"><td>" . $timesoutput . "</td></tr>\n            <tr style=\"display:none\" id=\"tasknotesexpandholder" . $taskid . "\"><td>" . $notesoutput . "</td></tr>\n        </table>\n    </td>\n</tr>";
    if( $createinvoice )
    {
        $invoicelineamt = $timecount / 3600 * $vars['hourlyrate'];
        insert_query('tblinvoiceitems', array( 'invoiceid' => $invoiceid, 'userid' => $userid, 'type' => 'Project', 'relid' => $projectid, 'description' => $invoicelinedesc, 'amount' => $invoicelineamt, 'taxed' => '1' ));
    }
}
if( !$taski )
{
    $taskshtml .= "<tr id=\"tasksnone\"><td class=\"fieldarea\" align=\"center\">" . $vars['_lang']['notasks'] . "</td></tr>";
}
$totalhours = project_management_sec2hms($totaltimecount);
$taskstatusdata = project_management_tasksstatus($projectid, $vars);
echo "\n\n<div class=\"heading\"><img src=\"images/icons/todolist.png\" /> " . $vars['_lang']['projecttasks'] . " <span class=\"stat\" id=\"taskssummary\">" . $taskstatusdata['html'] . "</span></div>\n\n<div class=\"box\">\n<table width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\" class=\"tasks\" id=\"tasks\">\n" . $taskshtml . "\n<tr class=\"loading\" id=\"taskloading\"><td colspan=\"2\" align=\"center\"><img src=\"images/loading.gif\"> " . $vars['_lang']['updating'] . "</td></tr>\n</table>\n</div>\n\n<div align=\"right\" style=\"padding:3px 20px;\"><input type=\"button\" value=\"" . $vars['_lang']['savetasklisttpl'] . "\" onclick=\"saveTaskList()\" /> <input type=\"button\" value=\"" . $vars['_lang']['loadtasklisttpl'] . "\" onclick=\"loadTaskList()\" /></div>\n\n";
if( project_management_checkperm("Create Tasks") )
{
    echo "<form onsubmit=\"addtask();return false\">\n    <div class=\"addtask\">\n        <b>" . $vars['_lang']['newtask'] . "</b>\n        <input type=\"text\" id=\"newtask\" style=\"width:65%;\" />\n        <input type=\"submit\" value=\"" . $vars['_lang']['add'] . "\" />\n    </div>\n</form>";
}
echo "\n\n<div class=\"heading\"><img src=\"images/icons/massmail.png\" /> " . $vars['_lang']['associatedtickets'] . " ";
if( project_management_checkperm("Associate Tickets") )
{
    echo "<span class=\"stat\">" . $vars['_lang']['add'] . " " . $vars['_lang']['ticketnumberhash'] . " <input type=\"text\" id=\"newticketid\" size=\"10\" /> <a href=\"#\" onclick=\"addticket();return false\">" . $vars['_lang']['add'] . " &raquo;</a></span><span id=\"assocticketsloading\" class=\"loading\"><img src=\"images/loading.gif\" /> " . $vars['_lang']['validating'] . "</span>";
}
echo "</div><div class=\"tablebg\">\n<table class=\"datatable\" width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"3\" id=\"assoctickets\">\n<tr><th>" . $vars['_lang']['date'] . "</th><th class=\"fieldarea\">" . $vars['_lang']['subject'] . "</th><th>" . $vars['_lang']['status'] . "</th><th>" . $vars['_lang']['lastupdated'] . "</th><th></th></tr>\n";
$ticketinvoicelinks = array(  );
$ticketid = $sometickets = '';
foreach( $ticketids as $i => $ticketnum )
{
    if( $ticketnum )
    {
        $result = select_query('tbltickets', 'id,tid,date,title,status,lastreply', array( 'tid' => $ticketnum ));
        $data = mysql_fetch_array($result);
        $ticketid = $data['id'];
        if( $ticketid )
        {
            $ticketdate = $data['date'];
            $ticketnum = $data['tid'];
            $tickettitle = $data['title'];
            $ticketstatus = $data['status'];
            $ticketlastreply = $data['lastreply'];
            $ticketinvoicelinks[] = "description LIKE '%Ticket #" . $ticketnum . "%'";
            if( $ticketid )
            {
                echo "<tr id=\"ticketholder" . $i . "\"><td>" . fromMySQLDate($ticketdate, true) . "</td><td class=\"left\"><a href=\"supporttickets.php?action=viewticket&id=" . $ticketid . "\" target=\"_blank\"><strong>#" . $ticketnum . " - " . $tickettitle . "</strong></a></td><td>" . getStatusColour($ticketstatus) . "</td><td>" . fromMySQLDate($ticketlastreply, true) . "</td><td>" . (project_management_checkperm("Associate Tickets") ? "<a class=\"deleteticket\" id=\"deleteticket" . $i . "\"><img src=\"images/delete.gif\"></a>" : '') . "</td></tr>";
            }
            $sometickets = true;
        }
    }
}
if( !$sometickets )
{
    echo "<tr id=\"assocticketsnone\"><td colspan=\"5\" align=\"center\">" . $vars['_lang']['noassociatedticketsfound'] . "</td></tr>";
}
echo "</table>\n</div>\n\n<br />\n\n<div class=\"heading\"><img src=\"images/icons/invoices.png\" />  " . $vars['_lang']['associatedinvoices'] . " ";
echo "<span class=\"stat\">" . $vars['_lang']['add'] . " " . $vars['_lang']['invoicenumberhash'] . " <input type=\"text\" id=\"newinvoiceid\" size=\"10\" /> <a href=\"#\" onclick=\"addinvoice();return false\">" . $vars['_lang']['add'] . " &raquo;</a></span><span id=\"associnvoicesloading\" class=\"loading\"><img src=\"images/loading.gif\" /> " . $vars['_lang']['validating'] . "</span>";
echo "</div><div class=\"tablebg\">\n<table class=\"datatable\" width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"3\" id=\"associnvoices\">\n<tr><th>" . $vars['_lang']['invoicenumberhash'] . "</th><th>" . $vars['_lang']['created'] . "</th><th>" . $vars['_lang']['datepaid'] . "</th><th>" . $vars['_lang']['total'] . "</th><th>" . $vars['_lang']['paymentmethod'] . "</th><th>" . $vars['_lang']['status'] . "</th></tr>\n";
$invoiceids = get_query_val('mod_project', 'invoiceids', array( 'id' => $projectid ));
$invoicesoutputarr = explode(',', $invoiceids);
foreach( $invoicesoutputarr as $invoiceid )
{
    if( $invoiceid )
    {
        $data = get_query_vals('tblinvoices', 'id,date,datepaid,total,paymentmethod,status', array( 'id' => $invoiceid ));
        echo "<tr><td><a href=\"invoices.php?action=edit&id=" . $data['id'] . "\">" . $data['id'] . "</a></td><td>" . fromMySQLdate($data['date']) . "</td><td>" . fromMySQLdate($data['datepaid']) . "</td><td>" . formatCurrency($data['total']) . "</td><td>" . $gateways[$data['paymentmethod']] . "</td><td>" . getInvoiceStatusColour($data['status']) . "</td></tr>";
    }
}
$invoiceid = '';
$ticketinvoicesquery = !empty($ticketinvoicelinks) ? "(" . implode(" OR ", $ticketinvoicelinks) . ") OR " : '';
$result = select_query('tblinvoices', '', "id IN (SELECT invoiceid FROM tblinvoiceitems WHERE description LIKE '%Project #" . $projectid . "%' OR " . $ticketinvoicesquery . " (type='Project' AND relid='" . $projectid . "'))", 'id', 'ASC');
while( $data = mysql_fetch_array($result) )
{
    $invoiceid = $data['id'];
    if( !in_array($invoiceid, $invoicesoutputarr) )
    {
        echo "<tr><td><a href=\"invoices.php?action=edit&id=" . $data['id'] . "\">" . $data['id'] . "</a></td><td>" . fromMySQLdate($data['date']) . "</td><td>" . fromMySQLdate($data['datepaid']) . "</td><td>" . formatCurrency($data['total']) . "</td><td>" . $gateways[$data['paymentmethod']] . "</td><td>" . getInvoiceStatusColour($data['status']) . "</td></tr>";
    }
}
if( !$invoiceid && !$invoicesoutputarr )
{
    echo "<tr id=\"noassociatedinvoicesfound\"><td colspan=\"6\" align=\"center\">" . $vars['_lang']['noassociatedinvoicesfound'] . "</td></tr>";
}
echo "</table>\n</div>";
if( project_management_checkperm("Bill Tasks") )
{
    echo "<form method=\"post\" action=\"" . $modulelink . "&a=addquickinvoice\"><p align=\"center\"><b>" . $vars['_lang']['quickinvoice'] . "</b> <input type=\"text\" name=\"newinvoice\"";
    if( !$userid )
    {
        echo " disabled value=\"" . $vars['_lang']['associateclienttousefeature'] . "\"";
    }
    echo " style=\"width:50%;\" />&nbsp;@&nbsp;<input type=\"text\" name=\"newinvoiceamt\" size=\"10\" ";
    if( !$userid )
    {
        echo " disabled ";
    }
    echo " /> <input type=\"submit\" id=\"quickcreatebtn\" value=\"" . $vars['_lang']['create'] . "\" ";
    if( !$userid )
    {
        echo " disabled ";
    }
    echo "/><br /><br />";
    echo "<input type=\"button\" id=\"dynamictasksinvoicegen\" value=\"" . $vars['_lang']['billfortasktimeentries'] . "\" ";
    if( !$userid )
    {
        echo " disabled ";
    }
    echo "/></p></form>";
}
echo "<div id=\"dynamictasksinvoiceholder\"></div><div align=\"center\" class=\"loading\" id=\"dynamictasksinvoiceloading\"><img src=\"images/loading.gif\"> " . $vars['_lang']['preparing'] . "</div>\n\n</div>\n\n";
echo "</td><td width=\"50%\" valign=\"top\">";
echo "<div class=\"messages\">\n\n<div class=\"title\"><img src=\"images/icons/attachment.png\" /> " . $vars['_lang']['attachments'] . " <span class=\"stat\"><a href=\"#\" onclick=\"uploadAttachment();return false\"><img src=\"images/icons/add.png\" align=\"absmiddle\" border=\"0\" /> " . $vars['_lang']['upload'] . " </a></span></div>";
$attachment = '';
$attachmentslist = get_query_val('mod_project', 'attachments', array( 'id' => $projectid ));
$attachments = explode(',', $attachmentslist);
echo "<div class=\"box\" id=\"attachmentsholderbox\">\n    <div class=\"padding\">";
foreach( $attachments as $i => $attachment )
{
    if( $attachment )
    {
        $attachment = substr($attachment, 7);
        echo "<img src=\"images/icons/ticketspredefined.png\" align=\"top\" /> <a href=\"../modules/addons/project_management/project_management.php?action=dl&projectid=" . $projectid . "&i=" . $i . "\">" . $attachment . "</a> " . (project_management_check_masteradmin() ? "<a href=\"#\" onclick=\"deleteAttachment('" . $i . "');return false\"><img src=\"images/delete.gif\" align=\"absmiddle\" border=\"0\" /></a>" : '') . " &nbsp;&nbsp;&nbsp; ";
    }
}
if( !$attachment )
{
    echo $vars['_lang']['noattachments'];
}
echo "\n    </div>\n</div>\n<div class=\"attachmentupload\" id=\"attachmentsholder\" style=\"display:none\">\n<form method=\"post\" action=\"" . $modulelink . "&a=addattachment\" enctype=\"multipart/form-data\">\n<input type=\"file\" name=\"attachments[]\" size=\"30\" /> <a href=\"#\" id=\"addattachment\"><img src=\"images/icons/add.png\" align=\"absmiddle\" border=\"0\" /> " . $vars['_lang']['addanother'] . "</a> <input type=\"submit\" value=\"" . $vars['_lang']['upload'] . "\" />\n<div id=\"attachments\"></div>\n</form>\n</div>\n<br />\n\n<div class=\"title\"><img src=\"images/icons/tickets.png\" /> " . $vars['_lang']['staffmessageboard'] . "</div>";
echo "<form method=\"post\" action=\"" . $modulelink . "&a=addmsg\" enctype=\"multipart/form-data\">\n<div class=\"msgreply\">\n<textarea name=\"msg\"></textarea><br />\n<img src=\"images/icons/attachment.png\" /> <strong>" . $vars['_lang']['attachments'] . ":</strong> <input type=\"file\" name=\"attachments[]\" size=\"30\" /> <a href=\"#\" id=\"addmsgattachment\"><img src=\"images/icons/add.png\" align=\"absmiddle\" border=\"0\" /> " . $vars['_lang']['addanother'] . "</a> <input type=\"submit\" value=\"Post\"" . (!$projectid ? " disabled" : '') . " />\n<div id=\"msgattachments\"></div>\n</div>\n</form>";
$msgid = '';
$result = select_query('mod_projectmessages', "*,(SELECT CONCAT(firstname,' ',lastname) FROM tbladmins WHERE tbladmins.id=mod_projectmessages.adminid) AS adminuser", array( 'projectid' => $projectid ), 'date', 'DESC');
$i = 1;
while( $data = mysql_fetch_array($result) )
{
    $msgid = $data['id'];
    $date = $data['date'];
    $message = strip_tags($data['message']);
    $attachments = $data['attachments'];
    $adminuser = $data['adminuser'];
    $dates = explode(" ", $date);
    $dates2 = explode('-', $dates[0]);
    $dates = $dates[1];
    $dates = explode(":", $dates);
    $date = date("jS F Y @ H:ia", mktime($dates[0], $dates[1], $dates[2], $dates2[1], $dates2[2], $dates2[0]));
    $attachments = explode(',', $attachments);
    $attachment = '';
    foreach( $attachments as $num => $attach )
    {
        if( $attach )
        {
            $attachment .= "<img src=\"../images/article.gif\" align=\"absmiddle\" /> <a href=\"../modules/addons/project_management/project_management.php?action=dl&projectid=" . $projectid . "&msg=" . $msgid . "&i=" . $num . "\">" . substr($attach, 7) . "</a>";
        }
    }
    if( $attachment )
    {
        $attachment = "<br /><br /><strong>" . $vars['_lang']['attachments'] . "</strong><br />" . $attachment;
    }
    echo "<div class=\"msg" . $i . "\" id=\"msg" . $msgid . "\"><div class=\"date\">" . $vars['_lang']['postedby'] . " <strong>" . $adminuser . "</strong> " . $vars['_lang']['on'] . " " . $date . "</div><div class=\"msg\"><div class=\"msgholder\" id=\"msgholder" . $msgid . "\">" . nl2br(ticketAutoHyperlinks($message)) . "</div>" . $attachment;
    echo "<div style=\"display:none\" class=\"msgeditorholder" . $i . "\" id=\"msgeditorholder" . $msgid . "\"><textarea class=\"msgeditor\" id=\"msgeditor" . $msgid . "\">" . $message . "</textarea><input type=\"button\" class=\"msgeditorsavechanges\" id=\"msgeditorsavechanges" . $msgid . "\" value=\"" . $vars['_lang']['savechanges'] . "\" /></div>";
    echo "<div class=\"actions\" align=\"right\"><a class=\"editstaffmsg\" id=\"editstaffmsg" . $msgid . "\"><img src=\"images/edit.gif\"></a>";
    if( project_management_checkperm("Delete Messages") )
    {
        echo "&nbsp;<a class=\"deletestaffmsg\" id=\"deletestaffmsg" . $msgid . "\"><img src=\"images/delete.gif\"></a>";
    }
    echo "</div></div></div><div class=\"clear\"></div>";
    if( $i == 1 )
    {
        $i = 2;
    }
    else
    {
        $i = 1;
    }
}
if( !$msgid )
{
    echo "<div class=\"msgnone\">" . $vars['_lang']['nomessagespostedyet'] . "</div>";
}
echo "</div>\n\n</td></tr></table>\n\n<h2>" . $vars['_lang']['activitylog'] . "</h2>\n\n";
$aInt->sortableTableInit('nopagination');
$tabledata = '';
$result = select_query('mod_projectlog', "mod_projectlog.*,(SELECT CONCAT(firstname,' ',lastname) FROM tbladmins WHERE tbladmins.id=mod_projectlog.adminid) AS admin", array( 'projectid' => $projectid ), 'id', 'DESC', '0,15');
while( $data = mysql_fetch_array($result) )
{
    $date = $data['date'];
    $msg = $data['msg'];
    $admin = $data['admin'];
    $date = fromMySQLDate($date, true);
    $tabledata[] = array( $date, "<div align=\"left\">" . $msg . "</div>", $admin );
}
echo $aInt->sortableTable(array( $vars['_lang']['date'], $vars['_lang']['logentry'], $vars['_lang']['adminuser'] ), $tabledata);
echo "\n<div align=\"right\" style=\"padding:0 10px;\"><a href=\"addonmodules.php?module=project_management&m=activity&projectid=" . $projectid . "\">" . $vars['_lang']['viewall'] . " &raquo;</a></div>\n\n";
$loadtpllisthtml = "<form method=\"post\" action=\"" . $vars['modulelink'] . "&m=view&projectid=" . $projectid . "&a=loadtasklist\" id=\"loadtasklistfrm\"><div align=\"center\"><select name=\"tasktplid\" style=\"width:250px;\">";
$tplid = '';
$result = select_query('mod_projecttasktpls', '', '', 'name', 'ASC');
while( $data = mysql_fetch_array($result) )
{
    $tplid = $data['id'];
    $loadtpllisthtml .= "<option value=\"" . $tplid . "\">" . $data['name'] . "</option>";
}
if( !$tplid )
{
    $loadtpllisthtml .= "<option value=\"\">" . $vars['_lang']['tasklisttplsnone'] . "</option>";
}
$loadtpllisthtml .= "</select></div></form>";
$savetxt = $aInt->lang('global', 'save');
if( !$savetxt )
{
    $savetxt = 'Save';
}
$oktxt = $aInt->lang('global', 'ok');
if( !$oktxt )
{
    $oktxt = 'OK';
}
echo $aInt->jqueryDialog('savetasklist', $vars['_lang']['savetasklisttpl'], "<div align=\"center\">" . $vars['_lang']['tasklisttplname'] . ": <input type=\"text\" name=\"taskname\" id=\"taskname\" style=\"width:200px;\" /></div>", array( $savetxt => "\$(this).dialog('close');\$.post('" . $vars['modulelink'] . "&m=view&projectid=" . $projectid . "', { a: 'savetasklist', taskname: \$('#taskname').val(), token: '" . generate_token('plain') . "' });", $aInt->lang('global', 'cancel') => '' ), '', '', '');
echo $aInt->jqueryDialog('loadtasklist', $vars['_lang']['loadtasklisttpl'], $loadtpllisthtml, array( $oktxt => "\$('#loadtasklistfrm').submit();", $aInt->lang('global', 'cancel') => '' ), '', '', '');
function project_management_timesoutput($vars, $taskid)
{
    $timesoutput = "<table width=\"95%\" bgcolor=\"#cccccc\" cellspacing=\"1\" align=\"center\" style=\"margin-top:5px;\"><tr class=\"taskholder" . $taskid . "\" bgcolor=\"#efefef\" style=\"text-align:center;font-weight:bold;\"><td align=\"center\">" . $vars['_lang']['staff'] . "</td><td>" . $vars['_lang']['starttime'] . "</td><td>" . $vars['_lang']['stoptime'] . "</td><td>" . $vars['_lang']['timespent'] . "</td><td width=\"25\"></td></tr>";
    $result2 = select_query('mod_projecttimes', "*", array( 'taskid' => $taskid ));
    while( $timerdata = mysql_fetch_assoc($result2) )
    {
        $show_startresume = 'false';
        $timerid = $timerdata['id'];
        $timeradmin = mysql_fetch_assoc(select_query('tbladmins', 'firstname,lastname', array( 'id' => $timerdata['adminid'] )));
        $timerstart = $timerdata['start'];
        $timerend = $timerdata['end'];
        $starttime = fromMySQLDate(date("Y-m-d H:i:s", $timerstart), 1) . ":" . date('s', $timerstart);
        $endtimerlink = $timerdata['adminid'] == $_SESSION['adminid'] || project_management_check_masteradmin() ? "<a rel=\"" . $timerid . "\" id=\"ajaxendtimertaskid" . $taskid . "\" class=\"ajaxendtimer timerlink\">" . $vars['_lang']['endtimer'] . "</a>" : $vars['_lang']['inprogress'];
        $deltimerlink = $timerdata['adminid'] == $_SESSION['adminid'] || project_management_check_masteradmin() ? "<a href=\"#\" onclick=\"deleteTimer('" . $timerid . "','" . $taskid . "');return false\"><img src=\"images/delete.gif\"></a>" : '';
        $endtime = $timerend ? fromMySQLDate(date("Y-m-d H:i:s", $timerend), 1) . ":" . date('s', $timerend) : $endtimerlink;
        $totaltime = $timerend ? project_management_sec2hms($timerend - $timerstart) : $vars['_lang']['inprogress'];
        $timesoutput .= "<tr bgcolor=\"#ffffff\" class=\"time taskholder" . $taskid . "\"><td>" . $timeradmin['firstname'] . " " . $timeradmin['lastname'] . "</td><td>" . $starttime . "</td><td id=\"ajaxendtimertaskholderid" . $timerid . "\">" . $endtime . "</td><td id=\"ajaxtimerstatusholderid" . $timerid . "\">" . $totaltime . "</td><td>" . $deltimerlink . "</td></tr>";
        if( $timerend )
        {
            $timecount += $timerend - $timerstart;
            $totaltimecount += $timerend - $timerstart;
            $show_startresume = 'true';
            $invoicelinedesc .= " > " . $starttime . " - " . $endtime . " (" . $totaltime . " " . $vars['_lang']['hours'] . ")\n";
        }
    }
    if( !$timerid )
    {
        $timesoutput .= "<tr id=\"notasktimersexist" . $taskid . "\"><td colspan=\"6\" align=\"center\" bgcolor=\"#fff\">" . $vars['_lang']['notimesrecorded'] . "</td></tr>";
    }
    $timesoutput .= "</table>";
    $GLOBALS['timerid'] = $timerid;
    $GLOBALS['timecount'] = $timecount;
    $GLOBALS['invoicelinedesc'] = $invoicelinedesc;
    return $timesoutput;
}