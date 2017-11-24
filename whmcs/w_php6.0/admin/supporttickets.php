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
$action = $whmcs->get_req_var('action');
$sub = $whmcs->get_req_var('sub');
if( in_array($action, array( 'viewticket', 'view', 'gettags', 'savetags', 'split', 'getmsg', 'getticketlog', 'getclientlog', 'gettickets', 'getallservices', 'updatereply', 'makingreply', 'endreply', 'checkstatus', 'changestatus', 'changeflag', 'loadpredefinedreplies', 'getpredefinedreply', 'getquotedtext', 'getcontacts' )) )
{
    $reqperm = "View Support Ticket";
}
else
{
    if( $action == 'openticket' || $action == 'open' )
    {
        $reqperm = "Open New Ticket";
    }
    else
    {
        $reqperm = "List Support Tickets";
    }
}
if( !$action )
{
    $aInt = new WHMCS_Admin($reqperm, false);
}
else
{
    $aInt = new WHMCS_Admin($reqperm);
}
if( $action == 'open' || $action == 'openticket' )
{
    $icon = 'ticketsopen';
    $title = $aInt->lang('support', 'opennewticket');
}
else
{
    $icon = 'tickets';
    $title = $aInt->lang('support', 'supporttickets');
}
$aInt->title = $title;
$aInt->sidebar = 'support';
$aInt->icon = $icon;
$aInt->helplink = "Support Tickets";
$aInt->requiredFiles(array( 'ticketfunctions', 'modulefunctions', 'customfieldfunctions' ));
$filters = new WHMCS_Filter('tickets');
$smartyvalues = array(  );
$jscode = '';
if( $whmcs->get_req_var('ticketid') )
{
    $action = 'search';
}
if( $action == 'gettags' )
{
    check_token("WHMCS.admin.default");
    $array = array(  );
    $result = select_query('tbltickettags', "DISTINCT tag", "tag LIKE '" . db_escape_string($q) . "%'", 'tag', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $array[] = $data[0];
    }
    echo json_encode($array);
    exit();
}
if( $action == 'savetags' )
{
    check_token("WHMCS.admin.default");
    $access = validateAdminTicketAccess($id);
    if( $access )
    {
        exit();
    }
    $tags = json_decode(WHMCS_Input_Sanitize::decode($tags), true);
    foreach( $tags as $k => $tag )
    {
        $tags[$k] = strip_tags($tag);
    }
    $existingtags = array(  );
    $result = select_query('tbltickettags', 'tag', array( 'ticketid' => $id ));
    while( $data = mysql_fetch_assoc($result) )
    {
        $existingtags[] = $data['tag'];
    }
    foreach( $existingtags as $tag )
    {
        if( trim($tag) && !in_array($tag, $tags) )
        {
            delete_query('tbltickettags', array( 'ticketid' => $id, 'tag' => $tag ));
            addTicketLog($id, "Deleted Tag " . $tag);
        }
    }
    foreach( $tags as $tag )
    {
        if( trim($tag) && !in_array($tag, $existingtags) )
        {
            insert_query('tbltickettags', array( 'ticketid' => $id, 'tag' => $tag ));
            addTicketLog($id, "Added Tag " . $tag);
        }
    }
    exit();
}
if( $action == 'checkstatus' )
{
    check_token("WHMCS.admin.default");
    $access = validateAdminTicketAccess($id);
    if( $access )
    {
        exit();
    }
    $result = select_query('tbltickets', 'status', array( 'id' => $id ));
    $data = mysql_fetch_assoc($result);
    $status = $data['status'];
    if( $status == $ticketstatus )
    {
        echo 'true';
    }
    else
    {
        echo 'false';
    }
    exit();
}
if( $action == 'split' )
{
    if( empty($rids) )
    {
        redir("action=viewticket&id=" . $id);
    }
    check_token("WHMCS.admin.default");
    $access = validateAdminTicketAccess($id);
    if( $access )
    {
        exit();
    }
    $rids = db_escape_numarray($rids);
    $splitCount = count($rids);
    $rids = implode(", ", $rids);
    $noemail = !$splitnotifyclient ? TRUE : FALSE;
    $result = select_query('tbltickets', '', array( 'id' => $id ));
    $data = mysql_fetch_array($result);
    $oldTicketID = $data['tid'];
    $newTicketUserid = $data['userid'];
    $newTicketContactid = $data['contactid'];
    $newTicketdepartmentid = $data['did'];
    $newTicketName = $data['name'];
    $newTicketEmail = $data['email'];
    $newTicketAttachment = $data['attachment'];
    $newTicketUrgency = $data['urgency'];
    $newTicketCC = $data['cc'];
    $newTicketService = $data['service'];
    $newTicketTitle = $data['title'];
    $result = select_query('tblticketreplies', 'id,message', "`id` IN (" . $rids . " )", 'date', 'ASC', '0,1');
    $data = mysql_fetch_array($result);
    $messageEarliestID = $data['id'];
    $messageEarliest = $data['message'];
    $messageAdmin = $data['admin'];
    $subject = trim($splitsubject) ? $splitsubject : $newTicketTitle;
    $deptid = trim($splitdeptid) ? $splitdeptid : $newTicketdepartmentid;
    $priority = trim($splitpriority) ? $splitpriority : $newTicketUrgency;
    $newOpenedTicketResults = openNewTicket($newTicketUserid, $newTicketContactid, $deptid, $subject, $messageEarliest, $priority, $newTicketAttachment, array( 'name' => $newTicketName, 'email' => $newTicketEmail ), $newTicketService, $newTicketCC, $noemail, $messageAdmin);
    $newTicketID = $newOpenedTicketResults['ID'];
    $repliesPlural = 1 < $splitCount ? 'Replies' : 'Reply';
    addTicketLog($id, "Ticket " . $repliesPlural . " Split to New Ticket #" . $newOpenedTicketResults['TID']);
    addTicketLog($newTicketID, "Ticket " . $repliesPlural . " Split from Ticket #" . $oldTicketID);
    delete_query('tblticketreplies', array( 'id' => $messageEarliestID ));
    update_query('tblticketreplies', array( 'tid' => $newTicketID ), "`id` IN (" . $rids . ")");
    redir("action=viewticket&id=" . $newTicketID);
}
if( $action == 'getmsg' )
{
    check_token("WHMCS.admin.default");
    $msg = '';
    $id = substr($ref, 1);
    if( substr($ref, 0, 1) == 't' )
    {
        $access = validateAdminTicketAccess($id);
        if( $access )
        {
            exit();
        }
        $msg = get_query_val('tbltickets', 'message', array( 'id' => $id ));
    }
    else
    {
        if( substr($ref, 0, 1) == 'r' )
        {
            $data = get_query_vals('tblticketreplies', 'tid,message', array( 'id' => $id ));
            $id = $data['tid'];
            $msg = $data['message'];
            $access = validateAdminTicketAccess($id);
            if( $access )
            {
                exit();
            }
        }
    }
    echo WHMCS_Input_Sanitize::decode($msg);
    exit();
}
if( $action == 'getticketlog' )
{
    check_token("WHMCS.admin.default");
    $access = validateAdminTicketAccess($id);
    if( $access )
    {
        exit();
    }
    $totaltickets = get_query_val('tblticketlog', "COUNT(id)", array( 'tid' => $id ));
    $qlimit = 10;
    $offset = (int) $offset;
    if( $offset < 0 )
    {
        $offset = 0;
    }
    $endnum = $offset + $qlimit;
    echo "<div style=\"padding:0 0 5px 0;text-align:left;\">Showing <strong>" . ($offset + 1) . "</strong> to <strong>" . ($totaltickets < $endnum ? $totaltickets : $endnum) . "</strong> of <strong>" . $totaltickets . " total</strong></div>";
    $aInt->sortableTableInit('nopagination');
    $result = select_query('tblticketlog', '', array( 'tid' => $id ), 'date', 'DESC', $offset . ',' . $qlimit);
    while( $data = mysql_fetch_array($result) )
    {
        $tabledata[] = array( fromMySQLDate($data['date'], 1), "<div style=\"text-align:left;\">" . $data['action'] . "</div>" );
    }
    echo $aInt->sortableTable(array( $aInt->lang('fields', 'date'), $aInt->lang('permissions', 'action') ), $tabledata);
    echo "<table width=\"80%\" align=\"center\"><tr><td style=\"text-align:left;\">";
    if( 0 < $offset )
    {
        echo "<a href=\"#\" onclick=\"loadTab(" . $target . ",'ticketlog'," . ($offset - $qlimit) . ");return false\">";
    }
    echo "&laquo; Previous</a></td><td style=\"text-align:right;\">";
    if( $endnum < $totaltickets )
    {
        echo "<a href=\"#\" onclick=\"loadTab(" . $target . ",'ticketlog'," . $endnum . ");return false\">";
    }
    echo "Next &raquo;</a></td></tr></table>";
    exit();
}
if( $action == 'getclientlog' )
{
    check_token("WHMCS.admin.default");
    checkPermission("View Activity Log");
    $log = new WHMCS_Log_Activity();
    $log->setCriteria(array( 'userid' => $userid ));
    $totaltickets = $log->getTotalCount();
    $qlimit = 10;
    $page = (int) $whmcs->get_req_var('offset');
    if( $page < 0 )
    {
        $page = 0;
    }
    $start = $page * $qlimit;
    $endnum = $start + $qlimit;
    echo "<div style=\"padding:0 0 5px 0;text-align:left;\">Showing <strong>" . ($start + 1) . "</strong> to <strong>" . ($totaltickets < $endnum ? $totaltickets : $endnum) . "</strong> of <strong>" . $totaltickets . " total</strong></div>";
    $aInt->sortableTableInit('nopagination');
    $tabledata = array(  );
    $logs = $log->getLogEntries($page, $qlimit);
    foreach( $logs as $entry )
    {
        $tabledata[] = array( $entry['date'], "<div align=\"left\">" . $entry['description'] . "</div>", $entry['username'], $entry['ipaddress'] );
    }
    echo $aInt->sortableTable(array( $aInt->lang('fields', 'date'), $aInt->lang('fields', 'description'), $aInt->lang('fields', 'username'), $aInt->lang('fields', 'ipaddress') ), $tabledata);
    echo "<table width=\"80%\" align=\"center\"><tr><td style=\"text-align:left;\">";
    if( 0 < $offset )
    {
        echo "<a href=\"#\" onclick=\"loadTab(" . $target . ",'clientlog'," . ($page - 1) . ");return false\">";
    }
    echo "&laquo; Previous</a></td><td style=\"text-align:right;\">";
    if( $endnum < $totaltickets )
    {
        echo "<a href=\"#\" onclick=\"loadTab(" . $target . ",'clientlog'," . ($page + 1) . ");return false\">";
    }
    echo "Next &raquo;</a></td></tr></table>";
    exit();
}
if( $action == 'gettickets' )
{
    check_token("WHMCS.admin.default");
    $departmentsarray = getDepartments();
    if( $userid )
    {
        $where = array( 'userid' => $userid );
    }
    else
    {
        $where = array( 'email' => get_query_val('tbltickets', 'email', array( 'id' => $id )) );
    }
    $totaltickets = get_query_val('tbltickets', "COUNT(id)", $where);
    $qlimit = 5;
    $offset = (int) $offset;
    if( $offset < 0 )
    {
        $offset = 0;
    }
    $endnum = $offset + $qlimit;
    echo "<div style=\"padding:0 0 5px 0;text-align:left;\">Showing <strong>" . ($offset + 1) . "</strong> to <strong>" . ($totaltickets < $endnum ? $totaltickets : $endnum) . "</strong> of <strong>" . $totaltickets . " total</strong></div>";
    $aInt->sortableTableInit('nopagination');
    $result = select_query('tbltickets', '', $where, 'lastreply', 'DESC', $offset . ',' . $qlimit);
    while( $data = mysql_fetch_array($result) )
    {
        $id = $data['id'];
        $ticketnumber = $data['tid'];
        $did = $data['did'];
        $puserid = $data['userid'];
        $name = $data['name'];
        $email = $data['email'];
        $date = $data['date'];
        $title = $data['title'];
        $message = $data['message'];
        $tstatus = $data['status'];
        $priority = $data['urgency'];
        $rawlastactivity = $data['lastreply'];
        $flag = $data['flag'];
        $adminread = $data['adminunread'];
        $adminread = explode(',', $adminread);
        if( !in_array($_SESSION['adminid'], $adminread) )
        {
            $unread = 1;
        }
        else
        {
            $unread = 0;
        }
        if( !trim($title) )
        {
            $title = "(" . $aInt->lang('emails', 'nosubject') . ")";
        }
        $flaggedto = '';
        if( $flag == $_SESSION['adminid'] )
        {
            $showflag = 'user';
        }
        else
        {
            if( $flag == 0 )
            {
                $showflag = 'none';
            }
            else
            {
                $showflag = 'other';
                $flaggedto = getAdminName($flag);
            }
        }
        $department = $departmentsarray[$did];
        if( $flaggedto )
        {
            $department .= " (" . $flaggedto . ")";
        }
        $date = fromMySQLDate($date, 'time');
        $lastactivity = fromMySQLDate($rawlastactivity, 'time');
        $tstatus = getStatusColour($tstatus);
        $lastreply = getShortLastReplyTime($rawlastactivity);
        $flagstyle = $showflag == 'user' ? "<span class=\"ticketflag\">" : '';
        $title = "#" . $ticketnumber . " - " . $title;
        if( $unread || $showflag == 'user' )
        {
            $title = "<strong>" . $title . "</strong>";
        }
        $ticketlink = "<a href=\"?action=viewticket&id=" . $id . "\"" . $ainject . ">";
        $tabledata[] = array( "<img src=\"images/" . strtolower($priority) . "priority.gif\" width=\"16\" height=\"16\" alt=\"" . $priority . "\" class=\"absmiddle\" />", $flagstyle . $date, $flagstyle . $department, "<div style=\"text-align:left;\">" . $flagstyle . $ticketlink . $title . "</a></div>", $flagstyle . $tstatus, $flagstyle . $lastreply );
    }
    echo $aInt->sortableTable(array( '', $aInt->lang('support', 'datesubmitted'), $aInt->lang('support', 'department'), $aInt->lang('fields', 'subject'), $aInt->lang('fields', 'status'), $aInt->lang('support', 'lastreply') ), $tabledata);
    echo "<table width=\"80%\" align=\"center\"><tr><td style=\"text-align:left;\">";
    if( 0 < $offset )
    {
        echo "<a href=\"#\" onclick=\"loadTab(" . $target . ",'tickets'," . ($offset - $qlimit) . ");return false\">";
    }
    echo "&laquo; Previous</a></td><td style=\"text-align:right;\">";
    if( $endnum < $totaltickets )
    {
        echo "<a href=\"#\" onclick=\"loadTab(" . $target . ",'tickets'," . $endnum . ");return false\">";
    }
    echo "Next &raquo;</a></td></tr></table>";
    exit();
}
if( $action == 'getallservices' )
{
    check_token("WHMCS.admin.default");
    $pauserid = (int) $userid;
    $currency = getCurrency($pauserid);
    $service = get_query_val('tbltickets', 'service', array( 'id' => $id ));
    $output = array(  );
    $result = select_query('tblhosting', "tblhosting.*,tblproducts.name", array( 'userid' => $pauserid ), "domainstatus` ASC,`id", 'DESC', '', "tblproducts ON tblproducts.id=tblhosting.packageid");
    while( $data = mysql_fetch_array($result) )
    {
        $service_id = $data['id'];
        $service_name = $data['name'];
        $service_domain = $data['domain'];
        $service_firstpaymentamount = $data['firstpaymentamount'];
        $service_recurringamount = $data['amount'];
        $service_billingcycle = $data['billingcycle'];
        $service_regdate = $data['regdate'];
        $service_regdate = fromMySQLDate($service_regdate);
        $service_nextduedate = $data['nextduedate'];
        $service_nextduedate = $service_nextduedate == '0000-00-00' ? '-' : fromMySQLDate($service_nextduedate);
        if( $service_recurringamount <= 0 )
        {
            $service_amount = $service_firstpaymentamount;
        }
        else
        {
            $service_amount = $service_recurringamount;
        }
        $service_amount = formatCurrency($service_amount);
        $selected = substr($service, 0, 1) == 'S' && substr($service, 1) == $service_id ? true : false;
        $service_name = "<a href=\"clientshosting.php?userid=" . $pauserid . "&id=" . $service_id . "\" target=\"_blank\">" . $service_name . "</a> - <a href=\"http://" . $service_domain . "/\" target=\"_blank\">" . $service_domain . "</a>";
        $output[] = "<tr" . ($selected ? " class=\"rowhighlight\"" : '') . "><td>" . $service_name . "</td><td>" . $service_amount . "</td><td>" . $service_billingcycle . "</td><td>" . $service_regdate . "</td><td>" . $service_nextduedate . "</td><td>" . $data['domainstatus'] . "</td></tr>";
    }
    $predefinedaddons = array(  );
    $result = select_query('tbladdons', '', '');
    while( $data = mysql_fetch_array($result) )
    {
        $addon_id = $data['id'];
        $addon_name = $data['name'];
        $predefinedaddons[$addon_id] = $addon_name;
    }
    $result = select_query('tblhostingaddons', "tblhostingaddons.*,tblhostingaddons.id AS addonid,tblhostingaddons.addonid AS addonid2,tblhostingaddons.name AS addonname,tblhosting.id AS hostingid,tblhosting.domain,tblproducts.name", array( "tblhosting.userid" => $pauserid ), "status` ASC,`tblhosting`.`id", 'DESC', '', "tblhosting ON tblhosting.id=tblhostingaddons.hostingid INNER JOIN tblproducts ON tblproducts.id=tblhosting.packageid");
    while( $data = mysql_fetch_array($result) )
    {
        $service_id = $data['id'];
        $hostingid = $data['hostingid'];
        $service_addonid = $data['addonid2'];
        $service_name = $data['name'];
        $service_addon = $data['addonname'];
        $service_domain = $data['domain'];
        $service_recurringamount = $data['recurring'];
        $service_billingcycle = $data['billingcycle'];
        $service_regdate = $data['regdate'];
        $service_regdate = fromMySQLDate($service_regdate);
        $service_nextduedate = $data['nextduedate'];
        $service_nextduedate = $service_nextduedate == '0000-00-00' ? '-' : fromMySQLDate($service_nextduedate);
        if( $service_recurringamount <= 0 )
        {
            $service_amount = $service_firstpaymentamount;
        }
        else
        {
            $service_amount = $service_recurringamount;
        }
        if( !$service_addon )
        {
            $service_addon = $predefinedaddons[$service_addonid];
        }
        $service_amount = formatCurrency($service_recurringamount);
        $selected = substr($service, 0, 1) == 'A' && substr($service, 1) == $service_id ? true : false;
        $service_name = $aInt->lang('orders', 'addon') . " - " . $service_addon . "<br /><a href=\"clientshosting.php?userid=" . $pauserid . "&id=" . $hostingid . "\" target=\"_blank\">" . $service_name . "</a> - <a href=\"http://" . $service_domain . "/\" target=\"_blank\">" . $service_domain . "</a>";
        $output[] = "<tr" . ($selected ? " class=\"rowhighlight\"" : '') . "><td>" . $service_name . "</td><td>" . $service_amount . "</td><td>" . $service_billingcycle . "</td><td>" . $service_regdate . "</td><td>" . $service_nextduedate . "</td><td>" . $data['status'] . "</td></tr>";
    }
    $result = select_query('tbldomains', '', array( 'userid' => $pauserid ), "status` ASC,`id", 'DESC');
    while( $data = mysql_fetch_array($result) )
    {
        $service_id = $data['id'];
        $service_domain = $data['domain'];
        $service_firstpaymentamount = $data['firstpaymentamount'];
        $service_recurringamount = $data['recurringamount'];
        $service_registrationperiod = $data['registrationperiod'] . " Year(s)";
        $service_regdate = $data['registrationdate'];
        $service_regdate = fromMySQLDate($service_regdate);
        $service_nextduedate = $data['nextduedate'];
        $service_nextduedate = $service_nextduedate == '0000-00-00' ? '-' : fromMySQLDate($service_nextduedate);
        if( $service_recurringamount <= 0 )
        {
            $service_amount = $service_firstpaymentamount;
        }
        else
        {
            $service_amount = $service_recurringamount;
        }
        $service_amount = formatCurrency($service_amount);
        $selected = substr($service, 0, 1) == 'D' && substr($service, 1) == $service_id ? true : false;
        $service_name = "<a href=\"clientsdomains.php?userid=" . $pauserid . "&id=" . $service_id . "\" target=\"_blank\">" . $aInt->lang('fields', 'domain') . "</a> - <a href=\"http://" . $service_domain . "/\" target=\"_blank\">" . $service_domain . "</a>";
        $output[] = "<tr" . ($selected ? " class=\"rowhighlight\"" : '') . "><td>" . $service_name . "</td><td>" . $service_amount . "</td><td>" . $service_registrationperiod . "</td><td>" . $service_regdate . "</td><td>" . $service_nextduedate . "</td><td>" . $data['status'] . "</td></tr>";
    }
    for( $i = 0; $i <= 9; $i++ )
    {
        unset($output[$i]);
    }
    echo implode($output);
    exit();
}
if( $action == 'updatereply' )
{
    check_token("WHMCS.admin.default");
    if( substr($ref, 0, 1) == 't' )
    {
        update_query('tbltickets', array( 'message' => $text ), array( 'id' => substr($ref, 1) ));
    }
    else
    {
        if( substr($ref, 0, 1) == 'r' )
        {
            update_query('tblticketreplies', array( 'message' => $text ), array( 'id' => substr($ref, 1) ));
        }
        else
        {
            if( $id && is_numeric($id) )
            {
                update_query('tblticketreplies', array( 'message' => $text ), array( 'id' => $id ));
            }
        }
    }
    $text = nl2br($text);
    $text = ticketAutoHyperlinks($text);
    echo $text;
    exit();
}
if( $action == 'makingreply' )
{
    check_token("WHMCS.admin.default");
    $access = validateAdminTicketAccess($id);
    if( $access )
    {
        exit();
    }
    $result = select_query('tbltickets', 'replyingadmin,replyingtime', array( 'id' => $id, 'replyingadmin' => array( 'sqltype' => ">", 'value' => '0' ) ));
    if( mysql_num_rows($result) )
    {
        $data = mysql_fetch_assoc($result);
        $replyingadmin = $data['replyingadmin'];
        $replyingtime = $data['replyingtime'];
        $replyingtime = fromMySQLDate($replyingtime, 'time');
        if( $replyingadmin != $_SESSION['adminid'] )
        {
            $result = select_query('tbladmins', '', array( 'id' => $replyingadmin ));
            $data = mysql_fetch_array($result);
            $replyingadmin = ucfirst($data['username']);
            echo "<div class=\"errorbox\">" . $replyingadmin . " " . $aInt->lang('support', 'viewedandstarted') . " @ " . $replyingtime . "</div>";
        }
    }
    else
    {
        update_query('tbltickets', array( 'replyingadmin' => $_SESSION['adminid'], 'replyingtime' => "now()" ), array( 'id' => $id ));
    }
    exit();
}
if( $action == 'endreply' )
{
    check_token("WHMCS.admin.default");
    $access = validateAdminTicketAccess($id);
    if( $access )
    {
        exit();
    }
    update_query('tbltickets', array( 'replyingadmin' => '' ), array( 'id' => $id ));
    exit();
}
if( $action == 'changestatus' )
{
    check_token("WHMCS.admin.default");
    $access = validateAdminTicketAccess($id);
    if( $access )
    {
        exit();
    }
    if( $status == 'Closed' )
    {
        closeTicket($id);
    }
    else
    {
        addTicketLog($id, "Status changed to " . $status);
        update_query('tbltickets', array( 'status' => $status ), array( 'id' => $id ));
        run_hook('TicketStatusChange', array( 'adminid' => $_SESSION['adminid'], 'status' => $status, 'ticketid' => $id ));
    }
    exit();
}
if( $action == 'changeflag' )
{
    check_token("WHMCS.admin.default");
    $access = validateAdminTicketAccess($id);
    if( $access )
    {
        exit();
    }
    addTicketLog($id, "Flagged to " . getAdminName($flag));
    update_query('tbltickets', array( 'flag' => $flag ), array( 'id' => $id ));
    if( $flag != 0 && $flag != $_SESSION['adminid'] )
    {
        echo '1';
    }
    exit();
}
if( $action == 'loadpredefinedreplies' )
{
    check_token("WHMCS.admin.default");
    echo genPredefinedRepliesList($cat, $predefq);
    exit();
}
if( $action == 'getpredefinedreply' )
{
    check_token("WHMCS.admin.default");
    $result = select_query('tblticketpredefinedreplies', '', array( 'id' => $id ));
    $data = mysql_fetch_array($result);
    $reply = WHMCS_Input_Sanitize::decode($data['reply']);
    echo $reply;
    exit();
}
if( $action == 'getquotedtext' )
{
    check_token("WHMCS.admin.default");
    $replytext = '';
    if( $id )
    {
        $access = validateAdminTicketAccess($id);
        if( $access )
        {
            exit();
        }
        $result = select_query('tbltickets', 'message', array( 'id' => $id ));
        $data = mysql_fetch_array($result);
        $replytext = $data['message'];
    }
    else
    {
        if( $ids )
        {
            $result = select_query('tblticketreplies', 'tid,message', array( 'id' => $ids ));
            $data = mysql_fetch_array($result);
            $id = $data['tid'];
            $access = validateAdminTicketAccess($id);
            if( $access )
            {
                exit();
            }
            $replytext = $data['message'];
        }
    }
    $replytext = wordwrap(WHMCS_Input_Sanitize::decode(strip_tags($replytext)), 80);
    $replytext = explode("\n", $replytext);
    foreach( $replytext as $line )
    {
        echo "> " . $line . "\n";
    }
    exit();
}
if( $action == 'getcontacts' )
{
    check_token("WHMCS.admin.default");
    echo getTicketContacts($userid);
    exit();
}
if( $action == 'getcustomfields' )
{
    check_token("WHMCS.admin.default");
    $id = $whmcs->get_req_var('id');
    $deptID = get_query_val('tbltickets', 'did', array( 'id' => $id ));
    $customFields = getCustomFields('support', $deptID, $id, true);
    $aInt->assign('csrfToken', generate_token('plain'));
    $aInt->assign('csrfTokenHiddenInput', generate_token());
    $aInt->assign('ticketid', $id);
    $aInt->assign('customfields', $customFields);
    $aInt->assign('numcustomfields', count($customFields));
    echo $aInt->getTemplate('viewticketcustomfields');
    exit();
}
if( $action == 'mergetickets' )
{
    check_token("WHMCS.admin.default");
    sort($selectedTickets);
    if( 1 < count($selectedTickets) )
    {
        $masterTID = $selectedTickets[0];
        addTicketLog($masterTID, "Merged Tickets " . implode(',', $selectedTickets));
        $result = select_query('tbltickets', "title, userid", array( 'id' => $masterTID ));
        $data = mysql_fetch_array($result);
        $userID = $data['userid'];
        getUsersLang($userID);
        $merge = $_LANG['ticketmerge'];
        if( !$merge || $merge == '' )
        {
            $merge = 'MERGED';
        }
        $subject = strpos($data[0], " [" . $merge . "]") === false ? $data[0] . " [" . $merge . "]" : $data[0];
        update_query('tbltickets', array( 'title' => $subject ), array( 'id' => $masterTID ));
        foreach( $selectedTickets as $id )
        {
            update_query('tblticketnotes', array( 'ticketid' => $masterTID ), array( 'ticketid' => $id ));
            update_query('tblticketreplies', array( 'tid' => $masterTID ), array( 'tid' => $id ));
            if( $id != $masterTID )
            {
                $result = select_query('tbltickets', '', array( 'id' => $id ));
                $data = mysql_fetch_array($result);
                $userid = $data['userid'];
                $name = $data['name'];
                $email = $data['email'];
                $date = $data['date'];
                $message = $data['message'];
                $admin = $data['admin'];
                $attachment = $data['attachment'];
                insert_query('tblticketreplies', array( 'tid' => $masterTID, 'userid' => $userID, 'name' => $name, 'email' => $email, 'date' => $date, 'message' => $message, 'admin' => $admin, 'attachment' => $attachment ));
                delete_query('tbltickets', array( 'id' => $id ));
            }
        }
        echo 1;
    }
    else
    {
        echo 0;
    }
    exit();
}
if( $action == 'deletetickets' )
{
    check_token("WHMCS.admin.default");
    if( 0 < count($selectedTickets) )
    {
        if( !checkPermission("Delete Ticket", true) )
        {
            echo 'denied';
            exit();
        }
        foreach( $selectedTickets as $id )
        {
            deleteTicket($id);
        }
        echo 1;
    }
    else
    {
        echo 0;
    }
    exit();
}
if( $action == 'blockdeletetickets' )
{
    check_token("WHMCS.admin.default");
    if( 0 < count($selectedTickets) )
    {
        if( !checkPermission("Delete Ticket", true) )
        {
            echo 'denied';
            exit();
        }
        foreach( $selectedTickets as $id )
        {
            $result = select_query('tbltickets', "userid, email", array( 'id' => $id ));
            $data = mysql_fetch_array($result);
            $userID = $data['userid'];
            $email = $data['email'];
            if( $userID )
            {
                $result = select_query('tblclients', 'email', array( 'id' => $userID ));
                $data = mysql_fetch_array($result);
                $email = $data['email'];
            }
            $result = select_query('tblticketspamfilters', "COUNT(*)", array( 'type' => 'Sender', 'content' => $email ));
            $data = mysql_fetch_array($result);
            $blockedAlready = $data[0];
            if( !$blockedAlready )
            {
                insert_query('tblticketspamfilters', array( 'type' => 'Sender', 'content' => $email ));
            }
            deleteTicket($id);
        }
        echo 1;
    }
    else
    {
        echo 0;
    }
    exit();
}
if( $action == 'closetickets' )
{
    check_token("WHMCS.admin.default");
    if( 0 < count($selectedTickets) )
    {
        foreach( $selectedTickets as $id )
        {
            closeTicket($id);
        }
        echo 1;
    }
    else
    {
        echo 0;
    }
    exit();
}
if( !$action )
{
    if( $sub == 'deleteticket' )
    {
        check_token("WHMCS.admin.default");
        checkPermission("Delete Ticket");
        deleteTicket($id);
        redir();
    }
}
else
{
    if( $action == 'mergeticket' )
    {
        check_token("WHMCS.admin.default");
        $result = select_query('tbltickets', 'id', array( 'tid' => $mergetid ));
        $data = mysql_fetch_array($result);
        $mergeid = $data['id'];
        if( !$mergeid )
        {
            exit( $aInt->lang('support', 'mergeidnotfound') );
        }
        if( $mergeid == $id )
        {
            exit( $aInt->lang('support', 'mergeticketequal') );
        }
        $mastertid = $id;
        if( $mergeid < $mastertid )
        {
            $mastertid = $mergeid;
            $mergeid = $id;
        }
        $adminname = getAdminName();
        addTicketLog($mastertid, "Merged Ticket " . $mergeid);
        $adminname = '';
        $result = select_query('tbltickets', 'title,userid', array( 'id' => $mastertid ));
        $data = mysql_fetch_array($result);
        $userid = $data['userid'];
        getUsersLang($userid);
        $merge = $_LANG['ticketmerge'];
        if( !$merge )
        {
            $merge = 'MERGED';
        }
        $subject = strpos($data[0], " [" . $merge . "]") === FALSE ? $data[0] . " [" . $merge . "]" : $data[0];
        update_query('tbltickets', array( 'title' => $subject ), array( 'id' => $mastertid ));
        update_query('tblticketnotes', array( 'ticketid' => $mastertid ), array( 'ticketid' => $mergeid ));
        update_query('tblticketreplies', array( 'tid' => $mastertid ), array( 'tid' => $mergeid ));
        $result = select_query('tbltickets', '', array( 'id' => $mergeid ));
        $data = mysql_fetch_array($result);
        $userid = $data['userid'];
        $name = $data['name'];
        $email = $data['email'];
        $date = $data['date'];
        $message = $data['message'];
        $admin = $data['admin'];
        $attachment = $data['attachment'];
        insert_query('tblticketreplies', array( 'tid' => $mastertid, 'userid' => $userid, 'name' => $name, 'email' => $email, 'date' => $date, 'message' => $message, 'admin' => $admin, 'attachment' => $attachment ));
        delete_query('tbltickets', array( 'id' => $mergeid ));
        redir("action=viewticket&id=" . $mastertid);
    }
    else
    {
        if( $action == 'openticket' )
        {
            check_token("WHMCS.admin.default");
            $validate = new WHMCS_Validate();
            $validate->validate('required', 'message', array( 'support', 'ticketmessageerror' ));
            $validate->validate('required', 'subject', array( 'support', 'ticketsubjecterror' ));
            if( !$client )
            {
                if( $validate->validate('required', 'email', array( 'support', 'ticketemailerror' )) )
                {
                    $validate->validate('email', 'email', array( 'support', 'ticketemailvalidationerror' ));
                }
                $validate->validate('required', 'name', array( 'support', 'ticketnameerror' ));
            }
            if( !$validate->hasErrors() )
            {
                $attachments = uploadTicketAttachments(true);
                $client = (int) str_replace("UserID:", '', $client);
                $ticketdata = openNewTicket($client, $contactid, $deptid, $subject, $message, $priority, $attachments, array( 'name' => $name, 'email' => $email ), $relatedservice, $ccemail, $sendemail ? false : true, true);
                $id = $ticketdata['ID'];
                redir("action=viewticket&id=" . $id);
            }
            else
            {
                $action = 'open';
            }
        }
        else
        {
            if( $action == 'viewticket' || $action == 'view' )
            {
                $access = validateAdminTicketAccess($id);
                if( $access == 'invalidid' )
                {
                    $aInt->gracefulExit($aInt->lang('support', 'ticketnotfound'));
                }
                if( $access == 'deptblocked' )
                {
                    $aInt->gracefulExit($aInt->lang('support', 'deptnoaccess'));
                }
                if( $access == 'flagged' )
                {
                    $aInt->gracefulExit($aInt->lang('support', 'flagnoaccess') . ": " . getAdminName(get_query_val('tbltickets', 'flag', array( 'id' => $id ))));
                }
                if( $access )
                {
                    $aInt->gracefulExit("Access Denied");
                }
                if( $postreply || $postaction )
                {
                    check_token("WHMCS.admin.default");
                    if( $postaction == 'note' )
                    {
                        AddNote($id, $message);
                    }
                    else
                    {
                        $attachments = uploadTicketAttachments(true);
                        if( $postaction == 'close' )
                        {
                            $newstatus = 'Closed';
                        }
                        else
                        {
                            if( substr($postaction, 0, 9) == 'setstatus' )
                            {
                                $result = select_query('tblticketstatuses', 'title', array( 'id' => substr($postaction, 9) ));
                                $data = mysql_fetch_array($result);
                                $newstatus = $data[0];
                            }
                            else
                            {
                                if( $postaction == 'onhold' )
                                {
                                    $newstatus = "On Hold";
                                }
                                else
                                {
                                    if( $postaction == 'inprogress' )
                                    {
                                        $newstatus = "In Progress";
                                    }
                                    else
                                    {
                                        $newstatus = 'Answered';
                                    }
                                }
                            }
                        }
                        AddReply($id, '', '', $message, WHMCS_Session::get('adminid'), $attachments, '', $newstatus);
                        run_hook('TicketStatusChange', array( 'adminid' => $_SESSION['adminid'], 'status' => $newstatus, 'ticketid' => $id ));
                        if( $billingdescription && $billingdescription != $aInt->lang('support', 'toinvoicedes') )
                        {
                            checkPermission("Create Invoice");
                            $result = select_query('tbltickets', '', array( 'id' => $id ));
                            $data = mysql_fetch_array($result);
                            $userid = $data['userid'];
                            $contactid = $data['contactid'];
                            $invoicenow = false;
                            if( $billingaction == '3' )
                            {
                                $invoicenow = true;
                                $billingaction = '1';
                            }
                            $billingamount = preg_replace("/[^0-9.]/", '', $billingamount);
                            insert_query('tblbillableitems', array( 'userid' => $userid, 'description' => $billingdescription, 'amount' => $billingamount, 'recur' => 0, 'recurcycle' => 0, 'recurfor' => 0, 'invoiceaction' => $billingaction, 'duedate' => "now()" ));
                            if( $invoicenow )
                            {
                                require(ROOTDIR . "/includes/clientfunctions.php");
                                require(ROOTDIR . "/includes/processinvoices.php");
                                require(ROOTDIR . "/includes/invoicefunctions.php");
                                createInvoices($userid);
                            }
                        }
                        if( $postaction == 'close' )
                        {
                            update_query('tbltickets', array( 'status' => 'Answered' ), array( 'id' => $id ));
                            closeTicket($id);
                        }
                    }
                    update_query('tbltickets', array( 'replyingadmin' => '', 'replyingtime' => '' ), array( 'id' => $id ));
                    if( $postaction == 'close' )
                    {
                        closeTicket($id);
                        $filters->redir();
                    }
                    else
                    {
                        if( $postaction == 'return' )
                        {
                            $filters->redir();
                        }
                        else
                        {
                            if( $postaction == 'onhold' )
                            {
                                update_query('tbltickets', array( 'status' => "On Hold" ), array( 'id' => $id ));
                                run_hook('TicketStatusChange', array( 'adminid' => $_SESSION['adminid'], 'status' => "On Hold", 'ticketid' => $id ));
                            }
                            else
                            {
                                if( $postaction == 'inprogress' )
                                {
                                    update_query('tbltickets', array( 'status' => "In Progress" ), array( 'id' => $id ));
                                    run_hook('TicketStatusChange', array( 'adminid' => $_SESSION['adminid'], 'status' => "In Progress", 'ticketid' => $id ));
                                }
                            }
                        }
                    }
                    redir("action=viewticket&id=" . $id);
                }
                if( $deptid )
                {
                    check_token("WHMCS.admin.default");
                    $adminname = getAdminName();
                    $result = select_query('tbltickets', '', array( 'id' => $id ));
                    $data = mysql_fetch_array($result);
                    $orig_userid = $data['userid'];
                    $orig_contactid = $data['contactid'];
                    $orig_deptid = $data['did'];
                    $orig_status = $data['status'];
                    $orig_priority = $data['urgency'];
                    $orig_flag = $data['flag'];
                    $orig_cc = $data['cc'];
                    if( $orig_userid != $userid )
                    {
                        addTicketLog($id, "Ticket Assigned to User ID " . $userid);
                    }
                    if( $orig_deptid != $deptid )
                    {
                        migrateCustomFields('support', $id, $deptid);
                        $ticket = new WHMCS_Tickets();
                        $ticket->setID($id);
                        $ticket->changeDept($deptid);
                    }
                    if( $orig_status != $status )
                    {
                        if( $status == 'Closed' )
                        {
                            closeTicket($id);
                        }
                        else
                        {
                            addTicketLog($id, "Status changed to " . $status);
                        }
                    }
                    if( $orig_priority != $priority )
                    {
                        addTicketLog($id, "Priority changed to " . $priority);
                    }
                    if( $orig_cc != $cc )
                    {
                        addTicketLog($id, "Modified CC Recipients");
                    }
                    if( $orig_flag != $flagto )
                    {
                        $ticket = new WHMCS_Tickets();
                        $ticket->setID($id);
                        $ticket->setFlagTo($flagto);
                    }
                    $table = 'tbltickets';
                    $array = array( 'status' => $status, 'urgency' => $priority, 'title' => $subject, 'userid' => $userid, 'cc' => $cc );
                    $where = array( 'id' => $id );
                    update_query($table, $array, $where);
                    if( $orig_status != 'Closed' && $status == 'Closed' )
                    {
                        run_hook('TicketClose', array( 'ticketid' => $id ));
                    }
                    if( $mergetid )
                    {
                        redir("action=mergeticket&id=" . $id . "&mergetid=" . $mergetid . generate_token('link'));
                    }
                    redir("action=viewticket&id=" . $id);
                }
                if( $removeattachment )
                {
                    check_token("WHMCS.admin.default");
                    if( $type == 'r' )
                    {
                        $result = select_query('tblticketreplies', '', array( 'id' => $idsd ));
                        $data = mysql_fetch_array($result);
                        $attachments = $data['attachment'];
                    }
                    else
                    {
                        $result = select_query('tbltickets', '', array( 'id' => $idsd ));
                        $data = mysql_fetch_array($result);
                        $attachments = $data['attachment'];
                    }
                    $attachments = explode("|", $attachments);
                    $i = (int) $filecount;
                    $filename = $attachments[$i];
                    try
                    {
                        $file = new WHMCS_File($whmcs->getAttachmentsDir() . $filename);
                        $file->delete();
                    }
                    catch( WHMCS_Exception_File_NotFound $e )
                    {
                    }
                    unset($attachments[$i]);
                    if( $type == 'r' )
                    {
                        update_query('tblticketreplies', array( 'attachment' => implode("|", $attachments) ), array( 'id' => $idsd ));
                    }
                    else
                    {
                        update_query('tbltickets', array( 'attachment' => implode("|", $attachments) ), array( 'id' => $idsd ));
                    }
                    redir("action=viewticket&id=" . $id);
                }
                if( $sub == 'del' )
                {
                    check_token("WHMCS.admin.default");
                    checkPermission("Delete Ticket");
                    deleteTicket($id, $idsd);
                    redir("action=viewticket&id=" . $id);
                }
                if( $sub == 'delnote' )
                {
                    check_token("WHMCS.admin.default");
                    delete_query('tblticketnotes', array( 'id' => $idsd ));
                    addTicketLog($id, "Deleted Ticket Note ID " . $idsd);
                    redir("action=viewticket&id=" . $id);
                }
                if( $blocksender )
                {
                    check_token("WHMCS.admin.default");
                    $result = select_query('tbltickets', 'userid,email', array( 'id' => $id ));
                    $data = get_query_vals('tbltickets', 'userid,email', array( 'id' => $id ));
                    $userid = $data['userid'];
                    $email = $data['email'];
                    if( $userid )
                    {
                        $email = get_query_val('tblclients', 'email', array( 'id' => $userid ));
                    }
                    $blockedalready = get_query_val('tblticketspamfilters', "COUNT(*)", array( 'type' => 'Sender', 'content' => $email ));
                    if( $blockedalready )
                    {
                        redir("action=viewticket&id=" . $id . "&blockresult=2");
                    }
                    else
                    {
                        insert_query('tblticketspamfilters', array( 'type' => 'Sender', 'content' => $email ));
                        redir("action=viewticket&id=" . $id . "&blockresult=1&email=" . $email);
                    }
                }
                if( $blockresult == '1' )
                {
                    infoBox($aInt->lang('support', 'spamupdatesuccess'), sprintf($aInt->lang('support', 'spamupdatesuccessinfo'), $email));
                }
                if( $blockresult == '2' )
                {
                    infoBox($aInt->lang('support', 'spamupdatefailed'), $aInt->lang('support', 'spamupdatefailedinfo'));
                }
            }
        }
    }
}
if( $autorefresh = $whmcs->get_req_var('autorefresh') )
{
    check_token("WHMCS.admin.default");
    setcookie('WHMCSAutoRefresh', null, 0 - 86400);
    WHMCS_Cookie::delete('AutoRefresh');
    if( is_numeric($autorefresh) )
    {
        WHMCS_Cookie::set('AutoRefresh', $autorefresh, time() + 90 * 24 * 60 * 60);
    }
    redir();
}
if( $action == 'viewticket' || $action == 'view' )
{
    $result = select_query('tbltickets', '', array( 'id' => $id ));
    $data = mysql_fetch_array($result);
    $replyingadmin = $data['replyingadmin'];
    if( !$replyingadmin )
    {
        $adminheaderbodyjs = "onunload=\"endMakingReply();\"";
    }
}
$supportdepts = getAdminDepartmentAssignments();
ob_start();
$view = $filters->get('view');
$deptid = $filters->get('deptid');
$client = $filters->get('client');
$clientid = $filters->get('clientid');
$clientname = $filters->get('clientname');
$subject = $filters->get('subject');
$email = $filters->get('email');
$tag = $filters->get('tag');
$smartyvalues['ticketfilterdata'] = array( 'view' => $view, 'deptid' => $deptid, 'subject' => $subject, 'email' => $email );
if( !$action )
{
    WHMCS_Session::release();
    $smartyvalues['inticketlist'] = true;
    if( !count($supportdepts) )
    {
        $aInt->gracefulExit($aInt->lang('permissions', 'accessdenied') . " - " . $aInt->lang('support', 'noticketdepts'));
    }
    $tickets = new WHMCS_Tickets();
    $autorefresh = isset($_COOKIE['WHMCSAutoRefresh']) ? (int) $_COOKIE['WHMCSAutoRefresh'] : 0;
    if( $autorefresh && !$action )
    {
        $refreshtime = $autorefresh * 60;
        if( $refreshtime && !$disable_auto_ticket_refresh )
        {
            echo "<meta http-equiv=\"refresh\" content=\"" . $refreshtime . "\">";
        }
    }
    echo $aInt->Tabs(array( $aInt->lang('global', 'searchfilter'), $aInt->lang('support', 'autorefresh') ), true);
    echo "\n<div id=\"tab0box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n<form action=\"";
    echo $whmcs->getPhpSelf();
    echo "\" method=\"post\">\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">";
    echo $aInt->lang('fields', 'status');
    echo "</td><td class=\"fieldarea\"><select name=\"view\">\n<option value=\"any\"";
    if( $view == 'any' )
    {
        echo " selected";
    }
    echo ">";
    echo $aInt->lang('global', 'any');
    echo "</option>\n<option value=\"\"";
    if( $view == '' )
    {
        echo " selected";
    }
    echo ">";
    echo $aInt->lang('support', 'awaitingreply');
    echo "</option>\n<option value=\"flagged\"";
    if( $view == 'flagged' )
    {
        echo " selected";
    }
    echo ">";
    echo $aInt->lang('support', 'flagged');
    echo "</option>\n<option value=\"active\"";
    if( $view == 'active' )
    {
        echo " selected";
    }
    echo ">";
    echo $aInt->lang('support', 'allactive');
    echo "</option>\n";
    $result = select_query('tblticketstatuses', '', '', 'sortorder', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        echo "<option";
        if( $view == $data['title'] )
        {
            echo " selected";
        }
        echo ">" . $data['title'] . "</option>";
    }
    echo "</select></td><td width=\"15%\" class=\"fieldlabel\">";
    echo $aInt->lang('fields', 'client');
    echo "</td><td class=\"fieldarea\">";
    if( $CONFIG['DisableClientDropdown'] )
    {
        echo "<input type=\"text\" name=\"client\" value=\"" . $client . "\" size=\"10\" />";
    }
    else
    {
        echo $aInt->clientsDropDown($client, '', 'client', true);
    }
    echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang('support', 'department');
    echo "</td><td class=\"fieldarea\"><select name=\"deptid\"><option value=\"\">";
    echo $aInt->lang('global', 'any');
    echo "</option>";
    $result = select_query('tblticketdepartments', '', '', 'order', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $id = $data['id'];
        $name = $data['name'];
        if( in_array($id, $supportdepts) )
        {
            echo "<option value=\"" . $id . "\"";
            if( $id == $deptid )
            {
                echo " selected";
            }
            echo ">" . $name . "</option>";
        }
    }
    echo "</select></td><td class=\"fieldlabel\">";
    echo $aInt->lang('support', 'ticketid');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"ticketid\" size=\"15\" /></td></tr>\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang('support', 'subjectmessage');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"subject\" size=\"40\" value=\"";
    echo $subject;
    echo "\" /></td><td class=\"fieldlabel\">";
    echo $aInt->lang('fields', 'email');
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"email\" size=\"40\" value=\"";
    echo $email;
    echo "\" /></td></tr>\n</table>\n\n<img src=\"images/spacer.gif\" height=\"10\" width=\"1\"><br>\n<DIV ALIGN=\"center\"><input type=\"submit\" value=\"";
    echo $aInt->lang('global', 'searchfilter');
    echo "\" class=\"button\"></DIV>\n\n</form>\n\n  </div>\n</div>\n<div id=\"tab1box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n<form action=\"";
    echo $whmcs->getPhpSelf();
    echo "\" method=\"post\">\n<div align=\"center\">";
    echo $aInt->lang('support', 'autorefreshevery');
    echo " <select name=\"autorefresh\"><option>Never</option>\n";
    $times = array( 1, 2, 5, 10, 15 );
    foreach( $times as $time )
    {
        echo "<option value=\"" . $time . "\"";
        if( $time == $autorefresh )
        {
            echo " selected";
        }
        echo ">" . $time . " " . $aInt->lang('support', 'minute' . (1 < $time ? 's' : '')) . "</option>";
    }
    echo "</select> <input type=\"submit\" value=\"";
    echo $aInt->lang('support', 'setautorefresh');
    echo "\" class=\"button\" /></div>\n</form>\n\n  </div>\n</div>\n\n<br />\n\n";
    if( $actionresult )
    {
        switch( $actionresult )
        {
            case 'blockdeleteticketsfailed':
            case 'closeticketsfailed':
                break;
            case 'deleteticketsfailed':
                break;
            case 'mergeticketsfailed':
                infoBox($aInt->lang('global', 'erroroccurred'), $aInt->lang('support', $actionresult), 'error');
                break;
            case 'blockdeleteticketssuccess':
                break;
            case 'closeticketssuccess':
                break;
            case 'deleteticketssuccess':
                break;
            case 'mergeticketssuccess':
                infoBox($aInt->lang('global', 'success'), $aInt->lang('support', $actionresult), 'success');
        }
        echo $infobox;
    }
    $tag = $whmcs->get_req_var('tag');
    if( $tag )
    {
        echo "<h2>Filtering Tickets for Tag <strong>\"" . $tag . "\"</strong></h2>";
    }
    $massactionbtns = "    <input onclick=\"showDialog('merge')\" type=\"button\" value=\"" . $aInt->lang('clientsummary', 'merge') . "\" name=\"merge\" class=\"btn-small\" />\n    <input onclick=\"showDialog('close')\" type=\"button\" value=\"" . $aInt->lang('global', 'close') . "\" name=\"close\" class=\"btn-small\" />\n    <input onclick=\"showDialog('delete')\" type=\"button\" value=\"" . $aInt->lang('global', 'delete') . "\" name=\"delete\" class=\"btn-small\" />\n    <input onclick=\"showDialog('blockDelete')\" type=\"button\" value=\"" . $aInt->lang('support', 'blockanddelete') . "\" name=\"blockdelete\" class=\"btn-small\" />";
    $name = 'tickets';
    $orderby = 'lastreply';
    $sort = 'DESC';
    $pageObj = new WHMCS_Pagination($name, $orderby, $sort);
    $pageObj->digestCookieData();
    $pageObj->setPagination(false);
    $filters->store();
    $tbl = new WHMCS_ListTable($pageObj, 1);
    $tbl->setColumns(array( 'checkall', '', array( 'deptname', $aInt->lang('support', 'department') ), array( 'title', $aInt->lang('fields', 'subject') ), $aInt->lang('support', 'submitter'), array( 'status', $aInt->lang('fields', 'status') ), array( 'lastreply', $aInt->lang('support', 'lastreply') ) ));
    $ticketsModel = new WHMCS_Tickets($pageObj);
    $criteria = array( 'status' => $view, 'deptid' => $deptid, 'subject' => $subject, 'tag' => $tag, 'client' => $client, 'clientid' => $clientid, 'clientname' => $clientname, 'email' => $email, 'flag' => WHMCS_Auth::getid() );
    $ticketsModel->execute($criteria);
    $ticketslist = $pageObj->getData();
    if( count($ticketslist) )
    {
        foreach( $ticketslist as $ticket )
        {
            $tbl->addRow(array( "<input type=\"checkbox\" name=\"selectedtickets[]\" value=\"" . $ticket['id'] . "\" class=\"checkall\" />", "<img src=\"images/" . strtolower($ticket['priority']) . "priority.gif\" width=\"16\" height=\"16\" alt=\"" . $ticket['priority'] . "\" class=\"absmiddle\" />", $ticket['department'], "<a href=\"supporttickets.php?action=view&id=" . $ticket['id'] . "\"" . ($ticket['unread'] ? " style=\"font-weight:bold;\"" : '') . " title=\"" . $ticket['textsummary'] . "\">#" . $ticket['ticketnum'] . " - " . $ticket['subject'] . "</a>", $ticket['clientname'], $ticket['status'], $ticket['lastreply'] ));
        }
        $tbl->setPagination(false);
        $tbl->setMassActionBtns($massactionbtns);
        echo "<h2>" . $aInt->lang('support', 'assignedtickets') . "</h2><p>" . sprintf($aInt->lang('support', 'numticketsassigned'), $pageObj->getNumResults()) . "</p>" . $tbl->output() . "<br /><h2>" . $aInt->lang('support', 'unassignedtickets') . "</h2>";
    }
    unset($ticketslist);
    unset($ticketsModel);
    $name = 'tickets';
    $orderby = 'lastreply';
    $sort = 'DESC';
    $pageObj = new WHMCS_Pagination($name, $orderby, $sort);
    $pageObj->digestCookieData();
    $tbl = new WHMCS_ListTable($pageObj, 2);
    $tbl->setColumns(array( 'checkall', '', array( 'deptname', $aInt->lang('support', 'department') ), array( 'title', $aInt->lang('fields', 'subject') ), $aInt->lang('support', 'submitter'), array( 'status', $aInt->lang('fields', 'status') ), array( 'lastreply', $aInt->lang('support', 'lastreply') ) ));
    $ticketsModel = new WHMCS_Tickets($pageObj);
    $criteria = array( 'status' => $view, 'deptid' => $deptid, 'subject' => $subject, 'tag' => $tag, 'client' => $client, 'clientid' => $clientid, 'clientname' => $clientname, 'email' => $email, 'notflaggedto' => WHMCS_Auth::getid() );
    $ticketsModel->execute($criteria);
    $ticketslist = $pageObj->getData();
    foreach( $ticketslist as $ticket )
    {
        $tbl->addRow(array( "<input type=\"checkbox\" name=\"selectedtickets[]\" value=\"" . $ticket['id'] . "\" class=\"checkall\" />", "<img src=\"images/" . strtolower($ticket['priority']) . "priority.gif\" width=\"16\" height=\"16\" alt=\"" . $ticket['priority'] . "\" class=\"absmiddle\" />", $ticket['department'], "<a href=\"supporttickets.php?action=view&id=" . $ticket['id'] . "\"" . ($ticket['unread'] ? " style=\"font-weight:bold;\"" : '') . " title=\"" . $ticket['textsummary'] . "\">#" . $ticket['ticketnum'] . " - " . $ticket['subject'] . "</a>", $ticket['clientname'], $ticket['status'], $ticket['lastreply'] ));
    }
    $tbl->setMassActionBtns($massactionbtns);
    $tbl->setShowMassActionBtnsTop(true);
    echo $tbl->output();
    $smartyvalues['tagcloud'] = $ticketsModel->buildTagCloud();
    unset($ticketslist);
    unset($ticketsModel);
    $jscode .= "\nfunction ticketMassAction(action) {\n    var selectedTickets = [];\n    \$(\"input:checkbox[name='selectedtickets[]']:checked\").each(function(){\n        selectedTickets.push(parseInt(\$(this).val()));\n    });\n    \$.post(\n        \"supporttickets.php\",\n        { action: action,\n          'selectedTickets[]': selectedTickets,\n          token: \"" . generate_token('plain') . "\"\n        },\n        function (data) {\n            if (data=='1') {\n                window.location='" . $whmcs->getPhpSelf() . "?actionresult='+action+'success&filter=1'\n            } else {\n                window.location='" . $whmcs->getPhpSelf() . "?actionresult='+action+'failed&filter=1'\n            }\n        }\n    );\n}\n\n";
    echo $aInt->jqueryDialog('merge', $aInt->lang('support', 'mergeticket'), $aInt->lang('support', 'massmergeconfirm'), array( $aInt->lang('global', 'yes') => "ticketMassAction('mergetickets')", $aInt->lang('global', 'no') => '' ));
    echo $aInt->jqueryDialog('delete', $aInt->lang('global', 'delete'), $aInt->lang('support', 'massdeleteconfirm'), array( $aInt->lang('global', 'yes') => "ticketMassAction('deletetickets')", $aInt->lang('global', 'no') => '' ));
    echo $aInt->jqueryDialog('blockDelete', $aInt->lang('support', 'blockanddelete'), $aInt->lang('support', 'massblockdeleteconfirm'), array( $aInt->lang('global', 'yes') => "ticketMassAction('blockdeletetickets')", $aInt->lang('global', 'no') => '' ));
    echo $aInt->jqueryDialog('close', $aInt->lang('global', 'close'), $aInt->lang('support', 'masscloseconfirm'), array( $aInt->lang('global', 'yes') => "ticketMassAction('closetickets')", $aInt->lang('global', 'no') => '' ));
}
if( $action == 'search' )
{
    $where = "tid='" . db_escape_string($ticketid) . "' AND did IN (" . db_build_in_array(db_escape_numarray(getAdminDepartmentAssignments())) . ")";
    $result = select_query('tbltickets', '', $where);
    $data = mysql_fetch_array($result);
    $id = $data['id'];
    if( !$id )
    {
        echo "<p>" . $aInt->lang('support', 'ticketnotfound') . "  <a href=\"javascript:history.go(-1)\">" . $aInt->lang('support', 'pleasetryagain') . "</a>.</p>";
    }
    else
    {
        $action = 'viewticket';
    }
}
if( $action == 'viewticket' || $action == 'view' )
{
    WHMCS_Session::release();
    $smartyvalues['ticketfilterdata'] = array( 'view' => $filters->getFromSession('view'), 'deptid' => $filters->getFromSession('deptid'), 'subject' => $filters->getFromSession('subject'), 'email' => $filters->getFromSession('email') );
    $aInt->template = 'viewticket';
    $smartyvalues['inticket'] = true;
    $ticket = new WHMCS_Tickets();
    $ticket->setID($id);
    $data = $ticket->getData();
    $id = $data['id'];
    $tid = $data['tid'];
    $deptid = $data['did'];
    $pauserid = $data['userid'];
    $pacontactid = $data['contactid'];
    $name = $data['name'];
    $email = $data['email'];
    $cc = $data['cc'];
    $date = $data['date'];
    $title = $data['title'];
    $message = $data['message'];
    $tstatus = $data['status'];
    $admin = $data['admin'];
    $attachment = $data['attachment'];
    $urgency = $data['urgency'];
    $lastreply = $data['lastreply'];
    $flag = $data['flag'];
    $replyingadmin = $data['replyingadmin'];
    $replyingtime = $data['replyingtime'];
    $service = $data['service'];
    $replyingtime = fromMySQLDate($replyingtime, 'time');
    $access = validateAdminTicketAccess($id);
    if( $access == 'invalidid' )
    {
        $aInt->gracefulExit($aInt->lang('support', 'ticketnotfound'));
    }
    if( $access == 'deptblocked' )
    {
        $aInt->gracefulExit($aInt->lang('support', 'deptnoaccess'));
    }
    if( $access == 'flagged' )
    {
        $aInt->gracefulExit($aInt->lang('support', 'flagnoaccess') . ": " . getAdminName($flag));
    }
    if( $access )
    {
        exit();
    }
    if( $updateticket )
    {
        check_token("WHMCS.admin.default");
        if( $updateticket == 'deptid' )
        {
            $ticket->changeDept($value);
            exit();
        }
        if( $updateticket == 'flagto' )
        {
            $ticket->setFlagTo($value);
            exit();
        }
        if( $updateticket == 'priority' )
        {
            if( !in_array($value, array( 'High', 'Medium', 'Low' )) )
            {
                exit();
            }
            update_query('tbltickets', array( 'urgency' => $value ), array( 'id' => (int) $id ));
            addTicketLog($id, "Priority changed to " . $value);
            exit();
        }
    }
    if( $sub == 'savecustomfields' )
    {
        check_token("WHMCS.admin.default");
        $customfields = getCustomFields('support', $deptid, $id, true);
        foreach( $customfields as $v )
        {
            $k = $v['id'];
            $customfieldsarray[$k] = $customfield[$k];
        }
        saveCustomFields($id, $customfieldsarray);
        $adminname = getAdminName();
        addTicketLog($id, "Custom Field Values Modified by " . $adminname);
        redir("action=viewticket&id=" . $id);
    }
    AdminRead($id);
    if( $replyingadmin && $replyingadmin != $_SESSION['adminid'] )
    {
        $result = select_query('tbladmins', '', array( 'id' => $replyingadmin ));
        $data = mysql_fetch_array($result);
        $replyingadmin = ucfirst($data['username']);
        $smartyvalues['replyingadmin'] = array( 'name' => $replyingadmin, 'time' => $replyingtime );
    }
    $clientname = $contactname = $clientGroupColour = '';
    if( $pauserid )
    {
        $clientname = strip_tags($aInt->outputClientLink($pauserid));
    }
    if( $pacontactid )
    {
        $contactname = strip_tags($aInt->outputClientLink(array( $pauserid, $pacontactid )));
    }
    if( $groupid )
    {
        $clientGroups = getClientGroups();
        $clientGroupColour = $clientGroups[$groupid]['colour'];
    }
    $staffinvolved = array(  );
    $result = select_query('tblticketreplies', "DISTINCT admin", array( 'tid' => $id ));
    while( $data = mysql_fetch_array($result) )
    {
        if( trim($data[0]) )
        {
            $staffinvolved[] = $data[0];
        }
    }
    $addons_html = run_hook('AdminAreaViewTicketPage', array( 'ticketid' => $id ));
    $smartyvalues['addons_html'] = $addons_html;
    $department = getDepartmentName($deptid);
    if( !$lastreply )
    {
        $lastreply = $date;
    }
    $date = fromMySQLDate($date, true);
    $outstatus = getStatusColour($tstatus);
    $aInt->Tabs();
    $tags = array(  );
    $result = select_query('tbltickettags', 'tag', array( 'ticketid' => $id ), 'tag', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $tags[] = $data['tag'];
    }
    $smartyvalues['tags'] = $tags;
    $tags = function_exists('json_encode') ? json_encode($tags) : '';
    $csrfToken = generate_token('plain');
    $jsheadoutput = "<script type=\"text/javascript\">\nvar ticketid = '" . $id . "';\nvar userid = '" . $pauserid . "';\nvar csrfToken = '" . $csrfToken . "';\nvar ticketTags = " . $tags . ";\nvar langdelreplysure = \"" . $_ADMINLANG['support']['delreplysure'] . "\";\nvar langdelticketsure = \"" . $_ADMINLANG['support']['delticketsure'] . "\";\nvar langdelnotesure = \"" . $_ADMINLANG['support']['delnotesure'] . "\";\nvar langloading = \"" . $_ADMINLANG['global']['loading'] . "\";\nvar langstatuschanged = \"" . $_ADMINLANG['support']['statuschanged'] . "\";\nvar langstillsubmit = \"" . $_ADMINLANG['support']['stillsubmit'] . "\";\n</script>\n<script type=\"text/javascript\" src=\"../includes/jscript/admintickets.js\"></script>";
    $aInt->addHeadOutput($jsheadoutput);
    $smartyvalues['infobox'] = $infobox;
    $smartyvalues['ticketid'] = $id;
    $smartyvalues['deptid'] = $deptid;
    $smartyvalues['tid'] = $tid;
    $smartyvalues['subject'] = $title;
    $smartyvalues['status'] = $tstatus;
    $smartyvalues['userid'] = $pauserid;
    $smartyvalues['contactid'] = $pacontactid;
    $smartyvalues['clientname'] = $clientname;
    $smartyvalues['contactname'] = $contactname;
    $smartyvalues['clientgroupcolour'] = $clientGroupColour;
    $smartyvalues['lastreply'] = getLastReplyTime($lastreply);
    $smartyvalues['priority'] = $urgency;
    $smartyvalues['flag'] = $flag;
    $smartyvalues['cc'] = $cc;
    $smartyvalues['staffinvolved'] = $staffinvolved;
    $smartyvalues['deleteperm'] = checkPermission("Delete Ticket", true);
    $result = select_query('tbladmins', 'firstname,lastname,signature', array( 'id' => $_SESSION['adminid'] ));
    $data = mysql_fetch_array($result);
    $signature = $data['signature'];
    $smartyvalues['signature'] = $signature;
    $smartyvalues['predefinedreplies'] = genPredefinedRepliesList(0);
    $smartyvalues['clientnotes'] = array(  );
    $result = select_query('tblnotes', "tblnotes.*,(SELECT CONCAT(firstname,' ',lastname) FROM tbladmins WHERE tbladmins.id=tblnotes.adminid) AS adminuser", array( 'userid' => $pauserid, 'sticky' => '1' ), 'modified', 'DESC');
    while( $data = mysql_fetch_assoc($result) )
    {
        $data['created'] = fromMySQLDate($data['created'], 1);
        $data['modified'] = fromMySQLDate($data['modified'], 1);
        $data['note'] = autoHyperLink(nl2br($data['note']));
        $smartyvalues['clientnotes'][] = $data;
    }
    $notes = array(  );
    $result = select_query('tblticketnotes', '', array( 'ticketid' => $id ), 'date', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $notes[] = array( 'id' => $data['id'], 'admin' => $data['admin'], 'date' => fromMySQLDate($data['date'], true), 'message' => ticketAutoHyperlinks($data['message']) );
    }
    $smartyvalues['notes'] = $notes;
    $smartyvalues['numnotes'] = count($notes);
    $customfields = getCustomFields('support', $deptid, $id, true);
    $smartyvalues['customfields'] = $customfields;
    $smartyvalues['numcustomfields'] = count($customfields);
    $departmentshtml = '';
    $departments = array(  );
    $result = select_query('tblticketdepartments', '', '', 'order', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $departments[] = array( 'id' => $data['id'], 'name' => $data['name'] );
        $departmentshtml .= "<option value=\"" . $data['id'] . "\"" . ($data['id'] == $deptid ? " selected" : '') . ">" . $data['name'] . "</option>";
    }
    $smartyvalues['departments'] = $departments;
    $staff = array(  );
    $result = select_query('tbladmins', 'id,firstname,lastname,supportdepts', "disabled=0 OR id='" . (int) $flag . "'", "firstname` ASC,`lastname", 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $staff[] = array( 'id' => $data['id'], 'name' => $data['firstname'] . " " . $data['lastname'] );
    }
    $smartyvalues['staff'] = $staff;
    $statuses = array(  );
    $result = select_query('tblticketstatuses', '', '', 'sortorder', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $statuses[] = array( 'title' => $data['title'], 'color' => $data['color'], 'id' => $data['id'] );
    }
    $smartyvalues['statuses'] = $statuses;
    if( $service )
    {
        switch( substr($service, 0, 1) )
        {
            case 'S':
                $result = select_query('tblhosting', "tblhosting.id,tblhosting.userid,tblhosting.regdate,tblhosting.domain,tblhosting.domainstatus,tblhosting.nextduedate,tblhosting.billingcycle,tblproducts.name,tblhosting.username,tblhosting.password,tblproducts.servertype", array( "tblhosting.id" => substr($service, 1) ), '', '', '', "tblproducts ON tblproducts.id=tblhosting.packageid");
                $data = mysql_fetch_array($result);
                $service_id = $data['id'];
                $service_userid = $data['userid'];
                $service_name = $data['name'];
                $service_domain = $data['domain'];
                $service_status = $data['domainstatus'];
                $service_regdate = $data['regdate'];
                $service_nextduedate = $data['nextduedate'];
                $service_username = $data['username'];
                $service_password = decrypt($data['password']);
                $service_servertype = $data['servertype'];
                if( $service_servertype )
                {
                    if( !isValidforPath($service_servertype) )
                    {
                        exit( "Invalid Server Module Name" );
                    }
                    include("../modules/servers/" . $service_servertype . '/' . $service_servertype . ".php");
                    if( function_exists($service_servertype . '_LoginLink') )
                    {
                        ob_start();
                        ServerLoginLink($service_id);
                        $service_loginlink = ob_get_contents();
                        ob_end_clean();
                    }
                }
                $smartyvalues['relatedproduct'] = array( 'id' => $service_id, 'name' => $service_name, 'regdate' => fromMySQLDate($service_regdate), 'domain' => $service_domain, 'nextduedate' => fromMySQLDate($service_nextduedate), 'username' => $service_username, 'password' => $service_password, 'loginlink' => $service_loginlink, 'status' => $service_status );
                break;
            case 'D':
                $result = select_query('tbldomains', '', array( 'id' => substr($service, 1) ));
                $data = mysql_fetch_array($result);
                $service_id = $data['id'];
                $service_userid = $data['userid'];
                $service_type = $data['type'];
                $service_domain = $data['domain'];
                $service_status = $data['status'];
                $service_nextduedate = $data['nextduedate'];
                $service_regperiod = $data['registrationperiod'];
                $service_registrar = $data['registrar'];
                $smartyvalues['relateddomain'] = array( 'id' => $service_id, 'domain' => $service_domain, 'nextduedate' => fromMySQLDate($service_nextduedate), 'registrar' => ucfirst($service_registrar), 'regperiod' => $service_regperiod, 'ordertype' => $service_type, 'status' => $service_status );
        }
    }
    if( $pauserid && checkPermission("List Services", true) )
    {
        $currency = getCurrency($pauserid);
        $smartyvalues['relatedservices'] = array(  );
        $totalitems = get_query_val('tblhosting', "COUNT(id)", array( 'userid' => $pauserid )) + get_query_val('tblhostingaddons', "COUNT(tblhostingaddons.id)", array( "tblhosting.userid" => $pauserid ), '', '', '', "tblhosting ON tblhosting.id=tblhostingaddons.hostingid") + get_query_val('tbldomains', "COUNT(id)", array( 'userid' => $pauserid ));
        $lefttoselect = 10;
        $result = select_query('tblhosting', "tblhosting.*,tblproducts.name", array( 'userid' => $pauserid ), "domainstatus` ASC,`id", 'DESC', '0,' . $lefttoselect, "tblproducts ON tblproducts.id=tblhosting.packageid");
        while( $data = mysql_fetch_array($result) )
        {
            $service_id = $data['id'];
            $service_name = $data['name'];
            $service_domain = $data['domain'];
            $service_firstpaymentamount = $data['firstpaymentamount'];
            $service_recurringamount = $data['amount'];
            $service_billingcycle = $data['billingcycle'];
            $service_signupdate = $data['regdate'];
            $service_nextduedate = $data['nextduedate'];
            $service_status = $data['domainstatus'];
            $service_signupdate = fromMySQLDate($service_signupdate);
            if( $service_nextduedate == '0000-00-00' )
            {
                $service_nextduedate = '-';
            }
            else
            {
                $service_nextduedate = fromMySQLDate($service_nextduedate);
            }
            if( $service_recurringamount <= 0 )
            {
                $service_amount = $service_firstpaymentamount;
            }
            else
            {
                $service_amount = $service_recurringamount;
            }
            $service_amount = formatCurrency($service_amount);
            $selected = substr($service, 0, 1) == 'S' && substr($service, 1) == $service_id ? true : false;
            $smartyvalues['relatedservices'][] = array( 'id' => $service_id, 'type' => 'product', 'name' => "<a href=\"clientsservices.php?userid=" . $pauserid . "&id=" . $service_id . "\" target=\"_blank\">" . $service_name . "</a> - <a href=\"http://" . $service_domain . "/\" target=\"_blank\">" . $service_domain . "</a>", 'product' => $service_name, 'domain' => $service_domain, 'amount' => $service_amount, 'billingcycle' => $service_billingcycle, 'regdate' => $service_signupdate, 'nextduedate' => $service_nextduedate, 'status' => $service_status, 'selected' => $selected );
        }
        $predefinedaddons = array(  );
        $result = select_query('tbladdons', '', '');
        while( $data = mysql_fetch_array($result) )
        {
            $addon_id = $data['id'];
            $addon_name = $data['name'];
            $predefinedaddons[$addon_id] = $addon_name;
        }
        $lefttoselect = 10 - count($smartyvalues['relatedservices']);
        if( 0 < $lefttoselect )
        {
            $result = select_query('tblhostingaddons', "tblhostingaddons.*,tblhostingaddons.id AS addonid,tblhostingaddons.addonid AS addonid2,tblhostingaddons.name AS addonname,tblhosting.id AS hostingid,tblhosting.domain,tblproducts.name", array( "tblhosting.userid" => $pauserid ), "status` ASC,`tblhosting`.`id", 'DESC', '0,' . $lefttoselect, "tblhosting ON tblhosting.id=tblhostingaddons.hostingid INNER JOIN tblproducts ON tblproducts.id=tblhosting.packageid");
            while( $data = mysql_fetch_array($result) )
            {
                $service_id = $data['id'];
                $hostingid = $data['hostingid'];
                $service_addonid = $data['addonid2'];
                $service_name = $data['name'];
                $service_addon = $data['addonname'];
                $service_domain = $data['domain'];
                $service_recurringamount = $data['recurring'];
                $service_billingcycle = $data['billingcycle'];
                $service_signupdate = $data['regdate'];
                $service_nextduedate = $data['nextduedate'];
                $service_status = $data['status'];
                if( !$service_addon )
                {
                    $service_addon = $predefinedaddons[$service_addonid];
                }
                $service_signupdate = fromMySQLDate($service_signupdate);
                if( $service_nextduedate == '0000-00-00' )
                {
                    $service_nextduedate = '-';
                }
                else
                {
                    $service_nextduedate = fromMySQLDate($service_nextduedate);
                }
                $service_amount = formatCurrency($service_recurringamount);
                $selected = substr($service, 0, 1) == 'A' && substr($service, 1) == $service_id ? true : false;
                $smartyvalues['relatedservices'][] = array( 'id' => $service_id, 'type' => 'addon', 'serviceid' => $hostingid, 'name' => $aInt->lang('orders', 'addon') . " - " . $service_addon . "<br /><a href=\"clientsservices.php?userid=" . $pauserid . "&id=" . $hostingid . "&aid=" . $service_id . "\" target=\"_blank\">" . $service_name . "</a> - <a href=\"http://" . $service_domain . "/\" target=\"_blank\">" . $service_domain . "</a>", 'product' => $service_addon, 'domain' => $service_domain, 'amount' => $service_amount, 'billingcycle' => $service_billingcycle, 'regdate' => $service_signupdate, 'nextduedate' => $service_nextduedate, 'status' => $service_status, 'selected' => $selected );
            }
        }
        $lefttoselect = 10 - count($smartyvalues['relatedservices']);
        if( 0 < $lefttoselect )
        {
            $result = select_query('tbldomains', '', array( 'userid' => $pauserid ), "status` ASC,`id", 'DESC', '0,' . $lefttoselect);
            while( $data = mysql_fetch_array($result) )
            {
                $service_id = $data['id'];
                $service_domain = $data['domain'];
                $service_firstpaymentamount = $data['firstpaymentamount'];
                $service_recurringamount = $data['recurringamount'];
                $service_registrationperiod = $data['registrationperiod'] . " Year(s)";
                $service_signupdate = $data['registrationdate'];
                $service_nextduedate = $data['nextduedate'];
                $service_status = $data['status'];
                $service_signupdate = fromMySQLDate($service_signupdate);
                if( $service_nextduedate == '0000-00-00' )
                {
                    $service_nextduedate = '-';
                }
                else
                {
                    $service_nextduedate = fromMySQLDate($service_nextduedate);
                }
                if( $service_recurringamount <= 0 )
                {
                    $service_amount = $service_firstpaymentamount;
                }
                else
                {
                    $service_amount = $service_recurringamount;
                }
                $service_amount = formatCurrency($service_amount);
                $selected = substr($service, 0, 1) == 'D' && substr($service, 1) == $service_id ? true : false;
                $smartyvalues['relatedservices'][] = array( 'id' => $service_id, 'type' => 'domain', 'name' => "<a href=\"clientsdomains.php?userid=" . $pauserid . "&id=" . $service_id . "\" target=\"_blank\">" . $aInt->lang('fields', 'domain') . "</a> - <a href=\"http://" . $service_domain . "/\" target=\"_blank\">" . $service_domain . "</a>", 'product' => $aInt->lang('fields', 'domain'), 'domain' => $service_domain, 'amount' => $service_amount, 'billingcycle' => $service_registrationperiod, 'regdate' => $service_signupdate, 'nextduedate' => $service_nextduedate, 'status' => $service_status, 'selected' => $selected );
            }
        }
        if( count($smartyvalues['relatedservices']) < $totalitems )
        {
            $smartyvalues['relatedservicesexpand'] = true;
        }
    }
    $jscode .= "function insertKBLink(url) {\n    \$(\"#replymessage\").addToReply(url);\n}";
    $jquerycode = "(function() {\n    var fieldSelection = {\n        addToReply: function() {\n            var e = this.jquery ? this[0] : this;\n            var text = arguments[0] || '';\n            return (\n                ('selectionStart' in e && function() {\n                    if (e.value==\"\\n\\n" . str_replace("\r\n", "\\n", $signature) . "\") {\n                        e.selectionStart=0;\n                        e.selectionEnd=0;\n                    }\n                    e.value = e.value.substr(0, e.selectionStart) + text + e.value.substr(e.selectionEnd, e.value.length);\n                    e.focus();\n                    return this;\n                }) ||\n                (document.selection && function() {\n                    e.focus();\n                    document.selection.createRange().text = text;\n                    return this;\n                }) ||\n                function() {\n                    e.value += text;\n                    return this;\n                }\n            )();\n        }\n    };\n    jQuery.each(fieldSelection, function(i) { jQuery.fn[i] = this; });\n    })();";
    $aInt->jquerycode = $jquerycode;
    $replies = array(  );
    $result = select_query('tbltickets', 'userid,contactid,name,email,date,title,message,admin,attachment', array( 'id' => $id ));
    $data = mysql_fetch_array($result);
    $userid = $data['userid'];
    $contactid = $data['contactid'];
    $name = $data['name'];
    $email = $data['email'];
    $date = $data['date'];
    $title = $data['title'];
    $message = $data['message'];
    $admin = $data['admin'];
    $attachment = $data['attachment'];
    $friendlydate = substr($date, 0, 10) == date('Y-m-d') ? '' : substr($date, 0, 4) == date('Y') ? date("l jS F", strtotime($date)) : date("l jS F Y", strtotime($date));
    $friendlytime = date("H:i", strtotime($date));
    $date = fromMySQLDate($date, true);
    $message = ticketMessageFormat($message);
    if( $userid )
    {
        $name = $aInt->outputClientLink(array( $userid, $contactid ), '', '', '', '', true);
    }
    $attachments = getTicketAttachmentsInfo($id, '', $attachment);
    $replies[] = array( 'id' => 0, 'admin' => $admin, 'userid' => $userid, 'contactid' => $contactid, 'clientname' => $name, 'clientemail' => $email, 'date' => $date, 'friendlydate' => $friendlydate, 'friendlytime' => $friendlytime, 'message' => $message, 'attachments' => $attachments, 'numattachments' => count($attachments) );
    $result = select_query('tblticketreplies', '', array( 'tid' => $id ), 'date', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $replyid = $data['id'];
        $userid = $data['userid'];
        $contactid = $data['contactid'];
        $name = $data['name'];
        $email = $data['email'];
        $date = $data['date'];
        $message = $data['message'];
        $attachment = $data['attachment'];
        $admin = $data['admin'];
        $rating = $data['rating'];
        $friendlydate = substr($date, 0, 10) == date('Y-m-d') ? '' : substr($date, 0, 4) == date('Y') ? date("l jS F", strtotime($date)) : date("l jS F Y", strtotime($date));
        $friendlytime = date("H:i", strtotime($date));
        $date = fromMySQLDate($date, true);
        $message = ticketMessageFormat($message);
        if( $userid )
        {
            $name = $aInt->outputClientLink(array( $userid, $contactid ), '', '', '', '', true);
        }
        $attachments = getTicketAttachmentsInfo($id, $replyid, $attachment);
        $ratingstars = '';
        if( $admin && $rating )
        {
            for( $i = 1; $i <= 5; $i++ )
            {
                $ratingstars .= $i <= $rating ? "<img src=\"../images/rating_pos.png\" align=\"absmiddle\">" : "<img src=\"../images/rating_neg.png\" align=\"absmiddle\">";
            }
        }
        $replies[] = array( 'id' => $replyid, 'admin' => $admin, 'userid' => $userid, 'contactid' => $contactid, 'clientname' => $name, 'clientemail' => $email, 'date' => $date, 'friendlydate' => $friendlydate, 'friendlytime' => $friendlytime, 'message' => $message, 'attachments' => $attachments, 'numattachments' => count($attachments), 'rating' => $ratingstars );
    }
    if( $CONFIG['SupportTicketOrder'] == 'DESC' )
    {
        krsort($replies);
    }
    $smartyvalues['replies'] = $replies;
    $smartyvalues['repliescount'] = count($replies);
    $smartyvalues['thumbnails'] = $CONFIG['AttachmentThumbnails'] ? true : false;
    $splitticketdialog = $aInt->jqueryDialog('splitticket', $aInt->lang('support', 'splitticketdialogtitle'), "<p>" . $aInt->lang('support', 'splitticketdialoginfo') . "</p><table><tr><td align=\"right\" width=\"120\">" . $aInt->lang('support', 'department') . ":</td><td><select id=\"splitdeptidx\">" . $departmentshtml . "</select></td></tr><tr><td align=\"right\">" . $aInt->lang('support', 'splitticketdialognewticketname') . ":</td><td><input type=\"text\" id=\"splitsubjectx\" size=\"35\" value=\"" . $title . "\" /></td></tr><tr><td align=\"right\">" . $aInt->lang('support', 'priority') . ":</td><td><select id=\"splitpriorityx\"><option value=\"High\"" . ($urgency == 'High' ? " selected" : '') . ">High</option><option value=\"Medium\"" . ($urgency == 'Medium' ? " selected" : '') . ">Medium</option><option value=\"Low\"" . ($urgency == 'Low' ? " selected" : '') . ">Low</option></select></td></tr><tr><td align=\"right\">" . $aInt->lang('support', 'splitticketdialognotifyclient') . ":</td><td><label><input type=\"checkbox\" id=\"splitnotifyclientx\" /> " . $aInt->lang('support', 'splitticketdialognotifyclientinfo') . "</label></td></tr></table>", array( $aInt->lang('global', 'submit') => "\$('#splitdeptid').val(\$('#splitdeptidx').val());\$('#splitsubject').val(\$('#splitsubjectx').val());\$('#splitpriority').val(\$('#splitpriorityx').val());\$('#splitnotifyclient').val(\$('#splitnotifyclientx').attr('checked'));\$('#ticketreplies').submit();", $aInt->lang('supportreq', 'cancel') => '' ), '', '400', '');
    $smartyvalues['splitticketdialog'] = $splitticketdialog;
}
else
{
    if( $action == 'open' )
    {
        $result = select_query('tbladmins', 'signature', array( 'id' => $_SESSION['adminid'] ));
        $data = mysql_fetch_array($result);
        $signature = $data['signature'];
        if( isset($validate) && $validate instanceof WHMCS_Validate && $validate->hasErrors() )
        {
            infoBox($aInt->lang('global', 'validationerror'), $validate->getHTMLErrorOutput(), 'error');
            echo $infobox;
        }
        $jquerycode = "(function() {\n    var fieldSelection = {\n        addToReply: function() {\n            var e = this.jquery ? this[0] : this;\n            var text = arguments[0] || '';\n            return (\n                ('selectionStart' in e && function() {\n                    if (e.value==\"\\n\\n" . str_replace("\r\n", "\\n", $signature) . "\") {\n                        e.selectionStart=0;\n                        e.selectionEnd=0;\n                    }\n                    e.value = e.value.substr(0, e.selectionStart) + text + e.value.substr(e.selectionEnd, e.value.length);\n                    e.focus();\n                    return this;\n                }) ||\n                (document.selection && function() {\n                    e.focus();\n                    document.selection.createRange().text = text;\n                    return this;\n                }) ||\n                function() {\n                    e.value += text;\n                    return this;\n                }\n            )();\n        }\n    };\n    jQuery.each(fieldSelection, function(i) { jQuery.fn[i] = this; });\n    })();\n\$(\"#addfileupload\").click(function () {\n    \$(\"#fileuploads\").append(\"<input type=\\\"file\\\" name=\\\"attachments[]\\\" size=\\\"85\\\"><br />\");\n    return false;\n});\n\$(\"#clientsearchval\").keyup(function () {\n    var ticketuseridsearchlength = \$(\"#clientsearchval\").val().length;\n    if (ticketuseridsearchlength>2) {\n    \$.post(\"search.php\", { ticketclientsearch: 1, value: \$(\"#clientsearchval\").val(), token: \"" . generate_token('plain') . "\" },\n        function(data){\n            if (data) {\n                \$(\"#ticketclientsearchresults\").html(data);\n                \$(\"#ticketclientsearchresults\").slideDown(\"slow\");\n                \$(\"#clientsearchcancel\").fadeIn();\n            }\n        });\n    }\n});\n\$(\"#clientsearchcancel\").click(function () {\n    \$(\"#ticketclientsearchresults\").slideUp(\"slow\");\n    \$(\"#clientsearchcancel\").fadeOut();\n});\n\$(\"#predefq\").keyup(function () {\n    var intellisearchlength = \$(\"#predefq\").val().length;\n    if (intellisearchlength>2) {\n    \$.post(\"supporttickets.php\", { action: \"loadpredefinedreplies\", predefq: \$(\"#predefq\").val(), token: \"" . generate_token('plain') . "\" },\n        function(data){\n            \$(\"#prerepliescontent\").html(data);\n        });\n    }\n});\n";
        $aInt->jquerycode = $jquerycode;
        $jscode .= "function insertKBLink(url) {\n    \$(\"#replymessage\").addToReply(url);\n}\nfunction selectpredefcat(catid) {\n    \$.post(\"supporttickets.php\", { action: \"loadpredefinedreplies\", cat: catid, token: \"" . generate_token('plain') . "\" },\n    function(data){\n        \$(\"#prerepliescontent\").html(data);\n    });\n}\nfunction loadpredef(catid) {\n    \$(\"#prerepliescontainer\").slideToggle();\n    \$(\"#prerepliescontent\").html('<img src=\"images/loading.gif\" align=\"top\" /> " . $aInt->lang('global', 'loading') . "');\n    \$.post(\"supporttickets.php\", { action: \"loadpredefinedreplies\", cat: catid, token: \"" . generate_token('plain') . "\" },\n    function(data){\n        \$(\"#prerepliescontent\").html(data);\n    });\n}\nfunction selectpredefreply(artid) {\n    \$.post(\"supporttickets.php\", { action: \"getpredefinedreply\", id: artid, token: \"" . generate_token('plain') . "\" },\n    function(data){\n        \$(\"#replymessage\").addToReply(data);\n    });\n    \$(\"#prerepliescontainer\").slideToggle();\n}\nfunction searchselectclient(userid,name,email) {\n    \$(\"#clientsearchval\").val(\"\");\n    \$(\"#clientinput\").val(userid);\n    \$(\"#name\").val(name)\n        .prop(\"disabled\", true);\n    \$(\"#email\").val(email)\n        .prop(\"disabled\", true);\n    \$(\"#ticketclientsearchresults\").slideUp(\"slow\");\n    \$(\"#clientsearchcancel\").fadeOut();\n    \$.post(\"supporttickets.php\", { action: \"getcontacts\", userid: userid, token: \"" . generate_token('plain') . "\" },\n    function(data){\n        if (data) {\n            \$(\"#contacthtml\").html(data);\n            \$(\"#contactrow\").show();\n        } else {\n            \$(\"#contactrow\").hide();\n        }\n    });\n}\n";
        if( $userid )
        {
            $result = select_query('tblclients', 'id,firstname,lastname,companyname,email', array( 'id' => $userid ));
            $data = mysql_fetch_array($result);
            $client = $data['id'];
            if( $client )
            {
                $name = $data['firstname'] . " " . $data['lastname'];
                if( $data['companyname'] )
                {
                    $name .= " (" . $data['companyname'] . ")";
                }
                $email = $data['email'];
            }
        }
        $contactsdata = '';
        if( $client )
        {
            $contactsdata = getTicketContacts($client);
        }
        echo "\n<form method=\"post\" action=\"";
        echo $whmcs->getPhpSelf();
        echo "?action=openticket\" enctype=\"multipart/form-data\">\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">";
        echo $aInt->lang('emails', 'to');
        echo "</td><td class=\"fieldarea\"><table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\"><tr><td width=\"500\"><input type=\"hidden\" name=\"client\" id=\"clientinput\" value=\"";
        echo $client;
        echo "\" /><input type=\"text\" name=\"name\" id=\"name\" size=\"40\" value=\"";
        echo $name;
        echo "\"></td><td>";
        echo $aInt->lang('clients', 'search');
        echo ": <input type=\"text\" id=\"clientsearchval\" size=\"15\" /> <img src=\"images/icons/delete.png\" alt=\"Cancel\" class=\"absmiddle\" id=\"clientsearchcancel\" height=\"16\" width=\"16\"><br /><div id=\"ticketclientsearchresults\"></div></td></tr></table></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'email');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"email\" id=\"email\" size=\"50\" value=\"";
        echo $email;
        echo "\"> <label><input type=\"checkbox\" name=\"sendemail\" checked /> ";
        echo $aInt->lang('global', 'sendemail');
        echo "</label></td></tr>\n<tr id=\"contactrow\"";
        if( !$contactsdata )
        {
            echo " style=\"display:none;\"";
        }
        echo "><td class=\"fieldlabel\">";
        echo $aInt->lang('clientsummary', 'contacts');
        echo "</td><td class=\"fieldarea\" id=\"contacthtml\">";
        echo $contactsdata;
        echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('support', 'ccrecipients');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"ccemail\" value=\"";
        echo $cc;
        echo "\" size=\"50\"> (";
        echo $aInt->lang('transactions', 'commaseparated');
        echo ")</td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('support', 'department');
        echo "</td><td class=\"fieldarea\"><select name=\"deptid\">";
        $result = select_query('tbladmins', '', array( 'id' => $_SESSION['adminid'] ));
        $data = mysql_fetch_array($result);
        $supportdepts = $data['supportdepts'];
        $supportdepts = explode(',', $supportdepts);
        $result = select_query('tblticketdepartments', '', '', 'order', 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $id = $data['id'];
            $name = $data['name'];
            if( in_array($id, $supportdepts) )
            {
                echo "<option value=\"" . $id . "\"";
                if( $id == $department )
                {
                    echo " selected";
                }
                echo ">" . $name . "</option>";
            }
        }
        echo "</select></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('fields', 'subject');
        echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"subject\" size=\"75\" value=\"";
        echo $subject;
        echo "\"></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('support', 'priority');
        echo "</td><td class=\"fieldarea\"><select name=\"priority\"><option>";
        echo $aInt->lang('status', 'high');
        echo "<option selected>";
        echo $aInt->lang('status', 'medium');
        echo "<option>";
        echo $aInt->lang('status', 'low');
        echo "</select></td></tr>\n</table>\n<img src=\"images/spacer.gif\" height=\"8\" width=\"1\"><br>\n<textarea name=\"message\" id=\"replymessage\" rows=20 style=\"width:100%\">";
        if( $message )
        {
            echo $message;
        }
        else
        {
            if( $signature )
            {
                echo "\n" . "\n" . "\n" . $signature;
            }
        }
        echo "</textarea><br>\n<img src=\"images/spacer.gif\" height=\"8\" width=\"1\"><br>\n<div id=\"insertlinks\" style=\"border:1px solid #DFDCCE;background-color:#F7F7F2;padding:5px;text-align:left;\">\n<div align=\"center\"><a href=\"#\" onClick=\"window.open('supportticketskbarticle.php','kbartwnd','width=500,height=400,scrollbars=yes');return false\">";
        echo $aInt->lang('support', 'insertkblink');
        echo "</a>\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n<a href=\"#\" onclick=\"loadpredef('0');return false\">";
        echo $aInt->lang('support', 'insertpredef');
        echo "</a></div>\n</div>\n<img src=\"images/spacer.gif\" height=\"8\" width=\"1\"><br>\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">";
        echo $aInt->lang('support', 'attachments');
        echo "</td><td class=\"fieldarea\"><input type=\"file\" name=\"attachments[]\" size=\"85\"> <a href=\"#\" id=\"addfileupload\"><img src=\"images/icons/add.png\" align=\"absmiddle\" border=\"0\" /> ";
        echo $aInt->lang('support', 'addmore');
        echo "</a><br /><div id=\"fileuploads\"></div></td></tr>\n</table>\n<div id=\"prerepliescontainer\" style=\"display:none;\">\n    <img src=\"images/spacer.gif\" height=\"8\" width=\"1\" />\n    <br />\n    <div style=\"border:1px solid #DFDCCE;background-color:#F7F7F2;padding:5px;text-align:left;\">\n        <div style=\"float:right;\">Search: <input type=\"text\" id=\"predefq\" size=\"25\" /></div>\n        <div id=\"prerepliescontent\"></div>\n    </div>\n</div>\n<img src=\"images/spacer.gif\" height=\"8\" width=\"1\"><br>\n<div align=\"center\"><input type=\"submit\" value=\"";
        echo $aInt->lang('clientsummary', 'openticket');
        echo "\" class=\"button\"></div>\n</form>\n\n";
    }
}
$content = ob_get_contents();
ob_end_clean();
$aInt->jscode = $jscode;
$aInt->content = $content;
$aInt->templatevars = $smartyvalues;
$aInt->display();