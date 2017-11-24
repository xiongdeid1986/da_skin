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
define('CLIENTAREA', true);
require("init.php");
require("includes/ticketfunctions.php");
$pagetitle = $_LANG['supportticketspagetitle'];
$breadcrumbnav = "<a href=\"index.php\">" . $_LANG['globalsystemname'] . "</a> > <a href=\"clientarea.php\">" . $_LANG['clientareatitle'] . "</a> > <a href=\"supporttickets.php\">" . $_LANG['supportticketspagetitle'] . "</a>";
$templatefile = 'supportticketslist';
$pageicon = "images/supporttickets_big.gif";
initialiseClientArea($pagetitle, $pageicon, $breadcrumbnav);
if( isset($_SESSION['uid']) )
{
    checkContactPermission('tickets');
    $usingsupportmodule = false;
    if( $CONFIG['SupportModule'] )
    {
        if( !isValidforPath($CONFIG['SupportModule']) )
        {
            exit( "Invalid Support Module" );
        }
        $supportmodulepath = 'modules/support/' . $CONFIG['SupportModule'] . "/supporttickets.php";
        if( file_exists($supportmodulepath) )
        {
            $usingsupportmodule = true;
            $templatefile = '';
            require($supportmodulepath);
            outputClientArea($templatefile);
            exit();
        }
    }
    $result = select_query('tbltickets', "COUNT(id)", "userid='" . mysql_real_escape_string($_SESSION['uid']) . "' AND status!='Closed'");
    $data = mysql_fetch_array($result);
    $smartyvalues['numopentickets'] = $data[0];
    if( $searchterm = $whmcs->get_req_var('searchterm') )
    {
        check_token();
        $smartyvalues['q'] = $searchterm;
        $smartyvalues['searchterm'] = $smartyvalues['q'];
        $searchterm = mysql_real_escape_string(trim($searchterm));
        $where = "tbltickets.userid='" . mysql_real_escape_string($_SESSION['uid']) . "' AND (tbltickets.tid='" . $searchterm . "' OR (tbltickets.title LIKE '%" . $searchterm . "%' OR tbltickets.message LIKE '%" . $searchterm . "%' OR tblticketreplies.message LIKE '%" . $searchterm . "%'))";
        $result = full_query("SELECT COUNT(DISTINCT tbltickets.id) FROM tbltickets LEFT JOIN tblticketreplies ON tbltickets.id = tblticketreplies.tid WHERE " . $where);
        $data = mysql_fetch_array($result);
        $numtickets = $data[0];
        $smartyvalues['numtickets'] = $numtickets;
        list($orderby, $sort, $limit) = clientAreaTableInit('tickets', 'lastreply', 'DESC', $numtickets);
        $smartyvalues['orderby'] = $orderby;
        $smartyvalues['sort'] = strtolower($sort);
        if( $orderby == 'date' )
        {
            $orderby = "tbltickets.date";
        }
        else
        {
            if( $orderby == 'dept' )
            {
                $orderby = 'did';
            }
            else
            {
                if( $orderby == 'subject' )
                {
                    $orderby = 'title';
                }
                else
                {
                    if( $orderby == 'status' )
                    {
                        $orderby = 'status';
                    }
                    else
                    {
                        if( $orderby == 'urgency' )
                        {
                            $orderby = 'urgency';
                        }
                        else
                        {
                            if( $orderby == 'priority' )
                            {
                                $orderby = 'urgency';
                            }
                            else
                            {
                                $orderby = 'lastreply';
                            }
                        }
                    }
                }
            }
        }
        if( !in_array($sort, array( 'ASC', 'DESC' )) )
        {
            $sort = 'ASC';
        }
        if( strpos($limit, ',') )
        {
            $limit = explode(',', $limit);
            $limit = (int) $limit[0] . ',' . (int) $limit[1];
        }
        else
        {
            $limit = (int) $limit;
        }
        $tickets = array(  );
        $result = full_query("SELECT DISTINCT tbltickets.id FROM tbltickets LEFT JOIN tblticketreplies ON tbltickets.id = tblticketreplies.tid WHERE " . $where . " ORDER BY " . $orderby . " " . $sort . " LIMIT " . $limit);
        while( $data = mysql_fetch_array($result) )
        {
            $id = $data['id'];
            $result2 = select_query('tbltickets', '', array( 'userid' => $_SESSION['uid'], 'id' => $id ));
            $data = mysql_fetch_array($result2);
            $tid = $data['tid'];
            $c = $data['c'];
            $deptid = $data['did'];
            $date = $data['date'];
            $date = fromMySQLDate($date, 1, 1);
            $subject = $data['title'];
            $tstatus = $data['status'];
            $urgency = $data['urgency'];
            $lastreply = $data['lastreply'];
            $lastreply = fromMySQLDate($lastreply, 1, 1);
            $clientunread = $data['clientunread'];
            $tstatus = getStatusColour($tstatus);
            $dept = getDepartmentName($deptid);
            $urgency = $_LANG['supportticketsticketurgency' . strtolower($urgency)];
            $tickets[] = array( 'id' => $id, 'tid' => $tid, 'c' => $c, 'date' => $date, 'department' => $dept, 'subject' => $subject, 'status' => $tstatus, 'urgency' => $urgency, 'lastreply' => $lastreply, 'unread' => $clientunread );
        }
    }
    else
    {
        $result = select_query('tbltickets', "COUNT(id)", array( 'userid' => $_SESSION['uid'] ));
        $data = mysql_fetch_array($result);
        $numtickets = $data[0];
        $smartyvalues['numtickets'] = $numtickets;
        list($orderby, $sort, $limit) = clientAreaTableInit('tickets', 'lastreply', 'DESC', $numtickets);
        $smartyvalues['orderby'] = $orderby;
        $smartyvalues['sort'] = strtolower($sort);
        if( $orderby == 'date' )
        {
            $orderby = 'date';
        }
        else
        {
            if( $orderby == 'dept' )
            {
                $orderby = 'deptname';
            }
            else
            {
                if( $orderby == 'subject' )
                {
                    $orderby = 'title';
                }
                else
                {
                    if( $orderby == 'status' )
                    {
                        $orderby = 'status';
                    }
                    else
                    {
                        if( $orderby == 'urgency' )
                        {
                            $orderby = 'urgency';
                        }
                        else
                        {
                            if( $orderby == 'priority' )
                            {
                                $orderby = 'urgency';
                            }
                            else
                            {
                                $orderby = 'lastreply';
                            }
                        }
                    }
                }
            }
        }
        $tickets = array(  );
        $result = select_query('tbltickets', "tbltickets.*,tblticketdepartments.name AS deptname", array( 'userid' => $_SESSION['uid'] ), $orderby, $sort, $limit, " tblticketdepartments ON tblticketdepartments.id=tbltickets.did");
        while( $data = mysql_fetch_array($result) )
        {
            $id = $data['id'];
            $tid = $data['tid'];
            $c = $data['c'];
            $deptid = $data['did'];
            $date = $data['date'];
            $date = fromMySQLDate($date, 1, 1);
            $subject = $data['title'];
            $tstatus = $data['status'];
            $urgency = $data['urgency'];
            $lastreply = $data['lastreply'];
            $lastreply = fromMySQLDate($lastreply, 1, 1);
            $clientunread = $data['clientunread'];
            $tstatus = getStatusColour($tstatus);
            $dept = getDepartmentName($deptid);
            $urgency = $_LANG['supportticketsticketurgency' . strtolower($urgency)];
            $tickets[] = array( 'id' => $id, 'tid' => $tid, 'c' => $c, 'date' => $date, 'department' => $dept, 'subject' => $subject, 'status' => $tstatus, 'urgency' => $urgency, 'lastreply' => $lastreply, 'unread' => $clientunread );
        }
    }
    $smarty->assign('tickets', $tickets);
    $smartyvalues = array_merge($smartyvalues, clientAreaTablePageNav($numtickets));
}
else
{
    $goto = 'supporttickets';
    include("login.php");
}
outputClientArea($templatefile);