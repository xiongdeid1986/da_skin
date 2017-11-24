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
if( !$limitstart )
{
    $limitstart = 0;
}
if( !$limitnum )
{
    $limitnum = 25;
}
$filters = array(  );
if( $deptid )
{
    $filters[] = "did IN (" . mysql_real_escape_string($deptid) . ")";
}
if( $clientid )
{
    $filters[] = "userid='" . mysql_real_escape_string($clientid) . "'";
}
if( $email )
{
    $filters[] = "(email='" . mysql_real_escape_string($email) . "' OR userid=(SELECT id FROM tblclients WHERE email='" . mysql_real_escape_string($email) . "'))";
}
if( $status == "Awaiting Reply" )
{
    $statusfilter = '';
    $result = select_query('tblticketstatuses', 'title', array( 'showawaiting' => '1' ));
    while( $data = mysql_fetch_array($result) )
    {
        $statusfilter .= "'" . $data[0] . "',";
    }
    $statusfilter = substr($statusfilter, 0, 0 - 1);
    $filters[] = "tbltickets.status IN (" . $statusfilter . ")";
}
else
{
    if( $status == "All Active Tickets" )
    {
        $statusfilter = '';
        $result = select_query('tblticketstatuses', 'title', array( 'showactive' => '1' ));
        while( $data = mysql_fetch_array($result) )
        {
            $statusfilter .= "'" . $data[0] . "',";
        }
        $statusfilter = substr($statusfilter, 0, 0 - 1);
        $filters[] = "tbltickets.status IN (" . $statusfilter . ")";
    }
    else
    {
        if( $status == "My Flagged Tickets" )
        {
            $statusfilter = '';
            $result = select_query('tblticketstatuses', 'title', array( 'showactive' => '1' ));
            while( $data = mysql_fetch_array($result) )
            {
                $statusfilter .= "'" . $data[0] . "',";
            }
            $statusfilter = substr($statusfilter, 0, 0 - 1);
            $filters[] = "tbltickets.status IN (" . $statusfilter . ") AND flag='" . $_SESSION['adminid'] . "'";
        }
        else
        {
            if( $status )
            {
                $filters[] = "status='" . mysql_real_escape_string($status) . "'";
            }
        }
    }
}
if( $subject )
{
    $filters[] = "title LIKE '%" . mysql_real_escape_string($subject) . "%'";
}
if( !$ignore_dept_assignments )
{
    $result = select_query('tbladmins', 'supportdepts', array( 'id' => $_SESSION['adminid'] ));
    $data = mysql_fetch_array($result);
    $supportdepts = $data[0];
    $supportdepts = explode(',', $supportdepts);
    $deptids = array(  );
    foreach( $supportdepts as $id )
    {
        if( trim($id) )
        {
            $deptids[] = trim($id);
        }
    }
    if( count($deptids) )
    {
        $filters[] = "did IN (" . db_build_in_array($deptids) . ")";
    }
}
$where = implode(" AND ", $filters);
$result = select_query('tbltickets', "COUNT(id)", $where);
$data = mysql_fetch_array($result);
$totalresults = $data[0];
$apiresults = array( 'result' => 'success', 'totalresults' => $totalresults, 'startnumber' => $limitstart );
$result = select_query('tbltickets', '', $where, 'lastreply', 'DESC', $limitstart . ',' . $limitnum);
$apiresults['numreturned'] = mysql_num_rows($result);
while( $data = mysql_fetch_array($result) )
{
    $id = $data['id'];
    $tid = $data['tid'];
    $deptid = $data['did'];
    $userid = $data['userid'];
    $name = $data['name'];
    $email = $data['email'];
    $cc = $data['cc'];
    $c = $data['c'];
    $date = $data['date'];
    $subject = $data['title'];
    $message = $data['message'];
    $status = $data['status'];
    $priority = $data['urgency'];
    $admin = $data['admin'];
    $attachment = $data['attachment'];
    $lastreply = $data['lastreply'];
    $flag = $data['flag'];
    $service = $data['service'];
    if( $userid )
    {
        $result2 = select_query('tblclients', '', array( 'id' => $userid ));
        $data = mysql_fetch_array($result2);
        $name = $data['firstname'] . " " . $data['lastname'];
        if( $data['companyname'] )
        {
            $name .= " (" . $data['companyname'] . ")";
        }
        $email = $data['email'];
    }
    $apiresults['tickets']['ticket'][] = array( 'id' => $id, 'tid' => $tid, 'deptid' => $deptid, 'userid' => $userid, 'name' => $name, 'email' => $email, 'cc' => $cc, 'c' => $c, 'date' => $date, 'subject' => $subject, 'status' => $status, 'priority' => $priority, 'admin' => $admin, 'attachment' => $attachment, 'lastreply' => $lastreply, 'flag' => $flag, 'service' => $service );
}
$responsetype = 'xml';