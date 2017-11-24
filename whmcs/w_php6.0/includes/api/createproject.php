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
if( !isset($_REQUEST['adminid']) )
{
    $apiresults = array( 'result' => 'error', 'message' => "Admin ID not Set" );
}
else
{
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
    if( !trim($_REQUEST['title']) )
    {
        $apiresults = array( 'result' => 'error', 'message' => "Project Title is Required." );
    }
    else
    {
        if( isset($_REQUEST['status']) )
        {
            $status = get_query_val('tbladdonmodules', 'value', array( 'module' => 'project_management', 'setting' => 'statusvalues' ));
            $status_get = explode(',', $status);
            $status_main = in_array($_REQUEST['status'], $status_get) ? $status_get : $status_get[0];
        }
        $created = !isset($_REQUEST['created']) ? date('Y-m-d') : $_REQUEST['created'];
        $duedate = !isset($_REQUEST['duedate']) ? date('Y-m-d') : $_REQUEST['duedate'];
        $completed = isset($_REQUEST['completed']) ? 1 : 0;
        $projectid = insert_query('mod_project', array( 'userid' => $_REQUEST['userid'], 'title' => $_REQUEST['title'], 'ticketids' => $_REQUEST['ticketids'], 'invoiceids' => $_REQUEST['invoiceids'], 'notes' => $_REQUEST['notes'], 'adminid' => $_REQUEST['adminid'], 'status' => $status_main, 'created' => $created, 'duedate' => $duedate, 'completed' => $completed, 'lastmodified' => "now()" ));
        $apiresults = array( 'result' => 'success', 'message' => "Project has been created" );
    }
}