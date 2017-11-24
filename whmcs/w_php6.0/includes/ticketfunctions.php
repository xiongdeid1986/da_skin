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
function getTimeBetweenDates($lastreply, $from = 'now')
{
    $datetime = strtotime($from);
    $date2 = strtotime($lastreply);
    $holdtotsec = $datetime - $date2;
    $holdtotmin = ($datetime - $date2) / 60;
    $holdtothr = ($datetime - $date2) / 3600;
    $holdtotday = intval(($datetime - $date2) / 86400);
    $holdhr = intval($holdtothr - $holdtotday * 24);
    $holdmr = intval($holdtotmin - ($holdhr * 60 + $holdtotday * 1440));
    $holdsr = intval($holdtotsec - ($holdhr * 3600 + $holdmr * 60 + 86400 * $holdtotday));
    return array( 'days' => $holdtotday, 'hours' => $holdhr, 'minutes' => $holdmr, 'seconds' => $holdsr );
}
function getShortLastReplyTime($lastreply)
{
    $timeparts = gettimebetweendates($lastreply);
    $str = '';
    if( 0 < $timeparts['days'] )
    {
        $str .= $timeparts['days'] . "d ";
    }
    $str .= $timeparts['hours'] . "h ";
    $str .= $timeparts['minutes'] . 'm';
    return $str;
}
function getLastReplyTime($lastreply)
{
    $timeparts = gettimebetweendates($lastreply);
    $str = '';
    if( 0 < $timeparts['days'] )
    {
        $str .= $timeparts['days'] . " Days ";
    }
    $str .= $timeparts['hours'] . " Hours ";
    $str .= $timeparts['minutes'] . " Minutes ";
    $str .= $timeparts['seconds'] . " Seconds ";
    $str .= 'Ago';
    return $str;
}
function getTicketDuration($start, $end)
{
    $timeparts = gettimebetweendates($start, $end);
    $str = '';
    if( 0 < $timeparts['days'] )
    {
        $str .= $timeparts['days'] . " Days ";
    }
    if( 0 < $timeparts['hours'] )
    {
        $str .= $timeparts['hours'] . " Hours ";
    }
    if( 0 < $timeparts['minutes'] )
    {
        $str .= $timeparts['minutes'] . " Minutes ";
    }
    $str .= $timeparts['seconds'] . " Seconds ";
    return $str;
}
function getStatusColour($tstatus)
{
    global $_LANG;
    static $ticketcolors;
    if( !array_key_exists($tstatus, $ticketcolors) )
    {
        $ticketcolors[$tstatus] = $color = get_query_val('tblticketstatuses', 'color', array( 'title' => $tstatus ));
    }
    else
    {
        $color = $ticketcolors[$tstatus];
    }
    $langstatus = preg_replace("/[^a-z]/i", '', strtolower($tstatus));
    if( $_LANG['supportticketsstatus' . $langstatus] )
    {
        $tstatus = $_LANG['supportticketsstatus' . $langstatus];
    }
    $statuslabel = '';
    if( $color )
    {
        $statuslabel .= "<span style=\"color:" . $color . "\">";
    }
    $statuslabel .= $tstatus;
    if( $color )
    {
        $statuslabel .= "</span>";
    }
    return $statuslabel;
}
function ticketAutoHyperlinks($message)
{
    return autoHyperLink($message);
}
function AddNote($tid, $message)
{
    if( !function_exists('getAdminName') )
    {
        require(ROOTDIR . "/includes/adminfunctions.php");
    }
    $adminname = getAdminName();
    insert_query('tblticketnotes', array( 'ticketid' => $tid, 'date' => "now()", 'admin' => $adminname, 'message' => nl2br($message) ));
    addTicketLog($tid, "Ticket Note Added");
    run_hook('TicketAddNote', array( 'ticketid' => $tid, 'message' => $message, 'adminid' => $_SESSION['adminid'] ));
}
function AdminRead($tid)
{
    $result = select_query('tbltickets', 'adminunread', array( 'id' => $tid ));
    $data = mysql_fetch_assoc($result);
    $adminread = $data['adminunread'];
    $adminreadarray = $adminread ? explode(',', $adminread) : array(  );
    if( !in_array($_SESSION['adminid'], $adminreadarray) )
    {
        $adminreadarray[] = $_SESSION['adminid'];
        update_query('tbltickets', array( 'adminunread' => implode(',', $adminreadarray) ), array( 'id' => $tid ));
    }
}
function ClientRead($tid)
{
    update_query('tbltickets', array( 'clientunread' => '' ), array( 'id' => $tid ));
}
function addTicketLog($tid, $action)
{
    if( isset($_SESSION['adminid']) )
    {
        if( !function_exists('getAdminName') )
        {
            require(ROOTDIR . "/includes/adminfunctions.php");
        }
        $action .= " (by " . getAdminName() . ")";
    }
    insert_query('tblticketlog', array( 'date' => "now()", 'tid' => $tid, 'action' => $action ));
}
function AddtoLog($tid, $action)
{
    addticketlog($tid, $action);
}
function getDepartmentName($deptid)
{
    $result = select_query('tblticketdepartments', 'name', array( 'id' => $deptid ));
    $data = mysql_fetch_array($result);
    $department = $data['name'];
    return $department;
}
function ticketGenerateAttachmentsListFromString($attachmentsString)
{
    $attachmentsOutput = '';
    $attachmentsString = trim($attachmentsString);
    if( $attachmentsString )
    {
        $attachmentsOutput .= "<br /><br /><strong>Attachments</strong><br />";
        $attachments = explode("|", $attachmentsString);
        foreach( $attachments as $i => $attachment )
        {
            $attachmentsOutput .= ($i + 1) . ". " . substr($attachment, 7) . "<br />";
        }
    }
    return $attachmentsOutput;
}
function openNewTicket($userid, $contactid, $deptid, $tickettitle, $message, $urgency, $attachmentsString = '', $from = '', $relatedservice = '', $ccemail = '', $noemail = '', $admin = '')
{
    global $CONFIG;
    $result = select_query('tblticketdepartments', '', array( 'id' => $deptid ));
    $data = mysql_fetch_array($result);
    $deptid = $data['id'];
    $noautoresponder = $data['noautoresponder'];
    if( !$deptid )
    {
        exit( "Department Not Found. Exiting." );
    }
    $ccemail = trim($ccemail);
    if( $userid )
    {
        $name = $email = '';
        if( 0 < $contactid )
        {
            $data = get_query_vals('tblcontacts', 'firstname,lastname,email', array( 'id' => $contactid, 'userid' => $userid ));
            $ccemail .= $ccemail ? ',' . $data['email'] : $data['email'];
        }
        else
        {
            $data = get_query_vals('tblclients', 'firstname,lastname,email', array( 'id' => $userid ));
        }
        if( $admin )
        {
            $message = str_replace("[NAME]", $data['firstname'] . " " . $data['lastname'], $message);
            $message = str_replace("[FIRSTNAME]", $data['firstname'], $message);
            $message = str_replace("[EMAIL]", $data['email'], $message);
        }
        $clientname = $data['firstname'] . " " . $data['lastname'];
    }
    else
    {
        if( $admin )
        {
            $message = str_replace("[NAME]", $from['name'], $message);
            $message = str_replace("[FIRSTNAME]", current(explode(" ", $from['name'])), $message);
            $message = str_replace("[EMAIL]", $from['email'], $message);
        }
        $clientname = $from['name'];
    }
    $ccemail = implode(',', array_unique(explode(',', $ccemail)));
    $length = 8;
    $seeds = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $c = null;
    $seeds_count = strlen($seeds) - 1;
    for( $i = 0; $i < $length; $i++ )
    {
        $c .= $seeds[rand(0, $seeds_count)];
    }
    $tid = genTicketMask();
    if( !in_array($urgency, array( 'High', 'Medium', 'Low' )) )
    {
        $urgency = 'Medium';
    }
    $table = 'tbltickets';
    $array = array( 'tid' => $tid, 'userid' => $userid, 'contactid' => $contactid, 'did' => $deptid, 'date' => "now()", 'title' => $tickettitle, 'message' => $message, 'urgency' => $urgency, 'status' => 'Open', 'attachment' => $attachmentsString, 'lastreply' => "now()", 'name' => $from['name'], 'email' => $from['email'], 'c' => $c, 'clientunread' => '1', 'adminunread' => '', 'service' => $relatedservice, 'cc' => $ccemail );
    if( $admin )
    {
        if( !function_exists('getAdminName') )
        {
            include_once(ROOTDIR . "/includes/adminfunctions.php");
        }
        $array['admin'] = getAdminName();
    }
    $id = insert_query($table, $array);
    $tid = genTicketMask($id);
    update_query('tbltickets', array( 'tid' => $tid ), array( 'id' => $id ));
    if( !$noemail )
    {
        if( $admin )
        {
            sendMessage("Support Ticket Opened by Admin", $id);
        }
        else
        {
            if( !$noautoresponder )
            {
                sendMessage("Support Ticket Opened", $id);
            }
        }
    }
    $deptname = getdepartmentname($deptid);
    if( !$noemail )
    {
        $adminNotifyEmail = ticketMessageFormat($message) . ticketgenerateattachmentslistfromstring($attachmentsString);
        sendAdminMessage("Support Ticket Created", array( 'ticket_id' => $id, 'ticket_tid' => $tid, 'client_id' => $userid, 'client_name' => $clientname, 'ticket_department' => $deptname, 'ticket_subject' => $tickettitle, 'ticket_priority' => $urgency, 'ticket_message' => $adminNotifyEmail ), 'support', $deptid, '', true);
    }
    if( $admin )
    {
        addticketlog($id, "New Support Ticket Opened");
    }
    else
    {
        addticketlog($id, "New Support Ticket Opened");
    }
    run_hook('TicketOpen' . ($admin ? 'Admin' : ''), array( 'ticketid' => $id, 'userid' => $userid, 'deptid' => $deptid, 'deptname' => $deptname, 'subject' => $tickettitle, 'message' => $message, 'priority' => $urgency ));
    return array( 'ID' => $id, 'TID' => $tid, 'C' => $c, 'Subject' => $tickettitle );
}
function AddReply($ticketid, $userid, $contactid, $message, $admin, $attachmentsString = '', $from = '', $status = '', $noemail = '', $api = false)
{
    global $CONFIG;
    if( !is_array($from) )
    {
        $from = array( 'name' => '', 'email' => '' );
    }
    if( $admin )
    {
        $data = get_query_vals('tbltickets', 'userid,contactid,name,email', array( 'id' => $ticketid ));
        if( 0 < $data['userid'] )
        {
            if( 0 < $data['contactid'] )
            {
                $data = get_query_vals('tblcontacts', 'firstname,lastname,email', array( 'id' => $data['contactid'], 'userid' => $data['userid'] ));
            }
            else
            {
                $data = get_query_vals('tblclients', 'firstname,lastname,email', array( 'id' => $data['userid'] ));
            }
            $message = str_replace("[NAME]", $data['firstname'] . " " . $data['lastname'], $message);
            $message = str_replace("[FIRSTNAME]", $data['firstname'], $message);
            $message = str_replace("[EMAIL]", $data['email'], $message);
        }
        else
        {
            $message = str_replace("[NAME]", $data['name'], $message);
            $message = str_replace("[FIRSTNAME]", current(explode(" ", $data['name'])), $message);
            $message = str_replace("[EMAIL]", $data['email'], $message);
        }
        if( !function_exists('getAdminName') )
        {
            require(ROOTDIR . "/includes/adminfunctions.php");
        }
        $adminname = $api ? $admin : getAdminName((int) $admin);
    }
    $table = 'tblticketreplies';
    $array = array( 'tid' => $ticketid, 'userid' => $userid, 'contactid' => $contactid, 'name' => $from['name'], 'email' => $from['email'], 'date' => "now()", 'message' => $message, 'admin' => $adminname, 'attachment' => $attachmentsString );
    $ticketreplyid = insert_query($table, $array);
    $result = select_query('tbltickets', 'tid,did,title,urgency,flag,status', array( 'id' => $ticketid ));
    $data = mysql_fetch_array($result);
    $tid = $data['tid'];
    $deptid = $data['did'];
    $tickettitle = $data['title'];
    $urgency = $data['urgency'];
    $flagadmin = $data['flag'];
    $oldStatus = $data['status'];
    if( $userid )
    {
        $result = select_query('tblclients', 'firstname,lastname', array( 'id' => $userid ));
        $data = mysql_fetch_array($result);
        $clientname = $data['firstname'] . " " . $data['lastname'];
    }
    else
    {
        $clientname = $from['name'];
    }
    $deptname = getdepartmentname($deptid);
    if( $admin )
    {
        if( $status == '' )
        {
            $status = 'Answered';
        }
        $updateqry = array( 'status' => $status, 'clientunread' => '1', 'lastreply' => "now()" );
        if( $CONFIG['TicketLastReplyUpdateClientOnly'] )
        {
            unset($updateqry['lastreply']);
        }
        update_query('tbltickets', $updateqry, array( 'id' => $ticketid ));
        addticketlog($ticketid, "New Ticket Response");
        if( !$noemail )
        {
            sendMessage("Support Ticket Reply", $ticketid, $ticketreplyid);
        }
        run_hook('TicketAdminReply', array( 'ticketid' => $ticketid, 'replyid' => $ticketreplyid, 'deptid' => $deptid, 'deptname' => $deptname, 'subject' => $tickettitle, 'message' => $message, 'priority' => $urgency, 'admin' => $adminname, 'status' => $status ));
    }
    else
    {
        $status = 'Customer-Reply';
        $updateqry = array( 'status' => 'Customer-Reply', 'clientunread' => '1', 'adminunread' => '', 'lastreply' => "now()" );
        $UpdateLastReplyTimestamp = WHMCS_Application::getinstance()->get_config('UpdateLastReplyTimestamp');
        if( $UpdateLastReplyTimestamp == 'statusonly' && ($oldStatus == $status || $oldStatus == 'Open' && $status == 'Customer-Reply') )
        {
            unset($updateqry['lastreply']);
        }
        update_query('tbltickets', $updateqry, array( 'id' => $ticketid ));
        addticketlog($ticketid, "New Ticket Response made by User");
        if( $flagadmin || !$noemail )
        {
            $adminNotifyEmail = ticketMessageFormat($message) . ticketgenerateattachmentslistfromstring($attachmentsString);
        }
        if( $flagadmin )
        {
            sendAdminMessage("Support Ticket Response", array( 'ticket_id' => $ticketid, 'ticket_tid' => $tid, 'client_id' => $userid, 'client_name' => $clientname, 'ticket_department' => $deptname, 'ticket_subject' => $tickettitle, 'ticket_priority' => $urgency, 'ticket_message' => $adminNotifyEmail ), 'support', $deptid, $flagadmin);
        }
        else
        {
            if( !$noemail )
            {
                sendAdminMessage("Support Ticket Response", array( 'ticket_id' => $ticketid, 'ticket_tid' => $tid, 'client_id' => $userid, 'client_name' => $clientname, 'ticket_department' => $deptname, 'ticket_subject' => $tickettitle, 'ticket_priority' => $urgency, 'ticket_message' => $adminNotifyEmail ), 'support', $deptid, '', true);
            }
        }
        run_hook('TicketUserReply', array( 'ticketid' => $ticketid, 'replyid' => $ticketreplyid, 'userid' => $userid, 'deptid' => $deptid, 'deptname' => $deptname, 'subject' => $tickettitle, 'message' => $message, 'priority' => $urgency, 'status' => $status ));
    }
}
function processPipedTicket($to, $name, $email, $subject, $message, $attachment)
{
    global $whmcs;
    global $CONFIG;
    global $supportticketpipe;
    global $pipenonregisteredreplyonly;
    $supportticketpipe = true;
    $decodestring = $subject . "##||-MESSAGESPLIT-||##" . $message;
    $decodestring = pipeDecodeString($decodestring);
    $decodestring = explode("##||-MESSAGESPLIT-||##", $decodestring);
    $subject = $decodestring[0];
    $message = $decodestring[1];
    $raw_message = $message;
    $result = select_query('tblticketspamfilters', '', '');
    while( $data = mysql_fetch_array($result) )
    {
        $id = $data['id'];
        $type = $data['type'];
        $content = $data['content'];
        if( $type == 'sender' )
        {
            if( strtolower($content) == strtolower($email) )
            {
                $mailstatus = "Blocked Sender";
            }
        }
        else
        {
            if( $type == 'subject' )
            {
                if( strpos('x' . strtolower($subject), strtolower($content)) )
                {
                    $mailstatus = "Blocked Subject";
                }
            }
            else
            {
                if( $type == 'phrase' && strpos('x' . strtolower($message), strtolower($content)) )
                {
                    $mailstatus = "Blocked Phrase";
                }
            }
        }
    }
    run_hook('TicketPiping', array(  ));
    if( !$mailstatus )
    {
        $pos = strpos($subject, "[Ticket ID: ");
        if( $pos === false )
        {
        }
        else
        {
            $tid = substr($subject, $pos + 12);
            $tid = substr($tid, 0, strpos($tid, "]"));
            $result = select_query('tbltickets', '', array( 'tid' => $tid ));
            $data = mysql_fetch_array($result);
            $tid = $data['id'];
            $ticketStatus = $data['status'];
        }
        $to = trim($to);
        $toemails = explode(',', $to);
        $deptid = '';
        foreach( $toemails as $toemail )
        {
            $result = select_query('tblticketdepartments', '', array( 'email' => trim(strtolower($toemail)) ));
            $data = mysql_fetch_array($result);
            $deptid = $data['id'];
            if( $deptid )
            {
                break;
            }
        }
        if( !$deptid )
        {
            $result = select_query('tblticketdepartments', '', array( 'hidden' => '' ), 'order', 'ASC', '1');
            $data = mysql_fetch_array($result);
            $deptid = $data['id'];
        }
        if( !$deptid )
        {
            $mailstatus = "Department Not Found";
        }
        else
        {
            $to = $data['email'];
            $deptclientsonly = $data['clientsonly'];
            $deptpiperepliesonly = $data['piperepliesonly'];
            $noautoresponder = $data['noautoresponder'];
            if( $to == $email )
            {
                $mailstatus = "Blocked Potential Email Loop";
            }
            else
            {
                $messagebackup = $message;
                $result = select_query('tblticketbreaklines', '', '', 'id', 'ASC');
                while( $data = mysql_fetch_array($result) )
                {
                    $breakpos = strpos($message, $data['breakline']);
                    if( $breakpos )
                    {
                        $message = substr($message, 0, $breakpos);
                    }
                }
                if( !$message )
                {
                    $message = $messagebackup;
                }
                $message = trim($message);
                $result = select_query('tbladmins', 'id', array( 'email' => $email ));
                $data = mysql_fetch_array($result);
                $adminid = $data['id'];
                if( $adminid )
                {
                    if( $tid )
                    {
                        addreply($tid, '', '', $message, $adminid, $attachment);
                        $mailstatus = "Ticket Reply Imported Successfully";
                    }
                    else
                    {
                        $mailstatus = "Ticket ID Not Found";
                    }
                }
                else
                {
                    $result = select_query('tblclients', 'id', array( 'email' => $email ));
                    $data = mysql_fetch_array($result);
                    $userid = $data['id'];
                    if( !$userid )
                    {
                        $result = select_query('tblcontacts', 'id,userid', array( 'email' => $email ));
                        $data = mysql_fetch_array($result);
                        $userid = $data['userid'];
                        $contactid = $data['id'];
                        if( $userid )
                        {
                            $ccemail = $email;
                        }
                    }
                    if( $deptclientsonly == 'on' && !$userid )
                    {
                        $mailstatus = "Unregistered Email Address";
                        if( !$noautoresponder )
                        {
                            sendMessage("Clients Only Bounce Message", '', array( $name, $email ));
                        }
                    }
                    else
                    {
                        $clientTicket = true;
                        if( $userid == '' )
                        {
                            $from['name'] = $name;
                            $from['email'] = $email;
                            $clientTicket = false;
                        }
                        $filterdate = date('YmdHis', mktime(date('H'), date('i') - 15, date('s'), date('m'), date('d'), date('Y')));
                        $query = "SELECT count(*) FROM tbltickets WHERE date>'" . $filterdate . "' AND ( email='" . mysql_real_escape_string($email) . "'";
                        if( $userid )
                        {
                            $query .= " OR userid=" . (int) $userid;
                        }
                        $query .= " )";
                        $result = full_query($query);
                        $data = mysql_fetch_array($result);
                        $numtickets = $data[0];
                        $ticketEmailLimit = (int) $whmcs->get_config('TicketEmailLimit');
                        if( !$ticketEmailLimit )
                        {
                            $ticketEmailLimit = 10;
                        }
                        if( $ticketEmailLimit < $numtickets )
                        {
                            $mailstatus = "Exceeded Limit of " . $ticketEmailLimit . " Tickets within 15 Minutes";
                        }
                        else
                        {
                            run_hook('TransliterateTicketText', array( 'subject' => $subject, 'message' => $message ));
                            if( $tid )
                            {
                                $closedTicketStatuses = array(  );
                                $result2 = select_query('tblticketstatuses', 'title', array( 'showactive' => 0, 'showawaiting' => 0, 'autoclose' => 0 ));
                                while( $data2 = mysql_fetch_array($result2) )
                                {
                                    $closedTicketStatuses[] = $data2['title'];
                                }
                                if( isset($ticketStatus) && in_array($ticketStatus, $closedTicketStatuses) && $whmcs->get_config('PreventEmailReopening') )
                                {
                                    $mailstatus = "Ticket Reopen via Email Stopped";
                                    if( !$noautoresponder )
                                    {
                                        sendMessage("Closed Ticket Bounce Message", $tid, array( $name, $email, 'clientTicket' => $clientTicket ));
                                    }
                                }
                                else
                                {
                                    addreply($tid, $userid, $contactid, htmlspecialchars_array($message), '', $attachment, htmlspecialchars_array($from));
                                    $mailstatus = "Ticket Reply Imported Successfully";
                                }
                            }
                            else
                            {
                                if( $pipenonregisteredreplyonly && !$userid )
                                {
                                    $mailstatus = "Blocked Ticket Opening from Unregistered User";
                                }
                                else
                                {
                                    if( $deptpiperepliesonly )
                                    {
                                        $mailstatus = "Only Replies Allowed by Email";
                                        if( !$noautoresponder )
                                        {
                                            sendMessage("Replies Only Bounce Message", '', array( $name, $email ));
                                        }
                                    }
                                    else
                                    {
                                        opennewticket(htmlspecialchars_array($userid), htmlspecialchars_array($contactid), htmlspecialchars_array($deptid), htmlspecialchars_array($subject), htmlspecialchars_array($message), 'Medium', $attachment, htmlspecialchars_array($from), '', htmlspecialchars_array($ccemail));
                                        $mailstatus = "Ticket Imported Successfully";
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    else
    {
        if( $attachment )
        {
            global $attachments_dir;
            $attachment = explode("|", $attachment);
            foreach( $attachment as $file )
            {
                unlink($attachments_dir . $file);
            }
        }
    }
    if( $mailstatus == '' )
    {
        $mailstatus = "Ticket Import Failed";
    }
    $table = 'tblticketmaillog';
    $array = '';
    $array = array( 'date' => "now()", 'to' => $to, 'name' => $name, 'email' => $email, 'subject' => $subject, 'message' => $message, 'status' => $mailstatus );
    insert_query($table, htmlspecialchars_array($array));
}
function uploadTicketAttachments($admin = false)
{
    $whmcs = WHMCS_Application::getinstance();
    $attachments = array(  );
    if( isset($_FILES['attachments']) )
    {
        foreach( $_FILES['attachments']['name'] as $num => $filename )
        {
            try
            {
                $file = new WHMCS_File_Upload('attachments', $num);
                $filename = $file->getCleanName();
                $validextension = checkTicketAttachmentExtension($filename);
                if( $validextension || $admin )
                {
                    $prefix = "{RAND}_";
                    $attachments[] = $file->move($whmcs->getAttachmentsDir(), $prefix);
                }
            }
            catch( WHMCS_Exception_File_NotUploaded $e )
            {
            }
        }
    }
    $attachments = implode("|", $attachments);
    return $attachments;
}
function checkTicketAttachmentExtension($file_name)
{
    global $CONFIG;
    $ext_array = $CONFIG['TicketAllowedFileTypes'];
    $ext_array = explode(',', $ext_array);
    $tmp = explode(".", $file_name);
    $extension = strtolower(end($tmp));
    $extension = "." . $extension;
    $bannedparts = array( ".php", ".cgi", ".pl", ".htaccess" );
    foreach( $bannedparts as $bannedpart )
    {
        $pos = strpos($file_name, $bannedpart);
        if( $pos !== false )
        {
            return false;
        }
    }
    foreach( $ext_array as $value )
    {
        if( trim($value) == $extension )
        {
            return true;
        }
    }
}
function pipeDecodeString($string)
{
    if( ($pos = strpos($string, "=?")) === false )
    {
        return $string;
    }
    $newresult = NULL;
    while( $pos !== false )
    {
        $newresult .= substr($string, 0, $pos);
        $string = substr($string, $pos + 2, strlen($string));
        $intpos = strpos($string, "?");
        $charset = substr($string, 0, $intpos);
        $enctype = strtolower(substr($string, $intpos + 1, 1));
        $string = substr($string, $intpos + 3, strlen($string));
        $endpos = strpos($string, "?=");
        $mystring = substr($string, 0, $endpos);
        $string = substr($string, $endpos + 2, strlen($string));
        if( $enctype == 'q' )
        {
            $mystring = quoted_printable_decode(str_replace('_', " ", $mystring));
        }
        else
        {
            if( $enctype == 'b' )
            {
                $mystring = base64_decode($mystring);
            }
        }
        $newresult .= $mystring;
        $pos = strpos($string, "=?");
    }
    $result = $newresult . $string;
    return $result;
}
function closeInactiveTickets()
{
    global $whmcs;
    global $cron;
    if( 0 < $whmcs->get_config('CloseInactiveTickets') )
    {
        $departmentresponders = array(  );
        $result = select_query('tblticketdepartments', 'id,noautoresponder', '');
        while( $data = mysql_fetch_array($result) )
        {
            $id = $data['id'];
            $noautoresponder = $data['noautoresponder'];
            $departmentresponders[$id] = $noautoresponder;
        }
        $closetitles = array(  );
        $result = select_query('tblticketstatuses', 'title', array( 'autoclose' => '1' ));
        while( $data = mysql_fetch_array($result) )
        {
            $closetitles[] = $data[0];
        }
        $ticketclosedate = date("Y-m-d H:i:s", mktime(date('H') - $whmcs->get_config('CloseInactiveTickets'), date('i'), date('s'), date('m'), date('d'), date('Y')));
        $i = 0;
        $query = "SELECT id,did,title FROM tbltickets WHERE status IN (" . db_build_in_array($closetitles) . ") AND lastreply<='" . $ticketclosedate . "'";
        for( $result = full_query($query); $data = mysql_fetch_array($result); $i++ )
        {
            $id = $data['id'];
            $did = $data['did'];
            $subject = $data['title'];
            closeTicket($id);
            if( !$departmentresponders[$did] && !$whmcs->get_config('TicketFeedback') )
            {
                sendMessage("Support Ticket Auto Close Notification", $id);
            }
            if( is_object($cron) )
            {
                $cron->logActivityDebug("Closed Ticket '" . $subject . "'");
            }
        }
        if( is_object($cron) )
        {
            $cron->logActivity("Processed " . $i . " Ticket Closures", true);
            $cron->emailLog($i . " Tickets Closed for Inactivity");
        }
    }
}
function deleteTicket($ticketid, $replyid = 0)
{
    $ticketid = (int) $ticketid;
    $replyid = (int) $replyid;
    $attachments = array(  );
    $where = 0 < $replyid ? array( 'id' => $replyid ) : array( 'tid' => $ticketid );
    $result = select_query('tblticketreplies', 'attachment', $where);
    while( $data = mysql_fetch_array($result) )
    {
        $attachments[] = $data['attachment'];
    }
    if( !$replyid )
    {
        $data = get_query_vals('tbltickets', "did, attachment", array( 'id' => $ticketid ));
        $deptid = $data['did'];
        $attachments[] = $data['attachment'];
    }
    $whmcs = WHMCS_Application::getinstance();
    foreach( $attachments as $attachment )
    {
        if( $attachment )
        {
            $attachment = explode("|", $attachment);
            foreach( $attachment as $filename )
            {
                try
                {
                    $file = new WHMCS_File($whmcs->getAttachmentsDir() . $filename);
                    $file->delete();
                }
                catch( WHMCS_Exception_File_NotFound $e )
                {
                }
            }
        }
    }
    if( !$replyid )
    {
        if( !function_exists('getCustomFields') )
        {
            require_once(ROOTDIR . "/includes/customfieldfunctions.php");
        }
        $customfields = getCustomFields('support', $deptid, $ticketid, true);
        foreach( $customfields as $field )
        {
            delete_query('tblcustomfieldsvalues', array( 'fieldid' => $field['id'], 'relid' => $ticketid ));
        }
        delete_query('tbltickettags', array( 'ticketid' => $ticketid ));
        delete_query('tblticketnotes', array( 'ticketid' => $ticketid ));
        delete_query('tblticketlog', array( 'tid' => $ticketid ));
        delete_query('tblticketreplies', array( 'tid' => $ticketid ));
        delete_query('tbltickets', array( 'id' => $ticketid ));
        logActivity("Deleted Ticket - Ticket ID: " . $ticketid);
    }
    else
    {
        delete_query('tblticketreplies', array( 'id' => $replyid ));
        addticketlog($ticketid, "Deleted Ticket Reply (ID: " . $replyid . ")");
        logActivity("Deleted Ticket Reply - ID: " . $replyid);
    }
}
function genTicketMask($id = '')
{
    global $CONFIG;
    $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVYWXYZ';
    $ticketmaskstr = '';
    $ticketmask = trim($CONFIG['TicketMask']);
    if( !$ticketmask )
    {
        $ticketmask = "%n%n%n%n%n%n";
    }
    $masklen = strlen($ticketmask);
    for( $i = 0; $i < $masklen; $i++ )
    {
        $maskval = $ticketmask[$i];
        if( $maskval == "%" )
        {
            $i++;
            $maskval .= $ticketmask[$i];
            if( $maskval == "%A" )
            {
                $ticketmaskstr .= $uppercase[rand(0, 25)];
            }
            else
            {
                if( $maskval == "%a" )
                {
                    $ticketmaskstr .= $lowercase[rand(0, 25)];
                }
                else
                {
                    if( $maskval == "%n" )
                    {
                        $ticketmaskstr .= strlen($ticketmaskstr) ? rand(0, 9) : rand(1, 9);
                    }
                    else
                    {
                        if( $maskval == "%y" )
                        {
                            $ticketmaskstr .= date('Y');
                        }
                        else
                        {
                            if( $maskval == "%m" )
                            {
                                $ticketmaskstr .= date('m');
                            }
                            else
                            {
                                if( $maskval == "%d" )
                                {
                                    $ticketmaskstr .= date('d');
                                }
                                else
                                {
                                    if( $maskval == "%i" )
                                    {
                                        $ticketmaskstr .= $id;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        else
        {
            $ticketmaskstr .= $maskval;
        }
    }
    $tid = get_query_val('tbltickets', 'id', array( 'tid' => $ticketmaskstr ));
    if( $tid )
    {
        $ticketmaskstr = genTicketMask($id);
    }
    return $ticketmaskstr;
}
function ticketMessageFormat($message)
{
    $message = strip_tags($message);
    $message = preg_replace("/\\[div=\"(.*?)\"\\]/", "<div class=\"\$1\">", $message);
    $replacetags = array( 'b' => 'strong', 'i' => 'em', 'u' => 'ul', 'div' => 'div' );
    foreach( $replacetags as $k => $v )
    {
        $message = str_replace("[" . $k . "]", "<" . $k . ">", $message);
        $message = str_replace("[/" . $k . "]", "</" . $k . ">", $message);
    }
    $message = nl2br($message);
    $message = ticketautohyperlinks($message);
    return $message;
}
function getKBAutoSuggestions($text)
{
    $kbarticles = array(  );
    $hookret = run_hook('SubmitTicketAnswerSuggestions', array( 'text' => $text ));
    if( count($hookret) )
    {
        foreach( $hookret as $hookdat )
        {
            foreach( $hookdat as $arrdata )
            {
                $kbarticles[] = $arrdata;
            }
        }
    }
    else
    {
        $ignorewords = array( 'able', 'about', 'above', 'according', 'accordingly', 'across', 'actually', 'after', 'afterwards', 'again', 'against', "ain't", 'allow', 'allows', 'almost', 'alone', 'along', 'already', 'also', 'although', 'always', 'among', 'amongst', 'another', 'anybody', 'anyhow', 'anyone', 'anything', 'anyway', 'anyways', 'anywhere', 'apart', 'appear', 'appreciate', 'appropriate', "aren't", 'around', 'aside', 'asking', 'associated', 'available', 'away', 'awfully', 'became', 'because', 'become', 'becomes', 'becoming', 'been', 'before', 'beforehand', 'behind', 'being', 'believe', 'below', 'beside', 'besides', 'best', 'better', 'between', 'beyond', 'both', 'brief', "c'mon", 'came', "can't", 'cannot', 'cant', 'cause', 'causes', 'certain', 'certainly', 'changes', 'clearly', 'come', 'comes', 'concerning', 'consequently', 'consider', 'considering', 'contain', 'containing', 'contains', 'corresponding', 'could', "couldn't", 'course', 'currently', 'definitely', 'described', 'despite', "didn't", 'different', 'does', "doesn't", 'doing', "don't", 'done', 'down', 'downwards', 'during', 'each', 'eight', 'either', 'else', 'elsewhere', 'enough', 'entirely', 'especially', 'even', 'ever', 'every', 'everybody', 'everyone', 'everything', 'everywhere', 'exactly', 'example', 'except', 'fifth', 'first', 'five', 'followed', 'following', 'follows', 'former', 'formerly', 'forth', 'four', 'from', 'further', 'furthermore', 'gets', 'getting', 'given', 'gives', 'goes', 'going', 'gone', 'gotten', 'greetings', "hadn't", 'happens', 'hardly', "hasn't", 'have', "haven't", 'having', "he's", 'hello', 'help', 'hence', 'here', "here's", 'hereafter', 'hereby', 'herein', 'hereupon', 'hers', 'herself', 'himself', 'hither', 'hopefully', 'howbeit', 'however', "i'll", "i've", 'ignored', 'immediate', 'inasmuch', 'indeed', 'indicate', 'indicated', 'indicates', 'inner', 'insofar', 'instead', 'into', 'inward', "isn't", "it'd", "it'll", "it's", 'itself', 'just', 'keep', 'keeps', 'kept', 'know', 'known', 'knows', 'last', 'lately', 'later', 'latter', 'latterly', 'least', 'less', 'lest', "let's", 'like', 'liked', 'likely', 'little', 'look', 'looking', 'looks', 'mainly', 'many', 'maybe', 'mean', 'meanwhile', 'merely', 'might', 'more', 'moreover', 'most', 'mostly', 'much', 'must', 'myself', 'name', 'namely', 'near', 'nearly', 'necessary', 'need', 'needs', 'neither', 'never', 'nevertheless', 'next', 'nine', 'nobody', 'none', 'noone', 'normally', 'nothing', 'novel', 'nowhere', 'obviously', 'often', 'okay', 'once', 'ones', 'only', 'onto', 'other', 'others', 'otherwise', 'ought', 'ours', 'ourselves', 'outside', 'over', 'overall', 'particular', 'particularly', 'perhaps', 'placed', 'please', 'plus', 'possible', 'presumably', 'probably', 'provides', 'quite', 'rather', 'really', 'reasonably', 'regarding', 'regardless', 'regards', 'relatively', 'respectively', 'right', 'said', 'same', 'saying', 'says', 'second', 'secondly', 'seeing', 'seem', 'seemed', 'seeming', 'seems', 'seen', 'self', 'selves', 'sensible', 'sent', 'serious', 'seriously', 'seven', 'several', 'shall', 'should', "shouldn't", 'since', 'some', 'somebody', 'somehow', 'someone', 'something', 'sometime', 'sometimes', 'somewhat', 'somewhere', 'soon', 'sorry', 'specified', 'specify', 'specifying', 'still', 'such', 'sure', 'take', 'taken', 'tell', 'tends', 'than', 'thank', 'thanks', 'thanx', 'that', "that's", 'thats', 'their', 'theirs', 'them', 'themselves', 'then', 'thence', 'there', "there's", 'thereafter', 'thereby', 'therefore', 'therein', 'theres', 'thereupon', 'these', 'they', "they'd", "they'll", "they're", "they've", 'think', 'third', 'this', 'thorough', 'thoroughly', 'those', 'though', 'three', 'through', 'throughout', 'thru', 'thus', 'together', 'took', 'toward', 'towards', 'tried', 'tries', 'truly', 'trying', 'twice', 'under', 'unfortunately', 'unless', 'unlikely', 'until', 'unto', 'upon', 'used', 'useful', 'uses', 'using', 'usually', 'value', 'various', 'very', 'want', 'wants', "wasn't", "we'd", "we'll", "we're", "we've", 'welcome', 'well', 'went', 'were', "weren't", 'what', "what's", 'whatever', 'when', 'whence', 'whenever', 'where', "where's", 'whereafter', 'whereas', 'whereby', 'wherein', 'whereupon', 'wherever', 'whether', 'which', 'while', 'whither', "who's", 'whoever', 'whole', 'whom', 'whose', 'will', 'willing', 'wish', 'with', 'within', 'without', "won't", 'wonder', 'would', "wouldn't", "you'd", "you'll", "you're", "you've", 'your', 'yours', 'yourself', 'yourselves', 'zero' );
        $text = str_replace("\n", " ", $text);
        $textparts = explode(" ", strtolower($text));
        $validword = 0;
        foreach( $textparts as $k => $v )
        {
            if( in_array($v, $ignorewords) || strlen($textparts[$k]) <= 3 || 100 <= $validword )
            {
                unset($textparts[$k]);
            }
            else
            {
                $validword++;
            }
        }
        $kbarticles = getKBAutoSuggestionsQuery('title', $textparts, '5');
        if( count($kbarticles) < 5 )
        {
            $numleft = 5 - count($kbarticles);
            $kbarticles = array_merge($kbarticles, getKBAutoSuggestionsQuery('article', $textparts, $numleft, $kbarticles));
        }
    }
    return $kbarticles;
}
function getKBAutoSuggestionsQuery($field, $textparts, $limit, $existingkbarticles = '')
{
    $kbarticles = array(  );
    $where = '';
    foreach( $textparts as $textpart )
    {
        $where .= $field . " LIKE '%" . db_escape_string($textpart) . "%' OR ";
    }
    $where = !$where ? "id!=''" : substr($where, 0, 0 - 4);
    if( is_array($existingkbarticles) )
    {
        $existingkbids = array(  );
        foreach( $existingkbarticles as $v )
        {
            $existingkbids[] = (int) $v['id'];
        }
        $where = "(" . $where . ")";
        if( 0 < count($existingkbids) )
        {
            $where .= " AND id NOT IN (" . db_build_in_array($existingkbids) . ")";
        }
    }
    $result = full_query("SELECT id,parentid FROM tblknowledgebase WHERE " . $where . " ORDER BY useful DESC LIMIT 0," . (int) $limit);
    while( $data = mysql_fetch_array($result) )
    {
        $articleid = $data['id'];
        $parentid = $data['parentid'];
        if( $parentid )
        {
            $articleid = $parentid;
        }
        $result2 = full_query("SELECT tblknowledgebaselinks.categoryid FROM tblknowledgebase INNER JOIN tblknowledgebaselinks ON tblknowledgebase.id=tblknowledgebaselinks.articleid INNER JOIN tblknowledgebasecats ON tblknowledgebasecats.id=tblknowledgebaselinks.categoryid WHERE (tblknowledgebase.id=" . (int) $articleid . " OR tblknowledgebase.parentid=" . (int) $articleid . ") AND tblknowledgebasecats.hidden=''");
        $data = mysql_fetch_array($result2);
        $categoryid = $data['categoryid'];
        if( $categoryid )
        {
            $result2 = full_query("SELECT * FROM tblknowledgebase WHERE (id=" . (int) $articleid . " OR parentid=" . (int) $articleid . ") AND (language='" . db_escape_string($_SESSION['Language']) . "' OR language='') ORDER BY language DESC");
            $data = mysql_fetch_array($result2);
            $title = $data['title'];
            $article = $data['article'];
            $views = $data['views'];
            $kbarticles[] = array( 'id' => $articleid, 'category' => $categoryid, 'title' => $title, 'article' => ticketsummary($article), 'text' => $article );
        }
    }
    return $kbarticles;
}
function ticketsummary($text, $length = 100)
{
    $tail = "...";
    $text = strip_tags($text);
    $txtl = strlen($text);
    if( $length < $txtl )
    {
        for( $i = 1; $text[$length - $i] != " "; $i++ )
        {
            if( $i == $length )
            {
                return substr($text, 0, $length) . $tail;
            }
        }
        $text = substr($text, 0, $length - $i + 1) . $tail;
    }
    return $text;
}
function getTicketContacts($userid)
{
    $contacts = '';
    $result = select_query('tblcontacts', '', array( 'userid' => $userid, 'email' => array( 'sqltype' => 'NEQ', 'value' => '' ) ));
    while( $data = mysql_fetch_array($result) )
    {
        $contacts .= "<option value=\"" . $data['id'] . "\"";
        if( isset($_POST['contactid']) && $_POST['contactid'] == $data['id'] )
        {
            $contacts .= " selected";
        }
        $contacts .= ">" . $data['firstname'] . " " . $data['lastname'] . " - " . $data['email'] . "</option>";
    }
    if( $contacts )
    {
        return "<select name=\"contactid\"><option value=\"0\">None</option>" . $contacts . "</select>";
    }
}
function getTicketAttachmentsInfo($ticketid, $replyid, $attachment)
{
    $attachments = array(  );
    if( $attachment )
    {
        $attachment = explode("|", $attachment);
        foreach( $attachment as $num => $file )
        {
            $file = substr($file, 7);
            if( $replyid )
            {
                $attachments[] = array( 'filename' => $file, 'dllink' => "dl.php?type=ar&id=" . $replyid . "&i=" . $num, 'deletelink' => $PHP_SELF . "?action=viewticket&id=" . $ticketid . "&removeattachment=true&type=r&idsd=" . $replyid . "&filecount=" . $num . generate_token('link') );
            }
            else
            {
                $attachments[] = array( 'filename' => $file, 'dllink' => "dl.php?type=a&id=" . $ticketid . "&i=" . $num, 'deletelink' => $PHP_SELF . "?action=viewticket&id=" . $ticketid . "&removeattachment=true&idsd=" . $ticketid . "&filecount=" . $num . generate_token('link') );
            }
        }
    }
    return $attachments;
}
function getAdminDepartmentAssignments()
{
    static $DepartmentIDs;
    if( count($DepartmentIDs) )
    {
        return $DepartmentIDs;
    }
    $result = select_query('tbladmins', 'supportdepts', array( 'id' => $_SESSION['adminid'] ));
    $data = mysql_fetch_array($result);
    $supportdepts = $data['supportdepts'];
    $supportdepts = explode(',', $supportdepts);
    foreach( $supportdepts as $k => $v )
    {
        if( !$v )
        {
            unset($supportdepts[$k]);
        }
    }
    $DepartmentIDs = $supportdepts;
    return $supportdepts;
}
function getDepartments()
{
    $departmentsarray = array(  );
    $result = select_query('tblticketdepartments', 'id,name', '');
    $departmentsarray = array(  );
    while( $data = mysql_fetch_array($result) )
    {
        $id = $data['id'];
        $name = $data['name'];
        $departmentsarray[$id] = $name;
    }
    return $departmentsarray;
}
function validateAdminTicketAccess($ticketid)
{
    $data = get_query_vals('tbltickets', 'id,did,flag', array( 'id' => $ticketid ));
    $id = $data['id'];
    $deptid = $data['did'];
    $flag = $data['flag'];
    if( !$id )
    {
        return 'invalidid';
    }
    if( !in_array($deptid, getadmindepartmentassignments()) && !checkPermission("Access All Tickets Directly", true) )
    {
        return 'deptblocked';
    }
    if( $flag && $flag != $_SESSION['adminid'] && !checkPermission("View Flagged Tickets", true) && !checkPermission("Access All Tickets Directly", true) )
    {
        return 'flagged';
    }
    return false;
}
function genPredefinedRepliesList($cat, $predefq = '')
{
    global $aInt;
    $catscontent = '';
    $repliescontent = '';
    if( !$predefq )
    {
        if( !$cat )
        {
            $cat = 0;
        }
        $result = select_query('tblticketpredefinedcats', '', array( 'parentid' => $cat ), 'name', 'ASC');
        $i = 0;
        while( $data = mysql_fetch_array($result) )
        {
            $id = $data['id'];
            $name = $data['name'];
            $catscontent .= "<td width=\"33%\"><img src=\"../images/folder.gif\" align=\"absmiddle\"> <a href=\"#\" onclick=\"selectpredefcat('" . $id . "');return false\">" . $name . "</a></td>";
            $i++;
            if( $i % 3 == 0 )
            {
                $catscontent .= "</tr><tr>";
                $i = 0;
            }
        }
    }
    $where = $predefq ? array( 'name' => array( 'sqltype' => 'LIKE', 'value' => $predefq ) ) : array( 'catid' => $cat );
    $result = select_query('tblticketpredefinedreplies', '', $where, 'name', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $id = $data['id'];
        $name = $data['name'];
        $reply = strip_tags($data['reply']);
        $shortreply = substr($reply, 0, 100) . "...";
        $shortreply = str_replace(chr(10), " ", $shortreply);
        $shortreply = str_replace(chr(13), " ", $shortreply);
        $repliescontent .= " &nbsp; <img src=\"../images/article.gif\" align=\"absmiddle\"> <a href=\"#\" onclick=\"selectpredefreply('" . $id . "');return false\">" . $name . "</a> - " . $shortreply . "<br>";
    }
    $content = '';
    if( $catscontent )
    {
        $content .= "<strong>" . $aInt->lang('support', 'categories') . "</strong><br><br><table width=\"95%\"><tr>" . $catscontent . "</tr></table><br>";
    }
    if( $repliescontent )
    {
        if( $predefq )
        {
            $content .= "<strong>" . $aInt->lang('global', 'searchresults') . "</strong><br><br>" . $repliescontent;
        }
        else
        {
            $content .= "<strong>" . $aInt->lang('support', 'replies') . "</strong><br><br>" . $repliescontent;
        }
    }
    if( !$content )
    {
        if( $predefq )
        {
            $content .= "<strong>" . $aInt->lang('global', 'searchresults') . "</strong><br><br>" . $aInt->lang('global', 'nomatchesfound') . "<br>";
        }
        else
        {
            $content .= "<span style=\"line-height:22px;\">" . $aInt->lang('support', 'catempty') . "</span><br>";
        }
    }
    $result = select_query('tblticketpredefinedcats', 'parentid', array( 'id' => $cat ));
    $data = mysql_fetch_array($result);
    if( 0 < $cat || $predefq )
    {
        $content .= "<br /><a href=\"#\" onclick=\"selectpredefcat('0');return false\"><img src=\"images/icons/navrotate.png\" align=\"top\" /> " . $aInt->lang('support', 'toplevel') . "</a>";
    }
    if( 0 < $cat )
    {
        $content .= " &nbsp;<a href=\"#\" onclick=\"selectpredefcat('" . $data[0] . "');return false\"><img src=\"images/icons/navback.png\" align=\"top\" /> " . $aInt->lang('support', 'uponelevel') . "</a>";
    }
    return $content;
}
function closeTicket($id)
{
    global $whmcs;
    $status = get_query_val('tbltickets', 'status', array( 'id' => $id ));
    if( $status == 'Closed' )
    {
        return false;
    }
    if( defined('CLIENTAREA') )
    {
        addticketlog($id, "Closed by Client");
    }
    else
    {
        if( defined('ADMINAREA') || defined('APICALL') )
        {
            addticketlog($id, "Status changed to Closed");
        }
        else
        {
            addticketlog($id, "Ticket Auto Closed For Inactivity");
        }
    }
    update_query('tbltickets', array( 'status' => 'Closed' ), array( 'id' => $id ));
    if( $whmcs->get_config('TicketFeedback') )
    {
        $feedbackcheck = get_query_val('tblticketfeedback', 'id', array( 'ticketid' => $id ));
        if( !$feedbackcheck )
        {
            sendMessage("Support Ticket Feedback Request", $id);
        }
    }
    run_hook('TicketClose', array( 'ticketid' => $id ));
    return true;
}