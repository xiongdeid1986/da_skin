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
if( !function_exists('AddReply') )
{
    require(ROOTDIR . "/includes/ticketfunctions.php");
}
if( $ticketnum )
{
    $result = select_query('tbltickets', '', array( 'tid' => $ticketnum ));
}
else
{
    $result = select_query('tbltickets', '', array( 'id' => $ticketid ));
}
$data = mysql_fetch_array($result);
$id = $data['id'];
$tid = $data['tid'];
$deptid = $data['did'];
$userid = $data['userid'];
$contactID = $data['contactid'];
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
$message = strip_tags($message);
if( !$id )
{
    $apiresults = array( 'result' => 'error', 'message' => "Ticket ID Not Found" );
}
else
{
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
        if( $contactID )
        {
            $contactData = get_query_vals('tblcontacts', '', array( 'id' => $contactID ));
            $contactName = $contactData['firstname'] . " " . $contactData['lastname'];
            if( $contactData['companyname'] )
            {
                $contactName .= " (" . $contactData['companyname'] . ")";
            }
            $contactEmail = $contactData['email'];
        }
    }
    $apiresults = array( 'result' => 'success', 'ticketid' => $id, 'tid' => $tid, 'c' => $c, 'deptid' => $deptid, 'deptname' => getDepartmentName($deptid), 'userid' => $userid, 'contactid' => $contactID, 'name' => $name, 'email' => $email, 'cc' => $cc, 'date' => $date, 'subject' => $subject, 'status' => $status, 'priority' => $priority, 'admin' => $admin, 'lastreply' => $lastreply, 'flag' => $flag, 'service' => $service );
    $first_reply = array( 'userid' => $userid, 'contactid' => $contactID, 'name' => isset($contactName) ? $contactName : $name, 'email' => isset($contactEmail) ? $contactEmail : $email, 'date' => $date, 'message' => $message, 'attachment' => $attachment, 'admin' => $admin );
    $sortorder = $_REQUEST['repliessort'] ? $_REQUEST['repliessort'] : 'ASC';
    if( $sortorder == 'ASC' )
    {
        $apiresults['replies']['reply'][] = $first_reply;
    }
    $result = select_query('tblticketreplies', '', array( 'tid' => $id ), 'id', $sortorder);
    while( $data = mysql_fetch_array($result) )
    {
        $userid = $data['userid'];
        $contactID = $data['contactid'];
        $name = $data['name'];
        $email = $data['email'];
        $date = $data['date'];
        $message = $data['message'];
        $attachment = $data['attachment'];
        $admin = $data['admin'];
        $rating = $data['rating'];
        $message = strip_tags($message);
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
            if( $contactID )
            {
                $contactData = get_query_vals('tblcontacts', '', array( 'id' => $contactID ));
                $name = $contactData['firstname'] . " " . $contactData['lastname'];
                if( $contactData['companyname'] )
                {
                    $name .= " (" . $contactData['companyname'] . ")";
                }
                $email = $contactData['email'];
            }
        }
        $apiresults['replies']['reply'][] = array( 'userid' => $userid, 'contactid' => $contactID, 'name' => $name, 'email' => $email, 'date' => $date, 'message' => $message, 'attachment' => $attachment, 'admin' => $admin, 'rating' => $rating );
    }
    if( $sortorder != 'ASC' )
    {
        $apiresults['replies']['reply'][] = $first_reply;
    }
    $apiresults['notes'] = '';
    $result = select_query('tblticketnotes', '', array( 'ticketid' => $id ), 'id', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $noteid = $data['id'];
        $admin = $data['admin'];
        $date = $data['date'];
        $message = $data['message'];
        $apiresults['notes']['note'][] = array( 'noteid' => $noteid, 'date' => $date, 'message' => $message, 'admin' => $admin );
    }
    $responsetype = 'xml';
}