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
if( !$vars['clientenable'] )
{
    redir();
}
$whmcs = WHMCS_Application::getinstance();
$tplvars = array(  );
$tplvars['_lang'] = $vars['_lang'];
$tplvars['features'] = $features = explode(',', $vars['clientfeatures']);
$a = $_GET['a'];
if( !$a )
{
    $tplfile = 'templates/clienthome';
    $result = select_query('mod_project', "COUNT(*)", array( 'userid' => $_SESSION['uid'] ));
    $data = mysql_fetch_array($result);
    $numitems = $data[0];
    list($orderby, $sort, $limit) = clientAreaTableInit('projects', 'lastmodified', 'DESC', $numitems);
    $projects = array(  );
    $result = select_query('mod_project', '', array( 'userid' => $_SESSION['uid'] ), $orderby, $sort, $limit);
    while( $data = mysql_fetch_array($result) )
    {
        $projects[] = array( 'id' => $data['id'], 'title' => $data['title'], 'adminid' => $data['adminid'], 'adminname' => get_query_val('tbladmins', "CONCAT(firstname,' ',lastname)", array( 'id' => $data['adminid'] )), 'created' => fromMySQLDate($data['created'], 0, 1), 'duedate' => fromMySQLDate($data['duedate'], 0, 1), 'lastmodified' => fromMySQLDate($data['lastmodified'], 0, 1), 'status' => $data['status'] );
    }
    $tplvars['projects'] = $projects;
    $tplvars['orderby'] = $orderby;
    $tplvars['sort'] = strtolower($sort);
    $tplvars = array_merge($tplvars, clientAreaTablePageNav($numitems));
}
else
{
    if( $a == 'view' )
    {
        $tplfile = 'templates/clientview';
        $result = select_query('mod_project', '', array( 'userid' => $_SESSION['uid'], 'id' => $_REQUEST['id'] ));
        $data = mysql_fetch_array($result);
        $projectid = (int) $data['id'];
        if( !$projectid )
        {
            exit( "Access Denied" );
        }
        if( in_array('addtasks', $features) && trim($_POST['newtask']) )
        {
            check_token();
            insert_query('mod_projecttasks', array( 'projectid' => $projectid, 'task' => trim($_POST['newtask']), 'created' => "now()", 'order' => get_query_val('mod_projecttasks', "`order`", array( 'projectid' => $projectid ), 'order', 'DESC') + 1 ));
            redir("m=project_management&a=view&id=" . $projectid);
        }
        if( in_array('files', $features) && $_POST['upload'] )
        {
            $whmcs = WHMCS_Application::getinstance();
            $attachmentsDirectory = $whmcs->getAttachmentsDir();
            $projectsdir2 = $attachmentsDirectory . 'projects/';
            $projectsdir = $attachmentsDirectory . 'projects/' . $projectid . '/';
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
            $attachments = explode(',', $data['attachments']);
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
                        if( !$file->checkExtension() )
                        {
                            WHMCS_Session::set('PMAttachmentError', true);
                            continue;
                        }
                        $prefix = "{RAND}_";
                        $filename = $file->move($projectsdir, $prefix);
                        $attachments[] = $filename;
                        project_management_log($projectid, $vars['_lang']['clientaddedattachment'] . " " . $file->getCleanName());
                    }
                    catch( WHMCS_Exception_File_NotUploaded $e )
                    {
                    }
                }
            }
            update_query('mod_project', array( 'attachments' => implode(',', $attachments) ), array( 'id' => $projectid ));
            redir("m=project_management&a=view&id=" . $projectid);
        }
        global $currency;
        $currency = getCurrency($_SESSION['uid']);
        $tplvars['project'] = array( 'id' => $data['id'], 'title' => $data['title'], 'adminid' => $data['adminid'], 'adminname' => get_query_val('tbladmins', "CONCAT(firstname,' ',lastname)", array( 'id' => $data['adminid'] )), 'created' => fromMySQLDate($data['created'], 0, 1), 'duedate' => fromMySQLDate($data['duedate'], 0, 1), 'duein' => project_management_daysleft($data['duedate'], $vars), 'lastmodified' => fromMySQLDate($data['lastmodified'], 0, 1), 'totaltime' => $totaltime, 'status' => $data['status'] );
        if( !$tplvars['project']['adminname'] )
        {
            $tplvars['project']['adminname'] = 'None';
        }
        $ticketids = $data['ticketids'];
        $invoiceids = $data['invoiceids'];
        $attachments = $data['attachments'];
        $tickets = $invoices = $ticketinvoicelinks = $attachmentsarray = array(  );
        $ticketids = explode(',', $ticketids);
        foreach( $ticketids as $ticketnum )
        {
            if( $ticketnum )
            {
                $result = select_query('tbltickets', 'id,tid,c,title,status,lastreply', array( 'tid' => $ticketnum ));
                $data = mysql_fetch_array($result);
                $ticketid = $data['id'];
                if( $ticketid )
                {
                    $tickets[] = array( 'tid' => $data['tid'], 'c' => $data['c'], 'title' => $data['title'], 'status' => $data['status'], 'lastreply' => $data['lastreply'] );
                    $ticketinvoicelinks[] = "description LIKE '%Ticket #" . $data['tid'] . "%'";
                }
            }
        }
        $tplvars['tickets'] = $tickets;
        $invoiceids = explode(',', $invoiceids);
        foreach( $invoiceids as $k => $invoiceid )
        {
            if( !$invoiceid )
            {
                unset($invoiceids[$k]);
            }
        }
        if( !function_exists('getGatewaysArray') )
        {
            require(ROOTDIR . "/includes/gatewayfunctions.php");
        }
        $gateways = getGatewaysArray();
        $ticketinvoicesquery = !empty($ticketinvoicelinks) ? "(" . implode(" OR ", $ticketinvoicelinks) . ") OR " : '';
        $where = "id IN (SELECT invoiceid FROM tblinvoiceitems" . " WHERE description LIKE '%Project #" . $projectid . "%' OR " . $ticketinvoicesquery . " (type='Project' AND relid='" . $projectid . "'))";
        if( 0 < count($invoiceids) )
        {
            $where .= " OR id IN (" . db_build_in_array($invoiceids) . ")";
        }
        $result = select_query('tblinvoices', '', $where, 'id', 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $invoices[] = array( 'id' => $data['id'], 'date' => fromMySQLDate($data['date'], 0, 1), 'duedate' => fromMySQLDate($data['duedate'], 0, 1), 'datepaid' => fromMySQLDate($data['datepaid'], 0, 1), 'total' => formatCurrency($data['total']), 'paymentmethod' => $gateways[$data['paymentmethod']], 'status' => $data['status'], 'rawstatus' => strtolower($data['status']) );
        }
        $tplvars['invoices'] = $invoices;
        $attachments = explode(',', $attachments);
        foreach( $attachments as $i => $attachment )
        {
            $attachment = substr($attachment, 7);
            if( $attachment )
            {
                $attachmentsarray[$i] = array( 'filename' => $attachment );
            }
        }
        $tplvars['attachments'] = $attachmentsarray;
        $totaltimecount = 0;
        $i = 1;
        $tasks = array(  );
        for( $result = select_query('mod_projecttasks', 'id,task,notes,adminid,created,duedate,completed', array( 'projectid' => $projectid ), 'order', 'ASC'); $data = mysql_fetch_assoc($result); $i++ )
        {
            $tasks[$i] = $data;
            $tasks[$i]['adminname'] = $data['adminid'] ? get_query_val('tbladmins', "CONCAT(firstname,' ',lastname)", array( 'id' => $data['adminid'] )) : '0';
            $tasks[$i]['duein'] = $data['duedate'] != '0000-00-00' ? project_management_daysleft($data['duedate'], $vars) : '0';
            $tasks[$i]['duedate'] = $data['duedate'] != '0000-00-00' ? fromMySQLDate($data['duedate'], 0, 1) : '0';
            $totaltasktime = 0;
            $result2 = select_query('mod_projecttimes', '', array( 'projectid' => $projectid, 'taskid' => $data['id'] ));
            while( $data = mysql_fetch_array($result2) )
            {
                $timerid = $data['id'];
                $timerstart = $data['start'];
                $timerend = $data['end'];
                $starttime = fromMySQLDate(date("Y-m-d H:i:s", $timerstart), 1, 1) . ":" . date('s', $timerstart);
                $endtime = $timerend ? fromMySQLDate(date("Y-m-d H:i:s", $timerend), 1, 1) . ":" . date('s', $timerend) : 0;
                $totaltime = $timerend ? project_management_sec2hms($timerend - $timerstart) : 0;
                $tasks[$i]['times'][] = array( 'id' => $data['id'], 'adminid' => $data['adminid'], 'adminname' => get_query_val('tbladmins', "CONCAT(firstname,' ',lastname)", array( 'id' => $data['adminid'] )), 'start' => $starttime, 'end' => $endtime, 'duration' => $totaltime );
                if( $timerend )
                {
                    $totaltasktime += $timerend - $timerstart;
                }
            }
            $totaltimecount += $totaltasktime;
            $tasks[$i]['totaltime'] = project_management_sec2hms($totaltasktime);
        }
        $tplvars['tasks'] = $tasks;
        $totaltime = project_management_sec2hms($totaltimecount);
        $tplvars['project']['totaltime'] = $totaltime;
        if( in_array('files', $features) )
        {
            $attachmentError = WHMCS_Session::getanddelete('PMAttachmentError');
            if( $attachmentError )
            {
                $tplvars['attachmentError'] = $attachmentError;
            }
            $tplvars['allowedExtensions'] = $whmcs->get_config('TicketAllowedFileTypes');
            return 1;
        }
    }
    else
    {
        redir("m=project_management");
    }
}