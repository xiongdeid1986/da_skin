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
if( !function_exists('getClientsDetails') )
{
    require(ROOTDIR . "/includes/clientfunctions.php");
}
if( !function_exists('saveCustomFields') )
{
    require(ROOTDIR . "/includes/customfieldfunctions.php");
}
if( !isset($_REQUEST['projectid']) )
{
    $apiresults = array( 'result' => 'error', 'message' => "Project ID Not SET" );
}
else
{
    if( isset($_REQUEST['projectid']) )
    {
        $result = select_query('mod_project', '', array( 'id' => (int) $projectid ));
        $data = mysql_fetch_assoc($result);
        $projectid = $data['id'];
        if( !$projectid )
        {
            $apiresults = array( 'result' => 'error', 'message' => "Project ID Not Found" );
            return NULL;
        }
    }
    if( isset($_REQUEST['userid']) )
    {
        $result_userid = select_query('tblclients', 'id', array( 'id' => $_REQUEST['userid'] ));
        $data_userid = mysql_fetch_array($result_userid);
        if( !$data_userid['id'] )
        {
            $apiresults = array( 'result' => 'error', 'message' => "Client ID Not Found" );
            return NULL;
        }
    }
    if( isset($_REQUEST['adminid']) )
    {
        $result_adminid = select_query('tbladmins', 'id', array( 'id' => $_REQUEST['adminid'] ));
        $data_adminid = mysql_fetch_array($result_adminid);
        if( !$data_adminid['id'] )
        {
            $apiresults = array( 'result' => 'error', 'message' => "Admin ID Not Found" );
            return NULL;
        }
    }
    if( isset($_REQUEST['status']) )
    {
        $status_get = get_query_val('tbladdonmodules', 'value', array( 'module' => 'project_management', 'setting' => 'statusvalues' ));
        $status_get = explode(',', $status_get);
        $status_main = in_array($_REQUEST['status'], $status_get) ? $status_get : $status_get[0];
    }
    $projectid = $_REQUEST['projectid'];
    $title = isset($_REQUEST['title']) ? trim($_REQUEST['title']) : '';
    $adminid = $data_adminid['id'];
    $userid = $data_user['id'];
    $ticketids = $_REQUEST['ticketids'];
    $invoiceids = $_REQUEST['invoiceids'];
    $notes = $_REQUEST['notes'];
    $status = $status_main;
    $duedate = $_REQUEST['duedate'];
    $completed = isset($_REQUEST['completed']) ? 1 : 0;
    $lastmodified = "now()";
    $updateqry = array(  );
    if( $projectid )
    {
        $updateqry['id'] = $projectid;
    }
    if( $title )
    {
        $updateqry['title'] = $title;
    }
    if( $adminid )
    {
        $updateqry['adminid'] = $adminid;
    }
    if( $userid )
    {
        $updateqry['userid'] = $userid;
    }
    if( $ticketids )
    {
        $updateqry['ticketids'] = $ticketids;
    }
    if( $invoiceid )
    {
        $updateqry['invoiceids'] = $invoiceids;
    }
    if( $notes )
    {
        $updateqry['notes'] = $notes;
    }
    if( $status )
    {
        $updateqry['status'] = $status;
    }
    if( $duedate )
    {
        $updateqry['duedate'] = $duedate;
    }
    if( $completed )
    {
        $updateqry['completed'] = $completed;
    }
    $updateqry['lastmodified'] = $lastmodified;
    update_query('mod_project', $updateqry, array( 'id' => $projectid ));
    $apiresults = array( 'result' => 'success', 'message' => "Project Has Been Updated" );
}