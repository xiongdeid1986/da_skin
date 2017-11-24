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
if( !function_exists('closeTicket') )
{
    require(ROOTDIR . "/includes/ticketfunctions.php");
}
if( !function_exists('migrateCustomFields') )
{
    require(ROOTDIR . "/includes/customfieldfunctions.php");
}
$whmcs = WHMCS_Application::getinstance();
$ticketID = (int) $whmcs->get_req_var('ticketid');
$ticket = new WHMCS_Tickets();
if( !$ticket->setID($ticketID) )
{
    $apiresults = array( 'result' => 'error', 'message' => "Ticket ID Not Found" );
}
else
{
    $departmentId = $whmcs->get_req_var('deptid') ? (int) $whmcs->get_req_var('deptid') : '';
    $userId = $whmcs->get_req_var('userid') ? (int) $whmcs->get_req_var('userid') : '';
    $name = $whmcs->get_req_var('name');
    $email = $whmcs->get_req_var('email');
    $cc = $whmcs->get_req_var('cc');
    $subject = $whmcs->get_req_var('subject');
    $priority = $whmcs->get_req_var('priority');
    $status = $whmcs->get_req_var('status');
    $flag = $whmcs->get_req_var('flag') ? (int) $whmcs->get_req_var('flag') : '';
    $removeFlag = (string) $whmcs->get_req_var('removeFlag');
    if( $departmentId && $departmentId != (int) $ticket->getData('did') && !$ticket->setDept($departmentId) )
    {
        $apiresults = array( 'result' => 'error', 'message' => "Department ID Not Found" );
    }
    else
    {
        if( $priority && $priority != $ticket->getData('urgency') && !$ticket->setPriority($priority) )
        {
            $apiresults = array( 'result' => 'error', 'message' => "Invalid Ticket Priority. Valid priorities are: Low,Medium,High" );
        }
        else
        {
            if( $status && $status != 'Closed' && $status != $ticket->getData('status') && !$ticket->setStatus($status) )
            {
                $validStatuses = $ticket->getAssignableStatuses();
                $validStatuses[0] = '';
                $validStatuses[1] = '';
                $validStatuses[2] = '';
                $validStatuses = array_filter($validStatuses);
                $apiresults = array( 'result' => 'error', 'message' => "Invalid Ticket Status. Valid statuses are: " . implode(',', $validStatuses) );
            }
            else
            {
                if( $flag && $flag != $ticket->getData('flag') && !$ticket->setFlagTo($flag) )
                {
                    $apiresults = array( 'result' => 'error', 'message' => "Invalid Admin ID for Flag" );
                }
                else
                {
                    if( $removeFlag && !$flag && $ticket->getData('flag') !== 0 )
                    {
                        $ticket->setFlagTo(0);
                    }
                    if( $subject && $subject != $ticket->getData('subject') )
                    {
                        $ticket->setSubject($subject);
                    }
                    if( $status && $status == 'Closed' && $status != $ticket->getData('status') )
                    {
                        closeTicket($ticketID);
                    }
                    $updateQuery = array(  );
                    if( $userId && $userId != (int) $ticket->getData('userid') )
                    {
                        $updateQuery['userid'] = $userId;
                    }
                    if( $name && $name != $ticket->getData('name') )
                    {
                        $updateQuery['name'] = $name;
                    }
                    if( $email && $email != $ticket->getData('email') )
                    {
                        $updateQuery['email'] = $email;
                    }
                    if( $cc && $cc != $ticket->getData('cc') )
                    {
                        $updateQuery['cc'] = $cc;
                    }
                    if( 0 < count($updateQuery) )
                    {
                        update_query('tbltickets', $updateQuery, array( 'id' => $ticketID ));
                    }
                    $apiresults = array( 'result' => 'success', 'ticketid' => $ticketID );
                }
            }
        }
    }
}