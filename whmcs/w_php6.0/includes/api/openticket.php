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
if( !function_exists('saveCustomFields') )
{
    require(ROOTDIR . "/includes/customfieldfunctions.php");
}
if( !function_exists('openNewTicket') )
{
    require(ROOTDIR . "/includes/ticketfunctions.php");
}
$from = '';
if( $clientid )
{
    $result = select_query('tblclients', 'id', array( 'id' => $clientid ));
    $data = mysql_fetch_array($result);
    if( !$data['id'] )
    {
        $apiresults = array( 'result' => 'error', 'message' => "Client ID Not Found" );
        return NULL;
    }
    if( $contactid )
    {
        $result = select_query('tblcontacts', 'id', array( 'id' => $contactid, 'userid' => $clientid ));
        $data = mysql_fetch_array($result);
        if( !$data['id'] )
        {
            $apiresults = array( 'result' => 'error', 'message' => "Contact ID Not Found" );
            return NULL;
        }
    }
}
else
{
    if( !$name || !$email )
    {
        $apiresults = array( 'result' => 'error', 'message' => "Name and email address are required if not a client" );
        return NULL;
    }
    $from = array( 'name' => $name, 'email' => $email );
}
$result = select_query('tblticketdepartments', '', array( 'id' => $deptid ));
$data = mysql_fetch_array($result);
$deptid = $data['id'];
if( !$deptid )
{
    $apiresults = array( 'result' => 'error', 'message' => "Department ID not found" );
}
else
{
    if( !$subject )
    {
        $apiresults = array( 'result' => 'error', 'message' => "Subject is required" );
    }
    else
    {
        if( !$message )
        {
            $apiresults = array( 'result' => 'error', 'message' => "Message is required" );
        }
        else
        {
            if( !$priority || !in_array($priority, array( 'Low', 'Medium', 'High' )) )
            {
                $priority = 'Low';
            }
            if( $serviceid )
            {
                if( is_numeric($serviceid) || substr($serviceid, 0, 1) == 'S' )
                {
                    $result = select_query('tblhosting', 'id', array( 'id' => $serviceid, 'userid' => $clientid ));
                    $data = mysql_fetch_array($result);
                    if( !$data['id'] )
                    {
                        $apiresults = array( 'result' => 'error', 'message' => "Service ID Not Found" );
                        return NULL;
                    }
                    $serviceid = 'S' . $data['id'];
                }
                else
                {
                    $serviceid = substr($serviceid, 1);
                    $result = select_query('tbldomains', 'id', array( 'id' => $serviceid, 'userid' => $clientid ));
                    $data = mysql_fetch_array($result);
                    if( !$data['id'] )
                    {
                        $apiresults = array( 'result' => 'error', 'message' => "Service ID Not Found" );
                        return NULL;
                    }
                    $serviceid = 'D' . $data['id'];
                }
            }
            if( $domainid )
            {
                $result = select_query('tbldomains', 'id', array( 'id' => $domainid, 'userid' => $clientid ));
                $data = mysql_fetch_array($result);
                if( !$data['id'] )
                {
                    $apiresults = array( 'result' => 'error', 'message' => "Domain ID Not Found" );
                    return NULL;
                }
                $serviceid = 'D' . $data['id'];
            }
            $treatAsAdmin = $whmcs->get_req_var('admin') ? true : false;
            $ticketdata = openNewTicket($clientid, $contactid, $deptid, $subject, $message, $priority, '', $from, $serviceid, $cc, $noemail, $treatAsAdmin);
            if( $customfields )
            {
                $customfields = base64_decode($customfields);
                $customfields = safe_unserialize($customfields);
                saveCustomFields($ticketdata['ID'], $customfields);
            }
            $apiresults = array( 'result' => 'success', 'id' => $ticketdata['ID'], 'tid' => $ticketdata['TID'], 'c' => $ticketdata['C'] );
        }
    }
}